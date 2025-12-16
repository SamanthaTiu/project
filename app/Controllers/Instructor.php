<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use App\Models\AssignmentModel;
use App\Models\AnnouncementModel;
use App\Models\GradeModel;
use CodeIgniter\HTTP\ResponseInterface;

class Instructor extends BaseController
{
    protected $courseModel;
    protected $enrollmentModel;
    protected $assignmentModel;
    protected $announcementModel;
    protected $gradeModel;
    protected $db;

    public function __construct()
    {
        $this->courseModel = new CourseModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->assignmentModel = new AssignmentModel();
        $this->announcementModel = new AnnouncementModel();
        $this->gradeModel = new GradeModel();
        $this->db = \Config\Database::connect();
    }

    public function dashboard()
    {
        $session = session();

        // Check if user is logged in and is a teacher
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(site_url('login'));
        }

        if ($session->get('role') !== 'instructor') {
            $session->setFlashdata('error', 'Access denied.');
            return redirect()->to(site_url('login'));
        }

        $userId = $session->get('user_id');
        $courses = $this->courseModel->where('instructor_id', $userId)->findAll();

        // Load the teacher dashboard view with courses data
        return view('instructor/dashboard', ['courses' => $courses]);
    }

    public function courses()
    {
        $session = session();

        if (!$session->get('isLoggedIn') || $session->get('role') !== 'instructor') {
            return redirect()->to(site_url('login'));
        }

        $userId = $session->get('user_id');
        $courses = $this->courseModel->where('instructor_id', $userId)->findAll();

        return view('instructor/course/courses', ['courses' => $courses]);
    }

    public function my_students()
    {
        $session = session();

        if (!$session->get('isLoggedIn') || $session->get('role') !== 'instructor') {
            return redirect()->to(site_url('login'));
        }

        $userId = $session->get('user_id');
        $students = $this->enrollmentModel->select('enrollments.*, courses.course_name, users.name as student_name, users.email as student_email')
            ->join('courses', 'courses.course_id = enrollments.course_id')
            ->join('users', 'users.user_id = enrollments.user_id')
            ->where('courses.instructor_id', $userId)
            ->findAll();

        return view('instructor/my_students', ['students' => $students]);
    }

    public function manageCourse($courseId)
    {
        $session = session();

        if (!$session->get('isLoggedIn') || $session->get('role') !== 'instructor') {
            return redirect()->to(site_url('login'));
        }

        $userId = $session->get('user_id');

        // Fetch course owned by instructor
        $course = $this->courseModel->where('course_id', $courseId)->where('instructor_id', $userId)->first();

        if (!$course) {
            return redirect()->to(site_url('instructor/courses'))->with('error', 'Course not found or access denied');
        }

        // Render manage course view
        return view('instructor/course/manage_courses', ['course' => $course]);
    }

    // Assignments
    public function assignments($courseId)
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'instructor') {
            return redirect()->to(site_url('login'));
        }

        $userId = $session->get('user_id');
        $course = $this->courseModel->where('course_id', $courseId)->where('instructor_id', $userId)->first();

        if (!$course) {
            return redirect()->to(site_url('instructor/courses'))->with('error', 'Course not found or access denied');
        }

        $assignments = $this->assignmentModel->where('course_id', $courseId)->findAll();

        return view('instructor/course/assignments', [
            'course' => $course,
            'assignments' => $assignments
        ]);
    }

    public function createAssignment($courseId)
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'instructor') {
            return redirect()->to(site_url('login'));
        }

        $userId = $session->get('user_id');
        $course = $this->courseModel->where('course_id', $courseId)->where('instructor_id', $userId)->first();

        if (!$course) {
            return redirect()->to(site_url('instructor/courses'))->with('error', 'Course not found or access denied');
        }

        if ($this->request->getMethod() === 'post') {
            $data = [
                'course_id' => $courseId,
                'title' => $this->request->getPost('title'),
                'description' => $this->request->getPost('description'),
                'due_date' => $this->request->getPost('due_date'),
                'total_points' => $this->request->getPost('total_points'),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->assignmentModel->insert($data);

            // Get all enrolled students for this course
            $enrolledStudents = $this->enrollmentModel->select('user_id')
                ->where('course_id', $courseId)
                ->findAll();

            // Create notifications for all enrolled students
            $notifications = [];
            foreach ($enrolledStudents as $student) {
                $notifications[] = [
                    'user_id' => $student['user_id'],
                    'message' => 'New assignment in ' . $course['course_name'] . ': ' . $data['title'] . ' (Due: ' . $data['due_date'] . ')',
                    'is_read' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                ];
            }

            if (!empty($notifications)) {
                $this->db->table('notifications')->insertBatch($notifications);
            }

            return redirect()->to(site_url('instructor/course/' . $courseId . '/assignments'))
                ->with('success', 'Assignment created successfully');
        }

        return view('instructor/course/create_assignment', ['course' => $course]);
    }

    // Announcements
    public function announcements($courseId)
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'instructor') {
            return redirect()->to(site_url('login'));
        }

        $userId = $session->get('user_id');
        $course = $this->courseModel->where('course_id', $courseId)->where('instructor_id', $userId)->first();

        if (!$course) {
            return redirect()->to(site_url('instructor/courses'))->with('error', 'Course not found or access denied');
        }

        $announcements = $this->announcementModel->where('course_id', $courseId)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('instructor/course/announcements', [
            'course' => $course,
            'announcements' => $announcements
        ]);
    }

    public function createAnnouncement($courseId)
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'instructor') {
            return redirect()->to(site_url('login'));
        }

        $userId = $session->get('user_id');
        $course = $this->courseModel->where('course_id', $courseId)->where('instructor_id', $userId)->first();

        if (!$course) {
            return redirect()->to(site_url('instructor/courses'))->with('error', 'Course not found or access denied');
        }

        if ($this->request->getMethod() === 'post') {
            $data = [
                'course_id' => $courseId,
                'title' => $this->request->getPost('title'),
                'content' => $this->request->getPost('content'),
                'created_by' => $userId,
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Validate the input
            $validation = $this->validate([
                'title' => 'required|min_length[5]|max_length[255]',
                'content' => 'required|min_length[10]',
            ]);

            if (!$validation) {
                return redirect()->back()->withInput()->with('validation', $this->validator);
            }

            $this->announcementModel->insert($data);

            // Get all enrolled students for this course
            $enrolledStudents = $this->enrollmentModel->select('user_id')
                ->where('course_id', $courseId)
                ->findAll();

            // Create notifications for all enrolled students
            $notifications = [];
            foreach ($enrolledStudents as $student) {
                $notifications[] = [
                    'user_id' => $student['user_id'],
                    'message' => 'New announcement in ' . $course['course_name'] . ': ' . $data['title'],
                    'is_read' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                ];
            }

            if (!empty($notifications)) {
                $this->db->table('notifications')->insertBatch($notifications);
            }

            return redirect()->to(site_url('instructor/course/' . $courseId . '/announcements'))
                ->with('success', 'Announcement created successfully');
        }

        return view('instructor/course/create_announcement', ['course' => $course]);
    }

    // Grades
    public function grades($courseId)
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'instructor') {
            return redirect()->to(site_url('login'));
        }

        $userId = $session->get('user_id');
        $course = $this->courseModel->where('course_id', $courseId)->where('instructor_id', $userId)->first();

        if (!$course) {
            return redirect()->to(site_url('instructor/courses'))->with('error', 'Course not found or access denied');
        }

        // Get all enrollments with grades for this course
        $enrollments = $this->enrollmentModel->select('enrollments.*, users.name as student_name, users.id as student_id, grades.*')
            ->join('users', 'users.user_id = enrollments.user_id')
            ->join('grades', 'grades.enrollment_id = enrollments.enrollment_id', 'left')
            ->where('enrollments.course_id', $courseId)
            ->findAll();

        return view('instructor/course/grades', [
            'course' => $course,
            'enrollments' => $enrollments
        ]);
    }

    public function saveGrades($courseId)
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'instructor' || !$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $grades = $this->request->getPost('grades');
        
        foreach ($grades as $enrollmentId => $gradeData) {
            // Calculate total and letter grade
            $assignment1 = (float)($gradeData['assignment1'] ?? 0);
            $assignment2 = (float)($gradeData['assignment2'] ?? 0);
            $midterm = (float)($gradeData['midterm'] ?? 0);
            $final = (float)($gradeData['final'] ?? 0);
            
            $total = ($assignment1 * 0.15) + ($assignment2 * 0.15) + ($midterm * 0.3) + ($final * 0.4);
            $letterGrade = $this->calculateLetterGrade($total);

            $data = [
                'enrollment_id' => $enrollmentId,
                'assignment1' => $assignment1,
                'assignment2' => $assignment2,
                'midterm' => $midterm,
                'final' => $final,
                'total' => $total,
                'letter_grade' => $letterGrade,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Check if grade record exists
            $existingGrade = $this->gradeModel->where('enrollment_id', $enrollmentId)->first();
            
            if ($existingGrade) {
                $this->gradeModel->update($existingGrade['grade_id'], $data);
            } else {
                $data['created_at'] = date('Y-m-d H:i:s');
                $this->gradeModel->insert($data);
            }
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Grades saved successfully']);
    }

    private function calculateLetterGrade($percentage)
    {
        if ($percentage >= 90) return 'A';
        if ($percentage >= 80) return 'B';
        if ($percentage >= 70) return 'C';
        if ($percentage >= 60) return 'D';
        return 'F';
    }
}