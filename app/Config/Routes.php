<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// --------------------------------------------------------------------
// 1. ROUTE MẶC ĐỊNH & VIEW (GIAO DIỆN)
// --------------------------------------------------------------------

// Trang chủ mặc định: Kiểm tra session và redirect theo role
$routes->get('/', 'Home::defaultPage');

// --- NHÓM VIEW CÔNG KHAI (Không cần đăng nhập) ---
$routes->match(['get', 'post'], 'login.html', 'AuthController::login');
$routes->match(['get', 'post'], 'register.html', 'AuthController::register');
$routes->get('logout', 'AuthController::logout');
$routes->get('clear-session', function() {
    session()->destroy();
    return redirect()->to('/login.html')->with('success', 'Session cleared!');
});

// --- NHÓM VIEW BẢO VỆ (Cần đăng nhập - Filter Auth) ---
$routes->group('', ['filter' => 'auth'], function($routes) {
    
    // A. View dành cho SINH VIÊN
    $routes->get('index.html', 'Home::index');
    $routes->get('information.html', 'Student\StudentController::information');
    $routes->get('grades.html', 'Student\StudentController::grades');
    $routes->get('class_schedule.html', 'Student\StudentController::classes');
    $routes->get('exam_schedule.html', 'Student\StudentController::exams');

    // B. View dành cho QUẢN LÝ (Manager/Admin)
    $routes->get('manager_dashboard.html', 'Manager\Dashboard::index');
    $routes->get('manager_schedule.html', 'Manager\ScheduleController::index');
    
    // Quản lý Lớp học (Views)
    $routes->get('manager_classes.html', 'Manager\ClassController::index');
    $routes->get('manager_class_add.html', 'Manager\ClassController::addForm');
    $routes->get('manager_class_edit.html/(:num)', 'Manager\ClassController::editForm/$1');
    $routes->get('manager_class_detail.html/(:num)', 'Manager\ClassController::detail/$1');
    
    // Quản lý Sinh viên (Views)
    $routes->get('manager_students.html', 'Manager\StudentController::index');
    
    // Actions (POST)
    $routes->post('manager_class_add', 'Manager\ClassController::create');
    $routes->post('manager_class_update/(:num)', 'Manager\ClassController::update/$1');
    $routes->post('manager_class_delete/(:num)', 'Manager\ClassController::delete/$1');
    $routes->post('manager_class_lock/(:num)', 'Manager\ClassController::toggleLock/$1');
    $routes->post('manager_class_add_student/(:num)', 'Manager\EnrollmentController::enrollAdd/$1');
    $routes->get('manager_class_remove_student/(:num)/(:num)', 'Manager\EnrollmentController::enrollRemove/$1/$2');
    $routes->post('manager_class_grades/(:num)', 'Manager\EnrollmentController::updateGrades/$1');
    
    $routes->post('manager_student_add', 'Manager\StudentController::create');
    $routes->post('manager_student_update/(:num)', 'Manager\StudentController::update/$1');
    $routes->post('manager_student_lock/(:num)', 'Manager\StudentController::toggleLock/$1');
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
    $routes->match(['get', 'post'], 'logout.php', 'AuthController::logout');
    $routes->get('status.php', 'AuthController::status');
    $routes->get('class_data_fetch.php', 'GeneralController::fetchClassData'); // Dropdown data
});