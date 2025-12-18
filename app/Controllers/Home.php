<?php

namespace App\Controllers;

class Home extends BaseController
{
    // 1. Trang Đăng nhập (Mặc định)
    public function login()
    {
        return view('login'); // Sẽ load file app/Views/login.php
    }

    // 2. Trang Đăng ký
    public function register()
    {
        return view('register');
    }

    // --- CÁC TRANG DÀNH CHO SINH VIÊN ---

    // 3. Trang chủ sinh viên
    public function index()
    {
        return view('index'); 
    }

    // 4. Thông tin sinh viên
    public function info()
    {
        return view('information');
    }

    // 5. Xem điểm
    public function grades()
    {
        return view('grades');
    }

    // 6. Lịch học
    public function schedule()
    {
        return view('class_schedule');
    }

    // 7. Lịch thi
    public function exams()
    {
        return view('exam_schedule');
    }
}