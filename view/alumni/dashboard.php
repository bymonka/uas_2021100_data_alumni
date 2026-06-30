<?php
require_once __DIR__ . '/../../controller/AuthController.php';
AuthController::checkSession();

require_once __DIR__ . '/../../controller/AlumniController.php';
$controller = new AlumniController();

if (isset($_GET['delete'])) {
    $controller->destroy($_GET['delete']);
    header("Location: dashboard.php?deleted=1");
    exit();
}

$keyword = trim($_GET['keyword'] ?? '');
$statusFilter = $_GET['status'] ?? '';
$data = $controller->getData($keyword, $statusFilter);
$stats = $controller->getStats();

function badgeClass($status) {
    return match ($status) {
        'Bekerja' => 'bekerja',
        'Belum Bekerja' => 'belum',
        'Wirausaha' => 'wirausaha',
        'Studi Lanjut' => 'studi',
        default => 'belum',
    };
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Manajemen Data Alumni</title>
    <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body>
<div class="layout">
    <?php include __DIR__ . '/../layout/sidebar.php'; ?>

    <div class="main-content">
        <div class="topbar">
            <h1>Dashboard</h1>
            <div class="date">📅 <?= date('d F Y') ?></div>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['deleted'])): ?>
            <div class="alert alert-success">Data alumni berhasil dihapus</div>
        <?php endif; ?>

        <div class="stat-grid">
            <div class="stat-card">
                <h4><?= $stats['total'] ?></h4>
                <p>👥 Total Alumni</p>
            </div>
            <div class="stat-card green">
                <h4><?= $stats['bekerja'] ?></h4>
                <p>✅ Sudah Bekerja</p>
            </div>
            <div class="stat-card yellow">
                <h4><?= $stats['studi_lanjut'] ?></h4>
                <p>🎓 Studi Lanjut</p>
            </div>
            <div class="stat-card purple">
                <h4><?= $stats['wirausaha'] ?></h4>
                <p>💼 Wirausaha</p>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <h3>📋 Data Alumni</h3>
                <a href="tambah.php" class="btn-add">+ Tambah Alumni</a>
            </div>

            <form method="GET" class="search-bar" style="margin-bottom:18px;">
                <input type="text" name="keyword" placeholder="Cari NIM, nama, jurusan, email..." value="<?= htmlspecialchars($keyword) ?>" style="flex:1;min-width:200px;">
                <select name="status">
                    <option value="">Semua Status</option>
                    <option value="Bekerja" <?= $statusFilter === 'Bekerja' ? 'selected' : '' ?>>Bekerja</option>
                    <option value="Belum Bekerja" <?= $statusFilter === 'Belum Bekerja' ? 'selected' : '' ?>>Belum Bekerja</option>
                    <option value="Wirausaha" <?= $statusFilter === 'Wirausaha' ? 'selected' : '' ?>>Wirausaha</option>
                    <option value="Studi Lanjut" <?= $statusFilter === 'Studi Lanjut' ? 'selected' : '' ?>>Studi Lanjut</option>
                </select>
                <button type="submit" class="btn btn-primary" style="width:auto;">🔍 Cari</button>
                <a href="dashboard.php" class="btn" style="background:#eee;width:auto;">Reset</a>
            </form>

            <div style="overflow-x:auto;">
            <table>
                <thead>
                <tr>
                    <th>#</th>
                    <th>Foto</th>
                    <th>NIM</th>
                    <th>Nama Lengkap</th>
                    <th>Jurusan</th>
                    <th>Thn Lulus</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($data)): ?>
                    <tr><td colspan="9" class="empty-state">Tidak ada data ditemukan</td></tr>
                <?php else: ?>
                    <?php foreach ($data as $i => $row): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <?php if (!empty($row['foto'])): ?>
                                    <img src="../../public/uploads/<?= htmlspecialchars($row['foto']) ?>" class="foto-thumb">
                                <?php else: ?>
                                    <div class="foto-thumb" style="background:#ddd;display:flex;align-items:center;justify-content:center;">👤</div>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['nim']) ?></td>
                            <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                            <td><?= htmlspecialchars($row['jurusan']) ?></td>
                            <td><?= htmlspecialchars($row['tahun_lulus']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><span class="badge <?= badgeClass($row['status']) ?>"><?= htmlspecialchars($row['status']) ?></span></td>
                            <td class="action-btns">
                                <a href="edit.php?id=<?= $row['id'] ?>" class="btn-edit">✏️ Edit</a>
                                <a href="dashboard.php?delete=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Yakin ingin menghapus data ini?')">🗑️ Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>
