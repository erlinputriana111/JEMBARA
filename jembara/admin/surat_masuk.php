<?php
$currentPage = "surat_masuk";
$pageTitle = "Surat Masuk";
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

  <main class="flex-1 p-4 md:p-6 space-y-5">

    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-xl font-bold text-gray-800">Surat Masuk</h1>
        <p class="text-gray-400 text-xs mt-1">Daftar seluruh surat masuk</p>
      </div>
      <a href="/admin/tambah_surat.php"
        class="flex items-center gap-2 px-4 py-2.5 bg-red-800 hover:bg-red-900 text-white text-xs font-bold rounded-xl transition">
        <i data-lucide="plus" class="w-4 h-4"></i>
        Tambah Surat
      </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-4 md:p-5">

      <!-- Toolbar -->
      <div class="flex flex-col sm:flex-row gap-2 mb-5">
        <input id="filterTanggal" type="date"
          class="px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-red-400 transition w-full sm:w-auto"
          oninput="loadSurat()"/>
        <input id="searchInput" type="text" placeholder="🔍 Cari pengirim / perihal..."
          class="px-4 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-red-400 w-full sm:w-64 transition"
          oninput="loadSurat()"/>
        <select id="filterStatus"
          class="px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-red-400 transition w-full sm:w-auto"
          onchange="loadSurat()">
          <option value="">Semua Status</option>
          <option value="frontdesk">Frontdesk</option>
          <option value="agendaris">Agendaris</option>
          <option value="setwan">Setwan</option>
          <option value="pimpinan dprd">Pimpinan DPRD</option>
          <option value="komisi">Komisi</option>
          <option value="selesai">Selesai</option>
        </select>
      </div>

      <!-- Desktop Table -->
      <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="text-gray-400 text-xs border-b border-gray-100">
              <th class="text-left pb-3 font-medium">#</th>
              <th class="text-left pb-3 font-medium">Nama Tamu</th>
              <th class="text-left pb-3 font-medium">Pengirim</th>
              <th class="text-left pb-3 font-medium">Perihal</th>
              <th class="text-left pb-3 font-medium">Tgl Surat</th>
              <th class="text-left pb-3 font-medium">Tgl Diterima</th>
              <th class="text-left pb-3 font-medium">Status</th>
              <th class="text-left pb-3 font-medium">Posisi</th>
              <th class="text-left pb-3 font-medium">File</th>
              <th class="text-left pb-3 font-medium">Aksi</th>
            </tr>
          </thead>
          <tbody id="suratBody" class="divide-y divide-gray-50 text-gray-700">
            <tr>
              <td colspan="10" class="py-10 text-center text-gray-400 text-xs">Memuat data...</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Mobile Cards -->
      <div class="md:hidden space-y-3" id="suratCards">
        <div class="py-10 text-center text-gray-400 text-xs">Memuat data...</div>
      </div>

      <div class="mt-4 text-xs text-gray-400" id="suratInfo"></div>
    </div>

  </main>
  <?php require_once "includes/footer.php"; ?>
</div>

<!-- ─────────────────────────────────────────
     Edit Status Modal
───────────────────────────────────────── -->
<div id="modalEditStatus"
  class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm px-4">
  <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 space-y-5">

    <!-- Header -->
    <div class="flex items-center justify-between">
      <h2 class="text-sm font-bold text-gray-800">Edit Status Surat</h2>
      <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition">
        <i data-lucide="x" class="w-4 h-4"></i>
      </button>
    </div>

    <input type="hidden" id="editSuratId"/>

    <!-- Read-only context -->
    <div class="bg-gray-50 rounded-xl px-4 py-3 space-y-1">
      <p id="editSuratPengirim" class="text-xs font-semibold text-gray-700"></p>
      <p id="editSuratPerihal"  class="text-xs text-gray-400 truncate"></p>
    </div>

    <!-- Status -->
    <div class="space-y-1.5">
      <label class="text-xs font-semibold text-gray-600">Status</label>
      <select id="editStatus"
        class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-red-400 transition">
        <option value="frontdesk">Frontdesk</option>
        <option value="agendaris">Agendaris</option>
        <option value="setwan">Setwan</option>
        <option value="pimpinan dprd">Pimpinan DPRD</option>
        <option value="keuangan">Keuangan</option>
        <option value="umum">Umum</option>
        <option value="pengawasan">Pengawasan</option>
        <option value="fasilitasi_dan_Penganggaran">Fasilitasi dan Penganggaran</option>
        
        <option value="selesai">Selesai</option>
      </select>
    </div>

    <!-- Posisi -->
    <div class="space-y-1.5">
      <label class="text-xs font-semibold text-gray-600">
        Posisi <span class="font-normal text-gray-400">(opsional)</span>
      </label>
      <input id="editPosisi" type="text" placeholder="cth. Ruang Ketua"
        class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-red-400 transition"/>
    </div>

    <!-- Error -->
    <p id="editError" class="hidden text-xs text-red-500 font-medium"></p>

    <!-- Actions -->
    <div class="flex gap-2 pt-1">
      <button onclick="closeModal()"
        class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-50 transition">
        Batal
      </button>
      <button onclick="submitEditStatus()" id="btnSimpan"
        class="flex-1 px-4 py-2.5 bg-red-800 hover:bg-red-900 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-2">
        <i data-lucide="save" class="w-3.5 h-3.5"></i>
        Simpan
      </button>
    </div>

  </div>
