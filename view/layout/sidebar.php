<div class="sidebar">
    <div class="brand">
        <div class="icon">🎓</div>
        <h3>Manajemen Alumni</h3>
        <small>Welcome, <?= htmlspecialchars($_SESSION['nama_lengkap'] ?? '') ?></small>
    </div>
    <nav>
        <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>">🏠 Dashboard</a>
        <a href="tambah.php" class="<?= basename($_SERVER['PHP_SELF']) === 'tambah.php' ? 'active' : '' ?>">➕ Tambah Alumni</a>
        <a href="laporan.php" class="<?= basename($_SERVER['PHP_SELF']) === 'laporan.php' ? 'active' : '' ?>">📊 Laporan</a>
        <a href="../auth/logout.php" onclick="return confirm('Yakin ingin logout?')">🚪 Logout</a>
    </nav>
</div>
