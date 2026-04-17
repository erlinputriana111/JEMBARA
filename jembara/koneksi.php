<?php
session_start();

$host   = 'localhost';
$db     = 'sijember';
$user   = 'root';
$pass   = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('Koneksi database gagal: ' . $e->getMessage());
}

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

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
$stmt->execute([$username]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    $_SESSION['error'] = 'Username atau password salah.';
    header('Location: login.php');
    exit;
}

// Set session
$_SESSION['user'] = [
    'id'       => $user['id'],
    'username' => $user['username'],
    'nama'     => $user['nama'],
    'role'     => $user['role'],
];

header('Location: dashboard.php');
exit;
