<?php
$currentPage = "track_surat";
$pageTitle = "Track Surat";
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

    <!-- Page Header -->
    <div class="flex items-center gap-3">
      <a href="/admin/surat_masuk.php" class="text-gray-400 hover:text-gray-600 transition">
        <i data-lucide="arrow-left" class="w-5 h-5"></i>
      </a>
      <div>
        <h1 class="text-xl font-bold text-gray-800">Track Surat</h1>
        <p class="text-gray-400 text-xs mt-0.5">Lacak posisi dan status surat masuk</p>
      </div>
    </div>

    <!-- Search Card -->
    <div class="bg-white rounded-xl shadow-sm p-5 md:p-6">
      <div class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
          <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 pointer-events-none"></i>
          <input id="trackSearch" type="text"
            placeholder="Cari nama pengirim, perihal, atau nama tamu..."
            autocomplete="off"
            class="w-full pl-9 pr-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-400 transition"
            onkeydown="if(event.key==='Enter') doTrack()"/>
        </div>
        <button onclick="doTrack()" id="btnTrack"
          class="flex items-center justify-center gap-2 px-6 py-3 bg-red-800 hover:bg-red-900 text-white text-sm font-bold rounded-xl transition">
          <i data-lucide="radar" class="w-4 h-4"></i>
          Lacak
        </button>
      </div>

      <!-- Quick filter chips -->
      <div class="flex flex-wrap gap-2 mt-3">
        <?php
        $chips = [
            "Permohonan Magang",
            "Audiensi",
            "Konsultasi",
            "Rapat",
            "Undangan",
        ];
        foreach ($chips as $chip): ?>
          <button onclick="quickSearch('<?= $chip ?>')"
            class="px-3 py-1 bg-gray-100 hover:bg-red-50 hover:text-red-800 text-gray-500 text-xs font-medium rounded-full transition">
            <?= $chip ?>
          </button>
        <?php endforeach;
        ?>
      </div>
    </div>

    <!-- ── Idle State ── -->
    <div id="stateIdle" class="bg-white rounded-xl shadow-sm p-12 text-center space-y-3">
      <div class="mx-auto w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center">
        <i data-lucide="radar" class="w-7 h-7 text-gray-300"></i>
      </div>
      <p class="text-sm font-semibold text-gray-500">Masukkan kata kunci untuk melacak surat</p>
      <p class="text-xs text-gray-400">Pencarian akan mencocokkan pengirim, perihal, dan nama tamu</p>
    </div>

    <!-- ── Loading State ── -->
    <div id="stateLoading" class="hidden bg-white rounded-xl shadow-sm p-12 text-center space-y-3">
      <svg class="animate-spin w-8 h-8 text-red-800 mx-auto" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
      </svg>
      <p class="text-sm text-gray-400">Mencari surat...</p>
    </div>

    <!-- ── Not Found State ── -->
    <div id="stateNotFound" class="hidden bg-white rounded-xl shadow-sm p-12 text-center space-y-3">
      <div class="mx-auto w-14 h-14 bg-yellow-50 rounded-full flex items-center justify-center">
        <i data-lucide="search-x" class="w-7 h-7 text-yellow-400"></i>
      </div>
      <p class="text-sm font-semibold text-gray-600">Surat tidak ditemukan</p>
      <p class="text-xs text-gray-400">Coba kata kunci lain seperti nama pengirim atau perihal surat.</p>
    </div>

    <!-- ── Results ── -->
    <div id="stateResults" class="hidden space-y-4">

      <!-- Result meta -->
      <div class="flex items-center justify-between">
        <p class="text-xs text-gray-400">
          Hasil untuk: <span id="trackQueryLabel" class="font-semibold text-gray-700"></span>
        </p>
        <span id="trackCountBadge" class="bg-red-800 text-white text-xs font-bold px-2.5 py-0.5 rounded-full">0</span>
      </div>

      <!-- Result cards -->
      <div id="trackList" class="space-y-4"></div>

    </div>

  </main>
  <?php require_once "includes/footer.php"; ?>
</div>

