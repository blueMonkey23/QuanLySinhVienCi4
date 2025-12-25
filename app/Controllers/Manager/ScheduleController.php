<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;
use App\Models\ScheduleModel;
use App\Models\SemesterModel;

class ScheduleController extends BaseController
{
    public function index()
    {
        $semesterId = $this->request->getGet('semester_id');
        $teacherId = $this->request->getGet('teacher_id');
        $room = $this->request->getGet('room');

        $semesterModel = new SemesterModel();
        $semesters = $semesterModel->getAllSemesters();
        
        // Nếu không chọn học kỳ, dùng học kỳ hiện tại
        if (empty($semesterId)) {
            $currentSemester = $semesterModel->getCurrentSemester();
            $semesterId = $currentSemester['id'] ?? 1;
        }

        $scheduleModel = new ScheduleModel();
        $data = $scheduleModel->getSchedulesWithDetails([
            'semester_id' => $semesterId,
            'teacher_id' => $teacherId,
            'room' => $room
        ]);

        $viewData = [
            'schedules' => $data,
            'semesters' => $semesters,
            'semesterId' => $semesterId,
            'teacherId' => $teacherId,
            'room' => $room
        ];
        
        return view('manager/schedule', $viewData);
    }
}
