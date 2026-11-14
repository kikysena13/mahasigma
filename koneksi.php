<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $koneksi = new mysqli('localhost', 'root', '', 'db_mahasiswa');
    $koneksi->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    error_log('Koneksi database gagal: ' . $e->getMessage());
    http_response_code(500);
    exit('Koneksi database gagal. Pastikan MySQL aktif dan pengaturan di koneksi.php sesuai.');
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e($nilai): string
{
    return htmlspecialchars((string) $nilai, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function set_flash(string $jenis, string $pesan): void
{
    $_SESSION['flash'] = [
        'jenis' => $jenis,
        'pesan' => $pesan,
    ];
}

function get_flash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}
