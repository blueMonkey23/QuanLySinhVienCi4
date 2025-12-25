<?php

namespace App\Controllers;

class Home extends BaseController
{
    // Trang chủ mặc định - redirect theo role
    public function defaultPage()
    {
        $session = session();
        
        // Kiểm tra đã đăng nhập chưa
        if (!$session->has('user_id')) {
            return redirect()->to('/login');
        }
        
        // Redirect theo role
        $roleId = $session->get('role_id');
        
        if ($roleId == 2) {
            // Quản lý -> Dashboard
            return redirect()->to('/manager_dashboard');
        } else {
            // Sinh viên -> Trang chủ sinh viên
            return redirect()->to('/index');
        }
    }
    
    // 1. Trang Đăng nhập (Mặc định)
    public function login()
    {
        return view('auth/login'); // Sẽ load file app/Views/auth/login.php
    }

    // 2. Trang Đăng ký
    public function register()
    {
        return view('auth/register');
    }

    // --- CÁC TRANG DÀNH CHO SINH VIÊN ---

    // 3. Trang chủ sinh viên
    public function index()
    {
        return view('student/index'); 
    }

    // 4. Thông tin sinh viên
    public function info()
    {
        return view('student/information');
    }

    // 5. Xem điểm
    public function grades()
    {
        return view('student/grades');
    }

    // 6. Lịch học
    public function schedule()
    {
        return view('student/class_schedule');
    }

    // 7. Lịch thi
    public function exams()
    {
        return view('student/exam_schedule');
    }
}