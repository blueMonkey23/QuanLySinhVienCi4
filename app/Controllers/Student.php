<?php

namespace App\Controllers;

use App\Models\StudentModel;
use CodeIgniter\API\ResponseTrait;

class Student extends BaseController
{
    use ResponseTrait;

    protected $studentModel;
    protected $db;

    public function __construct()
    {
        $this->studentModel = new StudentModel();
        $this->db = \Config\Database::connect();
    }

    // 1. Hiển thị giao diện
    public function index()
    {
        return view('student_index');
    }

    // 2. API Lấy danh sách
    public function list()
    {
        $keyword = $this->request->getGet('keyword');

        $builder = $this->db->table('students');
        $builder->select('students.*, users.email');
        $builder->join('users', 'students.user_id = users.id', 'left');
        
        if ($keyword) {
            $builder->groupStart()
                ->like('students.student_code', $keyword)
                ->orLike('students.name', $keyword)
            ->groupEnd();
        }
        $builder->orderBy('students.id', 'DESC');
        
        $data = $builder->get()->getResultArray();

        $result = array_map(function($row){
            $genderShow = 'Khác';
            if ($row['gender'] == 'male') $genderShow = 'Nam';
            if ($row['gender'] == 'female') $genderShow = 'Nữ';
            
            $row['gender_text'] = $genderShow;
            $row['fullname'] = $row['name']; 
            return $row;
        }, $data);

        return $this->respond(['success' => true, 'data' => $result]);
    }

    public function create()
    {
        $json = $this->request->getJSON();

        // Validate input
        if (empty($json->student_code) || empty($json->fullname) || empty($json->email)) {
            return $this->respond(['success' => false, 'message' => 'Thiếu thông tin bắt buộc']);
        }

        $genderDB = 'other';
        if ($json->gender == 'Nam') $genderDB = 'male';
        if ($json->gender == 'Nữ') $genderDB = 'female';

        // Kiểm tra mã sinh viên đã tồn tại chưa
        $existingStudent = $this->db->table('students')
            ->where('student_code', $json->student_code)
            ->get()
            ->getRow();
        
        if ($existingStudent) {
            return $this->respond(['success' => false, 'message' => 'Mã sinh viên đã tồn tại']);
        }

        // Kiểm tra email đã tồn tại chưa
        $existingEmail = $this->db->table('users')
            ->where('email', $json->email)
            ->get()
            ->getRow();
        
        if ($existingEmail) {
            return $this->respond(['success' => false, 'message' => 'Email đã tồn tại']);
        }

        $this->db->transStart();

        try {
            // B1: Tạo User
            $passHash = password_hash($json->student_code, PASSWORD_DEFAULT);
            $userInserted = $this->db->table('users')->insert([
                'name' => $json->fullname,
                'email' => $json->email,
                'password_hash' => $passHash
            ]);

            if (!$userInserted) {
                throw new \Exception('Không thể tạo user: ' . json_encode($this->db->error()));
            }

            $userId = $this->db->insertID();

            if (!$userId) {
                throw new \Exception('Không lấy được user ID sau khi insert');
            }

            // B2: Gán quyền (kiểm tra bảng role_user có tồn tại không)
            if ($this->db->tableExists('role_user')) {
                $roleInserted = $this->db->table('role_user')->insert([
                    'user_id' => $userId, 
                    'role_id' => 4
                ]);
                
                if (!$roleInserted) {
                    throw new \Exception('Không thể gán quyền: ' . json_encode($this->db->error()));
                }
            }

            // B3: Tạo Student
            $studentInserted = $this->studentModel->insert([
                'user_id' => $userId,
                'student_code' => $json->student_code,
                'name' => $json->fullname,
                'dob' => $json->dob ?? null,
                'gender' => $genderDB,
                'address' => $json->address ?? '',
                'status' => 1
            ]);

            if (!$studentInserted) {
                $errors = $this->studentModel->errors();
                throw new \Exception('Không thể tạo student: ' . json_encode($errors ?: $this->db->error()));
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                $error = $this->db->error();
                log_message('error', 'Transaction failed: ' . json_encode($error));
                return $this->respond(['success' => false, 'message' => 'Lỗi transaction: ' . ($error['message'] ?? 'Không xác định')]);
            }
            
            return $this->respond(['success' => true, 'message' => 'Thêm sinh viên thành công']);

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Exception in create student: ' . $e->getMessage());
            return $this->respond(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function update()
    {
        $json = $this->request->getJSON();

        $genderDB = 'other';
        if ($json->gender == 'Nam') $genderDB = 'male';
        if ($json->gender == 'Nữ') $genderDB = 'female';

        $data = [
            'name' => $json->fullname,
            'dob' => $json->dob,
            'gender' => $genderDB,
            'address' => $json->address
        ];

        if ($this->studentModel->update($json->id, $data)) {
            return $this->respond(['success' => true, 'message' => 'Cập nhật thành công']);
        } else {
            return $this->respond(['success' => false, 'message' => 'Lỗi cập nhật']);
        }
    }

    public function toggleLock()
    {
        $json = $this->request->getJSON();
        
        // Query trực tiếp để đảo ngược trạng thái NOT status
        $sql = "UPDATE students SET status = NOT status WHERE id = ?";
        $this->db->query($sql, [$json->id]);

        return $this->respond(['success' => true, 'message' => 'Đổi trạng thái thành công']);
    }
}