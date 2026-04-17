<?php
session_start(); // MUST be first line
require_once '../config/database.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input  = json_decode(file_get_contents('php://input'), true);
$id     = isset($input['id'])     ? (int) trim($input['id'])     : 0;
$status = isset($input['status']) ? trim($input['status'])       : '';
$posisi = isset($input['posisi']) ? trim($input['posisi'])       : null;

$allowedStatus = ['frontdesk', 'agendaris', 'setwan', 'pimpinan dprd', 'komisi', 'selesai'];

// ── Update DB ─────────────────────────────────────────────────
try {
    $pdo  = getDB(); // ← was missing, $pdo was never defined
    $stmt = $pdo->prepare("
        UPDATE surat_masuk
           SET status     = :status,
               posisi     = :posisi
         WHERE id = :id
    ");
    $stmt->execute([
        ':status' => $status,
        ':posisi' => $posisi ?: null,
        ':id'     => $id,
    ]);

    if ($stmt->rowCount() === 0) {
        echo json_encode(['success' => false, 'message' => 'Surat tidak ditemukan']);
        exit;
    }

    echo json_encode(['success' => true, 'message' => 'Status berhasil diperbarui']);

} catch (PDOException $e) {
    error_log($e->getMessage()); // log detail, don't expose to client
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan server']);
}