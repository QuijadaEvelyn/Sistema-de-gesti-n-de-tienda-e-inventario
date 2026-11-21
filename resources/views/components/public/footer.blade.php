<footer class="bg-surface-container-low border-t border-purple-200/80 text-on-surface mt-20 pt-16 pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Bloque de 3 Garantías de la Tienda -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 pb-14 border-b border-purple-200/70">
            
            <div class="flex items-start gap-4 p-4 rounded-xl bg-surface-container-lowest border border-purple-200/60 shadow-2xs">
                <div class="w-12 h-12 rounded-lg bg-primary-container text-white flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[24px]">verified_user</span>
                </div>
                <div>
                    <h3 class="font-bold text-on-surface text-base font-quicksand">Certificado de Autenticidad</h3>
                    <p class="text-on-surface-variant text-xs mt-1 leading-relaxed">
                        Cada libro, disco de vinilo y artículo de colección incluye certificado de autenticidad físico y garantía de originalidad verificada.
                    </p>
                </div>
            </div>

            <div class="flex items-start gap-4 p-4 rounded-xl bg-surface-container-lowest border border-purple-200/60 shadow-2xs">
                <div class="w-12 h-12 rounded-lg bg-secondary-container text-on-secondary-container flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[24px]">package_2</span>
                </div>
                <div>
                    <h3 class="font-bold text-on-surface text-base font-quicksand">Empaque de Alta Protección</h3>
                    <p class="text-on-surface-variant text-xs mt-1 leading-relaxed">
                        Protección con materiales libres de ácido y fundas antiestáticas de alta densidad para que tus libros y vinilos lleguen en perfecto estado.
                    </p>
                </div>
            </div>

            <div class="flex items-start gap-4 p-4 rounded-xl bg-surface-container-lowest border border-purple-200/60 shadow-2xs">
                <div class="w-12 h-12 rounded-lg bg-primary-container text-white flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[24px]">local_shipping</span>
                </div>
                <div>
                    <h3 class="font-bold text-on-surface text-base font-quicksand">Envío Seguro y Asegurado</h3>
                    <p class="text-on-surface-variant text-xs mt-1 leading-relaxed">
                        Envíos protegidos con seguimiento en tiempo real y seguro por el 100% del precio de tu compra hasta la entrega en tu domicilio.
                    </p>
                </div>
            </div>

        </div>

        <!-- Enlaces Principales del Catálogo y Boletín -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 py-12 border-b border-purple-200/60">
            
            <!-- Columna de Marca -->
            <div class="lg:col-span-2 space-y-4">
                <div class="flex items-center gap-3 group">
                    <x-ui.archivum-logo size="md" />
                    <div>
                        <span class="text-lg font-bold font-brand-serif tracking-[0.2em] uppercase text-on-surface transition-colors group-hover:text-primary">ARCHIVUM</span>
                        <p class="text-[10px] font-label-code text-primary uppercase tracking-widest">Sistema de Gestión de Tienda e Inventario</p>
                    </div>
                </div>
                <p class="font-title-editorial text-on-surface-variant text-sm max-w-sm">
                    «Tu tienda especializada en libros de colección, primeras ediciones, discos de vinilo y artículos auténticos seleccionados con garantía.»
                </p>
                <div class="text-xs text-on-surface-variant font-label-code">
                    <span class="font-semibold text-primary">Sucursal Central:</span> Av. Juárez 42, Centro Histórico, Bellas Artes, CDMX.
                </div>
            </div>

            <!-- Columna Catálogo -->
            <div>
                <h4 class="font-label-md text-primary font-bold tracking-wider mb-4">Catálogo Destacado</h4>
                <ul class="space-y-2 text-xs font-medium text-on-surface-variant">
                    <li><a href="{{ route('public.libros') }}" class="hover:text-primary transition">Ediciones Especiales Clásicas</a></li>
                    <li><a href="{{ route('public.libros') }}" class="hover:text-primary transition">Primeras Ediciones Siglo XX</a></li>
                    <li><a href="{{ route('public.vinilos') }}" class="hover:text-primary transition">Vinilos Originales Blue Note</a></li>
                    <li><a href="{{ route('public.vinilos') }}" class="hover:text-primary transition">Discos de Vinilo 180g y CDs</a></li>
                    <li><a href="{{ route('public.memorabilia') }}" class="hover:text-primary transition">Artículos de Colección</a></li>
                    <li><a href="{{ route('public.adquisiciones') }}" class="hover:text-primary transition">Ediciones Autografiadas</a></li>
                </ul>
            </div>

            <!-- Columna Sucursales -->
            <div>
                <h4 class="font-label-md text-primary font-bold tracking-wider mb-4">Nuestras Sucursales</h4>
                <ul class="space-y-2 text-xs font-medium text-on-surface-variant">
                    <li><a href="{{ route('public.contacto') }}" class="hover:text-primary transition font-semibold text-primary flex items-center gap-1"><span>Sucursales &amp; Horarios</span><span class="material-symbols-outlined text-[14px]">arrow_forward</span></a></li>
                    <li><a href="{{ route('public.contacto') }}" class="hover:text-primary transition">Sucursal Bellas Artes (Central)</a></li>
                    <li><a href="{{ route('public.contacto') }}" class="hover:text-primary transition">Sucursal Coyoacán (Librería)</a></li>
                    <li><a href="{{ route('public.contacto') }}" class="hover:text-primary transition">Almacén General</a></li>
                    <li><a href="{{ route('public.contacto') }}" class="hover:text-primary transition">Atención al Cliente</a></li>
                </ul>
            </div>

            <!-- Columna Boletín para Clientes -->
            <div>
                <h4 class="font-label-md text-primary font-bold tracking-wider mb-4">Novedades del Catálogo</h4>
                <p class="text-xs text-on-surface-variant mb-3">
                    Recibe notificaciones anticipadas de nuevos lanzamientos, promociones y preventas especiales 24h antes de su apertura.
                </p>
                <form action="#" method="POST" class="space-y-2">
                    @csrf
                    <input 
                        type="email" 
                        placeholder="cliente@correo.com" 
                        class="w-full bg-white border border-purple-200/80 rounded-lg px-3 py-2 text-xs text-on-surface placeholder:text-outline focus:border-primary focus:outline-none" 
                        required 
                    />
                    <button type="submit" class="w-full bg-primary-container hover:bg-primary text-white font-label-md py-2 rounded-lg text-xs font-semibold tracking-wider transition">
                        Suscribir al Catálogo
                    </button>
                </form>
            </div>

        </div>

        <!-- Fila Inferior de Copyright y Datos Legales -->
        <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-label-code text-on-surface-variant">
            <div>
                &copy; {{ date('Y') }} ARCHIVUM S.A. de C.V. Todos los derechos reservados.
            </div>
            <div class="flex items-center gap-6">
                <a href="{{ route('public.contacto') }}" class="hover:text-primary transition">Contacto &amp; Horarios</a>
                <a href="{{ route('public.nosotros') }}" class="hover:text-primary transition">Conoce más de nosotros</a>
                <a href="{{ route('public.privacidad') }}" class="hover:text-primary transition">Aviso de Privacidad</a>
            </div>
        </div>

    </div>
</footer>
