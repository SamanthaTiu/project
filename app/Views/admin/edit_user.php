<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
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
            background: #dc3545;
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
            background: #dc3545;
            color: #fff;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .main-content h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #dc3545;
            font-weight: bold;
            font-size: 42px;
        }
        .card {
            background: #fff;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, select {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            padding: 10px;
            background: #dc3545;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #c82333;
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
    <a href="<?= base_url('admin/dashboard') ?>">📊 Admin Dashboard</a>
    <a href="<?= base_url('admin/manage-users') ?>">👥 Manage Users</a>
    <a href="<?= base_url('admin/manage-courses') ?>">📚 Manage Courses</a>

    <a href="<?= base_url('logout') ?>">🚪 Logout</a>
</div>

<div class="main-content">
    <h2>Edit User</h2>

    <div class="card">
        <form action="<?= base_url('admin/update-user') ?>" method="post">
            <input type="hidden" name="user_id" value="<?= esc($user['user_id']) ?>">

            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?= esc($user['name']) ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= esc($user['email']) ?>" required>

            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="student" <?= $user['role'] == 'student' ? 'selected' : '' ?>>Student</option>
                <option value="teacher" <?= $user['role'] == 'instructor' ? 'selected' : '' ?>>Teacher</option>
                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>

            <button type="submit">Update User</button>
        </form>
    </div>
</div>

</body>
</html>
