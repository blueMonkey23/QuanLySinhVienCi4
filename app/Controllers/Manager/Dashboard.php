<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    // 1. Trang chủ Dashboard
    public function index()
    {
        return view('manager/dashboard');
    }

    // 2. Danh sách lớp học
    public function classes()
    {
        return view('manager/classes');
    }

    // 3. Form thêm lớp
    public function addClass()
    {
        return view('manager/class_add');
    }

    // 4. Form sửa lớp
    public function editClass()
    {
        return view('manager/class_edit');
    }

    // 5. Chi tiết lớp học
    public function detailClass()
    {
        return view('manager/class_detail');
    }

    // 6. Quản lý sinh viên
    public function students()
    {
        return view('manager/students');
    }

    // 7. Lịch giảng dạy tổng hợp
    public function schedule()
    {
        return view('manager/schedule');
    }

    // Các trang bổ sung (nếu có trong tương lai)
    public function grades() { return view('manager/grades'); }
    public function attendance() { return view('manager/attendance'); }
    public function assignments() { return view('manager/assignments'); }
}