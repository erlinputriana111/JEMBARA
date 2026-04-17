<?php
session_start(); // MUST be first line, before any output
require_once '../config/database.php';
header('Content-Type: application/json');

// ── Validate input ────────────────────────────────────────────
$nama        = trim($_POST['nama']        ?? '');
$instansi    = trim($_POST['instansi']    ?? '');
$kepentingan = trim($_POST['kepentingan'] ?? '');
$tujuan      = trim($_POST['tujuan']      ?? '');
$tanggal     = $_POST['tanggal']          ?? null;
$foto        = $_POST['foto']             ?? null;

// created_by is optional — null if accessed from public form (no session)
$createdBy = $_SESSION['user']['id'] ?? null;

if (!$nama || !$instansi || !$kepentingan || !$tujuan) {
    echo json_encode(['success' => false, 'message' => 'Field tidak lengkap.']);
    exit;
}

// ── Handle file pendukung ─────────────────────────────────────
$filePath = null;

if (isset($_FILES['file_pendukung']) && $_FILES['file_pendukung']['error'] === UPLOAD_ERR_OK) {
    $file    = $_FILES['file_pendukung'];
    $maxSize = 5 * 1024 * 1024;
    $allowed = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
    $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if ($file['size'] > $maxSize) {
        echo json_encode(['success' => false, 'message' => 'File maksimal 5MB.']);
        exit;
    }

    if (!in_array($ext, $allowed)) {
        echo json_encode(['success' => false, 'message' => 'Tipe file tidak diizinkan.']);
        exit;
    }

    $uploadDir = __DIR__ . '/../uploads/tamu/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $newName  = uniqid('doc_', true) . '.' . $ext;
    $destPath = $uploadDir . $newName;

    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        echo json_encode(['success' => false, 'message' => 'Gagal menyimpan file ke server.']);
        exit;
    }

    $filePath = '/uploads/tamu/' . $newName;
}

// ── Insert to DB ──────────────────────────────────────────────
try {
    $pdo  = getDB();
    $stmt = $pdo->prepare('
        INSERT INTO tamu (nama, instansi, kepentingan, tujuan, status, tanggal, foto, file_pendukung, created_by)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ');
    $stmt->execute([$nama, $instansi, $kepentingan, $tujuan, 'menunggu', $tanggal, $foto, $filePath, $createdBy]);

    echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);

} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Gagal menyimpan data.']);
}