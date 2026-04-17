<?php
$currentPage = 'anggota';   // highlights the correct sidebar menu
$pageTitle   = 'Anggota DPRD';
require_once 'includes/auth_check.php';
?>
<!DOCTYPE html>
<html lang="id">
    <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
      <title>Dashboard — DPRD Kabupaten Jember</title>
      <script src="https://cdn.tailwindcss.com"></script>
      <!-- Icons -->
      <script src="https://unpkg.com/lucide@latest"></script>
    </head>
<body class="bg-gray-100 font-sans">

<?php require_once 'includes/sidebar.php'; ?>

<div id="mainContent" class="ml-64 flex flex-col min-h-screen transition-all duration-300">
  <?php require_once 'includes/topbar.php'; ?>

  <main class="flex-1 p-6">
    <!-- Your page content here -->
  </main>

  <?php require_once 'includes/footer.php'; ?>
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script src="/assets/app.js"></script>
</body>
</html>
