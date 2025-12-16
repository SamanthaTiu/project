<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AnnouncementModel;

class CreateAnnouncement extends BaseController
{
    protected $announcementModel;
    protected $db;

    public function __construct()
    {
        $this->announcementModel = new AnnouncementModel();
        $this->db = \Config\Database::connect();
        helper(['form', 'url', 'text']);
    }

    public function index($courseId = null)
    {
        // Check if user is logged in and is an instructor
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'instructor') {
            return redirect()->to('/login');
        }

        // Get course details (you may need to load a CourseModel)
        $courseModel = new \App\Models\CourseModel();
        $course = $courseModel->find($courseId);

        if (!$course) {
            return redirect()->back()->with('error', 'Course not found');
        }

        $data = [
            'title' => 'Create Announcement - ' . ($course['course_name'] ?? 'Course'),
            'course_id' => $courseId,
            'course' => $course
        ];

        return view('instructor/course/create_announcement', $data);
    }

    public function create($courseId = null)
    {
        // Check if user is logged in and is an instructor
        if (!session()->get('isLoggedIn') || session()->get('user_type') !== 'instructor') {
            return redirect()->to('/login');
        }

        // Validate the form data
        $rules = [
            'title' => 'required|min_length[5]|max_length[255]',
            'content' => 'required|min_length[10]',
            'course_id' => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            // If validation fails, redirect back with errors
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        // Get the form data
        $data = [
            'title' => $this->request->getPost('title'),
            'content' => $this->request->getPost('content'),
            'course_id' => $this->request->getPost('course_id'),
            'created_by' => session()->get('user_id'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Save the announcement
        if ($this->announcementModel->insert($data)) {
            // Get course details for notification
            $courseModel = new \App\Models\CourseModel();
            $course = $courseModel->find($data['course_id']);

            // Get all enrolled students for this course
            $enrolledStudents = $this->db->table('enrollments')
                ->select('user_id')
                ->where('course_id', $data['course_id'])
                ->get()
                ->getResultArray();

            // Create notifications for all enrolled students
            $notifications = [];
            foreach ($enrolledStudents as $student) {
                $notifications[] = [
                    'user_id' => $student['user_id'],
                    'message' => 'New announcement in ' . ($course['course_name'] ?? 'Course') . ': ' . $data['title'],
                    'is_read' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                ];
            }

            if (!empty($notifications)) {
                $this->db->table('notifications')->insertBatch($notifications);
            }

            // Set success message
            session()->setFlashdata('success', 'Announcement created successfully!');
            // Redirect to the announcements page for the course
            return redirect()->to('/instructor/course/' . $data['course_id'] . '/announcements');
        } else {
            // If save fails, redirect back with error
            return redirect()->back()->withInput()->with('error', 'Failed to create announcement. Please try again.');
        }
    }
}