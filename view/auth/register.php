<?php
require_once __DIR__ . '/../../controller/AuthController.php';

$auth = new AuthController();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_lengkap'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    $result = $auth->register($nama, $username, $password, $confirm);
    if ($result['success']) {
        header("Location: login.php?registered=1");
        exit();
    } else {
        $error = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Registrasi - Manajemen Data Alumni</title>
    <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-head">
                <div class="icon">📝</div>
                <h2>Registrasi Akun</h2>
                <p>Buat akun baru untuk mengakses sistem</p>
            </div>
            <div class="auth-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label>Nama Lengkap *</label>
                        <input type="text" name="nama_lengkap" value="<?= htmlspecialchars($_POST['nama_lengkap'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Username *</label>
                        <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Password *</label>
                        <input type="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label>Konfirmasi Password *</label>
                        <input type="password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Daftar</button>
                </form>
                <div class="auth-foot">
                    Sudah punya akun? <a href="login.php">Login di sini</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
