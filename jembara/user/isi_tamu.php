<?php
$currentPage = "tamu";
$pageTitle = "Form Tamu";
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

  <main class="flex-1 p-4 md:p-6 space-y-6">

    <div>
      <h1 class="text-xl font-bold text-gray-800">Form Identitas Tamu</h1>
      <p class="text-gray-400 text-xs mt-1">Isi data tamu sebelum menemui anggota DPRD</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">

      <div id="alert" class="hidden mb-5 px-4 py-3 rounded-lg text-sm font-medium"></div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-5">

        <!-- Nama -->
        <div class="flex flex-col gap-1">
          <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Nama Lengkap</label>
          <input id="namaLengkap" type="text" placeholder="Masukkan nama lengkap"
            class="px-4 py-3 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-400 transition"/>
        </div>

        <!-- Instansi -->
        <div class="flex flex-col gap-1">
          <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Nama Instansi</label>
          <input id="namaInstansi" type="text" placeholder="Masukkan nama instansi"
            class="px-4 py-3 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-400 transition"/>
        </div>

        <!-- Kepentingan -->
        <div class="flex flex-col gap-1 sm:col-span-2">
          <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Kepentingan</label>
          <input id="kepentingan" type="text" placeholder="cth: Audiensi, Konsultasi, Rapat..."
            class="px-4 py-3 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-400 transition"/>
        </div>

        <!-- Tujuan -->
        <div class="flex flex-col gap-1 sm:col-span-2">
          <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
            Tujuan
            <span class="normal-case font-normal text-gray-400 ml-1">(menemui siapa / bagian apa)</span>
          </label>
          <input id="tujuan" type="text" placeholder="cth: Ketua DPRD, Komisi I, Bagian Umum..."
            class="px-4 py-3 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-400 transition"/>
        </div>

      </div>

      <!-- Hidden camera -->
      <div class="hidden">
        <video id="video" autoplay playsinline></video>
        <canvas id="canvas"></canvas>
      </div>

      <button id="btnSubmit"
        class="mt-6 w-full bg-red-800 hover:bg-red-900 active:bg-red-950 text-white font-bold py-3.5 rounded-xl text-sm transition flex items-center justify-center gap-2">
        <span id="btnText">Simpan Data</span>
        <svg id="btnSpinner" class="hidden animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
        </svg>
      </button>

    </div>
  </main>

  <?php require_once "includes/footer.php"; ?>
</div>

<!-- ===== LIGHTBOX ===== -->
<div id="lightbox" class="hidden fixed inset-0 bg-black/70 z-50 flex items-center justify-center" onclick="closeLightbox()">
  <button class="absolute top-4 right-4 bg-white w-9 h-9 rounded-full text-gray-800 font-bold text-lg flex items-center justify-center hover:bg-gray-100">✕</button>
  <img id="lightboxImg" src="" class="max-w-[90vw] max-h-[85vh] rounded-xl shadow-2xl"/>
</div>

<script src="/assets/app.js"></script>
<script>
  lucide.createIcons();

  function getNow() {
    const now = new Date();
    const pad = n => String(n).padStart(2, '0');
    return `${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())}T${pad(now.getHours())}:${pad(now.getMinutes())}`;
  }

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
      video.addEventListener('loadedmetadata', () => setTimeout(capturePhoto, 1000));
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
    setTimeout(() => el.classList.add('hidden'), 3000);
  }

  function openLightbox(src) { document.getElementById('lightboxImg').src = src; document.getElementById('lightbox').classList.remove('hidden'); }
  function closeLightbox()   { document.getElementById('lightbox').classList.add('hidden'); }

  // ===== SUBMIT =====
  document.getElementById('btnSubmit').addEventListener('click', async () => {
    const nama        = document.getElementById('namaLengkap').value.trim();
    const instansi    = document.getElementById('namaInstansi').value.trim();
    const kepentingan = document.getElementById('kepentingan').value.trim();
    const tujuan      = document.getElementById('tujuan').value.trim();
    const tanggal     = getNow();

    if (!nama)        return showAlert('Nama Lengkap wajib diisi!', 'error');
    if (!instansi)    return showAlert('Nama Instansi wajib diisi!', 'error');
    if (!kepentingan) return showAlert('Kepentingan wajib diisi!', 'error');
    if (!tujuan)      return showAlert('Tujuan wajib diisi!', 'error');

    document.getElementById('btnText').textContent = 'Menyimpan...';
    document.getElementById('btnSpinner').classList.remove('hidden');
    document.getElementById('btnSubmit').disabled = true;

    const formData = new FormData();
    formData.append('nama',        nama);
    formData.append('instansi',    instansi);
    formData.append('kepentingan', kepentingan);
    formData.append('tujuan',      tujuan);
    formData.append('tanggal',     tanggal);
    if (capturedImage) formData.append('foto', capturedImage);

    try {
      const res  = await fetch('/api/store-tamu.php', { method: 'POST', body: formData });
      const json = await res.json();

      if (json.success) {
        showAlert('Data tamu berhasil disimpan!');
        document.getElementById('namaLengkap').value  = '';
        document.getElementById('namaInstansi').value = '';
        document.getElementById('kepentingan').value  = '';
        document.getElementById('tujuan').value       = '';
        capturedImage = null;
        setTimeout(capturePhoto, 500);
      } else {
        showAlert(json.message ?? 'Gagal menyimpan data.', 'error');
      }
    } catch (err) {
      showAlert('Gagal terhubung ke server.', 'error');
      console.error(err);
    } finally {
      document.getElementById('btnText').textContent = 'Simpan Data';
      document.getElementById('btnSpinner').classList.add('hidden');
      document.getElementById('btnSubmit').disabled = false;
    }
  });
</script>
</body>
</html>
