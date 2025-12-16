<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Subjects</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

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
            padding: 20px;
        }
        .main-content h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #007bff;
            font-weight: bold;
            font-size: 42px;
        }
        .course-card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        .course-card:hover {
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .course-card h4 {
            margin: 0 0 10px 0;
            font-size: 20px;
            color: #0d6efd;
        }
        .course-card p {
            margin: 0 0 15px 0;
            font-size: 14px;
            color: #6c757d;
        }
        .course-meta {
            font-size: 13px;
            color: #888;
            font-style: italic;
            margin-bottom: 10px;
        }
        .instructor-info {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 6px;
            border-left: 4px solid #007bff;
        }
        .instructor-info strong {
            color: #007bff;
        }
        .empty-state {
            color: #6c757d;
            font-style: italic;
            text-align: center;
            padding: 50px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        /* Responsive */
        @media (max-width: 992px) {
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


<!-- Main Content -->
<div class="main-content">
    <h2>My Subjects</h2>

    <div class="container-fluid">
        <?php if (!empty($enrollments) && is_array($enrollments)): ?>
            <?php foreach ($enrollments as $enrollment): ?>
                <?php
                    $courseId = $enrollment['course_id'] ?? $enrollment['id'] ?? 0;
                    $courseTitle = $enrollment['course_name'] ?? $enrollment['name'] ?? 'Untitled Course';
                    $courseDesc = $enrollment['description'] ?? '';
                    $enrolledAt = isset($enrollment['enrollment_date'])
                        ? date('F j, Y', strtotime($enrollment['enrollment_date']))
                        : 'Unknown date';
                    $instructorName = $enrollment['instructor_name'] ?? 'Unknown Instructor';
                ?>
                <div class="course-card" style="cursor: pointer;" onclick="window.location.href='<?= base_url('course/' . $courseId . '/materials') ?>'">
                    <h4 style="cursor: pointer;"><?= esc($courseTitle) ?></h4>
                    <?php if ($courseDesc): ?>
                        <p><?= esc($courseDesc) ?></p>
                    <?php endif; ?>
                    <div class="course-meta">Enrolled: <?= $enrolledAt ?></div>
                    <div class="instructor-info">
                        <strong>Instructor:</strong> <?= esc($instructorName) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <h4>You haven't enrolled in any courses yet.</h4>
                <p>Visit the <a href="<?= base_url('dashboard') ?>" style="color: #007bff;">dashboard</a> to browse and enroll in available courses.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
