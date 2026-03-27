document.addEventListener('DOMContentLoaded', function () {
  var header = document.getElementById('rcc-header');
  var toggle = document.getElementById('rcc-menu-toggle');
  var mobileNav = document.getElementById('rcc-mobile-nav');

  if (header) {
    var syncHeader = function () {
      header.classList.toggle('is-scrolled', window.scrollY > 24);
    };

    window.addEventListener('scroll', syncHeader, { passive: true });
    syncHeader();
  }

  if (toggle && mobileNav) {
    toggle.addEventListener('click', function () {
      var isOpen = mobileNav.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
  }
});
