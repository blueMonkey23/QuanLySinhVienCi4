<?php

namespace App\Models;

use CodeIgniter\Model;

class ClassModel extends Model
{
    protected $table            = 'classes';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'class_code', 'subject_id', 'semester_id', 'teacher_id', 
        'format', 'max_students', 'is_locked', 'created_at'
    ];

    // Hàm lấy danh sách lớp đầy đủ thông tin (Join với Subject, Teacher, Semester)
    public function getClassesWithDetails($keyword = null, $subjectId = null)
    {
        $builder = $this->select('classes.*, subjects.name as subject_name, subjects.subject_code, 
                                  semesters.name as semester_name, semesters.end_date as semester_end_date,
                                  CONCAT(teachers.first_name, " ", teachers.last_name) as teacher_name')
                        ->join('subjects', 'classes.subject_id = subjects.id', 'left')
                        ->join('semesters', 'classes.semester_id = semesters.id', 'left')
                        ->join('teachers', 'classes.teacher_id = teachers.id', 'left');

        if ($keyword) {
            $builder->groupStart()
                ->like('classes.class_code', $keyword)
                ->orLike('subjects.name', $keyword)
                ->orLike('CONCAT(teachers.first_name, " ", teachers.last_name)', $keyword)
            ->groupEnd();
        }

        if ($subjectId && $subjectId !== 'all') {
            $builder->where('classes.subject_id', $subjectId);
        }

        // Subquery đếm số sinh viên hiện tại
        $builder->select('(SELECT COUNT(*) FROM enrollments WHERE enrollments.class_id = classes.id) as current_students');

        return $builder->findAll();
    }
}