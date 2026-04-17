<?php
session_start();
require_once "../config/database.php";
header("Content-Type: application/json");

if (!isset($_SESSION["user"]["id"])) {
    echo json_encode(["success" => false, "message" => "Unauthorized."]);
    exit();
}

try {
    $pdo = getDB();
    $search = trim($_GET["search"] ?? "");
    $tanggal = trim($_GET["tanggal"] ?? "");
    $status = trim($_GET["status"] ?? "");
    $page = max(1, (int) ($_GET["page"] ?? 1));
    $perPage = max(1, (int) ($_GET["per_page"] ?? 10));
    $offset = ($page - 1) * $perPage;

    // ✅ Tambahkan 2 status baru di sini
    $allowed_status = [
        "menunggu",
        "diproses",
        "selesai",
        "ditolak",
        "masuk ke kepemimpinan",
        "masuk ke bagian-bagian",
    ];

    $sql = '
        SELECT
            t.id, t.nama, t.instansi, t.kepentingan, t.tujuan,
            t.tanggal, t.foto, t.status, t.updated_at,
            t.file_pendukung,
            u.nama     AS staff_nama,
            u.username AS staff_username
        FROM tamu t
        LEFT JOIN users u ON u.id = t.created_by
        WHERE 1=1
    ';

    $params = [];

    if ($search) {
        $sql .= " AND (t.nama LIKE ? OR t.instansi LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    if ($tanggal) {
        $sql .= " AND DATE(t.tanggal) = ?";
        $params[] = $tanggal;
    }

    if ($status && in_array($status, $allowed_status)) {
        $sql .= " AND t.status = ?";
        $params[] = $status;
    }

    // Count total untuk pagination
    $countSql =
        "SELECT COUNT(*) FROM tamu t LEFT JOIN users u ON u.id = t.created_by WHERE 1=1";
    $countParams = [];

    if ($search) {
        $countSql .= " AND (t.nama LIKE ? OR t.instansi LIKE ?)";
        $countParams[] = "%$search%";
        $countParams[] = "%$search%";
    }
    if ($tanggal) {
        $countSql .= " AND DATE(t.tanggal) = ?";
        $countParams[] = $tanggal;
    }
    // ✅ Pakai $allowed_status yang sudah diupdate — konsisten
    if ($status && in_array($status, $allowed_status)) {
        $countSql .= " AND t.status = ?";
        $countParams[] = $status;
    }

    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($countParams);
    $total = (int) $countStmt->fetchColumn();

    // Stats cards
    $statsTotal = $pdo->query("SELECT COUNT(*) FROM tamu")->fetchColumn();
    $statsHariIni = $pdo
        ->query("SELECT COUNT(*) FROM tamu WHERE DATE(tanggal) = CURDATE()")
        ->fetchColumn();
    $statsBulanIni = $pdo
        ->query(
            "SELECT COUNT(*) FROM tamu WHERE MONTH(tanggal) = MONTH(NOW()) AND YEAR(tanggal) = YEAR(NOW())",
        )
        ->fetchColumn();

    $sql .= " ORDER BY t.tanggal DESC LIMIT ? OFFSET ?";
    $params[] = $perPage;
    $params[] = $offset;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    echo json_encode([
        "success" => true,
        "total" => $total,
        "data" => $stmt->fetchAll(PDO::FETCH_ASSOC),
        "stats" => [
            "total" => $statsTotal,
            "hari_ini" => $statsHariIni,
            "bulan_ini" => $statsBulanIni,
        ],
    ]);
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode([
        "success" => false,
        "message" => "Gagal mengambil data: " . $e->getMessage(),
    ]);
}
