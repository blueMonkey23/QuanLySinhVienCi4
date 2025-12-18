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

    // 1. Lấy danh sách sinh viên (Tìm kiếm)
    // URL: backend/students.php?action=list&keyword=...
    public function index()
    {
        $keyword = $this->request->getGet('keyword');
        $students = $this->studentModel->getStudentsWithUser($keyword);
        
        // Format lại dữ liệu cho giống cấu trúc cũ
        $data = array_map(function($row) {
            $row['gender_text'] = ($row['gender'] == 'Nam') ? 'Nam' : (($row['gender'] == 'Nữ') ? 'Nữ' : 'Khác');
            $row['fullname'] = $row['name']; // Alias cho Frontend cũ dùng
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
            'email' => $data['email'],
            'dob' => $data['dob'] ?? null,
            'gender' => $data['gender'] ?? 'Khác',
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
            'gender' => $data['gender'],
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
}