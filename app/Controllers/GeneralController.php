<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;

class GeneralController extends BaseController
{
    use ResponseTrait;

    public function fetchClassData()
    {
        $db = \Config\Database::connect();

        // 1. Lấy danh sách Môn học
        $subjects = $db->table('subjects')
                       ->select('id, name, subject_code')
                       ->orderBy('name', 'ASC')
                       ->get()->getResultArray();

        // 2. Lấy danh sách Giáo viên
        $teachers = $db->table('teachers')
                       ->select("id, CONCAT(first_name, ' ', last_name) as name, teacher_code")
                       ->orderBy('first_name', 'ASC')
                       ->get()->getResultArray();

        return $this->respond([
            'success' => true,
            'data' => [
                'subjects' => $subjects,
                'teachers' => $teachers
            ]
        ]);
    }
}