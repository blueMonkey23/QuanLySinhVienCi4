<?php

namespace App\Controllers;

use App\Models\StudentModel;
use CodeIgniter\API\ResponseTrait;

class Student extends BaseController
{
    use ResponseTrait;

    protected $studentModel;
    protected $db;

    public function __construct()
    {
        $this->studentModel = new StudentModel();
        $this->db = \Config\Database::connect();
    }

    // 1. View
    public function index()
    {
        return view('student_index');
    }

    // 2. API Lấy danh sách (JOIN với bảng student_classes để lấy tên lớp hành chính nếu có)
    public function list()
    {
        $keyword = $this->request->getGet('keyword');

        $builder = $this->db->table('students');
        $builder->select('students.*, users.email, student_classes.name as class_name');
        $builder->join('users', 'students.user_id = users.id');
        $builder->join('student_classes', 'students.student_class_id = student_classes.id', 'left'); // Join trái vì có thể chưa có lớp
        
        if ($keyword) {
            $builder->groupStart()
                ->like('students.student_code', $keyword)
                ->orLike('students.name', $keyword)
            ->groupEnd();
        }
        $builder->orderBy('students.id', 'DESC');
        
        $data = $builder->get()->getResultArray();

        // Xử lý hiển thị
        $result = array_map(function($row){
            $genderShow = 'Khác';
            if ($row['gender'] == 'male') $genderShow = 'Nam';
            if ($row['gender'] == 'female') $genderShow = 'Nữ';
            
            $row['gender_text'] = $genderShow;
            $row['fullname'] = $row['name']; 
            // Nếu chưa có lớp thì hiện dấu -
            $row['lop'] = $row['class_name'] ?? '-';
            return $row;
        }, $data);

        return $this->respond(['success' => true, 'data' => $result]);
    }

    // 3. API Thêm mới
    public function create()
    {
        $json = $this->request->getJSON();

        // Map giới tính từ Giao diện (Nam/Nữ) sang Database (male/female)
        $genderDB = 'other';
        if ($json->gender == 'Nam') $genderDB = 'male';
        if ($json->gender == 'Nữ') $genderDB = 'female';

        $this->db->transStart(); 

        try {
            // B1: Tạo User
            // Mật khẩu mặc định là Mã SV
            $passHash = password_hash($json->student_code, PASSWORD_DEFAULT);
            $this->db->table('users')->insert([
                'name' => $json->fullname,
                'email' => $json->email,
                'password_hash' => $passHash,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $userId = $this->db->insertID();

            // B2: Gán quyền (Role ID = 4 là Student theo file SQL của bạn)
            $this->db->table('role_user')->insert([
                'user_id' => $userId, 
                'role_id' => 4 
            ]);

            // B3: Tạo Student
            $this->studentModel->insert([
                'user_id' => $userId,
                'student_code' => $json->student_code,
                'name' => $json->fullname,
                'dob' => $json->dob,
                'gender' => $genderDB,
                'address' => $json->address,
                'status' => 1,
                // Hiện tại form chưa có chọn lớp, để null hoặc cập nhật sau
                'student_class_id' => null 
            ]);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                 return $this->respond(['success' => false, 'message' => 'Lỗi transaction']);
            }
            return $this->respond(['success' => true, 'message' => 'Thêm sinh viên thành công']);

        } catch (\Exception $e) {
            return $this->respond(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
    }

    // 4. API Cập nhật
    public function update()
    {
        $json = $this->request->getJSON();

        $genderDB = 'other';
        if ($json->gender == 'Nam') $genderDB = 'male';
        if ($json->gender == 'Nữ') $genderDB = 'female';

        $data = [
            'name' => $json->fullname,
            'dob' => $json->dob,
            'gender' => $genderDB,
            'address' => $json->address
        ];

        // Cập nhật cả tên trong bảng users để đồng bộ
        // Lấy user_id từ bảng students trước
        $student = $this->studentModel->find($json->id);
        if($student) {
            $this->db->table('users')->where('id', $student['user_id'])->update(['name' => $json->fullname]);
        }

        if ($this->studentModel->update($json->id, $data)) {
            return $this->respond(['success' => true, 'message' => 'Cập nhật thành công']);
        } else {
            return $this->respond(['success' => false, 'message' => 'Lỗi cập nhật']);
        }
    }

    // 5. Toggle Lock
    public function toggleLock()
    {
        $json = $this->request->getJSON();
        // SQL của bạn dùng TinyInt(1) cho status, logic này vẫn đúng
        $sql = "UPDATE students SET status = NOT status WHERE id = ?";
        $this->db->query($sql, [$json->id]);

        return $this->respond(['success' => true, 'message' => 'Đổi trạng thái thành công']);
    }
    public function schedule()
    {
        $studentId = $this->request->getGet('id');

        if (!$studentId) {
            return $this->respond(['success' => false, 'message' => 'Thiếu ID sinh viên']);
        }

        // Lấy thông tin cơ bản sinh viên
        $student = $this->studentModel->find($studentId);
        if (!$student) {
            return $this->respond(['success' => false, 'message' => 'Không tìm thấy sinh viên']);
        }

        // Query phức hợp để lấy: Lớp, Môn, Giáo viên, Lịch, Điểm
        $builder = $this->db->table('enrollments e');
        $builder->select('
            c.class_code,
            sub.name as subject_name,
            c.format,
            CONCAT(t.first_name, " ", t.last_name) as teacher_name,
            sch.day_of_week,
            sch.start_time,
            sch.end_time,
            sch.room,
            e.midterm_score,
            e.final_score
        ');
        
        // JOIN CÁC BẢNG (Dựa trên SQL bạn cung cấp)
        $builder->join('classes c', 'e.class_id = c.id');
        $builder->join('subjects sub', 'c.subject_id = sub.id');
        $builder->join('teachers t', 'c.teacher_id = t.id', 'left');
        $builder->join('schedules sch', 'c.id = sch.class_id', 'left'); // Dùng Left Join vì có thể lớp online không có phòng/lịch cố định
        
        $builder->where('e.student_id', $studentId);
        
        // Sắp xếp theo thứ trong tuần
        $builder->orderBy('sch.day_of_week', 'ASC');

        $classes = $builder->get()->getResultArray();

        // Xử lý hiển thị dữ liệu cho đẹp (Format giờ, thứ)
        $classes = array_map(function($row) {
            // Xử lý thứ
            $row['day_text'] = $row['day_of_week'] ? "Thứ " . $row['day_of_week'] : "Lịch linh động";
            if ($row['day_of_week'] == 8) $row['day_text'] = "Chủ Nhật";

            // Xử lý giờ (Cắt bỏ giây: 07:00:00 -> 07:00)
            if ($row['start_time']) {
                $row['time_text'] = substr($row['start_time'], 0, 5) . ' - ' . substr($row['end_time'], 0, 5);
            } else {
                $row['time_text'] = "";
            }

            return $row;
        }, $classes);

        return $this->respond([
            'success' => true, 
            'student' => $student, 
            'classes' => $classes
        ]);
    }
}