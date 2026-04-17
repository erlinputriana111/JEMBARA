<?php
// No auth_check — this is a public form for guests
$pageTitle = "Form Identitas Tamu"; ?>
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
<body class="bg-red-900 font-sans min-h-screen flex flex-col items-center justify-center py-10 px-4">

  <!-- Hidden camera — parent div hidden, video inside still streams -->
  <div class="hidden">
    <video id="video" autoplay playsinline></video>
    <canvas id="canvas"></canvas>
  </div>

  <!-- Admin Link -->
  <div class="fixed top-4 right-4 z-40">
    <a href="/admin/login.php"
      class="flex items-center gap-1.5 px-3 py-1.5 bg-white/10 hover:bg-white/20 border border-white/20 rounded-lg text-xs font-medium text-white/60 hover:text-white shadow-sm transition">
      <i data-lucide="lock" class="w-3 h-3"></i>
      Admin
    </a>
  </div>

  <div class="w-full max-w-xl space-y-6">

    <!-- Header -->
    <div class="flex flex-col items-center text-center space-y-2">
      <img src="/assets/logo.png" alt="Logo DPRD Jember" class="object-contain drop-shadow"/>
      <h1 class="text-xl font-extrabold text-white">Form Identitas Tamu</h1>
      <p class="text-red-200 text-xs">Isi data diri sebelum menemui anggota DPRD Kabupaten Jember</p>
    </div>

    <!-- Card -->
    <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8 space-y-5">

      <!-- Alert -->
      <div id="alert" class="hidden px-4 py-3 rounded-lg text-sm font-medium"></div>

      <!-- Nama -->
      <div class="flex flex-col gap-1.5">
        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
          Nama Lengkap <span class="text-red-500">*</span>
        </label>
        <input id="namaLengkap" type="text" placeholder="Masukkan nama lengkap" autocomplete="off"
          class="px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-400 transition"/>
      </div>

      <!-- Instansi -->
      <div class="flex flex-col gap-1.5">
        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
          Nama Instansi <span class="text-red-500">*</span>
        </label>
        <input id="namaInstansi" type="text" placeholder="Masukkan nama instansi / lembaga" autocomplete="off"
          class="px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-400 transition"/>
      </div>

      <!-- Kepentingan -->
      <div class="flex flex-col gap-1.5">
        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
          Kepentingan <span class="text-red-500">*</span>
        </label>
        <input id="kepentingan" type="text" placeholder="cth: Audiensi, Konsultasi, Rapat..." autocomplete="off"
          class="px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-400 transition"/>
      </div>

      <!-- Tujuan -->
      <div class="flex flex-col gap-1.5">
        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
          Tujuan <span class="text-red-500">*</span>
        </label>
        <input id="tujuan" type="text" placeholder="cth: Ketua DPRD, Komisi I, Bagian Umum..." autocomplete="off"
          class="px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-400 transition"/>
      </div>

      <!-- Submit -->
      <button id="btnSubmit"
        class="w-full bg-red-800 hover:bg-red-900 active:bg-red-950 text-white font-bold py-3.5 rounded-xl text-sm transition flex items-center justify-center gap-2">
        <i data-lucide="save" class="w-4 h-4"></i>
        <span id="btnText">Simpan Data</span>
        <svg id="btnSpinner" class="hidden animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
        </svg>
      </button>

    </div>

    <p class="text-center text-red-300 text-xs">
      &copy; <?= date("Y") ?> DPRD Kabupaten Jember. All rights reserved.
    </p>

  </div>

  <!-- SUCCESS OVERLAY -->
  <div id="successOverlay"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm px-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-8 text-center space-y-4">

        <div class="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
          <i data-lucide="check-circle" class="w-8 h-8 text-green-600"></i>
        </div>

      <h2 class="text-lg font-bold text-gray-800">Data Tersimpan!</h2>
      <p class="text-gray-400 text-sm">
        Terima kasih, <span id="successNama" class="font-semibold text-gray-700"></span>,
        telah mengisi Form.
      </p>
      <button onclick="closeSuccessOverlay()"
        class="w-full mt-2 bg-red-800 hover:bg-red-900 text-white font-bold py-3 rounded-xl text-sm transition">
        Kembali
      </button>
    </div>
  </div>

  <script>
    lucide.createIcons();

    const video  = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    let capturedImage = null;
    let activeStream  = null;

    // ── Camera — mirrors the working tamu.php approach ─────────
    async function startCamera() {
      capturedImage = null;
      try {
        activeStream = await navigator.mediaDevices.getUserMedia({
          video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } }
        });
        video.srcObject = activeStream;
        // Use loadedmetadata + 1s delay — same as working tamu.php
        video.addEventListener('loadedmetadata', () => setTimeout(capturePhoto, 1000), { once: true });
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

    function stopCamera() {
      if (activeStream) {
        activeStream.getTracks().forEach(t => t.stop());
        activeStream = null;
        video.srcObject = null;
      }
    }

    // ── Utilities ──────────────────────────────────────────────
    function getNow() {
      const now = new Date();
      const pad = n => String(n).padStart(2, '0');
      return `${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())}T${pad(now.getHours())}:${pad(now.getMinutes())}`;
    }

    function showAlert(message, type = 'success') {
      const el = document.getElementById('alert');
      el.className = `px-4 py-3 rounded-lg text-sm font-medium ${
        type === 'success'
          ? 'bg-green-50 text-green-700 border border-green-200'
          : 'bg-red-50 text-red-700 border border-red-200'
      }`;
      el.innerHTML = message;
      el.classList.remove('hidden');
      setTimeout(() => el.classList.add('hidden'), 4000);
    }

    function setLoading(state) {
      document.getElementById('btnText').textContent = state ? 'Menyimpan...' : 'Simpan Data';
      document.getElementById('btnSpinner').classList.toggle('hidden', !state);
      document.getElementById('btnSubmit').disabled = state;
    }

    function openSuccessOverlay(nama) {
      document.getElementById('successNama').textContent = nama;
      const overlay = document.getElementById('successOverlay');
      overlay.classList.remove('hidden');
      overlay.classList.add('flex');
      lucide.createIcons();
    }
    function closeSuccessOverlay() {
      document.getElementById('successOverlay').classList.add('hidden');
      document.getElementById('successOverlay').classList.remove('flex');
      startCamera(); // warm up for next guest
    }

    // ── Submit ─────────────────────────────────────────────────
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

      // Re-capture at submit in case 1s auto-capture missed
      capturePhoto();
      const foto = capturedImage;
      stopCamera();

      setLoading(true);

      const formData = new FormData();
      formData.append('nama',        nama);
      formData.append('instansi',    instansi);
      formData.append('kepentingan', kepentingan);
      formData.append('tujuan',      tujuan);
      formData.append('tanggal',     tanggal);
      if (foto) formData.append('foto', foto);

      try {
        const res  = await fetch('/api/store-tamu.php', { method: 'POST', body: formData });
        const json = await res.json();

        if (json.success) {
          document.getElementById('namaLengkap').value  = '';
          document.getElementById('namaInstansi').value = '';
          document.getElementById('kepentingan').value  = '';
          document.getElementById('tujuan').value       = '';
          openSuccessOverlay(nama);
        } else {
          showAlert(json.message ?? 'Gagal menyimpan data.', 'error');
          startCamera();
        }
      } catch (err) {
        showAlert('Gagal terhubung ke server.', 'error');
        startCamera();
        console.error(err);
      } finally {
        setLoading(false);
      }
    });

    // ── Boot ───────────────────────────────────────────────────
    startCamera();
  </script>
</body>
</html>
