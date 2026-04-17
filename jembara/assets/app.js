// Init Lucide icons
lucide.createIcons();

// Live clock
function updateClock() {
  const now = new Date();
  const clock = document.getElementById('clockDisplay');
  if (clock) {
    clock.textContent =
      now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })
      + ' — '
      + now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
  }
}
updateClock();
setInterval(updateClock, 1000);

// Sidebar toggle
function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  const main    = document.getElementById('mainContent');
  const overlay = document.getElementById('overlay');

  if (window.innerWidth >= 1024) {
    sidebar.classList.toggle('-translate-x-full');
    main.classList.toggle('ml-64');
    main.classList.toggle('ml-0');
  } else {
    sidebar.classList.toggle('-translate-x-full');
    overlay.classList.toggle('hidden');
  }
}

// Hide sidebar on mobile by default
if (window.innerWidth < 1024) {
  const sidebar = document.getElementById('sidebar');
  if (sidebar) sidebar.classList.add('-translate-x-full');
}
