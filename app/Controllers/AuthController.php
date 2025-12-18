<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class AuthController extends BaseController
{
    public function login()
    {
        $request = service('request');
        $input = $request->getJSON(true); // Lấy JSON từ JS gửi lên

        // Fallback nếu JS gửi dạng form-data hoặc raw
        $data = $input['data'] ?? $request->getPost('data'); 
        
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->first();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Đăng nhập thành công -> Lưu Session
            $session = session();
            
            // Lấy role (Dựa trên hàm getUserRole ta đã viết ở Model bước trước)
            $role = $userModel->getUserRole($user['id']); 
            
            $session_data = [
                'user_id'    => $user['id'],
                'user_email' => $user['email'],
                'user_role'  => $role,
                'logged_in'  => true
            ];
            $session->set($session_data);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Đăng nhập thành công!',
                'data' => [
                    'user_id' => $user['id'],
                    'role'    => $role,
                    'redirect'=> ($role === 'student') ? 'index.html' : 'manager_dashboard.html'
                ]
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Email hoặc mật khẩu không chính xác.'
        ]);
    }

    public function register()
    {
        $request = service('request');
        $input = $request->getJSON(true);
        $data = $input['data'] ?? $request->getPost('data');

        if (empty($data['email']) || empty($data['password']) || empty($data['student_id'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Vui lòng điền đầy đủ thông tin.'
            ]);
        }

        $userModel = new UserModel();
        $studentModel = new \App\Models\StudentModel(); // Gọi model sinh viên
        $db = \Config\Database::connect();

        // 1. Kiểm tra Email hoặc Mã SV đã tồn tại chưa
        if ($userModel->where('email', $data['email'])->first()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Email này đã được sử dụng.']);
        }
        if ($studentModel->where('student_code', $data['student_id'])->first()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Mã sinh viên này đã tồn tại.']);
        }

        // 2. Tạo tài khoản (Transaction)
        $db->transStart();

        // Tạo User
        $userId = $userModel->insert([
            'email' => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_BCRYPT),
            'name'  => $data['name'] ?? 'Sinh viên'
        ]);

        // Gán Role Student (ID=4)
        $db->table('role_user')->insert(['user_id' => $userId, 'role_id' => 4]);

        // Tạo Student Profile
        $studentModel->insert([
            'user_id' => $userId,
            'student_code' => $data['student_id'],
            'name' => $data['name'],
            'email' => $data['email'],
            'status' => 1
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON(['success' => false, 'message' => 'Lỗi hệ thống, không thể tạo tài khoản.']);
        }

        return $this->response->setJSON([
            'success' => true, 
            'message' => 'Đăng ký thành công! Bạn có thể đăng nhập ngay.'
        ]);
    }
    
    public function logout()
    {
        $session = session();
        $session->destroy();
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Đăng xuất thành công!'
        ]);
    }

    public function status()
    {
        $session = session();
        if ($session->get('logged_in')) {
            // Logic lấy tên đầy đủ từ bảng teachers/students (tương tự status.php cũ)
            // Tạm thời trả về thông tin session
            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'logged_in' => true,
                    'fullname'  => $session->get('user_email'), // Cần query thêm tên thật sau này
                    'role'      => $session->get('user_role')
                ]
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => ['logged_in' => false]
        ]);
    }
}