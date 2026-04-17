<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user']['id'])) { echo json_encode(['success'=>false,'message'=>'Unauthorized.']); exit; }

$pengirim        = trim($_POST['pengirim']         ?? '');
$perihal         = trim($_POST['perihal']          ?? '');
$tanggalSurat    = trim($_POST['tanggal_surat']    ?? '');
$tanggalDiterima = trim($_POST['tanggal_diterima'] ?? '');
$status          = trim($_POST['status']           ?? 'frontdesk');
$posisi          = trim($_POST['posisi']           ?? 'Frontdesk');
$tamuId          = $_POST['tamu_id'] ?? null;
$createdBy       = $_SESSION['user']['id'];

if (!$pengirim || !$perihal || !$tanggalSurat || !$tanggalDiterima) {
    echo json_encode(['success' => false, 'message' => 'Field tidak lengkap.']); exit;
}

$filePath = null;
if (isset($_FILES['file_surat']) && $_FILES['file_surat']['error'] === UPLOAD_ERR_OK) {
    $file    = $_FILES['file_surat'];
    $allowed = ['pdf','jpg','jpeg','png','doc','docx'];
    $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($file['size'] > 10*1024*1024) { echo json_encode(['success'=>false,'message'=>'File maks 10MB.']); exit; }
    if (!in_array($ext, $allowed))    { echo json_encode(['success'=>false,'message'=>'Tipe file tidak diizinkan.']); exit; }

    $uploadDir = __DIR__ . '/../uploads/surat/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    $newName  = uniqid('surat_', true) . '.' . $ext;
    move_uploaded_file($file['tmp_name'], $uploadDir . $newName);
    $filePath = '/uploads/surat/' . $newName;
}

try {
    $pdo  = getDB();
    $stmt = $pdo->prepare('
        INSERT INTO surat_masuk (tamu_id, pengirim, perihal, tanggal_surat, tanggal_diterima, status, posisi, file_path, created_by)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ');
    $stmt->execute([$tamuId ?: null, $pengirim, $perihal, $tanggalSurat, $tanggalDiterima, $status, $posisi, $filePath, $createdBy]);
    echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
