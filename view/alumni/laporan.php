<?php
require_once __DIR__ . '/../../controller/AuthController.php';
AuthController::checkSession();

require_once __DIR__ . '/../../controller/AlumniController.php';
$controller = new AlumniController();

$statusFilter = $_GET['status'] ?? '';
$data = $controller->getData('', $statusFilter);

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
    <title>Laporan Data Alumni</title>
    <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body>
<div class="layout">
    <?php include __DIR__ . '/../layout/sidebar.php'; ?>

    <div class="main-content">
        <div class="topbar">
            <h1>📊 Laporan Data Alumni</h1>
        </div>

        <div class="panel">
            <div class="panel-head">
                <h3>Filter Laporan</h3>
                <div style="display:flex;gap:10px;">
                    <a href="../../controller/export.php?type=pdf&status=<?= urlencode($statusFilter) ?>" class="btn-export-pdf">📄 Export PDF</a>
                    <a href="../../controller/export.php?type=excel&status=<?= urlencode($statusFilter) ?>" class="btn-export-xls">📊 Export Excel</a>
                </div>
            </div>

            <form method="GET" class="search-bar" style="margin-bottom:18px;">
                <select name="status">
                    <option value="">Semua Status</option>
                    <option value="Bekerja" <?= $statusFilter === 'Bekerja' ? 'selected' : '' ?>>Bekerja</option>
                    <option value="Belum Bekerja" <?= $statusFilter === 'Belum Bekerja' ? 'selected' : '' ?>>Belum Bekerja</option>
                    <option value="Wirausaha" <?= $statusFilter === 'Wirausaha' ? 'selected' : '' ?>>Wirausaha</option>
                    <option value="Studi Lanjut" <?= $statusFilter === 'Studi Lanjut' ? 'selected' : '' ?>>Studi Lanjut</option>
                </select>
                <button type="submit" class="btn btn-primary" style="width:auto;">🔍 Filter</button>
            </form>

            <h3 style="margin-bottom:10px;">Data Alumni <span style="font-weight:400;color:#777;">(<?= count($data) ?> records)</span></h3>
            <div style="overflow-x:auto;">
            <table>
                <thead>
                <tr>
                    <th>No</th>
                    <th>NIM</th>
                    <th>Nama Lengkap</th>
                    <th>Jurusan</th>
                    <th>Tahun Lulus</th>
                    <th>Email</th>
                    <th>No. Telepon</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($data)): ?>
                    <tr><td colspan="8" class="empty-state">Tidak ada data</td></tr>
                <?php else: ?>
                    <?php foreach ($data as $i => $row): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($row['nim']) ?></td>
                            <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                            <td><?= htmlspecialchars($row['jurusan']) ?></td>
                            <td><?= htmlspecialchars($row['tahun_lulus']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['no_telepon']) ?></td>
                            <td><span class="badge <?= badgeClass($row['status']) ?>"><?= htmlspecialchars($row['status']) ?></span></td>
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
