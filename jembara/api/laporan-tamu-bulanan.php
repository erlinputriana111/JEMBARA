<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user']['id'])) { echo json_encode(['success'=>false]); exit; }

$tahun = (int) ($_GET['tahun'] ?? date('Y'));

try {
    $pdo  = getDB();
    $stmt = $pdo->prepare('
        SELECT
            MONTH(tanggal) AS bulan,
            COUNT(*) AS total,
            SUM(status = "selesai")   AS selesai,
            SUM(status = "diproses")  AS diproses,
            SUM(status = "menunggu")  AS menunggu,
            SUM(status = "ditolak")   AS ditolak
        FROM tamu
        WHERE YEAR(tanggal) = ?
        GROUP BY MONTH(tanggal)
        ORDER BY MONTH(tanggal)
    ');
    $stmt->execute([$tahun]);

    echo json_encode(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'data' => []]);
}
