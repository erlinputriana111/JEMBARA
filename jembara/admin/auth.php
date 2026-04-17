<?php
session_start();

require_once   '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($username) || empty($password)) {
    $_SESSION['error'] = 'Username dan password wajib diisi.';
    header('Location: login.php');
    exit;
}

$pdo  = getDB();
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
$stmt->execute([$username]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    $_SESSION['error'] = 'Username atau password salah.';
    header('Location: login.php');
    exit;
}
// ✅ Cek role — hanya 'staff' yang boleh login di sini
if ($user['role'] !== 'admin') {
    $_SESSION['error'] = 'Akses ditolak. Halaman ini hanya untuk admin.';
    header('Location: login.php');
    exit;
}

$_SESSION['user'] = [
    'id'       => $user['id'],
    'username' => $user['username'],
    'nama'     => $user['nama'],
    'role'     => $user['role'],
];

header('Location: dashboard.php');
exit;
