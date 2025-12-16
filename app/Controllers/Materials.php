<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MaterialModel;
use App\Models\CourseModel;
use App\Models\EnrollmentModel;

class Materials extends BaseController
{
    protected $materialModel;
    protected $courseModel;
    protected $enrollmentModel;
    protected $session;
    protected $db;

    public function __construct()
    {
        $this->session = session();
        $this->materialModel = new MaterialModel();
        $this->courseModel = new CourseModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Delete a material file.
     * @param int $material_id
     */
    public function delete($material_id)
    {
        // Check if user is logged in and is an instructor
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'instructor') {
            return redirect()->to('/login')->with('error', 'Access denied');
        }

        // Get material details
        $material = $this->materialModel->find($material_id);
        if (!$material) {
            return redirect()->back()->with('error', 'Material not found');
        }

        // Verify the course belongs to the instructor
        $course = $this->courseModel->where('course_id', $material['course_id'])
                                   ->where('instructor_id', $this->session->get('user_id'))
                                   ->first();

        if (!$course) {
            return redirect()->back()->with('error', 'Access denied');
        }

        // Delete file from filesystem
        if (file_exists($material['file_path'])) {
            unlink($material['file_path']);
        }

        // Delete from database
        if ($this->materialModel->delete($material_id)) {
            return redirect()->back()->with('success', 'Material deleted successfully');
        } else {
            return redirect()->back()->with('error', 'Failed to delete material');
        }
    }

    /**
     * Download a material file.
     * @param int $material_id
     */
    public function download($material_id)
    {
        // Get material details
        $material = $this->materialModel->find($material_id);
        if (!$material) {
            return redirect()->back()->with('error', 'Material not found');
        }

        // Check if user is logged in
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userId = $this->session->get('user_id');

        // Check if user is enrolled in the course (for students) or is the instructor
        $course = $this->courseModel->find($material['course_id']);
        if (!$course) {
            return redirect()->back()->with('error', 'Course not found');
        }

        $hasAccess = false;
        if ($this->session->get('role') === 'instructor' && $course['instructor_id'] == $userId) {
            $hasAccess = true;
        } elseif ($this->enrollmentModel->isAlreadyEnrolled($userId, $material['course_id'])) {
            $hasAccess = true;
        }

        if (!$hasAccess) {
            return redirect()->back()->with('error', 'Access denied');
        }

        // Serve file download
        if (file_exists($material['file_path'])) {
            return $this->response->download($material['file_path'], null, true)->setFileName($material['file_name']);
        } else {
            return redirect()->back()->with('error', 'File not found');
        }
    }

    /**
     * Handle material upload for instructors.
     * GET: Display upload form
     * POST: Process file upload
     */
    public function upload()
    {
        // Check if user is logged in and is an instructor
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'instructor') {
            return redirect()->to('/login')->with('error', 'Access denied');
        }

        $courseId = $this->request->getPost('course_id') ?? $this->request->getGet('course_id');

        if (!$courseId) {
            return redirect()->to('/instructor/courses')->with('error', 'Course ID is required');
        }

        // Verify the course belongs to the instructor
        $course = $this->courseModel->where('course_id', $courseId)
                                   ->where('instructor_id', $this->session->get('user_id'))
                                   ->first();

        if (!$course) {
            return redirect()->to('/instructor/courses')->with('error', 'Course not found or access denied');
        }

        if ($this->request->getMethod() === 'POST') {
            // Handle file upload
            $file = $this->request->getFile('material_file');

            if (!$file->isValid()) {
                $errorCode = $file->getError();
                $errorMessage = $file->getErrorString();
                return redirect()->back()->with('error', 'File upload failed: ' . $errorMessage . ' (Error code: ' . $errorCode . ')');
            }

            // Check file size (max 10MB)
            $maxSize = 10 * 1024 * 1024; // 10MB in bytes
            if ($file->getSize() > $maxSize) {
                return redirect()->back()->with('error', 'File too large. Maximum allowed size is 10MB.');
            }

            // Validate file type
            $allowedTypes = ['pdf', 'ppt', 'pptx', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
            $fileExtension = strtolower($file->getExtension());
            if (!in_array($fileExtension, $allowedTypes)) {
                return redirect()->back()->with('error', 'Invalid file type. Allowed: PDF, PPT, DOC, JPG, PNG');
            }

            // Generate unique filename with timestamp to avoid conflicts
            $newName = time() . '_' . $file->getName();

            // Ensure uploads directory exists and is writable
            $uploadPath = WRITEPATH . 'uploads';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Move file to uploads directory
            if ($file->move($uploadPath, $newName)) {
                // Save to database
                $data = [
                    'course_id' => $courseId,
                    'file_name' => $file->getName(),
                    'file_path' => $uploadPath . '/' . $newName,
                ];

                if ($this->materialModel->insertMaterial($data)) {
                    // Get all enrolled students for this course
                    $enrolledStudents = $this->enrollmentModel->select('user_id')
                        ->where('course_id', $courseId)
                        ->findAll();

                    // Create notifications for all enrolled students
                    $notifications = [];
                    foreach ($enrolledStudents as $student) {
                        $notifications[] = [
                            'user_id' => $student['user_id'],
                            'message' => 'New material uploaded in ' . $course['course_name'] . ': ' . $data['file_name'],
                            'is_read' => 0,
                            'created_at' => date('Y-m-d H:i:s'),
                        ];
                    }

                    if (!empty($notifications)) {
                        $this->db->table('notifications')->insertBatch($notifications);
                    }

                    return redirect()->back()->with('success', 'Material uploaded successfully');
                } else {
                    // Delete file if database insert failed
                    if (file_exists($data['file_path'])) {
                        unlink($data['file_path']);
                    }
                    return redirect()->back()->with('error', 'Failed to save material to database');
                }
            } else {
                return redirect()->back()->with('error', 'Failed to move uploaded file to server');
            }
        }

        // GET request: Display upload form
        $materials = $this->materialModel->getMaterialsByCourse($courseId);

        $data = [
            'course' => $course,
            'materials' => $materials,
        ];

        return view('instructor/course/upload_materials', $data);
    }

    /**
     * Display materials for a course to enrolled students.
     * @param int $course_id
     */
    public function viewMaterials($course_id)
    {
        // Check if user is logged in
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userId = $this->session->get('user_id');

        // Check if user is enrolled in the course
        if (!$this->enrollmentModel->isAlreadyEnrolled($userId, $course_id)) {
            return redirect()->to('/my-courses')->with('error', 'You are not enrolled in this course');
        }

        // Get course details with instructor name
        $course = $this->courseModel->select('courses.*, users.name as instructor_name')
                                   ->join('users', 'users.user_id = courses.instructor_id')
                                   ->find($course_id);
        if (!$course) {
            return redirect()->to('/my-courses')->with('error', 'Course not found');
        }

        // Get materials for the course
        $materials = $this->materialModel->getMaterialsByCourse($course_id);

        $data = [
            'course' => $course,
            'materials' => $materials,
        ];

        return view('student/course/materials', $data);
    }
}
