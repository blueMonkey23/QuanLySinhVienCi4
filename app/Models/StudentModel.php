<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentModel extends Model
{
    protected $table            = 'students';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'user_id', 'student_code', 'name', 'dob', 
        'gender', 'address', 'email', 'status'
    ];

    public function getStudentsWithUser($keyword = null)
    {
        $builder = $this->select('students.*, users.email as user_email')
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