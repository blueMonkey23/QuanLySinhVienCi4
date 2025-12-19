<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;
use App\Models\ClassModel;
use App\Models\ScheduleModel;
use App\Models\SubjectModel;
use App\Models\TeacherModel;

class ClassController extends BaseController
{
    protected $classModel;
    protected $scheduleModel;
    protected $db;

    public function __construct()
    {
        $this->classModel = new ClassModel();
        $this->scheduleModel = new ScheduleModel();
        $this->db = \Config\Database::connect();
    }

    // 1. Danh sách lớp học - GET
    public function index()
    {
        $keyword = $this->request->getGet('q');
        $subjectId = $this->request->getGet('subject');

        $data = $this->classModel->getClassesWithDetails($keyword, $subjectId);

        // Lấy schedule cho mỗi class riêng biệt
        $data = array_map(function($item) {
            $item['class_id'] = $item['id'];
            
            // Lấy schedule riêng
            $schedule = $this->scheduleModel->where('class_id', $item['id'])->first();
            if ($schedule) {
                $item['day_of_week'] = $this->convertBitToDay($schedule['day_of_week']);
                $item['schedule_time'] = substr($schedule['start_time'], 0, 5) . '-' . substr($schedule['end_time'], 0, 5);
                $item['class_room'] = $schedule['room'];
            } else {
                $item['day_of_week'] = 'Chưa xếp';
                $item['schedule_time'] = 'Chưa có';
                $item['class_room'] = 'Chưa có';
            }
            
            return $item;
        }, $data);

        $viewData = [
            'classes' => $data,
            'keyword' => $keyword,
            'subjectId' => $subjectId
        ];

        return view('manager_classes', $viewData);
    }

    // 2. Form thêm lớp - GET
    public function addForm()
    {
        $subjectModel = new SubjectModel();
        $teacherModel = new TeacherModel();
        
        $viewData = [
            'subjects' => $subjectModel->findAll(),
            'teachers' => $teacherModel->findAll()
        ];
        
        return view('manager_class_add', $viewData);
    }

    // 3. Xử lý thêm lớp - POST
    public function create()
    {
        $session = session();
        
        // Validate
        if (empty($this->request->getPost('class_code')) || 
            empty($this->request->getPost('subject_id')) || 
            empty($this->request->getPost('teacher_id')) || 
            empty($this->request->getPost('class_room'))) {
            $session->setFlashdata('error', 'Vui lòng điền đầy đủ thông tin bắt buộc.');
            return redirect()->back()->withInput();
        }

        $data = [
            'class_code' => $this->request->getPost('class_code'),
            'subject_id' => $this->request->getPost('subject_id'),
            'teacher_id' => $this->request->getPost('teacher_id'),
            'class_room' => $this->request->getPost('class_room'),
            'day_of_week' => $this->request->getPost('day_of_week'),
            'schedule_time' => $this->request->getPost('schedule_time'),
            'format' => $this->request->getPost('format')
        ];

        // Chuyển đổi dữ liệu
        $dayInt = $this->convertDayToBit($data['day_of_week']);
        $times = explode('-', $data['schedule_time']);
        
        if (count($times) < 2) {
            $session->setFlashdata('error', 'Giờ học không hợp lệ.');
            return redirect()->back()->withInput();
        }

        // Kiểm tra trùng phòng
        $conflict = $this->scheduleModel->checkRoomConflict(
            $data['class_room'], 
            $dayInt, 
            $times[0], 
            $times[1]
        );

        if ($conflict) {
            $session->setFlashdata('error', "Xung đột phòng học! Phòng '{$data['class_room']}' đã có lớp {$conflict->class_code} học vào giờ này.");
            return redirect()->back()->withInput();
        }

        // Kiểm tra trùng mã lớp
        if ($this->classModel->where('class_code', $data['class_code'])->first()) {
            $session->setFlashdata('error', 'Mã lớp này đã tồn tại.');
            return redirect()->back()->withInput();
        }

        // Transaction Insert
        $this->db->transStart();

        $classId = $this->classModel->insert([
            'class_code'  => $data['class_code'],
            'subject_id'  => $data['subject_id'],
            'teacher_id'  => $data['teacher_id'],
            'semester_id' => 1,
            'format'      => $data['format'],
            'max_students'=> 60,
            'is_locked'   => 0
        ]);
        
        // Get the actual inserted ID
        if (!$classId) {
            $classId = $this->classModel->getInsertID();
        }

        $this->scheduleModel->insert([
            'class_id'    => $classId,
            'day_of_week' => $dayInt,
            'start_time'  => $times[0],
            'end_time'    => $times[1],
            'room'        => $data['class_room']
        ]);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            $session->setFlashdata('error', 'Lỗi hệ thống khi thêm lớp.');
            return redirect()->back()->withInput();
        }

