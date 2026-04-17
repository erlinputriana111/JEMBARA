<?php
// $pageTitle should be set before including this file
// e.g. $pageTitle = 'Dashboard';
$pageTitle = $pageTitle ?? 'Halaman';
?>

<header class="sticky top-0 z-30 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between shadow-sm">
  <div class="flex items-center gap-4">
    <!-- Hamburger -->
    <button onclick="toggleSidebar()" class="text-gray-500 hover:text-green-700 transition">
      <i data-lucide="menu" class="w-5 h-5"></i>
    </button>
    <div>
      <h1 class="text-gray-800 font-semibold text-base"><?= htmlspecialchars($pageTitle) ?></h1>
      <p class="text-gray-400 text-xs">Selamat datang, <?= htmlspecialchars($user['nama']) ?></p>
    </div>
  </div>

  <div class="flex items-center gap-4">
    <!-- Live Clock -->
    <span id="clockDisplay" class="text-sm text-gray-500 hidden sm:block"></span>

    <!-- Notification Bell -->


   
  </div>
</header>
