<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user']['id'])) { echo json_encode(['success'=>false]); exit; }

try {
    $pdo     = getDB();
    $search  = trim($_GET['search']  ?? '');
    $tanggal = trim($_GET['tanggal'] ?? '');
    $status  = trim($_GET['status']  ?? '');

    $sql    = 'SELECT s.*, t.nama AS nama_tamu FROM surat_masuk s LEFT JOIN tamu t ON t.id = s.tamu_id WHERE 1=1';
    $params = [];

    if ($search)  { $sql .= ' AND (s.pengirim LIKE ? OR s.perihal LIKE ?)'; $params[] = "%$search%"; $params[] = "%$search%"; }
    if ($tanggal) { $sql .= ' AND DATE(s.tanggal_diterima) = ?'; $params[] = $tanggal; }
    if ($status)  { $sql .= ' AND s.status = ?'; $params[] = $status; }

    $sql .= ' ORDER BY s.tanggal_diterima DESC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    echo json_encode(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'data' => [], 'message' => $e->getMessage()]);
}
