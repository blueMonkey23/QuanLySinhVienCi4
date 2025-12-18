<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;
use App\Models\EnrollmentModel;
use App\Models\StudentModel;
use App\Models\ClassModel;

class EnrollmentController extends BaseController
{
    protected $enrollmentModel;
    protected $studentModel;
    protected $classModel;

    public function __construct()
    {
        $this->enrollmentModel = new EnrollmentModel();
        $this->studentModel = new StudentModel();
        $this->classModel = new ClassModel();
    }

    // 1. Thêm sinh viên vào lớp - POST
    public function enrollAdd($classId)
    {
        $session = session();
        
        $studentCode = $this->request->getPost('student_code');

        if (!$studentCode) {
            $session->setFlashdata('error', 'Thiếu mã sinh viên.');
            return redirect()->back();
        }

        $student = $this->studentModel->where('student_code', $studentCode)->first();
        if (!$student) {
            $session->setFlashdata('error', "Không tìm thấy sinh viên với mã $studentCode.");
            return redirect()->back();
        }

        // Kiểm tra sĩ số lớp
        $currentCount = $this->enrollmentModel->where('class_id', $classId)->countAllResults();
        $class = $this->classModel->find($classId);
        
        if ($currentCount >= $class['max_students']) {
            $session->setFlashdata('error', 'Lớp đã đầy sĩ số.');
            return redirect()->back();
        }

        // Kiểm tra đã tồn tại chưa
        $exists = $this->enrollmentModel->where('class_id', $classId)
                                        ->where('student_id', $student['id'])->first();
        if ($exists) {
            $session->setFlashdata('error', 'Sinh viên này đã có trong lớp.');
            return redirect()->back();
        }

        $this->enrollmentModel->insert([
            'class_id' => $classId,
            'student_id' => $student['id']
        ]);
        
        $session->setFlashdata('success', "Đã thêm $studentCode vào lớp.");
        return redirect()->to("/manager_class_detail.html/$classId");
    }

    // 2. Xóa sinh viên khỏi lớp - POST
    public function enrollRemove($classId, $studentId)
    {
        $session = session();
        
        $this->enrollmentModel->where('class_id', $classId)
                              ->where('student_id', $studentId)->delete();
        
        $session->setFlashdata('success', 'Đã xóa sinh viên khỏi lớp.');
        return redirect()->to("/manager_class_detail.html/$classId");
    }

    // 3. Cập nhật bảng điểm - POST
    public function updateGrades($classId)
    {
        $session = session();
        
        // Lấy dữ liệu từ form
        $enrollmentIds = $this->request->getPost('enrollment_id');
        $midtermScores = $this->request->getPost('midterm_score');
        $finalScores = $this->request->getPost('final_score');
        $diligenceScores = $this->request->getPost('diligence_score');

        if (!$enrollmentIds) {
            $session->setFlashdata('error', 'Dữ liệu trống.');
            return redirect()->back();
        }

        // Kiểm tra lớp có bị khóa không
        $class = $this->classModel->find($classId);
        if ($class && $class['is_locked'] == 1) {
            $session->setFlashdata('error', 'Lớp học đã bị KHÓA. Không thể sửa điểm.');
            return redirect()->back();
        }

        $updatedCount = 0;
        foreach ($enrollmentIds as $index => $enrollId) {
            $dataToUpdate = [];
            
            if (isset($midtermScores[$index])) {
                $dataToUpdate['midterm_score'] = ($midtermScores[$index] === '') ? null : $midtermScores[$index];
            }
            if (isset($finalScores[$index])) {
                $dataToUpdate['final_score'] = ($finalScores[$index] === '') ? null : $finalScores[$index];
            }
            if (isset($diligenceScores[$index])) {
                $dataToUpdate['diligence_score'] = ($diligenceScores[$index] === '') ? null : $diligenceScores[$index];
            }

            if (!empty($dataToUpdate)) {
                $this->enrollmentModel->update($enrollId, $dataToUpdate);
                $updatedCount++;
            }
        }

        $session->setFlashdata('success', "Đã lưu điểm cho $updatedCount sinh viên.");
        return redirect()->to("/manager_class_detail.html/$classId");
    }
}
