<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Población inicial de la base de datos de ARCHIVUM.
     */
    public function run(): void
    {
        $this->call([
            UsuarioSeeder::class,
            CatalogoProductoSeeder::class,
        ]);
    }
}
