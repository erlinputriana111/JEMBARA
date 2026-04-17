<?php
$currentPage = "surat_masuk";
$pageTitle = "Tambah Surat Masuk";
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

    <div class="flex items-center gap-3">
      <a href="/admin/surat_masuk.php" class="text-gray-400 hover:text-gray-600 transition">
        <i data-lucide="arrow-left" class="w-5 h-5"></i>
      </a>
      <div>
        <h1 class="text-xl font-bold text-gray-800">Tambah Surat Masuk</h1>
        <p class="text-gray-400 text-xs mt-0.5">Isi data surat masuk yang diterima</p>
      </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">

      <div id="alert" class="hidden mb-5 px-4 py-3 rounded-lg text-sm font-medium"></div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-5">

        <!-- ===== SEARCHABLE TAMU DROPDOWN ===== -->
        <div class="flex flex-col gap-1 sm:col-span-2">
          <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
            Nama Tamu
            <span class="normal-case font-normal text-gray-400 ml-1">(opsional — pilih dari buku tamu)</span>
          </label>

          <!-- Hidden real value -->
          <input type="hidden" id="namaTamu" value=""/>

          <!-- Search input -->
          <div class="relative" id="tamuDropdownWrapper">
            <div class="relative">
              <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 pointer-events-none"></i>
              <input
                id="tamuSearch"
                type="text"
                placeholder="Cari nama tamu atau instansi..."
                autocomplete="off"
                class="w-full pl-9 pr-10 py-3 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-400 transition"/>
              <!-- Clear button -->
              <button type="button" id="btnClearTamu"
                class="hidden absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 rounded-full bg-gray-200 hover:bg-red-100 hover:text-red-500 flex items-center justify-center transition text-gray-400">
                <i data-lucide="x" class="w-3 h-3"></i>
              </button>
            </div>

            <!-- Selected badge — shown when a tamu is picked -->
            <div id="tamuSelected"
              class="hidden mt-2 flex items-center justify-between gap-2 bg-red-50 border border-red-200 rounded-lg px-3 py-2">
              <div class="flex items-center gap-2 min-w-0">
                <i data-lucide="user-check" class="w-4 h-4 text-red-800 shrink-0"></i>
                <span id="tamuSelectedLabel" class="text-xs font-semibold text-red-800 truncate"></span>
              </div>
              <button type="button" id="btnDeselectTamu"
                class="shrink-0 text-red-400 hover:text-red-600 transition">
                <i data-lucide="x" class="w-4 h-4"></i>
              </button>
            </div>

            <!-- Dropdown list -->
            <div id="tamuDropdown"
              class="hidden absolute z-30 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden">

              <!-- Dropdown inner scroll -->
              <ul id="tamuList" class="max-h-52 overflow-y-auto divide-y divide-gray-50 text-sm"></ul>

              <!-- Empty state -->
              <div id="tamuEmpty" class="hidden px-4 py-6 text-center text-gray-400 text-xs">
                Tidak ada tamu yang cocok.
              </div>
            </div>
          </div>
        </div>

        <!-- Pengirim -->
        <div class="flex flex-col gap-1 sm:col-span-2">
          <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Pengirim Surat</label>
          <input id="pengirim" type="text" placeholder="cth: Universitas Muhammadiyah Jember"
            class="px-4 py-3 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-400 transition"/>
        </div>

        <!-- Perihal -->
        <div class="flex flex-col gap-1 sm:col-span-2">
          <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Perihal Surat</label>
          <input id="perihal" type="text" placeholder="cth: Permohonan Magang"
            class="px-4 py-3 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-400 transition"/>
        </div>

        <!-- Tanggal Surat -->
        <div class="flex flex-col gap-1">
          <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Tanggal Surat</label>
          <input id="tanggalSurat" type="date"
            class="px-4 py-3 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-400 transition"/>
        </div>

        <!-- Tanggal Diterima -->
        <div class="flex flex-col gap-1">
          <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Tanggal Diterima</label>
          <input id="tanggalDiterima" type="date"
            class="px-4 py-3 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-400 transition"/>
        </div>

        <!-- Status -->
        <div class="flex flex-col gap-1">
          <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Status Surat</label>
          <select id="status"
            class="px-4 py-3 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-400 transition bg-white">
            <option value="frontdesk">Frontdesk</option>
            <option value="agendaris">Agendaris</option>
            <option value="setwan">Setwan</option>
            <option value="pimpinan dprd">Pimpinan DPRD</option>
            <option value="keuangan">Keuangan</option>
            <option value="umum">Umum</option>
            <option value="pengawasan">Pengawasan</option>
            <option value="fasilitasi dan penganggaran">Fasilitasi dan Penganggaran</option>
            <option value="selesai">Selesai</option>
          </select>
        </div>

        <!-- Posisi -->
        <div class="flex flex-col gap-1">
          <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Posisi Surat</label>
          <select id="posisi"
            class="px-4 py-3 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-400 transition bg-white">
            <option value="Frontdesk">Frontdesk</option>
            <option value="Pimpinan">Pimpinan</option>
            <option value="Sekretariat">Sekretariat</option>
            <option value="Komisi I">Komisi I</option>
            <option value="Komisi II">Komisi II</option>
            <option value="Komisi III">Komisi III</option>
            <option value="Komisi IV">Komisi IV</option>
            <option value="Arsip">Arsip</option>
          </select>
        </div>

        <!-- Upload File -->
        <div class="flex flex-col gap-1 sm:col-span-2">
          <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
            Upload File
            <span class="normal-case font-normal text-gray-400 ml-1">(PDF · maks. 10MB)</span>
          </label>
          <label id="dropZone"
            class="flex flex-col items-center justify-center gap-2 border-2 border-dashed border-gray-200 rounded-xl p-6 cursor-pointer hover:border-red-400 hover:bg-red-50 transition group">
            <i data-lucide="upload-cloud" class="w-8 h-8 text-gray-300 group-hover:text-red-400 transition"></i>
            <span class="text-xs text-gray-400 group-hover:text-red-500 transition">Klik atau seret file ke sini</span>
            <input id="fileSurat" type="file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="hidden"/>
          </label>
          <div id="filePreview" class="hidden mt-2 flex items-center justify-between gap-3 bg-gray-50 border border-gray-200 rounded-lg px-4 py-3">
            <div class="flex items-center gap-2 min-w-0">
              <i id="fileIcon" data-lucide="file-text" class="w-5 h-5 text-red-800 shrink-0"></i>
              <span id="fileName" class="text-xs text-gray-700 font-medium truncate"></span>
              <span id="fileSize" class="text-xs text-gray-400 shrink-0"></span>
            </div>
            <button type="button" id="btnRemoveFile"
              class="shrink-0 w-6 h-6 rounded-full bg-gray-200 hover:bg-red-100 hover:text-red-500 flex items-center justify-center transition text-gray-500">
              <i data-lucide="x" class="w-3 h-3"></i>
            </button>
          </div>
        </div>

      </div><!-- /grid -->

      <button id="btnSubmit"
        class="mt-6 w-full bg-red-800 hover:bg-red-900 active:bg-red-950 text-white font-bold py-3.5 rounded-xl text-sm transition flex items-center justify-center gap-2">
        <span id="btnText">Simpan Surat</span>
        <svg id="btnSpinner" class="hidden animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
        </svg>
      </button>

    </div>
  </main>
  <?php require_once "includes/footer.php"; ?>
