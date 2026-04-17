<?php $title = "Form Tamu"; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Form Identitas Tamu</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-10 px-4 font-sans">
<div class="max-w-4xl mx-auto space-y-6">

  <h1 class="text-2xl font-extrabold text-center text-gray-800">Form Identitas Tamu</h1>

  <!-- ===== FORM CARD ===== -->
  <div class="bg-white rounded-2xl shadow-sm p-7">

    <!-- Alert -->
    <div id="alert" class="hidden mb-5 px-4 py-3 rounded-lg text-sm font-medium"></div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

      <!-- Nama -->
      <div class="flex flex-col gap-1">
        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Nama Lengkap</label>
        <input id="namaLengkap" type="text" placeholder="Masukkan nama lengkap"
          class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 transition"/>
      </div>

      <!-- Instansi -->
      <div class="flex flex-col gap-1">
        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Nama Instansi</label>
        <input id="namaInstansi" type="text" placeholder="Masukkan nama instansi"
          class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 transition"/>
      </div>

      <!-- Kepentingan -->
      <div class="flex flex-col gap-1">
        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Kepentingan</label>
        <input id="kepentingan" type="text" placeholder="cth: Audiensi, Konsultasi, Rapat..."
          class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 transition"/>
      </div>

    </div>

    <!-- Hidden camera -->
    <div class="hidden">
      <video id="video" autoplay playsinline></video>
      <canvas id="canvas"></canvas>
    </div>

    <button id="btnSubmit"
      class="mt-6 w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl text-sm transition flex items-center justify-center gap-2">
      <span id="btnText">✅ Simpan Data</span>
      <svg id="btnSpinner" class="hidden animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
      </svg>
    </button>
  </div>

  <!-- ===== TABLE CARD ===== -->
  <!--<div class="bg-white rounded-2xl shadow-sm p-7">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
      <h2 class="text-base font-bold text-gray-800">
        Data Tamu
        <span id="rowCount" class="ml-2 bg-indigo-600 text-white text-xs font-bold px-2.5 py-0.5 rounded-full">0</span>
      </h2>
      <input id="searchInput" type="text" placeholder="🔍 Cari nama / instansi..."
        class="px-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 w-full sm:w-56 transition"
        oninput="loadData()"/>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="bg-gray-50 text-gray-400 text-xs uppercase tracking-wide border-b border-gray-100">
            <th class="text-left px-4 py-3 font-semibold">#</th>
            <th class="text-left px-4 py-3 font-semibold">Foto</th>
            <th class="text-left px-4 py-3 font-semibold">Nama Lengkap</th>
            <th class="text-left px-4 py-3 font-semibold">Instansi</th>
            <th class="text-left px-4 py-3 font-semibold">Kepentingan</th>
            <th class="text-left px-4 py-3 font-semibold">Tanggal</th>
            <th class="text-left px-4 py-3 font-semibold">Aksi</th>
          </tr>
        </thead>
        <tbody id="tableBody">
          <tr>
            <td colspan="7" class="text-center text-gray-400 py-10">Memuat data...</td>
          </tr>
        </tbody>
      </table>
    </div>

  </div>-->
</div>

<!-- ===== LIGHTBOX ===== -->
<div id="lightbox" class="hidden fixed inset-0 bg-black/70 z-50 flex items-center justify-center" onclick="closeLightbox()">
  <button class="absolute top-4 right-4 bg-white w-9 h-9 rounded-full text-gray-800 font-bold text-lg flex items-center justify-center hover:bg-gray-100">✕</button>
  <img id="lightboxImg" src="" class="max-w-[90vw] max-h-[85vh] rounded-xl shadow-2xl" />
</div>

<script>


