<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use CodeIgniter\HTTP\ResponseInterface;

class Admin extends BaseController
{
    protected $userModel;
    protected $courseModel;
    protected $enrollmentModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->courseModel = new CourseModel();
        $this->enrollmentModel = new EnrollmentModel();
    }

    public function dashboard()
    {
        $session = session();


        // Check if user is logged in and is an admin
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(site_url('login'));
        }

        if ($session->get('role') !== 'admin') {
            $session->setFlashdata('error', 'Access denied.');
            return redirect()->to(site_url('login'));
        }

        // Fetch recent registrations (last 10 users)
        $recentRegistrations = $this->userModel->orderBy('user_id', 'DESC')->findAll(10);

        // Pass data to the view
        $data = [
            'recentRegistrations' => $recentRegistrations
        ];

        // Load the admin dashboard view with data
        return view('admin/dashboard', $data);
    }

    public function manageUsers()
    {
        $session = session();

        // Check if user is logged in and is an admin
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to(site_url('login'));
        }

        // Fetch all users
        $users = $this->userModel->findAll();

        // Fetch user statistics
        $totalUsers = $this->userModel->countAll();
        $totalAdmins = $this->userModel->where('role', 'admin')->countAllResults();
        $totalTeachers = $this->userModel->where('role', 'instructor')->countAllResults();
        $totalStudents = $this->userModel->where('role', 'student')->countAllResults();

        $data = [
            'users' => $users,
            'totalUsers' => $totalUsers,
            'totalAdmins' => $totalAdmins,
            'totalTeachers' => $totalTeachers,
            'totalStudents' => $totalStudents
        ];

        return view('admin/manage_users', $data);
    }

    public function manageCourses()
    {
        $session = session();

        // Check if user is logged in and is an admin
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to(site_url('login'));
        }

        // Fetch all courses with instructor names
        $courses = $this->courseModel->select('courses.*, users.name as instructor_name')
            ->join('users', 'users.user_id = courses.instructor_id', 'left')
            ->findAll();

        // Fetch enrolled students for each course
        foreach ($courses as &$course) {
            $enrollments = $this->enrollmentModel->select('users.name as student_name')
                ->join('users', 'users.user_id = enrollments.user_id')
                ->where('enrollments.course_id', $course['course_id'])
                ->findAll();
            $course['enrolled_students'] = array_column($enrollments, 'student_name');
        }

        // Fetch instructors for the create course form
        $instructors = $this->userModel->where('role', 'instructor')->findAll();

        $data = [
            'courses' => $courses,
            'instructors' => $instructors
        ];

        return view('admin/manage_courses', $data);
    }

    public function editUser($userId)
    {
        $session = session();

        // Check if user is logged in and is an admin
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to(site_url('login'));
        }

        // Fetch user data
        $user = $this->userModel->find($userId);
        if (!$user) {
            return redirect()->to(site_url('admin/manage-users'))->with('error', 'User not found');
        }

        // Prevent editing own account or other admin accounts
        if ($userId == $session->get('user_id') || $user['role'] === 'admin') {
            return redirect()->to(site_url('admin/manage-users'))->with('error', 'You cannot edit admin accounts or your own account');
        }

        $data = [
            'user' => $user
        ];

        return view('admin/edit_user', $data);
    }

    public function updateUser()
    {
        $session = session();

        // Check if user is logged in and is an admin
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to(site_url('login'));
        }

        $userId = $this->request->getPost('user_id');

        // Prevent updating own account or other admin accounts
        if ($userId == $session->get('user_id')) {
            return redirect()->to(site_url('admin/manage-users'))->with('error', 'You cannot edit your own account');
        }

        $user = $this->userModel->find($userId);
        if ($user && $user['role'] === 'admin') {
            return redirect()->to(site_url('admin/manage-users'))->with('error', 'You cannot edit admin accounts');
        }

        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $role = $this->request->getPost('role');

        // Map "teacher" to "instructor" for database consistency
        if ($role === 'teacher') {
            $role = 'instructor';
        }

        // Update user
        $this->userModel->update($userId, [
            'name' => $name,
            'email' => $email,
            'role' => $role
        ]);

        return redirect()->to(site_url('admin/dashboard'))->with('success', 'User updated successfully');
    }

    public function restrictUser($userId)
    {
        $session = session();

        // Check if user is logged in and is an admin
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to(site_url('login'));
        }

        // Restrict user (e.g., change role to 'restricted')
        $this->userModel->update($userId, ['role' => 'restricted']);

        return redirect()->to(site_url('admin/dashboard'))->with('success', 'User restricted successfully');
    }

    public function deleteUser($userId)
    {
        $session = session();

        // Check if user is logged in and is an admin
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to(site_url('login'));
        }

        // Delete user
        $this->userModel->delete($userId);

        return redirect()->to(site_url('admin/dashboard'))->with('success', 'User deleted successfully');
    }

    public function editCourse($courseId)
    {
        $session = session();

        // Check if user is logged in and is an admin
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to(site_url('login'));
        }

        // Fetch course data
        $course = $this->courseModel->find($courseId);
        if (!$course) {
            return redirect()->to(site_url('admin/manage-courses'))->with('error', 'Course not found');
        }

        // Fetch instructors
        $instructors = $this->userModel->where('role', 'instructor')->findAll();

        $data = [
            'course' => $course,
            'instructors' => $instructors
        ];

        return view('admin/edit_course', $data);
    }

    public function updateCourse()
    {
        $session = session();

        // Check if user is logged in and is an admin
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to(site_url('login'));
        }

        $courseId = $this->request->getPost('course_id');
        $courseName = $this->request->getPost('course_name');
        $description = $this->request->getPost('description');
        $instructorId = $this->request->getPost('instructor_id');

        // Validate input
        if (empty($courseName) || empty($description) || empty($instructorId)) {
            return redirect()->to(site_url('admin/edit-course/' . $courseId))->with('error', 'All fields are required.');
        }

        // Check if instructor exists and is an instructor
        $instructor = $this->userModel->find($instructorId);
        if (!$instructor || $instructor['role'] !== 'instructor') {
            return redirect()->to(site_url('admin/edit-course/' . $courseId))->with('error', 'Invalid instructor selected.');
        }

        // Check if instructor already has a course (but not this one)
        $existingCourse = $this->courseModel->where('instructor_id', $instructorId)->where('course_id !=', $courseId)->first();
        if ($existingCourse) {
            return redirect()->to(site_url('admin/edit-course/' . $courseId))->with('error', 'Cant update course. The instructor already has a course.');
        }

        // Update course
        $this->courseModel->update($courseId, [
            'course_name' => $courseName,
            'description' => $description,
            'instructor_id' => $instructorId
        ]);

        return redirect()->to(site_url('admin/manage-courses'))->with('success', 'Course updated successfully.');
    }

    public function deleteCourse($courseId)
    {
        $session = session();

        // Check if user is logged in and is an admin
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to(site_url('login'));
        }

        // Delete enrollments first to avoid foreign key constraint
        $this->enrollmentModel->where('course_id', $courseId)->delete();

        // Delete course
        $this->courseModel->delete($courseId);

        return redirect()->to(site_url('admin/manage-courses'))->with('success', 'Course deleted successfully.');
    }

    public function createUser()
    {
        helper(['form']);
        $session = session();

        // Check if user is logged in and is an admin
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to(site_url('login'));
        }

        if ($this->request->getMethod() === 'post') {
            $rules = [
                'name' => 'required|min_length[3]|max_length[50]',
                'email' => 'required|valid_email',
                'password' => 'required|min_length[6]',
                'role' => 'required|in_list[student,instructor]'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('validation', $this->validator);
            }

            // Check if email already exists
            $existingUser = $this->userModel->where('email', $this->request->getPost('email'))->first();
            if ($existingUser) {
                return redirect()->back()->withInput()->with('error', 'Email already exists.');
            }

            $data = [
                'name' => $this->request->getPost('name'),
                'email' => $this->request->getPost('email'),
                'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                'role' => $this->request->getPost('role'),
            ];

            log_message('info', 'Attempting to insert user: ' . json_encode($data));
            if ($this->userModel->insert($data)) {
                log_message('info', 'User created successfully with ID: ' . $this->userModel->getInsertID());
                return redirect()->to(site_url('admin/dashboard'))->with('success', 'User created successfully.');
            } else {
                $dbError = $this->userModel->db->error();
                log_message('error', 'Failed to create user. DB Error: ' . json_encode($dbError));
                $errorMessage = isset($dbError['message']) ? $dbError['message'] : 'Unknown database error';
                return redirect()->back()->withInput()->with('error', 'Failed to create user: ' . $errorMessage);
            }
        }

        return view('admin/create_user');
    }
}
