<?php
require_once __DIR__ . '/../../controller/AuthController.php';
AuthController::checkSession();

require_once __DIR__ . '/../../controller/AlumniController.php';
$controller = new AlumniController();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nim' => trim($_POST['nim'] ?? ''),
        'nama_lengkap' => trim($_POST['nama_lengkap'] ?? ''),
        'jurusan' => trim($_POST['jurusan'] ?? ''),
        'tahun_lulus' => trim($_POST['tahun_lulus'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'no_telepon' => trim($_POST['no_telepon'] ?? ''),
        'pekerjaan_saat_ini' => trim($_POST['pekerjaan_saat_ini'] ?? '-'),
        'alamat' => trim($_POST['alamat'] ?? ''),
        'status' => $_POST['status'] ?? 'Belum Bekerja',
    ];

    $result = $controller->store($data, $_FILES['foto'] ?? []);
    if ($result['success']) {
        header("Location: dashboard.php?success=" . urlencode($result['message']));
        exit();
    } else {
        $errors = $result['errors'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Alumni - Manajemen Data Alumni</title>
    <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body>
<div class="layout">
    <?php include __DIR__ . '/../layout/sidebar.php'; ?>

    <div class="main-content">
        <div class="topbar">
            <h1>➕ Tambah Data Alumni</h1>
        </div>

        <div class="panel">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <strong>Terdapat kesalahan input:</strong>
                    <ul style="margin:6px 0 0 18px;">
                        <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="" enctype="multipart/form-data">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label>NIM *</label>
                        <input type="text" name="nim" value="<?= htmlspecialchars($_POST['nim'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Lengkap *</label>
                        <input type="text" name="nama_lengkap" value="<?= htmlspecialchars($_POST['nama_lengkap'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Jurusan *</label>
                        <input type="text" name="jurusan" value="<?= htmlspecialchars($_POST['jurusan'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Tahun Lulus *</label>
                        <input type="number" name="tahun_lulus" min="2000" max="<?= date('Y') + 1 ?>" value="<?= htmlspecialchars($_POST['tahun_lulus'] ?? date('Y')) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>No. Telepon *</label>
                        <input type="text" name="no_telepon" value="<?= htmlspecialchars($_POST['no_telepon'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Pekerjaan Saat Ini</label>
                        <input type="text" name="pekerjaan_saat_ini" value="<?= htmlspecialchars($_POST['pekerjaan_saat_ini'] ?? '') ?>" placeholder="-">
                    </div>
                    <div class="form-group">
                        <label>Status *</label>
                        <select name="status" required>
                            <option value="Bekerja">Bekerja</option>
                            <option value="Belum Bekerja">Belum Bekerja</option>
                            <option value="Wirausaha">Wirausaha</option>
                            <option value="Studi Lanjut">Studi Lanjut</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Alamat</label>
                    <textarea name="alamat" rows="3"><?= htmlspecialchars($_POST['alamat'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Foto (JPG/PNG, maks 2MB)</label>
                    <input type="file" name="foto" accept="image/jpeg,image/png">
                </div>

                <button type="submit" class="btn btn-primary" style="width:auto;padding:11px 28px;">💾 Simpan Data</button>
                <a href="dashboard.php" class="btn" style="background:#eee;width:auto;padding:11px 28px;">Batal</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
