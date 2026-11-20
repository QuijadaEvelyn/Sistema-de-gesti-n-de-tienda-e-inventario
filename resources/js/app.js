import './bootstrap';
import { initCoverflowCarousel } from './coverflow-carousel';

window.initCoverflowCarousel = initCoverflowCarousel;

/**
 * ==========================================================================
 * ARCHIVUM · MOTOR DE TRANSICIONES FLUIDAS SPA & BENTO GLASS DASHBOARD
 * Arquitectura Ultraligera Vanilla JS sin dependencias pesadas
 * ==========================================================================
 */

/**
 * Curva de aceleración easeOutExpo para conteo numérico progresivo fluido
 */
function easeOutExpo(x) {
    return x === 1 ? 1 : 1 - Math.pow(2, -10 * x);
}

/**
 * Animador numérico progresivo (Count-Up)
 * Duración: 850ms, respetando prefijos ($), decimales y puntuación localizada.
 */
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
 * Aplica animación de entrada escalonada (Staggered Bento Reveal)
 * Desfase de 55ms por tarjeta/bloque (index * 55ms) con transición cubic-bezier.
 */
export function applyStaggerAnimations(container) {
    if (!container) {
        return;
    }

    if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    // Buscar elementos explícitos o bloques principales dentro del contenedor
    let targets = Array.from(container.querySelectorAll('.admin-stagger-item, .bento-reveal'));

    if (targets.length === 0) {
        const directChildren = Array.from(container.children);
        let blocks = [];

        directChildren.forEach((child) => {
            if (
                child.classList.contains('space-y-6') ||
                child.classList.contains('space-y-8') ||
                child.classList.contains('space-y-4')
            ) {
                blocks.push(...Array.from(child.children));
            } else {
                blocks.push(child);
            }
        });

        // Desplegar tarjetas hijas si el contenedor es una grilla (ej. métricas KPI)
        const expandedBlocks = [];
        blocks.forEach((el) => {
            if (el.classList.contains('grid') && el.children.length > 0) {
                expandedBlocks.push(...Array.from(el.children));
            } else {
                expandedBlocks.push(el);
            }
        });

        targets = expandedBlocks.length > 0 ? expandedBlocks : blocks;
    }

    targets.forEach((el, index) => {
        el.classList.add('admin-stagger-item');
        el.style.setProperty('--stagger-delay', `${index * 55}ms`);
    });
}

/**
 * Actualiza inmediatamente el estado activo del Sidebar al hacer clic
 */
export function updateSidebarActiveState(targetUrl) {
    try {
        const parsedTarget = new URL(targetUrl, window.location.origin);
        const navLinks = document.querySelectorAll('aside nav a[href]');

        navLinks.forEach((link) => {
            try {
                const linkUrl = new URL(link.href, window.location.origin);
                const isMatch = linkUrl.pathname === parsedTarget.pathname;

                if (isMatch) {
                    link.classList.add('sidebar-link-glossy-active', 'font-label-md', 'shadow-xs');
                    link.classList.remove('sidebar-link-glossy-idle', 'font-body-md');
                } else {
                    link.classList.remove('sidebar-link-glossy-active', 'font-label-md', 'shadow-xs');
                    link.classList.add('sidebar-link-glossy-idle', 'font-body-md');
                }
            } catch (err) {}
        });
    } catch (e) {}
}

/**
 * Re-ejecuta scripts inline incluidos en las vistas cargadas asíncronamente
 */
export function executeInlineScripts(container) {
    if (!container) {
        return;
    }

    const scripts = container.querySelectorAll('script');
    scripts.forEach((oldScript) => {
        const newScript = document.createElement('script');
        Array.from(oldScript.attributes).forEach((attr) => {
            newScript.setAttribute(attr.name, attr.value);
        });
        newScript.textContent = oldScript.textContent;
        oldScript.parentNode.replaceChild(newScript, oldScript);
    });

    // Disparar eventos para que los manejadores de eventos se re-vinculen
    document.dispatchEvent(new Event('DOMContentLoaded'));
    window.dispatchEvent(new CustomEvent('admin:page-loaded'));
}

/**
 * Estado y control de navegación asíncrona SPA
 */
let isNavigating = false;

