<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/index', 'Index::index');
$routes->get('student', 'Student::index');           // Vào trang quản lý
$routes->get('student/list', 'Student::list');       // API lấy danh sách
$routes->post('student/create', 'Student::create');  // API thêm
$routes->post('student/update', 'Student::update');  // API sửa
$routes->post('student/toggleLock', 'Student::toggleLock'); // API khóa