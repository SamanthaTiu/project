<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EnrollmentModel;
use App\Models\CourseModel;

class Dashboard extends BaseController
{
    protected $session;
    protected $courseModel;
    protected $db;

    public function __construct()
    {
        $this->session = session();
        $this->courseModel = new CourseModel();
        $this->db = db_connect();
    }

    public function index()
    {
        // Check if user is logged in
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        // Get user data from session
        $userId = $this->session->get('user_id') ?? $this->session->get('id');
        $name = $this->session->get('name') ?? $this->session->get('username') ?? 'User';
        $email = $this->session->get('email') ?? '';
        $role = $this->session->get('role') ?? 'student';

        $data = [
            'name' => $name,
            'email' => $email,
            'role' => $role,
        ];

        // For students, load courses and enrollments
        if (in_array(strtolower($role), ['student', 'learner'])) {
            $courseModel = new CourseModel();
            $enrollmentModel = new EnrollmentModel();

            // Get all available courses
            $data['courses'] = $courseModel->findAll();

            // Get user's enrollments
            if ($userId) {
                $data['enrollments'] = $enrollmentModel->getUserEnrollments((int)$userId);
            } else {
                $data['enrollments'] = [];
            }
        }

        return view('auth/dashboard', $data);
    }

    public function myCourses()
    {
        // Check if user is logged in
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        // Get user data from session
        $userId = $this->session->get('user_id') ?? $this->session->get('id');
        $name = $this->session->get('name') ?? $this->session->get('username') ?? 'User';
        $email = $this->session->get('email') ?? '';
        $role = $this->session->get('role') ?? 'student';

        $data = [
            'name' => $name,
            'email' => $email,
            'role' => $role,
        ];

        // For students, load enrolled courses with instructor details
        if (in_array(strtolower($role), ['student', 'learner'])) {
            $enrollmentModel = new EnrollmentModel();

            // Get user's enrollments with instructor details
            if ($userId) {
                $data['enrollments'] = $enrollmentModel->getUserEnrollmentsWithInstructor((int)$userId);
            } else {
                $data['enrollments'] = [];
            }
        }

        return view('auth/my_courses', $data);
    }

    public function myGrades()
    {
        // Check if user is logged in
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        // Get user data from session
        $userId = $this->session->get('user_id') ?? $this->session->get('id');
        $name = $this->session->get('name') ?? $this->session->get('username') ?? 'User';
        $email = $this->session->get('email') ?? '';
        $role = $this->session->get('role') ?? 'student';

        $data = [
            'name' => $name,
            'email' => $email,
            'role' => $role,
        ];

        // For students, load grades (assuming grades are stored in enrollments or a separate table)
        if (in_array(strtolower($role), ['student', 'learner'])) {
            // For now, we'll use enrollments as a placeholder. In a real app, you'd have a grades table.
            $enrollmentModel = new EnrollmentModel();

            if ($userId) {
                $data['grades'] = $enrollmentModel->getUserEnrollmentsWithInstructor((int)$userId);
            } else {
                $data['grades'] = [];
            }
        }

        return view('auth/my_grades', $data);
    }

    public function student()
    {
        $userId = $this->session->get('user_id') ?? $this->session->get('id');

        // Load courses (adjust query if you want only relevant ones)
        $courses = $this->courseModel->orderBy('course_name', 'ASC')->findAll();

        // Get unread notifications count and recent notifications (for initial dropdown)
        $unreadCount = 0;
        $recentNotifications = [];

        if (! empty($userId)) {
            $notifQ = $this->db->table('notifications')->where('user_id', (int) $userId);
            $unreadCount = $notifQ->where('is_read', 0)->countAllResults(false);

            // Get recent 5 notifications
            $recentNotifications = $this->db->table('notifications')
                ->where('user_id', (int) $userId)
                ->orderBy('created_at', 'DESC')
                ->limit(5)
                ->get()
                ->getResultArray();
        }

        return view('dashboard/student', [
            'courses' => $courses,
            'searchTerm' => $this->request->getGet('search_term') ?? '',
            'unreadCount' => $unreadCount,
            'notifications' => $recentNotifications,
        ]);
    }
}
