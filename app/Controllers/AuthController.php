<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class AuthController extends BaseController
{
    public function login()
    {
        $request = service('request');
        $session = session();
        
        // Nếu là GET request, hiển thị form login
        if ($request->getMethod() === 'get') {
            return view('login');
        }
        
        // POST request - Xử lý đăng nhập
        $email = $request->getPost('email');
        $password = $request->getPost('password');

        // Validation
        if (empty($email) || empty($password)) {
            $session->setFlashdata('error', 'Vui lòng điền đầy đủ thông tin!');
            return redirect()->to('/login.html')->withInput();
        }

        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->first();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Đăng nhập thành công -> Lưu Session
            $role = $userModel->getUserRole($user['id']); 
            
            $session_data = [
                'user_id'    => $user['id'],
                'user_email' => $user['email'],
                'user_role'  => $role,
                'logged_in'  => true
            ];
            $session->set($session_data);
            
            $session->setFlashdata('success', 'Đăng nhập thành công!');
            
            // Redirect theo role
            $redirectUrl = ($role === 'student') ? '/index.html' : '/manager_dashboard.html';
            return redirect()->to($redirectUrl);
        }

        $session->setFlashdata('error', 'Email hoặc mật khẩu không chính xác.');
        return redirect()->to('/login.html')->withInput();
    }

    public function register()
    {
        $request = service('request');
        $session = session();
        
        // Nếu là GET request, hiển thị form register
        if ($request->getMethod() === 'get') {
            return view('register');
        }
        
        // POST request - Xử lý đăng ký
        $name = $request->getPost('fullname');
        $email = $request->getPost('email');
        $studentId = $request->getPost('student_id');
        $password = $request->getPost('password');
        $confirmPassword = $request->getPost('confirm_password');

        // Validation
        if (empty($email) || empty($password) || empty($studentId) || empty($name)) {
            $session->setFlashdata('error', 'Vui lòng điền đầy đủ thông tin.');
            return redirect()->to('/register.html')->withInput();
        }
        
        if ($password !== $confirmPassword) {
            $session->setFlashdata('error', 'Mật khẩu xác nhận không khớp.');
            return redirect()->to('/register.html')->withInput();
        }

        $userModel = new UserModel();
        $studentModel = new \App\Models\StudentModel();
        $db = \Config\Database::connect();

        // Kiểm tra Email hoặc Mã SV đã tồn tại chưa
        if ($userModel->where('email', $email)->first()) {
            $session->setFlashdata('error', 'Email này đã được sử dụng.');
            return redirect()->to('/register.html')->withInput();
        }
        if ($studentModel->where('student_code', $studentId)->first()) {
            $session->setFlashdata('error', 'Mã sinh viên này đã tồn tại.');
            return redirect()->to('/register.html')->withInput();
        }

        // Tạo tài khoản (Transaction)
        $db->transStart();

        // Tạo User
        $userId = $userModel->insert([
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_BCRYPT),
            'name'  => $name
        ]);

        // Gán Role Student (ID=4)
        $db->table('role_user')->insert(['user_id' => $userId, 'role_id' => 4]);

        // Tạo Student Profile
        $studentModel->insert([
            'user_id' => $userId,
            'student_code' => $studentId,
            'name' => $name,
            'email' => $email,
            'status' => 1
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            $session->setFlashdata('error', 'Lỗi hệ thống, không thể tạo tài khoản.');
            return redirect()->to('/register.html')->withInput();
        }

        $session->setFlashdata('success', 'Đăng ký thành công! Bạn có thể đăng nhập ngay.');
        return redirect()->to('/login.html');
    }
    
    public function logout()
    {
        $session = session();
        $session->destroy();
        $session->setFlashdata('success', 'Đăng xuất thành công!');
        return redirect()->to('/login.html');
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