<?php

namespace App\Models;

use CodeIgniter\Model;

class SubjectModel extends Model
{
    protected $table            = 'subjects';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['subject_code', 'name', 'credits'];
    
    public function getSubjectName($id)
    {
        $subject = $this->find($id);
        return $subject ? $subject['name'] : null;
    }
}
