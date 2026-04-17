<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user']['id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$pdo     = getDB();
$userId  = $_SESSION['user']['id'];
$search  = trim($_GET['search']  ?? '');
$tanggal = trim($_GET['tanggal'] ?? '');

$sql = '
    SELECT
        t.id, t.nama, t.instansi, t.kepentingan, t.tanggal, t.foto, t.status, t.tujuan,
        u.nama     AS staff_nama,
        u.username AS staff_username
    FROM tamu t
    LEFT JOIN users u ON u.id = t.created_by
    WHERE t.created_by = ?
';

$params = [$userId];

if ($search) {
    $sql .= ' AND (t.nama LIKE ? OR t.instansi LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($tanggal) {
    $sql .= ' AND DATE(t.tanggal) = ?';
    $params[] = $tanggal;
}

$sql .= ' ORDER BY t.tanggal DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

echo json_encode(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
