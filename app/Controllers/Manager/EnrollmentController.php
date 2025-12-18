<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;
use App\Models\EnrollmentModel;
use App\Models\StudentModel;
use App\Models\ClassModel;
use CodeIgniter\API\ResponseTrait;

class EnrollmentController extends BaseController
{
    use ResponseTrait;
    
    protected $enrollmentModel;
    protected $studentModel;
    protected $classModel;

    public function __construct()
    {
        $this->enrollmentModel = new EnrollmentModel();
        $this->studentModel = new StudentModel();
        $this->classModel = new ClassModel();
    }

    // 1. Thêm/Xóa sinh viên khỏi lớp
    // URL: backend/manager_enroll_student.php
    public function enroll()
    {
        $input = $this->request->getJSON(true);
        $classId = $input['class_id'] ?? null;
        $studentCode = $input['student_code'] ?? null;
        $action = $input['action'] ?? null;

        if (!$classId || !$studentCode || !$action) {
            return $this->fail('Thiếu thông tin.', 400);
        }

        // Tìm sinh viên theo mã
        $student = $this->studentModel->where('student_code', $studentCode)->first();
        if (!$student) {
            return $this->failNotFound("Không tìm thấy sinh viên với mã $studentCode.");
        }

        if ($action === 'add') {
            // Kiểm tra sĩ số lớp
            $currentCount = $this->enrollmentModel->where('class_id', $classId)->countAllResults();
            $class = $this->classModel->find($classId);
            
            if ($currentCount >= $class['max_students']) {
                return $this->fail('Lớp đã đầy sĩ số.', 409);
            }

            // Kiểm tra đã tồn tại chưa
            $exists = $this->enrollmentModel->where('class_id', $classId)
                                            ->where('student_id', $student['id'])->first();
            if ($exists) {
                return $this->fail('Sinh viên này đã có trong lớp.', 409);
            }

            $this->enrollmentModel->insert([
                'class_id' => $classId,
                'student_id' => $student['id']
            ]);
            return $this->respondCreated(['success' => true, 'message' => "Đã thêm $studentCode vào lớp."]);

        } elseif ($action === 'remove') {
            $this->enrollmentModel->where('class_id', $classId)
                                  ->where('student_id', $student['id'])->delete();
            return $this->respond(['success' => true, 'message' => "Đã xóa $studentCode khỏi lớp."]);
        }
    }

    // 2. Cập nhật bảng điểm
    // URL: backend/manager_update_grades.php
    public function updateGrades()
    {
        $input = $this->request->getJSON(true);
        $grades = $input['grades'] ?? [];

        if (empty($grades)) return $this->fail('Dữ liệu trống.', 400);

        // Kiểm tra lớp có bị khóa không (Lấy từ enrollment đầu tiên)
        $firstEnroll = $this->enrollmentModel->find($grades[0]['enrollment_id']);
        if ($firstEnroll) {
            $class = $this->classModel->find($firstEnroll['class_id']);
            if ($class && $class['is_locked'] == 1) {
                return $this->fail('Lớp học đã bị KHÓA. Không thể sửa điểm.', 403);
            }
        }

        $updatedCount = 0;
        foreach ($grades as $g) {
            $dataToUpdate = [];
            // Chỉ cập nhật nếu giá trị hợp lệ (0-10) hoặc null
            if (isset($g['midterm_score'])) $dataToUpdate['midterm_score'] = ($g['midterm_score'] === '') ? null : $g['midterm_score'];
            if (isset($g['final_score']))   $dataToUpdate['final_score']   = ($g['final_score'] === '') ? null : $g['final_score'];
            if (isset($g['diligence_score'])) $dataToUpdate['diligence_score'] = ($g['diligence_score'] === '') ? null : $g['diligence_score'];

            if (!empty($dataToUpdate)) {
                $this->enrollmentModel->update($g['enrollment_id'], $dataToUpdate);
                $updatedCount++;
            }
        }

        return $this->respond(['success' => true, 'message' => "Đã lưu điểm cho $updatedCount sinh viên."]);
    }
}