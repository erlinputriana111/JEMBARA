<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user']['id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$data   = json_decode(file_get_contents('php://input'), true);
$id     = (int) ($data['id']     ?? 0);
$status = trim($data['status']   ?? '');


try {
    $pdo  = getDB();
    $stmt = $pdo->prepare('UPDATE tamu SET status = ? WHERE id = ?');
    $stmt->execute([$status, $id]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Gagal update status.']);
}
