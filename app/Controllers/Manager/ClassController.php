<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;
use App\Models\ClassModel;
use App\Models\ScheduleModel;
use App\Models\SubjectModel;
use App\Models\TeacherModel;
use CodeIgniter\API\ResponseTrait;

class ClassController extends BaseController
{
    use ResponseTrait;

    protected $classModel;
    protected $scheduleModel;
    protected $db;

    public function __construct()
    {
        $this->classModel = new ClassModel();
        $this->scheduleModel = new ScheduleModel();
        $this->db = \Config\Database::connect();
    }

    // 1. API: Lấy danh sách lớp (Thay thế manager_classes.php)
    // URL: backend/manager_classes.php?q=...&subject=...
    public function index()
    {
        $keyword = $this->request->getGet('q');
        $subjectId = $this->request->getGet('subject');

        // Gọi hàm getClassesWithDetails đã viết trong Model ở bước trước
        $data = $this->classModel->getClassesWithDetails($keyword, $subjectId);

        return $this->respond([
            'success' => true,
            'data' => $data
        ]);
    }

    // 2. API: Thêm lớp mới (Thay thế manager_class_add.php)
    // URL: backend/manager_class_add.php (POST)
    public function create()
    {
        $input = $this->request->getJSON(true);
        $data = $input['data'] ?? [];

        // Validate dữ liệu cơ bản
        if (empty($data['class_id']) || empty($data['subject_id']) || empty($data['teacher_id']) || empty($data['class_room'])) {
            return $this->fail('Vui lòng điền đầy đủ thông tin bắt buộc.', 400);
        }

        // Chuyển đổi dữ liệu
        $dayInt = $this->convertDayToBit($data['day_of_week']); // Hàm helper ở dưới
        $times = explode('-', $data['schedule_time']); // "07:30-11:30" -> ["07:30", "11:30"]
        
        if (count($times) < 2) return $this->fail('Giờ học không hợp lệ.', 400);

        // 2.1. Kiểm tra trùng phòng (Room Conflict)
        // Gọi hàm checkRoomConflict trong ScheduleModel
        $conflict = $this->scheduleModel->checkRoomConflict(
            $data['class_room'], 
            $dayInt, 
            $times[0], 
            $times[1]
        );

        if ($conflict) {
            return $this->fail("Xung đột phòng học! Phòng '{$data['class_room']}' đã có lớp {$conflict->class_code} học vào giờ này.", 409);
        }

        // 2.2. Kiểm tra trùng mã lớp (Class Code)
        if ($this->classModel->where('class_code', $data['class_id'])->first()) {
            return $this->fail('Mã lớp này đã tồn tại.', 409);
        }

        // 2.3. Transaction Insert (Lớp + Lịch)
        $this->db->transStart();

        // Insert Class
        $classId = $this->classModel->insert([
            'class_code'  => $data['class_id'],
            'subject_id'  => $data['subject_id'],
            'teacher_id'  => $data['teacher_id'],
            'semester_id' => 1, // Mặc định HK1 (logic cũ), nên sửa lại lấy động sau này
            'format'      => $data['format'],
            'max_students'=> 60,
            'is_locked'   => 0
        ]);

        // Insert Schedule
        $this->scheduleModel->insert([
            'class_id'    => $classId,
            'day_of_week' => $dayInt,
            'start_time'  => $times[0],
            'end_time'    => $times[1],
            'room'        => $data['class_room']
        ]);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return $this->failServerError('Lỗi hệ thống khi thêm lớp.');
        }

