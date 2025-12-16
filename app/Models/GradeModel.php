<?php

namespace App\Models;

use CodeIgniter\Model;

class GradeModel extends Model
{
    protected $table            = 'grades';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;

    // ✅ Add allowed fields (important for insert/update)
    protected $allowedFields    = ['assignment_id', 'student_id', 'grade', 'feedback', 'graded_at'];

    // ✅ Automatically handle created_at and updated_at
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'graded_at';
    protected $updatedField  = 'updated_at';
}