<!-- ── Edit Status Modal ── -->
<div id="modalEditStatus"
  class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm px-4">
  <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 space-y-5">

    <div class="flex items-center justify-between">
      <h2 class="text-sm font-bold text-gray-800">Edit Status Surat</h2>
      <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition">
        <i data-lucide="x" class="w-4 h-4"></i>
      </button>
    </div>

    <input type="hidden" id="editSuratId"/>

    <div class="bg-gray-50 rounded-xl px-4 py-3 space-y-1">
      <p id="editSuratPengirim" class="text-xs font-semibold text-gray-700"></p>
      <p id="editSuratPerihal"  class="text-xs text-gray-400 truncate"></p>
    </div>

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

    <div class="space-y-1.5">
      <label class="text-xs font-semibold text-gray-600">
        Posisi <span class="font-normal text-gray-400">(opsional)</span>
      </label>
      <input id="editPosisi" type="text" placeholder="cth. Ruang Ketua"
        class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-red-400 transition"/>
    </div>

    <p id="editError" class="hidden text-xs text-red-500 font-medium"></p>

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

<!-- Toast -->
<div id="toast"
  class="fixed bottom-5 right-5 z-[60] hidden items-center gap-3 px-4 py-3 rounded-xl shadow-lg text-xs font-semibold text-white transition-all duration-300">
</div>

