<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentModel extends Model
{
    protected $table            = 'students';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    // Các cột được phép thêm/sửa
    protected $allowedFields    = [
        'user_id', 'student_code', 'name', 'dob', 
        'gender', 'address', 'status'
    ];
}