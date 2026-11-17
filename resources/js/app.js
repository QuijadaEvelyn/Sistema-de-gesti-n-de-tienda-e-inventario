import './bootstrap';

/**
 * ==========================================================================
 * SISTEMA DE ANIMACIONES E INTERACCIONES FLUIDAS BENTO / GLASS DASHBOARD
 * ARCHIVUM · Minimalista, Ultra-Ligero, Sin Dependencias Externas
 * ==========================================================================
 */

/**
 * Animador Numérico Progresivo (Count-Up) con Easing easeOutExpo
 * Duración: 850ms, respetando formatos de moneda ($), comas y decimales.
 */
function easeOutExpo(x) {
    return x === 1 ? 1 : 1 - Math.pow(2, -10 * x);
}

function animateCountUpElement(element) {
    if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    const targetVal = parseFloat(element.getAttribute('data-countup'));
    if (isNaN(targetVal)) {
        return;
    }

    const prefix = element.getAttribute('data-prefix') || '';
    const decimals = parseInt(element.getAttribute('data-decimals') || '0', 10);
    const duration = 850;
    const startTime = performance.now();

    function formatNumber(num) {
        return num.toLocaleString('en-US', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals,
        });
    }

    function step(now) {
        const elapsed = now - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const eased = easeOutExpo(progress);
        const current = eased * targetVal;

        element.textContent = prefix + formatNumber(current);

        if (progress < 1) {
            requestAnimationFrame(step);
        } else {
            element.textContent = prefix + formatNumber(targetVal);
        }
    }

    requestAnimationFrame(step);
}

export function initCountUps() {
    const elements = document.querySelectorAll('[data-countup]');
    elements.forEach((el) => {
        animateCountUpElement(el);
    });
}

/**
 * Transición Suave entre Vistas y Filtros (SPA-like Page Exit Transition)
 * Aplica opacidad y ligero desplazamiento antes de navegar en el área administrativa.
 */
export function initPageTransitions() {
    if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    const mainContainer = document.getElementById('admin-main-canvas') || document.querySelector('main');
    if (!mainContainer) {
        return;
    }

    // Interceptar clics en enlaces dentro de aside nav y filtros de temporalidad
    document.addEventListener('click', (event) => {
        const link = event.target.closest('a');
        if (!link || !link.href) {
            return;
        }

        // Ignorar descargas, enlaces a nueva pestaña, anclas locales o modificadores de teclado
        if (
            link.target === '_blank' ||
            link.hasAttribute('download') ||
            link.getAttribute('href').startsWith('#') ||
            link.getAttribute('href').startsWith('javascript:') ||
            event.ctrlKey || event.metaKey || event.shiftKey || event.altKey
        ) {
            return;
        }

        try {
            const url = new URL(link.href, window.location.origin);
            if (url.origin !== window.location.origin) {
                return;
            }

            // Solo aplicar transición para navegación dentro del panel administrativo
            const adminPrefixes = ['/admin', '/almacen', '/ventas', '/compras'];
            const esRutaAdmin = adminPrefixes.some((prefix) => url.pathname.startsWith(prefix));

            if (esRutaAdmin && url.href !== window.location.href) {
                event.preventDefault();
                mainContainer.style.transition = 'opacity 200ms ease, transform 200ms ease';
                mainContainer.style.opacity = '0.4';
                mainContainer.style.transform = 'translateY(4px)';

                setTimeout(() => {
                    window.location.href = link.href;
                }, 130);
            }
        } catch (e) {}
    });

    // Interceptar cambios en el selector de sucursales o períodos
    const branchSelects = document.querySelectorAll('#kpi-branch-select, #branch-select');
    branchSelects.forEach((select) => {
        select.addEventListener('change', () => {
            mainContainer.style.transition = 'opacity 200ms ease, transform 200ms ease';
            mainContainer.style.opacity = '0.4';
            mainContainer.style.transform = 'translateY(4px)';
        });
    });
}

// Inicialización automática al cargar el DOM
document.addEventListener('DOMContentLoaded', () => {
    initCountUps();
    initPageTransitions();
});
