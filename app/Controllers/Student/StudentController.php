<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;

class StudentController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    // 1. Xem thông tin cá nhân
    public function information()
    {
        $userId = session()->get('user_id');
        
        $student = $this->db->table('students')->where('user_id', $userId)->get()->getRow();
        
        $viewData = [
            'student' => $student
        ];
        
        return view('information', $viewData);
    }

    // 2. Xem lịch học & điểm
    public function classes()
    {
        $userId = session()->get('user_id');
        
        $student = $this->db->table('students')->where('user_id', $userId)->get()->getRow();
        
        if (!$student) {
            session()->setFlashdata('error', 'Không tìm thấy thông tin sinh viên.');
            return redirect()->to('/index');
        }

        $builder = $this->db->table('enrollments e')
            ->select("
                c.class_code, 
                s.name AS subject_name, 
                c.format, 
                CONCAT(t.first_name, ' ', t.last_name) AS teacher_name,
                sch.day_of_week, 
                sch.start_time, 
                sch.end_time, 
                sch.room AS class_room,
                e.midterm_score, 
                e.final_score,
                e.diligence_score
            ")
            ->join('classes c', 'e.class_id = c.id')
            ->join('subjects s', 'c.subject_id = s.id')
            ->join('teachers t', 'c.teacher_id = t.id', 'left')
            ->join('schedules sch', 'c.id = sch.class_id', 'left')
            ->where('e.student_id', $student->id)
            ->orderBy('sch.day_of_week', 'ASC');

        $classes = $builder->get()->getResultArray();

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
        
        return view('class_schedule', $viewData);
    }

    // 3. Xem điểm
    public function grades()
    {
        $userId = session()->get('user_id');
        
        $student = $this->db->table('students')->where('user_id', $userId)->get()->getRow();
        
        if (!$student) {
            session()->setFlashdata('error', 'Không tìm thấy thông tin sinh viên.');
            return redirect()->to('/index');
        }

        $builder = $this->db->table('enrollments e')
            ->select("
                c.class_code, 
                s.name AS subject_name,
                s.credits,
                c.format, 
                CONCAT(t.first_name, ' ', t.last_name) AS teacher_name,
                e.midterm_score, 
                e.final_score,
                e.diligence_score
            ")
            ->join('classes c', 'e.class_id = c.id')
            ->join('subjects s', 'c.subject_id = s.id')
            ->join('teachers t', 'c.teacher_id = t.id', 'left')
            ->where('e.student_id', $student->id);

        $classes = $builder->get()->getResultArray();

        $viewData = [
            'classes' => $classes
        ];
        
        return view('grades', $viewData);
    }

    // 4. Xem lịch thi
    public function exams()
    {
        $userId = session()->get('user_id');

        $sql = "SELECT 
                    s.name AS subject_name,
                    s.subject_code,
                    c.class_code,
                    ex.type,
                    ex.format,
                    ex.exam_date,
                    ex.start_time,
                    ex.end_time,
                    ex.room
                FROM exams ex
                JOIN classes c ON ex.class_id = c.id
                JOIN subjects s ON c.subject_id = s.id
                JOIN enrollments en ON c.id = en.class_id
                JOIN students st ON en.student_id = st.id
                WHERE st.user_id = ?
                ORDER BY ex.exam_date ASC, ex.start_time ASC";

        $query = $this->db->query($sql, [$userId]);
        $exams = $query->getResultArray();

        $viewData = [
            'exams' => $exams
        ];
        
        return view('exam_schedule', $viewData);
    }
}
