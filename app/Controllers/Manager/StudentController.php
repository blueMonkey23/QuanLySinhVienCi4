<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\UserModel;

class StudentController extends BaseController
{
    protected $studentModel;
    protected $userModel;

    public function __construct()
    {
        $this->studentModel = new StudentModel();
        $this->userModel = new UserModel();
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

        return view('manager/students', $viewData);
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

        // Gọi Model để tạo sinh viên
        $result = $this->studentModel->createStudentWithUser([
            'studentCode' => $studentCode,
            'fullname' => $fullname,
            'email' => $email,
            'dob' => $dob,
            'gender' => $gender,
            'address' => $address
        ]);

        if (!$result['success']) {
            $session->setFlashdata('error', $result['message']);
            return redirect()->back()->withInput();
        }

        $session->setFlashdata('success', $result['message']);
        return redirect()->to('/manager_students');
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
        
        return redirect()->to('/manager_students');
    }

    // 4. Khóa/Mở khóa sinh viên - POST
    public function toggleLock($id)
    {
        $session = session();
        
        $student = $this->studentModel->find($id);
        
        if (!$student) {
            $session->setFlashdata('error', 'Không tìm thấy sinh viên.');
            return redirect()->to('/manager_students');
        }

        $newStatus = ($student['status'] == 1) ? 0 : 1;
        $this->studentModel->update($id, ['status' => $newStatus]);

        $msg = ($newStatus == 1 ? 'Đã mở khóa ' : 'Đã khóa ') . 'hồ sơ sinh viên.';
        $session->setFlashdata('success', $msg);
        
        return redirect()->to('/manager_students');
    }
}
