<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    // 1. Trang chủ Dashboard
    public function index()
    {
        return view('manager_dashboard');
    }

    // 2. Danh sách lớp học
    public function classes()
    {
        return view('manager_classes');
    }

    // 3. Form thêm lớp
    public function addClass()
    {
        return view('manager_class_add');
    }

    // 4. Form sửa lớp
    public function editClass()
    {
        return view('manager_class_edit');
    }

    // 5. Chi tiết lớp học
    public function detailClass()
    {
        return view('manager_class_detail');
    }

    // 6. Quản lý sinh viên
    public function students()
    {
        return view('manager_students');
    }

    // 7. Lịch giảng dạy tổng hợp
    public function schedule()
    {
        return view('manager_schedule');
    }

    // Các trang bổ sung (nếu có trong tương lai)
    public function grades() { return view('manager_grades'); }
    public function attendance() { return view('manager_attendance'); }
    public function assignments() { return view('manager_assignments'); }
}