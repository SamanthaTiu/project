<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');
$routes->get('/home', 'Home::index');          // Homepage
$routes->get('about', 'Home::about');      // About
$routes->get('contact', 'Home::contact');  // Contact


$routes->get('/register', 'Auth::register');

$routes->post('/register', 'Auth::register');

$routes->get('/login', 'Auth::login');

$routes->post('/login', 'Auth::login');

$routes->get('/logout', 'Auth::logout');

$routes->get('/announcements', 'Announcements::index');


$routes->get('/instructor/dashboard', 'Instructor::dashboard');
$routes->get('/instructor/courses', 'Instructor::courses');
$routes->get('/instructor/my_students', 'Instructor::my_students');

$routes->get('/instructor/course/courses', 'Instructor::courses');

$routes->get('/instructor/course/(:num)/manage', 'Instructor::manageCourse/$1');

// Instructor course management routes
$routes->get('/instructor/course/(:num)/announcements', 'Instructor::announcements/$1');
$routes->match(['get', 'post'], '/instructor/course/(:num)/create-announcement', 'Instructor::createAnnouncement/$1');
$routes->get('/instructor/course/(:num)/assignments', 'Instructor::assignments/$1');
$routes->post('/instructor/course/(:num)/assignments/create', 'Instructor::createAssignment/$1');
$routes->get('/instructor/course/(:num)/grades', 'Instructor::grades/$1');
$routes->post('/instructor/course/(:num)/grades/update', 'Instructor::updateGrade/$1');
$routes->match(['get', 'post'], '/create-announcement', 'CreateAnnouncement::index');

$routes->match(['get', 'post'], '/instructor/course/upload', 'Materials::upload');
// $routes->post('instructor/course/upload', 'CourseController::uploadMaterial');

$routes->get('/admin/dashboard', 'Admin::dashboard');
$routes->get('/admin/manage-users', 'Admin::manageUsers');
$routes->get('/admin/manage-courses', 'Admin::manageCourses');
$routes->match(['get', 'post'], '/admin/create-user', 'Admin::createUser');
$routes->post('/admin/create-course', 'CreateCourse::create');
$routes->get('/admin/edit-course/(:num)', 'Admin::editCourse/$1');
$routes->post('/admin/update-course', 'Admin::updateCourse');
$routes->get('/admin/delete-course/(:num)', 'Admin::deleteCourse/$1');
$routes->get('/admin/edit-user/(:num)', 'Admin::editUser/$1');
$routes->post('/admin/update-user', 'Admin::updateUser');
$routes->get('/admin/restrict-user/(:num)', 'Admin::restrictUser/$1');
$routes->get('/admin/delete-user/(:num)', 'Admin::deleteUser/$1');

$routes->post('/course/enroll', 'Course::enroll'); // AJAX endpoint for enrollment
$routes->post('course/enroll', 'Course::enroll'); // AJAX endpoint for enrollment

// Use Dashboard::index for /dashboard to load courses and enrollments
$routes->get('/dashboard', 'Dashboard::index');

// My Courses page
$routes->get('/my-courses', 'Dashboard::myCourses');

// My Grades page
$routes->get('/my-grades', 'Dashboard::myGrades');

// Materials routes
$routes->get('/admin/course/(:num)/upload', 'Materials::upload/$1');
$routes->post('/admin/course/(:num)/upload', 'Materials::upload/$1');
$routes->post('/materials/delete/(:num)', 'Materials::delete/$1');
$routes->get('/materials/download/(:num)', 'Materials::download/$1');
$routes->get('/course/(:num)/materials', 'Materials::viewMaterials/$1');
$routes->get('/course/(:num)/materials', 'Materials::viewMaterials/$1');

// Notifications
$routes->get('/notifications', 'Notifications::index');
$routes->post('/notifications/mark_as_read/(:num)', 'Notifications::mark_as_read/$1');

$routes->get('courses', 'Course::index');

$routes->get('courses/search', 'Course::search');
$routes->post('courses/search', 'Course::search');

// Notifications fetch (AJAX polling endpoint)
$routes->get('notifications/fetch', 'Course::fetchNotifications');
