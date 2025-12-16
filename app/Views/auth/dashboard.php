
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF token for AJAX (CodeIgniter 4) -->
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="csrf-name" content="<?= csrf_token() ?>">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f7fb;
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 250px;
            background: #343a40;
            color: #fff;
            padding-top: 30px;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2);
        }
        .sidebar .profile {
            text-align: center;
            padding: 20px;
        }
        .sidebar .profile .circle {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: #6c757d;
            margin: 0 auto 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 28px;
            font-weight: bold;
        }
        .sidebar .profile h4 {
            margin: 5px 0;
            font-size: 18px;
            font-weight: bold;
        }
        .sidebar .profile p {
            font-size: 14px;
            color: #bbb;
        }
        .sidebar a {
            display: block;
            padding: 12px 20px;
            color: #ddd;
            text-decoration: none;
            transition: 0.3s;
        }
        .sidebar a:hover {
            background: #007bff;
            color: #fff;
        }
        .main-content {
            margin-left: 250px;
            margin-top: 60px;
            padding: 20px;
        }
        .main-content h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #007bff;
            font-weight: bold;
            font-size: 42px;
        }
        .welcome-card {
            background: #fff;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            max-width: 1000px;
            margin: auto;
            text-align: center;
            margin-bottom: 30px;
        }

        /* Course sections styling */
        .courses-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            max-width: 1200px;
            margin: 20px auto;
        }
        .course-section {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.08);
            padding: 20px;
        }
        .course-section h3 {
            margin-top: 0;
            color: #343a40;
            border-bottom: 3px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .course-card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            background: #f8f9fa;
            transition: all 0.3s ease;
        }
        .course-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .course-card h4 {
            margin: 0 0 8px 0;
            font-size: 18px;
            color: #0d6efd;
        }
        .course-card p {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #6c757d;
        }
        .course-meta {
            font-size: 12px;
            color: #888;
            font-style: italic;
        }
        .btn-enroll {
            background: #0d6efd;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-enroll:hover:not(:disabled) {
            background: #0b5ed7;
            transform: scale(1.05);
        }
        .btn-enrolled {
            background: #198754;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
            cursor: not-allowed;
        }
        .empty-state {
            color: #6c757d;
            font-style: italic;
            text-align: center;
            padding: 30px;
        }

        /* Alert container */
        #alert-container {
            max-width: 1200px;
            margin: 0 auto 20px;
        }

        /* Top Navbar */
        .top-navbar {
            position: fixed;
            top: 0;
            right: 0;
            left: 250px;
            height: 60px;
            background: #fff;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        .top-navbar .search-container {
            flex: 1;
            max-width: 400px;
            margin-right: 20px;
        }
        .top-navbar .search-container input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }
        .top-navbar .search-container input:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
        }

        /* Notification dropdown styling */
        .dropdown-menu {
            min-width: 400px;
        }
        .top-navbar .notification-icon {
            font-size: 24px;
            color: #343a40;
            cursor: pointer;
            position: relative;
        }
        .top-navbar .notification-icon:hover {
            color: #007bff;
        }
        .top-navbar .notification-icon .badge {
            position: absolute;
            top: -5px;
            right: -10px;
            font-size: 10px;
            padding: 2px 5px;
        }

        /* Search dropdown styling */
        .search-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #fff;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 8px 8px;
            max-height: 300px;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .search-dropdown-item {
            padding: 10px 15px;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: background 0.2s;
        }
        .search-dropdown-item:hover {
            background: #f8f9fa;
        }
        .search-dropdown-item:last-child {
            border-bottom: none;
        }
        .search-dropdown-item h5 {
            margin: 0;
            font-size: 16px;
            color: #0d6efd;
        }
        .search-dropdown-item p {
            margin: 5px 0 0 0;
            font-size: 14px;
            color: #6c757d;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .courses-grid {
                grid-template-columns: 1fr;
            }
            .sidebar {
                width: 200px;
            }
            .main-content {
                margin-left: 200px;
            }
        }
        @media (max-width: 768px) {
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
            }
            .main-content {
                margin-left: 0;
            }
            .main-content h2 {
                font-size: 32px;
            }
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="profile">
        <div class="circle"><?= strtoupper(substr($name ?? 'S', 0, 1)) ?></div>
        <h4><?= esc($name ?? 'Student') ?></h4>
        <p><?= esc($email ?? 'student@example.com') ?></p>
    </div>

    <a href="<?= base_url('dashboard') ?>">🎓 Student Dashboard</a>
    <a href="<?= base_url('my-courses') ?>">📖 My Subjects</a>
    <a href="<?= base_url('my-grades') ?>">🧾 My Grades</a>
    <a href="<?= base_url('announcements') ?>">📢 Announcements</a>
    <a href="<?= base_url('logout') ?>">🚪 Logout</a>
