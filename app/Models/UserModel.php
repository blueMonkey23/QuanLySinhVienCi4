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
}