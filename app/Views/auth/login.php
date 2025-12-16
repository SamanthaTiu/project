<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- ✅ Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= base_url('') ?>">MyWebsite</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="<?= base_url('') ?>">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('about') ?>">About</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('contact') ?>">Contact</a></li>
                <li class="nav-item"><a class="nav-link active" href="<?= base_url('login') ?>">Login</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('register') ?>">Register</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- ✅ Main Content -->
<div class="container mt-5">
    <h2 class="mb-4 text-center">Login</h2>

    <!-- Flash message -->
    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <!-- Login Form -->
    <form method="post" action="<?= base_url('login') ?>" class="mx-auto" style="max-width: 400px;">
        <?= csrf_field() ?>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
