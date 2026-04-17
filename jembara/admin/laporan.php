<?php
$currentPage = "laporan";
$pageTitle   = "Laporan";
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
  <!-- jsPDF + AutoTable -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.1/jspdf.plugin.autotable.min.js"></script>
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

    <div>
      <h1 class="text-xl font-bold text-gray-800">Laporan</h1>
      <p class="text-gray-400 text-xs mt-1">Rekap data tamu dan surat masuk</p>
    </div>

    <!-- ===== TABS ===== -->
    <div class="flex gap-1 bg-white rounded-xl shadow-sm p-1.5 w-full overflow-x-auto">
      <?php
      $tabs = [
        ['id' => 'tamu-harian',  'icon' => 'calendar-days', 'label' => 'Tamu Per Hari'],
        ['id' => 'tamu-bulanan', 'icon' => 'bar-chart-2',   'label' => 'Tamu Per Bulan'],
        ['id' => 'surat-masuk',  'icon' => 'mail-open',     'label' => 'Surat Masuk'],
      ];
      foreach ($tabs as $tab): ?>
        <button
          data-tab="<?= $tab['id'] ?>"
          onclick="switchTab('<?= $tab['id'] ?>')"
          class="tab-btn flex items-center gap-2 px-4 py-2.5 rounded-lg text-xs font-semibold transition whitespace-nowrap text-gray-500 hover:bg-gray-50">
          <i data-lucide="<?= $tab['icon'] ?>" class="w-4 h-4"></i>
          <?= $tab['label'] ?>
        </button>
      <?php endforeach; ?>
    </div>

    <!-- ===== TAB: TAMU PER HARI ===== -->
    <div id="tab-tamu-harian" class="tab-content space-y-4">

      <div class="bg-white rounded-xl shadow-sm p-4 flex flex-wrap gap-3 items-end">
        <div class="flex flex-col gap-1">
          <label class="text-xs font-semibold text-gray-400 uppercase">Dari</label>
          <input type="date" id="harianDari"
            class="px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-red-400"/>
        </div>
        <div class="flex flex-col gap-1">
          <label class="text-xs font-semibold text-gray-400 uppercase">Sampai</label>
          <input type="date" id="harianSampai"
            class="px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-red-400"/>
        </div>
        <button onclick="loadTamuHarian()"
          class="px-4 py-2 bg-red-800 hover:bg-red-900 text-white text-xs font-semibold rounded-lg transition flex items-center gap-2">
          <i data-lucide="search" class="w-3.5 h-3.5"></i> Tampilkan
        </button>
        <button onclick="exportPDF('tamu-harian')"
          class="px-4 py-2 border border-red-200 text-red-700 hover:bg-red-50 text-xs font-semibold rounded-lg transition flex items-center gap-2">
          <i data-lucide="file-text" class="w-3.5 h-3.5"></i> Ekspor ke PDF
        </button>
      </div>

      <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-sm font-bold text-gray-800">Rekap Tamu Per Hari</h3>
          <span id="harianCount" class="bg-red-800 text-white text-xs font-bold px-2.5 py-0.5 rounded-full">0</span>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="bg-gray-50 text-gray-400 text-xs uppercase tracking-wide border-b border-gray-100">
                <th class="text-left px-4 py-3 font-semibold">#</th>
                <th class="text-left px-4 py-3 font-semibold">Tanggal</th>
                <th class="text-left px-4 py-3 font-semibold">Nama Tamu</th>
                <th class="text-left px-4 py-3 font-semibold">Instansi</th>
                <th class="text-left px-4 py-3 font-semibold">Kepentingan</th>
                <th class="text-left px-4 py-3 font-semibold">Tujuan</th>
                <th class="text-left px-4 py-3 font-semibold">Status</th>
              </tr>
            </thead>
            <tbody id="harianBody">
              <tr><td colspan="7" class="py-10 text-center text-gray-400 text-xs">Pilih rentang tanggal lalu klik Tampilkan.</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ===== TAB: TAMU PER BULAN ===== -->
    <div id="tab-tamu-bulanan" class="tab-content hidden space-y-4">

      <div class="bg-white rounded-xl shadow-sm p-4 flex flex-wrap gap-3 items-end">
        <div class="flex flex-col gap-1">
          <label class="text-xs font-semibold text-gray-400 uppercase">Tahun</label>
          <select id="bulananTahun"
            class="px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-red-400">
            <?php for ($y = date('Y'); $y >= date('Y') - 3; $y--): ?>
              <option value="<?= $y ?>"><?= $y ?></option>
            <?php endfor; ?>
          </select>
        </div>
        <button onclick="loadTamuBulanan()"
          class="px-4 py-2 bg-red-800 hover:bg-red-900 text-white text-xs font-semibold rounded-lg transition flex items-center gap-2">
          <i data-lucide="search" class="w-3.5 h-3.5"></i> Tampilkan
        </button>
        <button onclick="exportPDF('tamu-bulanan')"
          class="px-4 py-2 border border-red-200 text-red-700 hover:bg-red-50 text-xs font-semibold rounded-lg transition flex items-center gap-2">
          <i data-lucide="file-text" class="w-3.5 h-3.5"></i> Ekspor ke PDF
        </button>
      </div>

      <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-sm font-bold text-gray-800">Rekap Tamu Per Bulan</h3>
          <span id="bulananCount" class="bg-red-800 text-white text-xs font-bold px-2.5 py-0.5 rounded-full">0</span>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="bg-gray-50 text-gray-400 text-xs uppercase tracking-wide border-b border-gray-100">
                <th class="text-left px-4 py-3 font-semibold">#</th>
                <th class="text-left px-4 py-3 font-semibold">Bulan</th>
                <th class="text-left px-4 py-3 font-semibold">Total Tamu</th>
                <th class="text-left px-4 py-3 font-semibold">Selesai</th>
                <th class="text-left px-4 py-3 font-semibold">Diproses</th>
                <th class="text-left px-4 py-3 font-semibold">Menunggu</th>
                <th class="text-left px-4 py-3 font-semibold">Ditolak</th>
              </tr>
            </thead>
            <tbody id="bulananBody">
              <tr><td colspan="7" class="py-10 text-center text-gray-400 text-xs">Pilih tahun lalu klik Tampilkan.</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ===== TAB: SURAT MASUK ===== -->
    <div id="tab-surat-masuk" class="tab-content hidden space-y-4">

      <div class="bg-white rounded-xl shadow-sm p-4 flex flex-wrap gap-3 items-end">
        <div class="flex flex-col gap-1">
          <label class="text-xs font-semibold text-gray-400 uppercase">Dari</label>
          <input type="date" id="suratDari"
            class="px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-red-400"/>
        </div>
        <div class="flex flex-col gap-1">
          <label class="text-xs font-semibold text-gray-400 uppercase">Sampai</label>
          <input type="date" id="suratSampai"
            class="px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-red-400"/>
        </div>
        <button onclick="loadSuratMasuk()"
          class="px-4 py-2 bg-red-800 hover:bg-red-900 text-white text-xs font-semibold rounded-lg transition flex items-center gap-2">
          <i data-lucide="search" class="w-3.5 h-3.5"></i> Tampilkan
        </button>
        <button onclick="exportPDF('surat-masuk')"
          class="px-4 py-2 border border-red-200 text-red-700 hover:bg-red-50 text-xs font-semibold rounded-lg transition flex items-center gap-2">
          <i data-lucide="file-text" class="w-3.5 h-3.5"></i> Ekspor ke PDF
        </button>
      </div>

      <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-sm font-bold text-gray-800">Rekap Surat Masuk</h3>
          <span id="suratCount" class="bg-red-800 text-white text-xs font-bold px-2.5 py-0.5 rounded-full">0</span>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="bg-gray-50 text-gray-400 text-xs uppercase tracking-wide border-b border-gray-100">
                <th class="text-left px-4 py-3 font-semibold">#</th>
                <th class="text-left px-4 py-3 font-semibold">Tanggal</th>
                <th class="text-left px-4 py-3 font-semibold">No. Surat</th>
                <th class="text-left px-4 py-3 font-semibold">Pengirim</th>
                <th class="text-left px-4 py-3 font-semibold">Perihal</th>
                <th class="text-left px-4 py-3 font-semibold">Status</th>
              </tr>
            </thead>
            <tbody id="suratBody">
              <tr><td colspan="6" class="py-10 text-center text-gray-400 text-xs">Pilih rentang tanggal lalu klik Tampilkan.</td></tr>
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
  lucide.createIcons();

  const { jsPDF } = window.jspdf;

  // ===== TABS =====
  function switchTab(id) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(btn => {
      btn.classList.remove('bg-red-800', 'text-white');
      btn.classList.add('text-gray-500', 'hover:bg-gray-50');
    });
    document.getElementById('tab-' + id).classList.remove('hidden');
    const activeBtn = document.querySelector(`[data-tab="${id}"]`);
    activeBtn.classList.add('bg-red-800', 'text-white');
    activeBtn.classList.remove('text-gray-500', 'hover:bg-gray-50');
    lucide.createIcons();
  }

  // ===== HELPERS =====
  function formatDate(dateStr) {
    if (!dateStr) return '—';
    return new Date(dateStr).toLocaleDateString('id-ID', {
      day: 'numeric', month: 'short', year: 'numeric'
    });
  }

  const BULAN = ['Januari','Februari','Maret','April','Mei','Juni',
                 'Juli','Agustus','September','Oktober','November','Desember'];

  function statusBadge(status) {
    const cfg = {
      menunggu               : 'bg-yellow-50 text-yellow-700',
      diproses               : 'bg-blue-50 text-blue-700',
      selesai                : 'bg-green-50 text-green-700',
      ditolak                : 'bg-red-50 text-red-700',
      'masuk ke kepemimpinan': 'bg-purple-50 text-purple-700',
      'masuk ke bagian-bagian':'bg-orange-50 text-orange-700',
    };
    return `<span class="text-xs font-semibold px-2.5 py-1 rounded-full ${cfg[status] ?? 'bg-gray-100 text-gray-500'}">${status}</span>`;
  }

  function emptyRow(colspan, msg = 'Tidak ada data.') {
    return `<tr><td colspan="${colspan}" class="py-10 text-center text-gray-400 text-xs">${msg}</td></tr>`;
  }

  function errorRow(colspan) {
    return `<tr><td colspan="${colspan}" class="py-10 text-center text-red-400 text-xs">Gagal memuat data.</td></tr>`;
  }

  // ===== TAMU HARIAN =====
  let harianData = [];
  async function loadTamuHarian() {
    const dari   = document.getElementById('harianDari').value;
    const sampai = document.getElementById('harianSampai').value;
    const tbody  = document.getElementById('harianBody');
    tbody.innerHTML = emptyRow(7, 'Memuat...');
    try {
      const res  = await fetch(`/api/laporan-tamu-harian.php?${new URLSearchParams({ dari, sampai })}`);
      const json = await res.json();
      harianData = json.data ?? [];
      document.getElementById('harianCount').textContent = harianData.length;
      if (!harianData.length) { tbody.innerHTML = emptyRow(7); return; }
      tbody.innerHTML = harianData.map((row, i) => `
        <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
          <td class="px-4 py-3 text-gray-400 text-xs">${i + 1}</td>
          <td class="px-4 py-3 text-gray-500 text-xs whitespace-nowrap">${formatDate(row.tanggal)}</td>
          <td class="px-4 py-3 font-semibold text-gray-800">${row.nama}</td>
          <td class="px-4 py-3 text-gray-500 text-xs">${row.instansi}</td>
          <td class="px-4 py-3">
            <span class="bg-red-50 text-red-800 text-xs font-semibold px-2.5 py-1 rounded-full">${row.kepentingan}</span>
          </td>
          <td class="px-4 py-3 text-gray-500 text-xs">${row.tujuan ?? '—'}</td>
          <td class="px-4 py-3">${statusBadge(row.status)}</td>
        </tr>
      `).join('');
    } catch { tbody.innerHTML = errorRow(7); }
  }

  // ===== TAMU BULANAN =====
  let bulananData = [];
  async function loadTamuBulanan() {
    const tahun = document.getElementById('bulananTahun').value;
    const tbody = document.getElementById('bulananBody');
    tbody.innerHTML = emptyRow(7, 'Memuat...');
    try {
      const res   = await fetch(`/api/laporan-tamu-bulanan.php?tahun=${tahun}`);
      const json  = await res.json();
      bulananData = json.data ?? [];
      document.getElementById('bulananCount').textContent = bulananData.length;
      if (!bulananData.length) { tbody.innerHTML = emptyRow(7); return; }
      tbody.innerHTML = bulananData.map((row, i) => `
        <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
          <td class="px-4 py-3 text-gray-400 text-xs">${i + 1}</td>
          <td class="px-4 py-3 font-semibold text-gray-800">${BULAN[parseInt(row.bulan) - 1]} ${tahun}</td>
          <td class="px-4 py-3 text-gray-800 font-bold">${row.total}</td>
          <td class="px-4 py-3"><span class="bg-green-50 text-green-700 text-xs font-semibold px-2.5 py-1 rounded-full">${row.selesai}</span></td>
          <td class="px-4 py-3"><span class="bg-blue-50 text-blue-700 text-xs font-semibold px-2.5 py-1 rounded-full">${row.diproses}</span></td>
          <td class="px-4 py-3"><span class="bg-yellow-50 text-yellow-700 text-xs font-semibold px-2.5 py-1 rounded-full">${row.menunggu}</span></td>
          <td class="px-4 py-3"><span class="bg-red-50 text-red-700 text-xs font-semibold px-2.5 py-1 rounded-full">${row.ditolak}</span></td>
        </tr>
      `).join('');
    } catch { tbody.innerHTML = errorRow(7); }
  }

  // ===== SURAT MASUK =====
  let suratData = [];
  async function loadSuratMasuk() {
    const dari   = document.getElementById('suratDari').value;
    const sampai = document.getElementById('suratSampai').value;
    const tbody  = document.getElementById('suratBody');
    tbody.innerHTML = emptyRow(6, 'Memuat...');
    try {
      const res  = await fetch(`/api/laporan-surat-masuk.php?${new URLSearchParams({ dari, sampai })}`);
      const json = await res.json();
      suratData  = json.data ?? [];
      document.getElementById('suratCount').textContent = suratData.length;
      if (!suratData.length) { tbody.innerHTML = emptyRow(6); return; }
      tbody.innerHTML = suratData.map((row, i) => `
        <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
          <td class="px-4 py-3 text-gray-400 text-xs">${i + 1}</td>
          <td class="px-4 py-3 text-gray-500 text-xs whitespace-nowrap">${formatDate(row.tanggal)}</td>
          <td class="px-4 py-3 text-gray-700 text-xs font-medium">${row.nomor_surat ?? '—'}</td>
          <td class="px-4 py-3 text-gray-700 text-xs">${row.pengirim}</td>
          <td class="px-4 py-3 text-gray-700 text-xs">${row.perihal}</td>
          <td class="px-4 py-3">${statusBadge(row.status)}</td>
        </tr>
      `).join('');
    } catch { tbody.innerHTML = errorRow(6); }
  }

  // ===== EXPORT CSV =====
  function exportCSV(type) {
    let rows = [], headers = [], filename = '';

    if (type === 'tamu-harian') {
      if (!harianData.length) return alert('Tidak ada data untuk di-export.');
      headers  = ['No','Tanggal','Nama','Instansi','Kepentingan','Tujuan','Status'];
      rows     = harianData.map((r, i) => [i+1, r.tanggal, r.nama, r.instansi, r.kepentingan, r.tujuan ?? '', r.status]);
      filename = 'laporan-tamu-harian.csv';
    } else if (type === 'tamu-bulanan') {
      if (!bulananData.length) return alert('Tidak ada data untuk di-export.');
      const tahun = document.getElementById('bulananTahun').value;
      headers  = ['No','Bulan','Total','Selesai','Diproses','Menunggu','Ditolak'];
      rows     = bulananData.map((r, i) => [i+1, `${BULAN[parseInt(r.bulan)-1]} ${tahun}`, r.total, r.selesai, r.diproses, r.menunggu, r.ditolak]);
      filename = `laporan-tamu-bulanan-${tahun}.csv`;
    } else if (type === 'surat-masuk') {
      if (!suratData.length) return alert('Tidak ada data untuk di-export.');
      headers  = ['No','Tanggal','No. Surat','Pengirim','Perihal','Status'];
      rows     = suratData.map((r, i) => [i+1, r.tanggal, r.nomor_surat ?? '', r.pengirim, r.perihal, r.status]);
      filename = 'laporan-surat-masuk.csv';
    }

    const csv = [headers, ...rows]
      .map(row => row.map(v => `"${String(v).replace(/"/g, '""')}"`).join(','))
      .join('\n');

    const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href     = url;
    a.download = filename;
    a.click();
    URL.revokeObjectURL(url);
  }

  // ===== EXPORT PDF =====
  function exportPDF(type) {
    let headers = [], rows = [], title = '', filename = '', subtitle = '';
    const now = new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });

    if (type === 'tamu-harian') {
      if (!harianData.length) return alert('Tidak ada data untuk di-export.');
      const dari   = document.getElementById('harianDari').value;
      const sampai = document.getElementById('harianSampai').value;
      title    = 'Laporan Tamu Harian';
      subtitle = dari && sampai ? `Periode: ${formatDate(dari)} — ${formatDate(sampai)}` : `Dicetak: ${now}`;
      headers  = [['No', 'Tanggal', 'Nama', 'Instansi', 'Kepentingan', 'Tujuan', 'Status']];
      rows     = harianData.map((r, i) => [i+1, formatDate(r.tanggal), r.nama, r.instansi, r.kepentingan, r.tujuan ?? '—', r.status]);
      filename = 'laporan-tamu-harian.pdf';

    } else if (type === 'tamu-bulanan') {
      if (!bulananData.length) return alert('Tidak ada data untuk di-export.');
      const tahun = document.getElementById('bulananTahun').value;
      title    = 'Laporan Tamu Per Bulan';
      subtitle = `Tahun: ${tahun}`;
      headers  = [['No', 'Bulan', 'Total', 'Selesai', 'Diproses', 'Menunggu', 'Ditolak']];
      rows     = bulananData.map((r, i) => [i+1, `${BULAN[parseInt(r.bulan)-1]} ${tahun}`, r.total, r.selesai, r.diproses, r.menunggu, r.ditolak]);
      filename = `laporan-tamu-bulanan-${tahun}.pdf`;

    } else if (type === 'surat-masuk') {
      if (!suratData.length) return alert('Tidak ada data untuk di-export.');
      const dari   = document.getElementById('suratDari').value;
      const sampai = document.getElementById('suratSampai').value;
      title    = 'Laporan Surat Masuk';
      subtitle = dari && sampai ? `Periode: ${formatDate(dari)} — ${formatDate(sampai)}` : `Dicetak: ${now}`;
      headers  = [['No', 'Tanggal', 'No. Surat', 'Pengirim', 'Perihal', 'Status']];
      rows     = suratData.map((r, i) => [i+1, formatDate(r.tanggal), r.nomor_surat ?? '—', r.pengirim, r.perihal, r.status]);
      filename = 'laporan-surat-masuk.pdf';
    }

    const doc = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });

    // ── Header block ──────────────────────────────────────────
    doc.setFillColor(127, 0, 0); // dark red / maroon
    doc.rect(0, 0, 297, 22, 'F');

    doc.setTextColor(255, 255, 255);
    doc.setFontSize(13);
    doc.setFont('helvetica', 'bold');
    doc.text('DPRD Kabupaten Jember', 14, 10);

    doc.setFontSize(9);
    doc.setFont('helvetica', 'normal');
    doc.text(title, 14, 17);

    // Date printed — top right
    doc.setFontSize(8);
    doc.text(`Dicetak: ${now}`, 283, 17, { align: 'right' });

    // ── Subtitle ──────────────────────────────────────────────
    doc.setTextColor(100, 100, 100);
    doc.setFontSize(8);
    doc.text(subtitle, 14, 29);

    // ── Table ─────────────────────────────────────────────────
    doc.autoTable({
      head: headers,
      body: rows,
      startY: 33,
      styles: {
        fontSize  : 8,
        cellPadding: 3,
        textColor : [40, 40, 40],
        lineColor : [220, 220, 220],
        lineWidth : 0.1,
      },
      headStyles: {
        fillColor  : [127, 0, 0],
        textColor  : [255, 255, 255],
        fontStyle  : 'bold',
        halign     : 'left',
      },
      alternateRowStyles: {
        fillColor: [250, 250, 250],
      },
      columnStyles: {
        0: { cellWidth: 10, halign: 'center' }, // No
      },
      margin: { left: 14, right: 14 },
      didDrawPage: (data) => {
        // Page number footer
        const pageCount = doc.internal.getNumberOfPages();
        doc.setFontSize(7);
        doc.setTextColor(160, 160, 160);
        doc.text(
          `Halaman ${data.pageNumber} dari ${pageCount}`,
          148.5, 205, { align: 'center' }
        );
      },
    });

    doc.save(filename);
  }

  // Init
  switchTab('tamu-harian');
</script>
</body>
</html>