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
        
        // Ngăn browser cache trang login
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        
        // Nếu đã đăng nhập rồi và session hợp lệ, redirect về trang chủ
        if ($session->get('logged_in') && $session->get('role_id')) {
            $roleId = $session->get('role_id');
            $redirectUrl = ($roleId == 4) ? '/index.html' : '/manager_dashboard.html';
            return redirect()->to($redirectUrl);
        }
        
        // Nếu là GET request, hiển thị form login
        if ($request->getMethod() === 'get') {
            return view('login');
        }
        
        // POST request - Xử lý đăng nhập
        $email = $request->getPost('email');
        $password = $request->getPost('password');

        // Validation
        if (empty($email) || empty($password)) {
            return view('login', ['error' => 'Vui lòng điền đầy đủ thông tin!', 'old' => ['email' => $email]]);
        }

        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->first();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Đăng nhập thành công -> Lưu Session
            $role = $userModel->getUserRole($user['id']); 
            $roleId = $userModel->getUserRoleId($user['id']);
            
            $session_data = [
                'user_id'    => $user['id'],
                'name'       => $user['name'],
                'user_email' => $user['email'],
                'user_role'  => $role,
                'role_id'    => $roleId,
                'logged_in'  => true
            ];
            $session->set($session_data);
            
            $session->setFlashdata('success', 'Đăng nhập thành công!');
            
            // Redirect theo role_id
            $redirectUrl = ($roleId == 4) ? '/index.html' : '/manager_dashboard.html';
            return redirect()->to($redirectUrl);
        }

        return view('login', ['error' => 'Email hoặc mật khẩu không chính xác.', 'old' => ['email' => $email]]);
    }

    public function register()
    {
        $request = service('request');
        $session = session();
        
        // Ngăn browser cache trang register
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        
        // Nếu đã đăng nhập rồi và session hợp lệ, redirect về trang chủ
        if ($session->get('logged_in') && $session->get('role_id')) {
            $roleId = $session->get('role_id');
            $redirectUrl = ($roleId == 4) ? '/index.html' : '/manager_dashboard.html';
            return redirect()->to($redirectUrl);
        }
        
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
            return view('register', [
                'error' => 'Vui lòng điền đầy đủ thông tin.',
                'old' => ['fullname' => $name, 'email' => $email, 'student_id' => $studentId]
            ]);
        }
        
        if ($password !== $confirmPassword) {
            return view('register', [
                'error' => 'Mật khẩu xác nhận không khớp.',
                'old' => ['fullname' => $name, 'email' => $email, 'student_id' => $studentId]
            ]);
        }

        $userModel = new UserModel();
        $studentModel = new \App\Models\StudentModel();
        $db = \Config\Database::connect();

        // Kiểm tra Email hoặc Mã SV đã tồn tại chưa
        if ($userModel->where('email', $email)->first()) {
            return view('register', [
                'error' => 'Email này đã được sử dụng.',
                'old' => ['fullname' => $name, 'email' => $email, 'student_id' => $studentId]
            ]);
        }
        if ($studentModel->where('student_code', $studentId)->first()) {
            return view('register', [
                'error' => 'Mã sinh viên này đã tồn tại.',
                'old' => ['fullname' => $name, 'email' => $email, 'student_id' => $studentId]
            ]);
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
            return view('register', [
                'error' => 'Lỗi hệ thống, không thể tạo tài khoản.',
                'old' => ['fullname' => $name, 'email' => $email, 'student_id' => $studentId]
            ]);
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