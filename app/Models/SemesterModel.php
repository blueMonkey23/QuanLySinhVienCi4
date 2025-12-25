<?php

namespace App\Models;

use CodeIgniter\Model;

class SemesterModel extends Model
{
    protected $table            = 'semesters';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['name', 'start_date', 'end_date', 'is_current'];

    // Lấy học kỳ hiện tại
    public function getCurrentSemester()
    {
        return $this->where('is_current', 1)->first();
    }

    // Lấy tất cả học kỳ (sắp xếp theo ngày bắt đầu mới nhất)
    public function getAllSemesters()
    {
        return $this->orderBy('start_date', 'DESC')
                    ->findAll();
    }
}
