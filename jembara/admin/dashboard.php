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
  <link rel="apple-touch-icon" sizes="180x180" href="/assets/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="/assets/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/assets/favicon-16x16.png">
  <link rel="manifest" href="/assets/site.webmanifest">
</head>
<body class="bg-gray-100 font-sans">

<?php require_once "includes/sidebar.php"; ?>

<div id="mainContent" class="ml-0 md:ml-64 flex flex-col min-h-screen transition-all duration-300">

  <?php require_once "includes/topbar.php"; ?>

  <main class="flex-1 p-4 md:p-6 space-y-5">

      <!-- ===== STATS CARDS ===== -->
      <div class="grid grid-cols-2 md:grid-cols-3 gap-3 md:gap-5">
      
        <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-3 border-l-4 border-indigo-500">
          <div class="bg-indigo-100 p-2.5 rounded-lg shrink-0">
            <i data-lucide="contact" class="w-5 h-5 text-indigo-600"></i>
          </div>
          <div class="min-w-0">
            <p class="text-gray-400 text-xs font-medium truncate">Total Tamu</p>
            <p class="text-gray-800 text-xl font-bold" id="statTotalTamu">—</p>
          </div>
        </div>
      
        <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-3 border-l-4 border-green-500">
          <div class="bg-green-100 p-2.5 rounded-lg shrink-0">
            <i data-lucide="user-check" class="w-5 h-5 text-green-600"></i>
          </div>
          <div class="min-w-0">
            <p class="text-gray-400 text-xs font-medium truncate">Hari Ini</p>
            <p class="text-gray-800 text-xl font-bold" id="statTamuHariIni">—</p>
          </div>
        </div>
      
        <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-3 border-l-4 border-blue-500 col-span-2 md:col-span-1">
          <div class="bg-blue-100 p-2.5 rounded-lg shrink-0">
            <i data-lucide="calendar-check" class="w-5 h-5 text-blue-600"></i>
          </div>
          <div class="min-w-0">
            <p class="text-gray-400 text-xs font-medium truncate">Bulan Ini</p>
            <p class="text-gray-800 text-xl font-bold" id="statTamuBulanIni">—</p>
          </div>
        </div>
      
        <!-- ✅ 3 card baru -->
        <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-3 border-l-4 border-red-800">
          <div class="bg-red-100 p-2.5 rounded-lg shrink-0">
            <i data-lucide="mail" class="w-5 h-5 text-red-800"></i>
          </div>
          <div class="min-w-0">
            <p class="text-gray-400 text-xs font-medium truncate">Total Surat Masuk</p>
            <p class="text-gray-800 text-xl font-bold" id="statTotalSurat">—</p>
          </div>
        </div>
      
        <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-3 border-l-4 border-orange-400">
          <div class="bg-orange-100 p-2.5 rounded-lg shrink-0">
            <i data-lucide="mail-open" class="w-5 h-5 text-orange-500"></i>
          </div>
          <div class="min-w-0">
            <p class="text-gray-400 text-xs font-medium truncate">Surat Diproses</p>
            <p class="text-gray-800 text-xl font-bold" id="statSuratDiproses">—</p>
          </div>
        </div>
      
        <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-3 border-l-4 border-teal-500 col-span-2 md:col-span-1">
          <div class="bg-teal-100 p-2.5 rounded-lg shrink-0">
            <i data-lucide="mail-check" class="w-5 h-5 text-teal-600"></i>
          </div>
          <div class="min-w-0">
            <p class="text-gray-400 text-xs font-medium truncate">Surat Selesai</p>
            <p class="text-gray-800 text-xl font-bold" id="statSuratSelesai">—</p>
          </div>
        </div>
      
      </div>


    <!-- ===== AKTIVITAS TAMU ===== -->
    <div class="bg-white rounded-xl shadow-sm p-4 md:p-5">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-gray-800 font-semibold text-sm">Aktivitas Tamu Terbaru</h2>
        <a href="/admin/data_tamu.php" class="text-indigo-600 text-xs hover:underline">Lihat semua</a>
      </div>

      <!-- Desktop Table -->
      <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="text-gray-400 text-xs border-b border-gray-100">
              <th class="text-left pb-3 font-medium">Nama</th>
              <th class="text-left pb-3 font-medium">Instansi</th>
              <th class="text-left pb-3 font-medium">Kepentingan</th>
              <th class="text-left pb-3 font-medium">Tujuan</th>
              <th class="text-left pb-3 font-medium">Tanggal</th>
            </tr>
          </thead>
          <tbody id="aktivitasTamu" class="divide-y divide-gray-50 text-gray-700">
            <tr>
              <td colspan="6" class="py-6 text-center text-gray-400 text-xs">Memuat data...</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Mobile Card List -->
      <div class="md:hidden space-y-3" id="aktivitasCards">
        <div class="py-6 text-center text-gray-400 text-xs">Memuat data...</div>
      </div>

    </div>

  </main>

  <?php require_once "includes/footer.php"; ?>

