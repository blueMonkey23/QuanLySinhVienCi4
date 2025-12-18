<?php

namespace App\Models;

use CodeIgniter\Model;

class TeacherModel extends Model
{
    protected $table            = 'teachers';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['user_id', 'teacher_code', 'first_name', 'last_name', 'faculty_id'];

    public function findAll(?int $limit = null, int $offset = 0)
    {
        $teachers = parent::findAll($limit, $offset);
        
        // Gộp first_name và last_name thành teacher_name
        return array_map(function($teacher) {
            $teacher['teacher_name'] = trim($teacher['first_name'] . ' ' . $teacher['last_name']);
            return $teacher;
        }, $teachers);
    }

    public function find($id = null)
    {
        $teacher = parent::find($id);
        
        if ($teacher && is_array($teacher)) {
            $teacher['teacher_name'] = trim($teacher['first_name'] . ' ' . $teacher['last_name']);
        }
        
        return $teacher;
    }
}
