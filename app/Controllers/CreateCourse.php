<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CourseModel;
use App\Models\UserModel;

class CreateCourse extends BaseController
{
    public function create()
    {
        $session = session();

        // Check if user is logged in and is an admin
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to(site_url('login'));
        }

        // Get form data
        $courseName = $this->request->getPost('course_name');
        $description = $this->request->getPost('description');
        $instructorId = $this->request->getPost('instructor_id');

        // Validate input
        if (empty($courseName) || empty($description) || empty($instructorId)) {
            return redirect()->back()->with('error', 'All fields are required.');
        }

        // Check if instructor exists and is an instructor
        $userModel = new UserModel();
        $instructor = $userModel->find($instructorId);
        if (!$instructor || $instructor['role'] !== 'instructor') {
            return redirect()->back()->with('error', 'Invalid instructor selected.');
        }

        // Check if instructor already has a course
        $courseModel = new CourseModel();
        $existingCourse = $courseModel->where('instructor_id', $instructorId)->first();
        if ($existingCourse) {
            return redirect()->back()->with('error', 'This instructor already has a course assigned.');
        }

        // Create course
        $courseModel = new CourseModel();
        $data = [
            'course_name' => $courseName,
            'description' => $description,
            'instructor_id' => $instructorId
        ];

        if ($courseModel->createCourse($data)) {
            return redirect()->to(site_url('admin/manage-courses'))->with('success', 'Course successfully created for ' . $instructor['name'] . '.');
        } else {
            return redirect()->back()->with('error', 'Failed to create course.');
        }
    }
}
