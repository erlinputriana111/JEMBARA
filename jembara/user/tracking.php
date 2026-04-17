<?php
$currentPage = "tracking";
$pageTitle = "Tracking";
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

<!-- ml-0 di mobile, ml-64 di desktop -->
<div id="mainContent" class="ml-0 md:ml-64 flex flex-col min-h-screen transition-all duration-300">

  <?php require_once "includes/topbar.php"; ?>

  <main class="flex-1 p-4 md:p-6 space-y-6">

    <div>
      <h1 class="text-xl font-bold text-gray-800">Tracking Laporan Tamu</h1>
      <p class="text-gray-400 text-xs mt-1">Daftar tamu yang Anda input beserta statusnya</p>
    </div>

    <!-- ===== TABLE CARD ===== -->
    <div class="bg-white rounded-xl shadow-sm p-4 md:p-5">

      <!-- Toolbar -->
      <div class="flex flex-col gap-3 mb-5">
        <h2 class="text-gray-800 font-semibold text-sm">Laporan Tamu</h2>
        <div class="flex flex-col sm:flex-row gap-2">
          <input id="filterTanggal" type="date"
            class="px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-400 transition w-full sm:w-auto"
            oninput="loadTracking()"/>
          <input id="searchInput" type="text" placeholder="🔍 Cari nama / instansi..."
            class="px-4 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-400 w-full sm:w-56 transition"
            oninput="loadTracking()"/>
        </div>
      </div>

      <!-- Desktop Table (hidden on mobile) -->
      <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="text-gray-400 text-xs border-b border-gray-100">
              <th class="text-left pb-3 font-medium">#</th>
              <th class="text-left pb-3 font-medium">Nama Tamu</th>
              <th class="text-left pb-3 font-medium">Instansi</th>
              <th class="text-left pb-3 font-medium">Kepentingan</th>
              <th class="text-left pb-3 font-medium">Tujuan</th>  <!-- ✅ -->
              
              <th class="text-left pb-3 font-medium">Status</th>
              <th class="text-left pb-3 font-medium">Tanggal</th>
            </tr>
          </thead>
          <tbody id="trackingBody" class="divide-y divide-gray-50 text-gray-700">
            <tr>
              <td colspan="7" class="py-10 text-center text-gray-400 text-xs">Memuat data...</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Mobile Card List (hidden on desktop) -->
      <div class="md:hidden space-y-3" id="trackingCards">
        <div class="py-10 text-center text-gray-400 text-xs">Memuat data...</div>
      </div>

      <div class="mt-4 text-xs text-gray-400" id="paginationInfo"></div>

    </div>

  </main>

  <?php require_once "includes/footer.php"; ?>

</div>

