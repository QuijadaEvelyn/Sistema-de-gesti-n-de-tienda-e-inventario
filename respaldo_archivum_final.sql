/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.2.6-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: archivum_db
-- ------------------------------------------------------
-- Server version	11.2.6-MariaDB-ubu2204

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `bitacoras`
--

DROP TABLE IF EXISTS `bitacoras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bitacoras` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` bigint(20) unsigned DEFAULT NULL,
  `accion` varchar(80) NOT NULL,
  `tabla_afectada` varchar(80) DEFAULT NULL,
  `registro_id` bigint(20) unsigned DEFAULT NULL,
  `detalle` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`detalle`)),
  `ip` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `bitacoras_usuario_id_foreign` (`usuario_id`),
  KEY `bitacoras_tabla_afectada_registro_id_index` (`tabla_afectada`,`registro_id`),
  CONSTRAINT `bitacoras_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bitacoras`
--

LOCK TABLES `bitacoras` WRITE;
/*!40000 ALTER TABLE `bitacoras` DISABLE KEYS */;
INSERT INTO `bitacoras` VALUES
(1,11,'LOGIN_EXITOSO','usuarios',11,'{\"email\":\"admin@archivum.com\",\"roles\":[],\"tab_session_id\":\"tab_mwsl2ezk_6pnqpwxdqq8m75leb39k\"}','172.19.0.1','2026-11-23 05:39:30'),
(2,11,'IMPORTACION_MASIVA_PRODUCTOS','productos',NULL,'{\"archivo_datos\":\"plantilla_productos_archivum.xlsx\",\"archivo_zip\":\"imagenes.zip\",\"creados\":10,\"actualizados\":0,\"omitidos\":8,\"imagenes_extraidas\":10,\"por_pestana\":{\"Libros\":{\"creados\":3,\"actualizados\":0,\"errores\":4},\"Discos\":{\"creados\":5,\"actualizados\":0,\"errores\":2},\"Coleccionables\":{\"creados\":2,\"actualizados\":0,\"errores\":2}},\"usuario_nombre\":\"Administrador ARCHIVUM\"}','172.19.0.1','2026-11-23 06:01:10'),
(3,11,'IMPORTACION_MASIVA_PRODUCTOS','productos',NULL,'{\"archivo_datos\":\"plantilla_productos_archivum.xlsx\",\"archivo_zip\":\"imagenes.zip\",\"creados\":0,\"actualizados\":10,\"omitidos\":5,\"imagenes_extraidas\":10,\"por_pestana\":{\"Libros\":{\"creados\":0,\"actualizados\":3,\"errores\":3},\"Discos\":{\"creados\":0,\"actualizados\":5,\"errores\":1},\"Coleccionables\":{\"creados\":0,\"actualizados\":2,\"errores\":1}},\"usuario_nombre\":\"Administrador ARCHIVUM\"}','172.19.0.1','2026-11-23 06:06:41'),
(4,11,'IMPORTACION_MASIVA_PRODUCTOS','productos',NULL,'{\"archivo_datos\":\"plantilla_productos_archivum.xlsx\",\"archivo_zip\":\"imagenes.zip\",\"creados\":4,\"actualizados\":9,\"omitidos\":2,\"imagenes_extraidas\":15,\"por_pestana\":{\"Libros\":{\"creados\":3,\"actualizados\":3,\"errores\":0},\"Discos\":{\"creados\":1,\"actualizados\":5,\"errores\":0},\"Coleccionables\":{\"creados\":0,\"actualizados\":1,\"errores\":2}},\"usuario_nombre\":\"Administrador ARCHIVUM\"}','172.19.0.1','2026-11-23 06:09:54'),
(5,11,'IMPORTACION_MASIVA_PRODUCTOS','productos',NULL,'{\"archivo_datos\":\"plantilla_productos_archivum.xlsx\",\"archivo_zip\":\"imagenes.zip\",\"creados\":0,\"actualizados\":13,\"omitidos\":2,\"imagenes_extraidas\":15,\"por_pestana\":{\"Libros\":{\"creados\":0,\"actualizados\":6,\"errores\":0},\"Discos\":{\"creados\":0,\"actualizados\":6,\"errores\":0},\"Coleccionables\":{\"creados\":0,\"actualizados\":1,\"errores\":2}},\"usuario_nombre\":\"Administrador ARCHIVUM\"}','172.19.0.1','2026-11-23 06:11:39'),
(6,11,'IMPORTACION_MASIVA_PRODUCTOS','productos',NULL,'{\"archivo_datos\":\"plantilla_productos_archivum.xlsx\",\"archivo_zip\":\"imagenes.zip\",\"creados\":0,\"actualizados\":13,\"omitidos\":2,\"imagenes_extraidas\":15,\"por_pestana\":{\"Libros\":{\"creados\":0,\"actualizados\":6,\"errores\":0},\"Discos\":{\"creados\":0,\"actualizados\":6,\"errores\":0},\"Coleccionables\":{\"creados\":0,\"actualizados\":1,\"errores\":2}},\"usuario_nombre\":\"Administrador ARCHIVUM\"}','172.19.0.1','2026-11-23 06:15:18'),
(7,11,'IMPORTACION_MASIVA_PRODUCTOS','productos',NULL,'{\"archivo_datos\":\"plantilla_productos_archivum.xlsx\",\"archivo_zip\":\"imagenes.zip\",\"creados\":0,\"actualizados\":13,\"omitidos\":2,\"imagenes_extraidas\":15,\"por_pestana\":{\"Libros\":{\"creados\":0,\"actualizados\":6,\"errores\":0},\"Discos\":{\"creados\":0,\"actualizados\":6,\"errores\":0},\"Coleccionables\":{\"creados\":0,\"actualizados\":1,\"errores\":2}},\"usuario_nombre\":\"Administrador ARCHIVUM\"}','172.19.0.1','2026-11-23 06:15:47'),
(8,11,'IMPORTACION_MASIVA_PRODUCTOS','productos',NULL,'{\"archivo_datos\":\"plantilla_productos_archivum.xlsx\",\"archivo_zip\":\"imagenes.zip\",\"creados\":0,\"actualizados\":14,\"omitidos\":1,\"imagenes_extraidas\":15,\"por_pestana\":{\"Libros\":{\"creados\":0,\"actualizados\":6,\"errores\":0},\"Discos\":{\"creados\":0,\"actualizados\":6,\"errores\":0},\"Coleccionables\":{\"creados\":0,\"actualizados\":2,\"errores\":1}},\"usuario_nombre\":\"Administrador ARCHIVUM\"}','172.19.0.1','2026-11-23 06:16:45'),
(9,11,'IMPORTACION_MASIVA_PRODUCTOS','productos',NULL,'{\"archivo_datos\":\"plantilla_productos_archivum.xlsx\",\"archivo_zip\":\"imagenes.zip\",\"creados\":0,\"actualizados\":14,\"omitidos\":1,\"imagenes_extraidas\":15,\"por_pestana\":{\"Libros\":{\"creados\":0,\"actualizados\":6,\"errores\":0},\"Discos\":{\"creados\":0,\"actualizados\":6,\"errores\":0},\"Coleccionables\":{\"creados\":0,\"actualizados\":2,\"errores\":1}},\"usuario_nombre\":\"Administrador ARCHIVUM\"}','172.19.0.1','2026-11-23 06:16:45'),
(10,11,'IMPORTACION_MASIVA_PRODUCTOS','productos',NULL,'{\"archivo_datos\":\"plantilla_productos_archivum.xlsx\",\"archivo_zip\":\"imagenes.zip\",\"creados\":0,\"actualizados\":14,\"omitidos\":1,\"imagenes_extraidas\":15,\"por_pestana\":{\"Libros\":{\"creados\":0,\"actualizados\":6,\"errores\":0},\"Discos\":{\"creados\":0,\"actualizados\":6,\"errores\":0},\"Coleccionables\":{\"creados\":0,\"actualizados\":2,\"errores\":1}},\"usuario_nombre\":\"Administrador ARCHIVUM\"}','172.19.0.1','2026-11-23 06:17:57'),
(11,11,'IMPORTACION_MASIVA_PRODUCTOS','productos',NULL,'{\"archivo_datos\":\"plantilla_productos_archivum.xlsx\",\"archivo_zip\":\"imagenes.zip\",\"creados\":1,\"actualizados\":14,\"omitidos\":0,\"imagenes_extraidas\":15,\"por_pestana\":{\"Libros\":{\"creados\":0,\"actualizados\":6,\"errores\":0},\"Discos\":{\"creados\":0,\"actualizados\":6,\"errores\":0},\"Coleccionables\":{\"creados\":1,\"actualizados\":2,\"errores\":0}},\"usuario_nombre\":\"Administrador ARCHIVUM\"}','172.19.0.1','2026-11-23 06:18:55'),
(12,NULL,'LOGIN_FALLIDO','usuarios',NULL,'{\"email\":\"quijadaevelin82@gmail.com\",\"ip\":\"172.19.0.1\"}','172.19.0.1','2026-11-23 06:20:12'),
(13,13,'CLIENTE_REGISTRADO','usuarios',13,'{\"email\":\"quijadaevelin82@gmail.com\",\"cliente_id\":5,\"rol\":\"cliente\"}','172.19.0.1','2026-11-23 06:20:32'),
(14,11,'IMPORTACION_MASIVA_PRODUCTOS','productos',NULL,'{\"archivo_datos\":\"plantilla_productos_archivum.xlsx\",\"archivo_zip\":\"imagenes.zip\",\"creados\":0,\"actualizados\":15,\"omitidos\":0,\"imagenes_extraidas\":15,\"por_pestana\":{\"Libros\":{\"creados\":0,\"actualizados\":6,\"errores\":0},\"Discos\":{\"creados\":0,\"actualizados\":6,\"errores\":0},\"Coleccionables\":{\"creados\":0,\"actualizados\":3,\"errores\":0}},\"usuario_nombre\":\"Administrador ARCHIVUM\"}','172.19.0.1','2026-11-23 06:39:35');
/*!40000 ALTER TABLE `bitacoras` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES
('laravel-cache-archivum_tab_token:tab_mwsl2ezk_6pnqpwxdqq8m75leb39k','a:7:{s:7:\"user_id\";i:11;s:16:\"carrito_archivum\";a:0:{}s:26:\"carrito_precios_congelados\";a:0:{}s:20:\"carrito_reservas_ids\";a:0:{}s:17:\"carrito_expira_en\";N;s:16:\"authenticated_at\";s:25:\"2026-11-23T05:39:30+00:00\";s:10:\"updated_at\";s:25:\"2026-11-23T06:39:35+00:00\";}',1795459175),
('laravel-cache-archivum_tab_token:tab_mwsum1yn_dvcjeo0x7dwqq7oj6ky0','a:7:{s:7:\"user_id\";i:13;s:16:\"carrito_archivum\";a:0:{}s:26:\"carrito_precios_congelados\";a:0:{}s:20:\"carrito_reservas_ids\";a:0:{}s:17:\"carrito_expira_en\";N;s:16:\"authenticated_at\";s:25:\"2026-11-23T06:20:33+00:00\";s:10:\"updated_at\";s:25:\"2026-11-23T06:40:06+00:00\";}',1795459206),
('laravel-cache-quijadaevelin82@gmail.com|172.19.0.1','i:1;',1795414872),
('laravel-cache-quijadaevelin82@gmail.com|172.19.0.1:timer','i:1795414872;',1795414872);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carrito_items`
--

