<?php
$currentPage = "dashboard";
$pageTitle = "Dashboard";
require_once 'includes/auth_check.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= $pageTitle ?> — DPRD Kabupaten Jember</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-100 font-sans">

<?php require_once "includes/sidebar.php"; ?>

<div id="mainContent" class="ml-64 flex flex-col min-h-screen transition-all duration-300">

  <?php require_once "includes/topbar.php"; ?>

  <main class="flex-1 p-6 space-y-6">

    <!-- ===== STATS CARDS ===== -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">

      <!-- Total Tamu (live) -->
      <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4 border-l-4 border-indigo-500">
        <div class="bg-indigo-100 p-3 rounded-lg">
          <i data-lucide="contact" class="w-6 h-6 text-indigo-600"></i>
        </div>
        <div>
          <p class="text-gray-400 text-xs font-medium">Total Tamu</p>
          <p class="text-gray-800 text-2xl font-bold" id="statTotalTamu">—</p>
        </div>
      </div>

      <!-- Tamu Hari Ini (live) -->
      <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4 border-l-4 border-green-500">
        <div class="bg-green-100 p-3 rounded-lg">
          <i data-lucide="user-check" class="w-6 h-6 text-green-600"></i>
        </div>
        <div>
          <p class="text-gray-400 text-xs font-medium">Tamu Hari Ini</p>
          <p class="text-gray-800 text-2xl font-bold" id="statTamuHariIni">—</p>
        </div>
      </div>

      <!-- Tamu Bulan Ini (live) -->
      <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4 border-l-4 border-blue-500">
        <div class="bg-blue-100 p-3 rounded-lg">
          <i data-lucide="calendar-check" class="w-6 h-6 text-blue-600"></i>
        </div>
        <div>
          <p class="text-gray-400 text-xs font-medium">Tamu Bulan Ini</p>
          <p class="text-gray-800 text-2xl font-bold" id="statTamuBulanIni">—</p>
        </div>
      </div>

    </div>

    <!-- ===== MIDDLE ROW ===== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

      <!-- Aktivitas Tamu Terbaru (live) -->
      <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-gray-800 font-semibold text-sm">Aktivitas Tamu Terbaru</h2>
          <a href="/admin/tamu.php" class="text-green-600 text-xs hover:underline">Lihat semua</a>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="text-gray-400 text-xs border-b border-gray-100">
                <th class="text-left pb-3 font-medium">Nama</th>
                <th class="text-left pb-3 font-medium">Instansi</th>
                <th class="text-left pb-3 font-medium">Kepentingan</th>
                <th class="text-left pb-3 font-medium">Tanggal</th>
              </tr>
            </thead>
            <tbody id="aktivitasTamu" class="divide-y divide-gray-50 text-gray-700">
              <tr>
                <td colspan="4" class="py-6 text-center text-gray-400 text-xs">Memuat data...</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>


    </div>


  </main>

  <?php require_once "includes/footer.php"; ?>

</div>

<script src="/assets/app.js"></script>
<script>
  // ===== LOAD STATS & AKTIVITAS FROM DB =====
  async function loadStats() {
    try {
      const res  = await fetch('/api/get-stats.php');
      const json = await res.json();

      if (!json.success) return;

      // Update stat cards
      document.getElementById('statTotalTamu').textContent    = json.stats.total_tamu;
      document.getElementById('statTamuHariIni').textContent  = json.stats.tamu_hari_ini;
      document.getElementById('statTamuBulanIni').textContent = json.stats.tamu_bulan_ini;

      // Render aktivitas table
      const tbody = document.getElementById('aktivitasTamu');

      if (!json.aktivitas.length) {
        tbody.innerHTML = `<tr><td colspan="4" class="py-6 text-center text-gray-400 text-xs">Belum ada data tamu.</td></tr>`;
        return;
      }

      tbody.innerHTML = json.aktivitas.map(row => `
        <tr class="hover:bg-gray-50 transition">
          <td class="py-3 font-medium text-gray-800">${row.nama}</td>
          <td class="py-3 text-gray-500">${row.instansi}</td>
          <td class="py-3">
            <span class="bg-indigo-50 text-indigo-700 text-xs font-semibold px-2.5 py-1 rounded-full">
              ${row.kepentingan}
            </span>
          </td>
          <td class="py-3 text-gray-400 text-xs whitespace-nowrap">${formatDate(row.tanggal)}</td>
        </tr>
      `).join('');

    } catch (err) {
      console.error('Gagal memuat stats:', err);
    }
  }

  function formatDate(dateStr) {
    if (!dateStr) return '—';
    return new Date(dateStr).toLocaleDateString('id-ID', {
      day: 'numeric', month: 'short', year: 'numeric',
      hour: '2-digit', minute: '2-digit'
    });
  }

  loadStats();
</script>

</body>
</html>
