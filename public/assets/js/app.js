/* EduFolio - Interacciones y animaciones de interfaz */
(function () {
    'use strict';

    var prefiereReducido = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* Preloader / efecto de carga */
    var preloader = document.getElementById('preloader');
    function ocultarPreloader() {
        document.body.classList.remove('cargando');
        if (!preloader) return;
        preloader.classList.add('oculto');
        setTimeout(function () {
            if (preloader && preloader.parentNode) preloader.parentNode.removeChild(preloader);
        }, 600);
    }
    var esperaMin = prefiereReducido ? 0 : 550;
    window.addEventListener('load', function () { setTimeout(ocultarPreloader, esperaMin); });
    // Respaldo: nunca dejar la pantalla bloqueada
    setTimeout(ocultarPreloader, 3500);

    /* Scroll reveal (animate on scroll) */
    var revelables = document.querySelectorAll('.reveal');
    if (revelables.length) {
        if (prefiereReducido || !('IntersectionObserver' in window)) {
            revelables.forEach(function (el) { el.classList.add('visible'); });
        } else {
            var io = new IntersectionObserver(function (entradas) {
                entradas.forEach(function (en) {
                    if (en.isIntersecting) {
                        en.target.classList.add('visible');
                        io.unobserve(en.target);
                    }
                });
            }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });
            revelables.forEach(function (el) { io.observe(el); });
        }
    }

    /* Contadores animados */
    function animarContador(el) {
        var destino = parseInt(el.getAttribute('data-count'), 10);
        var sufijo = el.getAttribute('data-suffix') || '';
        if (prefiereReducido) { el.textContent = destino + sufijo; return; }
        var inicio = 0, dur = 1400, t0 = null;
        function paso(ts) {
            if (!t0) t0 = ts;
            var p = Math.min((ts - t0) / dur, 1);
            var val = Math.floor(inicio + (destino - inicio) * (1 - Math.pow(1 - p, 3)));
            el.textContent = val + sufijo;
            if (p < 1) requestAnimationFrame(paso);
        }
        requestAnimationFrame(paso);
    }

    var contadores = document.querySelectorAll('[data-count]');
    if (contadores.length && 'IntersectionObserver' in window) {
        var ioC = new IntersectionObserver(function (entradas) {
            entradas.forEach(function (en) {
                if (en.isIntersecting) { animarContador(en.target); ioC.unobserve(en.target); }
            });
        }, { threshold: 0.6 });
        contadores.forEach(function (el) { ioC.observe(el); });
    } else {
        contadores.forEach(animarContador);
    }

    /* Navbar dinamico al hacer scroll */
    var nav = document.getElementById('nav');
    if (nav) {
        var alScroll = function () { nav.classList.toggle('scrolled', window.scrollY > 30); };
        alScroll();
        window.addEventListener('scroll', alScroll, { passive: true });
    }

    /* Menu movil del landing */
    var burger = document.getElementById('navBurger');
    var navLinks = document.getElementById('navLinks');
    if (burger && navLinks) {
        burger.addEventListener('click', function () { navLinks.classList.toggle('abierto'); });
        navLinks.addEventListener('click', function (e) {
            if (e.target.tagName === 'A') navLinks.classList.remove('abierto');
        });
    }

    /* Barra lateral del panel (movil) */
    var toggle = document.getElementById('menuToggle');
    var sidebar = document.getElementById('sidebar');
    if (toggle && sidebar) {
        toggle.addEventListener('click', function (e) {
            e.stopPropagation();
            sidebar.classList.toggle('abierto');
        });
        document.addEventListener('click', function (ev) {
            if (window.innerWidth > 760) return;
            if (sidebar.contains(ev.target) || toggle.contains(ev.target)) return;
            sidebar.classList.remove('abierto');
        });
    }

    /* Auto-ocultar mensajes flash flotantes */
    document.querySelectorAll('.flash-flotante[data-flash]').forEach(function (el) {
        setTimeout(function () {
            el.style.transition = 'opacity .4s, transform .4s';
            el.style.opacity = '0';
            el.style.transform = 'translateX(-50%) translateY(-12px)';
            setTimeout(function () { el.remove(); }, 400);
        }, 4000);
    });
})();