// ===== DEFAULT DATETIME =====
function getNow() {
  const now = new Date();
  // Format: YYYY-MM-DDTHH:mm (required by datetime-local input)
  const pad = n => String(n).padStart(2, '0');
  return `${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())}T${pad(now.getHours())}:${pad(now.getMinutes())}`;
}
document.getElementById('tanggalDibuat').value = getNow();

  // ===== CAMERA =====
  const video  = document.getElementById('video');
  const canvas = document.getElementById('canvas');
  let capturedImage = null;

  async function startCamera() {
    try {
      const stream = await navigator.mediaDevices.getUserMedia({
        video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } }
      });
      video.srcObject = stream;
      video.addEventListener('loadedmetadata', () => {
        setTimeout(capturePhoto, 1000);
      });
    } catch (err) {
      console.warn('Kamera tidak tersedia:', err.message);
    }
  }

  function capturePhoto() {
    if (!video.videoWidth) return;
    canvas.width  = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);
    capturedImage = canvas.toDataURL('image/jpeg', 0.9);
  }

  startCamera();

  // ===== FORMAT DATE =====
  function formatDate(dateStr) {
    if (!dateStr) return '—';
    return new Date(dateStr).toLocaleDateString('id-ID', {
      day: 'numeric', month: 'long', year: 'numeric'
    });
  }

  // ===== ALERT =====
  function showAlert(message, type = 'success') {
    const alert = document.getElementById('alert');
    alert.className = `mb-5 px-4 py-3 rounded-lg text-sm font-medium ${
      type === 'success'
        ? 'bg-green-50 text-green-700 border border-green-200'
        : 'bg-red-50 text-red-700 border border-red-200'
    }`;
    alert.textContent = message;
    alert.classList.remove('hidden');
    setTimeout(() => alert.classList.add('hidden'), 3000);
  }

  // ===== LOAD DATA =====
  async function loadData() {
    const search = document.getElementById('searchInput').value;
    const tbody  = document.getElementById('tableBody');

    try {
      const res  = await fetch(`api/get-tamu.php?search=${encodeURIComponent(search)}`);
      const json = await res.json();

      document.getElementById('rowCount').textContent = json.data.length;

      if (!json.data.length) {
        tbody.innerHTML = `<tr><td colspan="7" class="text-center text-gray-400 py-10">
          ${search ? 'Data tidak ditemukan.' : 'Belum ada data tamu.'}
        </td></tr>`;
        return;
      }

      tbody.innerHTML = json.data.map((row, i) => `
        <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
          <td class="px-4 py-3 text-gray-400">${i + 1}</td>
          <td class="px-4 py-3">
            ${row.foto
              ? `<img src="${row.foto}" onclick="openLightbox('${row.foto}')"
                  class="w-10 h-10 rounded-lg object-cover border border-gray-200 cursor-pointer hover:scale-110 transition-transform" />`
              : `<div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-300 text-xs">–</div>`
            }
          </td>
          <td class="px-4 py-3 font-semibold text-gray-700">${row.nama}</td>
          <td class="px-4 py-3 text-gray-500">${row.instansi}</td>
          <td class="px-4 py-3">
            <span class="bg-indigo-50 text-indigo-700 text-xs font-semibold px-2.5 py-1 rounded-full">
              ${row.kepentingan}
            </span>
          </td>
          <td class="px-4 py-3 text-gray-500 whitespace-nowrap">${formatDate(row.tanggal)}</td>
          <td class="px-4 py-3">
            <button onclick="deleteRow(${row.id})"
              class="bg-red-50 hover:bg-red-100 text-red-600 text-xs font-semibold px-3 py-1.5 rounded-lg transition">
              🗑 Hapus
            </button>
          </td>
        </tr>
      `).join('');

    } catch (err) {
      tbody.innerHTML = `<tr><td colspan="7" class="text-center text-red-400 py-10">Gagal memuat data.</td></tr>`;
    }
  }

  // ===== DELETE =====
  async function deleteRow(id) {
    if (!confirm('Hapus data ini?')) return;
    try {
      const res  = await fetch(`api/delete-tamu.php?id=${id}`, { method: 'DELETE' });
      const json = await res.json();
      if (json.success) {
        showAlert('Data berhasil dihapus.');
        loadData();
      } else {
        showAlert(json.message, 'error');
      }
    } catch {
      showAlert('Gagal menghapus data.', 'error');
    }
  }

  // ===== LIGHTBOX =====
  function openLightbox(src) {
    document.getElementById('lightboxImg').src = src;
    document.getElementById('lightbox').classList.remove('hidden');
  }

  function closeLightbox() {
    document.getElementById('lightbox').classList.add('hidden');
  }

  // ===== SUBMIT =====
  document.getElementById('btnSubmit').addEventListener('click', async () => {
    const nama        = document.getElementById('namaLengkap').value.trim();
    const instansi    = document.getElementById('namaInstansi').value.trim();
    const kepentingan = document.getElementById('kepentingan').value.trim();
    const tanggal     = document.getElementById('tanggalDibuat').value;

    if (!nama)        return showAlert('Nama Lengkap wajib diisi!', 'error');
    if (!instansi)    return showAlert('Nama Instansi wajib diisi!', 'error');
    if (!kepentingan) return showAlert('Kepentingan wajib diisi!', 'error');
    if (!tanggal)     return showAlert('Tanggal wajib diisi!', 'error');

    // Loading state
    document.getElementById('btnText').textContent = 'Menyimpan...';
    document.getElementById('btnSpinner').classList.remove('hidden');
    document.getElementById('btnSubmit').disabled = true;

    try {
      const res  = await fetch('api/store-tamu.php', {
        method : 'POST',
        headers: { 'Content-Type': 'application/json' },
        body   : JSON.stringify({ nama, instansi, kepentingan, tanggal, foto: capturedImage }),
      });
      const json = await res.json();

      if (json.success) {
        showAlert('Data tamu berhasil disimpan!');
        // Reset form
        document.getElementById('namaLengkap').value  = '';
        document.getElementById('namaInstansi').value = '';
        document.getElementById('kepentingan').value  = '';
        document.getElementById('tanggalDibuat').value = new Date().toISOString().split('T')[0];
        capturedImage = null;
        setTimeout(capturePhoto, 500);
        loadData();
        document.querySelector('.bg-white.rounded-2xl.shadow-sm.p-7:last-child')
          .scrollIntoView({ behavior: 'smooth' });
      } else {
        showAlert(json.message, 'error');
      }
    } catch {
      showAlert('Gagal terhubung ke server.', 'error');
    } finally {
      document.getElementById('btnText').textContent = '✅ Simpan Data';
      document.getElementById('btnSpinner').classList.add('hidden');
      document.getElementById('btnSubmit').disabled = false;
    }
  });

  // Initial load
  loadData();
</script>
</body>
</html>
