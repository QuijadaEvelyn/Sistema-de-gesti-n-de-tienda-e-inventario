# REGLA CRÍTICA DE PRODUCCIÓN — ARCHIVUM

La base de datos MariaDB actual (`archivum_db`, volumen `db_data`) contiene **datos reales de producción y presentación**.

1. **PROHIBIDO ejecutar comandos destructivos de Artisan:**
   - Nunca ejecutes `php artisan migrate:fresh`, `php artisan migrate:refresh`, `php artisan migrate:reset`, `php artisan db:seed` ni `php artisan db:wipe`.
2. **PROHIBIDO modificar o borrar registros reales:**
   - Nunca elimines ni sobrescribas registros existentes en las tablas `usuarios`, `roles` ni `productos`.
3. **PROHIBIDO borrar almacenamiento de imágenes:**
   - Nunca elimines ni limpies archivos existentes dentro de `storage/app/public/`.
4. **Aislamiento de pruebas:**
   - Toda prueba con `RefreshDatabase` debe ejecutarse estrictamente en SQLite en memoria (`phpunit.xml`), jamás contra MariaDB.
