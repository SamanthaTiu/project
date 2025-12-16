<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
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
            max-width: 700px;
            margin: auto;
            text-align: center;
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
    <h2>Admin Dashboard</h2>

    <div class="card">
        <h4>Welcome, <strong><?= esc(session()->get('name')) ?></strong>!</h4>
        <p>You are logged in as <strong>Admin</strong>.</p>
        <p>Manage users, courses, and system settings.</p>
    </div>

    <div class="card" style="margin-top: 20px;">
        <h4>Recent Registrations</h4>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="border: 1px solid #ddd; padding: 8px;">User ID</th>
                    <th style="border: 1px solid #ddd; padding: 8px;">Name</th>
                    <th style="border: 1px solid #ddd; padding: 8px;">Email</th>
                    <th style="border: 1px solid #ddd; padding: 8px;">Role</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($recentRegistrations)): ?>
                    <?php foreach ($recentRegistrations as $user): ?>
                        <tr>
                            <td style="border: 1px solid #ddd; padding: 8px;"><?= esc($user['user_id']) ?></td>
                            <td style="border: 1px solid #ddd; padding: 8px;"><?= esc($user['name']) ?></td>
                            <td style="border: 1px solid #ddd; padding: 8px;"><?= esc($user['email']) ?></td>
                            <td style="border: 1px solid #ddd; padding: 8px;"><?= esc($user['role']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="border: 1px solid #ddd; padding: 8px; text-align: center;">No recent registrations found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>