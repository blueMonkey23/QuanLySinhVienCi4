<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentModel extends Model
{
    protected $table            = 'students';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'user_id', 'student_code', 'name', 'dob', 
        'gender', 'address', 'phone', 'status', 'student_class_id'
    ];

    public function getStudentsWithUser($keyword = null)
    {
        $builder = $this->select('students.*, students.status as is_locked, users.email')
                        ->join('users', 'students.user_id = users.id', 'left');
        
        if ($keyword) {
            $builder->groupStart()
                ->like('students.student_code', $keyword)
                ->orLike('students.name', $keyword)
            ->groupEnd();
        }
        
        return $builder->orderBy('students.id', 'DESC')->findAll();
    }
}