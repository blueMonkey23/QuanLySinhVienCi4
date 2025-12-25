<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\SubjectModel;
use App\Models\TeacherModel;

class GeneralController extends BaseController
{
    use ResponseTrait;

    public function fetchClassData()
    {
        $subjectModel = new SubjectModel();
        $teacherModel = new TeacherModel();

        // 1. Lấy danh sách Môn học
        $subjects = $subjectModel->select('id, name, subject_code')
                                 ->orderBy('name', 'ASC')
                                 ->findAll();

        // 2. Lấy danh sách Giáo viên  
        $teachers = $teacherModel->select("id, CONCAT(first_name, ' ', last_name) as name, teacher_code")
                                 ->orderBy('first_name', 'ASC')
                                 ->findAll();

        return $this->respond([
            'success' => true,
            'data' => [
                'subjects' => $subjects,
                'teachers' => $teachers
            ]
        ]);
    }
}