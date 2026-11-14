<?php
require_once __DIR__ . '/koneksi.php';

$mahasiswa = [];
$pesanError = '';

try {
    $hasil = $koneksi->query(
        'SELECT nim, nama, program_studi, alamat FROM mahasiswa ORDER BY nim'
    );
    $mahasiswa = $hasil->fetch_all(MYSQLI_ASSOC);
} catch (mysqli_sql_exception $e) {
    error_log('Gagal mengambil daftar mahasiswa: ' . $e->getMessage());
    $pesanError = 'Data mahasiswa belum dapat ditampilkan. Silakan coba kembali.';
}

$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Data Mahasiswa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="container">
        <header class="page-header">
            <div>
                <p class="eyebrow">Data Akademik</p>
                <h1>Sistem Informasi Data Mahasiswa</h1>
                <p class="subtitle">Kelola data mahasiswa dengan mudah.</p>
            </div>
            <a class="button button-primary" href="tambah.php">Tambah Mahasiswa</a>
        </header>

        <?php if ($flash !== null): ?>
            <div class="notice notice-<?= e($flash['jenis']) ?>" role="status">
                <?= e($flash['pesan']) ?>
            </div>
        <?php endif; ?>

        <?php if ($pesanError !== ''): ?>
            <div class="notice notice-error" role="alert"><?= e($pesanError) ?></div>
        <?php elseif (count($mahasiswa) === 0): ?>
            <section class="empty-state">
                <h2>Belum ada data mahasiswa</h2>
                <p>Tambahkan data mahasiswa untuk mulai menggunakan aplikasi.</p>
                <a class="button button-primary" href="tambah.php">Tambah Mahasiswa</a>
            </section>
        <?php else: ?>
            <section class="card">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>Program Studi</th>
                                <th>Alamat</th>
                                <th class="actions-heading">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($mahasiswa as $baris): ?>
                                <tr>
                                    <td data-label="NIM"><?= e($baris['nim']) ?></td>
                                    <td data-label="Nama"><?= e($baris['nama']) ?></td>
                                    <td data-label="Program Studi"><?= e($baris['program_studi']) ?></td>
                                    <td data-label="Alamat"><?= e($baris['alamat']) ?></td>
                                    <td data-label="Aksi" class="row-actions">
                                        <a
                                            class="button button-small button-secondary"
                                            href="edit.php?nim=<?= rawurlencode($baris['nim']) ?>"
                                        >Edit</a>
                                        <form
                                            action="hapus.php"
                                            method="post"
                                            onsubmit="return confirm('Yakin ingin menghapus data mahasiswa ini?');"
                                        >
                                            <input type="hidden" name="nim" value="<?= e($baris['nim']) ?>">
                                            <button class="button button-small button-danger" type="submit">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        <?php endif; ?>
        <footer class="footer">Aplikasi sederhana untuk pengelolaan data mahasiswa.</footer>
    </main>
</body>
</html>