</div>

<!-- ─────────────────────────────────────────
     Toast Notification
───────────────────────────────────────── -->
<div id="toast"
  class="fixed bottom-5 right-5 z-[60] hidden items-center gap-3 px-4 py-3 rounded-xl shadow-lg text-xs font-semibold text-white transition-all duration-300">
</div>

<script src="/assets/app.js"></script>
<script>
  lucide.createIcons();

  // ── Utilities ──────────────────────────────────────────────

  function formatDate(d) {
    if (!d) return '—';
    return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
  }

  function statusBadge(status) {
    const cfg = {
      'frontdesk'    : 'bg-gray-100 text-gray-600',
      'agendaris'    : 'bg-yellow-50 text-yellow-700',
      'setwan'       : 'bg-blue-50 text-blue-700',
      'pimpinan dprd': 'bg-purple-50 text-purple-700',
      'komisi'       : 'bg-orange-50 text-orange-700',
      'selesai'      : 'bg-green-50 text-green-700',
    };
    return `<span class="text-xs font-semibold px-2.5 py-1 rounded-full ${cfg[status] ?? 'bg-gray-100 text-gray-500'}">${status}</span>`;
  }

  function fileBtn(path) {
    if (!path) return '<span class="text-gray-300 text-xs">—</span>';
    return `<a href="${path}" target="_blank"
      class="inline-flex items-center gap-1 text-xs text-red-800 hover:underline font-medium">
      <i data-lucide="file-text" class="w-3.5 h-3.5"></i> Lihat
    </a>`;
  }

  function editBtn(row) {
    // Encode row data safely into a data attribute to avoid quote-escaping issues
    const data = encodeURIComponent(JSON.stringify({
      id      : row.id,
      pengirim: row.pengirim,
      perihal : row.perihal,
      status  : row.status,
      posisi  : row.posisi ?? '',
    }));
    return `<button
      onclick="openEditModal(this)"
      data-row="${data}"
      class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800 font-medium transition">
      <i data-lucide="pencil" class="w-3.5 h-3.5"></i> Edit
    </button>`;
  }

  function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const colors = {
      success: 'bg-green-600',
      error  : 'bg-red-600',
    };
    toast.className = `fixed bottom-5 right-5 z-[60] flex items-center gap-3 px-4 py-3 rounded-xl shadow-lg text-xs font-semibold text-white transition-all duration-300 ${colors[type]}`;
    toast.textContent = message;
    toast.classList.remove('hidden');
    setTimeout(() => toast.classList.add('hidden'), 3000);
  }

  // ── Load Surat ─────────────────────────────────────────────

  async function loadSurat() {
    const search  = document.getElementById('searchInput').value;
    const tanggal = document.getElementById('filterTanggal').value;
    const status  = document.getElementById('filterStatus').value;
    const tbody   = document.getElementById('suratBody');
    const cards   = document.getElementById('suratCards');

    try {
      const params = new URLSearchParams({ search, tanggal, status });
      const res    = await fetch(`/api/get-surat-masuk.php?${params}`);
      const json   = await res.json();

      document.getElementById('suratInfo').textContent =
        json.data?.length ? `Menampilkan ${json.data.length} surat` : '';

      if (!json.data?.length) {
        const msg = 'Tidak ada data surat.';
        tbody.innerHTML = `<tr><td colspan="10" class="py-10 text-center text-gray-400 text-xs">${msg}</td></tr>`;
        cards.innerHTML = `<div class="py-10 text-center text-gray-400 text-xs">${msg}</div>`;
        return;
      }

      // ── Desktop rows ──
      tbody.innerHTML = json.data.map((row, i) => `
        <tr class="hover:bg-gray-50 transition">
          <td class="py-3 text-gray-400 text-xs">${i + 1}</td>
          <td class="py-3 font-medium text-gray-800 text-xs">${row.nama_tamu ?? '—'}</td>
          <td class="py-3 text-gray-700 text-xs">${row.pengirim}</td>
          <td class="py-3 text-gray-700 text-xs max-w-[180px] truncate">${row.perihal}</td>
          <td class="py-3 text-gray-400 text-xs whitespace-nowrap">${formatDate(row.tanggal_surat)}</td>
          <td class="py-3 text-gray-400 text-xs whitespace-nowrap">${formatDate(row.tanggal_diterima)}</td>
          <td class="py-3">${statusBadge(row.status)}</td>
          <td class="py-3 text-gray-500 text-xs">${row.posisi ?? '—'}</td>
          <td class="py-3">${fileBtn(row.file_path)}</td>
          <td class="py-3">${editBtn(row)}</td>
        </tr>
      `).join('');

      // ── Mobile cards ──
      cards.innerHTML = json.data.map((row, i) => `
        <div class="border border-gray-100 rounded-xl p-4 space-y-3 hover:bg-gray-50 transition">
          <div class="flex items-center justify-between">
            <span class="text-gray-400 text-xs">#${i + 1}</span>
            ${statusBadge(row.status)}
          </div>
          <div>
            <p class="font-semibold text-gray-800 text-sm">${row.pengirim}</p>
            <p class="text-gray-400 text-xs mt-0.5">${row.perihal}</p>
          </div>
          ${row.nama_tamu ? `<p class="text-xs text-gray-500">Tamu: <span class="font-medium">${row.nama_tamu}</span></p>` : ''}
          <div class="flex items-center justify-between flex-wrap gap-2">
            <div class="text-xs text-gray-400 space-y-0.5">
              <p>Surat: ${formatDate(row.tanggal_surat)}</p>
              <p>Diterima: ${formatDate(row.tanggal_diterima)}</p>
            </div>
            ${fileBtn(row.file_path)}
          </div>
          <div class="flex items-center justify-between">
            <p class="text-xs text-gray-400">Posisi: <span class="text-gray-600 font-medium">${row.posisi ?? '—'}</span></p>
            ${editBtn(row)}
          </div>
        </div>
      `).join('');

      lucide.createIcons();

    } catch (err) {
      tbody.innerHTML = `<tr><td colspan="10" class="py-10 text-center text-red-400 text-xs">Gagal memuat data.</td></tr>`;
      cards.innerHTML = `<div class="py-10 text-center text-red-400 text-xs">Gagal memuat data.</div>`;
    }
  }

  loadSurat();
  setInterval(loadSurat, 10000);

  // ── Modal ──────────────────────────────────────────────────

  function openEditModal(btn) {
    const row = JSON.parse(decodeURIComponent(btn.dataset.row));

    document.getElementById('editSuratId').value          = row.id;
    document.getElementById('editSuratPengirim').textContent = row.pengirim;
    document.getElementById('editSuratPerihal').textContent  = row.perihal;
    document.getElementById('editStatus').value           = row.status;
    document.getElementById('editPosisi').value           = row.posisi;
    document.getElementById('editError').classList.add('hidden');

    const modal = document.getElementById('modalEditStatus');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    lucide.createIcons();
  }

  function closeModal() {
    const modal = document.getElementById('modalEditStatus');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  }

  // Close on backdrop click
  document.getElementById('modalEditStatus').addEventListener('click', function (e) {
    if (e.target === this) closeModal();
  });

  // Close on Escape key
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeModal();
  });

  // ── Submit Edit Status ─────────────────────────────────────

  async function submitEditStatus() {
    const id     = document.getElementById('editSuratId').value;
    const status = document.getElementById('editStatus').value;
    const posisi = document.getElementById('editPosisi').value.trim();
    const btn    = document.getElementById('btnSimpan');
    const errEl  = document.getElementById('editError');

    errEl.classList.add('hidden');

    // Loading state
    btn.disabled    = true;
    btn.innerHTML   = `
      <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
      </svg>
      Menyimpan...`;

    try {
      const res  = await fetch('/api/update-status-surat.php', {
        method : 'POST',
        headers: { 'Content-Type': 'application/json' },
        body   : JSON.stringify({ id, status, posisi }),
      });
      const json = await res.json();

      if (!json.success) {
        errEl.textContent = json.message ?? 'Gagal menyimpan.';
        errEl.classList.remove('hidden');
        return;
      }

      closeModal();
      showToast('Status berhasil diperbarui ✓', 'success');
      loadSurat(); // refresh table immediately

    } catch (err) {
      errEl.textContent = 'Terjadi kesalahan koneksi. Coba lagi.';
      errEl.classList.remove('hidden');
      console.log(err)
    } finally {
      btn.disabled  = false;
      btn.innerHTML = `<i data-lucide="save" class="w-3.5 h-3.5"></i> Simpan`;
      lucide.createIcons();
    }
  }
</script>
</body>
</html>