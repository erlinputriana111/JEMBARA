<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../config/database.php";

try {
    $pdo = getDB();

    // Total tamu
    $totalTamu = $pdo->query("SELECT COUNT(*) FROM tamu")->fetchColumn();

    // Tamu hari ini
    $tamuHariIni = $pdo
        ->query(
            "
        SELECT COUNT(*) FROM tamu
        WHERE DATE(tanggal) = CURDATE()
    ",
        )
        ->fetchColumn();

    // Tamu bulan ini
    $tamuBulanIni = $pdo
        ->query(
            "
        SELECT COUNT(*) FROM tamu
        WHERE MONTH(tanggal) = MONTH(CURDATE())
        AND YEAR(tanggal) = YEAR(CURDATE())
    ",
        )
        ->fetchColumn();

    $totalSurat = $pdo
        ->query("SELECT COUNT(*) FROM surat_masuk")
        ->fetchColumn();
    $suratDiproses = $pdo
        ->query("SELECT COUNT(*) FROM surat_masuk WHERE status = 'diproses'")
        ->fetchColumn();
    $suratSelesai = $pdo
        ->query("SELECT COUNT(*) FROM surat_masuk WHERE status = 'selesai'")
        ->fetchColumn();
    // Aktivitas terbaru (10 tamu terakhir)
    $aktivitas = $pdo
        ->query(
            "
        SELECT nama, instansi, kepentingan, tujuan, tanggal
        FROM tamu
        ORDER BY tanggal DESC
        LIMIT 10
    ",
        )
        ->fetchAll();

    echo json_encode([
        "success" => true,
        "stats" => [
            "total_tamu" => (int) $totalTamu,
            "tamu_hari_ini" => (int) $tamuHariIni,
            "tamu_bulan_ini" => (int) $tamuBulanIni,
            "total_surat" => $totalSurat, // ✅
            "surat_diproses" => $suratDiproses, // ✅
            "surat_selesai" => $suratSelesai, // ✅
        ],
        "aktivitas" => $aktivitas,
    ]);
} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Gagal mengambil data.",
    ]);
}
