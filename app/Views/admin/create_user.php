<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create User - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>

<!-- Sidebar -->
<div class="d-flex">
    <div class="bg-dark text-white p-3" style="width: 250px; min-height: 100vh;">
        <h4 class="text-center mb-4">Admin Panel</h4>
        <a href="<?= base_url('admin/dashboard') ?>" class="d-block text-white text-decoration-none py-2">📊 Dashboard</a>
        <a href="<?= base_url('admin/manage-users') ?>" class="d-block text-white text-decoration-none py-2">👥 Manage Users</a>
        <a href="<?= base_url('admin/manage-courses') ?>" class="d-block text-white text-decoration-none py-2">📚 Manage Courses</a>
        <a href="<?= base_url('logout') ?>" class="d-block text-white text-decoration-none py-2 mt-4">🚪 Logout</a>
    </div>

    <!-- Main Content -->
    <div class="flex-grow-1 p-4">
        <h2>Create New User</h2>

        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <?php if(isset($validation)): ?>
            <div class="alert alert-danger">
                <?= $validation->listErrors() ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('admin/create-user') ?>" method="post" class="mx-auto" style="max-width: 500px;">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" name="name" id="name" class="form-control" value="<?= set_value('name') ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="<?= set_value('email') ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select name="role" id="role" class="form-control" required>
                    <option value="">Select Role</option>
                    <option value="student" <?= set_value('role') == 'student' ? 'selected' : '' ?>>Student</option>
                    <option value="instructor" <?= set_value('role') == 'instructor' ? 'selected' : '' ?>>Instructor</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Create User</button>
            <a href="<?= base_url('admin/manage-users') ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
