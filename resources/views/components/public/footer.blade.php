<footer class="bg-surface-container-low border-t border-purple-200/80 text-on-surface mt-20 pt-16 pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Bloque de 3 Garantías Curatoriales de Museo -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 pb-14 border-b border-purple-200/70">
            
            <div class="flex items-start gap-4 p-4 rounded-xl bg-surface-container-lowest border border-purple-200/60 shadow-2xs">
                <div class="w-12 h-12 rounded-lg bg-primary-container text-white flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[24px]">verified_user</span>
                </div>
                <div>
                    <h3 class="font-bold text-on-surface text-base font-quicksand">Autenticidad Notariada COA</h3>
                    <p class="text-on-surface-variant text-xs mt-1 leading-relaxed">
                        Cada ejemplar, prensado de vinilo y pieza de memorabilia incluye certificado de autenticidad físico con sello lacrado y registro en cadena de custodia.
                    </p>
                </div>
            </div>

            <div class="flex items-start gap-4 p-4 rounded-xl bg-surface-container-lowest border border-purple-200/60 shadow-2xs">
                <div class="w-12 h-12 rounded-lg bg-secondary-container text-on-secondary-container flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[24px]">package_2</span>
                </div>
                <div>
                    <h3 class="font-bold text-on-surface text-base font-quicksand">Embalaje Libre de Ácido</h3>
                    <p class="text-on-surface-variant text-xs mt-1 leading-relaxed">
                        Foliado con papel japonés sin lignina y fundas de polietileno de alta densidad para vinilo (antiestáticas y anti-fricción de grado archivo).
                    </p>
                </div>
            </div>

            <div class="flex items-start gap-4 p-4 rounded-xl bg-surface-container-lowest border border-purple-200/60 shadow-2xs">
                <div class="w-12 h-12 rounded-lg bg-primary-container text-white flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[24px]">local_shipping</span>
                </div>
                <div>
                    <h3 class="font-bold text-on-surface text-base font-quicksand">Logística Climatizada Blindada</h3>
                    <p class="text-on-surface-variant text-xs mt-1 leading-relaxed">
                        Envíos protegidos con control de humedad y seguro del 100% del valor tasado hasta la entrega personal en mano.
                    </p>
                </div>
            </div>

        </div>

        <!-- Enlaces Principales del Archivo y Boletín Curatorial -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 py-12 border-b border-purple-200/60">
            
            <!-- Columna de Marca -->
            <div class="lg:col-span-2 space-y-4">
                <div class="flex items-center gap-3 group">
                    <x-ui.archivum-logo size="md" />
                    <div>
                        <span class="text-lg font-bold font-brand-serif tracking-[0.2em] uppercase text-on-surface transition-colors group-hover:text-primary">ARCHIVUM</span>
                        <p class="text-[10px] font-label-code text-primary uppercase tracking-widest">Sistemas de Conservación &amp; Curaduría</p>
                    </div>
                </div>
                <p class="font-title-editorial text-on-surface-variant text-sm max-w-sm">
                    «Preservando la memoria táctil: primeras ediciones impresas, matrices de vinilo histórico y testimonios materiales autenticados para colecciones de trascendencia.»
                </p>
                <div class="text-xs text-on-surface-variant font-label-code">
                    <span class="font-semibold text-primary">Sede Central:</span> Av. Juárez 42, Centro Histórico, Bellas Artes, CDMX.
                </div>
            </div>

            <!-- Columna Catálogo -->
            <div>
                <h4 class="font-label-md text-primary font-bold tracking-wider mb-4">Catálogos Activos</h4>
                <ul class="space-y-2 text-xs font-medium text-on-surface-variant">
                    <li><a href="#" class="hover:text-primary transition">Incunables del Siglo XV</a></li>
                    <li><a href="#" class="hover:text-primary transition">Primeras Ediciones Siglo XX</a></li>
                    <li><a href="#" class="hover:text-primary transition">Prensados Originales Blue Note</a></li>
                    <li><a href="#" class="hover:text-primary transition">Discos 78 RPM / Shellac Raro</a></li>
                    <li><a href="#" class="hover:text-primary transition">Carteles &amp; Programas de Ópera</a></li>
                    <li><a href="#" class="hover:text-primary transition">Manuscritos Autografiados</a></li>
                </ul>
            </div>

            <!-- Columna Sucursales -->
            <div>
                <h4 class="font-label-md text-primary font-bold tracking-wider mb-4">Red de Galerías</h4>
                <ul class="space-y-2 text-xs font-medium text-on-surface-variant">
                    <li><a href="{{ route('public.contacto') }}" class="hover:text-primary transition font-semibold text-primary flex items-center gap-1"><span>Sedes &amp; Horarios</span><span class="material-symbols-outlined text-[14px]">arrow_forward</span></a></li>
                    <li><a href="{{ route('public.contacto') }}" class="hover:text-primary transition">Sucursal Bellas Artes (Central)</a></li>
                    <li><a href="{{ route('public.contacto') }}" class="hover:text-primary transition">Gabinete Coyoacán (Librería)</a></li>
                    <li><a href="{{ route('public.contacto') }}" class="hover:text-primary transition">Bóveda Almacén General</a></li>
                    <li><a href="{{ route('public.contacto') }}" class="hover:text-primary transition">Citas Privadas de Tasación</a></li>
                </ul>
            </div>

            <!-- Columna Boletín para Coleccionistas -->
            <div>
                <h4 class="font-label-md text-primary font-bold tracking-wider mb-4">Alertas de Bóveda</h4>
                <p class="text-xs text-on-surface-variant mb-3">
                    Reciba notificaciones anticipadas de desclasificación de lotes privados 24h antes de su apertura pública.
                </p>
                <form action="#" method="POST" class="space-y-2">
                    @csrf
                    <input 
                        type="email" 
                        placeholder="coleccionista@correo.com" 
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
                <a href="{{ route('public.nosotros') }}" class="hover:text-primary transition">Manifiesto Curatorial</a>
                <a href="{{ route('public.privacidad') }}" class="hover:text-primary transition">Aviso de Privacidad</a>
            </div>
        </div>

    </div>
</footer>
