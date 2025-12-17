<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentModel extends Model
{
    protected $table            = 'students';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    // Cập nhật đúng theo file SQL mới
    protected $allowedFields    = [
        'user_id', 
        'student_code', 
        'name', 
        'dob', 
        'gender', 
        'address', 
        'phone',            // Mới thêm
        'status', 
        'student_class_id'  // Mới thêm (Lớp quản lý/Lớp hành chính)
    ];
}