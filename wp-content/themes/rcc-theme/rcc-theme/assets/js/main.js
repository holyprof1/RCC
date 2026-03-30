document.addEventListener('DOMContentLoaded', function () {
  var body = document.body;
  var header = document.getElementById('rcc-header');
  var toggle = document.getElementById('rcc-menu-toggle');
  var mobileNav = document.getElementById('rcc-mobile-nav');
  var preloader = document.getElementById('rcc-preloader');

  if (body && preloader) {
    var loaderKey = 'rcc_home_loader_seen';
    var shouldShowLoader = false;

    try {
      shouldShowLoader = !window.sessionStorage.getItem(loaderKey);
    } catch (e) {
      shouldShowLoader = true;
    }

    if (shouldShowLoader) {
      preloader.classList.add('is-active');
      body.classList.add('rcc-loading');

      var loaderClosed = false;
      var pending = 0;

      var closePreloader = function () {
        if (loaderClosed) { return; }
        loaderClosed = true;
        preloader.classList.add('is-hidden');
        preloader.classList.remove('is-active');
        body.classList.remove('rcc-loading');
        try { window.sessionStorage.setItem(loaderKey, '1'); } catch (e) {}
      };

      var tick = function () {
        pending = Math.max(0, pending - 1);
        if (pending === 0) { closePreloader(); }
      };

      // Hide broken event logos in the preloader itself
      preloader.querySelectorAll('img').forEach(function (img) {
        img.addEventListener('error', function () {
          var ev = this.closest('.rcc-preloader__event');
          if (ev) { ev.style.display = 'none'; }
        });
      });

      // Wait for every <img> on the page to finish loading
      document.querySelectorAll('img').forEach(function (img) {
        if (!img.complete || !img.naturalWidth) {
          pending++;
          img.addEventListener('load',  tick, { once: true });
          img.addEventListener('error', tick, { once: true });
        }
      });

      // Also preload the hero background-image (it's a CSS background, not an <img>)
      var heroBg = document.querySelector('.rcc-home-hero');
      if (heroBg) {
        var bgMatch = (heroBg.style.backgroundImage || '').match(/url\(['"]?([^'")\s]+)['"]?\)/);
        if (bgMatch && bgMatch[1]) {
          pending++;
          var bgImg = new Image();
          bgImg.onload  = tick;
          bgImg.onerror = tick;
          bgImg.src = bgMatch[1];
        }
      }

      if (pending === 0) {
        // Everything already cached — short polished delay then close
        window.setTimeout(closePreloader, 600);
      }

      // Safety net: never block the user longer than 7 seconds
      window.setTimeout(closePreloader, 7000);
    } else {
      preloader.classList.add('is-hidden');
      preloader.classList.remove('is-active');
    }
  }

  if (header) {
    function syncHeader() {
      header.classList.toggle('is-scrolled', window.scrollY > 30);
    }

    window.addEventListener('scroll', syncHeader, { passive: true });
    syncHeader();
  }

  if (toggle && mobileNav) {
    toggle.addEventListener('click', function () {
      var isOpen = mobileNav.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
  }

  var reveals = document.querySelectorAll('.rcc-reveal');
  if ('IntersectionObserver' in window && reveals.length) {
    var revealObserver = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry, i) {
        if (entry.isIntersecting) {
          setTimeout(function () {
            entry.target.classList.add('is-visible');
          }, i * 90);
          revealObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1 });

    reveals.forEach(function (el) {
      revealObserver.observe(el);
    });
  } else {
    reveals.forEach(function (el) {
      el.classList.add('is-visible');
    });
  }

  document.querySelectorAll('.rcc-carousel').forEach(function (carousel) {
    var track = carousel.querySelector('.rcc-carousel__track');
    var trackWrap = carousel.querySelector('.rcc-carousel__track-wrap');
    var slides = carousel.querySelectorAll('.rcc-carousel__slide');
    var btnPrev = carousel.querySelector('.rcc-carousel__prev');
    var btnNext = carousel.querySelector('.rcc-carousel__next');
    var dotsWrap = carousel.querySelector('.rcc-carousel__dots');

    if (!track || !trackWrap || !slides.length || !dotsWrap) {
      return;
    }

    var current = 0;

    function getVisible() {
      return window.innerWidth < 640 ? 1 : window.innerWidth < 960 ? 2 : 3;
    }

    function getMax() {
      return Math.max(0, slides.length - getVisible());
    }

    var dots = [];
    dotsWrap.innerHTML = '';

    for (var d = 0; d <= getMax(); d++) {
      var dot = document.createElement('button');
      dot.className = 'rcc-carousel__dot' + (d === 0 ? ' is-active' : '');
      dot.setAttribute('aria-label', 'Go to slide ' + (d + 1));
      (function (idx) {
        dot.addEventListener('click', function () {
          goTo(idx);
        });
      })(d);
      dotsWrap.appendChild(dot);
      dots.push(dot);
    }

    function goTo(idx) {
      current = Math.max(0, Math.min(idx, getMax()));
      var wrapW = trackWrap.offsetWidth;
      var visible = getVisible();
      track.style.transform = 'translateX(-' + (current * wrapW / visible) + 'px)';

      dots.forEach(function (dot, i) {
        dot.classList.toggle('is-active', i === current);
      });

      if (btnPrev) {
        btnPrev.disabled = current <= 0;
      }
      if (btnNext) {
        btnNext.disabled = current >= getMax();
      }
    }

    if (btnPrev) {
      btnPrev.addEventListener('click', function () {
        goTo(current - 1);
      });
    }

    if (btnNext) {
      btnNext.addEventListener('click', function () {
        goTo(current + 1);
      });
    }

    window.addEventListener('resize', function () {
      goTo(current);
    }, { passive: true });

    setTimeout(function () {
      goTo(0);
    }, 60);
  });

  var eventNav = document.querySelector('.rcc-event-nav');
  if (eventNav && 'IntersectionObserver' in window) {
    var navLinks = eventNav.querySelectorAll('a[href^="#"]');
    var sectionObserver = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          var id = entry.target.getAttribute('id');
          navLinks.forEach(function (link) {
            link.classList.toggle('is-active', link.getAttribute('href') === '#' + id);
          });
        }
      });
    }, { rootMargin: '-40% 0px -55% 0px', threshold: 0 });

    navLinks.forEach(function (link) {
      var target = document.querySelector(link.getAttribute('href'));
      if (target) {
        sectionObserver.observe(target);
      }
    });
  }
});
