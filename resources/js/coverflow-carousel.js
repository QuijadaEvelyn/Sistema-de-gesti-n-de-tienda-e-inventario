/**
 * ==========================================================================
 * ARCHIVUM · CARRUSEL 3D COVERFLOW INTERACTIVO PURO
 * Diseño 100% limpio sin flechas ni barras accesorias.
 * Interacción directa sobre las tarjetas: clic directo en laterales,
 * arrastre fluido (mouse drag & touch swipe) y rueda/trackpad
 * con perspectiva 1200px y rotación 3D fluida.
 * ==========================================================================
 */

export function initCoverflowCarousel() {
    const stage = document.getElementById('vitrina-coverflow-carousel');
    if (!stage) {
        return;
    }

    const anchors = Array.from(stage.querySelectorAll('.coverflow-slot-anchor'));
    const total = anchors.length;
    if (total === 0) {
        return;
    }

    let activeIndex = 0;

    function getSpacingX() {
        const w = window.innerWidth;
        if (w < 640) {
            return 165; // Móvil
        }
        if (w < 1024) {
            return 260; // Tablet
        }
        if (w < 1280) {
            return 310; // Portátil
        }
        return 335; // Escritorio amplio
    }

    function updatePositions() {
        const spacingX = getSpacingX();

        anchors.forEach((anchor, idx) => {
            const slot = anchor.querySelector('.coverflow-slot');
            const card = anchor.querySelector('.coverflow-card');
            if (!slot) {
                return;
            }

            let diff = idx - activeIndex;

            // Envoltura circular continua
            if (total > 3) {
                while (diff > total / 2) {
                    diff -= total;
                }
                while (diff < -total / 2) {
                    diff += total;
                }
            } else if (total === 3) {
                if (diff === 2) diff = -1;
                if (diff === -2) diff = 1;
            } else if (total === 2) {
                if (diff === -1) diff = 1;
            }

            let posX = 0;
            let posZ = 0;
            let rotY = 0;
            let scale = 1;
            let zIndex = 1;
            let opacity = 1;
            let isVisible = true;

            if (diff === 0) {
                // Tarjeta central activa: en plano Z=0 exacto (scale(1), sin blur ni escalado que distorsione la rasterización)
                posX = 0;
                posZ = 0;
                rotY = 0;
                scale = 1;
                zIndex = 30;
                opacity = 1;

                anchor.classList.add('is-active');
                if (card) {
                    card.classList.add('coverflow-card-active', 'is-active');
                }
                anchor.style.pointerEvents = 'auto';
                anchor.setAttribute('aria-hidden', 'false');
                slot.style.cursor = 'default';
                slot.style.transform = 'translate3d(0, 0, 0) scale(1)';
                slot.style.backfaceVisibility = 'hidden';
                slot.style.webkitBackfaceVisibility = 'hidden';
                slot.style.filter = 'none';
                slot.style.willChange = 'auto';
            } else if (diff === -1) {
                // Inmediata izquierda: rotateY(24deg), scale(0.86), z-index 20
                posX = -spacingX;
                posZ = -110;
                rotY = 24;
                scale = 0.86;
                zIndex = 20;
                opacity = 0.85;

                anchor.classList.remove('is-active');
                if (card) {
                    card.classList.remove('coverflow-card-active', 'is-active');
                }
                anchor.style.pointerEvents = 'none';
                anchor.setAttribute('aria-hidden', 'true');
                slot.style.cursor = 'pointer';
            } else if (diff === 1) {
                // Inmediata derecha: rotateY(-24deg), scale(0.86), z-index 20
                posX = spacingX;
                posZ = -110;
                rotY = -24;
                scale = 0.86;
                zIndex = 20;
                opacity = 0.85;

                anchor.classList.remove('is-active');
                if (card) {
                    card.classList.remove('coverflow-card-active', 'is-active');
                }
                anchor.style.pointerEvents = 'none';
                anchor.setAttribute('aria-hidden', 'true');
                slot.style.cursor = 'pointer';
            } else if (diff === -2) {
                // Segunda izquierda: rotateY(30deg), scale(0.74), z-index 10
                posX = -Math.round(spacingX * 1.82);
                posZ = -210;
                rotY = 30;
                scale = 0.74;
                zIndex = 10;
                opacity = 0.55;

                anchor.classList.remove('is-active');
                if (card) {
                    card.classList.remove('coverflow-card-active', 'is-active');
                }
                anchor.style.pointerEvents = 'none';
                anchor.setAttribute('aria-hidden', 'true');
                slot.style.cursor = 'pointer';
            } else if (diff === 2) {
                // Segunda derecha: rotateY(-30deg), scale(0.74), z-index 10
                posX = Math.round(spacingX * 1.82);
                posZ = -210;
                rotY = -30;
                scale = 0.74;
                zIndex = 10;
                opacity = 0.55;

                anchor.classList.remove('is-active');
                if (card) {
                    card.classList.remove('coverflow-card-active', 'is-active');
                }
                anchor.style.pointerEvents = 'none';
                anchor.setAttribute('aria-hidden', 'true');
                slot.style.cursor = 'pointer';
            } else {
                // Lejanas: ocultas suavemente fuera del encuadre
                const dir = Math.sign(diff) || 1;
                posX = dir * Math.round(spacingX * 2.4);
                posZ = -300;
                rotY = -dir * 35;
                scale = 0.65;
                zIndex = 1;
                opacity = 0;
                isVisible = false;

                anchor.classList.remove('is-active');
                if (card) {
                    card.classList.remove('coverflow-card-active', 'is-active');
                }
                anchor.style.pointerEvents = 'none';
                anchor.setAttribute('aria-hidden', 'true');
                slot.style.cursor = 'default';
            }

            anchor.style.zIndex = zIndex;
            if (diff !== 0) {
                slot.style.transform = `translate3d(${posX}px, 0, ${posZ}px) rotateY(${rotY}deg) scale(${scale})`;
                slot.style.backfaceVisibility = 'hidden';
                slot.style.webkitBackfaceVisibility = 'hidden';
            }
            slot.style.opacity = opacity;
            slot.style.visibility = isVisible ? 'visible' : 'hidden';
        });
    }

    function goTo(index) {
        activeIndex = (index % total + total) % total;
        updatePositions();
    }

    function next() {
        goTo(activeIndex + 1);
    }

    function prev() {
        goTo(activeIndex - 1);
    }

    // Evitar duplicar listeners en recargas SPA
    if (stage.dataset.coverflowBound === 'true') {
        updatePositions();
        return;
    }
    stage.dataset.coverflowBound = 'true';

    // 1. Rueda del Mouse & Trackpad (wheel)
    // Escucha tanto e.deltaY (rueda vertical normal) como e.deltaX (trackpad)
    let wheelLocked = false;
    stage.addEventListener(
        'wheel',
        function (e) {
            let delta = 0;
            const absY = Math.abs(e.deltaY);
            const absX = Math.abs(e.deltaX);

            if (absY >= absX && absY > 5) {
                delta = e.deltaY;
            } else if (absX > 5) {
                delta = e.deltaX;
            }

            if (Math.abs(delta) > 5) {
                e.preventDefault();
                e.stopPropagation();

                if (wheelLocked) {
                    return;
                }
                wheelLocked = true;

                if (delta > 0) {
                    next();
                } else {
                    prev();
                }

                setTimeout(function () {
                    wheelLocked = false;
                }, 220); // Cooldown controlado ~220ms
            }
        },
        { passive: false }
    );

    // Prevenir arrastre fantasma de imágenes y enlaces nativo del navegador
    stage.addEventListener('dragstart', function (e) {
        e.preventDefault();
    });

    // 2. Arrastre con Pointer / Mouse / Touch
    let isDown = false;
    let startX = 0;
    let startY = 0;
    let currentDeltaX = 0;
    let dragMoved = false;
    let activePointerId = null;
    const DRAG_THRESHOLD = 8;
    const SWIPE_THRESHOLD = 35;

    function handleStart(clientX, clientY, pointerId = null) {
        isDown = true;
        dragMoved = false;
        startX = clientX;
        startY = clientY;
        currentDeltaX = 0;
        activePointerId = pointerId;
        if (pointerId !== null && stage.setPointerCapture) {
            try {
                stage.setPointerCapture(pointerId);
            } catch (err) {}
        }
    }

    function handleMove(clientX, clientY) {
        if (!isDown) {
            return;
        }
        currentDeltaX = clientX - startX;
        const dy = clientY - startY;

        if (Math.abs(currentDeltaX) > DRAG_THRESHOLD && Math.abs(currentDeltaX) > Math.abs(dy)) {
            dragMoved = true;
        }
    }

    function handleEnd() {
        if (!isDown) {
            return;
        }
        isDown = false;
        if (activePointerId !== null && stage.releasePointerCapture) {
            try {
                stage.releasePointerCapture(activePointerId);
            } catch (err) {}
        }
        activePointerId = null;

        if (dragMoved) {
            if (currentDeltaX < -SWIPE_THRESHOLD) {
                next();
            } else if (currentDeltaX > SWIPE_THRESHOLD) {
                prev();
            }
        }
    }

    // Pointer Events (unifica Mouse y Touch moderno)
    stage.addEventListener('pointerdown', function (e) {
        if (e.button !== undefined && e.button !== 0) {
            return;
        }
        // No interceptar ni secuestrar el puntero cuando el usuario hace clic en un botón, enlace o control de formulario
        if (e.target && typeof e.target.closest === 'function' && e.target.closest('button, a, select, input, form')) {
            return;
        }
        handleStart(e.clientX, e.clientY, e.pointerId);
    });

    stage.addEventListener('pointermove', function (e) {
        handleMove(e.clientX, e.clientY);
    });

    stage.addEventListener('pointerup', function (e) {
        handleEnd();
    });

    stage.addEventListener('pointercancel', function () {
        isDown = false;
        dragMoved = false;
        activePointerId = null;
    });

    // Soporte táctil móvil nativo adicional
    stage.addEventListener(
        'touchstart',
        function (e) {
            if (e.target && typeof e.target.closest === 'function' && e.target.closest('button, a, select, input, form')) {
                return;
            }
            if (e.touches && e.touches[0]) {
                handleStart(e.touches[0].clientX, e.touches[0].clientY);
            }
        },
        { passive: true }
    );

    stage.addEventListener(
        'touchmove',
        function (e) {
            if (e.touches && e.touches[0]) {
                handleMove(e.touches[0].clientX, e.touches[0].clientY);
            }
        },
        { passive: true }
    );

    stage.addEventListener('touchend', function () {
        handleEnd();
    });

    // 3. Clic en el escenario (navegación lateral cuando se hace clic a la izquierda o derecha de la tarjeta central)
    stage.addEventListener(
        'click',
        function (e) {
            if (dragMoved) {
                e.preventDefault();
                e.stopImmediatePropagation();
                dragMoved = false;
                return;
            }

            // Si el clic fue dentro de la tarjeta activa central (ej. botón RESERVAR o Ver detalles), permitir su flujo normal
            const activeAnchor = e.target.closest('.coverflow-slot-anchor.is-active');
            if (activeAnchor) {
                return;
            }

            const clickedAnchor = e.target.closest('.coverflow-slot-anchor');
            if (clickedAnchor) {
                const slideIdx = parseInt(clickedAnchor.dataset.slideIndex, 10);
                if (!isNaN(slideIdx) && slideIdx !== activeIndex) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    goTo(slideIdx);
                    return;
                }
            }

            // Si hizo clic en la zona lateral izquierda o derecha del escenario (sobre una tarjeta lateral con pointer-events: none)
            const rect = stage.getBoundingClientRect();
            const centerX = rect.left + rect.width / 2;
            const offsetFromCenter = e.clientX - centerX;
            if (Math.abs(offsetFromCenter) > 155) {
                if (offsetFromCenter > 0) {
                    next();
                } else {
                    prev();
                }
            }
        },
        false
    );

    // 4. Navegación por Teclado Accesible
    stage.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowLeft') {
            e.preventDefault();
            prev();
        } else if (e.key === 'ArrowRight') {
            e.preventDefault();
            next();
        }
    });

    // 5. Redimensionamiento Responsivo
    let resizeTimer = null;
    window.addEventListener('resize', function () {
        if (resizeTimer) {
            clearTimeout(resizeTimer);
        }
        resizeTimer = setTimeout(updatePositions, 80);
    });

    // Inicializar geometría inmediatamente
    updatePositions();
}

// Auto-ejecución inmediata y en eventos de ciclo de vida
if (typeof window !== 'undefined') {
    window.initCoverflowCarousel = initCoverflowCarousel;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCoverflowCarousel);
    } else {
        initCoverflowCarousel();
    }
    window.addEventListener('load', initCoverflowCarousel);
}
