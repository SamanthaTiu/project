<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Announcement - <?= esc($course['course_name'] ?? 'Course') ?></title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f7fb;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #444;
        }
        .form-group input[type="text"],
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        .form-group textarea {
            min-height: 200px;
            resize: vertical;
        }
        .form-group input[type="text"]:focus,
        .form-group textarea:focus {
            border-color: #5865daff;
            outline: none;
            box-shadow: 0 0 0 3px rgba(88, 101, 218, 0.2);
        }
        .btn {
            background-color: #5865daff;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #4752c4;
        }
        .btn-cancel {
            background-color: #6c757d;
            margin-right: 10px;
        }
        .btn-cancel:hover {
            background-color: #5a6268;
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
        <h2>Create Announcement - <?= esc($course['course_name'] ?? 'Course') ?></h2>
        
        <div class="form-container">
            <?= form_open('instructor/course/' . $course['course_id'] . '/create-announcement') ?>
                <?= csrf_field() ?>
                <input type="hidden" name="course_id" value="<?= $course['course_id'] ?>">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" required>
                </div>
                
                <div class="form-group">
                    <label for="content">Content</label>
                    <textarea id="content" name="content" required></textarea>
                </div>
                
                <div class="form-actions">
                    <a href="<?= site_url('instructor/course/' . $course['course_id'] . '/announcements') ?>" class="btn btn-cancel">Cancel</a>
                    <button type="submit" class="btn">Create Announcement</button>
                </div>
            <?= form_close() ?>
        </div>
    </div>
</body>
</html>
