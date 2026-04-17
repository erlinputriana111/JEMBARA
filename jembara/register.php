<?php
session_start();
if (isset($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}
$old   = $_SESSION['old']   ?? [];
$error = $_SESSION['error'] ?? null;
unset($_SESSION['old'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Registrasi — DPRD Kabupaten Jember</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="apple-touch-icon" sizes="180x180" href="/assets/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="/assets/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/assets/favicon-16x16.png">
  <link rel="manifest" href="/assets/site.webmanifest">
</head>
<body class="min-h-screen bg-gradient-to-br from-red-900 to-red-700 flex items-center justify-center px-4">

  <div class="w-full max-w-md">

    <div class="flex flex-col items-center mb-8">
      <img src="assets/logo.png" alt="Logo Kabupaten Jember"
        class="object-contain mb-3 drop-shadow-lg"/>
      <h1 class="text-white text-2xl font-bold tracking-wide text-center">JEMBARA</h1>
      <p class="text-red-200 text-sm mt-1">Sistem Informasi Internal</p>
    </div>

    <div class="bg-white rounded-2xl shadow-2xl p-8">
      <h2 class="text-gray-800 text-xl font-semibold mb-1">Buat Akun Baru</h2>
      <p class="text-gray-400 text-sm mb-6">Untuk pegawai internal yang telah mendapatkan izin</p>

      <?php if ($error): ?>
        <div class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg px-4 py-3 mb-5">
          <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10A8 8 0 1 1 2 10a8 8 0 0 1 16 0zm-7-4a1 1 0 1 0-2 0v4a1 1 0 0 0 2 0V6zm-1 8a1 1 0 1 0 0-2 1 1 0 0 0 0 2z" clip-rule="evenodd"/>
          </svg>
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <form action="register_process.php" method="POST" class="space-y-4">

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1" for="nama">Nama Lengkap</label>
          <input
            type="text" id="nama" name="nama" required
            value="<?= htmlspecialchars($old['nama'] ?? '') ?>"
            placeholder="Nama lengkap"
            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1" for="username">Username</label>
          <input
            type="text" id="username" name="username" required
            value="<?= htmlspecialchars($old['username'] ?? '') ?>"
            placeholder="Username login"
            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1" for="email">Email (opsional)</label>
          <input
            type="email" id="email" name="email"
            value="<?= htmlspecialchars($old['email'] ?? '') ?>"
            placeholder="email@contoh.com"
            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1" for="password">Password</label>
          <input
            type="password" id="password" name="password" required
            minlength="6"
            placeholder="Minimal 6 karakter"
            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1" for="password_confirmation">Konfirmasi Password</label>
          <input
            type="password" id="password_confirmation" name="password_confirmation" required
            minlength="6"
            placeholder="Ulangi password"
            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition"
          />
        </div>

        <button
          type="submit"
          class="w-full bg-red-800 hover:bg-red-900 text-white font-semibold py-2.5 rounded-lg transition duration-200 text-sm tracking-wide"
        >
          Daftar
        </button>

        <p class="text-xs text-gray-500 text-center mt-2">
          Sudah punya akun?
          <a href="login.php" class="text-red-700 font-semibold hover:underline">Masuk di sini</a>
        </p>

      </form>
    </div>

    <p class="text-center text-red-200 text-xs mt-6">
      &copy; <?= date('Y') ?> DPRD Kabupaten Jember. All rights reserved.
    </p>
  </div>
</body>
</html>