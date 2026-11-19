<div
    class="pointer-events-none absolute inset-0 z-0 overflow-hidden select-none transition-colors duration-700"
    aria-hidden="true"
    id="archivum-vault-ambient-bg"
>
    <!-- 1. Resplandores Radiales de Atmósfera (Violeta Místico, Ámbar de Anticuario y Lavanda) -->
    <div class="absolute -top-32 -left-32 w-[620px] h-[620px] rounded-full bg-gradient-to-br from-purple-500/20 via-indigo-600/15 to-transparent dark:from-purple-600/25 dark:via-indigo-900/20 blur-[130px] opacity-75 dark:opacity-60 transition-opacity duration-700"></div>
    <div class="absolute top-1/4 -right-36 w-[540px] h-[540px] rounded-full bg-gradient-to-bl from-amber-400/15 via-rose-500/10 to-transparent dark:from-amber-500/15 dark:via-purple-950/20 blur-[120px] opacity-70 dark:opacity-50 transition-opacity duration-700"></div>
    <div class="absolute -bottom-40 left-1/3 w-[720px] h-[720px] rounded-full bg-gradient-to-tr from-purple-600/15 via-fuchsia-600/10 to-transparent dark:from-indigo-950/30 dark:via-purple-900/20 blur-[140px] opacity-65 dark:opacity-55 transition-opacity duration-700"></div>

    <!-- 2. Retícula de Estantería de Bóveda Archivística (Grid Vectorial Fino con Nodos Tipográficos) -->
    <svg class="absolute inset-0 w-full h-full opacity-40 dark:opacity-25 text-purple-900/20 dark:text-purple-300/15 transition-opacity duration-700" width="100%" height="100%">
        <defs>
            <pattern id="vault-shelf-grid" width="64" height="64" patternUnits="userSpaceOnUse">
                <path d="M 64 0 L 0 0 0 64" fill="none" stroke="currentColor" stroke-width="0.75" stroke-dasharray="2 6" />
                <circle cx="0" cy="0" r="1.5" fill="currentColor" opacity="0.6" />
            </pattern>
        </defs>
        <rect width="100%" height="100%" fill="url(#vault-shelf-grid)" />
    </svg>

    <!-- 3. Surcos Concéntricos de Vinilo Orbital (Cámara Fonográfica Superior Derecha) -->
    <svg class="absolute -top-28 -right-28 w-[640px] h-[640px] opacity-35 dark:opacity-30 text-purple-700 dark:text-purple-300 transition-all duration-700" viewBox="0 0 600 600" fill="none">
        <!-- Surcos de Vinilo 180g (Microgrooves Concéntricos) -->
        <circle cx="300" cy="300" r="280" stroke="currentColor" stroke-width="0.8" stroke-dasharray="6 8" opacity="0.4" />
        <circle cx="300" cy="300" r="250" stroke="currentColor" stroke-width="1.2" opacity="0.5" />
        <circle cx="300" cy="300" r="220" stroke="currentColor" stroke-width="0.75" stroke-dasharray="3 5" opacity="0.4" />
        <circle cx="300" cy="300" r="190" stroke="currentColor" stroke-width="1.4" opacity="0.6" />
        <circle cx="300" cy="300" r="160" stroke="currentColor" stroke-width="0.8" stroke-dasharray="4 6" opacity="0.45" />
        <circle cx="300" cy="300" r="130" stroke="currentColor" stroke-width="1.2" opacity="0.6" />
        <circle cx="300" cy="300" r="100" stroke="currentColor" stroke-width="0.75" stroke-dasharray="2 4" opacity="0.4" />
        <!-- Galleta Central del Vinilo Orbital -->
        <circle cx="300" cy="300" r="72" stroke="currentColor" stroke-width="2" opacity="0.8" />
        <circle cx="300" cy="300" r="64" stroke="currentColor" stroke-width="0.8" stroke-dasharray="1 3" opacity="0.5" />
        <circle cx="300" cy="300" r="18" fill="currentColor" opacity="0.25" />
        <circle cx="300" cy="300" r="8" fill="currentColor" opacity="0.6" />
        <!-- Rayos de Reflexión Anisotrópica -->
        <line x1="300" y1="20" x2="300" y2="580" stroke="currentColor" stroke-width="0.5" stroke-dasharray="4 12" opacity="0.25" />
        <line x1="20" y1="300" x2="580" y2="300" stroke="currentColor" stroke-width="0.5" stroke-dasharray="4 12" opacity="0.25" />
        <line x1="102" y1="102" x2="498" y2="498" stroke="currentColor" stroke-width="0.5" stroke-dasharray="3 9" opacity="0.2" />
        <line x1="498" y1="102" x2="102" y2="498" stroke="currentColor" stroke-width="0.5" stroke-dasharray="3 9" opacity="0.2" />
    </svg>

    <!-- 4. Constelación Curatorial Inferior Izquierda (Códices y Discos de Época) -->
    <svg class="absolute -bottom-36 -left-28 w-[580px] h-[580px] opacity-25 dark:opacity-20 text-indigo-700 dark:text-indigo-300 transition-all duration-700" viewBox="0 0 500 500" fill="none">
        <circle cx="250" cy="250" r="230" stroke="currentColor" stroke-width="0.75" stroke-dasharray="8 12" opacity="0.4" />
        <circle cx="250" cy="250" r="190" stroke="currentColor" stroke-width="1" opacity="0.5" />
        <circle cx="250" cy="250" r="150" stroke="currentColor" stroke-width="0.6" stroke-dasharray="4 6" opacity="0.4" />
        <circle cx="250" cy="250" r="110" stroke="currentColor" stroke-width="1.2" opacity="0.6" />
        <circle cx="250" cy="250" r="50" stroke="currentColor" stroke-width="1.5" opacity="0.7" />
        <circle cx="250" cy="250" r="8" fill="currentColor" opacity="0.5" />
        <!-- Líneas Geodésicas de Catalogación -->
        <path d="M 20 250 Q 250 80 480 250" stroke="currentColor" stroke-width="0.7" stroke-dasharray="5 7" opacity="0.3" />
        <path d="M 20 250 Q 250 420 480 250" stroke="currentColor" stroke-width="0.7" stroke-dasharray="5 7" opacity="0.3" />
    </svg>

    <!-- 5. Marca de Agua y Filigrana "ARCHIVUM · BIBLIOTHECA & DISCOS" en la base -->
    <div class="absolute bottom-6 right-8 text-right opacity-15 dark:opacity-10 pointer-events-none select-none">
        <div class="font-label-code text-[10px] uppercase tracking-[0.3em] font-bold text-primary dark:text-purple-300">
            ARCHIVUM · BÓVEDA CURATORIAL
        </div>
        <div class="font-title-editorial text-[12px] italic text-on-surface dark:text-white">
            Bibliotheca &amp; Discos Históricos
        </div>
    </div>
</div>
