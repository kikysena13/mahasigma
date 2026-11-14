<?php
require_once __DIR__ . '/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Metode permintaan tidak diizinkan. Penghapusan harus menggunakan formulir.');
}

$nim = trim($_POST['nim'] ?? '');
if ($nim === '') {
    set_flash('error', 'NIM tidak valid. Data mahasiswa tidak dihapus.');
    header('Location: index.php');
    exit;
}

try {
    $stmt = $koneksi->prepare('DELETE FROM mahasiswa WHERE nim = ?');
    $stmt->bind_param('s', $nim);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        set_flash('error', 'Data mahasiswa tidak ditemukan atau sudah dihapus.');
    } else {
        set_flash('success', 'Data mahasiswa berhasil dihapus.');
    }
} catch (mysqli_sql_exception $e) {
    error_log('Gagal menghapus mahasiswa: ' . $e->getMessage());
    set_flash('error', 'Data mahasiswa belum dapat dihapus. Silakan coba kembali.');
}

header('Location: index.php');
exit;