</div>

<script src="/assets/app.js"></script>
<script>
  lucide.createIcons();

  // ===== SEARCHABLE TAMU DROPDOWN =====
  let tamuAll = []; // full list cache

  const tamuSearch       = document.getElementById('tamuSearch');
  const tamuDropdown     = document.getElementById('tamuDropdown');
  const tamuList         = document.getElementById('tamuList');
  const tamuEmpty        = document.getElementById('tamuEmpty');
  const tamuSelected     = document.getElementById('tamuSelected');
  const tamuSelectedLabel= document.getElementById('tamuSelectedLabel');
  const tamuHiddenInput  = document.getElementById('namaTamu');
  const btnClearTamu     = document.getElementById('btnClearTamu');
  const btnDeselectTamu  = document.getElementById('btnDeselectTamu');

  async function loadTamuOptions() {
    try {
      const res  = await fetch('/api/get-tamu-list.php');
      const json = await res.json();
      tamuAll    = json.data ?? [];
      renderTamuList(tamuAll);
    } catch {}
  }

  function highlight(text, query) {
    if (!query) return text;
    const re = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
    return text.replace(re, '<mark class="bg-red-100 text-red-800 rounded">$1</mark>');
  }

  function renderTamuList(items) {
    const q = tamuSearch.value.trim();
    tamuList.innerHTML = '';

    if (!items.length) {
      tamuList.classList.add('hidden');
      tamuEmpty.classList.remove('hidden');
      return;
    }

    tamuEmpty.classList.add('hidden');
    tamuList.classList.remove('hidden');

    // "— Tidak terkait tamu —" option at the top
    const noneItem = document.createElement('li');
    noneItem.innerHTML = `<span class="italic text-gray-400">— Tidak terkait tamu —</span>`;
    noneItem.className = 'px-4 py-3 cursor-pointer hover:bg-gray-50 transition text-sm';
    noneItem.addEventListener('click', () => selectTamu('', ''));
    tamuList.appendChild(noneItem);

    items.forEach(t => {
      const li = document.createElement('li');
      li.className = 'px-4 py-3 cursor-pointer hover:bg-red-50 transition';
      li.innerHTML = `
        <div class="flex items-center gap-2">
          <i data-lucide="user" class="w-4 h-4 text-gray-300 shrink-0"></i>
          <div class="min-w-0">
            <p class="text-sm font-semibold text-gray-800 truncate">${highlight(t.nama, q)}</p>
            <p class="text-xs text-gray-400 truncate">${highlight(t.instansi, q)}</p>
          </div>
        </div>
      `;
      li.addEventListener('click', () => selectTamu(t.id, `${t.nama} — ${t.instansi}`));
      tamuList.appendChild(li);
    });

    lucide.createIcons();
  }

  function selectTamu(id, label) {
    tamuHiddenInput.value = id;
    closeDropdown();

    if (id) {
      tamuSearch.value = '';
      btnClearTamu.classList.add('hidden');
      tamuSelectedLabel.textContent = label;
      tamuSelected.classList.remove('hidden');
    } else {
      tamuSearch.value = '';
      tamuSelected.classList.add('hidden');
    }
    lucide.createIcons();
  }

  function openDropdown() {
    tamuDropdown.classList.remove('hidden');
  }

  function closeDropdown() {
    tamuDropdown.classList.add('hidden');
  }

  // Search input handler
  tamuSearch.addEventListener('input', () => {
    const q       = tamuSearch.value.trim().toLowerCase();
    const filtered = q
      ? tamuAll.filter(t =>
          t.nama.toLowerCase().includes(q) ||
          t.instansi.toLowerCase().includes(q)
        )
      : tamuAll;

    btnClearTamu.classList.toggle('hidden', !tamuSearch.value);
    renderTamuList(filtered);
    openDropdown();
  });

  tamuSearch.addEventListener('focus', () => {
    renderTamuList(tamuAll);
    openDropdown();
  });

  // Clear search
  btnClearTamu.addEventListener('click', () => {
    tamuSearch.value = '';
    btnClearTamu.classList.add('hidden');
    renderTamuList(tamuAll);
    tamuSearch.focus();
  });

  // Deselect selected tamu
  btnDeselectTamu.addEventListener('click', () => {
    tamuHiddenInput.value = '';
    tamuSelected.classList.add('hidden');
    tamuSearch.value = '';
    tamuSearch.focus();
  });

  // Close dropdown on outside click
  document.addEventListener('click', e => {
    if (!document.getElementById('tamuDropdownWrapper').contains(e.target)) {
      closeDropdown();
    }
  });

  // Keyboard navigation
  tamuSearch.addEventListener('keydown', e => {
    const items = tamuList.querySelectorAll('li');
    const active = tamuList.querySelector('li.bg-red-50');
    let idx = Array.from(items).indexOf(active);

    if (e.key === 'ArrowDown') {
      e.preventDefault();
      if (active) active.classList.remove('bg-red-50');
      idx = (idx + 1) % items.length;
      items[idx]?.classList.add('bg-red-50');
      items[idx]?.scrollIntoView({ block: 'nearest' });
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      if (active) active.classList.remove('bg-red-50');
      idx = (idx - 1 + items.length) % items.length;
      items[idx]?.classList.add('bg-red-50');
      items[idx]?.scrollIntoView({ block: 'nearest' });
    } else if (e.key === 'Enter') {
      e.preventDefault();
      active?.click();
    } else if (e.key === 'Escape') {
      closeDropdown();
    }
  });

  loadTamuOptions();

  // ===== DEFAULTS =====
  document.getElementById('tanggalDiterima').value = new Date().toISOString().split('T')[0];

  // ===== FILE =====
  const fileInput   = document.getElementById('fileSurat');
  const dropZone    = document.getElementById('dropZone');
  const filePreview = document.getElementById('filePreview');
  const fileNameEl  = document.getElementById('fileName');
  const fileSizeEl  = document.getElementById('fileSize');
  const fileIconEl  = document.getElementById('fileIcon');
  const MAX_SIZE    = 10 * 1024 * 1024;

  const ICON_MAP = {
    pdf: 'file-text', doc: 'file-text', docx: 'file-text',
    jpg: 'image', jpeg: 'image', png: 'image',
  };

  function formatBytes(b) {
    if (b < 1024) return b + ' B';
    if (b < 1024*1024) return (b/1024).toFixed(1) + ' KB';
    return (b/(1024*1024)).toFixed(1) + ' MB';
  }

  function showFilePreview(file) {
    const ext = file.name.split('.').pop().toLowerCase();
    fileNameEl.textContent = file.name;
    fileSizeEl.textContent = formatBytes(file.size);
    fileIconEl.setAttribute('data-lucide', ICON_MAP[ext] ?? 'file');
    filePreview.classList.remove('hidden');
    lucide.createIcons();
  }

  function clearFile() {
    fileInput.value = '';
    fileNameEl.textContent = '';
    fileSizeEl.textContent = '';
    filePreview.classList.add('hidden');
  }

  dropZone.addEventListener('click', () => fileInput.click());
  dropZone.addEventListener('dragover',  e => { e.preventDefault(); dropZone.classList.add('border-red-400','bg-red-50'); });
  dropZone.addEventListener('dragleave', () => dropZone.classList.remove('border-red-400','bg-red-50'));
  dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.classList.remove('border-red-400','bg-red-50');
    const file = e.dataTransfer.files[0];
    if (!file) return;
    if (file.size > MAX_SIZE) return showAlert('File maksimal 10MB.', 'error');
    const dt = new DataTransfer(); dt.items.add(file); fileInput.files = dt.files;
    showFilePreview(file);
  });
  fileInput.addEventListener('change', () => {
    const file = fileInput.files[0];
    if (!file) return;
    if (file.size > MAX_SIZE) { showAlert('File maksimal 10MB.', 'error'); clearFile(); return; }
    showFilePreview(file);
  });
  document.getElementById('btnRemoveFile').addEventListener('click', clearFile);

  // ===== ALERT =====
  function showAlert(message, type = 'success') {
    const el = document.getElementById('alert');
    el.className = `mb-5 px-4 py-3 rounded-lg text-sm font-medium ${
      type === 'success'
        ? 'bg-green-50 text-green-700 border border-green-200'
        : 'bg-red-50 text-red-700 border border-red-200'
    }`;
    el.innerHTML = message;
    el.classList.remove('hidden');
    setTimeout(() => el.classList.add('hidden'), 3500);
  }

  // ===== SUBMIT =====
  document.getElementById('btnSubmit').addEventListener('click', async () => {
    const pengirim        = document.getElementById('pengirim').value.trim();
    const perihal         = document.getElementById('perihal').value.trim();
    const tanggalSurat    = document.getElementById('tanggalSurat').value;
    const tanggalDiterima = document.getElementById('tanggalDiterima').value;
    const status          = document.getElementById('status').value;
    const posisi          = document.getElementById('posisi').value;
    const tamuId          = tamuHiddenInput.value;
    const file            = fileInput.files[0] ?? null;

    if (!pengirim)        return showAlert('Pengirim wajib diisi!', 'error');
    if (!perihal)         return showAlert('Perihal wajib diisi!', 'error');
    if (!tanggalSurat)    return showAlert('Tanggal surat wajib diisi!', 'error');
    if (!tanggalDiterima) return showAlert('Tanggal diterima wajib diisi!', 'error');

    document.getElementById('btnText').textContent = 'Menyimpan...';
    document.getElementById('btnSpinner').classList.remove('hidden');
    document.getElementById('btnSubmit').disabled = true;

    const formData = new FormData();
    formData.append('pengirim',         pengirim);
    formData.append('perihal',          perihal);
    formData.append('tanggal_surat',    tanggalSurat);
    formData.append('tanggal_diterima', tanggalDiterima);
    formData.append('status',           status);
    formData.append('posisi',           posisi);
    if (tamuId) formData.append('tamu_id', tamuId);
    if (file)   formData.append('file_surat', file);

    try {
      const res  = await fetch('/api/store-surat.php', { method: 'POST', body: formData });
      const json = await res.json();
      if (json.success) {
        showAlert('Surat berhasil disimpan!');
        setTimeout(() => window.location.href = '/admin/surat_masuk.php', 1500);
      } else {
        showAlert(json.message ?? 'Gagal menyimpan surat.', 'error');
      }
    } catch {
      showAlert('Gagal terhubung ke server.', 'error');
    } finally {
      document.getElementById('btnText').textContent = 'Simpan Surat';
      document.getElementById('btnSpinner').classList.add('hidden');
      document.getElementById('btnSubmit').disabled = false;
    }
  });
</script>
</body>
</html>
