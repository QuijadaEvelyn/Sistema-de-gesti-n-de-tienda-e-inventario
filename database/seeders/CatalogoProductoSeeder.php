<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Catalogo;
use App\Models\Coleccionable;
use App\Models\Disco;
use App\Models\Existencia;
use App\Models\Libro;
use App\Models\PiezaUnica;
use App\Models\Producto;
use App\Models\Sucursal;
use App\Models\VarianteSku;
use Illuminate\Database\Seeder;

class CatalogoProductoSeeder extends Seeder
{
    /**
     * Pobla la base de datos de ARCHIVUM con sucursales, catálogos por categoría
     * (Libros Raros & Incunables, Discos & Vinilos Históricos, Memorabilia & Archivo)
     * y coleccionables altamente realistas con SKUs, graduaciones, precios tasados y stock.
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
            ['nombre' => 'Libros Raros & Incunables'],
            [
                'tipo' => 'libro',
                'descripcion' => 'Primeras ediciones hispanoamericanas, ejemplares autografiados, incunables modernos y encuadernaciones de época con peritaje bibliográfico.',
                'activo' => true,
            ]
        );

        $catVinilos = Catalogo::updateOrCreate(
            ['nombre' => 'Discos & Vinilos Históricos'],
            [
                'tipo' => 'disco',
                'descripcion' => 'Prensados originales en 180g, matrices mono de época, sellos Blue Note, Columbia e Impulse! con graduación Goldmine certificada.',
                'activo' => true,
            ]
        );

        $catMemorabilia = Catalogo::updateOrCreate(
            ['nombre' => 'Memorabilia & Archivo'],
            [
                'tipo' => 'coleccionable',
                'descripcion' => 'Manuscritos autógrafos, correspondencia diplomática, partituras históricas y obra gráfica con Certificado de Autenticidad (COA) notariado.',
                'activo' => true,
            ]
        );

        // 3. Piezas de Libros Raros & Incunables
        $libros = [
            [
                'sku' => 'LIB-1967-SUD',
                'nombre' => 'Cien Años de Soledad (1ª Edición Sudamericana)',
                'descripcion' => 'Tiraje inicial de Buenos Aires (Mayo 1967, 8,000 ejemplares). Cubierta original con galeón en la selva diseñada por Iris Alba. Sin restauraciones.',
                'precio' => 32000.00,
                'graduacion' => 'VERY FINE (VF) · PRIMERA EDICIÓN',
                'isbn' => '978-950-07-0010-1',
                'autor' => 'Gabriel García Márquez',
                'editorial' => 'Editorial Sudamericana',
                'anio_publicacion' => 1967,
                'paginas' => 351,
                'stock_central' => 3,
                'stock_coyoacan' => 1,
                'serie_unica' => 'ARC-LIB-1967-001',
                'certificado' => 'COA-BIB-2026-014',
            ],
            [
                'sku' => 'LIB-1955-FCE',
                'nombre' => 'Pedro Páramo (Edición Príncipe FCE)',
                'descripcion' => 'Primera edición de 2,000 ejemplares en la Colección Letras Mexicanas N.º 19. Encuadernación en tela editorial con sobrecubierta intacta.',
                'precio' => 44500.00,
                'graduacion' => 'NEAR MINT (NM) · ADQUISICIÓN SELECTA',
                'isbn' => '978-968-16-0001-2',
                'autor' => 'Juan Rulfo',
                'editorial' => 'Fondo de Cultura Económica',
                'anio_publicacion' => 1955,
                'paginas' => 155,
                'stock_central' => 2,
                'stock_coyoacan' => 1,
                'serie_unica' => 'ARC-LIB-1955-002',
                'certificado' => 'COA-BIB-2026-019',
            ],
            [
                'sku' => 'LIB-1949-LOS',
                'nombre' => 'El Aleph (Ejemplar Dedicado por el Autor)',
                'descripcion' => 'Primera edición impresa por Editorial Losada en Buenos Aires. Incluye dedicatoria manuscrita en tinta azul fechada en 1951 con dictamen caligráfico.',
                'precio' => 58000.00,
                'graduacion' => 'MINT (M) · ADQUISICIÓN SELECTA',
                'isbn' => '978-950-03-0049-8',
                'autor' => 'Jorge Luis Borges',
                'editorial' => 'Editorial Losada',
                'anio_publicacion' => 1949,
                'paginas' => 146,
                'stock_central' => 1,
                'stock_coyoacan' => 1,
                'serie_unica' => 'ARC-LIB-1949-003',
                'certificado' => 'COA-BIB-2026-022',
            ],
            [
                'sku' => 'LIB-1950-FCE',
                'nombre' => 'El Laberinto de la Soledad (1ª Edición Cuadernos Americanos)',
                'descripcion' => 'Edición original impresa en los talleres de Cuadernos Americanos en papel Chamois. Intonso en pliegos superiores.',
                'precio' => 16900.00,
                'graduacion' => 'VERY FINE (VF) · PRIMERA EDICIÓN',
                'isbn' => '978-968-16-0050-0',
                'autor' => 'Octavio Paz',
                'editorial' => 'Cuadernos Americanos',
                'anio_publicacion' => 1950,
                'paginas' => 195,
                'stock_central' => 4,
                'stock_coyoacan' => 2,
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
                $item['serie_unica'],
                $item['certificado'],
            );
        }

        // 4. Piezas de Discos & Vinilos Históricos
        $vinilos = [
            [
                'sku' => 'VIN-1959-COL',
                'nombre' => 'Kind of Blue (Mono 1st Press 6-Eye)',
                'descripcion' => 'Matriz original XLP 47324-1A con etiqueta Columbia «6-eye» deep groove. Sonido analógico sin reprocesar con funda interior original.',
                'precio' => 18500.00,
                'graduacion' => 'MINT (M) · VINILO LP 180G · ADQUISICIÓN SELECTA',
                'artista' => 'Miles Davis',
                'sello' => 'Columbia Records CL 1355',
                'genero' => 'Modal Jazz',
                'formato' => 'vinilo',
                'anio_lanzamiento' => 1959,
                'stock_central' => 3,
                'stock_coyoacan' => 1,
                'serie_unica' => 'ARC-VIN-1959-001',
                'certificado' => 'COA-AUD-2026-101',
            ],
            [
                'sku' => 'VIN-1957-BN1577',
                'nombre' => 'Blue Train (Plastylite RVG Ear Press)',
                'descripcion' => 'Grabado por Rudy Van Gelder con sello de oído «ear» en el surco muerto y dirección 47 West 63rd NYC en etiqueta.',
                'precio' => 27500.00,
                'graduacion' => 'NEAR MINT (NM) · BLUE NOTE RVG · ADQUISICIÓN SELECTA',
                'artista' => 'John Coltrane',
                'sello' => 'Blue Note BLP 1577',
                'genero' => 'Hard Bop Jazz',
                'formato' => 'vinilo',
                'anio_lanzamiento' => 1957,
                'stock_central' => 2,
                'stock_coyoacan' => 1,
                'serie_unica' => 'ARC-VIN-1957-002',
                'certificado' => 'COA-AUD-2026-104',
            ],
            [
                'sku' => 'VIN-1965-IMP77',
                'nombre' => 'A Love Supreme (Gatefold 1st Impulse! Mono)',
                'descripcion' => 'Carpeta desplegable laminada original con estampado VAN GELDER en ambas caras. Etiqueta naranja y negra primera tirada.',
                'precio' => 16200.00,
                'graduacion' => 'VERY FINE (VF) · VINILO LP',
                'artista' => 'John Coltrane Quartet',
                'sello' => 'Impulse! A-77',
                'genero' => 'Spiritual Jazz',
                'formato' => 'vinilo',
                'anio_lanzamiento' => 1965,
                'stock_central' => 4,
                'stock_coyoacan' => 2,
                'serie_unica' => null,
                'certificado' => null,
            ],
            [
                'sku' => 'VIN-1969-PCS7088',
                'nombre' => 'Abbey Road (UK 1st Press Misaligned Apple)',
                'descripcion' => 'Prensado británico matriz YEX 749-2 / 750-1 con el logotipo de la manzana desalineado en contraportada y sin crédito de «Her Majesty».',
                'precio' => 22900.00,
                'graduacion' => 'MINT (M) · ADQUISICIÓN SELECTA',
                'artista' => 'The Beatles',
                'sello' => 'Apple Records PCS 7088',
                'genero' => 'Rock Clásico',
                'formato' => 'vinilo',
                'anio_lanzamiento' => 1969,
                'stock_central' => 2,
                'stock_coyoacan' => 1,
                'serie_unica' => 'ARC-VIN-1969-004',
                'certificado' => 'COA-AUD-2026-112',
            ],
            [
                'sku' => 'CD-1983-CP35',
                'nombre' => 'The Dark Side of the Moon (1st Japan Black Triangle CD)',
                'descripcion' => 'Primera edición mundial en Disco Compacto (1983) prensada por Toshiba-EMI en Japón (CP35-3017). Superficie plateada refractiva con triángulo negro.',
                'precio' => 14800.00,
                'graduacion' => 'MINT (M) · CD COMPACT DISC · ADQUISICIÓN SELECTA',
                'artista' => 'Pink Floyd',
                'sello' => 'Harvest / Toshiba-EMI CP35-3017',
                'genero' => 'Rock Progresivo',
                'formato' => 'CD',
                'anio_lanzamiento' => 1983,
                'stock_central' => 2,
                'stock_coyoacan' => 1,
                'serie_unica' => 'ARC-CD-1983-005',
                'certificado' => 'COA-AUD-2026-118',
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
                $item['serie_unica'],
                $item['certificado'],
            );
        }

        // 5. Piezas de Memorabilia & Archivo
        $memorabilia = [
            [
                'sku' => 'MEM-1928-AUT',
                'nombre' => 'Carta Manuscrita Bellas Artes (1928)',
                'descripcion' => 'Pluma estilográfica con tinta ferrogálica original sobre papel membretado. Incluye certificado notarial e informe pericial caligráfico.',
                'precio' => 9400.00,
                'graduacion' => 'COA NOTARIADO · MEMORABILIA',
                'marca' => 'Colección Diplomática',
                'serie' => 'Archivo Histórico Bellas Artes',
                'material' => 'Papel Algodón & Tinta Ferrogálica',
                'edicion_limitada' => true,
                'stock_central' => 2,
                'stock_coyoacan' => 1,
                'serie_unica' => 'ARC-MEM-1928-001',
                'certificado' => 'COA-NOT-2026-301',
            ],
            [
                'sku' => 'MEM-1934-FOT',
                'nombre' => 'Fotografía en Plata sobre Gelatina Firmada (1934)',
                'descripcion' => 'Copia de época (vintage print) revelada en cuarto oscuro por el estudio original, con sello húmedo al reverso y paspartú libre de ácido.',
                'precio' => 41000.00,
                'graduacion' => 'MINT (M) · COA NOTARIADO · ADQUISICIÓN SELECTA',
                'marca' => 'Archivo Fotográfico Mexicano',
                'serie' => 'Vanguardia Documental S. XX',
                'material' => 'Plata sobre Gelatina en Papel Fibra',
                'edicion_limitada' => true,
                'stock_central' => 0,
                'stock_coyoacan' => 0,
                'serie_unica' => 'ARC-MEM-1934-002',
                'certificado' => 'COA-NOT-2026-309',
            ],
            [
                'sku' => 'MEM-1912-PAR',
                'nombre' => 'Partitura Autógrafa de Estudio en Sol Menor (1912)',
                'descripcion' => 'Manuscrito musical de 12 folios con anotaciones de dinámica a lápiz del compositor. Conservado en carpeta museográfica.',
                'precio' => 21500.00,
                'graduacion' => 'VERY FINE (VF) · ADQUISICIÓN SELECTA',
                'marca' => 'Fondo Conservatorio Nacional',
                'serie' => 'Manuscritos Musicales del Porfiriato',
                'material' => 'Papel Pautado de Lino',
                'edicion_limitada' => true,
                'stock_central' => 2,
                'stock_coyoacan' => 0,
                'serie_unica' => 'ARC-MEM-1912-003',
                'certificado' => 'COA-NOT-2026-315',
            ],
            [
                'sku' => 'MEM-1934-PBA',
                'nombre' => 'Programa Inaugural Palacio de Bellas Artes (1934)',
                'descripcion' => 'Folleto conmemorativo oficial de la inauguración del Palacio de Bellas Artes en septiembre de 1934 con tipografía Art Déco.',
                'precio' => 7800.00,
                'graduacion' => 'VERY FINE (VF) · ARCHIVO DOCUMENTAL',
                'marca' => 'Imprenta Mundial / INBA',
                'serie' => 'Efemérides Culturales de México',
                'material' => 'Papel Couché & Huecograbado',
                'edicion_limitada' => false,
                'stock_central' => 5,
                'stock_coyoacan' => 2,
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
                $item['serie_unica'],
                $item['certificado'],
            );
        }
    }

    /**
     * Crea o actualiza la VarianteSku, las Existencias en ambas sucursales y la PiezaUnica certificada.
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

        $existenciaCentral = Existencia::updateOrCreate(
            [
                'variante_sku_id' => $variante->id,
                'sucursal_id' => $sucursalCentralId,
            ],
            [
                'cantidad' => $stockCentral,
                'stock_minimo' => 1,
            ]
        );

        Existencia::updateOrCreate(
            [
                'variante_sku_id' => $variante->id,
                'sucursal_id' => $sucursalCoyoacanId,
            ],
            [
                'cantidad' => $stockCoyoacan,
                'stock_minimo' => 1,
            ]
        );

        $sucursalAlmacen = Sucursal::where('nombre', 'Almacén General')->first();
        if ($sucursalAlmacen) {
            $stockAlmacen = in_array($sku, ['MEM-1934-FOT', 'MEM-1912-PAR'], true)
                ? 0
                : 1;

            Existencia::updateOrCreate(
                [
                    'variante_sku_id' => $variante->id,
                    'sucursal_id' => $sucursalAlmacen->id,
                ],
                [
                    'cantidad' => $stockAlmacen,
                    'stock_minimo' => 1,
                ]
            );
        }

        if ($serieUnica !== null) {
            PiezaUnica::updateOrCreate(
                ['numero_serie' => $serieUnica],
                [
                    'existencia_id' => $existenciaCentral->id,
                    'certificado' => $certificado,
                    'notas' => "Ejemplar catalogado en bóveda climatizada ({$graduacion}).",
                    'estado' => 'disponible',
                ]
            );
        }
    }
}
