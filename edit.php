<?php
require_once __DIR__ . '/koneksi.php';

$nim = trim($_SERVER['REQUEST_METHOD'] === 'POST'
    ? ($_POST['nim'] ?? '')
    : ($_GET['nim'] ?? ''));
$nama = '';
$programStudi = '';
$alamat = '';
$errors = [];
$dataDitemukan = false;

if ($nim === '') {
    http_response_code(400);
    $errors[] = 'NIM tidak ditemukan pada permintaan.';
} else {
    try {
        $stmt = $koneksi->prepare(
            'SELECT nama, program_studi, alamat FROM mahasiswa WHERE nim = ?'
        );
        $stmt->bind_param('s', $nim);
        $stmt->execute();
        $stmt->bind_result($namaTersimpan, $programStudiTersimpan, $alamatTersimpan);
        if ($stmt->fetch()) {
            $dataDitemukan = true;
            $nama = $namaTersimpan;
            $programStudi = $programStudiTersimpan;
            $alamat = $alamatTersimpan ?? '';
        }
        $stmt->close();
    } catch (mysqli_sql_exception $e) {
        error_log('Gagal mengambil mahasiswa untuk diedit: ' . $e->getMessage());
        $errors[] = 'Data mahasiswa belum dapat dimuat. Silakan coba kembali.';
    }

    if (!$dataDitemukan && count($errors) === 0) {
        http_response_code(404);
        $errors[] = 'Data mahasiswa tidak ditemukan.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $dataDitemukan) {
    $nama = trim($_POST['nama'] ?? '');
    $programStudi = trim($_POST['program_studi'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');

    if ($nama === '') {
        $errors[] = 'Nama wajib diisi.';
    } elseif (mb_strlen($nama, 'UTF-8') > 100) {
        $errors[] = 'Nama maksimal 100 karakter.';
    }
    if ($programStudi === '') {
        $errors[] = 'Program studi wajib diisi.';
    } elseif (mb_strlen($programStudi, 'UTF-8') > 100) {
        $errors[] = 'Program studi maksimal 100 karakter.';
    }

    if (count($errors) === 0) {
        try {
            $stmt = $koneksi->prepare(
                'UPDATE mahasiswa SET nama = ?, program_studi = ?, alamat = ? WHERE nim = ?'
            );
            $stmt->bind_param('ssss', $nama, $programStudi, $alamat, $nim);
            $stmt->execute();

            set_flash('success', 'Data mahasiswa berhasil diperbarui.');
            header('Location: index.php');
            exit;
        } catch (mysqli_sql_exception $e) {
            error_log('Gagal memperbarui mahasiswa: ' . $e->getMessage());
            $errors[] = 'Perubahan data belum dapat disimpan. Silakan coba kembali.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Mahasiswa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="container container-form">
        <header class="page-header">
            <div>
                <p class="eyebrow">Data Akademik</p>
                <h1>Edit Mahasiswa</h1>
                <p class="subtitle">Perbarui informasi mahasiswa. NIM tidak dapat diubah.</p>
            </div>
        </header>

        <section class="card form-card">
            <?php if (count($errors) > 0): ?>
                <div class="notice notice-error" role="alert">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= e($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($dataDitemukan): ?>
                <form method="post" action="edit.php">
                    <input type="hidden" name="nim" value="<?= e($nim) ?>">
                    <div class="form-group">
                        <label for="nim_display">NIM</label>
                        <input id="nim_display" type="text" value="<?= e($nim) ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="nama">Nama <span class="required">*</span></label>
                        <input id="nama" name="nama" type="text" maxlength="100" value="<?= e($nama) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="program_studi">Program Studi <span class="required">*</span></label>
                        <input
                            id="program_studi"
                            name="program_studi"
                            type="text"
                            maxlength="100"
                            value="<?= e($programStudi) ?>"
                            required
                        >
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat</label>
                        <textarea id="alamat" name="alamat" rows="4"><?= e($alamat) ?></textarea>
                    </div>
                    <p class="form-hint"><span class="required">*</span> Wajib diisi.</p>
                    <div class="form-actions">
                        <button class="button button-primary" type="submit">Simpan</button>
                        <a class="button button-secondary" href="index.php">Batal/Kembali</a>
                    </div>
                </form>
            <?php else: ?>
                <a class="button button-secondary" href="index.php">Kembali ke Daftar</a>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
