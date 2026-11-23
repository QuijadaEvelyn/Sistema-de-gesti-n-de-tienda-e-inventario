<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Bitacora;
use App\Models\Catalogo;
use App\Models\Coleccionable;
use App\Models\Disco;
use App\Models\Existencia;
use App\Models\Libro;
use App\Models\MovimientoInventario;
use App\Models\PiezaUnica;
use App\Models\Producto;
use App\Models\Sucursal;
use App\Models\Usuario;
use App\Models\VarianteSku;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use RuntimeException;
use Throwable;
use ZipArchive;

class ImportacionProductosService
{
    /**
     * Encabezados oficiales por pestaña del libro Excel (.xlsx) de ARCHIVUM.
     *
     * @var array<string, list<string>>
     */
    public const COLUMNAS_EXCEL = [
        'Libros' => [
            'sku',
            'titulo',
            'autor',
            'editorial',
            'isbn',
            'anio_publicacion',
            'paginas',
            'genero_literario',
            'estado_conservacion',
            'sede',
            'precio',
            'existencias',
            'es_pieza_unica',
            'es_edicion_especial',
            'codigo_coa',
            'numero_serie',
            'descripcion',
            'archivo_imagen',
        ],
        'Discos' => [
            'sku',
            'titulo',
            'artista',
            'disquera_sello',
            'formato_discografico',
            'anio_lanzamiento',
            'genero_musical',
            'estado_conservacion',
            'sede',
            'precio',
            'existencias',
            'es_pieza_unica',
            'es_edicion_especial',
            'codigo_coa',
            'numero_serie',
            'descripcion',
            'archivo_imagen',
        ],
        'Coleccionables' => [
            'sku',
            'titulo',
            'fabricante_marca',
            'serie',
            'subcategoria_coleccionable',
            'material',
            'estado_conservacion',
            'sede',
            'precio',
            'existencias',
            'es_pieza_unica',
            'es_edicion_especial',
            'codigo_coa',
            'numero_serie',
            'descripcion',
            'archivo_imagen',
        ],
    ];

    /**
     * Encabezados oficiales de la plantilla CSV de respaldo.
     *
     * @var list<string>
     */
    public const COLUMNAS_PLANTILLA = [
        'sku',
        'titulo',
        'autor_artista',
        'categoria',
        'sede',
        'precio',
        'existencias',
        'es_pieza_unica',
        'codigo_coa',
        'descripcion',
        'archivo_imagen',
    ];

