<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Course Assignments</title>
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
            background: #5865daff;
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
            background: #5865daff;
            color: #fff;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .main-content h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #5865daff;
            font-weight: bold;
            font-size: 42px;
        }
        .assignments-container {
            max-width: 800px;
            margin: auto;
        }
        .assignment {
            background: #fff;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .assignment h3 {
            color: #5865daff;
            margin-bottom: 10px;
        }
        .assignment .due-date {
            color: #e74c3c;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .assignment .description {
            line-height: 1.6;
            margin-bottom: 15px;
        }
        .assignment .actions {
            text-align: right;
        }
        .assignment .actions a {
            display: inline-block;
            padding: 8px 15px;
            background: #28a745;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            margin-left: 10px;
        }
        .assignment .actions a:hover {
            background: #218838;
        }
        .create-btn {
            display: inline-block;
            padding: 12px 25px;
            background: #5865daff;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 20px;
            transition: background 0.3s;
        }
        .create-btn:hover {
            background: #4a5ac7;
        }
        .no-assignments {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 40px;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="profile">
        <div class="circle"><?= strtoupper(substr(session()->get('name'), 0, 1)) ?></div>
        <h4><?= esc(session()->get('name')) ?></h4>
        <p><?= esc(session()->get('email')) ?></p>
    </div>
    <a href="<?= base_url('instructor/dashboard') ?>">📘 Dashboard</a>
    <a href="<?= base_url('instructor/course/courses') ?>">📚 My Courses</a>
    <a href="<?= base_url('instructor/my_students') ?>">🧑 My Students</a>
    <a href="#">📅 Class Schedule</a>
    <a href="<?= base_url('logout') ?>">🚪 Logout</a>
</div>

<div class="main-content">
    <h2>Assignments - <?= esc($course['course_name']) ?></h2>

    <div class="assignments-container">
        <a href="<?= base_url('instructor/course/' . $course['course_id'] . '/assignments/create') ?>" class="create-btn">Create New Assignment</a>

        <?php if (empty($assignments)): ?>
            <div class="no-assignments">
                <p>No assignments yet. Create your first assignment!</p>
            </div>
        <?php else: ?>
            <?php foreach ($assignments as $assignment): ?>
                <div class="assignment">
                    <h3><?= esc($assignment['title']) ?></h3>
                    <div class="due-date">Due: <?= date('F j, Y \a\t g:i A', strtotime($assignment['due_date'])) ?></div>
                    <div class="description"><?= nl2br(esc($assignment['description'])) ?></div>
                    <div class="actions">
                        <a href="<?= base_url('instructor/course/' . $course['course_id'] . '/grades?assignment_id=' . $assignment['id']) ?>">View Submissions</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