        $session->setFlashdata('success', 'Thêm lớp học thành công!');
        return redirect()->to('/manager_classes');
    }

    // 4. Form sửa lớp - GET
    public function editForm($id)
    {
        $subjectModel = new SubjectModel();
        $teacherModel = new TeacherModel();
        
        $class = $this->classModel->select('classes.*, schedules.day_of_week, schedules.start_time, schedules.end_time, schedules.room as class_room')
                                  ->join('schedules', 'classes.id = schedules.class_id', 'left')
                                  ->find($id);

        if (!$class) {
            session()->setFlashdata('error', 'Không tìm thấy lớp học.');
            return redirect()->to('/manager_classes');
        }
        
        $class['day_of_week_text'] = $this->convertBitToDay($class['day_of_week']);
        $class['schedule_time'] = substr($class['start_time'] ?? '00:00', 0, 5) . '-' . substr($class['end_time'] ?? '00:00', 0, 5);

        $viewData = [
            'class' => $class,
            'subjects' => $subjectModel->findAll(),
            'teachers' => $teacherModel->findAll()
        ];
        
        return view('manager_class_edit', $viewData);
    }

    // 5. Xử lý sửa lớp - POST
    public function update($id)
    {
        $session = session();
        
        $data = [
            'class_code' => $this->request->getPost('class_code'),
            'subject_id' => $this->request->getPost('subject_id'),
            'teacher_id' => $this->request->getPost('teacher_id'),
            'class_room' => $this->request->getPost('class_room'),
            'day_of_week' => $this->request->getPost('day_of_week'),
            'schedule_time' => $this->request->getPost('schedule_time'),
            'format' => $this->request->getPost('format')
        ];

        $dayInt = $this->convertDayToBit($data['day_of_week']);
        $times = explode('-', $data['schedule_time']);

        // Kiểm tra trùng phòng (trừ chính lớp này)
        $conflict = $this->scheduleModel->checkRoomConflict(
            $data['class_room'], 
            $dayInt, 
            $times[0], 
            $times[1],
            $id
        );

        if ($conflict) {
            $session->setFlashdata('error', "Xung đột phòng học! Phòng '{$data['class_room']}' trùng lịch với lớp {$conflict->class_code}.");
            return redirect()->back()->withInput();
        }

        // Transaction Update
        $this->db->transStart();

        $this->classModel->update($id, [
            'class_code' => $data['class_code'],
            'subject_id' => $data['subject_id'],
            'teacher_id' => $data['teacher_id'],
            'format'     => $data['format']
        ]);

        $this->scheduleModel->where('class_id', $id)->set([
            'day_of_week' => $dayInt,
            'start_time'  => $times[0],
            'end_time'    => $times[1],
            'room'        => $data['class_room']
        ])->update();

        $this->db->transComplete();

        $session->setFlashdata('success', 'Cập nhật lớp học thành công!');
        return redirect()->to('/manager_classes');
    }

    // 6. Xóa lớp - POST
    public function delete($id)
    {
        $session = session();
        
        if ($this->classModel->delete($id)) {
            $session->setFlashdata('success', 'Đã xóa lớp học.');
        } else {
            $session->setFlashdata('error', 'Không tìm thấy lớp để xóa.');
        }
        
        return redirect()->to('/manager_classes');
    }

    // 7. Khóa/Mở khóa lớp - POST
    public function toggleLock($id)
    {
        $session = session();
        
        $class = $this->classModel->find($id);
        if (!$class) {
            $session->setFlashdata('error', 'Lớp không tồn tại.');
            return redirect()->to('/manager_classes');
        }

        $newStatus = $class['is_locked'] == 1 ? 0 : 1;
        $this->classModel->update($id, ['is_locked' => $newStatus]);
        
        $msg = $newStatus ? 'Đã KHÓA lớp học.' : 'Đã MỞ KHÓA lớp học.';
        $session->setFlashdata('success', $msg);
        
        return redirect()->to('/manager_classes');
    }

    // 8. Chi tiết lớp - GET
    public function detail($id)
    {
        // Lấy thông tin lớp học
        $classInfo = $this->classModel->asArray()
            ->select('classes.*, classes.id as class_id, 
                      subjects.name as subject_name, subjects.subject_code,
                      semesters.name as semester_name, 
                      CONCAT(teachers.first_name, " ", teachers.last_name) as teacher_name')
            ->join('subjects', 'classes.subject_id = subjects.id', 'left')
            ->join('teachers', 'classes.teacher_id = teachers.id', 'left')
            ->join('semesters', 'classes.semester_id = semesters.id', 'left')
            ->where('classes.id', $id)
            ->first();

        if (!$classInfo) {
            session()->setFlashdata('error', 'Không tìm thấy lớp học.');
            return redirect()->to('/manager_classes');
        }

        // Lấy schedule riêng biệt để tránh duplicate do JOIN
        $schedule = $this->scheduleModel->where('class_id', $id)->first();
        
        if ($schedule) {
            $classInfo['day_of_week'] = $this->convertBitToDay($schedule['day_of_week']);
            $classInfo['schedule_time'] = substr($schedule['start_time'] ?? '00:00',0,5) . '-' . substr($schedule['end_time'] ?? '00:00',0,5);
            $classInfo['room'] = $schedule['room'];
        } else {
            $classInfo['day_of_week'] = 'Chưa xếp';
            $classInfo['schedule_time'] = 'Chưa có';
            $classInfo['room'] = 'Chưa có';
        }
        
        // Lấy danh sách sinh viên
        $enrollmentModel = new \App\Models\EnrollmentModel();
        $students = $enrollmentModel->select('enrollments.*, 
                                             enrollments.midterm_score, enrollments.final_score, enrollments.diligence_score,
                                             students.student_code, students.name as student_name, students.name')
                                    ->join('students', 'enrollments.student_id = students.id')
                                    ->where('enrollments.class_id', $id)
                                    ->orderBy('students.name', 'ASC')
                                    ->findAll();

        $viewData = [
            'class' => $classInfo,
            'students' => $students
        ];

        return view('manager_class_detail', $viewData);
    }

    // Helper Functions
    private function convertDayToBit($text) {
        $map = [
            'Thứ Hai' => 2, 'Thứ Ba' => 3, 'Thứ Tư' => 4, 
            'Thứ Năm' => 5, 'Thứ Sáu' => 6, 'Thứ Bảy' => 7, 'Chủ Nhật' => 8
        ];
        return $map[$text] ?? 2;
    }

    private function convertBitToDay($bit) {
        $map = [
            2 => 'Thứ Hai', 3 => 'Thứ Ba', 4 => 'Thứ Tư', 
            5 => 'Thứ Năm', 6 => 'Thứ Sáu', 7 => 'Thứ Bảy', 8 => 'Chủ Nhật'
        ];
        return $map[$bit] ?? 'Thứ Hai';
    }
}
