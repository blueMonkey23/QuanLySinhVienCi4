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
        $builder = $this->select("students.*, users.email,
            CASE 
                WHEN students.gender = 'male' THEN 'Nam'
                WHEN students.gender = 'female' THEN 'Nữ'
                ELSE 'Khác'
            END as gender_text")
                        ->join('users', 'students.user_id = users.id', 'left');
        
        if ($keyword) {
            $builder->groupStart()
                ->like('students.student_code', $keyword)
                ->orLike('students.name', $keyword)
            ->groupEnd();
        }
        
        return $builder->orderBy('students.id', 'DESC')->findAll();
    }

    // Lấy thông tin sinh viên theo ID kèm thông tin user
    public function getStudentById($id)
    {
        return $this->select('students.*, users.email as user_email')
                    ->join('users', 'students.user_id = users.id', 'left')
                    ->where('students.id', $id)
                    ->first();
    }

    // Lấy danh sách lớp học của sinh viên
    public function getStudentClasses($studentId)
    {
        $db = \Config\Database::connect();
        
        return $db->table('enrollments e')
            ->select('c.*, s.name as subject_name, s.subject_code')
            ->join('classes c', 'e.class_id = c.id')
            ->join('subjects s', 'c.subject_id = s.id')
            ->where('e.student_id', $studentId)
            ->get()
            ->getResultArray();
    }
}