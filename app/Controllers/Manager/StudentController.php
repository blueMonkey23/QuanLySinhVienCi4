<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;

class StudentController extends BaseController
{
    use ResponseTrait;

    protected $studentModel;
    protected $userModel;
    protected $db;

    public function __construct()
    {
        $this->studentModel = new StudentModel();
        $this->userModel = new UserModel();
        $this->db = \Config\Database::connect();
    }

    // Helper: Map giới tính từ tiếng Việt sang tiếng Anh
    private function mapGender($gender)
    {
        $map = [
            'Nam' => 'male',
            'Nữ' => 'female',
            'Khác' => 'other'
        ];
        return $map[$gender] ?? 'other';
    }

    // 1. Lấy danh sách sinh viên (Tìm kiếm)
    // URL: backend/students.php?action=list&keyword=...
    public function index()
    {
        $keyword = $this->request->getGet('keyword');
        $students = $this->studentModel->getStudentsWithUser($keyword);
        
        // Format lại dữ liệu cho Frontend
        $data = array_map(function($row) {
            $row['fullname'] = $row['name']; // Alias cho Frontend
            return $row;
        }, $students);

        return $this->respond(['success' => true, 'data' => $data]);
    }

    // 2. Thêm hoặc Cập nhật Sinh viên
    // URL: backend/students.php?action=create HOẶC action=update
    public function save()
    {
        $action = $this->request->getGet('action');
        $input = $this->request->getJSON(true);
        
        if ($action === 'create') {
            return $this->createStudent($input);
        } elseif ($action === 'update') {
            return $this->updateStudent($input);
        } elseif ($action === 'toggle_lock') {
            return $this->toggleLock($input);
        }

        return $this->fail('Hành động không hợp lệ.', 400);
    }

    // Logic thêm mới (Transaction: Tạo User -> Tạo Student)
    private function createStudent($data)
    {
        if (empty($data['studentCode']) || empty($data['fullname']) || empty($data['email'])) {
            return $this->fail('Vui lòng điền đầy đủ thông tin.', 400);
        }

        // Kiểm tra trùng mã SV hoặc Email
        if ($this->studentModel->where('student_code', $data['studentCode'])->first()) {
            return $this->fail('Mã sinh viên đã tồn tại.', 409);
        }
        if ($this->userModel->where('email', $data['email'])->first()) {
            return $this->fail('Email đã được sử dụng.', 409);
        }

        $this->db->transStart();

        // 1. Tạo User (Mật khẩu mặc định là mã SV)
        $userId = $this->userModel->insert([
            'email' => $data['email'],
            'password_hash' => password_hash($data['studentCode'], PASSWORD_BCRYPT),
            'name' => $data['fullname'] // Lưu tên vào bảng user luôn
        ]);

        // Gán Role Student (ID=4 trong file SQL của bạn)
        $this->db->table('role_user')->insert(['user_id' => $userId, 'role_id' => 4]);

        // 2. Tạo Student
        $this->studentModel->insert([
            'user_id' => $userId,
            'student_code' => $data['studentCode'],
            'name' => $data['fullname'],
            'dob' => $data['dob'] ?? null,
            'gender' => $this->mapGender($data['gender'] ?? 'Khác'),
            'address' => $data['address'] ?? '',
            'status' => 1
        ]);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return $this->failServerError('Lỗi khi tạo sinh viên.');
        }

        return $this->respondCreated(['success' => true, 'message' => 'Thêm sinh viên thành công!']);
    }

    // Logic cập nhật
    private function updateStudent($data)
    {
        if (empty($data['id'])) return $this->fail('Thiếu ID sinh viên.', 400);

        $updateData = [
            'name' => $data['fullname'],
            'dob' => $data['dob'],
            'gender' => $this->mapGender($data['gender']),
            'address' => $data['address']
        ];

        if ($this->studentModel->update($data['id'], $updateData)) {
            return $this->respond(['success' => true, 'message' => 'Cập nhật thành công!']);
        }
        return $this->fail('Cập nhật thất bại.');
    }

    // Logic khóa/mở khóa
    private function toggleLock($data)
    {
        $id = $data['id'] ?? 0;
        $student = $this->studentModel->find($id);
        
        if (!$student) return $this->failNotFound('Không tìm thấy sinh viên.');

        $newStatus = ($student['status'] == 1) ? 0 : 1;
        $this->studentModel->update($id, ['status' => $newStatus]);

        return $this->respond([
            'success' => true, 
            'message' => ($newStatus == 1 ? 'Đã mở khóa ' : 'Đã khóa ') . 'hồ sơ sinh viên.'
        ]);
    }

    // 3. Xem danh sách lớp và thời khóa biểu của sinh viên
    // URL: backend/get_student_classes.php?id=studentId
    public function getStudentClasses()
    {
        $studentId = $this->request->getGet('id');
        
        if (!$studentId) {
            return $this->fail('Thiếu ID sinh viên', 400);
        }

        // Lấy thông tin sinh viên
        $student = $this->studentModel->find($studentId);
        if (!$student) {
            return $this->failNotFound('Không tìm thấy sinh viên');
        }

        // Lấy danh sách lớp học của sinh viên (join với enrollments, classes, subjects, teachers, schedules)
        $builder = $this->db->table('enrollments e')
            ->select("
                c.id as class_id,
                c.class_code, 
                s.name AS subject_name,
                s.subject_code,
                c.format, 
                CONCAT(t.first_name, ' ', t.last_name) AS teacher_name,
                sch.day_of_week, 
                sch.start_time, 
                sch.end_time, 
                sch.room,
                e.midterm_score, 
                e.final_score,
                e.diligence_score,
                sem.name as semester_name
            ")
            ->join('classes c', 'e.class_id = c.id')
            ->join('subjects s', 'c.subject_id = s.id')
            ->join('teachers t', 'c.teacher_id = t.id', 'left')
            ->join('schedules sch', 'c.id = sch.class_id', 'left')
            ->join('semesters sem', 'c.semester_id = sem.id', 'left')
            ->where('e.student_id', $studentId)
            ->orderBy('sch.day_of_week', 'ASC')
            ->orderBy('sch.start_time', 'ASC');

        $classes = $builder->get()->getResultArray();

        // Format dữ liệu (Map thứ từ số sang chữ)
        $days = [2=>'Thứ Hai', 3=>'Thứ Ba', 4=>'Thứ Tư', 5=>'Thứ Năm', 6=>'Thứ Sáu', 7=>'Thứ Bảy', 8=>'Chủ Nhật'];
        
        foreach ($classes as &$cls) {
            $cls['day_text'] = $days[$cls['day_of_week']] ?? 'Không rõ';
            // Tạo chuỗi hiển thị thời gian
            if ($cls['start_time'] && $cls['end_time']) {
                $cls['time_text'] = substr($cls['start_time'], 0, 5) . ' - ' . substr($cls['end_time'], 0, 5);
            } else {
                $cls['time_text'] = 'Chưa xếp lịch';
            }
        }

        return $this->respond([
            'success' => true,
            'student' => [
                'id' => $student['id'],
                'name' => $student['name'],
                'student_code' => $student['student_code']
            ],
            'classes' => $classes
        ]);
    }
}