</div>

<script src="/assets/app.js"></script>
<script>
  function formatDate(dateStr) {
    if (!dateStr) return '—';
    return new Date(dateStr).toLocaleDateString('id-ID', {
      day: 'numeric', month: 'short', year: 'numeric',
      hour: '2-digit', minute: '2-digit'
    });
  }

  async function loadStats() {
    try {
      const res  = await fetch('/api/get-stats.php');
      const json = await res.json();
      if (!json.success) return;

      document.getElementById('statTotalTamu').textContent    = json.stats.total_tamu;
      document.getElementById('statTamuHariIni').textContent  = json.stats.tamu_hari_ini;
      document.getElementById('statTamuBulanIni').textContent = json.stats.tamu_bulan_ini;


      document.getElementById('statTotalTamu').textContent    = json.stats.total_tamu;
      document.getElementById('statTamuHariIni').textContent  = json.stats.tamu_hari_ini;
      document.getElementById('statTamuBulanIni').textContent = json.stats.tamu_bulan_ini;
      document.getElementById('statTotalSurat').textContent   = json.stats.total_surat   ?? '—';  // ✅
      document.getElementById('statSuratDiproses').textContent = json.stats.surat_diproses ?? '—'; // ✅
      document.getElementById('statSuratSelesai').textContent  = json.stats.surat_selesai  ?? '—'; // ✅

      
      const tbody = document.getElementById('aktivitasTamu');
      const cards = document.getElementById('aktivitasCards');

      if (!json.aktivitas.length) {
        tbody.innerHTML = `<tr><td colspan="6" class="py-6 text-center text-gray-400 text-xs">Belum ada data tamu.</td></tr>`;
        cards.innerHTML = `<div class="py-6 text-center text-gray-400 text-xs">Belum ada data tamu.</div>`;
        return;
      }

      // ===== DESKTOP TABLE =====
      tbody.innerHTML = json.aktivitas.map(row => `
        <tr class="hover:bg-gray-50 transition">
          <td class="py-3 font-medium text-gray-800">${row.nama}</td>
          <td class="py-3 text-gray-500 text-xs">${row.instansi}</td>
          <td class="py-3">
            <span class="bg-indigo-50 text-indigo-700 text-xs font-semibold px-2.5 py-1 rounded-full">
              ${row.kepentingan}
            </span>
          </td>
          <td class="py-3 font-medium text-gray-800">${row.tujuan ?? '-'}</td>
          <td class="py-3 text-gray-400 text-xs whitespace-nowrap">${formatDate(row.tanggal)}</td>
        </tr>
      `).join('');

      // ===== MOBILE CARDS =====
      cards.innerHTML = json.aktivitas.map(row => `
        <div class="border border-gray-100 rounded-xl p-4 space-y-3 hover:bg-gray-50 transition">

          <!-- Nama & Instansi -->
          <div>
            <p class="font-semibold text-gray-800 text-sm">${row.nama}</p>
            <p class="text-gray-400 text-xs mt-0.5">${row.instansi}</p>
          </div>

          <!-- Kepentingan & Tanggal -->
          <div class="flex items-center justify-between flex-wrap gap-2">
            <span class="bg-indigo-50 text-indigo-700 text-xs font-semibold px-2.5 py-1 rounded-full">
              ${row.kepentingan}
            </span>
            <span class="text-gray-400 text-xs">${formatDate(row.tanggal)}</span>
          </div>

         
        </div>
      `).join('');

      lucide.createIcons();

    } catch (err) {
      console.error('Gagal memuat stats:', err);
    }
  }

  loadStats();
  setInterval(loadStats, 5000);
</script>

</body>
</html>