<!-- Lightbox -->
<div id="lightbox" class="hidden fixed inset-0 bg-black/70 z-50 flex items-center justify-center" onclick="closeLightbox()">
  <button class="absolute top-4 right-4 bg-white w-9 h-9 rounded-full text-gray-800 font-bold text-lg flex items-center justify-center hover:bg-gray-100">✕</button>
  <img id="lightboxImg" src="" class="max-w-[90vw] max-h-[85vh] rounded-xl shadow-2xl"/>
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

  function statusBadge(status) {
    const map = {
      menunggu : 'bg-yellow-50 text-yellow-700',
      diproses : 'bg-blue-50 text-blue-700',
      selesai  : 'bg-green-50 text-green-700',
      ditolak  : 'bg-red-50 text-red-700',
    };
    const label = {
      menunggu : '🕐 Menunggu',
      diproses : '⚙️ Diproses',
      selesai  : '✅ Selesai',
      ditolak  : '❌ Ditolak',
    };
    const cls = map[status] ?? 'bg-gray-100 text-gray-500';
    return `<span class="text-xs font-semibold px-2.5 py-1 rounded-full ${cls}">${label[status] ?? status}</span>`;
  }

  async function loadTracking() {
    const search  = document.getElementById('searchInput').value;
    const tanggal = document.getElementById('filterTanggal').value;
    const tbody   = document.getElementById('trackingBody');
    const cards   = document.getElementById('trackingCards');

    const emptyMsg = search || tanggal ? 'Data tidak ditemukan.' : 'Belum ada data tamu.';

    try {
      const params = new URLSearchParams({ search, tanggal });
      const res    = await fetch(`/api/get-tamu-user.php?${params}`);
      const json   = await res.json();

      if (!json.data.length) {
        tbody.innerHTML = `<tr><td colspan="7" class="py-10 text-center text-gray-400 text-xs">${emptyMsg}</td></tr>`;
        cards.innerHTML = `<div class="py-10 text-center text-gray-400 text-xs">${emptyMsg}</div>`;
        document.getElementById('paginationInfo').textContent = '';
        return;
      }

      document.getElementById('paginationInfo').textContent = `Menampilkan ${json.data.length} data`;

      // ===== DESKTOP TABLE =====
      tbody.innerHTML = json.data.map((row, i) => `
        <tr class="hover:bg-gray-50 transition">
          <td class="py-3 text-gray-400 text-xs">${i + 1}</td>
          <td class="py-3 font-medium text-gray-800">${row.nama}</td>
          <td class="py-3 text-gray-500 text-xs">${row.instansi}</td>
          <td class="py-3">
            <span class="bg-indigo-50 text-indigo-700 text-xs font-semibold px-2.5 py-1 rounded-full">
              ${row.kepentingan}
            </span>
          </td>
          <td class="py-3 text-gray-500 text-xs">${row.tujuan ?? '—'}</td>  <!-- ✅ -->
          <td class="py-3">${statusBadge(row.status)}</td>
          <td class="py-3 text-gray-400 text-xs whitespace-nowrap">${formatDate(row.tanggal)}</td>
        </tr>
      `).join('');

      // ===== MOBILE CARDS =====
      cards.innerHTML = json.data.map((row, i) => `
        <div class="border border-gray-100 rounded-xl p-4 space-y-3 hover:bg-gray-50 transition">

          <!-- Header: nomor + status -->
          <div class="flex items-center justify-between">
            <span class="text-gray-400 text-xs font-medium">#${i + 1}</span>
            ${statusBadge(row.status)}
          </div>

          <!-- Nama & Instansi -->
          <div>
            <p class="font-semibold text-gray-800 text-sm">${row.nama}</p>
            <p class="text-gray-400 text-xs mt-0.5">${row.instansi}</p>
          </div>

          
          <!-- ✅ Tujuan -->
          ${row.tujuan ? `
          <div class="flex items-center gap-1.5 text-xs text-gray-500">
            <i data-lucide="map-pin" class="w-3 h-3 text-gray-300 shrink-0"></i>
            <span>${row.tujuan}</span>
          </div>` : ''}
          
          <!-- Kepentingan & Tanggal -->
          <div class="flex items-center justify-between">
            <span class="bg-indigo-50 text-indigo-700 text-xs font-semibold px-2.5 py-1 rounded-full">
              ${row.kepentingan}
            </span>
            <span class="text-gray-400 text-xs">${formatDate(row.tanggal)}</span>
          </div>

        </div>
      `).join('');

    } catch (err) {
      tbody.innerHTML = `<tr><td colspan="7" class="py-10 text-center text-red-400 text-xs">Gagal memuat data.</td></tr>`;
      cards.innerHTML = `<div class="py-10 text-center text-red-400 text-xs">Gagal memuat data.</div>`;
    }
  }

  function openLightbox(src) {
    document.getElementById('lightboxImg').src = src;
    document.getElementById('lightbox').classList.remove('hidden');
  }
  function closeLightbox() {
    document.getElementById('lightbox').classList.add('hidden');
  }

  loadTracking();
  setInterval(loadTracking, 5000);
</script>
</body>
</html>
