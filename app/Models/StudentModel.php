<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentModel extends Model
{
    protected $table            = 'students';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'user_id', 'student_code', 'name', 'dob', 
        'gender', 'address', 'phone', 'status', 'student_class_id'
    ];

    public function getStudentsWithUser($keyword = null)
    {
        $builder = $this->select('students.*, students.status as is_locked, users.email')
                        ->join('users', 'students.user_id = users.id', 'left');
        
        if ($keyword) {
            $builder->groupStart()
                ->like('students.student_code', $keyword)
                ->orLike('students.name', $keyword)
            ->groupEnd();
        }
        
        return $builder->orderBy('students.id', 'DESC')->findAll();
    }

    /**
     * Tạo sinh viên mới (User + Student + gán Role)
     * @return array ['success' => bool, 'message' => string, 'student_id' => int]
     */
    public function createStudentWithUser($data)
    {
        $db = \Config\Database::connect();
        $userModel = new \App\Models\UserModel();

        // Kiểm tra trùng
        if ($this->where('student_code', $data['studentCode'])->first()) {
            return ['success' => false, 'message' => 'Mã sinh viên đã tồn tại.'];
        }
        if ($userModel->where('email', $data['email'])->first()) {
            return ['success' => false, 'message' => 'Email đã được sử dụng.'];
        }

        // Transaction
        $db->transStart();

        $userId = $userModel->insert([
            'email' => $data['email'],
            'password_hash' => password_hash($data['studentCode'], PASSWORD_BCRYPT),
            'name' => $data['fullname']
        ]);

        $db->table('role_user')->insert(['user_id' => $userId, 'role_id' => 4]);

        $studentId = $this->insert([
            'user_id' => $userId,
            'student_code' => $data['studentCode'],
            'name' => $data['fullname'],
            'dob' => $data['dob'] ?: null,
            'gender' => $data['gender'] ?: 'other',
            'address' => $data['address'] ?: '',
            'status' => 1
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return ['success' => false, 'message' => 'Lỗi khi tạo sinh viên.'];
        }

        return ['success' => true, 'message' => 'Thêm sinh viên thành công!', 'student_id' => $studentId];
    }

    /**
     * Lấy thông tin sinh viên theo user_id
     */
    public function getByUserId($userId)
    {
        return $this->where('user_id', $userId)->first();
    }

    /**
     * Lấy lịch học và điểm của sinh viên
     */
    public function getStudentClasses($studentId)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('enrollments e')
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
            ->where('e.student_id', $studentId)
            ->orderBy('sch.day_of_week', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Lấy điểm của sinh viên
     */
    public function getStudentGrades($studentId)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('enrollments e')
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
            ->where('e.student_id', $studentId);

        return $builder->get()->getResultArray();
    }

    /**
     * Lấy lịch thi của sinh viên
     */
    public function getStudentExams($userId)
    {
        $db = \Config\Database::connect();
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

        $query = $db->query($sql, [$userId]);
        return $query->getResultArray();
    }
}