<script src="/assets/app.js"></script>
<script>
  lucide.createIcons();

  // ── Status flow for timeline ───────────────────────────────
  const STATUS_FLOW = [
    { key: 'frontdesk',     label: 'Frontdesk',     icon: 'inbox' },
    { key: 'agendaris',     label: 'Agendaris',      icon: 'clipboard-list' },
    { key: 'setwan',        label: 'Setwan',         icon: 'building-2' },
    { key: 'pimpinan dprd', label: 'Pimpinan DPRD', icon: 'crown' },
    { key: 'komisi',        label: 'Komisi',         icon: 'users' },
    { key: 'selesai',       label: 'Selesai',        icon: 'check-circle' },
  ];

  function buildTimeline(currentStatus) {
    const currentIdx = STATUS_FLOW.findIndex(s => s.key === currentStatus);

    return STATUS_FLOW.map((step, i) => {
      const done    = i < currentIdx;
      const active  = i === currentIdx;
      const isLast  = i === STATUS_FLOW.length - 1;

      const circleCls = done
        ? 'bg-green-500 border-green-500 text-white'
        : active
          ? 'bg-red-800 border-red-800 text-white ring-4 ring-red-100'
          : 'bg-white border-gray-200 text-gray-300';

      const labelCls = done
        ? 'text-green-600 font-semibold'
        : active
          ? 'text-red-800 font-bold'
          : 'text-gray-300';

      const lineCls = done ? 'bg-green-400' : 'bg-gray-200';

      return `
        <div class="flex flex-col items-center shrink-0">
          <div class="flex items-center">
            <div class="w-9 h-9 rounded-full border-2 flex items-center justify-center transition-all ${circleCls}">
              <i data-lucide="${done ? 'check' : step.icon}" class="w-4 h-4"></i>
            </div>
            ${!isLast ? `<div class="w-8 md:w-12 h-0.5 ${lineCls}"></div>` : ''}
          </div>
          <p class="text-[10px] mt-1.5 text-center w-16 leading-tight ${labelCls}">${step.label}</p>
        </div>
      `;
    }).join('');
  }

  // ── Helpers ────────────────────────────────────────────────
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

  function editBtn(row) {
    const data = encodeURIComponent(JSON.stringify({
      id: row.id, pengirim: row.pengirim, perihal: row.perihal,
      status: row.status, posisi: row.posisi ?? '',
    }));
    return `<button onclick="openEditModal(this)" data-row="${data}"
      class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-blue-200 text-blue-600 hover:bg-blue-50 text-xs font-semibold rounded-lg transition">
      <i data-lucide="pencil" class="w-3.5 h-3.5"></i> Edit Status
    </button>`;
  }

  // ── State manager ──────────────────────────────────────────
  function showState(state) {
    ['Idle','Loading','NotFound','Results'].forEach(s =>
      document.getElementById('state' + s).classList.toggle('hidden', s.toLowerCase() !== state)
    );
  }

  // ── Quick search chips ─────────────────────────────────────
  function quickSearch(keyword) {
    document.getElementById('trackSearch').value = keyword;
    doTrack();
  }

  // ── Main search ────────────────────────────────────────────
  async function doTrack() {
    const q = document.getElementById('trackSearch').value.trim();
    if (!q) return;

    showState('loading');

    try {
      const res = await fetch(`/api/get-track-surat.php?${new URLSearchParams({ q })}`);
      const json = await res.json();
      const data = json.data ?? [];

      if (!data.length) {
        showState('notfound');
        return;
      }

      document.getElementById('trackQueryLabel').textContent  = `"${q}"`;
      document.getElementById('trackCountBadge').textContent  = data.length;

      document.getElementById('trackList').innerHTML = data.map(row => `
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">

          <!-- Card header -->
          <div class="px-5 py-4 border-b border-gray-50 flex items-start justify-between gap-3 flex-wrap">
            <div class="min-w-0 space-y-0.5">
              <p class="font-bold text-gray-800 text-sm">${row.pengirim}</p>
              <p class="text-gray-400 text-xs">${row.perihal}</p>
              ${row.nama_tamu
                ? `<p class="text-xs text-gray-500 pt-0.5">
                    Tamu: <span class="font-semibold text-gray-700">${row.nama_tamu}</span>
                   </p>`
                : ''}
            </div>
            <div class="flex items-center gap-2 shrink-0 flex-wrap">
              ${statusBadge(row.status)}
              ${editBtn(row)}
            </div>
          </div>

          <!-- Timeline -->
          <div class="px-5 py-4 bg-gray-50/50">
            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide mb-3">Progress Surat</p>
            <div class="flex items-start overflow-x-auto pb-1">
              ${buildTimeline(row.status)}
            </div>
          </div>

          <!-- Meta grid -->
          <div class="px-5 py-4 grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div>
              <p class="text-[10px] text-gray-400 uppercase font-semibold tracking-wide">Tanggal Surat</p>
              <p class="text-xs font-semibold text-gray-700 mt-1">${formatDate(row.tanggal_surat)}</p>
            </div>
            <div>
              <p class="text-[10px] text-gray-400 uppercase font-semibold tracking-wide">Tgl Diterima</p>
              <p class="text-xs font-semibold text-gray-700 mt-1">${formatDate(row.tanggal_diterima)}</p>
            </div>
            <div>
              <p class="text-[10px] text-gray-400 uppercase font-semibold tracking-wide">Posisi Saat Ini</p>
              <p class="text-xs font-semibold text-gray-700 mt-1">${row.posisi ?? '—'}</p>
            </div>
            <div>
              <p class="text-[10px] text-gray-400 uppercase font-semibold tracking-wide">File</p>
              <div class="mt-1">
                ${row.file_path
                  ? `<a href="${row.file_path}" target="_blank"
                       class="inline-flex items-center gap-1 text-xs text-red-800 hover:underline font-medium">
                       <i data-lucide="file-text" class="w-3.5 h-3.5"></i> Lihat File
                     </a>`
                  : '<span class="text-xs text-gray-300">Tidak ada</span>'}
              </div>
            </div>
          </div>

        </div>
      `).join('');

      showState('results');
      lucide.createIcons();

    } catch {
      showState('idle');
      showToast('Gagal terhubung ke server.', 'error');
    }
  }

  // ── Modal ──────────────────────────────────────────────────
  function openEditModal(btn) {
    const row = JSON.parse(decodeURIComponent(btn.dataset.row));
    document.getElementById('editSuratId').value             = row.id;
    document.getElementById('editSuratPengirim').textContent = row.pengirim;
    document.getElementById('editSuratPerihal').textContent  = row.perihal;
    document.getElementById('editStatus').value              = row.status;
    document.getElementById('editPosisi').value              = row.posisi;
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

  document.getElementById('modalEditStatus').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
  });
  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

  // ── Submit Edit ────────────────────────────────────────────
  async function submitEditStatus() {
    const id     = document.getElementById('editSuratId').value;
    const status = document.getElementById('editStatus').value;
    const posisi = document.getElementById('editPosisi').value.trim();
    const btn    = document.getElementById('btnSimpan');
    const errEl  = document.getElementById('editError');

    errEl.classList.add('hidden');
    btn.disabled  = true;
    btn.innerHTML = `<svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
    </svg> Menyimpan...`;

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
      doTrack(); // re-run search to refresh timeline

    } catch {
      errEl.textContent = 'Terjadi kesalahan koneksi.';
      errEl.classList.remove('hidden');
    } finally {
      btn.disabled  = false;
      btn.innerHTML = `<i data-lucide="save" class="w-3.5 h-3.5"></i> Simpan`;
      lucide.createIcons();
    }
  }

  // ── Toast ──────────────────────────────────────────────────
  function showToast(message, type = 'success') {
    const toast  = document.getElementById('toast');
    const colors = { success: 'bg-green-600', error: 'bg-red-600' };
    toast.className = `fixed bottom-5 right-5 z-[60] flex items-center gap-3 px-4 py-3 rounded-xl shadow-lg text-xs font-semibold text-white transition-all duration-300 ${colors[type]}`;
    toast.textContent = message;
    toast.classList.remove('hidden');
    setTimeout(() => toast.classList.add('hidden'), 3000);
  }
</script>
</body>
</html>
