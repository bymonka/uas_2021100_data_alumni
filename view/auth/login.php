<?php
require_once __DIR__ . '/../../controller/AuthController.php';

$auth = new AuthController();
if (isset($_SESSION['user_id'])) {
    header("Location: ../alumni/dashboard.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $result = $auth->login($username, $password);
    if ($result['success']) {
        header("Location: ../alumni/dashboard.php");
        exit();
    } else {
        $error = $result['message'];
    }
}

if (isset($_GET['msg']) && $_GET['msg'] === 'session_expired') {
    $error = 'Sesi Anda telah berakhir, silakan login kembali';
}
if (isset($_GET['registered'])) {
    $success = 'Registrasi berhasil! Silakan login.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Manajemen Data Alumni</title>
    <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-head">
                <div class="icon">🎓</div>
                <h2>Manajemen Data Alumni</h2>
                <p>Silakan login untuk melanjutkan</p>
            </div>
            <div class="auth-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" placeholder="Masukkan username" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" placeholder="Masukkan password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Login</button>
                </form>
                <div class="auth-foot">
                    Belum punya akun? <a href="register.php">Registrasi</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
