<?php
$currentPage = "data_tamu";
$pageTitle = "Data Tamu";
require_once "includes/auth_check.php";
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

  <main class="flex-1 p-4 md:p-6 space-y-4">

    <div>
      <h2 class="text-lg font-bold text-gray-800">Data Tamu</h2>
      <p class="text-gray-400 text-xs mt-0.5">Daftar seluruh tamu yang telah berkunjung</p>
    </div>

    <!-- ===== STAT CARDS ===== -->
    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
      <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-3 border-l-4 border-red-700">
        <div class="bg-red-100 p-2.5 rounded-lg shrink-0"><i data-lucide="users" class="w-5 h-5 text-red-700"></i></div>
        <div class="min-w-0">
          <p class="text-gray-400 text-xs truncate">Total Tamu</p>
          <p class="text-gray-800 text-xl font-bold" id="statTotal">—</p>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-3 border-l-4 border-blue-500">
        <div class="bg-blue-100 p-2.5 rounded-lg shrink-0"><i data-lucide="user-check" class="w-5 h-5 text-blue-600"></i></div>
        <div class="min-w-0">
          <p class="text-gray-400 text-xs truncate">Hari Ini</p>
          <p class="text-gray-800 text-xl font-bold" id="statHariIni">—</p>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-3 border-l-4 border-indigo-500 col-span-2 md:col-span-1">
        <div class="bg-indigo-100 p-2.5 rounded-lg shrink-0"><i data-lucide="calendar-check" class="w-5 h-5 text-indigo-600"></i></div>
        <div class="min-w-0">
          <p class="text-gray-400 text-xs truncate">Bulan Ini</p>
          <p class="text-gray-800 text-xl font-bold" id="statBulanIni">—</p>
        </div>
      </div>
    </div>

    <!-- ===== TABLE CARD ===== -->
    <div class="bg-white rounded-2xl shadow-sm p-4 md:p-6">

      <div class="flex flex-col gap-3 mb-5">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2">
            <h3 class="text-sm font-bold text-gray-800">Daftar Tamu</h3>
            <span id="rowCount" class="bg-red-800 text-white text-xs font-bold px-2.5 py-0.5 rounded-full">0</span>
          </div>
          <button onclick="resetFilter()"
            class="px-3 py-2 border border-gray-200 text-gray-500 hover:bg-gray-50 rounded-lg text-xs font-medium transition flex items-center gap-1">
            <i data-lucide="x" class="w-3 h-3"></i> Reset
          </button>
        </div>
        <div class="flex gap-2 overflow-x-auto pb-1 -mx-1 px-1">
          <input type="date" id="filterTanggal"
            class="shrink-0 px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-red-400 transition"
            onchange="loadData()"/>
          <select id="filterStatus"
            class="shrink-0 px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-red-400 transition"
            onchange="loadData()">
            <option value="">Semua Status</option>
            <option value="menunggu">🕐 Menunggu</option>
            <option value="diproses">⚙️ Diproses</option>
            <option value="selesai">✅ Selesai</option>
            <option value="ditolak">❌ Ditolak</option>
            <option value="masuk ke kepemimpinan">🏛️ Masuk ke Kepemimpinan</option>
            <option value="masuk ke bagian-bagian">🗂️ Masuk ke Bagian-bagian</option>
          </select>
          <div class="relative shrink-0">
            <i data-lucide="search" class="w-3.5 h-3.5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
            <input id="searchInput" type="text" placeholder="Cari nama / instansi..."
              class="pl-8 pr-4 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-red-400 w-44 transition"
              oninput="loadData()"/>
          </div>
        </div>
      </div>

      <!-- Desktop Table -->
      <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-gray-50 text-gray-400 text-xs uppercase tracking-wide border-b border-gray-100">
              <th class="text-left px-4 py-3 font-semibold">#</th>
              <th class="text-left px-4 py-3 font-semibold">Foto</th>
              <th class="text-left px-4 py-3 font-semibold">Nama Lengkap</th>
              <th class="text-left px-4 py-3 font-semibold">Instansi</th>
              <th class="text-left px-4 py-3 font-semibold">Kepentingan</th>
              <th class="text-left px-4 py-3 font-semibold">Tujuan</th>
              <th class="text-left px-4 py-3 font-semibold">Status</th>
              <th class="text-left px-4 py-3 font-semibold">Tanggal & Waktu</th>
              <th class="text-left px-4 py-3 font-semibold">Terakhir Update</th>
              <th class="text-left px-4 py-3 font-semibold">File</th>
              <th class="text-left px-4 py-3 font-semibold">Aksi</th>
            </tr>
          </thead>
          <tbody id="tableBody">
            <tr>
              <td colspan="11" class="text-center text-gray-400 py-12">
                <div class="flex flex-col items-center gap-2">
                  <i data-lucide="loader" class="w-6 h-6 animate-spin text-gray-300"></i>
                  <span class="text-xs">Memuat data...</span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Mobile Card List -->
      <div class="md:hidden space-y-3" id="mobileCards">
        <div class="py-12 text-center text-gray-400 text-xs flex flex-col items-center gap-2">
          <svg class="w-6 h-6 animate-spin text-gray-300" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
          </svg>
          Memuat data...
        </div>
      </div>

      <div class="flex flex-col sm:flex-row items-center justify-between gap-3 mt-5 pt-4 border-t border-gray-100">
        <p class="text-xs text-gray-400" id="paginationInfo">—</p>
        <div class="flex items-center gap-1 flex-wrap justify-center" id="paginationButtons"></div>
      </div>

    </div>
  </main>

  <?php require_once "includes/footer.php"; ?>
