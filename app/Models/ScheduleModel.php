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
}