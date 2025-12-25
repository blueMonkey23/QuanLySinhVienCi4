<?php

namespace App\Models;

use CodeIgniter\Model;

class ScheduleModel extends Model
{
    protected $table            = 'schedules';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['class_id', 'day_of_week', 'start_time', 'end_time', 'room'];

    // Kiểm tra trùng phòng học
    public function checkRoomConflict($room, $day, $start, $end, $excludeClassId = null)
    {
        $builder = $this->db->table('schedules')
            ->join('classes', 'schedules.class_id = classes.id')
            ->where('schedules.room', $room)
            ->where('schedules.day_of_week', $day)
            ->where('classes.is_locked', 0) // Chỉ check các lớp đang hoạt động
            ->groupStart()
                ->where("schedules.start_time <", $end)
                ->where("schedules.end_time >", $start)
            ->groupEnd();

        if ($excludeClassId) {
            $builder->where('classes.id !=', $excludeClassId);
        }

        return $builder->get()->getRow();
    }

    // Lấy lịch giảng dạy tổng hợp với filter
    public function getSchedulesWithDetails($filters = [])
    {
        $semesterId = $filters['semester_id'] ?? 1;
        $teacherId = $filters['teacher_id'] ?? null;
        $room = $filters['room'] ?? null;

        $builder = $this->db->table('classes')
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

        return $builder->orderBy('schedules.day_of_week', 'ASC')
                       ->orderBy('schedules.start_time', 'ASC')
                       ->get()
                       ->getResultArray();
    }
}