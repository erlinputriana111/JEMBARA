<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user']['id'])) { echo json_encode(['success'=>false]); exit; }

$dari   = $_GET['dari']   ?? '';
$sampai = $_GET['sampai'] ?? '';

try {
    $pdo  = getDB();
    $sql  = 'SELECT t.nama, t.instansi, t.kepentingan, t.tujuan, t.tanggal, t.status
             FROM tamu t WHERE 1=1';
    $params = [];

    if ($dari)   { $sql .= ' AND DATE(t.tanggal) >= ?'; $params[] = $dari; }
    if ($sampai) { $sql .= ' AND DATE(t.tanggal) <= ?'; $params[] = $sampai; }

    $sql .= ' ORDER BY t.tanggal DESC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    echo json_encode(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'data' => []]);
}
