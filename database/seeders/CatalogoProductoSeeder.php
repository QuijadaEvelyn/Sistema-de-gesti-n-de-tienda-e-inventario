<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Catalogo;
use App\Models\Cliente;
use App\Models\Coleccionable;
use App\Models\Direccion;
use App\Models\Disco;
use App\Models\Existencia;
use App\Models\Libro;
use App\Models\MovimientoInventario;
use App\Models\Pedido;
use App\Models\PiezaUnica;
use App\Models\Producto;
use App\Models\Promocion;
use App\Models\Sucursal;
use App\Models\VarianteSku;
use App\Services\PromocionDescuentoService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class CatalogoProductoSeeder extends Seeder
{
    /**
     * Pobla la base de datos de ARCHIVUM con sucursales, catálogos por categoría
     * e inventario híbrido (Piezas Únicas 1/1 diferenciadas por sucursal + productos de stock múltiple).
     */
    public function run(): void
    {
        // 1. Sucursales físicas y bóveda central
        $sucursalBellasArtes = Sucursal::firstOrCreate(
            ['nombre' => 'Sucursal Central Bellas Artes'],
            [
                'direccion' => 'Av. Juárez 18, Centro Histórico, Cuauhtémoc, CDMX',
                'telefono' => '5555128900',
                'activa' => true,
            ]
        );

        $sucursalCoyoacan = Sucursal::firstOrCreate(
            ['nombre' => 'Sucursal Coyoacán'],
            [
                'direccion' => 'Jardín Centenario 14, Villa Coyoacán, CDMX',
                'telefono' => '5556584420',
                'activa' => true,
            ]
        );

        $sucursalAlmacen = Sucursal::firstOrCreate(
            ['nombre' => 'Almacén General'],
            [
                'direccion' => 'Calz. Vallejo 1020, Industrial Vallejo, Azcapotzalco, CDMX',
                'telefono' => '5553689100',
                'activa' => true,
            ]
        );

        // 2. Catálogos principales por tipo ('libro', 'disco', 'coleccionable')
        $catLibros = Catalogo::updateOrCreate(
            ['tipo' => 'libro'],
            [
                'nombre' => 'Libros',
                'descripcion' => 'Primeras ediciones hispanoamericanas, ejemplares autografiados, narrativa, ensayo, poesía e historia con peritaje bibliográfico.',
                'activo' => true,
            ]
        );

        $catVinilos = Catalogo::updateOrCreate(
            ['tipo' => 'disco'],
            [
                'nombre' => 'Discos',
                'descripcion' => 'Prensados analógicos en vinilo de 180g y ediciones maestras en CD clasificadas con alta fidelidad.',
                'activo' => true,
            ]
        );

        $catMemorabilia = Catalogo::updateOrCreate(
            ['tipo' => 'coleccionable'],
            [
                'nombre' => 'Coleccionables',
                'descripcion' => 'Figuras de colección, numismática, juguetes manufacturados, objetos históricos y memorabilia de videojuegos.',
                'activo' => true,
            ]
        );

        // 3. Piezas de Libros Raros & Incunables (Catálogo Híbrido: Piezas Únicas 1/1 + Stock Masivo diferenciados por sucursal)
        $libros = [
            [
                'sku' => 'LIB-1967-SUD',
                'nombre' => 'Cien Años de Soledad (1ª Edición Sudamericana)',
                'descripcion' => 'Pieza Única (1/1) custodiada en Almacén General: Tiraje inicial de Buenos Aires (Mayo 1967) con prueba de galera firmada. Cubierta original con galeón en la selva diseñada por Iris Alba.',
                'precio' => 32000.00,
                'graduacion' => 'VERY FINE (VF) · PIEZA ÚNICA 1/1',
                'isbn' => '978-950-07-0010-1',
                'autor' => 'Gabriel García Márquez',
                'genero' => 'novela',
                'editorial' => 'Editorial Sudamericana',
                'anio_publicacion' => 1967,
                'paginas' => 351,
                'stock_central' => 0,
                'stock_coyoacan' => 0,
                'stock_almacen' => 1,
                'sede_pieza_unica' => 'almacen',
                'serie_unica' => 'ARC-LIB-1967-001',
                'certificado' => 'COA-BIB-2026-014',
            ],
            [
                'sku' => 'LIB-1955-FCE',
                'nombre' => 'Pedro Páramo (Edición Príncipe FCE)',
                'descripcion' => 'Pieza Única (1/1) custodiada en Sucursal Coyoacán: Primera edición de 1955 en la Colección Letras Mexicanas N.º 19 con firma autógrafa de Juan Rulfo en guarda anterior.',
                'precio' => 44500.00,
                'graduacion' => 'NEAR MINT (NM) · PIEZA ÚNICA 1/1 · ADQUISICIÓN SELECTA',
                'isbn' => '978-968-16-0001-2',
                'autor' => 'Juan Rulfo',
                'genero' => 'novela',
                'editorial' => 'Fondo de Cultura Económica',
                'anio_publicacion' => 1955,
                'paginas' => 155,
                'stock_central' => 0,
                'stock_coyoacan' => 1,
                'stock_almacen' => 0,
                'sede_pieza_unica' => 'coyoacan',
                'serie_unica' => 'ARC-LIB-1955-002',
                'certificado' => 'COA-BIB-2026-019',
            ],
            [
                'sku' => 'LIB-1949-LOS',
                'nombre' => 'El Aleph (Ejemplar Dedicado por el Autor)',
                'descripcion' => 'Pieza Única (1/1) custodiada en Sucursal Central Bellas Artes: Primera edición impresa por Editorial Losada (1949) con dedicatoria manuscrita de Jorge Luis Borges.',
                'precio' => 58000.00,
                'graduacion' => 'MINT (M) · PIEZA ÚNICA 1/1 · ADQUISICIÓN SELECTA',
                'isbn' => '978-950-03-0049-8',
                'autor' => 'Jorge Luis Borges',
                'genero' => 'novela',
                'editorial' => 'Editorial Losada',
                'anio_publicacion' => 1949,
                'paginas' => 146,
                'stock_central' => 1,
                'stock_coyoacan' => 0,
                'stock_almacen' => 0,
                'sede_pieza_unica' => 'central',
                'serie_unica' => 'ARC-LIB-1949-003',
                'certificado' => 'COA-BIB-2026-022',
            ],
            [
                'sku' => 'LIB-1950-FCE',
                'nombre' => 'El Laberinto de la Soledad (1ª Edición Cuadernos Americanos)',
                'descripcion' => 'Stock Masivo en Sucursal Central Bellas Artes: Edición de tiraje múltiple impresa en los talleres de Cuadernos Americanos en papel Chamois. Permite adquirir múltiples ejemplares.',
                'precio' => 16900.00,
                'graduacion' => 'VERY FINE (VF) · TIRAJE MÚLTIPLE',
                'isbn' => '978-968-16-0050-0',
                'autor' => 'Octavio Paz',
                'genero' => 'ensayo',
                'editorial' => 'Cuadernos Americanos',
                'anio_publicacion' => 1950,
                'paginas' => 195,
                'stock_central' => 8,
                'stock_coyoacan' => 0,
                'stock_almacen' => 0,
                'sede_pieza_unica' => null,
                'serie_unica' => null,
                'certificado' => null,
            ],
            [
                'sku' => 'LIB-1962-ERA',
                'nombre' => 'Aura (Edición Conmemorativa Biblioteca Era Coyoacán)',
                'descripcion' => 'Stock Masivo en Sucursal Coyoacán: Tiraje regular encuadernado en tela editorial por Ediciones Era. Disponible para compra de varias unidades sin bloqueo exclusivo.',
                'precio' => 6800.00,
                'graduacion' => 'NEAR MINT (NM) · TIRAJE REGULAR',
                'isbn' => '978-968-411-062-5',
                'autor' => 'Carlos Fuentes',
                'genero' => 'novela',
                'editorial' => 'Ediciones Era',
                'anio_publicacion' => 1962,
                'paginas' => 62,
                'stock_central' => 0,
                'stock_coyoacan' => 9,
                'stock_almacen' => 0,
                'sede_pieza_unica' => null,
                'serie_unica' => null,
                'certificado' => null,
            ],
            [
                'sku' => 'LIB-1963-SUD',
                'nombre' => 'Rayuela (Tiraje Clásico Editorial Sudamericana)',
                'descripcion' => 'Stock Masivo en Almacén General: Edición conmemorativa de tiraje amplio con tablero de dirección de lectura. Existencias múltiples disponibles para despacho inmediato.',
                'precio' => 8400.00,
                'graduacion' => 'MINT (M) · TIRAJE MÚLTIPLE',
                'isbn' => '978-950-07-0063-7',
                'autor' => 'Julio Cortázar',
                'genero' => 'novela',
                'editorial' => 'Editorial Sudamericana',
                'anio_publicacion' => 1963,
                'paginas' => 635,
                'stock_central' => 0,
                'stock_coyoacan' => 0,
                'stock_almacen' => 15,
                'sede_pieza_unica' => null,
                'serie_unica' => null,
                'certificado' => null,
            ],
            [
                'sku' => 'LIB-1957-FCE',
                'nombre' => 'Piedra de Sol y Obra Poética (Primera Edición 1957)',
                'descripcion' => 'Pieza de Poesía de Alto Valor Curatorial: Primera edición del poema circular de 584 versos endecasílabos numerados. Ejemplar de colección impreso en papel especial.',
                'precio' => 24500.00,
                'graduacion' => 'MINT (M) · PRIMERA EDICIÓN · POESÍA',
                'isbn' => '978-968-16-0157-6',
                'autor' => 'Octavio Paz',
                'genero' => 'poesia',
                'editorial' => 'Fondo de Cultura Económica',
                'anio_publicacion' => 1957,
                'paginas' => 112,
                'stock_central' => 2,
                'stock_coyoacan' => 1,
                'stock_almacen' => 0,
                'sede_pieza_unica' => null,
                'serie_unica' => null,
                'certificado' => null,
            ],
            [
                'sku' => 'LIB-1959-UNAM',
                'nombre' => 'Visión de los Vencidos: Relaciones Indígenas de la Conquista',
                'descripcion' => 'Obra Fundamental de Historia: Testimonios de los informantes de Sahagún traducidos del náhuatl por Ángel María Garibay con textos y notas de Miguel León-Portilla.',
                'precio' => 9800.00,
                'graduacion' => 'NEAR MINT (NM) · HISTORIA · BIBLIOTECA DEL ESTUDIANTE',
                'isbn' => '978-970-32-4469-0',
                'autor' => 'Miguel León-Portilla',
                'genero' => 'historia',
                'editorial' => 'Universidad Nacional Autónoma de México',
                'anio_publicacion' => 1959,
                'paginas' => 218,
                'stock_central' => 4,
                'stock_coyoacan' => 3,
                'stock_almacen' => 6,
                'sede_pieza_unica' => null,
                'serie_unica' => null,
                'certificado' => null,
            ],
            [
                'sku' => 'LIB-1971-FCE',
                'nombre' => 'La Estructura de las Revoluciones Científicas (1ª Edición FCE)',
                'descripcion' => 'Obra Cumbre de Ciencia y Epistemología: Traducción clásica al español del tratado pionero de Thomas Kuhn sobre los cambios de paradigma en la investigación científica.',
                'precio' => 11200.00,
                'graduacion' => 'VERY FINE (VF) · CIENCIA & FILOSOFÍA',
                'isbn' => '978-968-16-0442-3',
                'autor' => 'Thomas S. Kuhn',
                'genero' => 'ciencia',
                'editorial' => 'Fondo de Cultura Económica',
                'anio_publicacion' => 1971,
                'paginas' => 319,
                'stock_central' => 3,
                'stock_coyoacan' => 0,
                'stock_almacen' => 5,
                'sede_pieza_unica' => null,
                'serie_unica' => null,
                'certificado' => null,
            ],
        ];

        foreach ($libros as $item) {
            $producto = Producto::updateOrCreate(
                ['nombre' => $item['nombre']],
                [
                    'catalogo_id' => $catLibros->id,
                    'descripcion' => $item['descripcion'],
                    'precio' => $item['precio'],
                    'activo' => true,
                ]
            );

            Libro::updateOrCreate(
                ['producto_id' => $producto->id],
                [
                    'isbn' => $item['isbn'],
                    'autor' => $item['autor'],
                    'genero' => $item['genero'] ?? null,
                    'editorial' => $item['editorial'],
                    'anio_publicacion' => $item['anio_publicacion'],
                    'paginas' => $item['paginas'],
                ]
            );

            $this->registrarVarianteYExistencias(
                $producto,
                $item['sku'],
                $item['graduacion'],
                $item['precio'],
                $sucursalBellasArtes->id,
                $item['stock_central'],
                $sucursalCoyoacan->id,
                $item['stock_coyoacan'],
                $sucursalAlmacen->id,
                $item['stock_almacen'],
                $item['sede_pieza_unica'],
                $item['serie_unica'],
                $item['certificado'],
            );
        }

        // 4. Piezas de Discos & Vinilos Históricos (Catálogo Híbrido: Piezas Únicas 1/1 + Stock Masivo diferenciados por sucursal)
        $vinilos = [
            [
                'sku' => 'VIN-1959-COL',
                'nombre' => 'Kind of Blue (Mono 1st Press 6-Eye)',
                'descripcion' => 'Pieza Única (1/1) en Sucursal Central Bellas Artes: Matriz original XLP 47324-1A con etiqueta Columbia «6-eye» deep groove y firma certificada.',
                'precio' => 18500.00,
                'graduacion' => 'MINT (M) · PIEZA ÚNICA 1/1 · ADQUISICIÓN SELECTA',
                'artista' => 'Miles Davis',
                'sello' => 'Columbia Records CL 1355',
                'genero' => 'Modal Jazz',
                'formato' => 'vinilo',
                'anio_lanzamiento' => 1959,
                'stock_central' => 1,
                'stock_coyoacan' => 0,
                'stock_almacen' => 0,
                'sede_pieza_unica' => 'central',
                'serie_unica' => 'ARC-VIN-1959-001',
                'certificado' => 'COA-AUD-2026-101',
            ],
            [
                'sku' => 'VIN-1957-BN1577',
                'nombre' => 'Blue Train (Plastylite RVG Ear Press)',
                'descripcion' => 'Pieza Única (1/1) en Sucursal Coyoacán: Grabado por Rudy Van Gelder con sello «ear» en surco muerto y dirección 47 West 63rd NYC.',
                'precio' => 27500.00,
                'graduacion' => 'NEAR MINT (NM) · PIEZA ÚNICA 1/1 · ADQUISICIÓN SELECTA',
                'artista' => 'John Coltrane',
                'sello' => 'Blue Note BLP 1577',
                'genero' => 'Hard Bop Jazz',
                'formato' => 'vinilo',
                'anio_lanzamiento' => 1957,
                'stock_central' => 0,
                'stock_coyoacan' => 1,
                'stock_almacen' => 0,
                'sede_pieza_unica' => 'coyoacan',
                'serie_unica' => 'ARC-VIN-1957-002',
                'certificado' => 'COA-AUD-2026-104',
            ],
            [
                'sku' => 'VIN-1965-IMP77',
                'nombre' => 'A Love Supreme (Gatefold 1st Impulse! Mono)',
                'descripcion' => 'Stock Masivo en Sucursal Central Bellas Artes: Carpeta desplegable laminada con estampado VAN GELDER. Existencias múltiples disponibles.',
                'precio' => 16200.00,
                'graduacion' => 'VERY FINE (VF) · VINILO LP 180G',
                'artista' => 'John Coltrane Quartet',
                'sello' => 'Impulse! A-77',
                'genero' => 'Spiritual Jazz',
                'formato' => 'vinilo',
                'anio_lanzamiento' => 1965,
                'stock_central' => 6,
                'stock_coyoacan' => 0,
                'stock_almacen' => 0,
                'sede_pieza_unica' => null,
                'serie_unica' => null,
                'certificado' => null,
            ],
            [
                'sku' => 'VIN-1969-PCS7088',
                'nombre' => 'Abbey Road (UK 1st Press Misaligned Apple)',
                'descripcion' => 'Pieza Única (1/1) en Almacén General: Prensado británico matriz YEX 749-2 / 750-1 con manzana desalineada en contraportada.',
                'precio' => 22900.00,
                'graduacion' => 'MINT (M) · PIEZA ÚNICA 1/1 · ADQUISICIÓN SELECTA',
                'artista' => 'The Beatles',
                'sello' => 'Apple Records PCS 7088',
                'genero' => 'Rock Clásico',
                'formato' => 'vinilo',
                'anio_lanzamiento' => 1969,
                'stock_central' => 0,
                'stock_coyoacan' => 0,
                'stock_almacen' => 1,
                'sede_pieza_unica' => 'almacen',
                'serie_unica' => 'ARC-VIN-1969-004',
                'certificado' => 'COA-AUD-2026-112',
            ],
            [
                'sku' => 'CD-1983-CP35',
                'nombre' => 'The Dark Side of the Moon (1st Japan Black Triangle CD)',
                'descripcion' => 'Stock Masivo en Sucursal Coyoacán: Edición en Disco Compacto prensada por Toshiba-EMI en Japón (CP35-3017). Permite adquirir múltiples unidades.',
                'precio' => 14800.00,
                'graduacion' => 'MINT (M) · CD COMPACT DISC · ADQUISICIÓN SELECTA',
                'artista' => 'Pink Floyd',
                'sello' => 'Harvest / Toshiba-EMI CP35-3017',
                'genero' => 'Rock Progresivo',
                'formato' => 'CD',
                'anio_lanzamiento' => 1983,
                'stock_central' => 0,
                'stock_coyoacan' => 7,
                'stock_almacen' => 0,
                'sede_pieza_unica' => null,
                'serie_unica' => null,
                'certificado' => null,
            ],
            [
                'sku' => 'VIN-1971-LED4',
                'nombre' => 'Led Zeppelin IV (Prensado Audiófilo Atlantic 180g)',
                'descripcion' => 'Stock Masivo en Almacén General: Reedición audiófila de tiraje regular en vinilo virgen de 180 gramos masterizado desde las cintas analógicas.',
                'precio' => 5900.00,
                'graduacion' => 'MINT (M) · VINILO LP 180G',
                'artista' => 'Led Zeppelin',
                'sello' => 'Atlantic Records SD 7208',
                'genero' => 'Hard Rock',
                'formato' => 'vinilo',
                'anio_lanzamiento' => 1971,
                'stock_central' => 0,
                'stock_coyoacan' => 0,
                'stock_almacen' => 14,
                'sede_pieza_unica' => null,
                'serie_unica' => null,
                'certificado' => null,
            ],
            [
                'sku' => 'CD-1997-RAD1',
                'nombre' => 'OK Computer (Collector\'s Edition 2-CD Parlophone)',
                'descripcion' => 'Edición Maestra en Disco Compacto: Edición doble de 1997 con estuche digipak de colección, libreto con ilustraciones de Stanley Donwood y lados B masterizados.',
                'precio' => 4800.00,
                'graduacion' => 'MINT (M) · CD COMPACT DISC',
                'artista' => 'Radiohead',
                'sello' => 'Parlophone / EMI 7243 8 55229 2 4',
                'genero' => 'Art Rock & Alternativo',
                'formato' => 'CD',
                'anio_lanzamiento' => 1997,
                'stock_central' => 5,
                'stock_coyoacan' => 2,
                'stock_almacen' => 8,
                'sede_pieza_unica' => null,
                'serie_unica' => null,
                'certificado' => null,
            ],
            [
                'sku' => 'CD-1997-BVSC',
                'nombre' => 'Buena Vista Social Club (Edición CD Remasterizada World Circuit)',
                'descripcion' => 'Obra Fonográfica Esencial en Disco Compacto: Grabaciones de La Habana bajo la producción de Ry Cooder, masterizado digital con folleto historiográfico de 48 páginas.',
                'precio' => 3950.00,
                'graduacion' => 'MINT (M) · CD COMPACT DISC',
                'artista' => 'Buena Vista Social Club',
                'sello' => 'World Circuit WCD050',
                'genero' => 'Son Cubano & Trova Tradicional',
                'formato' => 'CD',
                'anio_lanzamiento' => 1997,
                'stock_central' => 4,
                'stock_coyoacan' => 4,
                'stock_almacen' => 6,
                'sede_pieza_unica' => null,
                'serie_unica' => null,
                'certificado' => null,
            ],
        ];

        foreach ($vinilos as $item) {
            $producto = Producto::updateOrCreate(
                ['nombre' => $item['nombre']],
                [
                    'catalogo_id' => $catVinilos->id,
                    'descripcion' => $item['descripcion'],
                    'precio' => $item['precio'],
                    'activo' => true,
                ]
            );

            Disco::updateOrCreate(
                ['producto_id' => $producto->id],
                [
                    'artista' => $item['artista'],
                    'sello' => $item['sello'],
                    'genero' => $item['genero'],
                    'formato' => $item['formato'],
                    'anio_lanzamiento' => $item['anio_lanzamiento'],
                ]
            );

            $this->registrarVarianteYExistencias(
                $producto,
                $item['sku'],
                $item['graduacion'],
                $item['precio'],
                $sucursalBellasArtes->id,
                $item['stock_central'],
                $sucursalCoyoacan->id,
                $item['stock_coyoacan'],
                $sucursalAlmacen->id,
                $item['stock_almacen'],
                $item['sede_pieza_unica'],
                $item['serie_unica'],
                $item['certificado'],
            );
        }

        // 5. Piezas de Memorabilia, Accesorios & Archivo (Catálogo Híbrido: Piezas Únicas 1/1 + Stock Masivo + Agotado)
        $memorabilia = [
            [
                'sku' => 'MEM-1928-AUT',
                'nombre' => 'Carta Manuscrita Bellas Artes (1928)',
                'descripcion' => 'Pieza Única (1/1) en Sucursal Central Bellas Artes: Pluma estilográfica con tinta ferrogálica original sobre papel membretado con COA notariado.',
                'precio' => 9400.00,
                'graduacion' => 'COA NOTARIADO · PIEZA ÚNICA 1/1',
                'marca' => 'Colección Diplomática',
                'serie' => 'Archivo Histórico Bellas Artes',
                'categoria' => 'historicos',
                'material' => 'Papel Algodón & Tinta Ferrogálica',
                'edicion_limitada' => true,
                'stock_central' => 1,
                'stock_coyoacan' => 0,
                'stock_almacen' => 0,
                'sede_pieza_unica' => 'central',
                'serie_unica' => 'ARC-MEM-1928-001',
                'certificado' => 'COA-NOT-2026-301',
            ],
            [
                'sku' => 'MEM-1940-COY',
                'nombre' => 'Manuscrito Autógrafo Casa Azul Coyoacán (1940)',
                'descripcion' => 'Pieza Única (1/1) en Sucursal Coyoacán: Misiva autógrafa en papel de algodón fechada en Villa Coyoacán con sello lacrado original.',
                'precio' => 36800.00,
                'graduacion' => 'MINT (M) · PIEZA ÚNICA 1/1 · ADQUISICIÓN SELECTA',
                'marca' => 'Archivo Coyoacán',
                'serie' => 'Correspondencia Muralista S. XX',
                'categoria' => 'historicos',
                'material' => 'Tinta Sepia sobre Papel Ingres',
                'edicion_limitada' => true,
                'stock_central' => 0,
                'stock_coyoacan' => 1,
                'stock_almacen' => 0,
                'sede_pieza_unica' => 'coyoacan',
                'serie_unica' => 'ARC-MEM-1940-004',
                'certificado' => 'COA-NOT-2026-320',
            ],
            [
                'sku' => 'MEM-1910-ALM',
                'nombre' => 'Medalla Conmemorativa del Centenario (1910)',
                'descripcion' => 'Pieza Única (1/1) en Almacén General: Medalla oficial en plata ley .925 acuñada por la Casa de Moneda en 1910 con estuche de terciopelo original.',
                'precio' => 29500.00,
                'graduacion' => 'MINT (M) · PIEZA ÚNICA 1/1 · ADQUISICIÓN SELECTA',
                'marca' => 'Casa de Moneda de México',
                'serie' => 'Numismática del Centenario',
                'categoria' => 'numismatica',
                'material' => 'Plata Ley .925 en Estuche de Época',
                'edicion_limitada' => true,
                'stock_central' => 0,
                'stock_coyoacan' => 0,
                'stock_almacen' => 1,
                'sede_pieza_unica' => 'almacen',
                'serie_unica' => 'ARC-MEM-1910-005',
                'certificado' => 'COA-NOT-2026-335',
            ],
            [
                'sku' => 'MEM-1934-FOT',
                'nombre' => 'Fotografía en Plata sobre Gelatina Firmada (1934)',
                'descripcion' => 'Copia de época (vintage print) revelada en cuarto oscuro por el estudio original, con sello húmedo al reverso y paspartú libre de ácido.',
                'precio' => 41000.00,
                'graduacion' => 'MINT (M) · COA NOTARIADO · ADQUISICIÓN SELECTA',
                'marca' => 'Archivo Fotográfico Mexicano',
                'serie' => 'Vanguardia Documental S. XX',
                'categoria' => 'historicos',
                'material' => 'Plata sobre Gelatina en Papel Fibra',
                'edicion_limitada' => true,
                'stock_central' => 0,
                'stock_coyoacan' => 0,
                'stock_almacen' => 0,
                'sede_pieza_unica' => 'central',
                'serie_unica' => 'ARC-MEM-1934-002',
                'certificado' => 'COA-NOT-2026-309',
            ],
            [
                'sku' => 'MEM-1912-PAR',
                'nombre' => 'Partitura Autógrafa de Estudio en Sol Menor (1912)',
                'descripcion' => 'Lote limitado en Sucursal Central Bellas Artes: 2 cuadernos musicales de 12 folios con anotaciones de dinámica a lápiz. Conservado en carpeta museográfica.',
                'precio' => 21500.00,
                'graduacion' => 'VERY FINE (VF) · ADQUISICIÓN SELECTA',
                'marca' => 'Fondo Conservatorio Nacional',
                'serie' => 'Manuscritos Musicales del Porfiriato',
                'categoria' => 'historicos',
                'material' => 'Papel Pautado de Lino',
                'edicion_limitada' => true,
                'stock_central' => 2,
                'stock_coyoacan' => 0,
                'stock_almacen' => 0,
                'sede_pieza_unica' => null,
                'serie_unica' => null,
                'certificado' => null,
            ],
            [
                'sku' => 'MEM-1934-PBA',
                'nombre' => 'Programa Inaugural Palacio de Bellas Artes (1934)',
                'descripcion' => 'Stock Masivo en Sucursal Central Bellas Artes: Folleto conmemorativo oficial de la inauguración del Palacio de Bellas Artes en septiembre de 1934.',
                'precio' => 7800.00,
                'graduacion' => 'VERY FINE (VF) · TIRAJE MÚLTIPLE',
                'marca' => 'Imprenta Mundial / INBA',
                'serie' => 'Efemérides Culturales de México',
                'categoria' => 'historicos',
                'material' => 'Papel Couché & Huecograbado',
                'edicion_limitada' => false,
                'stock_central' => 12,
                'stock_coyoacan' => 0,
                'stock_almacen' => 0,
                'sede_pieza_unica' => null,
                'serie_unica' => null,
                'certificado' => null,
            ],
            [
                'sku' => 'MEM-1954-COY',
                'nombre' => 'Litografía Taller de Gráfica Popular Coyoacán (1954)',
                'descripcion' => 'Stock Masivo en Sucursal Coyoacán: Grabado en linóleo de tiraje amplio impreso sobre papel de algodón en Coyoacán. Disponible en múltiples unidades.',
                'precio' => 5400.00,
                'graduacion' => 'NEAR MINT (NM) · TIRAJE MÚLTIPLE',
                'marca' => 'Taller de Gráfica Popular',
                'serie' => 'Gráfica Mexicana de Medio Siglo',
                'categoria' => 'historicos',
                'material' => 'Linóleo sobre Papel Fabriano',
                'edicion_limitada' => false,
                'stock_central' => 0,
                'stock_coyoacan' => 9,
                'stock_almacen' => 0,
                'sede_pieza_unica' => null,
                'serie_unica' => null,
                'certificado' => null,
            ],
            [
                'sku' => 'MEM-1968-IMP',
                'nombre' => 'Kit de Conservación Museográfica & Estuche Antiácido',
                'descripcion' => 'Stock Masivo en Almacén General: Set profesional de fundas de polipropileno grado archivo, guantes de nitrilo y caja libre de ácido para libros y vinilos.',
                'precio' => 3200.00,
                'graduacion' => 'MINT (M) · ACCESORIO DE BÓVEDA',
                'marca' => 'Archivum Conservation Lab',
                'serie' => 'Insumos de Preservación Documental',
                'categoria' => 'juguetes',
                'material' => 'Cartón Libre de Ácido pH Neutro',
                'edicion_limitada' => false,
                'stock_central' => 0,
                'stock_coyoacan' => 0,
                'stock_almacen' => 18,
                'sede_pieza_unica' => null,
                'serie_unica' => null,
                'certificado' => null,
            ],
            [
                'sku' => 'COL-1997-EVA1',
                'nombre' => 'Estatua Evangelion Test Type-01 (Edición Limitada Kaiyodo 1997)',
                'descripcion' => 'Figuras Coleccionables (Cultura Pop & Anime): Escultura en resina pintada a mano supervisada por Gainax Japón. Incluye pedestal con número de serie de tiraje.',
                'precio' => 18900.00,
                'graduacion' => 'MINT (M) · FIGURA ANIME · EDICIÓN LIMITADA',
                'marca' => 'Kaiyodo / Gainax Japón',
                'serie' => 'Neon Genesis Evangelion Masterpiece',
                'categoria' => 'figuras',
                'material' => 'Resina Polystone Pintada a Mano',
                'edicion_limitada' => true,
                'stock_central' => 2,
                'stock_coyoacan' => 1,
                'stock_almacen' => 0,
                'sede_pieza_unica' => null,
                'serie_unica' => null,
                'certificado' => null,
            ],
            [
                'sku' => 'COL-1993-DBZG',
                'nombre' => 'Figura Dragon Ball Z Super Saiyan Goku (Banpresto Japón 1993)',
                'descripcion' => 'Figuras Coleccionables (Cultura Pop & Anime): Figura vintage con caja sellada original de Toei Animation Japón. Conservada en acrílico anti-UV de coleccionista.',
                'precio' => 14200.00,
                'graduacion' => 'MINT (M) · CULTURA POP & ANIME · EN CAJA ORIGINAL',
                'marca' => 'Banpresto / Bird Studio',
                'serie' => 'Dragon Ball Z Vintage Series',
                'categoria' => 'figuras',
                'material' => 'PVC Grado Colección & Acrílico UV',
                'edicion_limitada' => true,
                'stock_central' => 1,
                'stock_coyoacan' => 2,
                'stock_almacen' => 0,
                'sede_pieza_unica' => null,
                'serie_unica' => null,
                'certificado' => null,
            ],
            [
                'sku' => 'COL-1965-ROB',
                'nombre' => 'Robot Mecánico de Hojalata Space Trooper (Japón Vintage 1965)',
                'descripcion' => 'Juguetes y Objetos Manufacturados: Juguete mecánico a cuerda en hojalata litografiada manufacturado por Yonezawa Toys Japón. Mecanismo de marcha y chispa plenamente funcional.',
                'precio' => 21800.00,
                'graduacion' => 'VERY FINE (VF) · JUGUETE MECÁNICO VINTAGE',
                'marca' => 'Yonezawa Toys Japón',
                'serie' => 'Space Age Tin Toy Series',
                'categoria' => 'juguetes',
                'material' => 'Hojalata Litografiada & Resorte de Acero',
                'edicion_limitada' => true,
                'stock_central' => 1,
                'stock_coyoacan' => 0,
                'stock_almacen' => 1,
                'sede_pieza_unica' => null,
                'serie_unica' => null,
                'certificado' => null,
            ],
            [
                'sku' => 'COL-1914-BIL',
                'nombre' => 'Billete Revolucionario 10 Pesos Ejército Constitucionalista (1914)',
                'descripcion' => 'Monedas y Billetes Históricos: Emisión oficial del Gobierno Provisional de México firmada en Monclova. Papel moneda histórico en estado sin circular (UNC).',
                'precio' => 8900.00,
                'graduacion' => 'MINT (M) · NUMISMÁTICA & NOTAFILIA REVOLUCIONARIA',
                'marca' => 'Gobierno Constitucionalista de México',
                'serie' => 'Papel Moneda de la Revolución Mexicana',
                'categoria' => 'numismatica',
                'material' => 'Papel Moneda de Seguridad',
                'edicion_limitada' => true,
                'stock_central' => 3,
                'stock_coyoacan' => 2,
                'stock_almacen' => 4,
                'sede_pieza_unica' => null,
                'serie_unica' => null,
                'certificado' => null,
            ],
            [
                'sku' => 'COL-1768-COL8',
                'nombre' => 'Moneda Virreinal 8 Reales Columnario de Plata (1768)',
                'descripcion' => 'Monedas y Billetes Históricos: Moneda de 8 Reales acuñada a volante en la Real Casa de Moneda de México bajo Carlos III. Plata pura con pátina histórica intocada.',
                'precio' => 38500.00,
                'graduacion' => 'VERY FINE (VF) · MONEDAS & BILLETES HISTÓRICOS',
                'marca' => 'Real Casa de Moneda de México',
                'serie' => 'Numismática Virreinal Novohispana',
                'categoria' => 'numismatica',
                'material' => 'Plata Ley .903',
                'edicion_limitada' => true,
                'stock_central' => 1,
                'stock_coyoacan' => 0,
                'stock_almacen' => 0,
                'sede_pieza_unica' => null,
                'serie_unica' => null,
                'certificado' => null,
            ],
            [
                'sku' => 'COL-1980-NIN',
                'nombre' => 'Consola Nintendo Game & Watch Ball (Primera Edición Japón 1980)',
                'descripcion' => 'Coleccionables de Videojuegos: El primer sistema portátil creado por Gunpei Yokoi para Nintendo (Modelo AC-01). En caja de colección con manual y batería sellada.',
                'precio' => 26900.00,
                'graduacion' => 'NEAR MINT (NM) · VIDEOJUEGOS RETRO EN CAJA',
                'marca' => 'Nintendo Co., Ltd. Japón',
                'serie' => 'Silver Series Game & Watch (AC-01)',
                'categoria' => 'videojuegos',
                'material' => 'Plástico ABS & Pantalla LCD Reflectiva',
                'edicion_limitada' => true,
                'stock_central' => 1,
                'stock_coyoacan' => 1,
                'stock_almacen' => 0,
                'sede_pieza_unica' => null,
                'serie_unica' => null,
                'certificado' => null,
            ],
            [
                'sku' => 'COL-1987-ZEL',
                'nombre' => 'Cartucho The Legend of Zelda (Gold Cartridge NES 1987 Sellado)',
                'descripcion' => 'Coleccionables de Videojuegos: Cartucho dorado original para Nintendo Entertainment System con batería de respaldo interna y sello pericial de graduación Wata/VGA.',
                'precio' => 34000.00,
                'graduacion' => 'MINT (M) · CARTUCHO DORADO DE COLECCIÓN · VIDEOJUEGOS',
                'marca' => 'Nintendo of America',
                'serie' => 'NES Classic Gold Series (NES-ZL-USA)',
                'categoria' => 'videojuegos',
                'material' => 'Carcasa Dorada Metalizada & Placa PCB',
                'edicion_limitada' => true,
                'stock_central' => 1,
                'stock_coyoacan' => 0,
                'stock_almacen' => 1,
                'sede_pieza_unica' => null,
                'serie_unica' => null,
                'certificado' => null,
            ],
        ];

        foreach ($memorabilia as $item) {
            $producto = Producto::updateOrCreate(
                ['nombre' => $item['nombre']],
                [
                    'catalogo_id' => $catMemorabilia->id,
                    'descripcion' => $item['descripcion'],
                    'precio' => $item['precio'],
                    'activo' => true,
                ]
            );

            Coleccionable::updateOrCreate(
                ['producto_id' => $producto->id],
                [
                    'marca' => $item['marca'],
                    'serie' => $item['serie'],
                    'categoria' => $item['categoria'] ?? 'historicos',
                    'material' => $item['material'],
                    'edicion_limitada' => $item['edicion_limitada'],
                ]
            );

            $this->registrarVarianteYExistencias(
                $producto,
                $item['sku'],
                $item['graduacion'],
                $item['precio'],
                $sucursalBellasArtes->id,
                $item['stock_central'],
                $sucursalCoyoacan->id,
                $item['stock_coyoacan'],
                $sucursalAlmacen->id,
                $item['stock_almacen'],
                $item['sede_pieza_unica'],
                $item['serie_unica'],
                $item['certificado'],
            );
        }

        // 6. Sembrar Promociones Exclusivas con Fase de Preventa Anticipada y Fase de Apertura Pública
        $this->sembrarPromocionesYClientesFrecuentes();
    }

    /**
     * Registra promociones con preventa anticipada para Clientes Frecuentes (5+ compras)
     * y asegura que exista al menos una cuenta de cliente frecuente de demostración.
     */
    private function sembrarPromocionesYClientesFrecuentes(): void
    {
        if (! Schema::hasTable('promociones')) {
            return;
        }

        $tieneColumnasPreventa = Schema::hasColumn('promociones', 'producto_id')
            && Schema::hasColumn('promociones', 'preventa_inicia_en')
            && Schema::hasColumn('promociones', 'apertura_publica_en');

        $prodBellasArtes = Producto::where('nombre', 'like', '%Programa Inaugural Palacio de Bellas Artes%')->first();
        $prodLoveSupreme = Producto::where('nombre', 'like', '%A Love Supreme%')->first();
        $prodLedZeppelin = Producto::where('nombre', 'like', '%Led Zeppelin IV%')->first();

        $promocionesSemilla = [
            [
                'nombre' => 'Preventa Exclusiva VIP: Programa Inaugural Palacio de Bellas Artes (Edición Especial)',
                'descripcion' => 'Acceso anticipado exclusivo para Clientes Frecuentes (5+ compras) antes de la apertura oficial al público general. Incluye carpeta museográfica conmemorativa.',
                'tipo_descuento' => 'porcentaje',
                'valor' => 15.00,
                'inicia_en' => now()->subDays(2)->toDateString(),
                'termina_en' => now()->addDays(10)->toDateString(),
                'activa' => true,
                'extra' => [
                    'producto_id' => $prodBellasArtes?->id,
                    'es_preventa_exclusiva' => true,
                    'preventa_inicia_en' => now()->subDays(2),
                    'apertura_publica_en' => now()->addDays(2),
                    'badge_etiqueta' => 'PREVENTA ACTIVA · ACCESO ANTICIPADO VIP',
                ],
            ],
            [
                'nombre' => 'Apertura Oficial: A Love Supreme (Lanzamiento Audiófilo 180g)',
                'descripcion' => 'Promoción desbloqueada en Fase de Apertura para todos los clientes comunes de la tienda. Prensado audiófilo con carpeta desplegable laminada.',
                'tipo_descuento' => 'porcentaje',
                'valor' => 12.00,
                'inicia_en' => now()->subDays(5)->toDateString(),
                'termina_en' => now()->addDays(5)->toDateString(),
                'activa' => true,
                'extra' => [
                    'producto_id' => $prodLoveSupreme?->id,
                    'es_preventa_exclusiva' => true,
                    'preventa_inicia_en' => now()->subDays(5),
                    'apertura_publica_en' => now()->subDay(),
                    'badge_etiqueta' => 'EDICIÓN ESPECIAL · APERTURA PÚBLICA',
                ],
            ],
            [
                'nombre' => 'Edición Especial: Led Zeppelin IV (Prensado Audiófilo 180g)',
                'descripcion' => 'Lanzamiento especial en Fase de Preventa Anticipada para Clientes Frecuentes VIP (5+ compras). Masterizado desde las cintas analógicas originales.',
                'tipo_descuento' => 'monto',
                'valor' => 900.00,
                'inicia_en' => now()->subDay()->toDateString(),
                'termina_en' => now()->addDays(12)->toDateString(),
                'activa' => true,
                'extra' => [
                    'producto_id' => $prodLedZeppelin?->id,
                    'es_preventa_exclusiva' => true,
                    'preventa_inicia_en' => now()->subDay(),
                    'apertura_publica_en' => now()->addDays(3),
                    'badge_etiqueta' => 'EDICIÓN ESPECIAL · PREVENTA VIP',
                ],
            ],
        ];

        foreach ($promocionesSemilla as $itemPromo) {
            $atributos = [
                'descripcion' => $itemPromo['descripcion'],
                'tipo_descuento' => $itemPromo['tipo_descuento'],
                'valor' => $itemPromo['valor'],
                'inicia_en' => $itemPromo['inicia_en'],
                'termina_en' => $itemPromo['termina_en'],
                'activa' => $itemPromo['activa'],
            ];

            if ($tieneColumnasPreventa) {
                $atributos = array_merge($atributos, $itemPromo['extra']);
            }

            Promocion::updateOrCreate(
                ['nombre' => $itemPromo['nombre']],
                $atributos
            );
        }

        // Configurar a Elena Ramírez (elena.ramirez@gmail.com) con 5 compras históricas para demostrar el nivel Cliente Frecuente VIP
        if (Schema::hasTable('clientes') && Schema::hasTable('pedidos') && Schema::hasTable('direcciones')) {
            $clienteElena = Cliente::where('email', 'elena.ramirez@gmail.com')->first();
            if ($clienteElena !== null) {
                $direccion = Direccion::firstOrCreate(
                    ['cliente_id' => $clienteElena->id, 'predeterminada' => true],
                    [
                        'alias' => 'Residencia Principal',
                        'calle' => 'Av. Paseo de la Reforma 222',
                        'numero_exterior' => '222',
                        'colonia' => 'Juárez',
                        'municipio' => 'Cuauhtémoc',
                        'estado' => 'CDMX',
                        'codigo_postal' => '06600',
                        'pais' => 'México',
                    ]
                );

                for ($i = 1; $i <= Cliente::COMPRAS_MINIMAS_FRECUENTE; $i++) {
                    Pedido::firstOrCreate(
                        ['folio' => sprintf('ARC-HIST-VIP-%03d', $i)],
                        [
                            'cliente_id' => $clienteElena->id,
                            'direccion_envio_id' => $direccion->id,
                            'estatus' => 'entregado',
                            'subtotal' => 7800.00,
                            'descuento' => 0.00,
                            'total' => 7800.00,
                        ]
                    );
                }

                app(PromocionDescuentoService::class)->sincronizarMembresiaPorCompras($clienteElena);
            }
        }
    }

    /**
     * Crea o actualiza la VarianteSku, registra Existencia únicamente en la(s) sucursal(es)
     * a las que pertenece el artículo (evitando repetir el mismo inventario en todas las sedes)
     * y sincroniza la PiezaUnica certificada si corresponde.
     */
    private function registrarVarianteYExistencias(
        Producto $producto,
        string $sku,
        string $graduacion,
        float $precio,
        int $sucursalCentralId,
        int $stockCentral,
        int $sucursalCoyoacanId,
        int $stockCoyoacan,
        int $sucursalAlmacenId,
        int $stockAlmacen,
        ?string $sedePiezaUnica,
        ?string $serieUnica,
        ?string $certificado,
    ): void {
        $variante = VarianteSku::updateOrCreate(
            ['sku' => $sku],
            [
                'producto_id' => $producto->id,
                'descripcion' => $graduacion,
                'precio' => $precio,
                'activa' => true,
            ]
        );

        $mapaStockPorSede = [
            'central' => ['id' => $sucursalCentralId, 'stock' => $stockCentral],
            'coyoacan' => ['id' => $sucursalCoyoacanId, 'stock' => $stockCoyoacan],
            'almacen' => ['id' => $sucursalAlmacenId, 'stock' => $stockAlmacen],
        ];

        $existenciasPorClave = [];
        $sucursalesConCatalogoIds = [];

        foreach ($mapaStockPorSede as $clave => $datosSede) {
            $perteneceASede = $datosSede['stock'] > 0 || $sedePiezaUnica === $clave;

            if ($perteneceASede) {
                $sucursalesConCatalogoIds[] = $datosSede['id'];
                $existenciasPorClave[$clave] = Existencia::updateOrCreate(
                    [
                        'variante_sku_id' => $variante->id,
                        'sucursal_id' => $datosSede['id'],
                    ],
                    [
                        'cantidad' => $datosSede['stock'],
                        'stock_minimo' => 1,
                    ]
                );
            }
        }

        if ($serieUnica !== null) {
            $claveSede = $sedePiezaUnica ?? 'central';
            $existenciaObjetivo = $existenciasPorClave[$claveSede] ?? reset($existenciasPorClave);
            $stockTotal = $stockCentral + $stockCoyoacan + $stockAlmacen;

            if ($existenciaObjetivo instanceof Existencia) {
                PiezaUnica::updateOrCreate(
                    ['numero_serie' => $serieUnica],
                    [
                        'existencia_id' => $existenciaObjetivo->id,
                        'certificado' => $certificado,
                        'notas' => "Ejemplar único 1/1 catalogado en bóveda climatizada ({$graduacion}).",
                        'estado' => $stockTotal > 0 ? 'disponible' : 'vendida',
                    ]
                );
            }
        } else {
            // Si es un artículo de stock masivo, asegurar que no tenga registros de PiezaUnica residuales
            $idsExistencias = Existencia::where('variante_sku_id', $variante->id)->pluck('id');
            if ($idsExistencias->isNotEmpty()) {
                PiezaUnica::whereIn('existencia_id', $idsExistencias)->delete();
            }
        }

        // Eliminar registros de existencia residuales en sucursales a las que no pertenece esta pieza
        if (! empty($sucursalesConCatalogoIds)) {
            $existenciasObsoletasIds = Existencia::where('variante_sku_id', $variante->id)
                ->whereNotIn('sucursal_id', $sucursalesConCatalogoIds)
                ->pluck('id');

            if ($existenciasObsoletasIds->isNotEmpty()) {
                $existenciaPrincipal = reset($existenciasPorClave);
                if ($existenciaPrincipal instanceof Existencia) {
                    PiezaUnica::whereIn('existencia_id', $existenciasObsoletasIds)
                        ->update(['existencia_id' => $existenciaPrincipal->id]);
                    MovimientoInventario::whereIn('existencia_id', $existenciasObsoletasIds)
                        ->update(['existencia_id' => $existenciaPrincipal->id]);
                } else {
                    PiezaUnica::whereIn('existencia_id', $existenciasObsoletasIds)->delete();
                    MovimientoInventario::whereIn('existencia_id', $existenciasObsoletasIds)->delete();
                }

                Existencia::whereIn('id', $existenciasObsoletasIds)->delete();
            }
        }
    }
}
