<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user']['id'])) {
    echo json_encode(['success' => false, 'data' => []]);
    exit;
}

$q = trim($_GET['q'] ?? '');

if (!$q) {
    echo json_encode(['success' => false, 'data' => [], 'message' => 'Kata kunci tidak boleh kosong.']);
    exit;
}

try {
    $pdo  = getDB();
    $like = "%$q%";

    $stmt = $pdo->prepare("
        SELECT
            s.id,
            s.pengirim,
            s.perihal,
            s.tanggal_surat,
            s.tanggal_diterima,
            s.status,
            s.posisi,
            s.file_path,
            t.nama AS nama_tamu
        FROM surat_masuk s
        LEFT JOIN tamu t ON t.id = s.tamu_id
        WHERE
            s.pengirim        LIKE :q1
            OR s.perihal      LIKE :q2
            OR t.nama         LIKE :q3
            OR t.instansi     LIKE :q4
        ORDER BY s.tanggal_diterima DESC
    ");

    $stmt->execute([
        ':q1' => $like,
        ':q2' => $like,
        ':q3' => $like,
        ':q4' => $like,
    ]);

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $data, 'total' => count($data)]);

} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'data' => [], 'message' => 'Terjadi kesalahan server.']);
}