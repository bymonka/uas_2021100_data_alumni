<?php
require_once __DIR__ . '/../../controller/AuthController.php';
AuthController::checkSession();

require_once __DIR__ . '/../../controller/AlumniController.php';
$controller = new AlumniController();

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: dashboard.php");
    exit();
}

$alumni = $controller->getById($id);
if (!$alumni) {
    header("Location: dashboard.php");
    exit();
}

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

    $result = $controller->updateData($id, $data, $_FILES['foto'] ?? []);
    if ($result['success']) {
        header("Location: dashboard.php?success=" . urlencode($result['message']));
        exit();
    } else {
        $errors = $result['errors'];
        $alumni = array_merge($alumni, $data); 
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Alumni - Manajemen Data Alumni</title>
    <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body>
<div class="layout">
    <?php include __DIR__ . '/../layout/sidebar.php'; ?>

    <div class="main-content">
        <div class="topbar">
            <h1>✏️ Edit Data Alumni</h1>
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
                        <input type="text" name="nim" value="<?= htmlspecialchars($alumni['nim']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Lengkap *</label>
                        <input type="text" name="nama_lengkap" value="<?= htmlspecialchars($alumni['nama_lengkap']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Jurusan *</label>
                        <input type="text" name="jurusan" value="<?= htmlspecialchars($alumni['jurusan']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Tahun Lulus *</label>
                        <input type="number" name="tahun_lulus" min="2000" max="<?= date('Y') + 1 ?>" value="<?= htmlspecialchars($alumni['tahun_lulus']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($alumni['email']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>No. Telepon *</label>
                        <input type="text" name="no_telepon" value="<?= htmlspecialchars($alumni['no_telepon']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Pekerjaan Saat Ini</label>
                        <input type="text" name="pekerjaan_saat_ini" value="<?= htmlspecialchars($alumni['pekerjaan_saat_ini']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Status *</label>
                        <select name="status" required>
                            <?php foreach (['Bekerja','Belum Bekerja','Wirausaha','Studi Lanjut'] as $opt): ?>
                                <option value="<?= $opt ?>" <?= $alumni['status'] === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Alamat</label>
                    <textarea name="alamat" rows="3"><?= htmlspecialchars($alumni['alamat']) ?></textarea>
                </div>
                <div class="form-group">
                    <label>Foto saat ini</label><br>
                    <?php if (!empty($alumni['foto'])): ?>
                        <img src="../../public/uploads/<?= htmlspecialchars($alumni['foto']) ?>" class="foto-thumb" style="width:60px;height:60px;margin-bottom:8px;">
                    <?php else: ?>
                        <span style="color:#999;font-size:13px;">Belum ada foto</span>
                    <?php endif; ?>
                    <label style="margin-top:8px;">Ganti Foto (opsional, JPG/PNG maks 2MB)</label>
                    <input type="file" name="foto" accept="image/jpeg,image/png">
                </div>

                <button type="submit" class="btn btn-primary" style="width:auto;padding:11px 28px;">💾 Update Data</button>
                <a href="dashboard.php" class="btn" style="background:#eee;width:auto;padding:11px 28px;">Batal</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
