<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class StudentController extends BaseController
{
    use ResponseTrait;

    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    // API 1: Lấy danh sách Lớp học, Điểm số & Lịch học (Thay get_student_classes.php)
    // URL: backend/student/classes
    public function getClasses()
    {
        $userId = session()->get('user_id');
        if (!$userId) return $this->failUnauthorized('Chưa đăng nhập');

        // 1. Lấy ID sinh viên từ User ID
        $student = $this->db->table('students')->where('user_id', $userId)->get()->getRow();
        if (!$student) return $this->failNotFound('Không tìm thấy thông tin sinh viên liên kết với tài khoản này.');

        // 2. Query dữ liệu (Join 5 bảng như file cũ)
        $builder = $this->db->table('enrollments e')
            ->select("
                c.class_code, 
                s.name AS subject_name, 
                c.format, 
                CONCAT(t.first_name, ' ', t.last_name) AS teacher_name,
                sch.day_of_week, 
                sch.start_time, 
                sch.end_time, 
                sch.room,
                e.midterm_score, 
                e.final_score,
                e.diligence_score
            ")
            ->join('classes c', 'e.class_id = c.id')
            ->join('subjects s', 'c.subject_id = s.id')
            ->join('teachers t', 'c.teacher_id = t.id', 'left')
            ->join('schedules sch', 'c.id = sch.class_id', 'left') // Giả định 1 lớp 1 lịch
            ->where('e.student_id', $student->id)
            ->orderBy('sch.day_of_week', 'ASC');

        $classes = $builder->get()->getResultArray();

        // 3. Format dữ liệu (Map thứ từ số sang chữ)
        $days = [2=>'Thứ Hai', 3=>'Thứ Ba', 4=>'Thứ Tư', 5=>'Thứ Năm', 6=>'Thứ Sáu', 7=>'Thứ Bảy', 8=>'Chủ Nhật'];
        
        foreach ($classes as &$cls) {
            $cls['day_text'] = $days[$cls['day_of_week']] ?? 'Chủ Nhật';
            // Tạo chuỗi hiển thị lịch
            $cls['schedule_display'] = $cls['day_text'] . ' (' . substr($cls['start_time'], 0, 5) . '-' . substr($cls['end_time'], 0, 5) . ')';
        }

        return $this->respond(['success' => true, 'data' => $classes]);
    }

    // API 2: Lấy lịch thi (Thay exam_schedule.php)
    // URL: backend/student/exams
    public function getExamSchedule()
    {
        $userId = session()->get('user_id');
        if (!$userId) return $this->failUnauthorized('Chưa đăng nhập');

        // Query trực tiếp từ bảng exams join qua enrollments
        // Logic: Sinh viên -> Enrollments -> Class -> Exam
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

        return $this->respond(['success' => true, 'data' => $exams]);
    }
}