<?php
$currentPage = $currentPage ?? '';
$menus = [
    ['page' => 'dashboard',   'href' => '/admin/dashboard.php',   'icon' => 'layout-dashboard', 'label' => 'Dashboard'],
    ['page' => 'data_tamu',   'href' => '/admin/data_tamu.php',   'icon' => 'users',             'label' => 'Data Tamu'],
    ['page' => 'track_surat', 'href' => '/admin/track_surat.php', 'icon' => 'mail-open',         'label' => 'Track Surat'],
    ['page' => 'surat_masuk', 'href' => '/admin/surat_masuk.php', 'icon' => 'mail-open',         'label' => 'Surat Masuk'],
    ['page' => 'laporan',     'href' => '/admin/laporan.php',     'icon' => 'file-bar-chart',    'label' => 'Laporan'],
];

// Admin-only menu items appended if role is admin
// if (isset($user['role']) && $user['role'] === 'admin') {
//     $menus[] = ['page' => 'pengaturan', 'href' => '/admin/pengaturan.php', 'icon' => 'settings',  'label' => 'Pengaturan'];
//     $menus[] = ['page' => 'users',      'href' => '/admin/users.php',       'icon' => 'user-cog',  'label' => 'Manajemen User'];
// }
?>

<aside id="sidebar" class="fixed top-0 left-0 h-full w-64 bg-red-950 text-white flex flex-col z-40 transition-transform duration-300">

  <!-- Logo -->
  <div class="flex items-center gap-3 px-5 py-5 border-b border-red-900">
    <img src="/assets/logo_dashboard.png" alt="Logo" class="w-10 h-10 object-contain"/>
    <div>
      <p class="font-bold text-sm leading-tight">JEMBARA</p>
      <p class="text-red-300 text-xs">Sistem Informasi</p>
    </div>
  </div>

  <!-- Nav -->
  <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">

    <p class="text-red-400 text-xs font-semibold uppercase px-3 mb-2">Menu Utama</p>

    <?php foreach ($menus as $menu):
      $active = $currentPage === $menu['page']
        ? 'bg-red-800 text-white'
        : 'hover:bg-red-900 text-red-100';
    ?>
    <a href="<?= $menu['href'] ?>"
      class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $active ?> text-sm font-medium transition">
      <i data-lucide="<?= $menu['icon'] ?>" class="w-4 h-4 shrink-0"></i>
      <?= $menu['label'] ?>
    </a>
    <?php endforeach; ?>

  </nav>

  <!-- User Info -->
  <div class="px-4 py-4 border-t border-red-900 flex items-center gap-3">
    <div class="w-9 h-9 rounded-full bg-red-800 flex items-center justify-center text-white font-bold text-sm shrink-0">
      <?= strtoupper(substr($user['nama'], 0, 1)) ?>
    </div>
    <div class="flex-1 min-w-0">
      <p class="text-sm font-semibold text-white truncate"><?= htmlspecialchars($user['nama']) ?></p>
      <p class="text-xs text-red-300 capitalize"><?= htmlspecialchars($user['role']) ?></p>
    </div>
    <a href="logout.php" title="Logout" class="text-red-300 hover:text-white transition">
      <i data-lucide="log-out" class="w-4 h-4"></i>
    </a>
  </div>

</aside>

<!-- Mobile Overlay -->
<div id="overlay" onclick="toggleSidebar()"
  class="fixed inset-0 bg-black/40 z-30 hidden lg:hidden"></div>
