<!-- Modal Curatorial de Detalles del Producto con Animación Fluida (Fade-In & Zoom-In) -->
<div
    id="product-curatorial-modal"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 transition-all duration-300 opacity-0 pointer-events-none"
    role="dialog"
    aria-modal="true"
    aria-labelledby="modal-product-title"
>
    <!-- Capa de Superposición Nocturna (Backdrop con Desenfoque Suave) -->
    <div
        id="product-modal-backdrop"
        class="fixed inset-0 bg-[#07040d]/85 backdrop-blur-sm transition-opacity duration-300"
        onclick="cerrarModalDetalleProducto()"
    ></div>

    <!-- Tarjeta Flotante Animada (Escalado y Desvanecimiento Sutil) -->
    <div
        id="product-modal-dialog"
        class="relative w-full max-w-2xl max-h-[90vh] bg-white dark:bg-[#171024] border border-purple-200/90 dark:border-purple-500/30 rounded-3xl shadow-2xl overflow-hidden flex flex-col transform scale-95 transition-all duration-300 ease-out z-10"
        onclick="event.stopPropagation()"
    >
        <!-- Encabezado del Modal con Identidad Curatorial -->
        <div class="px-6 py-4 bg-surface-container-low/70 dark:bg-[#1b132a] border-b border-purple-200/60 dark:border-purple-500/30 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-primary-container text-white flex items-center justify-center shadow-2xs">
                    <span id="modal-product-category-icon" class="material-symbols-outlined text-[18px]">auto_stories</span>
                </div>
                <div>
                    <span id="modal-product-category-badge" class="font-label-code text-[10px] uppercase font-bold tracking-wider text-primary dark:text-purple-300 block">
                        Ficha Curatorial ARCHIVUM
                    </span>
                    <span id="modal-product-sede" class="font-label-code text-[10px] text-secondary dark:text-stone-300 block">
                        Custodia: Sede Central
                    </span>
                </div>
            </div>

            <button
                type="button"
                onclick="cerrarModalDetalleProducto()"
                class="p-2 rounded-xl text-outline hover:text-on-surface hover:bg-surface-container dark:hover:bg-[#221834] transition focus:outline-none cursor-pointer"
                aria-label="Cerrar detalles del producto"
            >
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <!-- Cuerpo del Modal Desplazable con Toda la Información y Especificaciones Técnicas -->
        <div class="p-6 sm:p-8 overflow-y-auto space-y-6 flex-1 no-scrollbar">
            <!-- Bloque Principal: Icono/Miniatura, Insignias y Títulos -->
            <div class="flex flex-col sm:flex-row gap-5 items-start">
                <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl bg-surface-container-low dark:bg-[#140d20] border border-purple-200/70 dark:border-purple-500/30 flex items-center justify-center shrink-0 shadow-inner overflow-hidden">
                    <img id="modal-product-img" src="" alt="" class="w-full h-full object-cover hidden">
                    <span id="modal-product-hero-icon" class="material-symbols-outlined text-primary text-[52px]">auto_stories</span>
                </div>
                <div class="space-y-1.5 flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <span id="modal-product-sku" class="font-label-code text-xs text-primary font-bold uppercase tracking-wider bg-purple-50 dark:bg-purple-950/60 px-2 py-0.5 rounded border border-purple-200 dark:border-purple-500/30">
                            SKU: #ARC-001
                        </span>
                        <span id="modal-product-grading" class="font-label-code text-xs font-semibold px-2 py-0.5 rounded bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30">
                            MINT (M)
                        </span>
                        <span id="modal-product-availability" class="font-label-code text-xs font-semibold px-2 py-0.5 rounded bg-surface-container dark:bg-[#1b132a] text-on-surface border border-purple-200/60 dark:border-purple-500/30">
                            Pieza Disponible
                        </span>
                    </div>
                    <h2 id="modal-product-title" class="text-xl sm:text-2xl font-bold font-quicksand text-on-surface dark:text-white leading-tight">
                        Título del Producto
                    </h2>
                    <p id="modal-product-subtitle" class="font-title-editorial text-sm text-on-surface-variant dark:text-amber-100 italic">
                        Subtítulo Curatorial
                    </p>
                </div>
            </div>

            <!-- Reseña y Peritaje Curatorial Extenso -->
            <div class="space-y-2 p-5 rounded-2xl bg-surface-container-low/60 dark:bg-[#140d20] border border-purple-200/60 dark:border-purple-500/30">
                <span class="font-label-code text-[11px] uppercase tracking-wider font-bold text-primary dark:text-purple-300 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">history_edu</span>
                    <span>Dictamen y Reseña Curatorial</span>
                </span>
                <p id="modal-product-description" class="text-sm leading-relaxed text-secondary dark:text-stone-200 font-normal">
                    Descripción detallada del ejemplar...
                </p>
            </div>

            <!-- Especificaciones Técnicas y Peritaje Material -->
            <div class="space-y-3">
                <span class="font-label-code text-[11px] uppercase tracking-wider font-bold text-outline dark:text-stone-400 block">
                    Especificaciones Técnicas &amp; Catalogación
                </span>
                <div id="modal-product-specs-grid" class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-xs">
                    <!-- Filas de atributos técnicos inyectadas dinámicamente -->
                </div>
            </div>

            <!-- Garantía Curatorial & Protección Legal -->
            <div class="p-3.5 rounded-xl bg-purple-50/50 dark:bg-[#1b132a] border border-purple-200/50 dark:border-purple-500/20 flex items-center gap-3 text-xs text-on-surface-variant dark:text-stone-300">
                <span class="material-symbols-outlined text-primary text-[20px] shrink-0">verified</span>
                <p class="text-[11px] leading-snug">
                    Ejemplar autenticado bajo custodia con peritaje notarial y embalaje libre de ácido conforme a la Ley Federal de Protección al Patrimonio.
                </p>
            </div>
        </div>

        <!-- Pie del Modal: Precio Tasado y Botón de Acción Directo -->
        <div class="px-6 py-4 bg-surface-container-low/70 dark:bg-[#1b132a] border-t border-purple-200/60 dark:border-purple-500/30 flex flex-col sm:flex-row items-center justify-between gap-4 shrink-0">
            <div>
                <span class="text-[10px] uppercase font-label-code text-outline dark:text-stone-400 block">Precio Tasado:</span>
                <div class="flex items-baseline gap-1.5">
                    <span id="modal-product-price" class="font-tabular-data font-bold text-xl sm:text-2xl text-on-surface dark:text-white">
                        $0
                    </span>
                    <span class="text-xs font-normal text-on-surface-variant dark:text-stone-300">MXN</span>
                </div>
            </div>

            <div id="modal-product-action-container" class="w-full sm:w-auto">
                <!-- Botón de Reserva / Carrito inyectado dinámicamente -->
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        'use strict';

        window.abrirModalDetalleProducto = function (triggerElement) {
            const rawData = triggerElement.dataset.productDetails || triggerElement.closest('[data-product-details]')?.dataset.productDetails;
            if (!rawData) return;

            try {
                const item = JSON.parse(rawData);
                const modal = document.getElementById('product-curatorial-modal');
                const dialog = document.getElementById('product-modal-dialog');
                if (!modal || !dialog) return;

                // Inyección de textos y datos
                document.getElementById('modal-product-title').textContent = item.nombre;
                document.getElementById('modal-product-subtitle').textContent = item.subtitulo;
                document.getElementById('modal-product-description').textContent = item.descripcion || 'Sin descripción curatorial disponible.';
                document.getElementById('modal-product-price').textContent = '$' + item.precio;
                document.getElementById('modal-product-sku').textContent = 'SKU: #' + item.sku;
                document.getElementById('modal-product-grading').textContent = item.graduacion;
                
                // Imagen o Icono Curatorial
                const modalImg = document.getElementById('modal-product-img');
                const modalHeroIcon = document.getElementById('modal-product-hero-icon');
                if (item.imagen && modalImg && modalHeroIcon) {
                    modalImg.src = item.imagen;
                    modalImg.alt = item.nombre;
                    modalImg.classList.remove('hidden');
                    modalHeroIcon.classList.add('hidden');
                } else if (modalImg && modalHeroIcon) {
                    modalImg.classList.add('hidden');
                    modalHeroIcon.classList.remove('hidden');
                }

                document.getElementById('modal-product-hero-icon').textContent = item.icono;
                document.getElementById('modal-product-category-icon').textContent = item.icono;
                document.getElementById('modal-product-category-badge').textContent = 'CATÁLOGO · ' + (item.tipo || 'PIEZA').toUpperCase();
                document.getElementById('modal-product-sede').textContent = item.sede ? 'Custodia: ' + item.sede : 'Custodia en Bóveda Central';

                // Estado de Disponibilidad
                const availBadge = document.getElementById('modal-product-availability');
                if (item.estaReservada) {
                    availBadge.textContent = 'En Reserva Temporal (5 min)';
                    availBadge.className = 'font-label-code text-xs font-bold px-2 py-0.5 rounded bg-amber-500/20 text-amber-500 border border-amber-500/40';
                } else if (item.estaAgotado) {
                    availBadge.textContent = 'Pieza Agotada';
                    availBadge.className = 'font-label-code text-xs font-bold px-2 py-0.5 rounded bg-rose-500/20 text-rose-500 border border-rose-500/40';
                } else {
                    availBadge.textContent = item.esPiezaUnica ? 'Pieza Única 1/1 Certificada' : (item.stock + ' unidades en custodia');
                    availBadge.className = 'font-label-code text-xs font-bold px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30';
                }

                // Grid de Especificaciones Técnicas
                const specsGrid = document.getElementById('modal-product-specs-grid');
                specsGrid.innerHTML = '';
                if (item.especificaciones && typeof item.especificaciones === 'object') {
                    for (const [clave, valor] of Object.entries(item.especificaciones)) {
                        const fila = document.createElement('div');
                        fila.className = 'p-2.5 rounded-xl bg-surface-container/50 dark:bg-[#1b132a] border border-purple-200/40 dark:border-purple-500/20 flex flex-col justify-between';
                        fila.innerHTML = `
                            <span class="text-[10px] uppercase font-label-code text-outline dark:text-stone-400 font-semibold">${clave}</span>
                            <span class="font-medium text-on-surface dark:text-stone-100 mt-0.5">${valor}</span>
                        `;
                        specsGrid.appendChild(fila);
                    }
                }

                // Botón de Reserva Directo desde el Modal
                const actionContainer = document.getElementById('modal-product-action-container');
                actionContainer.innerHTML = '';
                if (!item.estaAgotado && item.varianteSkuId) {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = item.actionUrl;
                    form.className = 'flex items-center gap-2';
                    form.innerHTML = `
                        <input type="hidden" name="_token" value="${csrfToken}" />
                        <input type="hidden" name="variante_sku_id" value="${item.varianteSkuId}" />
                        <input type="hidden" name="cantidad" value="1" />
                        <button type="submit" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-primary-container hover:bg-primary text-white text-xs font-label-md font-bold uppercase tracking-wider flex items-center justify-center gap-2 shadow-sm transition cursor-pointer">
                            <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                            <span>Reservar en Bóveda</span>
                        </button>
                    `;
                    actionContainer.appendChild(form);
                } else {
                    actionContainer.innerHTML = `
                        <button type="button" disabled class="px-5 py-2.5 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-300 dark:border-slate-700 text-xs font-label-md font-bold uppercase tracking-wider flex items-center gap-2 opacity-60 cursor-not-allowed">
                            <span class="material-symbols-outlined text-[18px]">${item.estaReservada ? 'lock_clock' : 'block'}</span>
                            <span>${item.estaReservada ? 'Pieza en Reserva' : 'Agotado'}</span>
                        </button>
                    `;
                }

                // Animación fluida: fade-in & zoom-in
                modal.classList.remove('opacity-0', 'pointer-events-none');
                modal.classList.add('opacity-100');
                dialog.classList.remove('scale-95');
                dialog.classList.add('scale-100');
                document.body.style.overflow = 'hidden';
            } catch (err) {
                console.error('Error al abrir modal de producto:', err);
            }
        };

        window.cerrarModalDetalleProducto = function () {
            const modal = document.getElementById('product-curatorial-modal');
            const dialog = document.getElementById('product-modal-dialog');
            if (!modal || !dialog) return;

            modal.classList.add('opacity-0', 'pointer-events-none');
            modal.classList.remove('opacity-100');
            dialog.classList.add('scale-95');
            dialog.classList.remove('scale-100');
            document.body.style.overflow = '';
        };

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                window.cerrarModalDetalleProducto();
            }
        });
    })();
</script>

