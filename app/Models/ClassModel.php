<?php

namespace App\Models;

use CodeIgniter\Model;

class ClassModel extends Model
{
    protected $table            = 'classes';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'class_code', 'subject_id', 'semester_id', 'teacher_id', 
        'format', 'max_students', 'is_locked', 'created_at'
    ];

    // Hàm lấy danh sách lớp đầy đủ thông tin (Join với Subject, Teacher, Semester)
    public function getClassesWithDetails($keyword = null, $subjectId = null)
    {
        $builder = $this->select('classes.*, subjects.name as subject_name, subjects.subject_code, 
                                  semesters.name as semester_name, semesters.end_date as semester_end_date,
                                  CONCAT(teachers.first_name, " ", teachers.last_name) as teacher_name')
                        ->join('subjects', 'classes.subject_id = subjects.id', 'left')
                        ->join('semesters', 'classes.semester_id = semesters.id', 'left')
                        ->join('teachers', 'classes.teacher_id = teachers.id', 'left');

        if ($keyword) {
            $builder->groupStart()
                ->like('classes.class_code', $keyword)
                ->orLike('subjects.name', $keyword)
                ->orLike('CONCAT(teachers.first_name, " ", teachers.last_name)', $keyword)
            ->groupEnd();
        }

        if ($subjectId && $subjectId !== 'all') {
            $builder->where('classes.subject_id', $subjectId);
        }

        // Subquery đếm số sinh viên hiện tại
        $builder->select('(SELECT COUNT(*) FROM enrollments WHERE enrollments.class_id = classes.id) as current_students');

        return $builder->findAll();
    }

    /**
     * Tạo lớp học mới kèm nhiều schedule
     * @param array $classData - Dữ liệu lớp học
     * @param array $schedulesData - Mảng các schedule
     * @return array ['success' => bool, 'message' => string, 'class_id' => int]
     */
    public function createClassWithSchedule($classData, $schedulesData)
    {
        $db = \Config\Database::connect();
        $scheduleModel = new \App\Models\ScheduleModel();

        // Kiểm tra trùng mã lớp
        if ($this->where('class_code', $classData['class_code'])->first()) {
            return ['success' => false, 'message' => 'Mã lớp này đã tồn tại.'];
        }

        // Kiểm tra trùng phòng cho tất cả các lịch học
        foreach ($schedulesData as $schedule) {
            $conflict = $scheduleModel->checkRoomConflict(
                $schedule['room'],
                $schedule['day_of_week'],
                $schedule['start_time'],
                $schedule['end_time']
            );

            if ($conflict) {
                return [
                    'success' => false,
                    'message' => "Xung đột phòng học! Phòng '{$schedule['room']}' đã có lớp {$conflict->class_code} học vào {$schedule['day_of_week']} ({$schedule['start_time']}-{$schedule['end_time']})."
                ];
            }
        }

        // Transaction
        $db->transStart();

        $classId = $this->insert($classData);
        if (!$classId) {
            $classId = $this->getInsertID();
        }

        // Thêm tất cả các lịch học
        foreach ($schedulesData as $schedule) {
            $scheduleModel->insert([
                'class_id'    => $classId,
                'day_of_week' => $schedule['day_of_week'],
                'start_time'  => $schedule['start_time'],
                'end_time'    => $schedule['end_time'],
                'room'        => $schedule['room']
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return ['success' => false, 'message' => 'Lỗi hệ thống khi thêm lớp.'];
        }

        return ['success' => true, 'message' => 'Thêm lớp học thành công!', 'class_id' => $classId];
    }

    /**
     * Cập nhật lớp học kèm schedule
     * @return array ['success' => bool, 'message' => string]
     */
    public function updateClassWithSchedule($classId, $classData, $scheduleData)
    {
        $db = \Config\Database::connect();
        $scheduleModel = new \App\Models\ScheduleModel();

        // Kiểm tra trùng phòng (trừ chính lớp này)
        $conflict = $scheduleModel->checkRoomConflict(
            $scheduleData['room'],
            $scheduleData['day_of_week'],
            $scheduleData['start_time'],
            $scheduleData['end_time'],
            $classId
        );

        if ($conflict) {
            return [
                'success' => false,
                'message' => "Xung đột phòng học! Phòng '{$scheduleData['room']}' trùng lịch với lớp {$conflict->class_code}."
            ];
        }

        // Transaction
        $db->transStart();

        $this->update($classId, $classData);

        $scheduleModel->where('class_id', $classId)->set([
            'day_of_week' => $scheduleData['day_of_week'],
            'start_time'  => $scheduleData['start_time'],
            'end_time'    => $scheduleData['end_time'],
            'room'        => $scheduleData['room']
        ])->update();

        $db->transComplete();

        if ($db->transStatus() === false) {
            return ['success' => false, 'message' => 'Lỗi hệ thống khi cập nhật lớp.'];
        }

        return ['success' => true, 'message' => 'Cập nhật lớp học thành công!'];
    }

    /**
     * Lấy thông tin chi tiết lớp kèm schedule
     */
    public function getClassDetail($classId)
    {
        $scheduleModel = new \App\Models\ScheduleModel();

        $classInfo = $this->asArray()
            ->select('classes.*, classes.id as class_id, 
                      subjects.name as subject_name, subjects.subject_code,
                      semesters.name as semester_name, 
                      CONCAT(teachers.first_name, " ", teachers.last_name) as teacher_name')
            ->join('subjects', 'classes.subject_id = subjects.id', 'left')
            ->join('teachers', 'classes.teacher_id = teachers.id', 'left')
            ->join('semesters', 'classes.semester_id = semesters.id', 'left')
            ->where('classes.id', $classId)
            ->first();

        if (!$classInfo) {
            return null;
        }

        // Lấy schedule riêng
        $schedule = $scheduleModel->where('class_id', $classId)->first();
        
        if ($schedule) {
            $classInfo['day_of_week'] = $schedule['day_of_week'];
            $classInfo['schedule_time'] = substr($schedule['start_time'] ?? '00:00', 0, 5) . '-' . substr($schedule['end_time'] ?? '00:00', 0, 5);
            $classInfo['room'] = $schedule['room'];
        } else {
            $classInfo['day_of_week'] = null;
            $classInfo['schedule_time'] = 'Chưa có';
            $classInfo['room'] = 'Chưa có';
        }

        return $classInfo;
    }
}