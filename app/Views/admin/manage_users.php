<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
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
            max-width: 100%;
            margin: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
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
    <h2>Manage Users</h2>

    <div class="card" style="margin-bottom: 20px; display: flex; justify-content: space-around; flex-wrap: wrap;">
        <div style="text-align: center; flex: 1; min-width: 150px; margin: 10px;">
            <h3 style="color: #007bff; font-size: 36px; margin: 0;"><?= esc($totalUsers) ?></h3>
            <p style="margin: 5px 0; font-weight: bold;">Total Users</p>
        </div>
        <div style="text-align: center; flex: 1; min-width: 150px; margin: 10px;">
            <h3 style="color: #dc3545; font-size: 36px; margin: 0;"><?= esc($totalAdmins) ?></h3>
            <p style="margin: 5px 0; font-weight: bold;">Administrators</p>
        </div>
        <div style="text-align: center; flex: 1; min-width: 150px; margin: 10px;">
            <h3 style="color: #28a745; font-size: 36px; margin: 0;"><?= esc($totalTeachers) ?></h3>
            <p style="margin: 5px 0; font-weight: bold;">Teachers</p>
        </div>
        <div style="text-align: center; flex: 1; min-width: 150px; margin: 10px;">
            <h3 style="color: #ffc107; font-size: 36px; margin: 0;"><?= esc($totalStudents) ?></h3>
            <p style="margin: 5px 0; font-weight: bold;">Students</p>
        </div>
    </div>

    <div class="card">
        <div style="margin-bottom: 20px;">
            <a href="<?= base_url('admin/create-user') ?>" class="btn btn-primary">Create New User</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= esc($user['user_id']) ?></td>
                            <td><?= esc($user['name']) ?></td>
                            <td><?= esc($user['email']) ?></td>
                            <td><?= esc($user['role']) ?></td>
                            <td>
                                <a href="<?= base_url('admin/edit-user/' . $user['user_id']) ?>">Edit</a> |
                                <a href="<?= base_url('admin/restrict-user/' . $user['user_id']) ?>" onclick="return confirm('Are you sure you want to restrict this user?')">Restrict</a> |
                                <a href="<?= base_url('admin/delete-user/' . $user['user_id']) ?>" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">No users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>

