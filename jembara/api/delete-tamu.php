<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$id = (int) ($_GET['id'] ?? 0);

if (!$id) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'ID tidak valid.']);
    exit;
}

try {
    $pdo  = getDB();
    $stmt = $pdo->prepare("DELETE FROM tamu WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode(['success' => true, 'message' => 'Data berhasil dihapus.']);
} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Gagal menghapus data.']);
}