export async function navigateToAdminUrl(url, isPopState = false) {
    if (isNavigating) {
        return;
    }

    const mainContent = document.getElementById('admin-main-content');
    if (!mainContent) {
        window.location.href = url;
        return;
    }

    isNavigating = true;

    // Fase 1: Salida animada de la sección actual e indicador inmediato en el Sidebar
    updateSidebarActiveState(url);

    const isReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (!isReducedMotion) {
        mainContent.classList.add('admin-stage-exit');
    }

    const exitAnimationPromise = isReducedMotion
        ? Promise.resolve()
        : new Promise((resolve) => setTimeout(resolve, 220));

    try {
        // Fase 2: Carga asíncrona y reemplazo fluido del DOM
        const fetchPromise = fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html, application/xhtml+xml',
            },
        });

        const [response] = await Promise.all([fetchPromise, exitAnimationPromise]);

        if (!response.ok || response.redirected) {
            window.location.href = response.url || url;
            return;
        }

        const htmlText = await response.text();
        const parser = new DOMParser();
        const doc = parser.parseFromString(htmlText, 'text/html');

        const newMainContent = doc.getElementById('admin-main-content');
        if (!newMainContent) {
            window.location.href = url;
            return;
        }

        // Actualizar título del documento
        if (doc.title) {
            document.title = doc.title;
        }

        // Actualizar URL en el historial del navegador si no es navegación popstate
        if (!isPopState) {
            window.history.pushState({ url }, '', url);
        }

        // Sincronizar el contenido del sidebar y sus contadores/insignias dinámicas
        const newAside = doc.querySelector('aside');
        const currentAside = document.querySelector('aside');
        if (newAside && currentAside) {
            const currentScroll = currentAside.scrollTop;
            currentAside.innerHTML = newAside.innerHTML;
            currentAside.scrollTop = currentScroll;
        }

        // Reemplazar el contenido principal de trabajo
        mainContent.innerHTML = newMainContent.innerHTML;

        // Limpiar cualquier nodo huérfano que pudiera haber quedado fuera de admin-main-content en main-canvas
        const mainCanvas = document.getElementById('admin-main-canvas');
        if (mainCanvas) {
            Array.from(mainCanvas.children).forEach((child) => {
                if (
                    child.id !== 'admin-main-content' &&
                    !child.classList.contains('vault-ambient-background') &&
                    !child.hasAttribute('data-vault-ambient')
                ) {
                    child.remove();
                }
            });
        }

        // Fase 3: Entrada en cascada estilo Bento de la nueva sección
        window.scrollTo({ top: 0, behavior: 'instant' });
        mainContent.classList.remove('admin-stage-exit');

        // Aplicar animación escalonada (staggered reveal)
        applyStaggerAnimations(mainContent);

        // Re-ejecutar contadores numéricos (countUp)
        initCountUps();

        // Re-ejecutar scripts inline de la vista cargada
        executeInlineScripts(mainContent);

        // Notificar a listeners que la sección SPA fue reemplazada
        window.dispatchEvent(new CustomEvent('admin-spa-navigated'));
    } catch (error) {
        console.error('Error durante la navegación SPA:', error);
        window.location.href = url;
    } finally {
        isNavigating = false;
    }
}

/**
 * Inicializador de interceptores de navegación SPA
 */
export function initAdminSpaNavigation() {
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
            const targetUrl = new URL(link.href, window.location.origin);

            // Verificar que pertenezca al mismo origen
            if (targetUrl.origin !== window.location.origin) {
                return;
            }

            // Excluir expresamente descargas de archivos CSV, PDF o plantillas
            const isDownloadOrExport =
                targetUrl.pathname.includes('/exportar/') ||
                targetUrl.pathname.includes('plantilla') ||
                targetUrl.pathname.endsWith('.csv') ||
                targetUrl.pathname.endsWith('.pdf') ||
                targetUrl.pathname.endsWith('.zip');

            if (isDownloadOrExport) {
                return;
            }

            // Verificar si es una ruta administrativa interna o enlace de la barra lateral
            const adminPrefixes = ['/admin', '/almacen', '/ventas', '/compras'];
            const esRutaAdmin = adminPrefixes.some((prefix) => targetUrl.pathname.startsWith(prefix));
            const estaEnSidebar = !!link.closest('aside');

            if ((esRutaAdmin || estaEnSidebar) && targetUrl.href !== window.location.href) {
                event.preventDefault();
                navigateToAdminUrl(targetUrl.href);
            }
        } catch (e) {}
    });

    // Soporte para los botones Atrás / Adelante del navegador
    window.addEventListener('popstate', () => {
        const currentUrl = window.location.href;
        const adminPrefixes = ['/admin', '/almacen', '/ventas', '/compras'];
        const esRutaAdmin = adminPrefixes.some((prefix) => window.location.pathname.startsWith(prefix));

        if (esRutaAdmin) {
            navigateToAdminUrl(currentUrl, true);
        }
    });
}

