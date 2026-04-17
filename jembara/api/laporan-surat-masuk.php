<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user']['id'])) {
    echo json_encode(['success' => false, 'data' => []]);
    exit;
}

$dari   = $_GET['dari']   ?? '';
$sampai = $_GET['sampai'] ?? '';

try {
    $pdo    = getDB();
    $sql    = '
        SELECT
            pengirim,
            perihal,
            tanggal_surat    AS tanggal,
            tanggal_diterima,
            status,
            posisi
        FROM surat_masuk
        WHERE 1=1
    ';
    $params = [];

    if ($dari)   { $sql .= ' AND DATE(tanggal_surat) >= ?'; $params[] = $dari; }
    if ($sampai) { $sql .= ' AND DATE(tanggal_surat) <= ?'; $params[] = $sampai; }

    $sql .= ' ORDER BY tanggal_surat DESC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    echo json_encode(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);

} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'data' => [], 'message' => $e->getMessage()]);
}