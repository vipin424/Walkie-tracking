(function () {
  const sidebar = document.getElementById('sidebar');
  const content = document.getElementById('content');
  const mobileToggle = document.getElementById('sidebarToggle');
  const desktopToggle = document.getElementById('sidebarToggleDesktop');

  // Mobile toggle
  if (mobileToggle) {
    mobileToggle.addEventListener('click', () => {
      sidebar.classList.toggle('open');
    });
  }

  // Desktop collapse/expand
  if (desktopToggle) {
    desktopToggle.addEventListener('click', () => {
      const collapsed = sidebar.classList.toggle('collapsed');
      if (collapsed) {
        content.classList.add('expanded');
      } else {
        content.classList.remove('expanded');
      }
    });
  }

  // Close sidebar on outside click (mobile)
  document.addEventListener('click', (e) => {
    if (window.innerWidth < 992) {
      if (!sidebar.contains(e.target) && !mobileToggle?.contains(e.target)) {
        sidebar.classList.remove('open');
      }
    }
  });
})();
