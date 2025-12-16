<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EnrollmentModel;
use App\Models\CourseModel;

class Course extends BaseController
{
    protected $enrollmentModel;
    protected $courseModel;
    protected $session;
    protected $db;

    public function __construct()
    {
        $this->session = session();
        $this->enrollmentModel = new EnrollmentModel();
        $this->courseModel = new CourseModel();
        $this->db = db_connect();
    }

    /**
     * Course listing (renders app/Views/courses/index.php)
     */
    public function index()
    {
        $courses = $this->courseModel->orderBy('course_name', 'ASC')->findAll();

        return view('courses/index', [
            'courses' => $courses,
        ]);
    }

    /**
     * AJAX endpoint to enroll the current user into a course.
     * Expects POST: course_id
     * Returns JSON with appropriate HTTP status codes.
     */
    public function enroll()
    {
        // Get logged-in user id from session (adjust keys if your app uses different ones)
        $userId = $this->session->get('user_id') ?? $this->session->get('id');

        if (empty($userId)) {
            return $this->response
                        ->setStatusCode(401)
                        ->setJSON(['status' => 'error', 'message' => 'User not logged in']);
        }

        $courseId = (int) $this->request->getPost('course_id');
        if (empty($courseId)) {
            return $this->response
                        ->setStatusCode(400)
                        ->setJSON(['status' => 'error', 'message' => 'Missing course_id']);
        }

        // Verify course exists
        $course = $this->courseModel->find($courseId);
        if (empty($course)) {
            return $this->response
                        ->setStatusCode(404)
                        ->setJSON(['status' => 'error', 'message' => 'Course not found']);
        }

        // Prevent duplicate enrollment
        if ($this->enrollmentModel->isAlreadyEnrolled((int) $userId, $courseId)) {
            return $this->response
                        ->setStatusCode(409)
                        ->setJSON(['status' => 'error', 'message' => 'User already enrolled in this course']);
        }

        // Insert new enrollment (your model method may differ — adapt if necessary)
        $insertId = $this->enrollmentModel->enrollUser([
            'user_id' => (int) $userId,
            'course_id' => $courseId,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        if ($insertId) {
            // Create notification
            $this->db->table('notifications')->insert([
                'user_id' => (int) $userId,
                'message' => 'You have successfully enrolled in the course: ' . ($course['course_name'] ?? $course['title'] ?? ''),
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return $this->response
                        ->setStatusCode(201)
                        ->setJSON(['status' => 'success', 'message' => 'Enrolled successfully', 'enrollment_id' => $insertId]);
        }

        // fallback
        return $this->response
                    ->setStatusCode(500)
                    ->setJSON(['status' => 'error', 'message' => 'Failed to create enrollment']);
    }

    /**
     * Implement the search logic
     *
     * Matches the screenshot behavior:
     * - reads GET param "search_term" (accepts POST via getVar too)
     * - applies LIKE on course_name and course_description columns
     * - returns JSON for AJAX requests, renders view for normal requests
     */
    public function search()
    {
        // Prefer GET param named search_term (matches your screenshots). Accept POST via getVar too.
        $searchTerm = trim((string) ($this->request->getGet('search_term') ?? $this->request->getVar('search_term') ?? ''));

        // Use a fresh model instance so other requests don't get affected by prior chain calls
        $model = new CourseModel();

        if ($searchTerm !== '') {
            $model = $model->like('course_name', $searchTerm)
                           ->orLike('course_description', $searchTerm);
        }

        $courses = $model->findAll();

        // If AJAX, return JSON
        if ($this->request->isAJAX()) {
            return $this->response->setJSON($courses);
        }

        // Non-AJAX: render a view (create app/Views/courses/search_results.php or reuse index)
        return view('courses/search_results', [
            'courses' => $courses,
            'searchTerm' => $searchTerm,
        ]);
    }

    /**
     * Fetch notifications for the logged-in user (AJAX).
     * Returns JSON array of notifications (latest first).
     *
     * Optional: call every 60s on the client to simulate real-time updates.
     */
    public function fetchNotifications()
    {
        $userId = $this->session->get('user_id') ?? $this->session->get('id');

        if (empty($userId)) {
            return $this->response
                        ->setStatusCode(401)
                        ->setJSON(['status' => 'error', 'message' => 'User not logged in']);
        }

        $notifications = $this->db->table('notifications')
            ->where('user_id', (int) $userId)
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        return $this->response->setJSON($notifications);
    }
}