</div>

<!-- ===== LIGHTBOX (foto) ===== -->
<div id="lightbox" class="hidden fixed inset-0 bg-black/70 z-50 flex items-center justify-center" onclick="closeLightbox()">
  <button class="absolute top-4 right-4 bg-white w-9 h-9 rounded-full text-gray-800 font-bold flex items-center justify-center hover:bg-gray-100">✕</button>
  <img id="lightboxImg" src="" class="max-w-[90vw] max-h-[85vh] rounded-xl shadow-2xl"/>
</div>

<!-- ===== FILE MODAL ===== -->
<div id="fileModal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center px-4">
  <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden">

    <!-- Header -->
    <!--<div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
      <div class="flex items-center gap-2">
        <i data-lucide="paperclip" class="w-4 h-4 text-indigo-500"></i>
        <p class="font-bold text-gray-800 text-sm">File Pendukung</p>
      </div>
      <button onclick="closeFileModal()" class="w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center text-gray-400 transition">
        <i data-lucide="x" class="w-4 h-4"></i>
      </button>
    </div>-->

    <!-- Body -->
    <div class="p-5 space-y-4">

      <!-- Preview gambar -->
      <div id="filePreviewImg" class="hidden">
        <img id="fileModalImg" src="" class="w-full max-h-72 object-contain rounded-xl border border-gray-100"/>
      </div>

      <!-- Preview PDF embed -->
      <div id="filePreviewPdf" class="hidden">
        <iframe id="fileModalPdf" src="" class="w-full h-72 rounded-xl border border-gray-100"></iframe>
      </div>

      <!-- Preview DOC — tidak bisa di-embed, tampilkan info saja -->
      <div id="filePreviewDoc" class="hidden">
        <div class="flex flex-col items-center gap-3 py-6 text-gray-400">
          <i data-lucide="file-text" class="w-12 h-12 text-indigo-300"></i>
          <p class="text-sm text-gray-600 font-medium" id="fileModalDocName"></p>
          <p class="text-xs">File Word tidak dapat di-preview langsung.</p>
        </div>
      </div>

      <!-- Info nama file -->
      <div class="flex items-center gap-2 bg-gray-50 rounded-lg px-4 py-2.5">
        <i data-lucide="file" class="w-4 h-4 text-gray-400 shrink-0"></i>
        <span id="fileModalName" class="text-xs text-gray-600 truncate flex-1"></span>
      </div>

    </div>

    <!-- Footer -->
    <div class="flex gap-2 px-5 pb-5">
      <button onclick="closeFileModal()"
        class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-600 rounded-lg text-sm font-semibold hover:bg-gray-50 transition">
        Tutup
      </button>
      <a id="fileModalDownload" href="#" target="_blank" download
        class="flex-1 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold transition flex items-center justify-center gap-2">
        <i data-lucide="download" class="w-4 h-4"></i> Download
      </a>
    </div>

  </div>
