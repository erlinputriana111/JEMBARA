<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

$nama       = trim($_POST['nama'] ?? '');
$username   = trim($_POST['username'] ?? '');
$email      = trim($_POST['email'] ?? '');
$password   = $_POST['password'] ?? '';
$confirm    = $_POST['password_confirmation'] ?? '';

$_SESSION['old'] = [
    'nama'     => $nama,
    'username' => $username,
    'email'    => $email,
];

if (!$nama || !$username || !$password || !$confirm) {
    $_SESSION['error'] = 'Semua field wajib diisi (kecuali email).';
    header('Location: register.php');
    exit;
}

if (strlen($password) < 6) {
    $_SESSION['error'] = 'Password minimal 6 karakter.';
    header('Location: register.php');
    exit;
}

if ($password !== $confirm) {
    $_SESSION['error'] = 'Konfirmasi password tidak sesuai.';
    header('Location: register.php');
    exit;
}

if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) { 
    $_SESSION['error'] = 'Format email tidak valid.';
    header('Location: register.php');
    exit;
}

try {
    $pdo = getDB();

    // Cek username sudah dipakai atau belum
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        $_SESSION['error'] = 'Username sudah digunakan, pilih yang lain.';
        header('Location: register.php');
        exit;
    }

    // Hash password
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare('
        INSERT INTO users (nama, username, email, password, role)
        VALUES (?, ?, ?, ?, ?)
    ');
    $stmt->execute([$nama, $username, $email ?: null, $hashed, 'admin']);

    // Optional: langsung login setelah registrasi
    $userId = $pdo->lastInsertId();
    $_SESSION['user'] = [
        'id'       => $userId,
        'nama'     => $nama,
        'username' => $username,
        'role'     => 'admin',
    ];
    unset($_SESSION['old']);

    header('Location: dashboard.php');
    exit;

} catch (PDOException $e) {
    error_log($e->getMessage());
    $_SESSION['error'] = 'DB Error: ' . $e->getMessage(); // ← tampilkan pesan asli
    // $_SESSION['error'] = 'Terjadi kesalahan saat menyimpan data.';
    header('Location: register.php');
    exit;
}
