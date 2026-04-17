<?php
session_start();

require_once __DIR__ . '/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($username) || empty($password)) {
    $_SESSION['error'] = 'Username dan password wajib diisi.';
    header('Location: index.php');
    exit;
}

$pdo  = getDB();
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
$stmt->execute([$username]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    $_SESSION['error'] = 'Username atau password salah.';
    header('Location: index.php');
    exit;
}

// ✅ Cek role — hanya 'staff' yang boleh login di sini
if ($user['role'] !== 'staff') {
    $_SESSION['error'] = 'Akses ditolak. Halaman ini hanya untuk pengguna.';
    header('Location: index.php');
    exit;
}

$_SESSION['user'] = [
    'id'       => $user['id'],
    'username' => $user['username'],
    'nama'     => $user['nama'],
    'role'     => $user['role'],
];

header('Location: user/tracking.php');
exit;