        return $this->respondCreated(['success' => true, 'message' => 'Thêm lớp học thành công!']);
    }

    // 3. API: Sửa lớp (Thay thế manager_class_edit.php)
    // URL: backend/manager_class_edit.php (POST)
    public function update()
    {
        $input = $this->request->getJSON(true);
        $data = $input['data'] ?? [];
        $id = $input['id'] ?? 0;

        if (!$id) return $this->fail('Thiếu ID lớp học.', 400);

        // Chuyển đổi dữ liệu
        $dayInt = $this->convertDayToBit($data['day_of_week']);
        $times = explode('-', $data['schedule_time']);

        // 3.1. Kiểm tra trùng phòng (Trừ chính lớp này ra)
        $conflict = $this->scheduleModel->checkRoomConflict(
            $data['class_room'], 
            $dayInt, 
            $times[0], 
            $times[1],
            $id // Exclude ID hiện tại
        );

        if ($conflict) {
            return $this->fail("Xung đột phòng học! Phòng '{$data['class_room']}' trùng lịch với lớp {$conflict->class_code}.", 409);
        }

        // 3.2. Transaction Update
        $this->db->transStart();

        // Update Class
        $this->classModel->update($id, [
            'class_code' => $data['class_id'],
            'subject_id' => $data['subject_id'],
            'teacher_id' => $data['teacher_id'],
            'format'     => $data['format']
        ]);

        // Update Schedule (Giả sử 1 lớp 1 lịch như DB cũ)
        // Tìm lịch cũ để update, hoặc xóa đi thêm lại. Ở đây dùng update based on class_id
        $this->scheduleModel->where('class_id', $id)->set([
            'day_of_week' => $dayInt,
            'start_time'  => $times[0],
            'end_time'    => $times[1],
            'room'        => $data['class_room']
        ])->update();

        $this->db->transComplete();

        return $this->respond(['success' => true, 'message' => 'Cập nhật lớp học thành công!']);
    }

    // 4. API: Lấy thông tin 1 lớp để sửa (Thay thế manager_class_get.php)
    public function show()
    {
        $id = $this->request->getGet('id');
        
        // Cần join bảng schedules để lấy lịch
        $class = $this->classModel->select('classes.*, schedules.day_of_week, schedules.start_time, schedules.end_time, schedules.room')
                                  ->join('schedules', 'classes.id = schedules.class_id', 'left')
                                  ->find($id);

        if (!$class) return $this->failNotFound('Không tìm thấy lớp học.');
        
        // Convert ngược day_of_week (2 -> "Thứ Hai") cho Frontend binding
        $class['day_of_week_text'] = $this->convertBitToDay($class['day_of_week']);
        // Tạo string schedule_time "07:30-11:30" để match value select box
        $class['schedule_time'] = substr($class['start_time'], 0, 5) . '-' . substr($class['end_time'], 0, 5);

        return $this->respond(['success' => true, 'data' => $class]);
    }

    // 5. API: Xóa lớp (Thay thế manager_class_delete.php)
    public function delete()
    {
        $input = $this->request->getJSON(true);
        $id = $input['id'] ?? 0;

        if ($this->classModel->delete($id)) {
            return $this->respondDeleted(['success' => true, 'message' => 'Đã xóa lớp học.']);
        }
        return $this->failNotFound('Không tìm thấy lớp để xóa.');
    }

    // 6. API: Khóa/Mở khóa lớp (Thay thế manager_class_lock.php)
    public function toggleLock()
    {
        $input = $this->request->getJSON(true);
        $id = $input['id'] ?? 0;
        
        $class = $this->classModel->find($id);
        if (!$class) return $this->failNotFound('Lớp không tồn tại.');

        // Đảo ngược trạng thái: 0 -> 1, 1 -> 0
        $newStatus = $class['is_locked'] == 1 ? 0 : 1;
        $this->classModel->update($id, ['is_locked' => $newStatus]);
        
        $msg = $newStatus ? 'Đã KHÓA lớp học.' : 'Đã MỞ KHÓA lớp học.';
        return $this->respond(['success' => true, 'message' => $msg]);
    }

    // 7. API: Chi tiết lớp học + Danh sách sinh viên (Thay manager_class_detail.php)
    public function detail()
    {
        $id = $this->request->getGet('id');
        
        // Lấy thông tin lớp
        $class = $this->classModel->getClassesWithDetails(null, null); 
        // Note: Hàm getClassesWithDetails trả về mảng tất cả, ta cần filter lại hoặc viết hàm findWithDetails
        // Để nhanh, ta dùng query builder trực tiếp tại đây cho tối ưu 1 record:
        
        $classInfo = $this->classModel->select('classes.*, subjects.name as subject_name, semesters.name as semester_name, 
                                                CONCAT(teachers.first_name, " ", teachers.last_name) as teacher_name,
                                                schedules.room, schedules.start_time, schedules.end_time, schedules.day_of_week')
                                      ->join('subjects', 'classes.subject_id = subjects.id')
                                      ->join('teachers', 'classes.teacher_id = teachers.id')
                                      ->join('semesters', 'classes.semester_id = semesters.id')
                                      ->join('schedules', 'classes.id = schedules.class_id')
                                      ->find($id);

        if (!$classInfo) return $this->failNotFound('Không tìm thấy lớp.');

        // Format lại dữ liệu hiển thị
        $classInfo['schedule_display'] = $this->convertBitToDay($classInfo['day_of_week']) . ' (' . substr($classInfo['start_time'],0,5) . '-' . substr($classInfo['end_time'],0,5) . ')';

        // Lấy danh sách sinh viên (Dùng EnrollmentModel)
        $enrollmentModel = new \App\Models\EnrollmentModel();
        $students = $enrollmentModel->getStudentsInClass($id);

        return $this->respond([
            'success' => true,
            'data' => [
                'class_info' => $classInfo,
                'students' => $students
            ]
        ]);
    }

    // --- Helper Functions (Private) ---

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