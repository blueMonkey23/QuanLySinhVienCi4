<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table            = 'enrollments';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'class_id', 'student_id', 'diligence_score', 'midterm_score', 'final_score'
    ];

    // Lấy danh sách sinh viên trong một lớp kèm điểm số
    public function getStudentsInClass($classId)
    {
        return $this->select('enrollments.*, students.student_code, students.name as student_name')
                    ->join('students', 'enrollments.student_id = students.id')
                    ->where('enrollments.class_id', $classId)
                    ->orderBy('students.name', 'ASC')
                    ->findAll();
    }
}