</div>

<!-- ===== DELETE MODAL ===== -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center px-4">
  <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-sm">
    <div class="flex items-center gap-3 mb-4">
      <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center shrink-0">
        <i data-lucide="trash-2" class="w-5 h-5 text-red-600"></i>
      </div>
      <div>
        <p class="font-bold text-gray-800 text-sm">Hapus Data Tamu</p>
        <p class="text-gray-400 text-xs">Tindakan ini tidak dapat dibatalkan</p>
      </div>
    </div>
    <p class="text-gray-600 text-sm mb-5">
      Yakin ingin menghapus data tamu <strong id="deleteNama" class="text-gray-800"></strong>?
    </p>
    <div class="flex gap-2">
      <button onclick="closeDeleteModal()"
        class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-600 rounded-lg text-sm font-semibold hover:bg-gray-50 transition">Batal</button>
      <button id="btnConfirmDelete"
        class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-semibold transition flex items-center justify-center gap-2">
        <span>Hapus</span>
      </button>
    </div>
  </div>
</div>

<!-- ===== EDIT STATUS MODAL ===== -->
<div id="statusModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center px-4">
  <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-sm max-h-[90vh] overflow-y-auto">
    <div class="flex items-center gap-3 mb-4">
      <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center shrink-0">
        <i data-lucide="badge-check" class="w-5 h-5 text-indigo-600"></i>
      </div>
      <div>
        <p class="font-bold text-gray-800 text-sm">Ubah Status Tamu</p>
        <p class="text-gray-400 text-xs" id="statusModalNama">—</p>
      </div>
    </div>
    <div class="space-y-2 mb-5">
      <?php
      $statuses = [
        'menunggu'               => ['label' => 'Menunggu',               'icon' => '🕐', 'cls' => 'bg-yellow-50 text-yellow-700'],
        'diproses'               => ['label' => 'Diproses',               'icon' => '⚙️', 'cls' => 'bg-blue-50 text-blue-700'],
        'selesai'                => ['label' => 'Selesai',                'icon' => '✅', 'cls' => 'bg-green-50 text-green-700'],
        'ditolak'                => ['label' => 'Ditolak',                'icon' => '❌', 'cls' => 'bg-red-50 text-red-700'],
        'masuk ke kepemimpinan'  => ['label' => 'Masuk ke Kepemimpinan',  'icon' => '🏛️', 'cls' => 'bg-purple-50 text-purple-700'],
        'masuk ke bagian-bagian' => ['label' => 'Masuk ke Bagian-bagian', 'icon' => '🗂️', 'cls' => 'bg-orange-50 text-orange-700'],
      ];
      foreach ($statuses as $val => $opt): ?>
        <label class="flex items-center gap-3 p-3 border-2 rounded-xl cursor-pointer transition hover:shadow-sm has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50 border-gray-100">
          <input type="radio" name="statusPilihan" value="<?= $val ?>" class="accent-indigo-600 shrink-0"/>
          <span class="text-xs font-medium <?= $opt['cls'] ?> px-2.5 py-1 rounded-full">
            <?= $opt['icon'] ?> <?= $opt['label'] ?>
          </span>
        </label>
      <?php endforeach; ?>
    </div>
    <div class="flex gap-2">
      <button onclick="closeStatusModal()"
        class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-600 rounded-lg text-sm font-semibold hover:bg-gray-50 transition">Batal</button>
      <button id="btnConfirmStatus"
        class="flex-1 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold transition flex items-center justify-center gap-2">
        <span>Simpan</span>
      </button>
    </div>
  </div>
</div>

