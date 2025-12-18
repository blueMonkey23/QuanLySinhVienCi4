<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\UserModel;

class StudentController extends BaseController
{
    protected $studentModel;
    protected $userModel;
    protected $db;

    public function __construct()
    {
        $this->studentModel = new StudentModel();
        $this->userModel = new UserModel();
        $this->db = \Config\Database::connect();
    }

    // 1. Danh sách sinh viên - GET
    public function index()
    {
        $keyword = $this->request->getGet('keyword');
        $students = $this->studentModel->getStudentsWithUser($keyword);
        
        $data = array_map(function($row) {
            $row['gender_text'] = ($row['gender'] == 'Nam') ? 'Nam' : (($row['gender'] == 'Nữ') ? 'Nữ' : 'Khác');
            $row['fullname'] = $row['name'];
            return $row;
        }, $students);

        $viewData = [
            'students' => $data,
            'keyword' => $keyword
        ];

        return view('manager_students', $viewData);
    }

    // 2. Thêm sinh viên - POST
    public function create()
    {
        $session = session();
        
        $studentCode = $this->request->getPost('studentCode');
        $fullname = $this->request->getPost('fullname');
        $email = $this->request->getPost('email');
        $dob = $this->request->getPost('dob');
        $gender = $this->request->getPost('gender');
        $address = $this->request->getPost('address');

        if (empty($studentCode) || empty($fullname) || empty($email)) {
            $session->setFlashdata('error', 'Vui lòng điền đầy đủ thông tin.');
            return redirect()->back()->withInput();
        }

        // Kiểm tra trùng
        if ($this->studentModel->where('student_code', $studentCode)->first()) {
            $session->setFlashdata('error', 'Mã sinh viên đã tồn tại.');
            return redirect()->back()->withInput();
        }
        if ($this->userModel->where('email', $email)->first()) {
            $session->setFlashdata('error', 'Email đã được sử dụng.');
            return redirect()->back()->withInput();
        }

        $this->db->transStart();

        $userId = $this->userModel->insert([
            'email' => $email,
            'password_hash' => password_hash($studentCode, PASSWORD_BCRYPT),
            'name' => $fullname
        ]);

        $this->db->table('role_user')->insert(['user_id' => $userId, 'role_id' => 4]);

        $this->studentModel->insert([
            'user_id' => $userId,
            'student_code' => $studentCode,
            'name' => $fullname,
            'dob' => $dob ?: null,
            'gender' => $gender ?: 'other',
            'address' => $address ?: '',
            'status' => 1
        ]);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            $session->setFlashdata('error', 'Lỗi khi tạo sinh viên.');
            return redirect()->back()->withInput();
        }

        $session->setFlashdata('success', 'Thêm sinh viên thành công!');
        return redirect()->to('/manager_students.html');
    }

    // 3. Cập nhật sinh viên - POST
    public function update($id)
    {
        $session = session();

        $updateData = [
            'name' => $this->request->getPost('fullname'),
            'dob' => $this->request->getPost('dob') ?: null,
            'gender' => $this->request->getPost('gender'),
            'address' => $this->request->getPost('address')
        ];

        if ($this->studentModel->update($id, $updateData)) {
            $session->setFlashdata('success', 'Cập nhật thành công!');
        } else {
            $session->setFlashdata('error', 'Cập nhật thất bại.');
        }
        
        return redirect()->to('/manager_students.html');
    }

    // 4. Khóa/Mở khóa sinh viên - POST
    public function toggleLock($id)
    {
        $session = session();
        
        $student = $this->studentModel->find($id);
        
        if (!$student) {
            $session->setFlashdata('error', 'Không tìm thấy sinh viên.');
            return redirect()->to('/manager_students.html');
        }

        $newStatus = ($student['status'] == 1) ? 0 : 1;
        $this->studentModel->update($id, ['status' => $newStatus]);

        $msg = ($newStatus == 1 ? 'Đã mở khóa ' : 'Đã khóa ') . 'hồ sơ sinh viên.';
        $session->setFlashdata('success', $msg);
        
        return redirect()->to('/manager_students.html');
    }

    // 5. API - Lấy lịch học của sinh viên
    public function getSchedule($studentId)
    {
        try {
            // Lấy thông tin sinh viên để debug
            $student = $this->studentModel->find($studentId);
            
            if (!$student) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => "Không tìm thấy sinh viên với ID: {$studentId}"
                ]);
            }
            
            // Lấy danh sách lớp học của sinh viên
            $enrollments = $this->db->table('enrollments')
                ->select('enrollments.*, classes.class_code')
                ->join('classes', 'enrollments.class_id = classes.id', 'left')
                ->where('enrollments.student_id', $studentId)
                ->get()
                ->getResultArray();

            if (empty($enrollments)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Sinh viên chưa đăng ký lớp học nào.'
                ]);
            }

            // Lấy lịch học của các lớp đó
            $schedule = $this->db->table('enrollments')
                ->select('
                    classes.class_code,
                    subjects.name as subject_name,
                    CONCAT(teachers.first_name, " ", teachers.last_name) as teacher_name,
                    schedules.day_of_week,
                    schedules.start_time,
                    schedules.end_time,
                    schedules.room
                ')
                ->join('classes', 'enrollments.class_id = classes.id', 'inner')
                ->join('subjects', 'classes.subject_id = subjects.id', 'left')
                ->join('teachers', 'classes.teacher_id = teachers.id', 'left')
                ->join('schedules', 'classes.id = schedules.class_id', 'inner')
                ->where('enrollments.student_id', $studentId)
                ->orderBy('schedules.day_of_week', 'ASC')
                ->orderBy('schedules.start_time', 'ASC')
                ->get()
                ->getResultArray();

            if (empty($schedule)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Các lớp học chưa có lịch học được thiết lập.',
                    'enrollments' => $enrollments
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'schedule' => $schedule
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    }

    // Debug student data
    public function debugStudent($studentIdOrCode)
    {
        // Tìm sinh viên theo ID hoặc student_code
        if (is_numeric($studentIdOrCode)) {
            $student = $this->studentModel->find($studentIdOrCode);
        } else {
            $student = $this->studentModel->where('student_code', $studentIdOrCode)->first();
        }
        
        if (!$student) {
            return $this->response->setJSON([
                'error' => 'Không tìm thấy sinh viên',
                'input' => $studentIdOrCode
            ]);
        }
        
        $studentId = $student['id'];
        
        // Lấy tất cả enrollments
        $allEnrollments = $this->db->table('enrollments')->get()->getResultArray();
        
        // Lấy tất cả students
        $allStudents = $this->db->table('students')
            ->select('id, student_code, name, user_id')
            ->get()
            ->getResultArray();
        
        // Kiểm tra enrollments với student_id
        $enrollmentsByStudentId = $this->db->table('enrollments')
            ->where('student_id', $studentId)
            ->get()
            ->getResultArray();
            
        // Kiểm tra enrollments với user_id (nếu có)
        $enrollmentsByUserId = [];
        if ($student && isset($student['user_id'])) {
            $enrollmentsByUserId = $this->db->table('enrollments')
                ->where('student_id', $student['user_id'])
                ->get()
                ->getResultArray();
        }
        
        return $this->response->setJSON([
            'student_info' => $student,
            'all_students' => $allStudents,
            'all_enrollments' => $allEnrollments,
            'enrollments_by_student_id' => $enrollmentsByStudentId,
            'enrollments_by_user_id' => $enrollmentsByUserId,
            'note' => 'Kiểm tra xem enrollments có student_id = ' . $studentId . ' hay user_id = ' . ($student['user_id'] ?? 'null')
        ]);
    }
}
