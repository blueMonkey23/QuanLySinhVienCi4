<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Models\StudentModel;

class StudentController extends BaseController
{
    protected $studentModel;

    public function __construct()
    {
        $this->studentModel = new StudentModel();
    }

    // 1. Xem thông tin cá nhân
    public function information()
    {
        $userId = session()->get('user_id');
        $student = $this->studentModel->getByUserId($userId);
        
        $viewData = [
            'student' => $student
        ];
        
        return view('student/information', $viewData);
    }

    // 2. Xem lịch học & điểm
    public function classes()
    {
        $userId = session()->get('user_id');
        $student = $this->studentModel->getByUserId($userId);
        
        if (!$student) {
            session()->setFlashdata('error', 'Không tìm thấy thông tin sinh viên.');
            return redirect()->to('/index');
        }

        $classes = $this->studentModel->getStudentClasses($student['id']);

        $days = [2=>'Thứ Hai', 3=>'Thứ Ba', 4=>'Thứ Tư', 5=>'Thứ Năm', 6=>'Thứ Sáu', 7=>'Thứ Bảy', 8=>'Chủ Nhật'];
        
        foreach ($classes as &$cls) {
            // Convert day_of_week from TINYINT to text for view comparison
            $cls['day_of_week'] = $days[$cls['day_of_week']] ?? 'Chủ Nhật';
            // Create schedule_time in format HH:MM-HH:MM for view comparison
            $cls['schedule_time'] = substr($cls['start_time'], 0, 5) . '-' . substr($cls['end_time'], 0, 5);
        }

        $viewData = [
            'classes' => $classes
        ];
        
        return view('student/class_schedule', $viewData);
    }

    // 3. Xem điểm
    public function grades()
    {
        $userId = session()->get('user_id');
        $student = $this->studentModel->getByUserId($userId);
        
        if (!$student) {
            session()->setFlashdata('error', 'Không tìm thấy thông tin sinh viên.');
            return redirect()->to('/index');
        }

        $classes = $this->studentModel->getStudentGrades($student['id']);

        $viewData = [
            'classes' => $classes
        ];
        
        return view('student/grades', $viewData);
    }

    // 4. Xem lịch thi
    public function exams()
    {
        $userId = session()->get('user_id');
        $exams = $this->studentModel->getStudentExams($userId);

        $viewData = [
            'exams' => $exams
        ];
        
        return view('student/exam_schedule', $viewData);
    }
}