<script src="/assets/app.js"></script>
<script>
  const PER_PAGE = 10;
  let currentPage = 1;
  let deleteTargetId = null;
  let statusTargetId = null;

  function formatDate(dateStr) {
    if (!dateStr) return '—';
    return new Date(dateStr).toLocaleDateString('id-ID', {
      day: 'numeric', month: 'short', year: 'numeric',
      hour: '2-digit', minute: '2-digit'
    });
  }

  function formatUpdated(dateStr) {
    if (!dateStr) return '<span class="text-gray-300 text-xs italic">Belum diupdate</span>';
    const now  = new Date(), date = new Date(dateStr);
    const diff = Math.floor((now - date) / 1000);
    let relative;
    if      (diff < 60)        relative = 'Baru saja';
    else if (diff < 3600)      relative = `${Math.floor(diff / 60)} menit lalu`;
    else if (diff < 86400)     relative = `${Math.floor(diff / 3600)} jam lalu`;
    else if (diff < 86400 * 7) relative = `${Math.floor(diff / 86400)} hari lalu`;
    else                       relative = formatDate(dateStr);
    return `<span class="text-gray-500 text-xs" title="${formatDate(dateStr)}">${relative}</span>`;
  }

  // ===== FILE BADGE =====
  function fileBadge(filePath) {
    if (!filePath) return '<span class="text-gray-300 text-xs italic">—</span>';
    const ext  = filePath.split('.').pop().toLowerCase();
    const icon = ['jpg','jpeg','png'].includes(ext) ? 'image' : ext === 'pdf' ? 'file-text' : 'file';
    const color = ['jpg','jpeg','png'].includes(ext)
      ? 'bg-pink-50 text-pink-600 hover:bg-pink-100'
      : ext === 'pdf'
        ? 'bg-red-50 text-red-600 hover:bg-red-100'
        : 'bg-blue-50 text-blue-600 hover:bg-blue-100';
    return `
      <button onclick="openFileModal('${filePath}')"
        class="inline-flex items-center gap-1 ${color} text-xs font-semibold px-2.5 py-1.5 rounded-lg transition">
        <i data-lucide="${icon}" class="w-3 h-3"></i>
        ${ext.toUpperCase()}
      </button>`;
  }

  // ===== FILE MODAL =====
  function openFileModal(filePath) {
    const ext      = filePath.split('.').pop().toLowerCase();
    const fileName = filePath.split('/').pop();

    document.getElementById('fileModalName').textContent = fileName;
    document.getElementById('fileModalDownload').href    = filePath;
    document.getElementById('filePreviewImg').classList.add('hidden');
    document.getElementById('filePreviewPdf').classList.add('hidden');
    document.getElementById('filePreviewDoc').classList.add('hidden');

    if (['jpg','jpeg','png'].includes(ext)) {
      document.getElementById('fileModalImg').src = filePath;
      document.getElementById('filePreviewImg').classList.remove('hidden');
    } else if (ext === 'pdf') {
      document.getElementById('fileModalPdf').src = filePath;
      document.getElementById('filePreviewPdf').classList.remove('hidden');
    } else {
      document.getElementById('fileModalDocName').textContent = fileName;
      document.getElementById('filePreviewDoc').classList.remove('hidden');
    }

    document.getElementById('fileModal').classList.remove('hidden');
    lucide.createIcons();
  }

  function closeFileModal() {
    document.getElementById('fileModal').classList.add('hidden');
    document.getElementById('fileModalPdf').src = ''; // stop PDF loading
  }

  const STATUS_CONFIG = {
    'menunggu'               : { label: 'Menunggu',               icon: '🕐', cls: 'bg-yellow-50 text-yellow-700' },
    'diproses'               : { label: 'Diproses',               icon: '⚙️', cls: 'bg-blue-50 text-blue-700'    },
    'selesai'                : { label: 'Selesai',                icon: '✅', cls: 'bg-green-50 text-green-700'   },
    'ditolak'                : { label: 'Ditolak',                icon: '❌', cls: 'bg-red-50 text-red-700'      },
    'masuk ke kepemimpinan'  : { label: 'Masuk ke Kepemimpinan',  icon: '🏛️', cls: 'bg-purple-50 text-purple-700' },
    'masuk ke bagian-bagian' : { label: 'Masuk ke Bagian-bagian', icon: '🗂️', cls: 'bg-orange-50 text-orange-700' },
  };

  function statusBadge(status) {
    const cfg = STATUS_CONFIG[status];
    if (!cfg) return `<span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-gray-100 text-gray-500">${status}</span>`;
    return `<span class="text-xs font-semibold px-2.5 py-1 rounded-full ${cfg.cls}">${cfg.icon} ${cfg.label}</span>`;
  }

  async function loadData() {
    const search  = document.getElementById('searchInput').value;
    const tanggal = document.getElementById('filterTanggal').value;
    const status  = document.getElementById('filterStatus').value;
    const tbody   = document.getElementById('tableBody');
    const cards   = document.getElementById('mobileCards');

    const loadingHtml = `<div class="py-12 text-center text-gray-400 text-xs flex flex-col items-center gap-2">
      <svg class="w-6 h-6 animate-spin text-gray-300" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
      </svg>Memuat data...
    </div>`;

    tbody.innerHTML = `<tr><td colspan="11" class="py-0">${loadingHtml}</td></tr>`;
    cards.innerHTML = loadingHtml;

    try {
      const params = new URLSearchParams({ search, tanggal, status, page: currentPage, per_page: PER_PAGE });
      const res    = await fetch(`/api/get-tamu.php?${params}`);
      const json   = await res.json();

      if (!json.success) throw new Error(json.message);

      document.getElementById('statTotal').textContent    = json.stats?.total    ?? '—';
      document.getElementById('statHariIni').textContent  = json.stats?.hari_ini ?? '—';
      document.getElementById('statBulanIni').textContent = json.stats?.bulan_ini ?? '—';
      document.getElementById('rowCount').textContent     = json.total ?? 0;

      if (!json.data.length) {
        const emptyHtml = `<div class="flex flex-col items-center gap-2 text-gray-300 py-12">
          <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
          </svg>
          <p class="text-sm font-medium text-gray-400">Tidak ada data ditemukan</p>
        </div>`;
        tbody.innerHTML = `<tr><td colspan="11">${emptyHtml}</td></tr>`;
        cards.innerHTML = emptyHtml;
        renderPagination(0);
        return;
      }

      const offset = (currentPage - 1) * PER_PAGE;

      // ===== DESKTOP TABLE =====
      tbody.innerHTML = json.data.map((row, i) => `
        <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
          <td class="px-4 py-3 text-gray-400 text-xs">${offset + i + 1}</td>
          <td class="px-4 py-3">
            ${row.foto
              ? `<img src="${row.foto}" onclick="openLightbox('${row.foto}')"
                  class="w-10 h-10 rounded-lg object-cover border border-gray-200 cursor-pointer hover:scale-110 transition-transform"/>`
              : `<div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 text-sm font-bold">
                  ${row.nama.charAt(0).toUpperCase()}</div>`
            }
          </td>
          <td class="px-4 py-3 font-semibold text-gray-800">${row.nama}</td>
          <td class="px-4 py-3 text-gray-500 text-xs">${row.instansi}</td>
          <td class="px-4 py-3">
            <span class="bg-red-50 text-red-800 text-xs font-semibold px-2.5 py-1 rounded-full">${row.kepentingan}</span>
          </td>
          <td class="px-4 py-3 text-gray-500 text-xs">${row.tujuan}</td>
        
          <td class="px-4 py-3">${statusBadge(row.status)}</td>
          <td class="px-4 py-3 text-gray-400 text-xs whitespace-nowrap">${formatDate(row.tanggal)}</td>
          <td class="px-4 py-3 whitespace-nowrap">${formatUpdated(row.updated_at)}</td>
          <td class="px-4 py-3">${fileBadge(row.file_pendukung)}</td>
          <td class="px-4 py-3">
            <div class="flex items-center gap-1.5">
              <button onclick="openStatusModal(${row.id}, '${row.nama.replace(/'/g,"\\'")}', '${row.status}')"
                class="bg-indigo-50 hover:bg-indigo-100 text-indigo-600 text-xs font-semibold px-3 py-1.5 rounded-lg transition flex items-center gap-1">
                <i data-lucide="pencil" class="w-3 h-3"></i> Status
              </button>
              <button onclick="openDeleteModal(${row.id}, '${row.nama.replace(/'/g,"\\'")}' )"
                class="bg-red-50 hover:bg-red-100 text-red-600 text-xs font-semibold px-3 py-1.5 rounded-lg transition flex items-center gap-1">
                <i data-lucide="trash-2" class="w-3 h-3"></i> Hapus
              </button>
            </div>
          </td>
        </tr>
      `).join('');

      // ===== MOBILE CARDS =====
      cards.innerHTML = json.data.map((row, i) => `
        <div class="border border-gray-100 rounded-xl p-4 space-y-3 hover:bg-gray-50 transition">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              ${row.foto
                ? `<img src="${row.foto}" onclick="openLightbox('${row.foto}')"
                    class="w-10 h-10 rounded-lg object-cover border border-gray-200 cursor-pointer shrink-0"/>`
                : `<div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 font-bold shrink-0">
                    ${row.nama.charAt(0).toUpperCase()}</div>`
              }
              <div>
                <p class="font-semibold text-gray-800 text-sm">${row.nama}</p>
                <p class="text-gray-400 text-xs">${row.instansi}</p>
              </div>
            </div>
            <span class="text-gray-400 text-xs shrink-0">#${offset + i + 1}</span>
          </div>

          <div class="flex items-center gap-2 flex-wrap">
            <span class="bg-red-50 text-red-800 text-xs font-semibold px-2.5 py-1 rounded-full">${row.kepentingan}</span>
            ${statusBadge(row.status)}
          </div>

          <div class="flex items-center justify-between gap-2 text-xs text-gray-400 flex-wrap">
            <span>${formatDate(row.tanggal)}</span>
            <div class="flex items-center gap-1">
              <i data-lucide="clock" class="w-3 h-3 text-gray-300 shrink-0"></i>
              ${formatUpdated(row.updated_at)}
            </div>
          </div>

          <!-- File pendukung jika ada -->
          ${row.file_pendukung ? `
          <div class="flex items-center gap-2 bg-gray-50 rounded-lg px-3 py-2">
            <i data-lucide="paperclip" class="w-3.5 h-3.5 text-gray-400 shrink-0"></i>
            <span class="text-xs text-gray-500 truncate flex-1">${row.file_pendukung.split('/').pop()}</span>
            <button onclick="openFileModal('${row.file_pendukung}')"
              class="shrink-0 text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition flex items-center gap-1">
              <i data-lucide="eye" class="w-3 h-3"></i> Lihat
            </button>
          </div>` : ''}

          <div class="flex gap-2 pt-1 border-t border-gray-50">
            <button onclick="openStatusModal(${row.id}, '${row.nama.replace(/'/g,"\\'")}', '${row.status}')"
              class="flex-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 text-xs font-semibold py-2 rounded-lg transition flex items-center justify-center gap-1">
              <i data-lucide="pencil" class="w-3 h-3"></i> Ubah Status
            </button>
            <button onclick="openDeleteModal(${row.id}, '${row.nama.replace(/'/g,"\\'")}' )"
              class="flex-1 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-semibold py-2 rounded-lg transition flex items-center justify-center gap-1">
              <i data-lucide="trash-2" class="w-3 h-3"></i> Hapus
            </button>
          </div>
        </div>
      `).join('');

      renderPagination(json.total);
      lucide.createIcons();

    } catch (err) {
      const errHtml = `<div class="py-10 text-center text-red-400 text-xs">Gagal memuat data. ${err.message}</div>`;
      tbody.innerHTML = `<tr><td colspan="11">${errHtml}</td></tr>`;
      cards.innerHTML = errHtml;
    }
  }

  function renderPagination(total) {
    const totalPages = Math.ceil(total / PER_PAGE);
    const from = total === 0 ? 0 : (currentPage - 1) * PER_PAGE + 1;
    const to   = Math.min(currentPage * PER_PAGE, total);

    document.getElementById('paginationInfo').textContent =
      total === 0 ? 'Tidak ada data' : `Menampilkan ${from}–${to} dari ${total} data`;

    const btns = document.getElementById('paginationButtons');
    btns.innerHTML = '';

    const prev = document.createElement('button');
    prev.innerHTML = `<i data-lucide="chevron-left" class="w-4 h-4"></i>`;
    prev.className = `px-2 py-1.5 rounded-lg border text-xs transition ${currentPage === 1 ? 'border-gray-100 text-gray-300 cursor-not-allowed' : 'border-gray-200 text-gray-600 hover:bg-gray-50'}`;
    prev.disabled  = currentPage === 1;
    prev.onclick   = () => { currentPage--; loadData(); };
    btns.appendChild(prev);

    for (let p = 1; p <= totalPages; p++) {
      if (totalPages > 7 && p > 2 && p < totalPages - 1 && Math.abs(p - currentPage) > 1) {
        if (p === 3 || p === totalPages - 2) {
          const dots = document.createElement('span');
          dots.textContent = '...';
          dots.className = 'px-2 text-gray-400 text-xs';
          btns.appendChild(dots);
        }
        continue;
      }
      const btn = document.createElement('button');
      btn.textContent = p;
      btn.className   = `w-8 h-8 rounded-lg text-xs font-semibold transition ${p === currentPage ? 'bg-red-800 text-white' : 'border border-gray-200 text-gray-600 hover:bg-gray-50'}`;
      btn.onclick     = () => { currentPage = p; loadData(); };
      btns.appendChild(btn);
    }

    const next = document.createElement('button');
    next.innerHTML = `<i data-lucide="chevron-right" class="w-4 h-4"></i>`;
    next.className = `px-2 py-1.5 rounded-lg border text-xs transition ${currentPage >= totalPages || totalPages === 0 ? 'border-gray-100 text-gray-300 cursor-not-allowed' : 'border-gray-200 text-gray-600 hover:bg-gray-50'}`;
    next.disabled  = currentPage >= totalPages || totalPages === 0;
    next.onclick   = () => { currentPage++; loadData(); };
    btns.appendChild(next);

    lucide.createIcons();
  }

  function resetFilter() {
    document.getElementById('searchInput').value   = '';
    document.getElementById('filterTanggal').value = '';
    document.getElementById('filterStatus').value  = '';
    currentPage = 1;
    loadData();
  }

  function openLightbox(src) { document.getElementById('lightboxImg').src = src; document.getElementById('lightbox').classList.remove('hidden'); }
  function closeLightbox()   { document.getElementById('lightbox').classList.add('hidden'); }

  function openDeleteModal(id, nama) {
    deleteTargetId = id;
    document.getElementById('deleteNama').textContent = nama;
    document.getElementById('deleteModal').classList.remove('hidden');
  }
  function closeDeleteModal() { deleteTargetId = null; document.getElementById('deleteModal').classList.add('hidden'); }

  document.getElementById('btnConfirmDelete').addEventListener('click', async () => {
    if (!deleteTargetId) return;
    const btn = document.getElementById('btnConfirmDelete');
    btn.innerHTML = `<svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>`;
    btn.disabled = true;
    try {
      const res  = await fetch(`/api/delete-tamu.php?id=${deleteTargetId}`, { method: 'DELETE' });
      const json = await res.json();
      if (json.success) { closeDeleteModal(); loadData(); }
    } catch (err) { console.error(err); }
    finally { btn.innerHTML = '<span>Hapus</span>'; btn.disabled = false; }
  });

  function openStatusModal(id, nama, currentStatus) {
    statusTargetId = id;
    document.getElementById('statusModalNama').textContent = nama;
    const radio = document.querySelector(`input[name="statusPilihan"][value="${currentStatus}"]`);
    if (radio) radio.checked = true;
    document.getElementById('statusModal').classList.remove('hidden');
  }
  function closeStatusModal() { statusTargetId = null; document.getElementById('statusModal').classList.add('hidden'); }

  document.getElementById('btnConfirmStatus').addEventListener('click', async () => {
    if (!statusTargetId) return;
    const selected = document.querySelector('input[name="statusPilihan"]:checked');
    if (!selected) return;
    const btn = document.getElementById('btnConfirmStatus');
    btn.innerHTML = `<svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>`;
    btn.disabled = true;
    try {
      const res  = await fetch('/api/update-status-tamu.php', {
        method: 'POST', headers: {'Content-Type':'application/json'},
        body: JSON.stringify({ id: statusTargetId, status: selected.value }),
      });
      const json = await res.json();
      if (json.success) { closeStatusModal(); loadData(); }
    } catch (err) { console.error(err); }
    finally { btn.innerHTML = '<span>Simpan</span>'; btn.disabled = false; }
  });

  loadData();
</script>
</body>
</html>