DROP TABLE IF EXISTS `carrito_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `carrito_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `carrito_id` bigint(20) unsigned NOT NULL,
  `variante_sku_id` bigint(20) unsigned NOT NULL,
  `cantidad` int(10) unsigned NOT NULL DEFAULT 1,
  `precio_unitario_congelado` decimal(12,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `carrito_items_carrito_id_variante_sku_id_unique` (`carrito_id`,`variante_sku_id`),
  KEY `carrito_items_variante_sku_id_foreign` (`variante_sku_id`),
  CONSTRAINT `carrito_items_carrito_id_foreign` FOREIGN KEY (`carrito_id`) REFERENCES `carritos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `carrito_items_variante_sku_id_foreign` FOREIGN KEY (`variante_sku_id`) REFERENCES `variantes_sku` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carrito_items`
--

LOCK TABLES `carrito_items` WRITE;
/*!40000 ALTER TABLE `carrito_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `carrito_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carritos`
--

DROP TABLE IF EXISTS `carritos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `carritos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cliente_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `carritos_cliente_id_unique` (`cliente_id`),
  CONSTRAINT `carritos_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carritos`
--

LOCK TABLES `carritos` WRITE;
/*!40000 ALTER TABLE `carritos` DISABLE KEYS */;
INSERT INTO `carritos` VALUES
(5,5,'2026-11-23 06:20:32','2026-11-23 06:20:32');
/*!40000 ALTER TABLE `carritos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `catalogos`
--

DROP TABLE IF EXISTS `catalogos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `catalogos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(120) NOT NULL,
  `tipo` enum('libro','disco','coleccionable') NOT NULL,
  `descripcion` text DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `catalogos_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `catalogos`
--

LOCK TABLES `catalogos` WRITE;
/*!40000 ALTER TABLE `catalogos` DISABLE KEYS */;
INSERT INTO `catalogos` VALUES
(4,'Libros','libro','Primeras ediciones hispanoamericanas, ejemplares autografiados, narrativa, ensayo, poesía e historia con peritaje bibliográfico.',1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(5,'Discos','disco','Prensados analógicos en vinilo de 180g y ediciones maestras en CD clasificadas con alta fidelidad.',1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(6,'Coleccionables','coleccionable','Figuras de colección, numismática, juguetes manufacturados, objetos históricos y memorabilia de videojuegos.',1,'2026-11-22 04:15:56','2026-11-22 04:15:56');
/*!40000 ALTER TABLE `catalogos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cliente_promocion`
--

DROP TABLE IF EXISTS `cliente_promocion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cliente_promocion` (
  `cliente_frecuente_id` bigint(20) unsigned NOT NULL,
  `promocion_id` bigint(20) unsigned NOT NULL,
  `recibida_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `usada_en` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`cliente_frecuente_id`,`promocion_id`),
  KEY `cliente_promocion_promocion_id_foreign` (`promocion_id`),
  CONSTRAINT `cliente_promocion_cliente_frecuente_id_foreign` FOREIGN KEY (`cliente_frecuente_id`) REFERENCES `clientes_frecuentes` (`cliente_id`) ON DELETE CASCADE,
  CONSTRAINT `cliente_promocion_promocion_id_foreign` FOREIGN KEY (`promocion_id`) REFERENCES `promociones` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cliente_promocion`
--

LOCK TABLES `cliente_promocion` WRITE;
/*!40000 ALTER TABLE `cliente_promocion` DISABLE KEYS */;
/*!40000 ALTER TABLE `cliente_promocion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clientes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` bigint(20) unsigned DEFAULT NULL,
  `nombre` varchar(120) NOT NULL,
  `apellidos` varchar(120) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clientes_email_unique` (`email`),
  UNIQUE KEY `clientes_usuario_id_unique` (`usuario_id`),
  CONSTRAINT `clientes_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` VALUES
(5,13,'Evelyn','Alvarez','quijadaevelin82@gmail.com',NULL,'2026-11-23 06:20:32','2026-11-23 06:20:32',NULL);
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clientes_frecuentes`
--

DROP TABLE IF EXISTS `clientes_frecuentes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clientes_frecuentes` (
  `cliente_id` bigint(20) unsigned NOT NULL,
  `desde` date NOT NULL,
  `puntos` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`cliente_id`),
  CONSTRAINT `clientes_frecuentes_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes_frecuentes`
--

LOCK TABLES `clientes_frecuentes` WRITE;
/*!40000 ALTER TABLE `clientes_frecuentes` DISABLE KEYS */;
/*!40000 ALTER TABLE `clientes_frecuentes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coleccionables`
--

DROP TABLE IF EXISTS `coleccionables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coleccionables` (
  `producto_id` bigint(20) unsigned NOT NULL,
  `marca` varchar(255) DEFAULT NULL,
  `serie` varchar(255) DEFAULT NULL,
  `categoria` varchar(60) DEFAULT NULL,
  `material` varchar(80) DEFAULT NULL,
  `edicion_limitada` tinyint(1) NOT NULL DEFAULT 0,
  `imagen` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`producto_id`),
  CONSTRAINT `coleccionables_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coleccionables`
--

LOCK TABLES `coleccionables` WRITE;
/*!40000 ALTER TABLE `coleccionables` DISABLE KEYS */;
INSERT INTO `coleccionables` VALUES
(111,'Colección Diplomática','Archivo Histórico Bellas Artes','historicos','Papel Algodón & Tinta Ferrogálica',1,NULL),
(112,'Archivo Coyoacán','Correspondencia Muralista S. XX','historicos','Tinta Sepia sobre Papel Ingres',1,NULL),
(113,'Casa de Moneda de México','Numismática del Centenario','numismatica','Plata Ley .925 en Estuche de Época',1,NULL),
(114,'Archivo Fotográfico Mexicano','Vanguardia Documental S. XX','historicos','Plata sobre Gelatina en Papel Fibra',1,NULL),
(115,'Fondo Conservatorio Nacional','Manuscritos Musicales del Porfiriato','historicos','Papel Pautado de Lino',1,NULL),
(116,'Imprenta Mundial / INBA','Efemérides Culturales de México','historicos','Papel Couché & Huecograbado',0,NULL),
(117,'Taller de Gráfica Popular','Gráfica Mexicana de Medio Siglo','historicos','Linóleo sobre Papel Fabriano',0,NULL),
(118,'Archivum Conservation Lab','Insumos de Preservación Documental','juguetes','Cartón Libre de Ácido pH Neutro',0,NULL),
(119,'Kaiyodo / Gainax Japón','Neon Genesis Evangelion Masterpiece','figuras','Resina Polystone Pintada a Mano',1,NULL),
(120,'United States Mint','Gold Bullion Series','numismatica','Oro Puro 900',1,'images/coleccionables/Double-Eagle-de-1933.jpg'),
(121,'United States Mint','Early US Silver Dollars','numismatica','Plata Ley 892',1,'images/coleccionables/Dolar-Flowing-Hair-de-1794.jpg'),
(122,'Royal Mint UK','Medieval Gold Series','numismatica','Oro Medieval',1,'images/coleccionables/Florin-de-Eduardo-III-de-1344.jpg'),
(123,'Ceca Omeya','Ancient Islamic Coinage','numismatica','Oro Nativo',1,'images/coleccionables/Dinar-de-Oro-Omeya-del-año-723.jpg'),
(124,'Ceca de México (Mo)','Spanish Colonial Currency','numismatica','Plata Colonial',0,'images/coleccionables/Monedas-Reales.jpg'),
(125,'Casa de Moneda de México','Monedas Bicentenario de Oro','numismatica','Oro Puro 0.900',0,'images/coleccionables/Centenario-de-Oro.jpg'),
(126,'Casa de Moneda de México','Serie Libertad','numismatica','Plata Pura Ley .999',0,'images/coleccionables/Moneda-Libertad.jpg'),
(127,'Banco de México','Familia C1 Conmemorativa','numismatica','Aleación Bimetálica',0,'images/coleccionables/Monedas-Conmemorativas.jpg'),
(128,'Banco de México / ABNC','Billetes Retirados','numismatica','Papel Seguridad Antiguo',0,'images/coleccionables/Familia-AAA.jpg'),
(129,'Pop Mart','The Monsters Series','juguetes','Vinilo & Peluche Sintético',1,'images/coleccionables/Pop-Mart.jpg'),
(130,'Funko','Pop! Animation & Movies','juguetes','Plástico Vinilo',0,'images/coleccionables/Funko-Pop.jpg'),
(131,'LEGO Systems A/S','Collector Series / Harry Potter','juguetes','Plástico ABS de Alta Resistencia',1,'images/coleccionables/lego.jpg'),
(132,'Artesanías Temáticas','Egyptian Mythology Collection','figuras','Resina Poliéster Pintada a Mano',0,'images/coleccionables/Estatua-Anubis.jpg'),
(133,'Snowflake Flower (Cochiti Pueblo)','Native American Storyteller Series','figuras','Arcilla y Pigmentos Naturales',1,'images/coleccionables/Figura-ceramica.jpg'),
(134,'DecoArte Colección','Egyptian Artifacts','figuras','Resina Compuesta',0,'images/coleccionables/Exquisito-Joyero.jpg'),
(135,'Banpresto / Bird Studio','Dragon Ball Z Vintage Series','figuras','PVC Grado Colección & Acrílico UV',1,NULL),
(136,'Yonezawa Toys Japón','Space Age Tin Toy Series','juguetes','Hojalata Litografiada & Resorte de Acero',1,NULL),
(137,'Gobierno Constitucionalista de México','Papel Moneda de la Revolución Mexicana','numismatica','Papel Moneda de Seguridad',1,NULL),
(138,'Real Casa de Moneda de México','Numismática Virreinal Novohispana','numismatica','Plata Ley .903',1,NULL),
(139,'Nintendo Co., Ltd. Japón','Silver Series Game & Watch (AC-01)','videojuegos','Plástico ABS & Pantalla LCD Reflectiva',1,NULL),
(140,'Nintendo of America','NES Classic Gold Series (NES-ZL-USA)','videojuegos','Carcasa Dorada Metalizada & Placa PCB',1,NULL),
(149,'Fender','Eléctrico','figuras','Madera de aliso, arce y componentes metálicos',1,'productos/plmvqDqWdaonmP4pvCcQKSMShw7H2C9J9gwucizs.jpg'),
(150,'Sideshow Collectibles','Juguete','figuras','Resina Polystone de Alta Densidad (Pintada a mano)',1,'productos/hTG48Fhmq6xPvzcb7CAI0W16xTvh1tru3VRmVniT.jpg'),
(162,'Topps Chewing Gum, Inc.','Topps Baseball 1952','figuras','Cartulina de fibra gruesa con impresión litográfica a color.',1,'productos/9x8MOv2UAXphugNEuTxIaW70fAAsbMqhIeQUAvsr.jpg');
/*!40000 ALTER TABLE `coleccionables` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detalle_envios`
--

DROP TABLE IF EXISTS `detalle_envios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detalle_envios` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `envio_id` bigint(20) unsigned NOT NULL,
  `detalle_pedido_id` bigint(20) unsigned NOT NULL,
  `cantidad` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `detalle_envios_envio_id_detalle_pedido_id_unique` (`envio_id`,`detalle_pedido_id`),
  KEY `detalle_envios_detalle_pedido_id_foreign` (`detalle_pedido_id`),
  CONSTRAINT `detalle_envios_detalle_pedido_id_foreign` FOREIGN KEY (`detalle_pedido_id`) REFERENCES `detalle_pedidos` (`id`),
  CONSTRAINT `detalle_envios_envio_id_foreign` FOREIGN KEY (`envio_id`) REFERENCES `envios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_envios`
--

LOCK TABLES `detalle_envios` WRITE;
/*!40000 ALTER TABLE `detalle_envios` DISABLE KEYS */;
/*!40000 ALTER TABLE `detalle_envios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detalle_pagos`
--

DROP TABLE IF EXISTS `detalle_pagos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detalle_pagos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pago_id` bigint(20) unsigned NOT NULL,
  `referencia_externa` varchar(255) DEFAULT NULL,
  `ultimos_digitos` varchar(4) DEFAULT NULL,
  `autorizacion` varchar(60) DEFAULT NULL,
  `respuesta_pasarela` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`respuesta_pasarela`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `detalle_pagos_pago_id_foreign` (`pago_id`),
  CONSTRAINT `detalle_pagos_pago_id_foreign` FOREIGN KEY (`pago_id`) REFERENCES `pagos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_pagos`
--

LOCK TABLES `detalle_pagos` WRITE;
/*!40000 ALTER TABLE `detalle_pagos` DISABLE KEYS */;
/*!40000 ALTER TABLE `detalle_pagos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detalle_pedidos`
--

DROP TABLE IF EXISTS `detalle_pedidos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detalle_pedidos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pedido_id` bigint(20) unsigned NOT NULL,
  `variante_sku_id` bigint(20) unsigned NOT NULL,
  `cantidad` int(10) unsigned NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `detalle_pedidos_pedido_id_variante_sku_id_unique` (`pedido_id`,`variante_sku_id`),
  KEY `detalle_pedidos_variante_sku_id_foreign` (`variante_sku_id`),
  CONSTRAINT `detalle_pedidos_pedido_id_foreign` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `detalle_pedidos_variante_sku_id_foreign` FOREIGN KEY (`variante_sku_id`) REFERENCES `variantes_sku` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_pedidos`
--

LOCK TABLES `detalle_pedidos` WRITE;
/*!40000 ALTER TABLE `detalle_pedidos` DISABLE KEYS */;
/*!40000 ALTER TABLE `detalle_pedidos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `devoluciones`
--

DROP TABLE IF EXISTS `devoluciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `devoluciones` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `folio_devolucion` varchar(40) DEFAULT NULL,
  `pedido_id` bigint(20) unsigned NOT NULL,
  `cliente_id` bigint(20) unsigned NOT NULL,
  `detalle_pedido_id` bigint(20) unsigned NOT NULL,
  `cantidad` int(10) unsigned NOT NULL,
  `motivo` varchar(255) NOT NULL,
  `comentarios_cliente` text DEFAULT NULL,
  `evidencias` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`evidencias`)),
  `estatus` enum('solicitada','aprobada','rechazada','completada') NOT NULL DEFAULT 'solicitada',
  `etapa_devolucion` varchar(40) NOT NULL DEFAULT 'solicitada',
  `monto_reembolso` decimal(12,2) DEFAULT NULL,
  `dictamen_admin` text DEFAULT NULL,
  `guia_retorno` varchar(50) DEFAULT NULL,
  `instrucciones_retorno` text DEFAULT NULL,
  `destino_inventario` varchar(40) DEFAULT NULL,
  `sucursal_destino_id` bigint(20) unsigned DEFAULT NULL,
  `nueva_graduacion` varchar(120) DEFAULT NULL,
  `stock_procesado` tinyint(1) NOT NULL DEFAULT 0,
  `revisado_por` bigint(20) unsigned DEFAULT NULL,
  `aprobada_en` timestamp NULL DEFAULT NULL,
  `recibida_en` timestamp NULL DEFAULT NULL,
  `reembolsada_en` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `devoluciones_folio_devolucion_unique` (`folio_devolucion`),
  KEY `devoluciones_pedido_id_foreign` (`pedido_id`),
  KEY `devoluciones_cliente_id_foreign` (`cliente_id`),
  KEY `devoluciones_detalle_pedido_id_foreign` (`detalle_pedido_id`),
  KEY `devoluciones_sucursal_destino_id_foreign` (`sucursal_destino_id`),
  KEY `devoluciones_revisado_por_foreign` (`revisado_por`),
  KEY `devoluciones_etapa_devolucion_index` (`etapa_devolucion`),
  CONSTRAINT `devoluciones_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `devoluciones_detalle_pedido_id_foreign` FOREIGN KEY (`detalle_pedido_id`) REFERENCES `detalle_pedidos` (`id`),
  CONSTRAINT `devoluciones_pedido_id_foreign` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`),
  CONSTRAINT `devoluciones_revisado_por_foreign` FOREIGN KEY (`revisado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  CONSTRAINT `devoluciones_sucursal_destino_id_foreign` FOREIGN KEY (`sucursal_destino_id`) REFERENCES `sucursales` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `devoluciones`
--

LOCK TABLES `devoluciones` WRITE;
/*!40000 ALTER TABLE `devoluciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `devoluciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `direcciones`
--

DROP TABLE IF EXISTS `direcciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `direcciones` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cliente_id` bigint(20) unsigned NOT NULL,
  `alias` varchar(50) DEFAULT NULL,
  `calle` varchar(255) NOT NULL,
  `numero_exterior` varchar(20) NOT NULL,
  `numero_interior` varchar(20) DEFAULT NULL,
  `colonia` varchar(120) NOT NULL,
  `municipio` varchar(120) NOT NULL,
  `estado` varchar(80) NOT NULL,
  `codigo_postal` varchar(10) NOT NULL,
  `pais` varchar(60) NOT NULL DEFAULT 'México',
  `referencias` varchar(255) DEFAULT NULL,
  `predeterminada` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `direcciones_cliente_id_foreign` (`cliente_id`),
  CONSTRAINT `direcciones_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `direcciones`
--

LOCK TABLES `direcciones` WRITE;
/*!40000 ALTER TABLE `direcciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `direcciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `discos`
--

DROP TABLE IF EXISTS `discos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `discos` (
  `producto_id` bigint(20) unsigned NOT NULL,
  `artista` varchar(255) NOT NULL,
  `sello` varchar(255) DEFAULT NULL,
  `genero` varchar(80) DEFAULT NULL,
  `formato` enum('CD','vinilo','cassette','digital') NOT NULL DEFAULT 'CD',
  `anio_lanzamiento` smallint(5) unsigned DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`producto_id`),
  CONSTRAINT `discos_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `discos`
--

LOCK TABLES `discos` WRITE;
/*!40000 ALTER TABLE `discos` DISABLE KEYS */;
INSERT INTO `discos` VALUES
(92,'Miles Davis','Columbia Records CL 1355','Modal Jazz','vinilo',1959,NULL),
(93,'John Coltrane','Blue Note BLP 1577','Hard Bop Jazz','vinilo',1957,NULL),
(94,'John Coltrane Quartet','Impulse! A-77','Spiritual Jazz','vinilo',1965,NULL),
(95,'The Beatles','Apple Records PCS 7088','Rock Clásico','vinilo',1969,NULL),
(96,'Pink Floyd','Harvest / Toshiba-EMI CP35-3017','Rock Progresivo','CD',1983,NULL),
(97,'Led Zeppelin','Atlantic Records SD 7208','Hard Rock','vinilo',1971,NULL),
(98,'Radiohead','Parlophone / EMI 7243 8 55229 2 4','Art Rock & Alternativo','CD',1997,NULL),
(99,'Las Cabras Montañesas','Independiente','Rock / Indie','vinilo',2017,'images/coleccionables/las-cabras-montañeras.jpg'),
(100,'Los Yaki','Latin Gold Records','Rock and Roll / Go-Go','vinilo',1970,'images/coleccionables/Los-Yaki.jpg'),
(101,'Caifanes','RCA Victor (102-02124)','Rock Pop Latino','vinilo',1990,'images/coleccionables/Caifanes.jpg'),
(102,'Mägo de Oz','Locomotive Music','Folk Metal','vinilo',2007,'images/coleccionables/Mago-de-oz-La-ciudad.jpg'),
(103,'Mägo de Oz','Warner Music Spain','Folk Metal','vinilo',2024,'images/coleccionables/Mago-de-oz-Alicia.jpg'),
(104,'Varios Artistas','Sony Music / BMG','Balada Romántica','CD',2005,'images/coleccionables/Los-Grandes-Romanticos.jpg'),
(105,'La Arrolladora Banda El Limón','Disa / Universal','Banda / Regional Mexicano','CD',2012,'images/coleccionables/Cd-la-arrolladora.jpg'),
(106,'Juan Gabriel & Rocío Dúrcal','BMG Ariola','Ranchera / Balada','CD',1997,'images/coleccionables/Juan-Gabriel.jpg'),
(107,'La Sonora Santanera','Sony Music','Tropical / Danzón / Bolero','CD',2010,'images/coleccionables/3-cds-La-Sonora.jpg'),
(108,'Varios Artistas','Discos Peerless','Folklor Mexicano / Huapango','CD',2000,'images/coleccionables/Huapangos.jpg'),
(109,'Taylor Swift','Republic Records / Universal','Pop / Synth-Pop','vinilo',2022,'images/coleccionables/TAYLO.jpg'),
(110,'Buena Vista Social Club','World Circuit WCD050','Son Cubano & Trova Tradicional','CD',1997,NULL),
(144,'Bad Bunny','Rimas Entertainment','Reguetón','vinilo',2025,'productos/QH2qN8ZD2xujYUoEMlaCUNYkToV8XSVQpdDGbSyW.jpg'),
(145,'Rosalía','Columbia Records','Flamenco','vinilo',2025,'productos/VP4Gn46dpjSrleB1gFaNglEGu93GkyI9HDA1pmnO.jpg'),
(146,'Bomba Estéreo','Sony Music Latin','Pop electrónico','vinilo',2022,'productos/WqX1d2lcnJs5age66LvrEyQm3D7eoz1xrqux1RrT.jpg'),
(147,'Pink Floyd','Pink Floyd Records','Rock progresivo','vinilo',1973,'productos/zKFBnBudGTxAYJGNOcV5r5jBFQQG3KFmo33OThBh.jpg'),
(148,'Whitney Houston','Arista Records','Banda sonora','vinilo',1992,'productos/Q6L3WnKI7ANA97CbCeSskGNFQtJZZAXd1duc230A.jpg'),
(154,'ACDC','Columbia','Hard Rock','vinilo',1980,'productos/59ku0yscQyCH9x4N0Vdn8q1gpaf5imKSHz2g38Wz.jpg');
/*!40000 ALTER TABLE `discos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `envios`
--

DROP TABLE IF EXISTS `envios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `envios` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pedido_id` bigint(20) unsigned NOT NULL,
  `sucursal_id` bigint(20) unsigned DEFAULT NULL,
  `paqueteria` varchar(80) DEFAULT NULL,
  `numero_guia` varchar(80) DEFAULT NULL,
  `estado_actual` enum('pendiente','en_preparacion','en_transito','en_camino','entregado','incidencia') NOT NULL DEFAULT 'pendiente',
  `fecha_estimada_entrega` timestamp NULL DEFAULT NULL,
  `estatus` enum('preparando','en_transito','entregado','devuelto') NOT NULL DEFAULT 'preparando',
  `enviado_en` timestamp NULL DEFAULT NULL,
  `entregado_en` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `envios_numero_guia_unique` (`numero_guia`),
  KEY `envios_pedido_id_foreign` (`pedido_id`),
  KEY `envios_sucursal_id_foreign` (`sucursal_id`),
  CONSTRAINT `envios_pedido_id_foreign` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`),
  CONSTRAINT `envios_sucursal_id_foreign` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `envios`
--

LOCK TABLES `envios` WRITE;
/*!40000 ALTER TABLE `envios` DISABLE KEYS */;
/*!40000 ALTER TABLE `envios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `existencias`
--

DROP TABLE IF EXISTS `existencias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `existencias` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `variante_sku_id` bigint(20) unsigned NOT NULL,
  `sucursal_id` bigint(20) unsigned NOT NULL,
  `cantidad` int(10) unsigned NOT NULL DEFAULT 0,
  `stock_minimo` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `existencias_variante_sku_id_sucursal_id_unique` (`variante_sku_id`,`sucursal_id`),
  KEY `existencias_sucursal_id_foreign` (`sucursal_id`),
  CONSTRAINT `existencias_sucursal_id_foreign` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id`) ON DELETE CASCADE,
  CONSTRAINT `existencias_variante_sku_id_foreign` FOREIGN KEY (`variante_sku_id`) REFERENCES `variantes_sku` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=260 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `existencias`
--

LOCK TABLES `existencias` WRITE;
/*!40000 ALTER TABLE `existencias` DISABLE KEYS */;
INSERT INTO `existencias` VALUES
(120,71,6,1,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(121,72,5,1,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(122,73,4,1,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(123,74,4,8,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(124,75,5,9,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(125,76,6,15,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(126,77,4,2,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(127,77,5,1,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(128,78,4,4,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(129,78,5,3,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(130,78,6,6,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(131,79,4,3,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(132,79,6,5,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(133,80,5,1,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(134,81,4,1,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(135,82,4,2,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(136,82,5,1,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(137,82,6,4,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(138,83,4,1,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(139,83,5,1,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(140,84,4,2,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(141,84,6,3,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(142,85,5,2,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(143,85,6,1,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(144,86,4,1,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(145,86,6,2,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(146,87,4,2,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(147,87,5,1,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(148,87,6,2,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(149,88,4,1,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(150,89,6,1,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(151,90,4,3,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(152,90,5,2,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(153,91,4,4,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(154,91,5,2,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(155,91,6,5,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(156,92,4,1,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(157,93,5,1,1,'2026-11-22 04:15:56','2026-11-23 06:39:53'),
(158,94,4,6,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(159,95,6,1,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(160,96,5,7,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(161,97,6,14,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(162,98,4,5,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(163,98,5,2,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(164,98,6,8,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(165,99,4,4,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(166,99,5,2,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(167,100,5,5,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(168,101,5,1,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(169,102,4,6,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(170,103,4,3,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(171,103,5,2,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(172,104,6,12,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(173,105,4,5,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(174,105,5,3,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(175,106,4,2,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(176,106,5,4,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(177,107,6,8,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(178,108,4,10,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(179,109,6,1,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(180,110,4,4,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(181,110,5,4,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(182,110,6,6,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(183,111,4,1,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(184,112,5,1,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(185,113,6,1,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(186,114,4,0,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(187,115,4,2,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(188,116,4,12,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(189,117,5,9,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(190,118,6,18,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(191,119,4,2,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(192,119,5,1,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(193,120,6,1,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(194,121,4,1,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(195,122,5,1,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(196,123,6,1,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(197,124,4,4,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(198,124,5,3,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(199,124,6,5,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(200,125,4,2,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(201,125,5,1,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(202,125,6,2,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(203,126,4,15,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(204,126,5,10,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(205,126,6,20,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(206,127,4,25,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(207,127,5,15,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(208,127,6,30,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(209,128,4,5,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(210,128,5,4,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(211,128,6,8,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(212,129,4,3,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(213,129,5,4,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(214,129,6,6,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(215,130,4,8,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(216,130,5,6,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(217,130,6,10,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(218,131,4,1,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(219,131,6,2,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(220,132,4,3,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(221,132,5,2,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(222,132,6,4,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(223,133,5,1,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(224,134,4,5,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(225,134,5,3,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(226,134,6,4,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(227,135,4,1,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(228,135,5,2,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(229,136,4,1,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(230,136,6,1,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(231,137,4,3,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(232,137,5,2,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(233,137,6,4,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(234,138,4,1,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(235,139,4,1,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(236,139,5,1,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(237,140,4,1,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(238,140,6,1,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(239,141,4,8,1,'2026-11-23 06:01:10','2026-11-23 06:01:10'),
(240,142,4,9,1,'2026-11-23 06:01:10','2026-11-23 06:01:10'),
(241,143,4,9,1,'2026-11-23 06:01:10','2026-11-23 06:01:10'),
(242,144,4,5,1,'2026-11-23 06:01:10','2026-11-23 06:01:10'),
(243,145,5,6,1,'2026-11-23 06:01:10','2026-11-23 06:01:10'),
(244,146,4,4,1,'2026-11-23 06:01:10','2026-11-23 06:01:10'),
(245,147,4,1,1,'2026-11-23 06:01:10','2026-11-23 06:01:10'),
(246,148,4,5,1,'2026-11-23 06:01:10','2026-11-23 06:01:10'),
(247,149,4,1,1,'2026-11-23 06:01:10','2026-11-23 06:01:10'),
(248,150,5,1,1,'2026-11-23 06:01:10','2026-11-23 06:01:10'),
(249,151,4,10,1,'2026-11-23 06:09:54','2026-11-23 06:09:54'),
(250,152,4,15,1,'2026-11-23 06:09:54','2026-11-23 06:09:54'),
(251,153,4,5,1,'2026-11-23 06:09:54','2026-11-23 06:09:54'),
(252,145,4,6,1,'2026-11-23 06:09:54','2026-11-23 06:09:54'),
(253,154,4,1,1,'2026-11-23 06:09:54','2026-11-23 06:09:54'),
(258,150,4,1,1,'2026-11-23 06:16:45','2026-11-23 06:16:45'),
(259,162,4,1,1,'2026-11-23 06:18:55','2026-11-23 06:18:55');
/*!40000 ALTER TABLE `existencias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` varchar(255) NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `historial_envios`
--

DROP TABLE IF EXISTS `historial_envios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `historial_envios` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `envio_id` bigint(20) unsigned NOT NULL,
  `estado` varchar(60) NOT NULL,
  `ubicacion` varchar(120) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `historial_envios_envio_id_foreign` (`envio_id`),
  CONSTRAINT `historial_envios_envio_id_foreign` FOREIGN KEY (`envio_id`) REFERENCES `envios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historial_envios`
--

LOCK TABLES `historial_envios` WRITE;
/*!40000 ALTER TABLE `historial_envios` DISABLE KEYS */;
/*!40000 ALTER TABLE `historial_envios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `libros`
--

DROP TABLE IF EXISTS `libros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `libros` (
  `producto_id` bigint(20) unsigned NOT NULL,
  `isbn` varchar(17) DEFAULT NULL,
  `autor` varchar(255) NOT NULL,
  `genero` varchar(60) DEFAULT NULL,
  `editorial` varchar(255) DEFAULT NULL,
  `anio_publicacion` smallint(5) unsigned DEFAULT NULL,
  `paginas` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`producto_id`),
  UNIQUE KEY `libros_isbn_unique` (`isbn`),
  CONSTRAINT `libros_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `libros`
--

LOCK TABLES `libros` WRITE;
/*!40000 ALTER TABLE `libros` DISABLE KEYS */;
INSERT INTO `libros` VALUES
(71,'978-950-07-0010-1','Gabriel García Márquez','novela','Editorial Sudamericana',1967,351),
(72,'978-968-16-0001-2','Juan Rulfo','novela','Fondo de Cultura Económica',1955,155),
(73,'978-950-03-0049-8','Jorge Luis Borges','fantasia','Editorial Losada',1949,146),
(74,'978-968-16-0050-0','Octavio Paz','ensayo','Cuadernos Americanos',1950,195),
(75,'978-968-411-062-5','Carlos Fuentes','terror','Ediciones Era',1962,62),
(76,'978-950-07-0063-7','Julio Cortázar','novela','Editorial Sudamericana',1963,635),
(77,'978-968-16-0157-6','Octavio Paz','poesia','Fondo de Cultura Económica',1957,112),
(78,'978-970-32-4469-0','Miguel León-Portilla','historia','Universidad Nacional Autónoma de México',1959,218),
(79,'978-968-16-0442-3','Thomas S. Kuhn','ciencia','Fondo de Cultura Económica',1971,319),
(80,'978-950-04-0184-5','Edgar Allan Poe','terror','Editorial Poseidón',1945,384),
(81,'978-84-450-7055-1','Ray Bradbury','ciencia_ficcion','Ediciones Minotauro',1955,196),
(82,'978-84-450-7063-6','Ray Bradbury','ciencia_ficcion','Ediciones Minotauro',1955,248),
(83,'978-84-450-7033-9','J.R.R. Tolkien','fantasia','Ediciones Minotauro',1977,423),
(84,'978-84-264-1182-2','Umberto Eco','misterio_thriller','Editorial Lumen',1982,512),
(85,'978-84-261-0028-3','Arthur Conan Doyle','misterio_thriller','Editorial Juventud',1928,318),
(86,'978-84-03-09320-1','Friedrich Nietzsche','filosofia','Editorial Aguilar',1932,392),
(87,'978-968-36-0194-4','Platón','filosofia','Universidad Nacional Autónoma de México',1944,286),
(88,'978-84-249-1947-8','Miguel de Cervantes Saavedra','clasicos','Real Academia Española',1947,720),
(89,'978-84-376-0192-1','Dante Alighieri','clasicos','Montaner y Simón Editores',1921,684),
(90,'978-84-322-0274-2','Pablo Neruda','biografia_cronica','Editorial Seix Barral',1974,482),
(91,'978-968-432-005-5','Bernal Díaz del Castillo','biografia_cronica','Editorial Porrúa',1960,712),
(141,'978-607-39-4301-7','Yoseline Hoffman','no ficción','Editorial Planeta Mexicana',2026,320),
(142,'978-84-18051-03-6','Jordi Wild','autoayuda','Ediciones B (Penguin Random House)',2022,208),
(143,'978-607-31-7252-3','Lesslie Polinesia','Novela juvenil / Romance y drama','Montena',2019,240),
(151,'978-607-07-5477-7','TheGrefg','juvenil','Ediciones Martínez Roca (Grupo Planeta)',2018,192),
(152,'978-84-08-27248-9','Exi','juvenil','Timun Mas (Grupo Planeta)',2023,64),
(153,'6070736737','Raúl Álvarez Genes','Autobiografía / Humor','Ediciones Martínez Roca',2016,192);
/*!40000 ALTER TABLE `libros` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mensajes_contacto`
--

DROP TABLE IF EXISTS `mensajes_contacto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mensajes_contacto` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `sucursal_id` bigint(20) unsigned DEFAULT NULL,
  `asunto` varchar(200) NOT NULL,
  `mensaje` text NOT NULL,
  `estatus` enum('pendiente','leido','respondido') NOT NULL DEFAULT 'pendiente',
  `ip` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mensajes_contacto_sucursal_id_foreign` (`sucursal_id`),
  CONSTRAINT `mensajes_contacto_sucursal_id_foreign` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mensajes_contacto`
--

LOCK TABLES `mensajes_contacto` WRITE;
/*!40000 ALTER TABLE `mensajes_contacto` DISABLE KEYS */;
/*!40000 ALTER TABLE `mensajes_contacto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(1,'0001_01_01_000000_create_users_table',1),
(2,'0001_01_01_000001_create_cache_table',1),
(3,'0001_01_01_000002_create_jobs_table',1),
(4,'2026_10_22_000001_create_roles_table',1),
(5,'2026_10_22_000002_create_usuarios_table',1),
(6,'2026_10_22_000003_create_usuario_rol_table',1),
(7,'2026_10_22_000004_create_recuperacion_contrasenas_table',1),
(8,'2026_10_22_000005_create_bitacoras_table',1),
(9,'2026_10_22_000006_create_clientes_table',1),
(10,'2026_10_22_000007_create_direcciones_table',1),
(11,'2026_10_22_000008_create_clientes_frecuentes_table',1),
(12,'2026_10_22_000009_create_promociones_table',1),
(13,'2026_10_22_000010_create_cliente_promocion_table',1),
(14,'2026_10_22_000011_create_catalogos_table',1),
(15,'2026_10_22_000012_create_productos_table',1),
(16,'2026_10_22_000013_create_libros_table',1),
(17,'2026_10_22_000014_create_discos_table',1),
(18,'2026_10_22_000015_create_coleccionables_table',1),
(19,'2026_10_22_000016_create_variantes_sku_table',1),
(20,'2026_10_22_000017_create_sucursales_table',1),
(21,'2026_10_22_000018_create_existencias_table',1),
(22,'2026_10_22_000019_create_piezas_unicas_table',1),
(23,'2026_10_22_000020_create_movimientos_inventario_table',1),
(24,'2026_10_22_000021_create_carritos_table',1),
(25,'2026_10_22_000022_create_carrito_items_table',1),
(26,'2026_10_22_000023_create_pedidos_table',1),
(27,'2026_10_22_000024_create_detalle_pedidos_table',1),
(28,'2026_10_22_000025_create_reservas_stock_table',1),
(29,'2026_10_22_000026_create_pagos_table',1),
(30,'2026_10_22_000027_create_detalle_pagos_table',1),
(31,'2026_10_22_000028_create_devoluciones_table',1),
(32,'2026_10_22_000029_create_envios_table',1),
(33,'2026_10_22_000030_create_detalle_envios_table',1),
(34,'2026_10_28_014911_create_historial_envios_table',1),
(35,'2026_10_28_014912_add_tracking_fields_to_envios_table',1),
(36,'2026_10_28_014913_add_payment_methods_fields_to_pagos_table',1),
(37,'2026_11_06_000001_add_preventa_fields_to_productos_table',1),
(38,'2026_11_09_000001_add_garantia_fields_to_devoluciones_table',1),
(39,'2026_11_09_000002_add_preventa_fields_to_promociones_and_productos_table',1),
(40,'2026_11_10_000001_add_filtros_to_libros_and_coleccionables_tables',1),
(41,'2026_11_11_000001_add_imagen_to_discos_table',1),
(42,'2026_11_11_000002_add_imagen_to_coleccionables_table',1),
(43,'2026_11_16_000001_create_proveedores_table',1),
(44,'2026_11_17_055027_create_mensajes_contacto_table',1),
(45,'2026_11_17_234459_add_imagen_to_productos_and_variantes_sku_table',1),
(46,'2026_11_20_000001_add_precio_unitario_congelado_to_carrito_items_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `movimientos_inventario`
--

DROP TABLE IF EXISTS `movimientos_inventario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `movimientos_inventario` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `existencia_id` bigint(20) unsigned NOT NULL,
  `usuario_id` bigint(20) unsigned DEFAULT NULL,
  `tipo` enum('entrada','salida','ajuste','traspaso','devolucion') NOT NULL,
  `cantidad` int(11) NOT NULL,
  `motivo` varchar(255) DEFAULT NULL,
  `referencia_type` varchar(255) DEFAULT NULL,
  `referencia_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `proveedor_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `movimientos_inventario_existencia_id_foreign` (`existencia_id`),
  KEY `movimientos_inventario_usuario_id_foreign` (`usuario_id`),
  KEY `movimientos_inventario_referencia_type_referencia_id_index` (`referencia_type`,`referencia_id`),
  KEY `movimientos_inventario_proveedor_id_foreign` (`proveedor_id`),
  CONSTRAINT `movimientos_inventario_existencia_id_foreign` FOREIGN KEY (`existencia_id`) REFERENCES `existencias` (`id`),
  CONSTRAINT `movimientos_inventario_proveedor_id_foreign` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`) ON DELETE SET NULL,
  CONSTRAINT `movimientos_inventario_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `movimientos_inventario`
--

LOCK TABLES `movimientos_inventario` WRITE;
/*!40000 ALTER TABLE `movimientos_inventario` DISABLE KEYS */;
INSERT INTO `movimientos_inventario` VALUES
(1,239,11,'entrada',8,'Importación masiva [LIB-2026-003]',NULL,NULL,'2026-11-23 06:01:10',NULL),
(2,240,11,'entrada',9,'Importación masiva [LIB-2026-005]',NULL,NULL,'2026-11-23 06:01:10',NULL),
(3,241,11,'entrada',9,'Importación masiva [LIB-2026-007]',NULL,NULL,'2026-11-23 06:01:10',NULL),
(4,242,11,'entrada',5,'Importación masiva [DIS-2026-002]',NULL,NULL,'2026-11-23 06:01:10',NULL),
(5,243,11,'entrada',6,'Importación masiva [DIS-2026-003]',NULL,NULL,'2026-11-23 06:01:10',NULL),
(6,244,11,'entrada',4,'Importación masiva [DIS-2026-004]',NULL,NULL,'2026-11-23 06:01:10',NULL),
(7,245,11,'entrada',1,'Importación masiva [DIS-2026-006]',NULL,NULL,'2026-11-23 06:01:10',NULL),
(8,246,11,'entrada',5,'Importación masiva [DIS-2026-007]',NULL,NULL,'2026-11-23 06:01:10',NULL),
(9,247,11,'entrada',1,'Importación masiva [COL-2036.002]',NULL,NULL,'2026-11-23 06:01:10',NULL),
(10,248,11,'entrada',1,'Importación masiva [COL-2036.003]',NULL,NULL,'2026-11-23 06:01:10',NULL),
(11,249,11,'entrada',10,'Importación masiva [LIB-2026-002]',NULL,NULL,'2026-11-23 06:09:54',NULL),
(12,250,11,'entrada',15,'Importación masiva [LIB-2026-004]',NULL,NULL,'2026-11-23 06:09:54',NULL),
(13,251,11,'entrada',5,'Importación masiva [LIB-2026-006]',NULL,NULL,'2026-11-23 06:09:54',NULL),
(14,252,11,'entrada',6,'Importación masiva [DIS-2026-003]',NULL,NULL,'2026-11-23 06:09:54',NULL),
(15,253,11,'entrada',1,'Importación masiva [DIS-2026-005]',NULL,NULL,'2026-11-23 06:09:54',NULL),
(16,258,11,'entrada',1,'Importación masiva [COL-2036.003]',NULL,NULL,'2026-11-23 06:16:45',NULL),
(17,259,11,'entrada',1,'Importación masiva [COL-2076.010]',NULL,NULL,'2026-11-23 06:18:55',NULL);
/*!40000 ALTER TABLE `movimientos_inventario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pagos`
--

DROP TABLE IF EXISTS `pagos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pagos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pedido_id` bigint(20) unsigned NOT NULL,
  `metodo` enum('tarjeta','transferencia','efectivo','paypal','otro') NOT NULL,
  `metodo_pago` enum('oxxo','transferencia','tarjeta') DEFAULT NULL,
  `codigo_oxxo` varchar(60) DEFAULT NULL,
  `referencia_bancaria` varchar(80) DEFAULT NULL,
  `expira_at` timestamp NULL DEFAULT NULL,
  `comprobante_url` varchar(255) DEFAULT NULL,
  `monto` decimal(12,2) NOT NULL,
  `estatus` enum('pendiente','aprobado','rechazado','reembolsado') NOT NULL DEFAULT 'pendiente',
  `pagado_en` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pagos_pedido_id_foreign` (`pedido_id`),
  CONSTRAINT `pagos_pedido_id_foreign` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pagos`
--

LOCK TABLES `pagos` WRITE;
/*!40000 ALTER TABLE `pagos` DISABLE KEYS */;
/*!40000 ALTER TABLE `pagos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pedidos`
--

DROP TABLE IF EXISTS `pedidos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pedidos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `folio` varchar(30) NOT NULL,
  `cliente_id` bigint(20) unsigned NOT NULL,
  `direccion_envio_id` bigint(20) unsigned DEFAULT NULL,
  `estatus` enum('pendiente','pagado','enviado','entregado','cancelado') NOT NULL DEFAULT 'pendiente',
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `descuento` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pedidos_folio_unique` (`folio`),
  KEY `pedidos_direccion_envio_id_foreign` (`direccion_envio_id`),
  KEY `pedidos_cliente_id_estatus_index` (`cliente_id`,`estatus`),
  CONSTRAINT `pedidos_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `pedidos_direccion_envio_id_foreign` FOREIGN KEY (`direccion_envio_id`) REFERENCES `direcciones` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedidos`
--

LOCK TABLES `pedidos` WRITE;
/*!40000 ALTER TABLE `pedidos` DISABLE KEYS */;
/*!40000 ALTER TABLE `pedidos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `piezas_unicas`
--

DROP TABLE IF EXISTS `piezas_unicas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `piezas_unicas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `existencia_id` bigint(20) unsigned NOT NULL,
  `numero_serie` varchar(80) NOT NULL,
  `certificado` varchar(255) DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `estado` enum('disponible','reservada','vendida') NOT NULL DEFAULT 'disponible',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `piezas_unicas_numero_serie_unique` (`numero_serie`),
  KEY `piezas_unicas_existencia_id_foreign` (`existencia_id`),
  CONSTRAINT `piezas_unicas_existencia_id_foreign` FOREIGN KEY (`existencia_id`) REFERENCES `existencias` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `piezas_unicas`
--

LOCK TABLES `piezas_unicas` WRITE;
/*!40000 ALTER TABLE `piezas_unicas` DISABLE KEYS */;
INSERT INTO `piezas_unicas` VALUES
(22,120,'ARC-LIB-1967-001','COA-BIB-2026-014','Ejemplar único 1/1 catalogado en bóveda climatizada (VERY FINE (VF) · PIEZA ÚNICA 1/1).','disponible','2026-11-22 04:15:56','2026-11-22 04:15:56'),
(23,121,'ARC-LIB-1955-002','COA-BIB-2026-019','Ejemplar único 1/1 catalogado en bóveda climatizada (NEAR MINT (NM) · PIEZA ÚNICA 1/1 · ADQUISICIÓN SELECTA).','disponible','2026-11-22 04:15:56','2026-11-22 04:15:56'),
(24,122,'ARC-LIB-1949-003','COA-BIB-2026-022','Ejemplar único 1/1 catalogado en bóveda climatizada (MINT (M) · PIEZA ÚNICA 1/1 · ADQUISICIÓN SELECTA).','disponible','2026-11-22 04:15:56','2026-11-22 04:15:56'),
(25,133,'ARC-LIB-1845-010','COA-BIB-2026-031','Ejemplar único 1/1 catalogado en bóveda climatizada (VERY FINE (VF) · TERROR GÓTICO · GRABADOS XILOGRÁFICOS).','disponible','2026-11-22 04:15:56','2026-11-22 04:15:56'),
(26,134,'ARC-LIB-1955-011','COA-BIB-2026-034','Ejemplar único 1/1 catalogado en bóveda climatizada (MINT (M) · PIEZA ÚNICA 1/1 · CIENCIA FICCIÓN DISTÓPICA).','disponible','2026-11-22 04:15:56','2026-11-22 04:15:56'),
(27,149,'ARC-LIB-1605-015','COA-BIB-2026-045','Ejemplar único 1/1 catalogado en bóveda climatizada (MINT (M) · CLÁSICOS UNIVERSALES · ESTUCHE DE TELA).','disponible','2026-11-22 04:15:56','2026-11-22 04:15:56'),
(28,150,'ARC-LIB-1320-016','COA-BIB-2026-048','Ejemplar único 1/1 catalogado en bóveda climatizada (NEAR MINT (NM) · PIEZA ÚNICA 1/1 · GRAN FOLIO DORÉ).','disponible','2026-11-22 04:15:56','2026-11-22 04:15:56'),
(29,156,'ARC-VIN-1959-001','COA-AUD-2026-101','Ejemplar único 1/1 catalogado en bóveda climatizada (MINT (M) · PIEZA ÚNICA 1/1 · ADQUISICIÓN SELECTA).','disponible','2026-11-22 04:15:56','2026-11-22 04:15:56'),
(30,157,'ARC-VIN-1957-002','COA-AUD-2026-104','Ejemplar único 1/1 catalogado en bóveda climatizada (NEAR MINT (NM) · PIEZA ÚNICA 1/1 · ADQUISICIÓN SELECTA).','disponible','2026-11-22 04:15:56','2026-11-23 06:39:53'),
(31,159,'ARC-VIN-1969-004','COA-AUD-2026-112','Ejemplar único 1/1 catalogado en bóveda climatizada (MINT (M) · PIEZA ÚNICA 1/1 · ADQUISICIÓN SELECTA).','disponible','2026-11-22 04:15:56','2026-11-22 04:15:56'),
(32,168,'ARC-VIN-1990-001','COA-MUS-2026-101','Ejemplar único 1/1 catalogado en bóveda climatizada (NEAR MINT (NM) · PIEZA ÚNICA 1/1).','disponible','2026-11-22 04:15:56','2026-11-22 04:15:56'),
(33,179,'ARC-VIN-2024-002','COA-MUS-2026-102','Ejemplar único 1/1 catalogado en bóveda climatizada (MINT (M) · PIEZA ÚNICA 1/1).','disponible','2026-11-22 04:15:57','2026-11-22 04:15:57'),
(34,183,'ARC-MEM-1928-001','COA-NOT-2026-301','Ejemplar único 1/1 catalogado en bóveda climatizada (COA NOTARIADO · PIEZA ÚNICA 1/1).','disponible','2026-11-22 04:15:57','2026-11-22 04:15:57'),
(35,184,'ARC-MEM-1940-004','COA-NOT-2026-320','Ejemplar único 1/1 catalogado en bóveda climatizada (MINT (M) · PIEZA ÚNICA 1/1 · ADQUISICIÓN SELECTA).','disponible','2026-11-22 04:15:57','2026-11-22 04:15:57'),
(36,185,'ARC-MEM-1910-005','COA-NOT-2026-335','Ejemplar único 1/1 catalogado en bóveda climatizada (MINT (M) · PIEZA ÚNICA 1/1 · ADQUISICIÓN SELECTA).','disponible','2026-11-22 04:15:57','2026-11-22 04:15:57'),
(37,186,'ARC-MEM-1934-002','COA-NOT-2026-309','Ejemplar único 1/1 catalogado en bóveda climatizada (MINT (M) · COA NOTARIADO · ADQUISICIÓN SELECTA).','vendida','2026-11-22 04:15:57','2026-11-22 04:15:57'),
(38,193,'ARC-COL-1933-001','COA-COL-2026-001','Ejemplar único 1/1 catalogado en bóveda climatizada (MINT (M) · MONEDA HISTÓRICA RARA).','disponible','2026-11-22 04:15:57','2026-11-22 04:15:57'),
(39,194,'ARC-COL-1794-002','COA-COL-2026-002','Ejemplar único 1/1 catalogado en bóveda climatizada (VERY FINE (VF) · PRIMER DÓLAR FEDERAL).','disponible','2026-11-22 04:15:57','2026-11-22 04:15:57'),
(40,195,'ARC-COL-1344-003','COA-COL-2026-003','Ejemplar único 1/1 catalogado en bóveda climatizada (NEAR MINT (NM) · PIEZA ÚNICA MEDIEVAL).','disponible','2026-11-22 04:15:57','2026-11-22 04:15:57'),
(41,196,'ARC-COL-0723-004','COA-COL-2026-004','Ejemplar único 1/1 catalogado en bóveda climatizada (MINT (M) · CALIFATO OMEYA).','disponible','2026-11-22 04:15:57','2026-11-22 04:15:57'),
(42,223,'ARC-FIG-1992-005','COA-FIG-2026-005','Ejemplar único 1/1 catalogado en bóveda climatizada (VERY FINE (VF) · ARTE POPULAR INDÍGENA).','disponible','2026-11-22 04:15:57','2026-11-22 04:15:57'),
(43,245,'ARC-DIS-2026-006','COA-BIB-2027-006','Alta por importación masiva Excel/CSV.','disponible','2026-11-23 06:01:10','2026-11-23 06:01:10'),
(44,247,'BAS-1/1-CS2026','COA-COL-2026-002','Alta por importación masiva Excel/CSV.','disponible','2026-11-23 06:01:10','2026-11-23 06:01:10'),
(45,248,'COL-SID-ST1616','COA-COL-2026-003','Alta por importación masiva Excel/CSV.','disponible','2026-11-23 06:01:10','2026-11-23 06:01:10'),
(46,253,'ARC-DIS-2026-005','COA-BIB-2027-005','Alta por importación masiva Excel/CSV.','disponible','2026-11-23 06:09:54','2026-11-23 06:09:54'),
(51,258,'COL-SID-ST1678','COA-COL-2026-010','Alta por importación masiva Excel/CSV.','disponible','2026-11-23 06:16:45','2026-11-23 06:16:45'),
(52,259,'COL-TOP-MLB52-MM311-SGC89','COA-COL-2026-015','Alta por importación masiva Excel/CSV.','disponible','2026-11-23 06:18:55','2026-11-23 06:18:55');
/*!40000 ALTER TABLE `piezas_unicas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productos`
--

DROP TABLE IF EXISTS `productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `productos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `catalogo_id` bigint(20) unsigned NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `imagen` varchar(500) DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `es_preventa` tinyint(1) NOT NULL DEFAULT 0,
  `limite_preventa` int(10) unsigned DEFAULT NULL,
  `preventa_exclusiva_hasta` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `productos_catalogo_id_foreign` (`catalogo_id`),
  KEY `productos_nombre_index` (`nombre`),
  CONSTRAINT `productos_catalogo_id_foreign` FOREIGN KEY (`catalogo_id`) REFERENCES `catalogos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=163 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES
(71,4,'Cien Años de Soledad (1ª Edición Sudamericana)','Pieza Única (1/1) custodiada en Almacén General: Tiraje inicial de Buenos Aires (Mayo 1967) con prueba de galera firmada. Cubierta original con galeón en la selva diseñada por Iris Alba.',NULL,32000.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-22 04:15:56',NULL),
(72,4,'Pedro Páramo (Edición Príncipe FCE)','Pieza Única (1/1) custodiada en Sucursal Coyoacán: Primera edición de 1955 en la Colección Letras Mexicanas N.º 19 con firma autógrafa de Juan Rulfo en guarda anterior.',NULL,44500.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-22 04:15:56',NULL),
(73,4,'El Aleph (Ejemplar Dedicado por el Autor)','Pieza Única (1/1) custodiada en Sucursal Central Bellas Artes: Primera edición impresa por Editorial Losada (1949) con dedicatoria manuscrita de Jorge Luis Borges.',NULL,58000.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-22 04:15:56',NULL),
(74,4,'El Laberinto de la Soledad (1ª Edición Cuadernos Americanos)','Stock Masivo en Sucursal Central Bellas Artes: Edición de tiraje múltiple impresa en los talleres de Cuadernos Americanos en papel Chamois. Permite adquirir múltiples ejemplares.',NULL,16900.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-22 04:15:56',NULL),
(75,4,'Aura (Edición Conmemorativa Biblioteca Era Coyoacán)','Stock Masivo en Sucursal Coyoacán: Tiraje regular encuadernado en tela editorial por Ediciones Era. Disponible para compra de varias unidades sin bloqueo exclusivo.',NULL,6800.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-22 04:15:56',NULL),
(76,4,'Rayuela (Tiraje Clásico Editorial Sudamericana)','Stock Masivo en Almacén General: Edición conmemorativa de tiraje amplio con tablero de dirección de lectura. Existencias múltiples disponibles para despacho inmediato.',NULL,8400.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-22 04:15:56',NULL),
(77,4,'Piedra de Sol y Obra Poética (Primera Edición 1957)','Pieza de Poesía de Alto Valor Curatorial: Primera edición del poema circular de 584 versos endecasílabos numerados. Ejemplar de colección impreso en papel especial.',NULL,24500.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-22 04:15:56',NULL),
(78,4,'Visión de los Vencidos: Relaciones Indígenas de la Conquista','Obra Fundamental de Historia: Testimonios de los informantes de Sahagún traducidos del náhuatl por Ángel María Garibay con textos y notas de Miguel León-Portilla.',NULL,9800.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-22 04:15:56',NULL),
(79,4,'La Estructura de las Revoluciones Científicas (1ª Edición FCE)','Obra Cumbre de Ciencia y Epistemología: Traducción clásica al español del tratado pionero de Thomas Kuhn sobre los cambios de paradigma en la investigación científica.',NULL,11200.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-22 04:15:56',NULL),
(80,4,'Narraciones Extraordinarias & El Cuervo (Edición Ilustrada 1945)','Pieza de Terror Gótico en Bóveda Coyoacán: Traducción canónica de Charles Baudelaire y notas de Julio Cortázar. Encuadernación en tela con grabados xilográficos originales.',NULL,18500.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-22 04:15:56',NULL),
(81,4,'Fahrenheit 451 (Primera Edición en Español Minotauro 1955)','Pieza Única (1/1) en Sucursal Central Bellas Artes: Primera edición hispanoamericana traducida por Francisco Porrúa con sobrecubierta tipográfica original intacta.',NULL,26000.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-22 04:15:56',NULL),
(82,4,'Crónicas Marcianas (Edición Príncipe con Prólogo de Borges 1955)','Stock Masivo en Almacén General: Célebre edición con prólogo de Jorge Luis Borges reflexionando sobre la soledad y la condición humana en el cosmos.',NULL,21500.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-22 04:15:56',NULL),
(83,4,'El Señor de los Anillos: La Comunidad del Anillo (1ª Edición Minotauro 1977)','Obra Cumbre de Fantasía Épica: Primera traducción autorizada al español con sobrecubierta ilustrada y mapa desplegable de la Tierra Media en perfecto estado.',NULL,29800.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-22 04:15:56',NULL),
(84,4,'El Nombre de la Rosa (1ª Edición en Castellano Lumen 1982)','Pieza Selecta de Misterio & Semiótica: Primera tirada en castellano con sobrecubierta original de diseño medieval. Clásico del thriller histórico monástico.',NULL,14200.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-22 04:15:56',NULL),
(85,4,'Estudio en Escarlata & El Sabueso de los Baskerville (Edición Anotada 1928)','Clásico de Novela Policíaca y Misterio: Primera traducción de la Colección Misterio de Editorial Juventud con grabados e ilustraciones de época.',NULL,17800.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-22 04:15:56',NULL),
(86,4,'Así Habló Zaratustra: Un Libro para Todos y para Nadie (Edición Aguilar 1932)','Encuadernación de Lujo en Piel: Edición de la célebre Colección Crisol con cantos dorados y papel biblia. Obra fundamental de la filosofía continental moderna.',NULL,19500.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-22 04:15:56',NULL),
(87,4,'El Banquete y Fedón (Traducción Clásica Bibliotheca Scriptorum 1944)','Filosofía Griega Clásica: Edición bilingüe griego-español en papel de hilo fabricado especialmente para la imprenta universitaria de la UNAM.',NULL,13400.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-22 04:15:56',NULL),
(88,4,'Don Quijote de la Mancha (Edición Conmemorativa Facsimilar RAE 1947)','Pieza Cumbre de Clásicos Universales: Edición monumental conmemorativa del IV Centenario de Cervantes en dos volúmenes en papel de tina con estuche de tela inglesa.',NULL,38000.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-22 04:15:56',NULL),
(89,4,'La Divina Comedia (Edición Bilingüe con Grabados de Gustave Doré 1921)','Gran Folio Monumental: Encuadernado en piel gofrada con 135 láminas calcográficas de Gustave Doré sobre el Infierno, Purgatorio y Paraíso.',NULL,42000.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-22 04:15:56',NULL),
(90,4,'Confieso que he Vivido: Memorias (Primera Edición Seix Barral 1974)','Biografía & Crónica Esencial: Primera edición póstuma de las memorias de Pablo Neruda preparada por Matilde Urrutia. Colección Biblioteca Breve.',NULL,15600.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-22 04:15:56',NULL),
(91,4,'Historia Verdadera de la Conquista de la Nueva España (Edición Crítica 1960)','Crónica Fundamental de Indias: Edición canónica de la Colección Sepan Cuantos N.º 5 con estudio y mapas facsimilares de la ruta de Cortés.',NULL,8900.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-22 04:15:56',NULL),
(92,5,'Kind of Blue (Mono 1st Press 6-Eye)','Pieza Única (1/1) en Sucursal Central Bellas Artes: Matriz original XLP 47324-1A con etiqueta Columbia «6-eye» deep groove y firma certificada.',NULL,18500.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-22 04:15:56',NULL),
(93,5,'Blue Train (Plastylite RVG Ear Press)','Pieza Única (1/1) en Sucursal Coyoacán: Grabado por Rudy Van Gelder con sello «ear» en surco muerto y dirección 47 West 63rd NYC.',NULL,27500.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-22 04:15:56',NULL),
(94,5,'A Love Supreme (Gatefold 1st Impulse! Mono)','Stock Masivo en Sucursal Central Bellas Artes: Carpeta desplegable laminada con estampado VAN GELDER. Existencias múltiples disponibles.',NULL,16200.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-22 04:15:56',NULL),
(95,5,'Abbey Road (UK 1st Press Misaligned Apple)','Pieza Única (1/1) en Almacén General: Prensado británico matriz YEX 749-2 / 750-1 con manzana desalineada en contraportada.',NULL,22900.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-22 04:15:56',NULL),
(96,5,'The Dark Side of the Moon (1st Japan Black Triangle CD)','Stock Masivo en Sucursal Coyoacán: Edición en Disco Compacto prensada por Toshiba-EMI en Japón (CP35-3017). Permite adquirir múltiples unidades.',NULL,14800.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-22 04:15:56',NULL),
(97,5,'Led Zeppelin IV (Prensado Audiófilo Atlantic 180g)','Stock Masivo en Almacén General: Reedición audiófila de tiraje regular en vinilo virgen de 180 gramos masterizado desde las cintas analógicas.',NULL,5900.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-22 04:15:56',NULL),
(98,5,'OK Computer (Collector\'s Edition 2-CD Parlophone)','Edición Maestra en Disco Compacto: Edición doble de 1997 con estuche digipak de colección, libreto con ilustraciones de Stanley Donwood y lados B masterizados.',NULL,4800.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-22 04:15:56',NULL),
(99,5,'Las Cabras Montañesas - Góticos (2 LP)','Álbum doble de vinilo (2 LP) en edición de alta calidad.','productos/las-cabras-montañeras.jpg',521.50,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-23 05:55:47',NULL),
(100,5,'Los Yaki – Los Grandes Años Del Rock','Edición clásica de Latin Gold Records recuperada para coleccionistas de rock en español.','productos/Los-Yaki.jpg',372.56,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-23 05:55:47',NULL),
(101,5,'Caifanes – Caifanes (LP, Edición Original Venezuela)','Pieza Única (1/1) custodiada en Sucursal Coyoacán: Edición original de 1990 prensada en Venezuela bajo el sello RCA (102-02124).','productos/Caifanes.jpg',7413.93,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-23 05:55:47',NULL),
(102,5,'Mägo de Oz - La Ciudad de los Árboles (Lp Vinyl)','Edición en LP de vinilo de la reconocida banda de folk metal.','productos/Mago-de-oz-La-ciudad.jpg',1566.55,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-23 05:55:47',NULL),
(103,5,'Mägo de Oz - Alicia En El Pais Del Metalverso (Lp Vinyl)','Edición especial en formato LP de vinilo.','productos/Mago-de-oz-Alicia.jpg',864.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-23 05:55:47',NULL),
(104,5,'Los Grandes Románticos de la Balada (3CDs)','Compilación de colección con José José, Juan Gabriel, Rocío Dúrcal, entre otros.','productos/Los-Grandes-Romanticos.jpg',179.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-23 05:55:47',NULL),
(105,5,'La Arrolladora Banda El Limón - Las Bandas Románticas','CD con grandes éxitos románticos de banda sinaloense.','productos/Cd-la-arrolladora.jpg',183.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-23 05:55:47',NULL),
(106,5,'Juan Gabriel & Rocío Dúrcal - Juntos Otra Vez (2CD)','Álbum doble en CD de dos grandes exponentes de la música ranchera y balada.','productos/Juan-Gabriel.jpg',129.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-23 05:55:47',NULL),
(107,5,'La Sonora Santanera - Tesoros de Colección Vol. 3 (3 Discos)','Edición especial de 3 discos originales en formato CD.','productos/3-cds-La-Sonora.jpg',391.50,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-23 05:55:47',NULL),
(108,5,'Los Mejores Huapangos | CD Música','CD con selección folklórica de los huapangos tradicionales más emblemáticos.','productos/Huapangos.jpg',69.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-23 05:55:47',NULL),
(109,5,'Taylor Swift - Midnights (Korea Edition)','Pieza Única (1/1) custodiada en Almacén General: Edición coleccionable de importación coreana.','productos/TAYLO.jpg',641.68,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 05:55:47',NULL),
(110,5,'Buena Vista Social Club (Edición CD Remasterizada World Circuit)','Obra Fonográfica Esencial en Disco Compacto: Grabaciones de La Habana bajo la producción de Ry Cooder, masterizado digital con folleto historiográfico de 48 páginas.',NULL,3950.00,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-22 04:15:57',NULL),
(111,6,'Carta Manuscrita Bellas Artes (1928)','Pieza Única (1/1) en Sucursal Central Bellas Artes: Pluma estilográfica con tinta ferrogálica original sobre papel membretado con COA notariado.',NULL,9400.00,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-22 04:15:57',NULL),
(112,6,'Manuscrito Autógrafo Casa Azul Coyoacán (1940)','Pieza Única (1/1) en Sucursal Coyoacán: Misiva autógrafa en papel de algodón fechada en Villa Coyoacán con sello lacrado original.',NULL,36800.00,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-22 04:15:57',NULL),
(113,6,'Medalla Conmemorativa del Centenario (1910)','Pieza Única (1/1) en Almacén General: Medalla oficial en plata ley .925 acuñada por la Casa de Moneda en 1910 con estuche de terciopelo original.',NULL,29500.00,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-22 04:15:57',NULL),
(114,6,'Fotografía en Plata sobre Gelatina Firmada (1934)','Copia de época (vintage print) revelada en cuarto oscuro por el estudio original, con sello húmedo al reverso y paspartú libre de ácido.',NULL,41000.00,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-22 04:15:57',NULL),
(115,6,'Partitura Autógrafa de Estudio en Sol Menor (1912)','Lote limitado en Sucursal Central Bellas Artes: 2 cuadernos musicales de 12 folios con anotaciones de dinámica a lápiz. Conservado en carpeta museográfica.',NULL,21500.00,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-22 04:15:57',NULL),
(116,6,'Programa Inaugural Palacio de Bellas Artes (1934)','Stock Masivo en Sucursal Central Bellas Artes: Folleto conmemorativo oficial de la inauguración del Palacio de Bellas Artes en septiembre de 1934.',NULL,7800.00,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-22 04:15:57',NULL),
(117,6,'Litografía Taller de Gráfica Popular Coyoacán (1954)','Stock Masivo en Sucursal Coyoacán: Grabado en linóleo de tiraje amplio impreso sobre papel de algodón en Coyoacán. Disponible en múltiples unidades.',NULL,5400.00,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-22 04:15:57',NULL),
(118,6,'Kit de Conservación Museográfica & Estuche Antiácido','Stock Masivo en Almacén General: Set profesional de fundas de polipropileno grado archivo, guantes de nitrilo y caja libre de ácido para libros y vinilos.',NULL,3200.00,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-22 04:15:57',NULL),
(119,6,'Estatua Evangelion Test Type-01 (Edición Limitada Kaiyodo 1997)','Figuras Coleccionables (Cultura Pop & Anime): Escultura en resina pintada a mano supervisada por Gainax Japón. Incluye pedestal con número de serie de tiraje.',NULL,18900.00,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-22 04:15:57',NULL),
(120,6,'Double Eagle de 1933 (EE. UU.)','Moneda de oro de $20 que el gobierno ordenó fundir durante la Gran Depresión. Pieza de valor histórico incalculable.','productos/Double-Eagle-de-1933.jpg',18872250.00,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 05:55:47',NULL),
(121,6,'Dólar Flowing Hair de 1794 (EE. UU.)','El primer dólar de plata acuñado por el gobierno federal estadounidense.','productos/Dolar-Flowing-Hair-de-1794.jpg',175000.00,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 05:55:47',NULL),
(122,6,'Florín de Eduardo III de 1344 (Inglaterra)','Legendaria moneda de oro medieval de la cual solo se conocen tres ejemplares en todo el mundo.','productos/Florin-de-Eduardo-III-de-1344.jpg',7000000.00,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 05:55:47',NULL),
(123,6,'Dinar de Oro Omeya del año 723 d.C.','Acuñada en la región de la actual Arabia Saudita con oro proveniente de una mina del propio Califa.','productos/Dinar-de-Oro-Omeya-del-año-723.jpg',95600000.00,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 05:55:47',NULL),
(124,6,'Monedas de 8 Reales (Época Colonial / Spanish Dollars)','Conocidas mundialmente como columnarios o piezas de 8, fueron la primera moneda de uso internacional.','productos/Monedas-Reales.jpg',5500.00,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 05:55:47',NULL),
(125,6,'Centenario de Oro (50 Pesos)','Contiene 37.5 gramos de oro puro y presenta el clásico diseño del Ángel de la Independencia.','productos/Centenario-de-Oro.jpg',92250.00,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 05:55:47',NULL),
(126,6,'Moneda de Plata Libertad (Onza Troy)','Una de las monedas de inversión (bullion) de plata más hermosas y cotizadas del mundo.','productos/Moneda-Libertad.jpg',1250.00,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 05:55:47',NULL),
(127,6,'Monedas Conmemorativas de 20 Pesos (Familia C y C1)','Piezas recientes conmemorativas (como la Fundación de Veracruz o el Bicentenario de la Independencia).','productos/Monedas-Conmemorativas.jpg',100.00,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 05:55:47',NULL),
(128,6,'Familia AA y A (Billetes Desmonetizados)','Billetes antiguos impresos por la American Bank Note Company y de fabricación nacional.','productos/Familia-AAA.jpg',325.00,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 05:55:47',NULL),
(129,6,'Pop Mart Labubu (Designer Toy Coleccionable)','Figura de diseñador caracterizada por orejas puntiagudas, cuerpo peludo, sonrisa traviesa con 9 dientes y rostro de vinilo.','productos/Pop-Mart.jpg',1100.00,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 05:55:47',NULL),
(130,6,'Funko Pop! Edición Especial Coleccionable','Figura de vinilo con cabeza grande y diseño estilizado de cultura pop.','productos/Funko-Pop.jpg',1075.00,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 05:55:47',NULL),
(131,6,'Lego Sets Temáticos y de Colección (Edición Especial)','Set de construcción de edición de coleccionista con miles de piezas y minifiguras exclusivas.','productos/lego.jpg',19999.00,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 05:55:47',NULL),
(132,6,'Estatua Anubis Egipcia con Recipiente Base','Estatua decorativa de acabado negro con detalles dorados e iconografía del antiguo Egipto.','productos/Estatua-Anubis.jpg',773.08,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 05:55:47',NULL),
(133,6,'Figura de Cerámica \"Storyteller\" de Cochiti','Pieza artesanal tradicional de Snowflake Flower, ca. 1992, narrativa tradicional en arcilla.','productos/Figura-ceramica.jpg',4914.54,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 05:55:47',NULL),
(134,6,'Exquisito Joyero de Estilo Egipcio Decorado con Anubis','Joyero ornamental decorado con motivos y deidades de la mitología egipcia antigua.','productos/Exquisito-Joyero.jpg',600.49,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 05:55:47',NULL),
(135,6,'Figura Dragon Ball Z Super Saiyan Goku (Banpresto Japón 1993)','Figuras Coleccionables (Cultura Pop & Anime): Figura vintage con caja sellada original de Toei Animation Japón. Conservada en acrílico anti-UV de coleccionista.',NULL,14200.00,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-22 04:15:57',NULL),
(136,6,'Robot Mecánico de Hojalata Space Trooper (Japón Vintage 1965)','Juguetes y Objetos Manufacturados: Juguete mecánico a cuerda en hojalata litografiada manufacturado por Yonezawa Toys Japón. Mecanismo de marcha y chispa plenamente funcional.',NULL,21800.00,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-22 04:15:57',NULL),
(137,6,'Billete Revolucionario 10 Pesos Ejército Constitucionalista (1914)','Monedas y Billetes Históricos: Emisión oficial del Gobierno Provisional de México firmada en Monclova. Papel moneda histórico en estado sin circular (UNC).',NULL,8900.00,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-22 04:15:57',NULL),
(138,6,'Moneda Virreinal 8 Reales Columnario de Plata (1768)','Monedas y Billetes Históricos: Moneda de 8 Reales acuñada a volante en la Real Casa de Moneda de México bajo Carlos III. Plata pura con pátina histórica intocada.',NULL,38500.00,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-22 04:15:57',NULL),
(139,6,'Consola Nintendo Game & Watch Ball (Primera Edición Japón 1980)','Coleccionables de Videojuegos: El primer sistema portátil creado por Gunpei Yokoi para Nintendo (Modelo AC-01). En caja de colección con manual y batería sellada.',NULL,26900.00,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-22 04:15:57',NULL),
(140,6,'Cartucho The Legend of Zelda (Gold Cartridge NES 1987 Sellado)','Coleccionables de Videojuegos: Cartucho dorado original para Nintendo Entertainment System con batería de respaldo interna y sello pericial de graduación Wata/VGA.',NULL,34000.00,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-22 04:15:57',NULL),
(141,4,'Cancelada encarcelada','A través de las páginas íntimas de su diario en prisión, la autora desnuda el miedo, la soledad y las sombras del sistema penitenciario, revelando lo que ocurre cuando el tribunal de las redes sociales y la justicia real chocan de frente.','productos/z3HOwFAzBCfqVSpc2bwJgNsMIOgEHW6MLMJvgpP9.jpg',249.00,1,0,NULL,NULL,'2026-11-23 06:01:10','2026-11-23 06:39:34',NULL),
(142,4,'Así es la puta vida','Se presenta como un libro de \"anti-autoayuda\" que busca alejarse de los clichés motivacionales y las frases positivas vacías. Con un tono crudo, directo y apoyado en conceptos psicológicos y sociológicos, Jordi Wild plantea que la vida está llena de golpes inevitables y que nadie es el centro del universo.','productos/mXsakZMRdos8e3XhJrGNMrnfTTFiGGbpbXI5FMds.jpg',150.00,1,0,NULL,NULL,'2026-11-23 06:01:10','2026-11-23 06:39:34',NULL),
(143,4,'Tres Promesas','Lily Rose, Pablo y Ana, tres jóvenes cuyas vidas dan un giro inesperado cuando los planes que creían seguros cambian de golpe. A través de un viaje entre los recuerdos de su infancia y las decisiones de su presente, los protagonistas deben enfrentar secretos, culpas arrastradas por accidentes del pasado y conflictos sentimentales, descubriendo que el perdón, la lealtad y el amor son clave para sanar sus heridas','productos/bJP8n6wZqmA6HijD0iKhd2zUBAxr8SsM3x7rJnwg.jpg',379.00,1,0,NULL,NULL,'2026-11-23 06:01:10','2026-11-23 06:39:34',NULL),
(144,5,'DeBÍ TiRAR MáS FOToS','Edición doble LP en vinilo blanco opaco con carpeta desplegable (gatefold). Sexto álbum de estudio en solitario de Bad Bunny, un homenaje a la música tradicional puertorriqueña que fusiona reguetón con salsa, plena, bomba y música jíbara.','productos/QH2qN8ZD2xujYUoEMlaCUNYkToV8XSVQpdDGbSyW.jpg',600.00,1,0,NULL,NULL,'2026-11-23 06:01:10','2026-11-23 06:39:34',NULL),
(145,5,'LUX','Prensado en vinilo transparente (Crystal Clear) que incluye póster desplegable. Cuarto álbum de estudio de Rosalía, grabado junto a la Orquesta Sinfónica de Londres y la Escolanía de Montserrat, con colaboraciones de Björk, Estrella Mor','productos/VP4Gn46dpjSrleB1gFaNglEGu93GkyI9HDA1pmnO.jpg',800.00,1,0,NULL,NULL,'2026-11-23 06:01:10','2026-11-23 06:39:34',NULL),
(146,5,'Deja','Edición doble LP en vinilo transparente. Álbum conceptual dividido en cuatro elementos (Agua, Aire, Tierra y Fuego), grabado en Santa Marta, Colombia, y mezclado por Damian Taylor. Fusiona percusión folclórica afrocolombian','productos/WqX1d2lcnJs5age66LvrEyQm3D7eoz1xrqux1RrT.jpg',550.00,1,0,NULL,NULL,'2026-11-23 06:01:10','2026-11-23 06:39:34',NULL),
(147,5,'Radio City Music Hall','Octavo álbum de estudio de la banda británica Pink Floyd y una obra cumbre del rock conceptual. Incluye las 10 pistas originales remasterizadas —con clásicos como \"Time\", \"Money\" y \"Us and Them\"','productos/zKFBnBudGTxAYJGNOcV5r5jBFQQG3KFmo33OThBh.jpg',1097.00,1,0,NULL,NULL,'2026-11-23 06:01:10','2026-11-23 06:39:35',NULL),
(148,5,'The Bodyguard: Original Soundtrack Album.','Descripción del producto: Banda sonora oficial de la película El guardaespaldas, protagonizada por Whitney Houston y Kevin Costner. Contiene 12 o 13 pistas (según la edición) divididas en una primera mitad interpretada por Houston','productos/Q6L3WnKI7ANA97CbCeSskGNFQtJZZAXd1duc230A.jpg',699.00,1,0,NULL,NULL,'2026-11-23 06:01:10','2026-11-23 06:39:35',NULL),
(149,6,'Guitarra Eléctrica Autografiada por Claudio Sánchez','Pieza única de colección (1/1). Guitarra eléctrica firmada de puño y letra sobre el golpeador por Claudio Sánchez, vocalista y guitarrista de Coheed and Cambria.','productos/plmvqDqWdaonmP4pvCcQKSMShw7H2C9J9gwucizs.jpg',11494.00,1,0,NULL,NULL,'2026-11-23 06:01:10','2026-11-23 06:39:35',NULL),
(150,6,'Busto de colección Star Trek','Busto oficial de colección fabricado por Sideshow Collectibles en resina Polystone maciza con acabados pintados a mano. Edición limitada mundialmente a 3,000 piezas numeradas en la base','productos/hTG48Fhmq6xPvzcb7CAI0W16xTvh1tru3VRmVniT.jpg',3065.00,1,0,NULL,NULL,'2026-11-23 06:01:10','2026-11-23 06:39:35',NULL),
(151,4,'Los secretos de YouTube','Es una guía práctica y reflexiva sobre la realidad de dedicarse a crear contenido en internet. TheGrefg responde dudas frecuentes sobre cómo funciona la plataforma, cómo monetizar un canal y qué diferencia hay entre hacerlo por hobby o como trabajo.','productos/0QCI76yUzRyTebI6k8JkqZfAhvRE1Wu06xBaPkuE.jpg',149.00,1,0,NULL,NULL,'2026-11-23 06:09:54','2026-11-23 06:39:34',NULL),
(152,4,'Cómo ser youtuber','Este libro mezcla una historia biográfica con consejos para quienes quieren empezar en redes sociales. Exi relata cómo pasó de ser un adolescente sin motivación en los estudios a convertirse en un creador de contenido reconocido en España y Latinoamérica','productos/eYHeYy3Fgl2TSMLSPkz6QpWRG9ltOtsi83r3BB5R.jpg',189.00,1,0,NULL,NULL,'2026-11-23 06:09:54','2026-11-23 06:39:34',NULL),
(153,4,'Auronplay el libro','Con su característico estilo directo, sarcástico y sin filtros, repasa su vida desde su infancia en Badalona, su paso por la escuela, sus primeras amistades y fiestas, hasta sus inicios en internet','productos/vHKpgYflGJSIj0WAnRHtKR7YWsMGX92LxWe7nfou.jpg',138.00,1,0,NULL,NULL,'2026-11-23 06:09:54','2026-11-23 06:39:34',NULL),
(154,5,'Back in black','Séptimo álbum de estudio de la banda australiana AC/DC y el primero grabado con el vocalista Brian Johnson tras el fallecimiento de Bon Scott. Producido por Robert John «Mutt» Lange, este disco emblemático de portada completamente negra en señal de luto incluye 10 clásicos imprescindibles del rock','productos/59ku0yscQyCH9x4N0Vdn8q1gpaf5imKSHz2g38Wz.jpg',938.00,1,0,NULL,NULL,'2026-11-23 06:09:54','2026-11-23 06:39:34',NULL),
(162,6,'Micky Mantle','Considerada el \"Santo Grial\" del coleccionismo deportivo de posguerra. Pertenece a la última tirada de 1952 cuya mercancía no vendida fue arrojada al océano Atlántico por la propia compañía, lo que provocó una escasez extrema.','productos/9x8MOv2UAXphugNEuTxIaW70fAAsbMqhIeQUAvsr.jpg',12600000.00,1,0,NULL,NULL,'2026-11-23 06:18:55','2026-11-23 06:39:35',NULL);
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promociones`
--

DROP TABLE IF EXISTS `promociones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `promociones` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `producto_id` bigint(20) unsigned DEFAULT NULL,
  `nombre` varchar(120) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo_descuento` enum('porcentaje','monto') NOT NULL DEFAULT 'porcentaje',
  `valor` decimal(10,2) NOT NULL,
  `es_preventa_exclusiva` tinyint(1) NOT NULL DEFAULT 1,
  `preventa_inicia_en` timestamp NULL DEFAULT NULL,
  `apertura_publica_en` timestamp NULL DEFAULT NULL,
  `badge_etiqueta` varchar(120) DEFAULT NULL,
  `inicia_en` date NOT NULL,
  `termina_en` date DEFAULT NULL,
  `activa` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `promociones_producto_id_foreign` (`producto_id`),
  CONSTRAINT `promociones_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promociones`
--

LOCK TABLES `promociones` WRITE;
/*!40000 ALTER TABLE `promociones` DISABLE KEYS */;
INSERT INTO `promociones` VALUES
(4,116,'Preventa Exclusiva VIP: Programa Inaugural Palacio de Bellas Artes (Edición Especial)','Acceso anticipado exclusivo para Clientes Frecuentes (5+ compras) antes de la apertura oficial al público general. Incluye carpeta museográfica conmemorativa.','porcentaje',15.00,1,'2026-11-20 04:15:57','2026-11-24 04:15:57','PREVENTA ACTIVA · ACCESO ANTICIPADO VIP','2026-11-20','2026-12-02',1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(5,94,'Apertura Oficial: A Love Supreme (Lanzamiento Audiófilo 180g)','Promoción desbloqueada en Fase de Apertura para todos los clientes comunes de la tienda. Prensado audiófilo con carpeta desplegable laminada.','porcentaje',12.00,1,'2026-11-17 04:15:57','2026-11-21 04:15:57','EDICIÓN ESPECIAL · APERTURA PÚBLICA','2026-11-17','2026-11-27',1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(6,97,'Edición Especial: Led Zeppelin IV (Prensado Audiófilo 180g)','Lanzamiento especial en Fase de Preventa Anticipada para Clientes Frecuentes VIP (5+ compras). Masterizado desde las cintas analógicas originales.','monto',900.00,1,'2026-11-21 04:15:57','2026-11-25 04:15:57','EDICIÓN ESPECIAL · PREVENTA VIP','2026-11-21','2026-12-04',1,'2026-11-22 04:15:57','2026-11-22 04:15:57');
/*!40000 ALTER TABLE `promociones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `proveedores`
--

DROP TABLE IF EXISTS `proveedores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proveedores` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `razon_social` varchar(255) DEFAULT NULL,
  `rfc` varchar(20) DEFAULT NULL,
  `contacto_nombre` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `proveedores_rfc_unique` (`rfc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proveedores`
--

LOCK TABLES `proveedores` WRITE;
/*!40000 ALTER TABLE `proveedores` DISABLE KEYS */;
/*!40000 ALTER TABLE `proveedores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recuperacion_contrasenas`
--

DROP TABLE IF EXISTS `recuperacion_contrasenas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recuperacion_contrasenas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` bigint(20) unsigned NOT NULL,
  `token_hash` varchar(255) NOT NULL,
  `expira_en` timestamp NOT NULL,
  `usado_en` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `recuperacion_contrasenas_token_hash_unique` (`token_hash`),
  KEY `recuperacion_contrasenas_usuario_id_foreign` (`usuario_id`),
  CONSTRAINT `recuperacion_contrasenas_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recuperacion_contrasenas`
--

LOCK TABLES `recuperacion_contrasenas` WRITE;
/*!40000 ALTER TABLE `recuperacion_contrasenas` DISABLE KEYS */;
/*!40000 ALTER TABLE `recuperacion_contrasenas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservas_stock`
--

DROP TABLE IF EXISTS `reservas_stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reservas_stock` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `existencia_id` bigint(20) unsigned NOT NULL,
  `pedido_id` bigint(20) unsigned DEFAULT NULL,
  `carrito_id` bigint(20) unsigned DEFAULT NULL,
  `cantidad` int(10) unsigned NOT NULL,
  `expira_en` timestamp NULL DEFAULT NULL,
  `estado` enum('activa','confirmada','liberada','expirada') NOT NULL DEFAULT 'activa',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reservas_stock_existencia_id_foreign` (`existencia_id`),
  KEY `reservas_stock_pedido_id_foreign` (`pedido_id`),
  KEY `reservas_stock_carrito_id_foreign` (`carrito_id`),
  CONSTRAINT `reservas_stock_carrito_id_foreign` FOREIGN KEY (`carrito_id`) REFERENCES `carritos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reservas_stock_existencia_id_foreign` FOREIGN KEY (`existencia_id`) REFERENCES `existencias` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reservas_stock_pedido_id_foreign` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservas_stock`
--

LOCK TABLES `reservas_stock` WRITE;
/*!40000 ALTER TABLE `reservas_stock` DISABLE KEYS */;
INSERT INTO `reservas_stock` VALUES
(1,157,NULL,5,1,'2026-11-23 06:25:40','expirada','2026-11-23 06:20:40','2026-11-23 06:39:53');
/*!40000 ALTER TABLE `reservas_stock` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES
(6,'Administrador',NULL,'2026-11-23 05:40:06','2026-11-23 05:40:06'),
(7,'Compras',NULL,'2026-11-23 05:40:06','2026-11-23 05:40:06'),
(8,'Ventas',NULL,'2026-11-23 05:40:06','2026-11-23 05:40:06'),
(9,'Almacen',NULL,'2026-11-23 05:40:06','2026-11-23 05:40:06'),
(10,'cliente','Cliente de la tienda y catálogo público','2026-11-23 06:20:32','2026-11-23 06:20:32');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES
('ixm6ASBXHlHBQT6lQUE3vj6evVrkrlCD5VraXGTB',13,'172.19.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/158.0.0.0 Safari/537.36 Edg/158.0.0.0','eyJfdG9rZW4iOiJVcDRJbVd2dXZVZjRvSmo1eExxU3lqNVJPWGVlcDh4ZmU2enhrVG5DIiwiYXJjaGl2dW1fdGFiX3Nlc3Npb25zIjp7InRhYl9td3NsMmV6a182cG5xcHd4ZHFxOG03NWxlYjM5ayI6eyJ1c2VyX2lkIjoxMSwiY2Fycml0b19hcmNoaXZ1bSI6W10sImNhcnJpdG9fcHJlY2lvc19jb25nZWxhZG9zIjpbXSwiY2Fycml0b19yZXNlcnZhc19pZHMiOltdLCJjYXJyaXRvX2V4cGlyYV9lbiI6bnVsbCwiYXV0aGVudGljYXRlZF9hdCI6IjIwMjYtMTEtMjNUMDU6Mzk6MzArMDA6MDAiLCJ1cGRhdGVkX2F0IjoiMjAyNi0xMS0yM1QwNjozOTozNSswMDowMCJ9LCJ0YWJfbXdzdW0xeW5fZHZjamVvMHg3ZHdxcTdvajZreTAiOnsidXNlcl9pZCI6MTMsImNhcnJpdG9fYXJjaGl2dW0iOltdLCJjYXJyaXRvX3ByZWNpb3NfY29uZ2VsYWRvcyI6W10sImNhcnJpdG9fcmVzZXJ2YXNfaWRzIjpbXSwiY2Fycml0b19leHBpcmFfZW4iOm51bGwsImF1dGhlbnRpY2F0ZWRfYXQiOiIyMDI2LTExLTIzVDA2OjIwOjMzKzAwOjAwIiwidXBkYXRlZF9hdCI6IjIwMjYtMTEtMjNUMDY6NDA6MDYrMDA6MDAifX0sIl9wcmV2aW91cyI6eyJ1cmwiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvY2F0YWxvZ29cL3Zpbmlsb3M/X3RhYj10YWJfbXdzdW0xeW5fZHZjamVvMHg3ZHdxcTdvajZreTAiLCJyb3V0ZSI6InB1YmxpYy52aW5pbG9zIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1795416006),
('yZXYi6qYkE8iFvYxiZdJFMbL24OS4HtIucjEaswV',13,'172.19.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/158.0.0.0 Safari/537.36 Edg/158.0.0.0','eyJfdG9rZW4iOiJXaTB3TUdRRHpCMVpuSmNuelN3Tk43dmFxcWpvVTFNYnpMU3NJNkI2IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC9jYXRhbG9nb1wvbGlicm9zP190YWI9dGFiX213c3VtMXluX2R2Y2plbzB4N2R3cXE3b2o2a3kwIiwicm91dGUiOiJwdWJsaWMubGlicm9zIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwicGFzc3dvcmRfaGFzaF93ZWIiOiI0MzQyZDhkMDE0MDM1M2YxYzAwY2NkMzY5NDRiNmI5ZGRlZTI5NjEzOTAyZWFlMWM1YTdmYmIzYzRmYWE2NTA1IiwiYXJjaGl2dW1fdGFiX3Nlc3Npb25zIjp7InRhYl9td3NsMmV6a182cG5xcHd4ZHFxOG03NWxlYjM5ayI6eyJ1c2VyX2lkIjoxMSwiY2Fycml0b19hcmNoaXZ1bSI6W10sImNhcnJpdG9fcHJlY2lvc19jb25nZWxhZG9zIjpbXSwiY2Fycml0b19yZXNlcnZhc19pZHMiOltdLCJjYXJyaXRvX2V4cGlyYV9lbiI6bnVsbCwiYXV0aGVudGljYXRlZF9hdCI6IjIwMjYtMTEtMjNUMDU6Mzk6MzArMDA6MDAiLCJ1cGRhdGVkX2F0IjoiMjAyNi0xMS0yM1QwNjoxODo1NSswMDowMCJ9LCJ0YWJfbXdzdW0xeW5fZHZjamVvMHg3ZHdxcTdvajZreTAiOnsidXNlcl9pZCI6MTMsImNhcnJpdG9fYXJjaGl2dW0iOnsiOTMiOjF9LCJjYXJyaXRvX3ByZWNpb3NfY29uZ2VsYWRvcyI6W10sImNhcnJpdG9fcmVzZXJ2YXNfaWRzIjpbMV0sImNhcnJpdG9fZXhwaXJhX2VuIjoiMjAyNi0xMS0yM1QwNjoyNTo0MCswMDowMCIsImF1dGhlbnRpY2F0ZWRfYXQiOiIyMDI2LTExLTIzVDA2OjIwOjMzKzAwOjAwIiwidXBkYXRlZF9hdCI6IjIwMjYtMTEtMjNUMDY6MjE6MjkrMDA6MDAifX0sImFyY2hpdnVtX2p1c3RfbG9nZ2VkX2luX3RhYl90b2tlbiI6InRhYl9td3N1bTF5bl9kdmNqZW8weDdkd3FxN29qNmt5MCJ9',1795414889);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sucursales`
--

DROP TABLE IF EXISTS `sucursales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sucursales` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(120) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `activa` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sucursales_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sucursales`
--

LOCK TABLES `sucursales` WRITE;
/*!40000 ALTER TABLE `sucursales` DISABLE KEYS */;
INSERT INTO `sucursales` VALUES
(4,'Sucursal Central Bellas Artes','Av. Juárez 18, Centro Histórico, Cuauhtémoc, CDMX','5555128900',1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(5,'Sucursal Coyoacán','Jardín Centenario 14, Villa Coyoacán, CDMX','5556584420',1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(6,'Almacén General','Calz. Vallejo 1020, Industrial Vallejo, Azcapotzalco, CDMX','5553689100',1,'2026-11-22 04:15:56','2026-11-22 04:15:56');
/*!40000 ALTER TABLE `sucursales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario_rol`
--

DROP TABLE IF EXISTS `usuario_rol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuario_rol` (
  `usuario_id` bigint(20) unsigned NOT NULL,
  `rol_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`usuario_id`,`rol_id`),
  KEY `usuario_rol_rol_id_foreign` (`rol_id`),
  CONSTRAINT `usuario_rol_rol_id_foreign` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `usuario_rol_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario_rol`
--

LOCK TABLES `usuario_rol` WRITE;
/*!40000 ALTER TABLE `usuario_rol` DISABLE KEYS */;
INSERT INTO `usuario_rol` VALUES
(11,6,'2026-11-23 05:40:06','2026-11-23 05:40:06'),
(12,7,'2026-11-23 05:40:06','2026-11-23 05:40:06'),
(13,10,'2026-11-23 06:20:32','2026-11-23 06:20:32');
/*!40000 ALTER TABLE `usuario_rol` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(120) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `email_verificado_en` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuarios_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES
(11,'Administrador ARCHIVUM','admin@archivum.com','$2y$12$aAnQDnf4WmEI8jfVn2pXH.ENWS5fZbTglF4Ld5K6jtOs66XEQAz42',1,NULL,NULL,'2026-11-23 03:23:24','2026-11-23 05:39:01',NULL),
(12,'Encargado Compras','compras1@archivum.com','$2y$12$lDRW5iAswFmut6HVcJ11wuRfAJlx6NVXuxz8CmBVlURGHIbAs45Ru',1,NULL,NULL,'2026-11-23 05:39:14','2026-11-23 05:51:15',NULL),
(13,'Evelyn','quijadaevelin82@gmail.com','$2y$12$0V0gcOlVkYw1UFRo2sic6OiIb8Z/7Fz.uL/5W.mggWYK07SoniAuu',1,NULL,NULL,'2026-11-23 06:20:32','2026-11-23 06:20:32',NULL);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `variantes_sku`
--

DROP TABLE IF EXISTS `variantes_sku`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `variantes_sku` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `producto_id` bigint(20) unsigned NOT NULL,
  `sku` varchar(60) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `imagen` varchar(500) DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `activa` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `variantes_sku_sku_unique` (`sku`),
  KEY `variantes_sku_producto_id_foreign` (`producto_id`),
  CONSTRAINT `variantes_sku_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=163 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `variantes_sku`
--

LOCK TABLES `variantes_sku` WRITE;
/*!40000 ALTER TABLE `variantes_sku` DISABLE KEYS */;
INSERT INTO `variantes_sku` VALUES
(71,71,'LIB-1967-SUD','VERY FINE (VF) · PIEZA ÚNICA 1/1',NULL,32000.00,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(72,72,'LIB-1955-FCE','NEAR MINT (NM) · PIEZA ÚNICA 1/1 · ADQUISICIÓN SELECTA',NULL,44500.00,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(73,73,'LIB-1949-LOS','MINT (M) · PIEZA ÚNICA 1/1 · ADQUISICIÓN SELECTA',NULL,58000.00,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(74,74,'LIB-1950-FCE','VERY FINE (VF) · TIRAJE MÚLTIPLE',NULL,16900.00,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(75,75,'LIB-1962-ERA','NEAR MINT (NM) · TIRAJE REGULAR · TERROR GÓTICO',NULL,6800.00,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(76,76,'LIB-1963-SUD','MINT (M) · TIRAJE MÚLTIPLE',NULL,8400.00,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(77,77,'LIB-1957-FCE','MINT (M) · PRIMERA EDICIÓN · POESÍA',NULL,24500.00,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(78,78,'LIB-1959-UNAM','NEAR MINT (NM) · HISTORIA · BIBLIOTECA DEL ESTUDIANTE',NULL,9800.00,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(79,79,'LIB-1971-FCE','VERY FINE (VF) · CIENCIA & FILOSOFÍA',NULL,11200.00,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(80,80,'LIB-1845-POE','VERY FINE (VF) · TERROR GÓTICO · GRABADOS XILOGRÁFICOS',NULL,18500.00,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(81,81,'LIB-1955-MIN','MINT (M) · PIEZA ÚNICA 1/1 · CIENCIA FICCIÓN DISTÓPICA',NULL,26000.00,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(82,82,'LIB-1955-BUE','NEAR MINT (NM) · CIENCIA FICCIÓN CLÁSICA',NULL,21500.00,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(83,83,'LIB-1977-TOL','VERY FINE (VF) · FANTASÍA ÉPICA · MAPAS DESPLEGABLES',NULL,29800.00,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(84,84,'LIB-1982-LUM','NEAR MINT (NM) · MISTERIO MONÁSTICO & THRILLER',NULL,14200.00,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(85,85,'LIB-1928-JUV','VERY FINE (VF) · POLICÍACO CLÁSICO SHERLOCK HOLMES',NULL,17800.00,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(86,86,'LIB-1932-AGU','MINT (M) · FILOSOFÍA MODERNA · ENCUADERNACIÓN EN PIEL',NULL,19500.00,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(87,87,'LIB-1944-UNA','NEAR MINT (NM) · FILOSOFÍA CLÁSICA GRIEGA',NULL,13400.00,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(88,88,'LIB-1947-RAE','MINT (M) · CLÁSICOS UNIVERSALES · ESTUCHE DE TELA',NULL,38000.00,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(89,89,'LIB-1921-DIV','NEAR MINT (NM) · PIEZA ÚNICA 1/1 · GRAN FOLIO DORÉ',NULL,42000.00,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(90,90,'LIB-1974-NER','VERY FINE (VF) · MEMORIAS & BIOGRAFÍA · EDICIÓN PÓSTUMA',NULL,15600.00,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(91,91,'LIB-1960-POR','MINT (M) · CRÓNICA DE INDIAS · EDICIÓN CRÍTICA',NULL,8900.00,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(92,92,'VIN-1959-COL','MINT (M) · PIEZA ÚNICA 1/1 · ADQUISICIÓN SELECTA',NULL,18500.00,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(93,93,'VIN-1957-BN1577','NEAR MINT (NM) · PIEZA ÚNICA 1/1 · ADQUISICIÓN SELECTA',NULL,27500.00,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(94,94,'VIN-1965-IMP77','VERY FINE (VF) · VINILO LP 180G',NULL,16200.00,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(95,95,'VIN-1969-PCS7088','MINT (M) · PIEZA ÚNICA 1/1 · ADQUISICIÓN SELECTA',NULL,22900.00,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(96,96,'CD-1983-CP35','MINT (M) · CD COMPACT DISC · ADQUISICIÓN SELECTA',NULL,14800.00,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(97,97,'VIN-1971-LED4','MINT (M) · VINILO LP 180G',NULL,5900.00,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(98,98,'CD-1997-RAD1','MINT (M) · CD COMPACT DISC',NULL,4800.00,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(99,99,'VIN-2017-GOT','MINT (M) · VINILO LP',NULL,521.50,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(100,100,'VIN-1970-YAK','VERY FINE (VF) · VINILO LP',NULL,372.56,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(101,101,'VIN-1990-CAI','NEAR MINT (NM) · PIEZA ÚNICA 1/1',NULL,7413.93,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(102,102,'VIN-2007-MAG','MINT (M) · VINILO LP',NULL,1566.55,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(103,103,'VIN-2024-MAG','MINT (M) · VINILO LP',NULL,864.00,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(104,104,'CD-2005-ROM','VERY FINE (VF) · BOX SET CD',NULL,179.00,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(105,105,'CD-2012-ARR','VERY FINE (VF) · CD COMPACT DISC',NULL,183.00,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(106,106,'CD-1997-JUA','VERY FINE (VF) · CD COMPACT DISC',NULL,129.00,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(107,107,'CD-2010-SON','MINT (M) · CD COMPACT DISC',NULL,391.50,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(108,108,'CD-2000-HUA','VERY FINE (VF) · CD COMPACT DISC',NULL,69.00,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(109,109,'VIN-2022-MID','MINT (M) · PIEZA ÚNICA 1/1',NULL,641.68,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(110,110,'CD-1997-BVSC','MINT (M) · CD COMPACT DISC',NULL,3950.00,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(111,111,'MEM-1928-AUT','COA NOTARIADO · PIEZA ÚNICA 1/1',NULL,9400.00,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(112,112,'MEM-1940-COY','MINT (M) · PIEZA ÚNICA 1/1 · ADQUISICIÓN SELECTA',NULL,36800.00,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(113,113,'MEM-1910-ALM','MINT (M) · PIEZA ÚNICA 1/1 · ADQUISICIÓN SELECTA',NULL,29500.00,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(114,114,'MEM-1934-FOT','MINT (M) · COA NOTARIADO · ADQUISICIÓN SELECTA',NULL,41000.00,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(115,115,'MEM-1912-PAR','VERY FINE (VF) · ADQUISICIÓN SELECTA',NULL,21500.00,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(116,116,'MEM-1934-PBA','VERY FINE (VF) · TIRAJE MÚLTIPLE',NULL,7800.00,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(117,117,'MEM-1954-COY','NEAR MINT (NM) · TIRAJE MÚLTIPLE',NULL,5400.00,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(118,118,'MEM-1968-IMP','MINT (M) · ACCESORIO DE BÓVEDA',NULL,3200.00,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(119,119,'COL-1997-EVA1','MINT (M) · FIGURA ANIME · EDICIÓN LIMITADA',NULL,18900.00,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(120,120,'COL-1933-DOU','MINT (M) · MONEDA HISTÓRICA RARA',NULL,18872250.00,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(121,121,'COL-1794-FLO','VERY FINE (VF) · PRIMER DÓLAR FEDERAL',NULL,175000.00,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(122,122,'COL-1344-EDU','NEAR MINT (NM) · PIEZA ÚNICA MEDIEVAL',NULL,7000000.00,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(123,123,'COL-0723-DIN','MINT (M) · CALIFATO OMEYA',NULL,95600000.00,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(124,124,'COL-1800-REA','VERY FINE (VF) · COLONIAL',NULL,5500.00,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(125,125,'COL-1947-CEN','MINT (M) · BULLION MEXICANO',NULL,92250.00,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(126,126,'COL-2020-LIB','MINT (M) · ONZA PLATA PURA',NULL,1250.00,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(127,127,'COL-2023-20P','VERY FINE (VF) · COMMEMORATIVE',NULL,100.00,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(128,128,'COL-1975-BIL','FINE (F) · NOTAFILIA HISTÓRICA',NULL,325.00,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(129,129,'COL-2023-POP','MINT (M) · DESIGNER ART TOY',NULL,1100.00,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(130,130,'COL-2022-FUN','MINT (M) · VINYL FIGURE',NULL,1075.00,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(131,131,'COL-2021-LEG','MINT (M) · MASTER BUILDER SET',NULL,19999.00,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(132,132,'COL-2023-ANU','MINT (M) · FIGURA ORNAMENTAL',NULL,773.08,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(133,133,'COL-1992-COC','VERY FINE (VF) · ARTE POPULAR INDÍGENA',NULL,4914.54,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(134,134,'COL-2023-JOY','MINT (M) · ARTESANÍA DECORATIVA',NULL,600.49,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(135,135,'COL-1993-DBZG','MINT (M) · CULTURA POP & ANIME · EN CAJA ORIGINAL',NULL,14200.00,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(136,136,'COL-1965-ROB','VERY FINE (VF) · JUGUETE MECÁNICO VINTAGE',NULL,21800.00,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(137,137,'COL-1914-BIL','MINT (M) · NUMISMÁTICA & NOTAFILIA REVOLUCIONARIA',NULL,8900.00,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(138,138,'COL-1768-COL8','VERY FINE (VF) · MONEDAS & BILLETES HISTÓRICOS',NULL,38500.00,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(139,139,'COL-1980-NIN','NEAR MINT (NM) · VIDEOJUEGOS RETRO EN CAJA',NULL,26900.00,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(140,140,'COL-1987-ZEL','MINT (M) · CARTUCHO DORADO DE COLECCIÓN · VIDEOJUEGOS',NULL,34000.00,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(141,141,'LIB-2026-003','MINT (M) · Cancelada encarcelada','productos/z3HOwFAzBCfqVSpc2bwJgNsMIOgEHW6MLMJvgpP9.jpg',249.00,1,'2026-11-23 06:01:10','2026-11-23 06:39:34'),
(142,142,'LIB-2026-005','MINT (M) · Así es la puta vida','productos/mXsakZMRdos8e3XhJrGNMrnfTTFiGGbpbXI5FMds.jpg',150.00,1,'2026-11-23 06:01:10','2026-11-23 06:39:34'),
(143,143,'LIB-2026-007','MINT (M) · Tres Promesas','productos/bJP8n6wZqmA6HijD0iKhd2zUBAxr8SsM3x7rJnwg.jpg',379.00,1,'2026-11-23 06:01:10','2026-11-23 06:39:34'),
(144,144,'DIS-2026-002','MINT(M) · DeBÍ TiRAR MáS FOToS','productos/QH2qN8ZD2xujYUoEMlaCUNYkToV8XSVQpdDGbSyW.jpg',600.00,1,'2026-11-23 06:01:10','2026-11-23 06:39:34'),
(145,145,'DIS-2026-003','MINT(M) · LUX','productos/VP4Gn46dpjSrleB1gFaNglEGu93GkyI9HDA1pmnO.jpg',800.00,1,'2026-11-23 06:01:10','2026-11-23 06:39:34'),
(146,146,'DIS-2026-004','MINT(M) · Deja','productos/WqX1d2lcnJs5age66LvrEyQm3D7eoz1xrqux1RrT.jpg',550.00,1,'2026-11-23 06:01:10','2026-11-23 06:39:34'),
(147,147,'DIS-2026-006','MINT(M) · PIEZA ÚNICA 1/1 · Radio City Music Hall','productos/zKFBnBudGTxAYJGNOcV5r5jBFQQG3KFmo33OThBh.jpg',1097.00,1,'2026-11-23 06:01:10','2026-11-23 06:39:35'),
(148,148,'DIS-2026-007','MINT(M) · The Bodyguard: Original Soundtrack Album.','productos/Q6L3WnKI7ANA97CbCeSskGNFQtJZZAXd1duc230A.jpg',699.00,1,'2026-11-23 06:01:10','2026-11-23 06:39:35'),
(149,149,'COL-2036.002','MINT (M) · PIEZA ÚNICA 1/1 · Guitarra Eléctrica Autografiada por Claudio Sánchez','productos/plmvqDqWdaonmP4pvCcQKSMShw7H2C9J9gwucizs.jpg',11494.00,1,'2026-11-23 06:01:10','2026-11-23 06:39:35'),
(150,150,'COL-2036.003','MINT (M) · PIEZA ÚNICA 1/1 · Busto de colección Star Trek','productos/hTG48Fhmq6xPvzcb7CAI0W16xTvh1tru3VRmVniT.jpg',3065.00,1,'2026-11-23 06:01:10','2026-11-23 06:39:35'),
(151,151,'LIB-2026-002','MINT (M) · Los secretos de YouTube','productos/0QCI76yUzRyTebI6k8JkqZfAhvRE1Wu06xBaPkuE.jpg',149.00,1,'2026-11-23 06:09:54','2026-11-23 06:39:34'),
(152,152,'LIB-2026-004','MINT (M) · Cómo ser youtuber','productos/eYHeYy3Fgl2TSMLSPkz6QpWRG9ltOtsi83r3BB5R.jpg',189.00,1,'2026-11-23 06:09:54','2026-11-23 06:39:34'),
(153,153,'LIB-2026-006','MINT (M) · Auronplay el libro','productos/vHKpgYflGJSIj0WAnRHtKR7YWsMGX92LxWe7nfou.jpg',138.00,1,'2026-11-23 06:09:54','2026-11-23 06:39:34'),
(154,154,'DIS-2026-005','MINT(M) · PIEZA ÚNICA 1/1 · Back in black','productos/59ku0yscQyCH9x4N0Vdn8q1gpaf5imKSHz2g38Wz.jpg',938.00,1,'2026-11-23 06:09:54','2026-11-23 06:39:34'),
(162,162,'COL-2076.010','MINT (M) · PIEZA ÚNICA 1/1 · Micky Mantle','productos/9x8MOv2UAXphugNEuTxIaW70fAAsbMqhIeQUAvsr.jpg',12600000.00,1,'2026-11-23 06:18:55','2026-11-23 06:39:35');
/*!40000 ALTER TABLE `variantes_sku` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-23  6:41:15
