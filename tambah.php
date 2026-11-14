<?php
require_once __DIR__ . '/koneksi.php';

$nim = '';
$nama = '';
$programStudi = '';
$alamat = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nim = trim($_POST['nim'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $programStudi = trim($_POST['program_studi'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');

    if ($nim === '') {
        $errors[] = 'NIM wajib diisi.';
    } elseif (strlen($nim) > 20) {
        $errors[] = 'NIM maksimal 20 karakter.';
    }
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
                'INSERT INTO mahasiswa (nim, nama, program_studi, alamat) VALUES (?, ?, ?, ?)'
            );
            $stmt->bind_param('ssss', $nim, $nama, $programStudi, $alamat);
            $stmt->execute();

            set_flash('success', 'Data mahasiswa berhasil ditambahkan.');
            header('Location: index.php');
            exit;
        } catch (mysqli_sql_exception $e) {
            if ((int) $e->getCode() === 1062) {
                $errors[] = 'NIM tersebut sudah digunakan. Silakan gunakan NIM lain.';
            } else {
                error_log('Gagal menambahkan mahasiswa: ' . $e->getMessage());
                $errors[] = 'Data mahasiswa belum dapat disimpan. Silakan coba kembali.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Mahasiswa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="container container-form">
        <header class="page-header">
            <div>
                <p class="eyebrow">Data Akademik</p>
                <h1>Tambah Mahasiswa</h1>
                <p class="subtitle">Isi informasi mahasiswa di bawah ini.</p>
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

            <form method="post" action="tambah.php">
                <div class="form-group">
                    <label for="nim">NIM <span class="required">*</span></label>
                    <input id="nim" name="nim" type="text" maxlength="20" value="<?= e($nim) ?>" required>
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
        </section>
    </main>
</body>
</html>