</div>

<!-- Top Navbar -->
<div class="top-navbar">
    <div class="search-container">
        <input type="text" id="course-search" placeholder="Search courses..." />
        <div id="search-dropdown" class="search-dropdown" style="display: none;"></div>
    </div>
    <div class="dropdown" data-bs-auto-close="outside">
        <div class="notification-icon" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            🔔
            <span id="notification-badge" class="badge bg-danger" style="display: none;">0</span>
        </div>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown" id="notification-list">
            <li><a class="dropdown-item" href=>No new notifications</a></li>
        </ul>
    </div>
</div>

<!-- Main Content -->
<div class="main-content">
    <h2>Student Dashboard</h2>

    <!-- Alert Container for Bootstrap Alerts -->
    <div id="alert-container"></div>

    <!-- Welcome Card -->
    <div class="welcome-card">
        <h4>Welcome, <strong><?= esc($name ?? 'Student') ?></strong>!</h4>
        <p>You are logged in as <strong>student</strong>.</p>
        <p>Here you can view your lessons, submit assignments, and check grades.</p>
    </div>



    <!-- STEP 4: Display Enrolled Courses and Available Courses -->
    <div class="courses-grid">
        
        <!-- ENROLLED COURSES SECTION -->
        <div class="course-section">
            <h3>📚 Enrolled Courses</h3>
            <div id="enrolled-courses-list">
                <?php if (!empty($enrollments) && is_array($enrollments)): ?>
                    <?php foreach ($enrollments as $enrollment): ?>
                        <?php
                            $courseId = $enrollment['course_id'] ?? 0;
                            $courseTitle = $enrollment['course_name'] ?? $enrollment['name'] ?? 'Untitled Course';
                            $courseDesc = $enrollment['description'] ?? '';
                            $enrolledAt = isset($enrollment['enrollment_date'])
                                ? date('F j, Y', strtotime($enrollment['enrollment_date']))
                                : 'Unknown date';
                        ?>
                        <div class="course-card" data-course-id="<?= $courseId ?>">
                            <h4><?= esc($courseTitle) ?></h4>
                            <?php if ($courseDesc): ?>
                                <p><?= esc($courseDesc) ?></p>
                            <?php endif; ?>
                            <div class="course-meta">Enrolled: <?= $enrolledAt ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state" id="no-enrollments-message">
                        You are not enrolled in any courses yet.<br>
                        Browse available courses to get started!
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- AVAILABLE COURSES SECTION -->
        <div class="course-section">
            <h3>🌟 Available Courses</h3>
            <div id="available-courses-list">
                <?php if (!empty($courses) && is_array($courses)): ?>
                    <?php 
                        // Get list of enrolled course IDs for comparison
                        $enrolledCourseIds = [];
                        if (!empty($enrollments)) {
                            foreach ($enrollments as $en) {
                                $enrolledCourseIds[] = $en['id'] ?? $en['course_id'] ?? 0;
                            }
                        }
                    ?>
                    <?php foreach ($courses as $course): ?>
                        <?php
                            $courseId = $course['course_id'] ?? 0;
                            $courseTitle = $course['course_name'] ?? $course['name'] ?? 'Untitled Course';
                            $courseDesc = $course['description'] ?? '';
                            $isEnrolled = in_array($courseId, $enrolledCourseIds);
                        ?>
                        <div class="course-card" id="course-<?= $courseId ?>">
                            <h4><?= esc($courseTitle) ?></h4>
                            <?php if ($courseDesc): ?>
                                <p><?= esc($courseDesc) ?></p>
                            <?php endif; ?>
                            
                            <?php if ($isEnrolled): ?>
                                <button class="btn-enrolled" disabled>Enrolled</button>
                            <?php else: ?>
                                <!-- STEP 5: Add data_course_id attribute -->
                                <button class="btn-enroll" data-course-id="<?= $courseId ?>">
                                    Enroll Now
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        No courses available at the moment.
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<!-- jQuery Library -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- STEP 5: AJAX Enrollment Script -->
<script>
$(document).ready(function() {
    // Get CSRF token from meta tags
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    var csrfName = $('meta[name="csrf-name"]').attr('content');

    // Function to display Bootstrap alert
    function showAlert(type, message) {
        var alertId = 'alert-' + Date.now();
        var alertHTML = `
            <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        $('#alert-container').append(alertHTML);
        
        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            $('#' + alertId).alert('close');
        }, 5000);
    }

    // Listen for click on Enroll buttons
    $(document).on('click', '.btn-enroll', function(e) {
        e.preventDefault(); // Prevent default form submission

        var $button = $(this);
        var courseId = $button.attr('data-course-id');
        
        // Validation
        if (!courseId) {
            showAlert('danger', 'Error: Invalid course ID');
            return;
        }

        // Disable button and show loading state
        $button.prop('disabled', true).text('Enrolling...');

        // Prepare POST data
        var postData = {
            course_id: courseId
        };

        // Add CSRF token if available
        if (csrfName && csrfToken) {
            postData[csrfName] = csrfToken;
        }

        // AJAX POST request using $.post()
        $.post('<?= site_url("course/enroll") ?>', postData, function(response) {
            // Success callback
            if (response.status === 'success') {
                // Display success alert
                showAlert('success', response.message || 'Successfully enrolled in the course!');

                // Get course details
                var $courseCard = $('#course-' + courseId);
                var courseTitle = $courseCard.find('h4').text();
                var courseDesc = $courseCard.find('p').text();

                // Hide/Disable the Enroll button
                $button.removeClass('btn-enroll')
                       .addClass('btn-enrolled')
                       .text('Enrolled')
                       .prop('disabled', true);

                // Update Enrolled Courses list dynamically
                var enrolledDate = new Date().toLocaleDateString('en-US', { 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                });

                var newEnrolledCourse = `
                    <div class="course-card" data-course-id="${courseId}">
                        <h4>${courseTitle}</h4>
                        ${courseDesc ? '<p>' + courseDesc + '</p>' : ''}
                        <div class="course-meta">Enrolled: ${enrolledDate}</div>
                    </div>
                `;

                // Remove "no courses" message if it exists
                $('#no-enrollments-message').remove();

                // Add to enrolled courses list (prepend to show at top)
                $('#enrolled-courses-list').prepend(newEnrolledCourse);

                // Refresh notifications to show the new enrollment notification
                loadNotifications();

            } else {
                // Handle error response
                showAlert('danger', response.message || 'Failed to enroll in the course');
                
                // Re-enable button if not already enrolled
                if (response.message && response.message.includes('already')) {
                    $button.removeClass('btn-enroll')
                           .addClass('btn-enrolled')
                           .text('Enrolled')
                           .prop('disabled', true);
                } else {
                    $button.prop('disabled', false).text('Enroll Now');
                }
            }
        }, 'json')
        .fail(function(jqXHR, textStatus, errorThrown) {
            // Handle AJAX error
            console.error('AJAX Error:', textStatus, errorThrown);
            console.error('Response:', jqXHR.responseText);
            
            var errorMessage = 'An error occurred. Please try again.';
            
            // Try to parse error response
            try {
                var errorResponse = JSON.parse(jqXHR.responseText);
                errorMessage = errorResponse.message || errorMessage;
            } catch (e) {
                // Use default error message
            }

            showAlert('danger', errorMessage);

            // Handle specific HTTP status codes
            if (jqXHR.status === 409) {
                // Already enrolled
                $button.removeClass('btn-enroll')
                       .addClass('btn-enrolled')
                       .text('Enrolled')
                       .prop('disabled', true);
            } else if (jqXHR.status === 401) {
                // Not logged in
                showAlert('warning', 'Please log in to enroll in courses');
            } else {
                // Other errors - re-enable button
                $button.prop('disabled', false).text('Enroll Now');
            }
        });
    });

    // Function to load notifications
    function loadNotifications() {
        $.get('<?= site_url("notifications") ?>', function(response) {
            if (response.status === 'success') {
                var unreadCount = response.unread_count;
                var notifications = response.notifications;

                // Update badge
                if (unreadCount > 0) {
                    $('#notification-badge').text(unreadCount).show();
                } else {
                    $('#notification-badge').hide();
                }

                // Update dropdown menu
                var notificationList = $('#notification-list');
                notificationList.empty();

                if (notifications.length > 0) {
                    notifications.forEach(function(notification) {
                        // Determine the link based on notification message
                        var linkUrl = '#';
                        if (notification.message.includes('material uploaded')) {
                            // Extract course name from message like "New material uploaded in Course Name: file.pdf"
                            var courseMatch = notification.message.match(/New material uploaded in ([^:]+):/);
                            if (courseMatch) {
                                var courseName = courseMatch[1].trim();
                                // For now, link to my-courses page - you might want to make this more specific
                                linkUrl = '<?= site_url("my-courses") ?>';
                            }
                        } else if (notification.message.includes('announcement')) {
                            linkUrl = '<?= site_url("announcements") ?>';
                        } else if (notification.message.includes('assignment')) {
                            linkUrl = '<?= site_url("my-courses") ?>';
                        } else if (notification.message.includes('enrolled')) {
                            linkUrl = '<?= site_url("my-courses") ?>';
                        }

                        var isRead = notification.is_read == 1;
                        var alertClass = isRead ? 'alert-light' : 'alert-info';
                        var buttonText = isRead ? 'Mark as Unread' : 'Mark as Read';
                        var buttonClass = isRead ? 'btn-outline-secondary' : 'btn-outline-primary';

                        var notificationItem = `
                            <li class="alert ${alertClass} p-2 mb-1">
                                <div style="cursor: pointer;" onclick="window.location.href='${linkUrl}'">${notification.message}</div>
                                <button class="btn btn-sm ${buttonClass} mt-1 toggle-read-btn" data-id="${notification.id}" data-read="${notification.is_read}">${buttonText}</button>
                            </li>
                        `;
                        notificationList.append(notificationItem);
                    });
                } else {
                    notificationList.append('<li><a class="dropdown-item" href="#">No new notifications</a></li>');
                }
            }
        }, 'json');
    }

    // Load notifications on page load
    loadNotifications();

    // Optional: Fetch notifications every 60 seconds for real-time updates
    setInterval(function() {
        loadNotifications();
    }, 60000); // 60 seconds

    // Function to toggle read/unread status
    function toggleReadStatus(notificationId, buttonElement) {
        var $item = $(buttonElement).closest('li');
        var $button = $(buttonElement);
        var currentReadStatus = parseInt($button.data('read'));
        var isCurrentlyRead = currentReadStatus === 1;

        // Disable button and show loading state
        $button.prop('disabled', true).text('Updating...');

        // Determine which endpoint to call
        var endpoint = isCurrentlyRead ? '<?= site_url("notifications/mark_as_unread/") ?>' + notificationId : '<?= site_url("notifications/mark_as_read/") ?>' + notificationId;

        $.post(endpoint, function(response) {
            if (response.status === 'success') {
                // Toggle the read status
                var newReadStatus = isCurrentlyRead ? 0 : 1;
                $button.data('read', newReadStatus);

                if (newReadStatus === 1) {
                    // Mark as read - change to white/light background
                    $item.removeClass('alert-info').addClass('alert-light');
                    $button.removeClass('btn-outline-primary').addClass('btn-outline-secondary').text('Mark as Unread');
                } else {
                    // Mark as unread - change back to blue background
                    $item.removeClass('alert-light').addClass('alert-info');
                    $button.removeClass('btn-outline-secondary').addClass('btn-outline-primary').text('Mark as Read');
                }

                // Re-enable button
                $button.prop('disabled', false);

                // Update badge count only (don't refresh entire list)
                updateNotificationBadge();
            } else {
                showAlert('danger', 'Failed to update notification status');
                // Re-enable button on error
                $button.prop('disabled', false).text(isCurrentlyRead ? 'Mark as Unread' : 'Mark as Read');
            }
        }, 'json');
    }

    // Function to update only the notification badge count
    function updateNotificationBadge() {
        $.get('<?= site_url("notifications") ?>', function(response) {
            if (response.status === 'success') {
                var unreadCount = response.unread_count;
                // Update badge
                if (unreadCount > 0) {
                    $('#notification-badge').text(unreadCount).show();
                } else {
                    $('#notification-badge').hide();
                }
            }
        }, 'json');
    }

    // Handle toggle read status
    $(document).on('click', '.toggle-read-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var notificationId = $(this).data('id');
        toggleReadStatus(notificationId, this);
    });

    // Course search functionality with dropdown
    var allCourses = [];

    // Collect all courses on page load
    function collectCourses() {
        allCourses = [];
        $('.course-card').each(function() {
            var $card = $(this);
            var courseId = $card.data('course-id') || $card.attr('id').replace('course-', '');
            var title = $card.find('h4').text().trim();
            var description = $card.find('p').text().trim() || '';
            allCourses.push({
                id: courseId,
                title: title,
                description: description,
                element: $card
            });
        });
    }

    // Initialize courses collection
    collectCourses();

    // Search input handler
    $('#course-search').on('input', function() {
        var query = $(this).val().toLowerCase().trim();
        var $dropdown = $('#search-dropdown');

        if (query.length === 0) {
            $dropdown.hide();
            $('.course-card').show();
            return;
        }

        // Filter courses
        var matchingCourses = allCourses.filter(function(course) {
            return course.title.toLowerCase().includes(query) ||
                   course.description.toLowerCase().includes(query);
        });

        // Show all courses first, then hide non-matching ones
        $('.course-card').show();
        if (matchingCourses.length > 0) {
            $('.course-card').each(function() {
                var courseId = $(this).data('course-id') || $(this).attr('id').replace('course-', '');
                var isMatch = matchingCourses.some(function(course) {
                    return course.id == courseId;
                });
                if (!isMatch) {
                    $(this).hide();
                }
            });
        } else {
            $('.course-card').hide();
        }

        // Update dropdown
        $dropdown.empty();
        if (matchingCourses.length > 0 && matchingCourses.length <= 10) { // Show dropdown only if 10 or fewer matches
            matchingCourses.forEach(function(course) {
                var itemHTML = `
                    <div class="search-dropdown-item" data-course-id="${course.id}">
                        <h5>${course.title}</h5>
                        ${course.description ? '<p>' + course.description + '</p>' : ''}
                    </div>
                `;
                $dropdown.append(itemHTML);
            });
            $dropdown.show();
        } else {
            $dropdown.hide();
        }
    });

    // Handle dropdown item click
    $(document).on('click', '.search-dropdown-item', function() {
        var courseId = $(this).data('course-id');

        // Redirect to course materials page
        window.location.href = '<?= site_url("course/") ?>' + courseId + '/materials';

        // Hide dropdown
        $('#search-dropdown').hide();
    });

    // Hide dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.search-container').length) {
            $('#search-dropdown').hide();
        }
    });
});
</script>

</body>
</html>
