<?php
session_start();
if (isset($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}
$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login — DPRD Kabupaten Jember</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="apple-touch-icon" sizes="180x180" href="/assets/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="/assets/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/assets/favicon-16x16.png">
  <link rel="manifest" href="/assets/site.webmanifest">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            maroon: {
              50:  '#fdf2f2',
              100: '#fce4e4',
              200: '#f9c0c0',
              500: '#e02424',
              600: '#8b0000',
              700: '#7a0000',
              800: '#650000',
            }
          }
        }
      }
    }
  </script>
</head>
<body class="min-h-screen bg-gradient-to-br from-maroon-800 to-maroon-600 flex items-center justify-center px-4">

  <div class="w-full max-w-md">

    <!-- Logo & Header -->
    <div class="flex flex-col items-center mb-8">
      <img
        src="../assets/logo.png"
        alt="Logo Kabupaten Jember"
        class="object-contain mb-3 drop-shadow-lg"
      />
      <h1 class="text-white text-2xl font-bold tracking-wide text-center">JEMBARA</h1>
      <p class="text-maroon-200 text-sm mt-1">Sistem Informasi Internal</p>
    </div>

    <!-- Card -->
    <div class="bg-white rounded-2xl shadow-2xl p-8">

      <h2 class="text-gray-800 text-xl font-semibold mb-1">Selamat Datang</h2>
      <p class="text-gray-400 text-sm mb-6">Masuk dengan akun yang telah terdaftar</p>

      <?php if ($error): ?>
        <div class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg px-4 py-3 mb-5">
          <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10A8 8 0 1 1 2 10a8 8 0 0 1 16 0zm-7-4a1 1 0 1 0-2 0v4a1 1 0 0 0 2 0V6zm-1 8a1 1 0 1 0 0-2 1 1 0 0 0 0 2z" clip-rule="evenodd"/>
          </svg>
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <form action="auth.php" method="POST" class="space-y-5">

        <!-- Username -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1" for="username">
            Username
          </label>
          <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0zM12 14a7 7 0 0 0-7 7h14a7 7 0 0 0-7-7z"/>
              </svg>
            </span>
            <input
              type="text"
              id="username"
              name="username"
              required
              placeholder="Masukkan username"
              class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-maroon-600 focus:border-transparent transition"
            />
          </div>
        </div>

        <!-- Password -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1" for="password">
            Password
          </label>
          <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0-1.1.9-2 2-2s2 .9 2 2-.9 2-2 2-2-.9-2-2zm-6 8a6 6 0 0 1 12 0H6z"/>
              </svg>
            </span>
            <input
              type="password"
              id="password"
              name="password"
              required
              placeholder="Masukkan password"
              class="w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-maroon-600 focus:border-transparent transition"
            />
            <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
              <svg id="eyeIcon" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Remember Me -->
        <div class="flex items-center gap-2">
          <input type="checkbox" id="remember" name="remember" class="accent-maroon-600 w-4 h-4" />
          <label for="remember" class="text-sm text-gray-600">Ingat saya</label>
        </div>

        <!-- Submit -->
        <button
          type="submit"
          class="w-full bg-maroon-600 hover:bg-maroon-700 text-white font-semibold py-2.5 rounded-lg transition duration-200 text-sm tracking-wide"
        >
          Masuk
        </button>

      </form>

      <!-- Divider -->
      <div class="flex items-center gap-3 my-5">
        <div class="flex-1 h-px bg-gray-200"></div>
        <span class="text-gray-400 text-xs">atau</span>
        <div class="flex-1 h-px bg-gray-200"></div>
      </div>

      <!-- Register Link -->
      <a
        href="register.php"
        class="flex items-center justify-center gap-2 w-full border border-maroon-600 text-maroon-600 hover:bg-maroon-50 font-semibold py-2.5 rounded-lg transition duration-200 text-sm tracking-wide"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 1 1-8 0 4 4 0 0 1 8 0zM3 20a6 6 0 0 1 12 0v1H3v-1z"/>
        </svg>
        Daftar Akun Baru
      </a>

    </div>

    <p class="text-center text-maroon-200 text-xs mt-6">
      &copy; <?= date('Y') ?> DPRD Kabupaten Jember. All rights reserved.
    </p>
  </div>

  <script>
    function togglePassword() {
      const input  = document.getElementById('password');
      const icon   = document.getElementById('eyeIcon');
      const isHidden = input.type === 'password';
      input.type = isHidden ? 'text' : 'password';
      icon.innerHTML = isHidden
        ? `<path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0 1 12 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 0 1 2.01-3.49M6.53 6.53A9.97 9.97 0 0 1 12 5c4.477 0 8.268 2.943 9.542 7a10.05 10.05 0 0 1-1.364 2.53M3 3l18 18"/>`
        : `<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
    }
  </script>
</body>
</html>
