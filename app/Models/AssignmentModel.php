<?php

namespace App\Models;

use CodeIgniter\Model;

class AssignmentModel extends Model
{
    protected $table = 'assignments';
    protected $primaryKey = 'assignment_id';
    protected $allowedFields = [
        'course_id',
        'title',
        'description',
        'due_date',
        'total_points',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
}
