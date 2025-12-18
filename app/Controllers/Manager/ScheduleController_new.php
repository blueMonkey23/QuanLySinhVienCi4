<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;
use App\Models\TeacherModel;

class ScheduleController extends BaseController
{
    public function index()
    {
        $teacherId = $this->request->getGet('teacher_id');
        $room = $this->request->getGet('room');
        $semesterId = 1; // Mặc định HK1

        $db = \Config\Database::connect();
        $builder = $db->table('classes')
                      ->select("classes.id as class_id, classes.class_code, classes.format,
                                subjects.name as subject_name,
                                CONCAT(teachers.first_name, ' ', teachers.last_name) as teacher_name,
                                schedules.day_of_week, schedules.start_time, schedules.end_time, schedules.room")
                      ->join('schedules', 'classes.id = schedules.class_id')
                      ->join('subjects', 'classes.subject_id = subjects.id', 'left')
                      ->join('teachers', 'classes.teacher_id = teachers.id', 'left')
                      ->where('classes.semester_id', $semesterId);

        if (!empty($teacherId)) {
            $builder->where('classes.teacher_id', $teacherId);
        }
        if (!empty($room)) {
            $builder->like('schedules.room', $room);
        }

        $schedules = $builder->orderBy('schedules.day_of_week', 'ASC')
                            ->orderBy('schedules.start_time', 'ASC')
                            ->get()->getResultArray();

        // Get teachers for filter
        $teacherModel = new TeacherModel();
        $teachers = $teacherModel->findAll();

        $viewData = [
            'schedules' => $schedules,
            'teachers' => $teachers,
            'selectedTeacherId' => $teacherId,
            'selectedRoom' => $room
        ];

        return view('manager_schedule', $viewData);
    }
}