/**
 * ==========================================================================
 * ARCHIVUM · RETROALIMENTACIÓN HÁPTICA SONORA DE ALTA DEFINICIÓN
 * Síntesis procedural pura con Web Audio API (Sin dependencias ni MP3 externos)
 * Clic táctil ultracorto, sutil y elegante (~38ms, 820Hz -> 320Hz, vol: 0.08)
 * ==========================================================================
 */
let audioCtx = null;
let soundEnabled = localStorage.getItem('archivum_sound_enabled') !== 'false';

function getAudioContext() {
    if (!audioCtx) {
        const AudioContextClass = window.AudioContext || window.webkitAudioContext;
        if (AudioContextClass) {
            audioCtx = new AudioContextClass();
        }
    }
    if (audioCtx && audioCtx.state === 'suspended') {
        audioCtx.resume();
    }
    return audioCtx;
}

export function playTactileClick() {
    if (!soundEnabled) {
        return;
    }
    try {
        const ctx = getAudioContext();
        if (!ctx) {
            return;
        }

        const now = ctx.currentTime;
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();

        // Forma de onda sinusoidal suave para simular pulsación mecánica refinada
        osc.type = 'sine';
        // Rampa de frecuencia descendente tipo micro-switch de cristal (820 Hz a 320 Hz)
        osc.frequency.setValueAtTime(820, now);
        osc.frequency.exponentialRampToValueAtTime(320, now + 0.038);

        // Curva de envolvente: ataque ultra rápido de 3ms, decaimiento exponencial a 0.0001
        gain.gain.setValueAtTime(0.001, now);
        gain.gain.linearRampToValueAtTime(0.075, now + 0.003);
        gain.gain.exponentialRampToValueAtTime(0.0001, now + 0.038);

        osc.connect(gain);
        gain.connect(ctx.destination);

        osc.start(now);
        osc.stop(now + 0.040);
    } catch (e) {
        // En navegadores que restrinjan audio previo a interacción
    }
}

export function toggleArchivumSound() {
    soundEnabled = !soundEnabled;
    localStorage.setItem('archivum_sound_enabled', soundEnabled ? 'true' : 'false');
    updateSoundToggleButtons();
    if (soundEnabled) {
        playTactileClick();
    }
    return soundEnabled;
}

function updateSoundToggleButtons() {
    const buttons = document.querySelectorAll('.sound-toggle-btn');
    buttons.forEach((btn) => {
        const icon = btn.querySelector('.material-symbols-outlined, span');
        if (icon) {
            icon.textContent = soundEnabled ? 'volume_up' : 'volume_off';
        }
        btn.setAttribute('title', soundEnabled ? 'Silenciar sonidos del sistema' : 'Activar sonidos táctiles');
        btn.setAttribute('aria-label', soundEnabled ? 'Silenciar sonidos' : 'Activar sonidos');
        if (soundEnabled) {
            btn.classList.remove('opacity-60');
        } else {
            btn.classList.add('opacity-60');
        }
    });
}

export function initArchivumSound() {
    // Desbloquear AudioContext al primer gesto del usuario
    const unlockEvents = ['pointerdown', 'keydown', 'touchstart'];
    const unlockHandler = () => {
        getAudioContext();
        unlockEvents.forEach((ev) => window.removeEventListener(ev, unlockHandler, { capture: true }));
    };
    unlockEvents.forEach((ev) => window.addEventListener(ev, unlockHandler, { capture: true, once: true }));

    // Delegación global para cualquier botón, píldora o enlace de navegación
    document.addEventListener('click', (event) => {
        const target = event.target;
        if (!target) {
            return;
        }

        const soundBtn = target.closest('.sound-toggle-btn');
        if (soundBtn) {
            event.preventDefault();
            toggleArchivumSound();
            return;
        }

        const interactive = target.closest(
            'button, .btn, [role="button"], a.btn-glass-purple, a.btn-glass-surface, ' +
            'nav a, #header-main-nav a, .sidebar-link-glossy-active, .sidebar-link-glossy-idle, ' +
            '#theme-toggle-btn, #admin-theme-toggle, .chart-range-pill, input[type="submit"]'
        );

        if (interactive) {
            playTactileClick();
        }
    });

    updateSoundToggleButtons();
}

window.playTactileClick = playTactileClick;
window.toggleArchivumSound = toggleArchivumSound;

// Inicialización automática al cargar el DOM inicial
function bootApp() {
    initCountUps();
    const mainContent = document.getElementById('admin-main-content');
    if (mainContent) {
        applyStaggerAnimations(mainContent);
    }
    initAdminSpaNavigation();
    initCoverflowCarousel();
    initArchivumSound();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootApp);
} else {
    bootApp();
}
