<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['name', 'email', 'password_hash', 'created_at'];
    protected $returnType       = 'array';

    // Hàm lấy vai trò của user (Admin, Manager, Teacher, Student)
    public function getUserRole($userId)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('roles');
        $builder->select('roles.name');
        $builder->join('role_user', 'roles.id = role_user.role_id');
        $builder->where('role_user.user_id', $userId);
        $query = $builder->get();
        $row = $query->getRow();
        return $row ? $row->name : 'student';
    }
    
    // Hàm lấy role_id của user
    public function getUserRoleId($userId)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('role_user');
        $builder->select('role_id');
        $builder->where('user_id', $userId);
        $query = $builder->get();
        $row = $query->getRow();
        return $row ? $row->role_id : 4; // Default: 4 = Student
    }

    /**
     * Đăng ký sinh viên mới (tạo User + Student + gán Role)
     * @return array ['success' => bool, 'message' => string, 'user_id' => int]
     */
    public function registerStudent($data)
    {
        $db = \Config\Database::connect();
        $studentModel = new StudentModel();
        
        // Kiểm tra Email hoặc Mã SV đã tồn tại
        if ($this->where('email', $data['email'])->first()) {
            return ['success' => false, 'message' => 'Email này đã được sử dụng.'];
        }
        if ($studentModel->where('student_code', $data['student_id'])->first()) {
            return ['success' => false, 'message' => 'Mã sinh viên này đã tồn tại.'];
        }

        // Transaction
        $db->transStart();

        // Tạo User
        $userId = $this->insert([
            'email' => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_BCRYPT),
            'name'  => $data['name']
        ]);

        // Gán Role Student (ID=4)
        $db->table('role_user')->insert(['user_id' => $userId, 'role_id' => 4]);

        // Tạo Student Profile
        $studentModel->insert([
            'user_id' => $userId,
            'student_code' => $data['student_id'],
            'name' => $data['name'],
            'email' => $data['email'],
            'status' => 1
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return ['success' => false, 'message' => 'Lỗi hệ thống, không thể tạo tài khoản.'];
        }

        return ['success' => true, 'message' => 'Đăng ký thành công!', 'user_id' => $userId];
    }
}