    /**
     * Genera la plantilla oficial Excel (.xlsx) con 3 pestañas (Libros, Discos, Coleccionables),
     * encabezados estilizados, anchos autoajustados y una fila de ejemplo por hoja.
     */
    public function generarPlantillaExcel(): string
    {
        $spreadsheet = new Spreadsheet;
        $spreadsheet->removeSheetByIndex(0);

        $ejemplos = [
            'Libros' => [
                'LIB-EJEMPLO-001',
                'Cien Años de Soledad (Edición Especial)',
                'Gabriel García Márquez',
                'Editorial Sudamericana',
                '978-950-07-0010-1',
                1967,
                471,
                'novela',
                'MINT (M)',
                'Sucursal Central Bellas Artes',
                1850.00,
                1,
                1,
                1,
                'COA-BIB-2026-001',
                'ARC-LIB-1967-001',
                'Edición de colección en pasta dura con sobrecubierta original.',
                'portada_libro.jpg',
            ],
            'Discos' => [
                'DIS-EJEMPLO-001',
                'Kind of Blue (Mono 1st Press)',
                'Miles Davis',
                'Columbia Records',
                'vinilo',
                1959,
                'Jazz',
                'NEAR MINT (NM)',
                'Sucursal Central Bellas Artes',
                4200.00,
                3,
                0,
                1,
                '',
                '',
                'Vinilo LP 180g prensado analógico en perfecto estado.',
                'portada_disco.jpg',
            ],
            'Coleccionables' => [
                'COL-EJEMPLO-001',
                'Figura Evangelion EVA-01 Edición Limitada',
                'Kaiyodo',
                'Neon Genesis Evangelion',
                'figuras',
                'Resina Polystone',
                'MINT (M)',
                'Sucursal Coyoacán',
                3500.00,
                1,
                1,
                1,
                'COA-COL-2026-001',
                'ARC-COL-2026-001',
                'Figura de colección pintada a mano con base numerada.',
                'portada_coleccionable.png',
            ],
        ];

        $estiloCabecera = [
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF5B21B6'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF3B0764'],
                ],
            ],
        ];

        $estiloEjemplo = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFF5F3FF'],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];

        foreach (self::COLUMNAS_EXCEL as $nombreHoja => $columnas) {
            $hoja = $spreadsheet->createSheet();
            $hoja->setTitle($nombreHoja);

            foreach ($columnas as $idx => $tituloColumna) {
                $letra = Coordinate::stringFromColumnIndex($idx + 1);
                $hoja->setCellValue("{$letra}1", $tituloColumna);
                $hoja->getColumnDimension($letra)->setAutoSize(true);
            }

            $ultimaCol = Coordinate::stringFromColumnIndex(count($columnas));
            $hoja->getStyle("A1:{$ultimaCol}1")->applyFromArray($estiloCabecera);
            $hoja->getRowDimension(1)->setRowHeight(26);

            $filaEjemplo = $ejemplos[$nombreHoja] ?? [];
            foreach ($filaEjemplo as $idx => $valorEjemplo) {
                $letra = Coordinate::stringFromColumnIndex($idx + 1);
                $hoja->setCellValue("{$letra}2", $valorEjemplo);
            }
            $hoja->getStyle("A2:{$ultimaCol}2")->applyFromArray($estiloEjemplo);
            $hoja->getRowDimension(2)->setRowHeight(22);
            $hoja->freezePane('A2');
        }

        $spreadsheet->setActiveSheetIndex(0);

        $rutaTemp = tempnam(sys_get_temp_dir(), 'archivum_xlsx_');
        if ($rutaTemp === false) {
            throw new RuntimeException('No fue posible crear el archivo temporal para la plantilla Excel.');
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($rutaTemp);
        $contenido = file_get_contents($rutaTemp) ?: '';
        @unlink($rutaTemp);

        return $contenido;
    }

    /**
     * Genera el contenido UTF-8 (con BOM para Excel) de la plantilla CSV de respaldo.
     */
    public function generarPlantillaCsv(): string
    {
        $handle = fopen('php://temp', 'r+');
        if ($handle === false) {
            throw new RuntimeException('No fue posible inicializar el flujo temporal para generar la plantilla CSV.');
        }

        fputcsv($handle, self::COLUMNAS_PLANTILLA);
        fputcsv($handle, [
            'LIB-EJEMPLO-001',
            'Cien Años de Soledad (Edición Especial)',
            'Gabriel García Márquez',
            'Libros',
            'Sucursal Central Bellas Artes',
            '1850.00',
            '1',
            '1',
            'COA-BIB-2026-001',
            'Edición de colección en pasta dura con sobrecubierta original.',
            'portada_ejemplo.jpg',
        ]);

        rewind($handle);
        $csv = stream_get_contents($handle) ?: '';
        fclose($handle);

        return "\xEF\xBB\xBF".$csv;
    }

    /**
     * Importa productos desde un libro Excel (.xlsx con pestañas Libros, Discos, Coleccionables)
     * o desde un archivo CSV (UTF-8), junto con un paquete ZIP opcional de imágenes.
     *
     * @return array{
     *     creados: int,
     *     actualizados: int,
     *     omitidos: int,
     *     imagenes_extraidas: int,
     *     por_pestana: array<string, array{creados: int, actualizados: int, errores: int}>,
     *     errores: list<string>,
     *     detalles: list<string>
     * }
     */
    public function importar(
        UploadedFile $archivoDatos,
        ?UploadedFile $archivoZip = null,
        ?Usuario $usuario = null,
        string $ip = '127.0.0.1'
    ): array {
        $extension = Str::lower((string) ($archivoDatos->getClientOriginalExtension() ?: $archivoDatos->guessExtension()));

        if (in_array($extension, ['xlsx', 'xls'], true)) {
            return $this->importarExcel($archivoDatos, $archivoZip, $usuario, $ip);
        }

        return $this->importarCsv($archivoDatos, $archivoZip, $usuario, $ip);
    }

    /**
     * Procesa un archivo Excel (.xlsx) recorriendo sus pestañas (Libros, Discos, Coleccionables).
     *
     * @return array{
     *     creados: int,
     *     actualizados: int,
     *     omitidos: int,
     *     imagenes_extraidas: int,
     *     por_pestana: array<string, array{creados: int, actualizados: int, errores: int}>,
     *     errores: list<string>,
     *     detalles: list<string>
     * }
     */
    private function importarExcel(
        UploadedFile $archivoExcel,
        ?UploadedFile $archivoZip,
        ?Usuario $usuario,
        string $ip
    ): array {
        $rutaReal = $archivoExcel->getRealPath();
        if (! is_string($rutaReal) || ! is_readable($rutaReal)) {
            throw new RuntimeException('No fue posible leer el archivo Excel proporcionado.');
        }

        $spreadsheet = IOFactory::load($rutaReal);
        [$zip, $indiceZip] = $this->prepararIndiceZip($archivoZip);

        $catalogos = Catalogo::all();
        $sucursales = Sucursal::where('activa', true)->get();

        $estado = [
            'creados' => 0,
            'actualizados' => 0,
            'omitidos' => 0,
            'imagenes_extraidas' => 0,
            'por_pestana' => [
                'Libros' => ['creados' => 0, 'actualizados' => 0, 'errores' => 0],
                'Discos' => ['creados' => 0, 'actualizados' => 0, 'errores' => 0],
                'Coleccionables' => ['creados' => 0, 'actualizados' => 0, 'errores' => 0],
            ],
            'errores' => [],
            'detalles' => [],
            'skusEnArchivo' => [],
        ];

        foreach ($spreadsheet->getAllSheets() as $hoja) {
            $tituloHoja = trim($hoja->getTitle());
            $categoriaPorHoja = $this->resolverNombrePestana($tituloHoja);
            $nombrePestanaReporte = $categoriaPorHoja ?? $tituloHoja;

            if (! isset($estado['por_pestana'][$nombrePestanaReporte])) {
                $estado['por_pestana'][$nombrePestanaReporte] = ['creados' => 0, 'actualizados' => 0, 'errores' => 0];
            }

            $filas = $hoja->toArray(null, true, true, false);
            if (empty($filas)) {
                continue;
            }

            $encabezados = array_shift($filas);
            if (! is_array($encabezados)) {
                continue;
            }

            $mapaColumnas = [];
            foreach ($encabezados as $idx => $col) {
                $clave = Str::ascii(Str::lower(trim((string) $col)));
                if ($clave !== '') {
                    $mapaColumnas[$clave] = (int) $idx;
                }
            }

            if (empty($mapaColumnas)) {
                continue;
            }

            foreach ($filas as $idxFila => $fila) {
                $numeroFila = $idxFila + 2;
                if (! is_array($fila) || empty(array_filter($fila, fn ($v): bool => trim((string) $v) !== ''))) {
                    continue;
                }

                $this->procesarFilaNormalizada(
                    fila: $fila,
                    mapaColumnas: $mapaColumnas,
                    numeroFila: $numeroFila,
                    nombrePestana: $nombrePestanaReporte,
                    categoriaForzada: $categoriaPorHoja,
                    catalogos: $catalogos,
                    sucursales: $sucursales,
                    zip: $zip,
                    indiceZip: $indiceZip,
                    usuario: $usuario,
                    estado: $estado
                );
            }
        }

        if ($zip !== null) {
            $zip->close();
        }

        $this->registrarBitacoraImportacion($archivoExcel, $archivoZip, $usuario, $ip, $estado);

        return [
            'creados' => $estado['creados'],
            'actualizados' => $estado['actualizados'],
            'omitidos' => $estado['omitidos'],
            'imagenes_extraidas' => $estado['imagenes_extraidas'],
            'por_pestana' => $estado['por_pestana'],
            'errores' => $estado['errores'],
            'detalles' => $estado['detalles'],
        ];
    }

    /**
     * Procesa un archivo CSV (UTF-8) plano manteniendo compatibilidad.
     *
     * @return array{
     *     creados: int,
     *     actualizados: int,
     *     omitidos: int,
     *     imagenes_extraidas: int,
     *     por_pestana: array<string, array{creados: int, actualizados: int, errores: int}>,
     *     errores: list<string>,
     *     detalles: list<string>
     * }
     */
    private function importarCsv(
        UploadedFile $archivoCsv,
        ?UploadedFile $archivoZip,
        ?Usuario $usuario,
        string $ip
    ): array {
        $rutaCsv = $archivoCsv->getRealPath();
        if (! is_string($rutaCsv) || ! is_readable($rutaCsv)) {
            throw new RuntimeException('No fue posible leer el archivo CSV proporcionado.');
        }

        $handle = fopen($rutaCsv, 'r');
        if ($handle === false) {
            throw new RuntimeException('No fue posible abrir el archivo CSV.');
        }

        $primeraLinea = fgets($handle);
        if ($primeraLinea === false) {
            fclose($handle);
            throw new RuntimeException('El archivo CSV se encuentra vacío.');
        }

        $delimitador = substr_count($primeraLinea, ';') > substr_count($primeraLinea, ',') ? ';' : ',';
        rewind($handle);

        $encabezados = fgetcsv($handle, 0, $delimitador);
        if (! is_array($encabezados) || empty($encabezados)) {
            fclose($handle);
            throw new RuntimeException('El archivo CSV no contiene encabezados válidos.');
        }

        $encabezados[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $encabezados[0]);
        $mapaColumnas = [];
        foreach ($encabezados as $idx => $col) {
            $clave = Str::ascii(Str::lower(trim((string) $col)));
            if ($clave !== '') {
                $mapaColumnas[$clave] = (int) $idx;
            }
        }

        $tieneSku = array_key_exists('sku', $mapaColumnas);
        $tieneTitulo = array_key_exists('titulo', $mapaColumnas) || array_key_exists('nombre', $mapaColumnas);
        $tieneCategoria = array_key_exists('categoria', $mapaColumnas) || array_key_exists('catalogo', $mapaColumnas);
        $tieneSede = array_key_exists('sede', $mapaColumnas) || array_key_exists('sucursal', $mapaColumnas);
        $tienePrecio = array_key_exists('precio', $mapaColumnas);

        if (! $tieneSku || ! $tieneTitulo || ! $tieneCategoria || ! $tieneSede || ! $tienePrecio) {
            fclose($handle);
            throw new RuntimeException(
                'El archivo debe incluir las columnas requeridas (sku, titulo, categoria, sede, precio). Descarga la plantilla oficial.'
            );
        }

        [$zip, $indiceZip] = $this->prepararIndiceZip($archivoZip);
        $catalogos = Catalogo::all();
        $sucursales = Sucursal::where('activa', true)->get();

        $estado = [
            'creados' => 0,
            'actualizados' => 0,
            'omitidos' => 0,
            'imagenes_extraidas' => 0,
            'por_pestana' => [
                'Libros' => ['creados' => 0, 'actualizados' => 0, 'errores' => 0],
                'Discos' => ['creados' => 0, 'actualizados' => 0, 'errores' => 0],
                'Coleccionables' => ['creados' => 0, 'actualizados' => 0, 'errores' => 0],
            ],
            'errores' => [],
            'detalles' => [],
            'skusEnArchivo' => [],
        ];

        $numeroFila = 1;
        while (($fila = fgetcsv($handle, 0, $delimitador)) !== false) {
            $numeroFila++;
            if (! is_array($fila) || empty(array_filter($fila, fn ($v): bool => trim((string) $v) !== ''))) {
                continue;
            }

            $this->procesarFilaNormalizada(
                fila: $fila,
                mapaColumnas: $mapaColumnas,
                numeroFila: $numeroFila,
                nombrePestana: 'CSV',
                categoriaForzada: null,
                catalogos: $catalogos,
                sucursales: $sucursales,
                zip: $zip,
                indiceZip: $indiceZip,
                usuario: $usuario,
                estado: $estado
            );
        }

        fclose($handle);
        if ($zip !== null) {
            $zip->close();
        }

        $this->registrarBitacoraImportacion($archivoCsv, $archivoZip, $usuario, $ip, $estado);

        return [
            'creados' => $estado['creados'],
            'actualizados' => $estado['actualizados'],
            'omitidos' => $estado['omitidos'],
            'imagenes_extraidas' => $estado['imagenes_extraidas'],
            'por_pestana' => $estado['por_pestana'],
            'errores' => $estado['errores'],
            'detalles' => $estado['detalles'],
        ];
    }

    /**
     * Prepara el índice de imágenes válidas (JPG, PNG, WEBP) dentro de un archivo ZIP.
     *
     * @return array{0: ?ZipArchive, 1: array<string, array{index: int, ext: string}>}
     */
    private function prepararIndiceZip(?UploadedFile $archivoZip): array
    {
        if ($archivoZip === null) {
            return [null, []];
        }

        if (! class_exists(ZipArchive::class)) {
            throw new RuntimeException('La extensión ZipArchive de PHP no está habilitada en el servidor.');
        }

        $zip = new ZipArchive;
        if ($zip->open($archivoZip->getRealPath()) !== true) {
            throw new RuntimeException('No fue posible abrir el archivo ZIP de imágenes (archivo dañado o inválido).');
        }

        $indiceZip = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $nombreEntrada = (string) $zip->getNameIndex($i);
            if ($nombreEntrada === '' || str_ends_with($nombreEntrada, '/') || str_contains($nombreEntrada, '__MACOSX')) {
                continue;
            }

            $base = basename(str_replace('\\', '/', $nombreEntrada));
            $ext = Str::lower(pathinfo($base, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                $indiceZip[Str::lower($base)] = [
                    'index' => $i,
                    'ext' => $ext === 'jpeg' ? 'jpg' : $ext,
                ];
            }
        }

        return [$zip, $indiceZip];
    }

    /**
     * Normaliza y persiste una fila de Excel o CSV dentro de `DB::transaction`.
     *
     * @param  array<int, mixed>  $fila
     * @param  array<string, int>  $mapaColumnas
     * @param  Collection<int, Catalogo>  $catalogos
     * @param  Collection<int, Sucursal>  $sucursales
     * @param  array<string, array{index: int, ext: string}>  $indiceZip
     * @param  array<string, mixed>  $estado
     */
    private function procesarFilaNormalizada(
        array $fila,
        array $mapaColumnas,
        int $numeroFila,
        string $nombrePestana,
        ?string $categoriaForzada,
        Collection $catalogos,
        Collection $sucursales,
        ?ZipArchive $zip,
        array $indiceZip,
        ?Usuario $usuario,
        array &$estado
    ): void {
        $leerCol = static function (array $aliases) use ($fila, $mapaColumnas): string {
            foreach ($aliases as $alias) {
                if (array_key_exists($alias, $mapaColumnas)) {
                    return trim((string) ($fila[$mapaColumnas[$alias]] ?? ''));
                }
            }

            return '';
        };

        $prefijo = "[{$nombrePestana} · Fila {$numeroFila}]";
        $sku = Str::upper($leerCol(['sku']));
        $titulo = $leerCol(['titulo', 'nombre']);
        $categoriaRaw = $categoriaForzada ?: $leerCol(['categoria', 'catalogo']);
        $sedeRaw = $leerCol(['sede', 'sucursal']);
        $precioRaw = str_replace(['$', ',', ' '], '', $leerCol(['precio']));
        $existenciasRaw = $leerCol(['existencias', 'cantidad', 'stock']);
        $piezaUnicaRaw = Str::lower($leerCol(['es_pieza_unica', 'pieza_unica']));
        $edicionEspecialRaw = Str::lower($leerCol(['es_edicion_especial', 'edicion_especial']));
        $codigoCoa = $leerCol(['codigo_coa', 'coa', 'certificado']);
        $numeroSerie = Str::upper($leerCol(['numero_serie', 'serie_unica']));
        $estadoConservacion = $leerCol(['estado_conservacion', 'graduacion']) ?: 'MINT (M)';
        $descripcion = $leerCol(['descripcion']) ?: $titulo;
        $archivoImagen = $leerCol(['archivo_imagen', 'imagen']);

        $registrarError = static function (string $mensaje) use (&$estado, $nombrePestana): void {
            $estado['errores'][] = $mensaje;
            $estado['omitidos']++;
            if (isset($estado['por_pestana'][$nombrePestana])) {
                $estado['por_pestana'][$nombrePestana]['errores']++;
            }
        };

        if ($sku === '') {
            $registrarError("{$prefijo}: El campo SKU está vacío.");

            return;
        }

        if (isset($estado['skusEnArchivo'][$sku])) {
            $registrarError("{$prefijo}: El SKU «{$sku}» está duplicado dentro del archivo (ya apareció en {$estado['skusEnArchivo'][$sku]}).");

            return;
        }
        $estado['skusEnArchivo'][$sku] = "{$nombrePestana} fila {$numeroFila}";

        if ($titulo === '') {
            $registrarError("{$prefijo} (SKU {$sku}): El título del producto es obligatorio.");

            return;
        }

        if ($precioRaw === '' || ! is_numeric($precioRaw) || (float) $precioRaw <= 0) {
            $registrarError("{$prefijo} (SKU {$sku}): El precio «{$precioRaw}» debe ser un número positivo mayor a 0.");

            return;
        }
        $precio = round((float) $precioRaw, 2);

        $catalogo = $this->resolverCatalogo($catalogos, $categoriaRaw);
        if (! $catalogo) {
            $registrarError("{$prefijo} (SKU {$sku}): La categoría «{$categoriaRaw}» no existe en el sistema.");

            return;
        }

        $sucursal = $this->resolverSucursal($sucursales, $sedeRaw);
        if (! $sucursal) {
            $registrarError("{$prefijo} (SKU {$sku}): La sede «{$sedeRaw}» no existe entre las sucursales activas.");

            return;
        }

        $esPiezaUnica = in_array($piezaUnicaRaw, ['1', 'true', 'si', 'sí', 'yes', 's'], true);
        $esEdicionEspecial = $esPiezaUnica || in_array($edicionEspecialRaw, ['1', 'true', 'si', 'sí', 'yes', 's'], true);

        if ($esPiezaUnica) {
            $existencias = 1;
        } elseif ($existenciasRaw === '') {
            $existencias = 1;
        } elseif (filter_var($existenciasRaw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]) === false) {
            $registrarError("{$prefijo} (SKU {$sku}): Las existencias «{$existenciasRaw}» deben ser un número entero mayor o igual a 0.");

            return;
        } else {
            $existencias = (int) $existenciasRaw;
        }

        // Metadatos específicos según pestaña/categoría
        $metadatos = [
            'autor' => $leerCol(['autor', 'autor_artista']),
            'editorial' => $leerCol(['editorial']),
            'isbn' => $leerCol(['isbn']),
            'anio_publicacion' => $leerCol(['anio_publicacion']),
            'paginas' => $leerCol(['paginas']),
            'genero_literario' => $leerCol(['genero_literario', 'genero']),
            'artista' => $leerCol(['artista', 'autor_artista']),
            'disquera_sello' => $leerCol(['disquera_sello', 'sello_discografico', 'sello', 'disquera']),
            'formato_discografico' => $leerCol(['formato_discografico', 'formato']),
            'anio_lanzamiento' => $leerCol(['anio_lanzamiento']),
            'genero_musical' => $leerCol(['genero_musical', 'genero']),
            'fabricante_marca' => $leerCol(['fabricante_marca', 'fabricante', 'marca', 'autor_artista']),
            'serie' => $leerCol(['serie']),
            'subcategoria_coleccionable' => $leerCol(['subcategoria_coleccionable', 'subcategoria']),
            'material' => $leerCol(['material', 'material_estado']),
        ];

        $rutaImagen = null;
        if ($archivoImagen !== '') {
            $nombreBaseImg = basename(str_replace('\\', '/', $archivoImagen));
            $claveImg = Str::lower($nombreBaseImg);

            if ($zip !== null && isset($indiceZip[$claveImg])) {
                $contenidoImg = $zip->getFromIndex($indiceZip[$claveImg]['index']);
                if (is_string($contenidoImg) && $contenidoImg !== '') {
                    $nombreDestino = Str::random(40).'.'.$indiceZip[$claveImg]['ext'];
                    Storage::disk('public')->put('productos/'.$nombreDestino, $contenidoImg);
                    $rutaImagen = 'productos/'.$nombreDestino;
                    $estado['imagenes_extraidas']++;
                }
            } elseif (Storage::disk('public')->exists('productos/'.$nombreBaseImg)) {
                $rutaImagen = 'productos/'.$nombreBaseImg;
            } elseif (file_exists(public_path('images/productos/'.$nombreBaseImg))) {
                $rutaImagen = 'images/productos/'.$nombreBaseImg;
            } elseif ($zip !== null) {
                $registrarError("{$prefijo} (SKU {$sku}): La imagen «{$nombreBaseImg}» no se encontró en el ZIP (o no es JPG/PNG/WEBP).");

                return;
            }
        }

        $pestanaClave = $this->resolverNombrePestana($catalogo->nombre) ?? $nombrePestana;
        if (! isset($estado['por_pestana'][$pestanaClave])) {
            $estado['por_pestana'][$pestanaClave] = ['creados' => 0, 'actualizados' => 0, 'errores' => 0];
        }

        try {
            $accion = DB::transaction(function () use (
                $sku,
                $titulo,
                $catalogo,
                $sucursal,
                $precio,
                $existencias,
                $esPiezaUnica,
                $esEdicionEspecial,
                $codigoCoa,
                $numeroSerie,
                $estadoConservacion,
                $descripcion,
                $rutaImagen,
                $metadatos,
                $usuario
            ): string {
                $etiquetaEdicion = $esPiezaUnica
                    ? "{$estadoConservacion} · PIEZA ÚNICA 1/1 · {$titulo}"
                    : ($esEdicionEspecial ? "{$estadoConservacion} · EDICIÓN ESPECIAL · {$titulo}" : "{$estadoConservacion} · {$titulo}");

                $varianteExistente = VarianteSku::with('producto')->where('sku', $sku)->first();

                if ($varianteExistente && $varianteExistente->producto) {
                    $producto = $varianteExistente->producto;
                    $datosUpdateProd = [
                        'catalogo_id' => $catalogo->id,
                        'nombre' => $titulo,
                        'descripcion' => $descripcion,
                        'precio' => $precio,
                        'activo' => true,
                    ];
                    if ($rutaImagen !== null && Schema::hasColumn('productos', 'imagen')) {
                        $datosUpdateProd['imagen'] = $rutaImagen;
                    }
                    $producto->update($datosUpdateProd);

                    $datosUpdateVar = [
                        'descripcion' => $etiquetaEdicion,
                        'precio' => $precio,
                        'activa' => true,
                    ];
                    if ($rutaImagen !== null && Schema::hasColumn('variantes_sku', 'imagen')) {
                        $datosUpdateVar['imagen'] = $rutaImagen;
                    }
                    $varianteExistente->update($datosUpdateVar);
                    $variante = $varianteExistente;
                    $resultado = 'actualizado';
                } else {
                    $datosProd = [
                        'catalogo_id' => $catalogo->id,
                        'nombre' => $titulo,
                        'descripcion' => $descripcion,
                        'precio' => $precio,
                        'activo' => true,
                    ];
                    if ($rutaImagen !== null && Schema::hasColumn('productos', 'imagen')) {
                        $datosProd['imagen'] = $rutaImagen;
                    }
                    $producto = Producto::create($datosProd);

                    $datosVar = [
                        'producto_id' => $producto->id,
                        'sku' => $sku,
                        'descripcion' => $etiquetaEdicion,
                        'precio' => $precio,
                        'activa' => true,
                    ];
                    if ($rutaImagen !== null && Schema::hasColumn('variantes_sku', 'imagen')) {
                        $datosVar['imagen'] = $rutaImagen;
                    }
                    $variante = VarianteSku::create($datosVar);
                    $resultado = 'creado';
                }

                $this->sincronizarFichaEspecializada(
                    $producto,
                    $catalogo,
                    $metadatos,
                    $esEdicionEspecial,
                    $rutaImagen
                );

                $existencia = Existencia::firstOrCreate(
                    [
                        'variante_sku_id' => $variante->id,
                        'sucursal_id' => $sucursal->id,
                    ],
                    [
                        'cantidad' => 0,
                        'stock_minimo' => 1,
                    ]
                );

                $cantidadAnterior = (int) $existencia->cantidad;
                $existencia->update([
                    'cantidad' => $existencias,
                ]);

                if ($esPiezaUnica && Schema::hasTable('piezas_unicas')) {
                    $certificado = $codigoCoa !== ''
                        ? $codigoCoa
                        : ('COA-'.strtoupper(substr(md5($sku), 0, 8)));

                    $serieFinal = $numeroSerie !== ''
                        ? $numeroSerie
                        : ('ARC-'.$sku);

                    PiezaUnica::firstOrCreate(
                        ['existencia_id' => $existencia->id],
                        [
                            'numero_serie' => $serieFinal,
                            'certificado' => $certificado,
                            'notas' => 'Alta por importación masiva Excel/CSV.',
                            'estado' => 'disponible',
                        ]
                    );
                }

                if ($existencias !== $cantidadAnterior && Schema::hasTable('movimientos_inventario')) {
                    MovimientoInventario::create([
                        'existencia_id' => $existencia->id,
                        'usuario_id' => $usuario?->id,
                        'tipo' => $existencias >= $cantidadAnterior ? 'entrada' : 'ajuste',
                        'cantidad' => abs($existencias - $cantidadAnterior),
                        'motivo' => "Importación masiva [{$sku}]",
                        'created_at' => now(),
                    ]);
                }

                return $resultado;
            });

            if ($accion === 'creado') {
                $estado['creados']++;
                $estado['por_pestana'][$pestanaClave]['creados']++;
                $estado['detalles'][] = "{$prefijo}: SKU «{$sku}» creado ({$titulo}).";
            } else {
                $estado['actualizados']++;
                $estado['por_pestana'][$pestanaClave]['actualizados']++;
                $estado['detalles'][] = "{$prefijo}: SKU «{$sku}» actualizado ({$titulo}).";
            }
        } catch (Throwable $e) {
            $registrarError("{$prefijo} (SKU {$sku}): Error al guardar en base de datos ({$e->getMessage()}).");
        }
    }

    /**
     * Registra en bitácora el resumen de la importación masiva.
     *
     * @param  array<string, mixed>  $estado
     */
    private function registrarBitacoraImportacion(
        UploadedFile $archivoDatos,
        ?UploadedFile $archivoZip,
        ?Usuario $usuario,
        string $ip,
        array $estado
    ): void {
        if (($estado['creados'] > 0 || $estado['actualizados'] > 0) && Schema::hasTable('bitacoras')) {
            Bitacora::create([
                'usuario_id' => $usuario?->id,
                'accion' => 'IMPORTACION_MASIVA_PRODUCTOS',
                'tabla_afectada' => 'productos',
                'registro_id' => null,
                'detalle' => [
                    'archivo_datos' => $archivoDatos->getClientOriginalName(),
                    'archivo_zip' => $archivoZip?->getClientOriginalName(),
                    'creados' => $estado['creados'],
                    'actualizados' => $estado['actualizados'],
                    'omitidos' => $estado['omitidos'],
                    'imagenes_extraidas' => $estado['imagenes_extraidas'],
                    'por_pestana' => $estado['por_pestana'],
                    'usuario_nombre' => $usuario?->nombre,
                ],
                'ip' => $ip,
                'created_at' => now(),
            ]);
        }
    }

    /**
     * Determina la pestaña canónica (`Libros`, `Discos`, `Coleccionables`) según el nombre de la hoja.
     */
    private function resolverNombrePestana(string $nombreHoja): ?string
    {
        $limpio = Str::ascii(Str::lower(trim($nombreHoja)));

        if (str_contains($limpio, 'libro')) {
            return 'Libros';
        }
        if (str_contains($limpio, 'disco') || str_contains($limpio, 'vinil')) {
            return 'Discos';
        }
        if (str_contains($limpio, 'coleccion') || str_contains($limpio, 'memorabilia')) {
            return 'Coleccionables';
        }

        return null;
    }

    /**
     * Resuelve el catálogo por ID, nombre o tipo.
     *
     * @param  Collection<int, Catalogo>  $catalogos
     */
    private function resolverCatalogo(Collection $catalogos, string $valor): ?Catalogo
    {
        $limpio = Str::ascii(Str::lower(trim($valor)));
        if ($limpio === '') {
            return null;
        }

        return $catalogos->first(function (Catalogo $cat) use ($limpio, $valor): bool {
            if ((string) $cat->id === trim($valor)) {
                return true;
            }

            $nombre = Str::ascii(Str::lower((string) $cat->nombre));
            $tipo = Str::ascii(Str::lower((string) $cat->tipo));

            if ($nombre === $limpio || $tipo === $limpio) {
                return true;
            }

            if ($tipo === 'libro' && in_array($limpio, ['libro', 'libros'], true)) {
                return true;
            }
            if ($tipo === 'disco' && in_array($limpio, ['disco', 'discos', 'vinilo', 'vinilos', 'cd'], true)) {
                return true;
            }
            if ($tipo === 'coleccionable' && in_array($limpio, ['coleccionable', 'coleccionables', 'memorabilia'], true)) {
                return true;
            }

            return false;
        });
    }

    /**
     * Resuelve la sucursal activa por ID, nombre exacto o coincidencia parcial.
     *
     * @param  Collection<int, Sucursal>  $sucursales
     */
    private function resolverSucursal(Collection $sucursales, string $valor): ?Sucursal
    {
        $limpio = Str::ascii(Str::lower(trim($valor)));
        if ($limpio === '') {
            return null;
        }

        $exacta = $sucursales->first(function (Sucursal $suc) use ($limpio, $valor): bool {
            return (string) $suc->id === trim($valor)
                || Str::ascii(Str::lower((string) $suc->nombre)) === $limpio;
        });

        if ($exacta) {
            return $exacta;
        }

        return $sucursales->first(function (Sucursal $suc) use ($limpio): bool {
            return str_contains(Str::ascii(Str::lower((string) $suc->nombre)), $limpio);
        });
    }

    /**
     * Crea o actualiza la ficha especializada (`libros`, `discos` o `coleccionables`).
     *
     * @param  array<string, string>  $metadatos
     */
    private function sincronizarFichaEspecializada(
        Producto $producto,
        Catalogo $catalogo,
        array $metadatos,
        bool $esEdicionEspecial,
        ?string $rutaImagen
    ): void {
        $tipo = Str::lower((string) $catalogo->tipo);
        $nombre = Str::lower((string) $catalogo->nombre);

        if (($tipo === 'libro' || str_contains($nombre, 'libro')) && Schema::hasTable('libros')) {
            $isbn = trim((string) ($metadatos['isbn'] ?? ''));
            $datos = [
                'autor' => ($metadatos['autor'] ?? '') !== '' ? $metadatos['autor'] : 'Autor',
                'editorial' => ($metadatos['editorial'] ?? '') !== '' ? $metadatos['editorial'] : 'Edición de Catálogo',
                'genero' => ($metadatos['genero_literario'] ?? '') !== '' ? $metadatos['genero_literario'] : 'novela',
                'anio_publicacion' => is_numeric($metadatos['anio_publicacion'] ?? '') ? (int) $metadatos['anio_publicacion'] : (int) date('Y'),
                'paginas' => is_numeric($metadatos['paginas'] ?? '') ? (int) $metadatos['paginas'] : null,
                'isbn' => $isbn !== '' ? $isbn : null,
            ];
            if ($rutaImagen !== null && Schema::hasColumn('libros', 'imagen')) {
                $datos['imagen'] = $rutaImagen;
            }
            Libro::updateOrCreate(['producto_id' => $producto->id], $datos);
        } elseif (($tipo === 'disco' || $tipo === 'vinilo' || str_contains($nombre, 'disco') || str_contains($nombre, 'vinil')) && Schema::hasTable('discos')) {
            $formatoRaw = Str::lower((string) ($metadatos['formato_discografico'] ?? 'vinilo'));
            $formato = str_contains($formatoRaw, 'cd')
                ? 'CD'
                : (str_contains($formatoRaw, 'cassette') ? 'cassette' : 'vinilo');

            $datos = [
                'artista' => ($metadatos['artista'] ?? '') !== '' ? $metadatos['artista'] : $producto->nombre,
                'sello' => ($metadatos['disquera_sello'] ?? '') !== '' ? $metadatos['disquera_sello'] : 'Edición Oficial',
                'genero' => ($metadatos['genero_musical'] ?? '') !== '' ? $metadatos['genero_musical'] : 'Música',
                'formato' => $formato,
                'anio_lanzamiento' => is_numeric($metadatos['anio_lanzamiento'] ?? '') ? (int) $metadatos['anio_lanzamiento'] : (int) date('Y'),
            ];
            if ($rutaImagen !== null && Schema::hasColumn('discos', 'imagen')) {
                $datos['imagen'] = $rutaImagen;
            }
            Disco::updateOrCreate(['producto_id' => $producto->id], $datos);
        } elseif (($tipo === 'coleccionable' || str_contains($nombre, 'coleccion')) && Schema::hasTable('coleccionables')) {
            $subcat = Str::ascii(Str::lower((string) ($metadatos['subcategoria_coleccionable'] ?? 'figuras')));
            if (str_contains($subcat, 'numism') || str_contains($subcat, 'moneda') || str_contains($subcat, 'billete')) {
                $subcat = 'numismatica';
            } elseif (str_contains($subcat, 'juguet')) {
                $subcat = 'juguetes';
            } elseif (str_contains($subcat, 'histor')) {
                $subcat = 'historicos';
            } elseif (str_contains($subcat, 'video') || str_contains($subcat, 'gamer')) {
                $subcat = 'videojuegos';
            } else {
                $subcat = 'figuras';
            }

            $datos = [
                'marca' => ($metadatos['fabricante_marca'] ?? '') !== '' ? $metadatos['fabricante_marca'] : 'Colección Oficial',
                'serie' => ($metadatos['serie'] ?? '') !== '' ? $metadatos['serie'] : 'Edición Especial',
                'categoria' => $subcat,
                'material' => ($metadatos['material'] ?? '') !== '' ? $metadatos['material'] : 'Original',
                'edicion_limitada' => $esEdicionEspecial,
            ];
            if ($rutaImagen !== null && Schema::hasColumn('coleccionables', 'imagen')) {
                $datos['imagen'] = $rutaImagen;
            }
            Coleccionable::updateOrCreate(['producto_id' => $producto->id], $datos);
        }
    }
}
