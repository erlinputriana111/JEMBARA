<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user']['id'])) { echo json_encode(['success'=>false]); exit; }

try {
    $pdo  = getDB();
    $stmt = $pdo->query('SELECT id, nama, instansi FROM tamu ORDER BY nama ASC');
    echo json_encode(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'data' => []]);
}
