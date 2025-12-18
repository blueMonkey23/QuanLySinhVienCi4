<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\UserModel;

class StudentController extends BaseController
{
    protected $studentModel;
    protected $userModel;
    protected $db;

    public function __construct()
    {
        $this->studentModel = new StudentModel();
        $this->userModel = new UserModel();
        $this->db = \Config\Database::connect();
    }

    // 1. Danh sách sinh viên - GET
    public function index()
    {
        $keyword = $this->request->getGet('keyword');
        $students = $this->studentModel->getStudentsWithUser($keyword);
        
        $data = array_map(function($row) {
            $row['gender_text'] = ($row['gender'] == 'Nam') ? 'Nam' : (($row['gender'] == 'Nữ') ? 'Nữ' : 'Khác');
            $row['fullname'] = $row['name'];
            return $row;
        }, $students);

        $viewData = [
            'students' => $data,
            'keyword' => $keyword
        ];

        return view('manager_students', $viewData);
    }

    // 2. Thêm sinh viên - POST
    public function create()
    {
        $session = session();
        
        $studentCode = $this->request->getPost('studentCode');
        $fullname = $this->request->getPost('fullname');
        $email = $this->request->getPost('email');
        $dob = $this->request->getPost('dob');
        $gender = $this->request->getPost('gender');
        $address = $this->request->getPost('address');

        if (empty($studentCode) || empty($fullname) || empty($email)) {
            $session->setFlashdata('error', 'Vui lòng điền đầy đủ thông tin.');
            return redirect()->back()->withInput();
        }

        // Kiểm tra trùng
        if ($this->studentModel->where('student_code', $studentCode)->first()) {
            $session->setFlashdata('error', 'Mã sinh viên đã tồn tại.');
            return redirect()->back()->withInput();
        }
        if ($this->userModel->where('email', $email)->first()) {
            $session->setFlashdata('error', 'Email đã được sử dụng.');
            return redirect()->back()->withInput();
        }

        $this->db->transStart();

        $userId = $this->userModel->insert([
            'email' => $email,
            'password_hash' => password_hash($studentCode, PASSWORD_BCRYPT),
            'name' => $fullname
        ]);

        $this->db->table('role_user')->insert(['user_id' => $userId, 'role_id' => 4]);

        $this->studentModel->insert([
            'user_id' => $userId,
            'student_code' => $studentCode,
            'name' => $fullname,
            'dob' => $dob ?: null,
            'gender' => $gender ?: 'other',
            'address' => $address ?: '',
            'status' => 1
        ]);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            $session->setFlashdata('error', 'Lỗi khi tạo sinh viên.');
            return redirect()->back()->withInput();
        }

        $session->setFlashdata('success', 'Thêm sinh viên thành công!');
        return redirect()->to('/manager_students.html');
    }

    // 3. Cập nhật sinh viên - POST
    public function update($id)
    {
        $session = session();

        $updateData = [
            'name' => $this->request->getPost('fullname'),
            'dob' => $this->request->getPost('dob') ?: null,
            'gender' => $this->request->getPost('gender'),
            'address' => $this->request->getPost('address')
        ];

        if ($this->studentModel->update($id, $updateData)) {
            $session->setFlashdata('success', 'Cập nhật thành công!');
        } else {
            $session->setFlashdata('error', 'Cập nhật thất bại.');
        }
        
        return redirect()->to('/manager_students.html');
    }

    // 4. Khóa/Mở khóa sinh viên - POST
    public function toggleLock($id)
    {
        $session = session();
        
        $student = $this->studentModel->find($id);
        
        if (!$student) {
            $session->setFlashdata('error', 'Không tìm thấy sinh viên.');
            return redirect()->to('/manager_students.html');
        }

        $newStatus = ($student['status'] == 1) ? 0 : 1;
        $this->studentModel->update($id, ['status' => $newStatus]);

        $msg = ($newStatus == 1 ? 'Đã mở khóa ' : 'Đã khóa ') . 'hồ sơ sinh viên.';
        $session->setFlashdata('success', $msg);
        
        return redirect()->to('/manager_students.html');
    }
}
