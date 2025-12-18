<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// --------------------------------------------------------------------
// 1. ROUTE MẶC ĐỊNH & VIEW (GIAO DIỆN)
// --------------------------------------------------------------------

// Trang chủ mặc định: Chuyển hướng đến trang đăng nhập
$routes->get('/', 'Home::login');

// --- NHÓM VIEW CÔNG KHAI (Không cần đăng nhập) ---
$routes->get('login.html', 'Home::login');
$routes->get('register.html', 'Home::register');

// --- NHÓM VIEW BẢO VỆ (Cần đăng nhập - Filter Auth) ---
$routes->group('', ['filter' => 'auth'], function($routes) {
    
    // A. View dành cho SINH VIÊN
    $routes->get('index.html', 'Home::index');           // Trang chủ SV
    $routes->get('information.html', 'Home::info');      // Thông tin SV
    $routes->get('grades.html', 'Home::grades');         // Xem điểm
    $routes->get('class_schedule.html', 'Home::schedule'); // Lịch học SV
    $routes->get('exam_schedule.html', 'Home::exams');   // Lịch thi SV

    // B. View dành cho QUẢN LÝ (Manager/Admin)
    // Chúng ta sẽ tạo Controller 'Manager\Dashboard' sau để xử lý việc nạp view này
    $routes->get('manager_dashboard.html', 'Manager\Dashboard::index');
    $routes->get('manager_classes.html', 'Manager\Dashboard::classes');
    $routes->get('manager_class_add.html', 'Manager\Dashboard::addClass');
    $routes->get('manager_class_edit.html', 'Manager\Dashboard::editClass');
    $routes->get('manager_class_detail.html', 'Manager\Dashboard::detailClass');
    $routes->get('manager_students.html', 'Manager\Dashboard::students');
    $routes->get('manager_schedule.html', 'Manager\Dashboard::schedule');
    
    // Các trang bổ sung nếu có
    $routes->get('manager_grades.html', 'Manager\Dashboard::grades');
    $routes->get('manager_attendance.html', 'Manager\Dashboard::attendance');
    $routes->get('manager_assignments.html', 'Manager\Dashboard::assignments');
});

// --------------------------------------------------------------------
// 2. API BACKEND (JSON DATA)
// --------------------------------------------------------------------

// --- NHÓM API CÔNG KHAI ---
$routes->group('backend', function($routes) {
    $routes->post('login.php', 'AuthController::login');
    $routes->post('register.php', 'AuthController::register'); // (Cần thêm hàm register vào AuthController)
});

// --- NHÓM API BẢO MẬT (Cần đăng nhập) ---
$routes->group('backend', ['filter' => 'auth'], function($routes) {
    
    // --- AUTH & HỆ THỐNG ---
    $routes->post('logout.php', 'AuthController::logout');
    $routes->get('status.php', 'AuthController::status');
    $routes->get('class_data_fetch.php', 'GeneralController::fetchClassData'); // Dropdown data

    // --- QUẢN LÝ (MANAGER) ---
    // 1. Quản lý Lớp học
    $routes->get('manager_classes.php', 'Manager\ClassController::index');        // List
    $routes->post('manager_class_add.php', 'Manager\ClassController::create');    // Add
    $routes->post('manager_class_edit.php', 'Manager\ClassController::update');   // Edit
    $routes->post('manager_class_delete.php', 'Manager\ClassController::delete'); // Delete
    $routes->post('manager_class_lock.php', 'Manager\ClassController::toggleLock'); // Lock/Unlock
    $routes->get('manager_class_detail.php', 'Manager\ClassController::detail');  // Detail + Students
    $routes->get('manager_class_get.php', 'Manager\ClassController::show');       // Get Single Class (cho form sửa)

    // 2. Quản lý Sinh viên & Ghi danh
    $routes->get('students.php', 'Manager\StudentController::index');   // Search / List
    $routes->post('students.php', 'Manager\StudentController::save');   // Create / Update / Toggle Lock
    $routes->get('get_student_classes.php', 'Manager\StudentController::getStudentClasses'); // Xem lớp & TKB (dùng chung cho manager và student)
    $routes->post('manager_enroll_student.php', 'Manager\EnrollmentController::enroll'); // Add/Remove student class
    $routes->post('manager_update_grades.php', 'Manager\EnrollmentController::updateGrades'); // Update grades

    // 3. Lịch giảng dạy (Tổng hợp)
    $routes->get('manager_schedule.php', 'Manager\ScheduleController::index');

    // --- SINH VIÊN (STUDENT) ---
    // Mapping đúng tên file cũ để Frontend JS hoạt động không cần sửa nhiều
    $routes->get('get_student_classes.php', 'Student\StudentController::getClasses');     // Lấy lịch học & điểm
    $routes->get('exam_schedule.php', 'Student\StudentController::getExamSchedule');      // Lấy lịch thi
});