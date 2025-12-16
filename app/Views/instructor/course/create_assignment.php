<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Assignment</title>
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
        .form-container {
            max-width: 600px;
            margin: auto;
            background: #fff;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            box-sizing: border-box;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #5865daff;
            box-shadow: 0 0 5px rgba(88, 101, 218, 0.3);
        }
        .btn-container {
            text-align: center;
            margin-top: 30px;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.3s;
            margin: 0 10px;
        }
        .btn-primary {
            background: #5865daff;
            color: #fff;
        }
        .btn-primary:hover {
            background: #4a5ac7;
        }
        .btn-secondary {
            background: #6c757d;
            color: #fff;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        .error {
            color: #e74c3c;
            font-size: 14px;
            margin-top: 5px;
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
    <h2>Create Assignment - <?= esc($course['course_name']) ?></h2>

    <div class="form-container">
        <form method="POST" action="">
            <div class="form-group">
                <label for="title">Assignment Title *</label>
                <input type="text" id="title" name="title" required value="<?= set_value('title') ?>">
                <?php if (isset($validation) && $validation->hasError('title')): ?>
                    <div class="error"><?= $validation->getError('title') ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="description">Description *</label>
                <textarea id="description" name="description" required><?= set_value('description') ?></textarea>
                <?php if (isset($validation) && $validation->hasError('description')): ?>
                    <div class="error"><?= $validation->getError('description') ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="due_date">Due Date *</label>
                <input type="datetime-local" id="due_date" name="due_date" required value="<?= set_value('due_date') ?>">
                <?php if (isset($validation) && $validation->hasError('due_date')): ?>
                    <div class="error"><?= $validation->getError('due_date') ?></div>
                <?php endif; ?>
            </div>

            <div class="btn-container">
                <a href="<?= base_url('instructor/course/' . $course['course_id'] . '/assignments') ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Assignment</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
