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
) ENGINE=InnoDB AUTO_INCREMENT=110 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
(14,11,'IMPORTACION_MASIVA_PRODUCTOS','productos',NULL,'{\"archivo_datos\":\"plantilla_productos_archivum.xlsx\",\"archivo_zip\":\"imagenes.zip\",\"creados\":0,\"actualizados\":15,\"omitidos\":0,\"imagenes_extraidas\":15,\"por_pestana\":{\"Libros\":{\"creados\":0,\"actualizados\":6,\"errores\":0},\"Discos\":{\"creados\":0,\"actualizados\":6,\"errores\":0},\"Coleccionables\":{\"creados\":0,\"actualizados\":3,\"errores\":0}},\"usuario_nombre\":\"Administrador ARCHIVUM\"}','172.19.0.1','2026-11-23 06:39:35'),
(15,13,'LOGOUT','usuarios',13,'{\"ip\":\"172.19.0.1\"}','172.19.0.1','2026-11-23 06:41:22'),
(16,13,'SOLICITUD_RECUPERACION_CONTRASENA','recuperacion_contrasenas',13,'{\"email\":\"quijadaevelin82@gmail.com\",\"ip\":\"172.19.0.1\",\"expira_en\":\"2026-11-23 07:41:38\"}','172.19.0.1','2026-11-23 06:41:38'),
(17,13,'LOGIN_EXITOSO','usuarios',13,'{\"email\":\"quijadaevelin82@gmail.com\",\"roles\":[\"cliente\"],\"tab_session_id\":\"tab_mwsum1yn_dvcjeo0x7dwqq7oj6ky0\"}','172.19.0.1','2026-11-23 06:41:58'),
(18,11,'ACTUALIZACION_IMAGEN_PRODUCTO','productos',140,'{\"nombre\":\"Cartucho The Legend of Zelda (Gold Cartridge NES 1987 Sellado)\",\"nueva_imagen\":\"productos\\/lMStJFZVoRaFgiGxdH3QPglfHsNjnBzPESNRtTbh.avif\",\"usuario_nombre\":\"Administrador ARCHIVUM\"}','172.19.0.1','2026-11-23 06:44:59'),
(19,11,'ACTUALIZACION_IMAGEN_PRODUCTO','productos',139,'{\"nombre\":\"Consola Nintendo Game & Watch Ball (Primera Edici\\u00f3n Jap\\u00f3n 1980)\",\"nueva_imagen\":\"productos\\/Ov8dBeHpr9qzg7jBmxx6rzvIFBVrcyrIfVdQyYNz.webp\",\"usuario_nombre\":\"Administrador ARCHIVUM\"}','172.19.0.1','2026-11-23 06:45:14'),
(20,11,'BAJA_PRODUCTO','productos',138,'{\"nombre\":\"Moneda Virreinal 8 Reales Columnario de Plata (1768)\",\"estado_anterior\":\"activo\",\"nuevo_estado\":\"baja\",\"usuario_nombre\":\"Administrador ARCHIVUM\",\"usuario_rol\":[\"Administrador\"]}','172.19.0.1','2026-11-23 06:45:21'),
(21,11,'ACTUALIZACION_IMAGEN_PRODUCTO','productos',137,'{\"nombre\":\"Billete Revolucionario 10 Pesos Ej\\u00e9rcito Constitucionalista (1914)\",\"nueva_imagen\":\"productos\\/paiPRw6iKSvFPyK4TkoNDxvKiIMdvW2cGgNfRCgz.jpg\",\"usuario_nombre\":\"Administrador ARCHIVUM\"}','172.19.0.1','2026-11-23 06:45:33'),
(22,11,'ACTUALIZAR_PRECIO_PRODUCTO','productos',137,'{\"nombre\":\"Billete Revolucionario 10 Pesos Ej\\u00e9rcito Constitucionalista (1914)\",\"precio_anterior\":8900,\"precio_nuevo\":500,\"usuario_nombre\":\"Administrador ARCHIVUM\",\"usuario_rol\":[\"Administrador\"]}','172.19.0.1','2026-11-23 06:45:40'),
(23,11,'ACTUALIZAR_PRECIO_PRODUCTO','productos',140,'{\"nombre\":\"Cartucho The Legend of Zelda (Gold Cartridge NES 1987 Sellado)\",\"precio_anterior\":34000,\"precio_nuevo\":2500,\"usuario_nombre\":\"Administrador ARCHIVUM\",\"usuario_rol\":[\"Administrador\"]}','172.19.0.1','2026-11-23 06:45:46'),
(24,11,'ACTUALIZAR_PRECIO_PRODUCTO','productos',139,'{\"nombre\":\"Consola Nintendo Game & Watch Ball (Primera Edici\\u00f3n Jap\\u00f3n 1980)\",\"precio_anterior\":26900,\"precio_nuevo\":2500,\"usuario_nombre\":\"Administrador ARCHIVUM\",\"usuario_rol\":[\"Administrador\"]}','172.19.0.1','2026-11-23 06:45:57'),
(25,11,'BAJA_PRODUCTO','productos',136,'{\"nombre\":\"Robot Mec\\u00e1nico de Hojalata Space Trooper (Jap\\u00f3n Vintage 1965)\",\"estado_anterior\":\"activo\",\"nuevo_estado\":\"baja\",\"usuario_nombre\":\"Administrador ARCHIVUM\",\"usuario_rol\":[\"Administrador\"]}','172.19.0.1','2026-11-23 06:46:04'),
(26,11,'ACTUALIZACION_IMAGEN_PRODUCTO','productos',135,'{\"nombre\":\"Figura Dragon Ball Z Super Saiyan Goku (Banpresto Jap\\u00f3n 1993)\",\"nueva_imagen\":\"productos\\/pURPIuY5YUTPvNs5V0NuCOACVAXYA0rdg10gsJVB.jpg\",\"usuario_nombre\":\"Administrador ARCHIVUM\"}','172.19.0.1','2026-11-23 06:46:16'),
(27,11,'BAJA_PRODUCTO','productos',125,'{\"nombre\":\"Centenario de Oro (50 Pesos)\",\"estado_anterior\":\"activo\",\"nuevo_estado\":\"baja\",\"usuario_nombre\":\"Administrador ARCHIVUM\",\"usuario_rol\":[\"Administrador\"]}','172.19.0.1','2026-11-23 06:46:37'),
(28,11,'BAJA_PRODUCTO','productos',119,'{\"nombre\":\"Estatua Evangelion Test Type-01 (Edici\\u00f3n Limitada Kaiyodo 1997)\",\"estado_anterior\":\"activo\",\"nuevo_estado\":\"baja\",\"usuario_nombre\":\"Administrador ARCHIVUM\",\"usuario_rol\":[\"Administrador\"]}','172.19.0.1','2026-11-23 06:46:47'),
(29,11,'BAJA_PRODUCTO','productos',118,'{\"nombre\":\"Kit de Conservaci\\u00f3n Museogr\\u00e1fica & Estuche Anti\\u00e1cido\",\"estado_anterior\":\"activo\",\"nuevo_estado\":\"baja\",\"usuario_nombre\":\"Administrador ARCHIVUM\",\"usuario_rol\":[\"Administrador\"]}','172.19.0.1','2026-11-23 06:46:54'),
(30,11,'BAJA_PRODUCTO','productos',116,'{\"nombre\":\"Programa Inaugural Palacio de Bellas Artes (1934)\",\"estado_anterior\":\"activo\",\"nuevo_estado\":\"baja\",\"usuario_nombre\":\"Administrador ARCHIVUM\",\"usuario_rol\":[\"Administrador\"]}','172.19.0.1','2026-11-23 06:47:06'),
(31,11,'BAJA_PRODUCTO','productos',117,'{\"nombre\":\"Litograf\\u00eda Taller de Gr\\u00e1fica Popular Coyoac\\u00e1n (1954)\",\"estado_anterior\":\"activo\",\"nuevo_estado\":\"baja\",\"usuario_nombre\":\"Administrador ARCHIVUM\",\"usuario_rol\":[\"Administrador\"]}','172.19.0.1','2026-11-23 06:47:11'),
(32,11,'BAJA_PRODUCTO','productos',115,'{\"nombre\":\"Partitura Aut\\u00f3grafa de Estudio en Sol Menor (1912)\",\"estado_anterior\":\"activo\",\"nuevo_estado\":\"baja\",\"usuario_nombre\":\"Administrador ARCHIVUM\",\"usuario_rol\":[\"Administrador\"]}','172.19.0.1','2026-11-23 06:47:18'),
(33,11,'BAJA_PRODUCTO','productos',114,'{\"nombre\":\"Fotograf\\u00eda en Plata sobre Gelatina Firmada (1934)\",\"estado_anterior\":\"activo\",\"nuevo_estado\":\"baja\",\"usuario_nombre\":\"Administrador ARCHIVUM\",\"usuario_rol\":[\"Administrador\"]}','172.19.0.1','2026-11-23 06:47:22'),
(34,11,'BAJA_PRODUCTO','productos',113,'{\"nombre\":\"Medalla Conmemorativa del Centenario (1910)\",\"estado_anterior\":\"activo\",\"nuevo_estado\":\"baja\",\"usuario_nombre\":\"Administrador ARCHIVUM\",\"usuario_rol\":[\"Administrador\"]}','172.19.0.1','2026-11-23 06:47:25'),
(35,11,'BAJA_PRODUCTO','productos',112,'{\"nombre\":\"Manuscrito Aut\\u00f3grafo Casa Azul Coyoac\\u00e1n (1940)\",\"estado_anterior\":\"activo\",\"nuevo_estado\":\"baja\",\"usuario_nombre\":\"Administrador ARCHIVUM\",\"usuario_rol\":[\"Administrador\"]}','172.19.0.1','2026-11-23 06:47:31'),
(36,11,'BAJA_PRODUCTO','productos',111,'{\"nombre\":\"Carta Manuscrita Bellas Artes (1928)\",\"estado_anterior\":\"activo\",\"nuevo_estado\":\"baja\",\"usuario_nombre\":\"Administrador ARCHIVUM\",\"usuario_rol\":[\"Administrador\"]}','172.19.0.1','2026-11-23 06:47:36'),
(37,11,'BAJA_PRODUCTO','productos',110,'{\"nombre\":\"Buena Vista Social Club (Edici\\u00f3n CD Remasterizada World Circuit)\",\"estado_anterior\":\"activo\",\"nuevo_estado\":\"baja\",\"usuario_nombre\":\"Administrador ARCHIVUM\",\"usuario_rol\":[\"Administrador\"]}','172.19.0.1','2026-11-23 06:47:46'),
(38,11,'ACTUALIZACION_IMAGEN_PRODUCTO','productos',98,'{\"nombre\":\"OK Computer (Collector\'s Edition 2-CD Parlophone)\",\"nueva_imagen\":\"productos\\/Q456A28SU5bldfSxRvIVCataUpdvgzyxOcZnpPhn.webp\",\"usuario_nombre\":\"Administrador ARCHIVUM\"}','172.19.0.1','2026-11-23 06:48:15'),
(39,11,'ACTUALIZACION_IMAGEN_PRODUCTO','productos',83,'{\"nombre\":\"El Se\\u00f1or de los Anillos: La Comunidad del Anillo (1\\u00aa Edici\\u00f3n Minotauro 1977)\",\"nueva_imagen\":\"productos\\/Yp21v7nB63v6WboOV3gMWawFicNu2LvUSVwstArn.jpg\",\"usuario_nombre\":\"Administrador ARCHIVUM\"}','172.19.0.1','2026-11-23 06:48:44'),
(40,11,'ACTUALIZACION_IMAGEN_PRODUCTO','productos',84,'{\"nombre\":\"El Nombre de la Rosa (1\\u00aa Edici\\u00f3n en Castellano Lumen 1982)\",\"nueva_imagen\":\"productos\\/X4kWplGRxzkWUJRimbhbzdRoKhLkCuFO6MKjm3Ja.jpg\",\"usuario_nombre\":\"Administrador ARCHIVUM\"}','172.19.0.1','2026-11-23 06:48:59'),
(41,11,'ACTUALIZACION_IMAGEN_PRODUCTO','productos',85,'{\"nombre\":\"Estudio en Escarlata & El Sabueso de los Baskerville (Edici\\u00f3n Anotada 1928)\",\"nueva_imagen\":\"productos\\/w4o5WSAutGgYS2vUk76XEpDfmhmw7q6VP7ApUm9R.webp\",\"usuario_nombre\":\"Administrador ARCHIVUM\"}','172.19.0.1','2026-11-23 06:49:21'),
(42,11,'ACTUALIZACION_IMAGEN_PRODUCTO','productos',86,'{\"nombre\":\"As\\u00ed Habl\\u00f3 Zaratustra: Un Libro para Todos y para Nadie (Edici\\u00f3n Aguilar 1932)\",\"nueva_imagen\":\"productos\\/w3PmtPgXy1u6PJEWZiZEP1nHRm9i4COxdwSt2aYz.jpg\",\"usuario_nombre\":\"Administrador ARCHIVUM\"}','172.19.0.1','2026-11-23 06:49:37'),
(43,11,'ACTUALIZACION_IMAGEN_PRODUCTO','productos',87,'{\"nombre\":\"El Banquete y Fed\\u00f3n (Traducci\\u00f3n Cl\\u00e1sica Bibliotheca Scriptorum 1944)\",\"nueva_imagen\":\"productos\\/zeytArWzUEWrUhsrWamTQN6XnDoEdGvcF0ib5c2T.jpg\",\"usuario_nombre\":\"Administrador ARCHIVUM\"}','172.19.0.1','2026-11-23 06:49:52'),
(44,11,'ACTUALIZACION_IMAGEN_PRODUCTO','productos',88,'{\"nombre\":\"Don Quijote de la Mancha (Edici\\u00f3n Conmemorativa Facsimilar RAE 1947)\",\"nueva_imagen\":\"productos\\/dgIYo3PHjBqj7MFrwT0MfScUdOzwhYJfXeS6yHN5.jpg\",\"usuario_nombre\":\"Administrador ARCHIVUM\"}','172.19.0.1','2026-11-23 06:50:05'),
(45,11,'ACTUALIZACION_IMAGEN_PRODUCTO','productos',89,'{\"nombre\":\"La Divina Comedia (Edici\\u00f3n Biling\\u00fce con Grabados de Gustave Dor\\u00e9 1921)\",\"nueva_imagen\":\"productos\\/RoHPqdVCnxcwTtWnv4jkG8HHqvJ1XLqrSnNG9eSM.webp\",\"usuario_nombre\":\"Administrador ARCHIVUM\"}','172.19.0.1','2026-11-23 06:50:27'),
(46,11,'BAJA_PRODUCTO','productos',90,'{\"nombre\":\"Confieso que he Vivido: Memorias (Primera Edici\\u00f3n Seix Barral 1974)\",\"estado_anterior\":\"activo\",\"nuevo_estado\":\"baja\",\"usuario_nombre\":\"Administrador ARCHIVUM\",\"usuario_rol\":[\"Administrador\"]}','172.19.0.1','2026-11-23 06:50:38'),
(47,11,'BAJA_PRODUCTO','productos',91,'{\"nombre\":\"Historia Verdadera de la Conquista de la Nueva Espa\\u00f1a (Edici\\u00f3n Cr\\u00edtica 1960)\",\"estado_anterior\":\"activo\",\"nuevo_estado\":\"baja\",\"usuario_nombre\":\"Administrador ARCHIVUM\",\"usuario_rol\":[\"Administrador\"]}','172.19.0.1','2026-11-23 06:50:43'),
(48,11,'BAJA_PRODUCTO','productos',92,'{\"nombre\":\"Kind of Blue (Mono 1st Press 6-Eye)\",\"estado_anterior\":\"activo\",\"nuevo_estado\":\"baja\",\"usuario_nombre\":\"Administrador ARCHIVUM\",\"usuario_rol\":[\"Administrador\"]}','172.19.0.1','2026-11-23 06:50:48'),
(49,11,'BAJA_PRODUCTO','productos',93,'{\"nombre\":\"Blue Train (Plastylite RVG Ear Press)\",\"estado_anterior\":\"activo\",\"nuevo_estado\":\"baja\",\"usuario_nombre\":\"Administrador ARCHIVUM\",\"usuario_rol\":[\"Administrador\"]}','172.19.0.1','2026-11-23 06:50:54'),
(50,11,'BAJA_PRODUCTO','productos',94,'{\"nombre\":\"A Love Supreme (Gatefold 1st Impulse! Mono)\",\"estado_anterior\":\"activo\",\"nuevo_estado\":\"baja\",\"usuario_nombre\":\"Administrador ARCHIVUM\",\"usuario_rol\":[\"Administrador\"]}','172.19.0.1','2026-11-23 06:51:00'),
(51,11,'ACTUALIZACION_IMAGEN_PRODUCTO','productos',96,'{\"nombre\":\"The Dark Side of the Moon (1st Japan Black Triangle CD)\",\"nueva_imagen\":\"productos\\/C3qtAHVSU9LoQlb9SU4WFgz187Cz84fDeAZXu3pt.jpg\",\"usuario_nombre\":\"Administrador ARCHIVUM\"}','172.19.0.1','2026-11-23 06:51:22'),
(52,11,'BAJA_PRODUCTO','productos',95,'{\"nombre\":\"Abbey Road (UK 1st Press Misaligned Apple)\",\"estado_anterior\":\"activo\",\"nuevo_estado\":\"baja\",\"usuario_nombre\":\"Administrador ARCHIVUM\",\"usuario_rol\":[\"Administrador\"]}','172.19.0.1','2026-11-23 06:51:28'),
(53,11,'BAJA_PRODUCTO','productos',97,'{\"nombre\":\"Led Zeppelin IV (Prensado Audi\\u00f3filo Atlantic 180g)\",\"estado_anterior\":\"activo\",\"nuevo_estado\":\"baja\",\"usuario_nombre\":\"Administrador ARCHIVUM\",\"usuario_rol\":[\"Administrador\"]}','172.19.0.1','2026-11-23 06:51:34'),
(54,11,'ACTUALIZAR_PRECIO_PRODUCTO','productos',88,'{\"nombre\":\"Don Quijote de la Mancha (Edici\\u00f3n Conmemorativa Facsimilar RAE 1947)\",\"precio_anterior\":38000,\"precio_nuevo\":1000,\"usuario_nombre\":\"Administrador ARCHIVUM\",\"usuario_rol\":[\"Administrador\"]}','172.19.0.1','2026-11-23 06:51:40'),
(55,11,'ACTUALIZAR_PRECIO_PRODUCTO','productos',89,'{\"nombre\":\"La Divina Comedia (Edici\\u00f3n Biling\\u00fce con Grabados de Gustave Dor\\u00e9 1921)\",\"precio_anterior\":42000,\"precio_nuevo\":500,\"usuario_nombre\":\"Administrador ARCHIVUM\",\"usuario_rol\":[\"Administrador\"]}','172.19.0.1','2026-11-23 06:51:49'),
(56,11,'ACTUALIZAR_PRECIO_PRODUCTO','productos',96,'{\"nombre\":\"The Dark Side of the Moon (1st Japan Black Triangle CD)\",\"precio_anterior\":14800,\"precio_nuevo\":530,\"usuario_nombre\":\"Administrador ARCHIVUM\",\"usuario_rol\":[\"Administrador\"]}','172.19.0.1','2026-11-23 06:51:54'),
(57,11,'ACTUALIZAR_PRECIO_PRODUCTO','productos',87,'{\"nombre\":\"El Banquete y Fed\\u00f3n (Traducci\\u00f3n Cl\\u00e1sica Bibliotheca Scriptorum 1944)\",\"precio_anterior\":13400,\"precio_nuevo\":360,\"usuario_nombre\":\"Administrador ARCHIVUM\",\"usuario_rol\":[\"Administrador\"]}','172.19.0.1','2026-11-23 06:52:05'),
(58,11,'ACTUALIZAR_PRECIO_PRODUCTO','productos',86,'{\"nombre\":\"As\\u00ed Habl\\u00f3 Zaratustra: Un Libro para Todos y para Nadie (Edici\\u00f3n Aguilar 1932)\",\"precio_anterior\":19500,\"precio_nuevo\":380,\"usuario_nombre\":\"Administrador ARCHIVUM\",\"usuario_rol\":[\"Administrador\"]}','172.19.0.1','2026-11-23 06:52:15'),
(59,11,'ACTUALIZAR_PRECIO_PRODUCTO','productos',84,'{\"nombre\":\"El Nombre de la Rosa (1\\u00aa Edici\\u00f3n en Castellano Lumen 1982)\",\"precio_anterior\":14200,\"precio_nuevo\":250,\"usuario_nombre\":\"Administrador ARCHIVUM\",\"usuario_rol\":[\"Administrador\"]}','172.19.0.1','2026-11-23 06:52:22'),
(60,11,'ACTUALIZAR_PRECIO_PRODUCTO','productos',83,'{\"nombre\":\"El Se\\u00f1or de los Anillos: La Comunidad del Anillo (1\\u00aa Edici\\u00f3n Minotauro 1977)\",\"precio_anterior\":29800,\"precio_nuevo\":300,\"usuario_nombre\":\"Administrador ARCHIVUM\",\"usuario_rol\":[\"Administrador\"]}','172.19.0.1','2026-11-23 06:52:29'),
(61,11,'ACTUALIZAR_PRECIO_PRODUCTO','productos',85,'{\"nombre\":\"Estudio en Escarlata & El Sabueso de los Baskerville (Edici\\u00f3n Anotada 1928)\",\"precio_anterior\":17800,\"precio_nuevo\":280,\"usuario_nombre\":\"Administrador ARCHIVUM\",\"usuario_rol\":[\"Administrador\"]}','172.19.0.1','2026-11-23 06:52:40'),
(62,11,'ACTUALIZACION_IMAGEN_PRODUCTO','productos',82,'{\"nombre\":\"Cr\\u00f3nicas Marcianas (Edici\\u00f3n Pr\\u00edncipe con Pr\\u00f3logo de Borges 1955)\",\"nueva_imagen\":\"productos\\/DDJgwHy3Hd6EbSR0LA67OVwBhiLBakis3xI3bHm9.jpg\",\"usuario_nombre\":\"Administrador ARCHIVUM\"}','172.19.0.1','2026-11-23 06:52:59'),
(63,11,'ACTUALIZACION_IMAGEN_PRODUCTO','productos',81,'{\"nombre\":\"Fahrenheit 451 (Primera Edici\\u00f3n en Espa\\u00f1ol Minotauro 1955)\",\"nueva_imagen\":\"productos\\/TLYiBzkepp04SsB7cHRFLORSJA1IGPiPXW1O7BH9.jpg\",\"usuario_nombre\":\"Administrador ARCHIVUM\"}','172.19.0.1','2026-11-23 06:53:18'),
(64,11,'ACTUALIZACION_IMAGEN_PRODUCTO','productos',80,'{\"nombre\":\"Narraciones Extraordinarias & El Cuervo (Edici\\u00f3n Ilustrada 1945)\",\"nueva_imagen\":\"productos\\/p9Cf6Nskf3dQFBA3Vr6G1QtqVH6HutScpRqC1q8b.webp\",\"usuario_nombre\":\"Administrador ARCHIVUM\"}','172.19.0.1','2026-11-23 06:54:12'),
(65,11,'ACTUALIZACION_IMAGEN_PRODUCTO','productos',79,'{\"nombre\":\"La Estructura de las Revoluciones Cient\\u00edficas (1\\u00aa Edici\\u00f3n FCE)\",\"nueva_imagen\":\"productos\\/7HzDLydxgzIHkTURYQu6UJMifjJKndEvoRs0aUPs.jpg\",\"usuario_nombre\":\"Administrador ARCHIVUM\"}','172.19.0.1','2026-11-23 06:54:25'),
(66,11,'BAJA_PRODUCTO','productos',78,'{\"nombre\":\"Visi\\u00f3n de los Vencidos: Relaciones Ind\\u00edgenas de la Conquista\",\"estado_anterior\":\"activo\",\"nuevo_estado\":\"baja\",\"usuario_nombre\":\"Administrador ARCHIVUM\",\"usuario_rol\":[\"Administrador\"]}','172.19.0.1','2026-11-23 06:55:09'),
(67,11,'ACTUALIZACION_IMAGEN_PRODUCTO','productos',77,'{\"nombre\":\"Piedra de Sol y Obra Po\\u00e9tica (Primera Edici\\u00f3n 1957)\",\"nueva_imagen\":\"productos\\/mJvRjktDKa0jbFxCDpRi4FhhQU5o2cvndCFB5xZZ.jpg\",\"usuario_nombre\":\"Administrador ARCHIVUM\"}','172.19.0.1','2026-11-23 06:55:32'),
(68,11,'ACTUALIZACION_IMAGEN_PRODUCTO','productos',76,'{\"nombre\":\"Rayuela (Tiraje Cl\\u00e1sico Editorial Sudamericana)\",\"nueva_imagen\":\"productos\\/TCPLCpmh1SnaGKU280WLmOfcZPFzSCiWiP89POg9.webp\",\"usuario_nombre\":\"Administrador ARCHIVUM\"}','172.19.0.1','2026-11-23 06:55:47'),
(69,11,'ACTUALIZACION_IMAGEN_PRODUCTO','productos',75,'{\"nombre\":\"Aura (Edici\\u00f3n Conmemorativa Biblioteca Era Coyoac\\u00e1n)\",\"nueva_imagen\":\"productos\\/aVTFPNupBVuqv8AqdLxNykqPMmHV8d0R3tME0hoB.webp\",\"usuario_nombre\":\"Administrador ARCHIVUM\"}','172.19.0.1','2026-11-23 06:56:02'),
(70,11,'ACTUALIZACION_IMAGEN_PRODUCTO','productos',74,'{\"nombre\":\"El Laberinto de la Soledad (1\\u00aa Edici\\u00f3n Cuadernos Americanos)\",\"nueva_imagen\":\"productos\\/qYnCho0DwirzbkIQJ1PrhVvyhXI9QRBZWdW6kcXG.jpg\",\"usuario_nombre\":\"Administrador ARCHIVUM\"}','172.19.0.1','2026-11-23 06:56:19'),
(71,11,'ACTUALIZACION_IMAGEN_PRODUCTO','productos',73,'{\"nombre\":\"El Aleph (Ejemplar Dedicado por el Autor)\",\"nueva_imagen\":\"productos\\/gYa5QlrlkdwvAIGMlq3ldGpHoZuGe8ygDtIKpg2b.jpg\",\"usuario_nombre\":\"Administrador ARCHIVUM\"}','172.19.0.1','2026-11-23 06:56:34'),
(72,11,'BAJA_PRODUCTO','productos',72,'{\"nombre\":\"Pedro P\\u00e1ramo (Edici\\u00f3n Pr\\u00edncipe FCE)\",\"estado_anterior\":\"activo\",\"nuevo_estado\":\"baja\",\"usuario_nombre\":\"Administrador ARCHIVUM\",\"usuario_rol\":[\"Administrador\"]}','172.19.0.1','2026-11-23 06:57:19'),
(73,11,'ACTUALIZACION_IMAGEN_PRODUCTO','productos',71,'{\"nombre\":\"Cien A\\u00f1os de Soledad (1\\u00aa Edici\\u00f3n Sudamericana)\",\"nueva_imagen\":\"productos\\/9b2xdCyABitnYZYoAAhn8J5OjYrrB4NGxAvBBSJ1.jpg\",\"usuario_nombre\":\"Administrador ARCHIVUM\"}','172.19.0.1','2026-11-23 06:57:32'),
(74,13,'CHECKOUT_SIMULADO_EXITOSO','pedidos',6,'{\"folio\":\"ARC-20261123-NEW0B\",\"total\":\"3065.00\",\"ultimos_digitos\":\"4242\",\"numero_guia\":\"GUA-ARC-261123-Z6UPEZ\"}','172.19.0.1','2026-11-23 07:07:33'),
(75,13,'DEVOLUCION_SOLICITADA','devoluciones',1,'{\"folio_devolucion\":\"DEV-ARC-20261123-UNAB\",\"pedido_folio\":\"ARC-20261123-NEW0B\",\"detalle_pedido_id\":1,\"cantidad\":1,\"motivo\":\"producto_defectuoso\",\"evidencias_count\":1}','172.19.0.1','2026-11-23 07:08:15'),
(76,11,'LOGOUT','usuarios',11,'{\"ip\":\"172.19.0.1\"}','172.19.0.1','2026-11-23 07:09:02'),
(77,13,'LOGOUT','usuarios',13,'{\"ip\":\"172.19.0.1\"}','172.19.0.1','2026-11-23 07:09:11'),
(78,NULL,'LOGIN_FALLIDO','usuarios',NULL,'{\"email\":\"ventas1@archivum.com\",\"ip\":\"172.19.0.1\"}','172.19.0.1','2026-11-23 07:09:18'),
(79,NULL,'LOGIN_FALLIDO','usuarios',NULL,'{\"email\":\"ventas1@archivum.com\",\"ip\":\"172.19.0.1\"}','172.19.0.1','2026-11-23 07:09:23'),
(80,NULL,'LOGIN_FALLIDO','usuarios',NULL,'{\"email\":\"ventas1@archivum.com\",\"ip\":\"172.19.0.1\"}','172.19.0.1','2026-11-23 07:09:31'),
(81,12,'LOGIN_EXITOSO','usuarios',12,'{\"email\":\"compras1@archivum.com\",\"roles\":[\"Compras\"],\"tab_session_id\":\"tab_mwsum1yn_dvcjeo0x7dwqq7oj6ky0\"}','172.19.0.1','2026-11-23 07:09:49'),
(82,12,'LOGOUT','usuarios',12,'{\"ip\":\"172.19.0.1\"}','172.19.0.1','2026-11-23 07:09:52'),
(83,NULL,'LOGIN_FALLIDO','usuarios',NULL,'{\"email\":\"almacen1@archivum.com\",\"ip\":\"172.19.0.1\"}','172.19.0.1','2026-11-23 07:10:09'),
(84,NULL,'LOGIN_FALLIDO','usuarios',NULL,'{\"email\":\"admin@archivum.com\",\"ip\":\"172.19.0.1\"}','172.19.0.1','2026-11-23 07:32:12'),
(85,11,'LOGIN_EXITOSO','usuarios',11,'{\"email\":\"admin@archivum.com\",\"roles\":[\"Administrador\"],\"tab_session_id\":\"tab_mwsum1yn_dvcjeo0x7dwqq7oj6ky0\"}','172.19.0.1','2026-11-23 07:32:37'),
(86,13,'LOGIN_EXITOSO','usuarios',13,'{\"email\":\"quijadaevelin82@gmail.com\",\"roles\":[\"cliente\"],\"tab_session_id\":\"tab_mwsy9rsh_1vkvjh5txzhbo51dfjv9\"}','172.19.0.1','2026-11-23 07:34:32'),
(87,11,'ACTUALIZAR_PRECIO_PRODUCTO','productos',140,'{\"nombre\":\"Cartucho The Legend of Zelda (Gold Cartridge NES 1987 Sellado)\",\"precio_anterior\":2500,\"precio_nuevo\":2600,\"usuario_nombre\":\"Administrador ARCHIVUM\",\"usuario_rol\":[\"Administrador\"]}','172.19.0.1','2026-11-23 07:35:42'),
(88,11,'ACTUALIZAR_PRECIO_PRODUCTO','productos',152,'{\"nombre\":\"C\\u00f3mo ser youtuber\",\"precio_anterior\":189,\"precio_nuevo\":200,\"usuario_nombre\":\"Administrador ARCHIVUM\",\"usuario_rol\":[\"Administrador\"]}','172.19.0.1','2026-11-23 07:36:47'),
(89,13,'LOGIN_EXITOSO','usuarios',13,'{\"email\":\"quijadaevelin82@gmail.com\",\"roles\":[\"cliente\"],\"tab_session_id\":\"tab_mwtcvui3_af69dhs2umubxqk25ps3\"}','172.19.0.1','2026-11-23 14:25:29'),
(90,11,'LOGIN_EXITOSO','usuarios',11,'{\"email\":\"admin@archivum.com\",\"roles\":[\"Administrador\"],\"tab_session_id\":\"tab_mwtczk9d_ghg0wb8dbspmqf8vwmdu\"}','172.19.0.1','2026-11-23 14:27:23'),
(91,NULL,'LOGIN_FALLIDO','usuarios',NULL,'{\"email\":\"vanequialva@gmail.com\",\"ip\":\"172.19.0.1\"}','172.19.0.1','2026-11-23 14:55:53'),
(92,13,'CHECKOUT_SIMULADO_EXITOSO','pedidos',7,'{\"folio\":\"ARC-20261123-SQZIZ\",\"total\":\"24563.00\",\"ultimos_digitos\":\"4242\",\"numero_guia\":\"GUA-ARC-261123-X4UNQ9\"}','172.19.0.1','2026-11-23 15:35:16'),
(93,13,'CHECKOUT_SIMULADO_EXITOSO','pedidos',8,'{\"folio\":\"ARC-20261123-VMBDB\",\"total\":\"1566.55\",\"ultimos_digitos\":\"4242\",\"numero_guia\":\"GUA-ARC-261123-J67RFF\"}','172.19.0.1','2026-11-23 15:37:19'),
(94,13,'CHECKOUT_SIMULADO_EXITOSO','pedidos',9,'{\"folio\":\"ARC-20261123-F1NRM\",\"total\":\"5500.00\",\"ultimos_digitos\":\"4242\",\"numero_guia\":\"GUA-ARC-261123-4CG7AI\"}','172.19.0.1','2026-11-23 15:37:41'),
(95,13,'CHECKOUT_SIMULADO_EXITOSO','pedidos',10,'{\"folio\":\"ARC-20261123-CSL6L\",\"total\":\"4800.00\",\"ultimos_digitos\":\"4242\",\"numero_guia\":\"GUA-ARC-261123-USNDUT\"}','172.19.0.1','2026-11-23 15:38:19'),
(96,13,'CHECKOUT_SIMULADO_EXITOSO','pedidos',11,'{\"folio\":\"ARC-20261123-VPXCC\",\"total\":\"58000.00\",\"ultimos_digitos\":\"4242\",\"numero_guia\":\"GUA-ARC-261123-6GM6SA\"}','172.19.0.1','2026-11-23 17:46:57'),
(97,13,'CHECKOUT_SIMULADO_EXITOSO','pedidos',12,'{\"folio\":\"ARC-20261123-EXKD3\",\"total\":\"32000.00\",\"ultimos_digitos\":\"4242\",\"numero_guia\":\"GUA-ARC-261123-3ZU3CQ\"}','172.19.0.1','2026-11-23 17:50:40'),
(98,13,'CHECKOUT_SIMULADO_EXITOSO','pedidos',13,'{\"folio\":\"ARC-20261123-WARHQ\",\"total\":\"4800.00\",\"ultimos_digitos\":\"4242\",\"numero_guia\":\"GUA-ARC-261123-7EL7BX\"}','172.19.0.1','2026-11-23 18:02:53'),
(99,13,'CHECKOUT_SIMULADO_EXITOSO','pedidos',14,'{\"folio\":\"ARC-20261123-1U1YM\",\"total\":\"1000.00\",\"ultimos_digitos\":\"4242\",\"numero_guia\":\"GUA-ARC-261123-2WZF42\"}','172.19.0.1','2026-11-23 18:11:03'),
(100,13,'CHECKOUT_SIMULADO_EXITOSO','pedidos',15,'{\"folio\":\"ARC-20261123-AINDZ\",\"total\":\"11494.00\",\"ultimos_digitos\":\"4242\",\"numero_guia\":\"GUA-ARC-261123-OJQC0Y\"}','172.19.0.1','2026-11-23 18:36:47'),
(101,13,'CHECKOUT_SIMULADO_EXITOSO','pedidos',16,'{\"folio\":\"ARC-20261123-IOOS0\",\"total\":\"11200.00\",\"ultimos_digitos\":\"4242\",\"numero_guia\":\"GUA-ARC-261123-94XI9M\"}','172.19.0.1','2026-11-23 18:57:06'),
(102,11,'LOGIN_EXITOSO','usuarios',11,'{\"email\":\"admin@archivum.com\",\"roles\":[\"Administrador\"],\"tab_session_id\":\"tab_mwv5v8x8_5f5k5cc7su0526msdvft\"}','172.19.0.1','2026-11-24 21:01:34'),
(103,11,'LOGOUT','usuarios',11,'{\"ip\":\"172.19.0.1\"}','172.19.0.1','2026-11-24 22:51:17'),
(104,13,'LOGIN_EXITOSO','usuarios',13,'{\"email\":\"quijadaevelin82@gmail.com\",\"roles\":[\"cliente\"],\"tab_session_id\":\"tab_mwvfrhgj_q1w2ll7a5tp554lum45h\"}','172.19.0.1','2026-11-25 01:20:36'),
(105,13,'LOGIN_EXITOSO','usuarios',13,'{\"email\":\"quijadaevelin82@gmail.com\",\"roles\":[\"cliente\"],\"tab_session_id\":\"tab_mwvg7mro_s4sz2qz8y4i66mqsrh7a\"}','172.19.0.1','2026-11-25 01:32:54'),
(106,11,'LOGIN_EXITOSO','usuarios',11,'{\"email\":\"admin@archivum.com\",\"roles\":[\"Administrador\"],\"tab_session_id\":\"tab_mwwmggj0_gpuwr62p67n9j3jtkfr1\"}','172.19.0.1','2026-11-25 21:32:52'),
(107,11,'ALTA_PRODUCTO','productos',163,'{\"nombre\":\"Rain In My Heart\",\"sku\":\"VIN-FS-1968-R51026\",\"precio\":\"150.00\",\"stock_inicial\":5,\"es_pieza_unica\":false,\"usuario_nombre\":\"Administrador ARCHIVUM\",\"usuario_rol\":[\"Administrador\"],\"imagen\":\"productos\\/1HL0V4cl2WoW0LIBPKatIhbqwyCtABQvnS9sayJm.webp\"}','172.19.0.1','2026-11-25 21:40:58'),
(108,11,'ALTA_PRODUCTO','productos',164,'{\"nombre\":\"El Amor Manda\",\"sku\":\"CD-MJ-2013-887654156426\",\"precio\":\"150.00\",\"stock_inicial\":5,\"es_pieza_unica\":false,\"usuario_nombre\":\"Administrador ARCHIVUM\",\"usuario_rol\":[\"Administrador\"],\"imagen\":\"productos\\/jxYSgpkqMiZTYpXe0VHbQTVLXPkmlY1dLdaKBMsT.webp\"}','172.19.0.1','2026-11-25 21:52:24'),
(109,11,'ALTA_PRODUCTO','productos',165,'{\"nombre\":\"Gotas de Fuego\",\"sku\":\"VIN-JJ-2026-198029747416\",\"precio\":\"899.00\",\"stock_inicial\":6,\"es_pieza_unica\":false,\"usuario_nombre\":\"Administrador ARCHIVUM\",\"usuario_rol\":[\"Administrador\"],\"imagen\":\"productos\\/rmyeOIA6gOL4UNwzJNtfWc5EAEqtIXKxU9fysQqO.webp\"}','172.19.0.1','2026-11-25 22:11:38');
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
('laravel-cache-archivum_tab_token:tab_mwvfrhgj_q1w2ll7a5tp554lum45h','a:7:{s:7:\"user_id\";i:13;s:16:\"carrito_archivum\";a:2:{i:76;i:1;i:75;i:1;}s:26:\"carrito_precios_congelados\";a:2:{i:76;i:8400;i:75;i:6800;}s:20:\"carrito_reservas_ids\";a:2:{i:0;i:26;i:1;i:27;}s:17:\"carrito_expira_en\";N;s:16:\"authenticated_at\";s:25:\"2026-11-25T01:20:36+00:00\";s:10:\"updated_at\";s:25:\"2026-11-25T01:31:53+00:00\";}',1795613513),
('laravel-cache-archivum_tab_token:tab_mwvg7mro_s4sz2qz8y4i66mqsrh7a','a:7:{s:7:\"user_id\";i:13;s:16:\"carrito_archivum\";a:3:{i:75;i:1;i:76;i:1;i:74;i:1;}s:26:\"carrito_precios_congelados\";a:3:{i:75;i:6800;i:76;i:8400;i:74;i:16900;}s:20:\"carrito_reservas_ids\";a:1:{i:0;i:28;}s:17:\"carrito_expira_en\";N;s:16:\"authenticated_at\";s:25:\"2026-11-25T01:32:54+00:00\";s:10:\"updated_at\";s:25:\"2026-11-25T01:32:59+00:00\";}',1795613579),
('laravel-cache-archivum_tab_token:tab_mwwmggj0_gpuwr62p67n9j3jtkfr1','a:7:{s:7:\"user_id\";i:11;s:16:\"carrito_archivum\";a:0:{}s:26:\"carrito_precios_congelados\";a:0:{}s:20:\"carrito_reservas_ids\";a:0:{}s:17:\"carrito_expira_en\";N;s:16:\"authenticated_at\";s:25:\"2026-11-25T21:32:52+00:00\";s:10:\"updated_at\";s:25:\"2026-11-25T22:11:38+00:00\";}',1795687898);
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
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carrito_items`
--

LOCK TABLES `carrito_items` WRITE;
/*!40000 ALTER TABLE `carrito_items` DISABLE KEYS */;
INSERT INTO `carrito_items` VALUES
(49,5,75,1,6800.00,'2026-11-25 01:32:58','2026-11-25 01:32:58'),
(50,5,76,1,8400.00,'2026-11-25 01:32:59','2026-11-25 01:32:59'),
(51,5,74,1,16900.00,'2026-11-25 01:32:59','2026-11-25 01:32:59');
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
INSERT INTO `cliente_promocion` VALUES
(5,4,'2026-11-23 15:38:19',NULL),
(5,5,'2026-11-23 15:38:19',NULL),
(5,6,'2026-11-23 15:38:19',NULL);
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
INSERT INTO `clientes_frecuentes` VALUES
(5,'2026-11-23',1100,'2026-11-23 15:38:19','2026-11-23 18:57:06');
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
(135,'Banpresto / Bird Studio','Dragon Ball Z Vintage Series','figuras','PVC Grado Colección & Acrílico UV',1,'productos/pURPIuY5YUTPvNs5V0NuCOACVAXYA0rdg10gsJVB.jpg'),
(136,'Yonezawa Toys Japón','Space Age Tin Toy Series','juguetes','Hojalata Litografiada & Resorte de Acero',1,NULL),
(137,'Gobierno Constitucionalista de México','Papel Moneda de la Revolución Mexicana','numismatica','Papel Moneda de Seguridad',1,'productos/paiPRw6iKSvFPyK4TkoNDxvKiIMdvW2cGgNfRCgz.jpg'),
(138,'Real Casa de Moneda de México','Numismática Virreinal Novohispana','numismatica','Plata Ley .903',1,NULL),
(139,'Nintendo Co., Ltd. Japón','Silver Series Game & Watch (AC-01)','videojuegos','Plástico ABS & Pantalla LCD Reflectiva',1,'productos/Ov8dBeHpr9qzg7jBmxx6rzvIFBVrcyrIfVdQyYNz.webp'),
(140,'Nintendo of America','NES Classic Gold Series (NES-ZL-USA)','videojuegos','Carcasa Dorada Metalizada & Placa PCB',1,'productos/lMStJFZVoRaFgiGxdH3QPglfHsNjnBzPESNRtTbh.avif'),
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
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_envios`
--

LOCK TABLES `detalle_envios` WRITE;
/*!40000 ALTER TABLE `detalle_envios` DISABLE KEYS */;
INSERT INTO `detalle_envios` VALUES
(1,1,1,1,'2026-11-23 07:07:33','2026-11-23 07:07:33'),
(2,2,6,1,'2026-11-23 15:35:16','2026-11-23 15:35:16'),
(3,2,5,1,'2026-11-23 15:35:16','2026-11-23 15:35:16'),
(4,2,3,1,'2026-11-23 15:35:16','2026-11-23 15:35:16'),
(5,2,4,1,'2026-11-23 15:35:16','2026-11-23 15:35:16'),
(6,2,2,1,'2026-11-23 15:35:16','2026-11-23 15:35:16'),
(7,3,7,1,'2026-11-23 15:37:19','2026-11-23 15:37:19'),
(8,4,8,1,'2026-11-23 15:37:41','2026-11-23 15:37:41'),
(9,5,9,1,'2026-11-23 15:38:19','2026-11-23 15:38:19'),
(10,6,10,1,'2026-11-23 17:46:57','2026-11-23 17:46:57'),
(11,7,11,1,'2026-11-23 17:50:40','2026-11-23 17:50:40'),
(12,8,12,1,'2026-11-23 18:02:53','2026-11-23 18:02:53'),
(13,9,13,1,'2026-11-23 18:11:03','2026-11-23 18:11:03'),
(14,10,14,1,'2026-11-23 18:36:47','2026-11-23 18:36:47'),
(15,11,15,1,'2026-11-23 18:57:06','2026-11-23 18:57:06');
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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_pagos`
--

LOCK TABLES `detalle_pagos` WRITE;
/*!40000 ALTER TABLE `detalle_pagos` DISABLE KEYS */;
INSERT INTO `detalle_pagos` VALUES
(1,1,'PAS-ARC-PENNCGQE','4242','SIM-AUTH-840602','{\"modo\":\"SIMULACION_PRUEBA\",\"titular\":\"Evelyn\",\"terminacion_tarjeta\":\"4242\",\"codigo_autorizacion\":\"SIM-AUTH-840602\",\"mensaje\":\"Transacci\\u00f3n aprobada satisfactoriamente por Pasarela Simulada ARCHIVUM.\"}','2026-11-23 07:07:33','2026-11-23 07:07:33'),
(2,2,'PAS-ARC-HEDLTF1A','4242','SIM-AUTH-815095','{\"modo\":\"SIMULACION_PRUEBA\",\"titular\":\"Evelyn\",\"terminacion_tarjeta\":\"4242\",\"codigo_autorizacion\":\"SIM-AUTH-815095\",\"mensaje\":\"Transacci\\u00f3n aprobada satisfactoriamente por Pasarela Simulada ARCHIVUM.\"}','2026-11-23 15:35:16','2026-11-23 15:35:16'),
(3,3,'PAS-ARC-DWJ2IGKW','4242','SIM-AUTH-507919','{\"modo\":\"SIMULACION_PRUEBA\",\"titular\":\"Evelyn\",\"terminacion_tarjeta\":\"4242\",\"codigo_autorizacion\":\"SIM-AUTH-507919\",\"mensaje\":\"Transacci\\u00f3n aprobada satisfactoriamente por Pasarela Simulada ARCHIVUM.\"}','2026-11-23 15:37:19','2026-11-23 15:37:19'),
(4,4,'PAS-ARC-A7BRVCTC','4242','SIM-AUTH-876118','{\"modo\":\"SIMULACION_PRUEBA\",\"titular\":\"Evelyn\",\"terminacion_tarjeta\":\"4242\",\"codigo_autorizacion\":\"SIM-AUTH-876118\",\"mensaje\":\"Transacci\\u00f3n aprobada satisfactoriamente por Pasarela Simulada ARCHIVUM.\"}','2026-11-23 15:37:41','2026-11-23 15:37:41'),
(5,5,'PAS-ARC-67U2OGAH','4242','SIM-AUTH-137278','{\"modo\":\"SIMULACION_PRUEBA\",\"titular\":\"Evelyn\",\"terminacion_tarjeta\":\"4242\",\"codigo_autorizacion\":\"SIM-AUTH-137278\",\"mensaje\":\"Transacci\\u00f3n aprobada satisfactoriamente por Pasarela Simulada ARCHIVUM.\"}','2026-11-23 15:38:19','2026-11-23 15:38:19'),
(6,6,'PAS-ARC-INMSNRSV','4242','SIM-AUTH-457773','{\"modo\":\"SIMULACION_PRUEBA\",\"titular\":\"Evelyn\",\"terminacion_tarjeta\":\"4242\",\"codigo_autorizacion\":\"SIM-AUTH-457773\",\"mensaje\":\"Transacci\\u00f3n aprobada satisfactoriamente por Pasarela Simulada ARCHIVUM.\"}','2026-11-23 17:46:57','2026-11-23 17:46:57'),
(7,7,'PAS-ARC-4D2UYEEK','4242','SIM-AUTH-761486','{\"modo\":\"SIMULACION_PRUEBA\",\"titular\":\"Evelyn\",\"terminacion_tarjeta\":\"4242\",\"codigo_autorizacion\":\"SIM-AUTH-761486\",\"mensaje\":\"Transacci\\u00f3n aprobada satisfactoriamente por Pasarela Simulada ARCHIVUM.\"}','2026-11-23 17:50:40','2026-11-23 17:50:40'),
(8,8,'PAS-ARC-TJX6DH6B','4242','SIM-AUTH-125084','{\"modo\":\"SIMULACION_PRUEBA\",\"titular\":\"Evelyn\",\"terminacion_tarjeta\":\"4242\",\"codigo_autorizacion\":\"SIM-AUTH-125084\",\"mensaje\":\"Transacci\\u00f3n aprobada satisfactoriamente por Pasarela Simulada ARCHIVUM.\"}','2026-11-23 18:02:52','2026-11-23 18:02:52'),
(9,9,'PAS-ARC-XAR0WE6U','4242','SIM-AUTH-488928','{\"modo\":\"SIMULACION_PRUEBA\",\"titular\":\"Evelyn\",\"terminacion_tarjeta\":\"4242\",\"codigo_autorizacion\":\"SIM-AUTH-488928\",\"mensaje\":\"Transacci\\u00f3n aprobada satisfactoriamente por Pasarela Simulada ARCHIVUM.\"}','2026-11-23 18:11:03','2026-11-23 18:11:03'),
(10,10,'PAS-ARC-CZFOJNCA','4242','SIM-AUTH-760586','{\"modo\":\"SIMULACION_PRUEBA\",\"titular\":\"Evelyn\",\"terminacion_tarjeta\":\"4242\",\"codigo_autorizacion\":\"SIM-AUTH-760586\",\"mensaje\":\"Transacci\\u00f3n aprobada satisfactoriamente por Pasarela Simulada ARCHIVUM.\"}','2026-11-23 18:36:47','2026-11-23 18:36:47'),
(11,11,'PAS-ARC-UTQWA3MC','4242','SIM-AUTH-367760','{\"modo\":\"SIMULACION_PRUEBA\",\"titular\":\"Evelyn\",\"terminacion_tarjeta\":\"4242\",\"codigo_autorizacion\":\"SIM-AUTH-367760\",\"mensaje\":\"Transacci\\u00f3n aprobada satisfactoriamente por Pasarela Simulada ARCHIVUM.\"}','2026-11-23 18:57:06','2026-11-23 18:57:06');
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
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_pedidos`
--

LOCK TABLES `detalle_pedidos` WRITE;
/*!40000 ALTER TABLE `detalle_pedidos` DISABLE KEYS */;
INSERT INTO `detalle_pedidos` VALUES
(1,6,150,1,3065.00,3065.00,'2026-11-23 07:07:33','2026-11-23 07:07:33'),
(2,7,152,1,189.00,189.00,'2026-11-23 15:35:16','2026-11-23 15:35:16'),
(3,7,135,1,14200.00,14200.00,'2026-11-23 15:35:16','2026-11-23 15:35:16'),
(4,7,148,1,699.00,699.00,'2026-11-23 15:35:16','2026-11-23 15:35:16'),
(5,7,130,1,1075.00,1075.00,'2026-11-23 15:35:16','2026-11-23 15:35:16'),
(6,7,76,1,8400.00,8400.00,'2026-11-23 15:35:16','2026-11-23 15:35:16'),
(7,8,102,1,1566.55,1566.55,'2026-11-23 15:37:19','2026-11-23 15:37:19'),
(8,9,124,1,5500.00,5500.00,'2026-11-23 15:37:41','2026-11-23 15:37:41'),
(9,10,98,1,4800.00,4800.00,'2026-11-23 15:38:19','2026-11-23 15:38:19'),
(10,11,73,1,58000.00,58000.00,'2026-11-23 17:46:57','2026-11-23 17:46:57'),
(11,12,71,1,32000.00,32000.00,'2026-11-23 17:50:40','2026-11-23 17:50:40'),
(12,13,98,1,4800.00,4800.00,'2026-11-23 18:02:52','2026-11-23 18:02:52'),
(13,14,88,1,1000.00,1000.00,'2026-11-23 18:11:03','2026-11-23 18:11:03'),
(14,15,149,1,11494.00,11494.00,'2026-11-23 18:36:47','2026-11-23 18:36:47'),
(15,16,79,1,11200.00,11200.00,'2026-11-23 18:57:06','2026-11-23 18:57:06');
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `devoluciones`
--

LOCK TABLES `devoluciones` WRITE;
/*!40000 ALTER TABLE `devoluciones` DISABLE KEYS */;
INSERT INTO `devoluciones` VALUES
(1,'DEV-ARC-20261123-UNAB',6,5,1,1,'producto_defectuoso','esta roto','[{\"nombre\":\"WhatsApp Image 2026-07-01 at 8.54.17 PM.jpeg\",\"ruta\":\"devoluciones\\/pNGAEfmEgtS9ul8lYBzLofV0BeIXZ88CMitqYUMI.jpg\",\"url\":\"http:\\/\\/localhost:8000\\/storage\\/devoluciones\\/pNGAEfmEgtS9ul8lYBzLofV0BeIXZ88CMitqYUMI.jpg\",\"mime\":\"image\\/jpeg\"}]','solicitada','solicitada',3065.00,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-11-23 07:08:15','2026-11-23 07:08:15');
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `direcciones`
--

LOCK TABLES `direcciones` WRITE;
/*!40000 ALTER TABLE `direcciones` DISABLE KEYS */;
INSERT INTO `direcciones` VALUES
(2,5,'Entrega Custodiada','Av. Álvaro Obregón 185, Col. Roma Norte','S/N',NULL,'Centro','Cuauhtémoc','Ciudad de México','06700','México',NULL,1,'2026-11-23 07:07:33','2026-11-23 07:07:33'),
(3,5,'Entrega Custodiada','Av. Álvaro Obregón 185, Col. Roma Norte','S/N',NULL,'Centro','Cuauhtémoc','Ciudad de México','06700','México',NULL,1,'2026-11-23 15:35:15','2026-11-23 15:35:15'),
(4,5,'Entrega Custodiada','Av. Álvaro Obregón 185, Col. Roma Norte','S/N',NULL,'Centro','Cuauhtémoc','Ciudad de México','06700','México',NULL,1,'2026-11-23 15:37:19','2026-11-23 15:37:19'),
(5,5,'Entrega Custodiada','Av. Álvaro Obregón 185, Col. Roma Norte','S/N',NULL,'Centro','Cuauhtémoc','Ciudad de México','06700','México',NULL,1,'2026-11-23 15:37:41','2026-11-23 15:37:41'),
(6,5,'Entrega Custodiada','Av. Álvaro Obregón 185, Col. Roma Norte','S/N',NULL,'Centro','Cuauhtémoc','Ciudad de México','06700','México',NULL,1,'2026-11-23 15:38:19','2026-11-23 15:38:19'),
(7,5,'Entrega Custodiada','Av. Álvaro Obregón 185, Col. Roma Norte','S/N',NULL,'Centro','Cuauhtémoc','Ciudad de México','06700','México',NULL,1,'2026-11-23 17:46:57','2026-11-23 17:46:57'),
(8,5,'Entrega Custodiada','Av. Álvaro Obregón 185, Col. Roma Norte','S/N',NULL,'Centro','Cuauhtémoc','Ciudad de México','06700','México',NULL,1,'2026-11-23 17:50:40','2026-11-23 17:50:40'),
(9,5,'Entrega Custodiada','Av. Álvaro Obregón 185, Col. Roma Norte','S/N',NULL,'Centro','Cuauhtémoc','Ciudad de México','06700','México',NULL,1,'2026-11-23 18:02:52','2026-11-23 18:02:52'),
(10,5,'Entrega Custodiada','Av. Álvaro Obregón 185, Col. Roma Norte','S/N',NULL,'Centro','Cuauhtémoc','Ciudad de México','06700','México',NULL,1,'2026-11-23 18:11:03','2026-11-23 18:11:03'),
(11,5,'Entrega Custodiada','Av. Álvaro Obregón 185, Col. Roma Norte','S/N',NULL,'Centro','Cuauhtémoc','Ciudad de México','06700','México',NULL,1,'2026-11-23 18:36:47','2026-11-23 18:36:47'),
(12,5,'Entrega Custodiada','Av. Álvaro Obregón 185, Col. Roma Norte','S/N',NULL,'Centro','Cuauhtémoc','Ciudad de México','06700','México',NULL,1,'2026-11-23 18:57:06','2026-11-23 18:57:06');
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
(96,'Pink Floyd','Harvest / Toshiba-EMI CP35-3017','Rock Progresivo','CD',1983,'productos/C3qtAHVSU9LoQlb9SU4WFgz187Cz84fDeAZXu3pt.jpg'),
(97,'Led Zeppelin','Atlantic Records SD 7208','Hard Rock','vinilo',1971,NULL),
(98,'Radiohead','Parlophone / EMI 7243 8 55229 2 4','Art Rock & Alternativo','CD',1997,'productos/Q456A28SU5bldfSxRvIVCataUpdvgzyxOcZnpPhn.webp'),
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
(154,'ACDC','Columbia','Hard Rock','vinilo',1980,'productos/59ku0yscQyCH9x4N0Vdn8q1gpaf5imKSHz2g38Wz.jpg'),
(163,'Frank Sinatra','Reprise Records','Música','vinilo',1968,'productos/1HL0V4cl2WoW0LIBPKatIhbqwyCtABQvnS9sayJm.webp'),
(164,'María José','Sony Music Entertainment México','Música','CD',2013,'productos/jxYSgpkqMiZTYpXe0VHbQTVLXPkmlY1dLdaKBMsT.webp'),
(165,'José José','Sony Music Entertainment México','Música','vinilo',2026,'productos/rmyeOIA6gOL4UNwzJNtfWc5EAEqtIXKxU9fysQqO.webp');
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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `envios`
--

LOCK TABLES `envios` WRITE;
/*!40000 ALTER TABLE `envios` DISABLE KEYS */;
INSERT INTO `envios` VALUES
(1,6,4,'Custodia Climatizada ARCHIVUM','GUA-ARC-261123-Z6UPEZ','entregado','2026-11-26 07:07:33','entregado','2026-11-23 07:07:39','2026-11-23 07:07:42','2026-11-23 07:07:33','2026-11-23 07:07:42'),
(2,7,4,'Custodia Climatizada ARCHIVUM','GUA-ARC-261123-X4UNQ9','entregado','2026-11-26 15:35:16','entregado','2026-11-23 15:35:25','2026-11-23 15:35:28','2026-11-23 15:35:16','2026-11-23 15:35:28'),
(3,8,4,'Custodia Climatizada ARCHIVUM','GUA-ARC-261123-J67RFF','en_preparacion','2026-11-26 15:37:19','preparando',NULL,NULL,'2026-11-23 15:37:19','2026-11-23 15:37:19'),
(4,9,4,'Custodia Climatizada ARCHIVUM','GUA-ARC-261123-4CG7AI','en_preparacion','2026-11-26 15:37:41','preparando',NULL,NULL,'2026-11-23 15:37:41','2026-11-23 15:37:41'),
(5,10,4,'Custodia Climatizada ARCHIVUM','GUA-ARC-261123-USNDUT','en_preparacion','2026-11-26 15:38:19','preparando',NULL,NULL,'2026-11-23 15:38:19','2026-11-23 15:38:19'),
(6,11,4,'Custodia Climatizada ARCHIVUM','GUA-ARC-261123-6GM6SA','en_transito','2026-11-26 17:46:57','en_transito','2026-11-23 17:47:01',NULL,'2026-11-23 17:46:57','2026-11-23 17:47:01'),
(7,12,4,'Custodia Climatizada ARCHIVUM','GUA-ARC-261123-3ZU3CQ','en_camino','2026-11-26 17:50:40','en_transito','2026-11-23 17:50:43',NULL,'2026-11-23 17:50:40','2026-11-23 17:50:45'),
(8,13,4,'Custodia Climatizada ARCHIVUM','GUA-ARC-261123-7EL7BX','entregado','2026-11-26 18:02:53','entregado','2026-11-23 18:02:56','2026-11-23 18:02:59','2026-11-23 18:02:53','2026-11-23 18:02:59'),
(9,14,4,'Custodia Climatizada ARCHIVUM','GUA-ARC-261123-2WZF42','en_camino','2026-11-26 18:11:03','en_transito','2026-11-23 18:11:07',NULL,'2026-11-23 18:11:03','2026-11-23 18:11:08'),
(10,15,4,'Custodia Climatizada ARCHIVUM','GUA-ARC-261123-OJQC0Y','en_camino','2026-11-26 18:36:47','en_transito','2026-11-23 18:36:51',NULL,'2026-11-23 18:36:47','2026-11-23 18:36:56'),
(11,16,4,'Custodia Climatizada ARCHIVUM','GUA-ARC-261123-94XI9M','entregado','2026-11-26 18:57:06','entregado','2026-11-23 18:57:08','2026-11-23 18:57:20','2026-11-23 18:57:06','2026-11-23 18:57:20');
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
) ENGINE=InnoDB AUTO_INCREMENT=269 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `existencias`
--

LOCK TABLES `existencias` WRITE;
/*!40000 ALTER TABLE `existencias` DISABLE KEYS */;
INSERT INTO `existencias` VALUES
(120,71,6,0,1,'2026-11-22 04:15:56','2026-11-23 17:50:32'),
(121,72,5,1,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(122,73,4,0,1,'2026-11-22 04:15:56','2026-11-23 17:46:45'),
(123,74,4,7,1,'2026-11-22 04:15:56','2026-11-25 01:32:58'),
(124,75,5,8,1,'2026-11-22 04:15:56','2026-11-25 01:24:53'),
(125,76,6,13,1,'2026-11-22 04:15:56','2026-11-25 01:24:44'),
(126,77,4,2,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(127,77,5,1,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(128,78,4,4,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(129,78,5,3,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(130,78,6,6,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(131,79,4,3,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(132,79,6,4,1,'2026-11-22 04:15:56','2026-11-23 18:42:10'),
(133,80,5,1,1,'2026-11-22 04:15:56','2026-11-23 18:55:14'),
(134,81,4,1,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(135,82,4,2,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(136,82,5,1,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(137,82,6,4,1,'2026-11-22 04:15:56','2026-11-23 07:36:17'),
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
(149,88,4,0,1,'2026-11-22 04:15:56','2026-11-23 18:10:46'),
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
(164,98,6,6,1,'2026-11-22 04:15:56','2026-11-23 17:51:50'),
(165,99,4,4,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(166,99,5,2,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(167,100,5,5,1,'2026-11-22 04:15:56','2026-11-22 04:15:56'),
(168,101,5,1,1,'2026-11-22 04:15:56','2026-11-23 18:08:35'),
(169,102,4,5,1,'2026-11-22 04:15:56','2026-11-23 15:37:15'),
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
(193,120,6,1,1,'2026-11-22 04:15:57','2026-11-23 18:43:19'),
(194,121,4,1,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(195,122,5,1,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(196,123,6,1,1,'2026-11-22 04:15:57','2026-11-23 15:14:31'),
(197,124,4,4,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(198,124,5,3,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(199,124,6,4,1,'2026-11-22 04:15:57','2026-11-23 15:37:29'),
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
(217,130,6,9,1,'2026-11-22 04:15:57','2026-11-23 15:34:58'),
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
(228,135,5,1,1,'2026-11-22 04:15:57','2026-11-23 15:34:35'),
(229,136,4,1,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(230,136,6,1,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(231,137,4,3,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(232,137,5,2,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(233,137,6,4,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(234,138,4,1,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(235,139,4,1,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(236,139,5,1,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(237,140,4,2,1,'2026-11-22 04:15:57','2026-11-23 14:23:24'),
(238,140,6,1,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(239,141,4,8,1,'2026-11-23 06:01:10','2026-11-23 06:01:10'),
(240,142,4,9,1,'2026-11-23 06:01:10','2026-11-23 06:01:10'),
(241,143,4,9,1,'2026-11-23 06:01:10','2026-11-23 06:01:10'),
(242,144,4,5,1,'2026-11-23 06:01:10','2026-11-23 06:01:10'),
(243,145,5,6,1,'2026-11-23 06:01:10','2026-11-23 06:01:10'),
(244,146,4,4,1,'2026-11-23 06:01:10','2026-11-23 06:01:10'),
(245,147,4,1,1,'2026-11-23 06:01:10','2026-11-23 06:01:10'),
(246,148,4,4,1,'2026-11-23 06:01:10','2026-11-23 15:34:47'),
(247,149,4,0,1,'2026-11-23 06:01:10','2026-11-23 18:36:33'),
(248,150,5,1,1,'2026-11-23 06:01:10','2026-11-23 06:01:10'),
(249,151,4,10,1,'2026-11-23 06:09:54','2026-11-23 06:09:54'),
(250,152,4,14,1,'2026-11-23 06:09:54','2026-11-23 07:36:31'),
(251,153,4,5,1,'2026-11-23 06:09:54','2026-11-23 06:09:54'),
(252,145,4,6,1,'2026-11-23 06:09:54','2026-11-23 06:09:54'),
(253,154,4,1,1,'2026-11-23 06:09:54','2026-11-23 06:09:54'),
(258,150,4,0,1,'2026-11-23 06:16:45','2026-11-23 07:07:27'),
(259,162,4,1,1,'2026-11-23 06:18:55','2026-11-23 15:34:18'),
(260,163,4,0,1,'2026-11-25 21:40:58','2026-11-25 21:40:58'),
(261,163,5,5,1,'2026-11-25 21:40:58','2026-11-25 21:40:58'),
(262,163,6,0,1,'2026-11-25 21:40:58','2026-11-25 21:40:58'),
(263,164,4,0,1,'2026-11-25 21:52:24','2026-11-25 21:52:24'),
(264,164,5,5,1,'2026-11-25 21:52:24','2026-11-25 21:52:24'),
(265,164,6,0,1,'2026-11-25 21:52:24','2026-11-25 21:52:24'),
(266,165,4,6,1,'2026-11-25 22:11:38','2026-11-25 22:11:38'),
(267,165,5,0,1,'2026-11-25 22:11:38','2026-11-25 22:11:38'),
(268,165,6,0,1,'2026-11-25 22:11:38','2026-11-25 22:11:38');
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
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historial_envios`
--

LOCK TABLES `historial_envios` WRITE;
/*!40000 ALTER TABLE `historial_envios` DISABLE KEYS */;
INSERT INTO `historial_envios` VALUES
(1,1,'en_preparacion','Sucursal Central Bellas Artes, CDMX','Preparando Pedido: Verificación de calidad, certificado de autenticidad y preparación en almacén.','2026-11-23 07:07:33'),
(2,1,'en_transito','Centro de Empaque ARCHIVUM, CDMX','Empaque Protegido: Embalaje de alta protección con sello de seguridad e inspección física completada.','2026-11-23 07:07:39'),
(3,1,'en_camino','Unidad de Envío en Ruta','En Camino: Traslado monitoreado hacia la dirección de entrega indicada en tu pedido.','2026-11-23 07:07:41'),
(4,1,'entregado','Domicilio del Cliente','Entregado en Domicilio: Recepción confirmada con entrega de tu producto y Certificado de Autenticidad.','2026-11-23 07:07:42'),
(5,2,'en_preparacion','Sucursal Central Bellas Artes, CDMX','Preparando Pedido: Verificación de calidad, certificado de autenticidad y preparación en almacén.','2026-11-23 15:35:16'),
(6,2,'en_transito','Centro de Empaque ARCHIVUM, CDMX','Empaque Protegido: Embalaje de alta protección con sello de seguridad e inspección física completada.','2026-11-23 15:35:25'),
(7,2,'en_camino','Unidad de Envío en Ruta','En Camino: Traslado monitoreado hacia la dirección de entrega indicada en tu pedido.','2026-11-23 15:35:26'),
(8,2,'entregado','Domicilio del Cliente','Entregado en Domicilio: Recepción confirmada con entrega de tu producto y Certificado de Autenticidad.','2026-11-23 15:35:27'),
(9,2,'entregado','Domicilio del Cliente','Entregado en Domicilio: Recepción confirmada con entrega de tu producto y Certificado de Autenticidad.','2026-11-23 15:35:27'),
(10,2,'entregado','Domicilio del Cliente','Entregado en Domicilio: Recepción confirmada con entrega de tu producto y Certificado de Autenticidad.','2026-11-23 15:35:27'),
(11,2,'entregado','Domicilio del Cliente','Entregado en Domicilio: Recepción confirmada con entrega de tu producto y Certificado de Autenticidad.','2026-11-23 15:35:28'),
(12,2,'entregado','Domicilio del Cliente','Entregado en Domicilio: Recepción confirmada con entrega de tu producto y Certificado de Autenticidad.','2026-11-23 15:35:28'),
(13,3,'en_preparacion','Sucursal Central Bellas Artes, CDMX','Preparando Pedido: Verificación de calidad, certificado de autenticidad y preparación en almacén.','2026-11-23 15:37:19'),
(14,4,'en_preparacion','Sucursal Central Bellas Artes, CDMX','Preparando Pedido: Verificación de calidad, certificado de autenticidad y preparación en almacén.','2026-11-23 15:37:41'),
(15,5,'en_preparacion','Sucursal Central Bellas Artes, CDMX','Preparando Pedido: Verificación de calidad, certificado de autenticidad y preparación en almacén.','2026-11-23 15:38:19'),
(16,6,'en_preparacion','Sucursal Central Bellas Artes, CDMX','Preparando Pedido: Verificación de calidad, certificado de autenticidad y preparación en almacén.','2026-11-23 17:46:57'),
(17,6,'en_transito','Centro de Empaque ARCHIVUM, CDMX','Empaque Protegido: Embalaje de alta protección con sello de seguridad e inspección física completada.','2026-11-23 17:47:01'),
(18,7,'en_preparacion','Sucursal Central Bellas Artes, CDMX','Preparando Pedido: Verificación de calidad, certificado de autenticidad y preparación en almacén.','2026-11-23 17:50:40'),
(19,7,'en_transito','Centro de Empaque ARCHIVUM, CDMX','Empaque Protegido: Embalaje de alta protección con sello de seguridad e inspección física completada.','2026-11-23 17:50:43'),
(20,7,'en_camino','Unidad de Envío en Ruta','En Camino: Traslado monitoreado hacia la dirección de entrega indicada en tu pedido.','2026-11-23 17:50:46'),
(21,8,'en_preparacion','Sucursal Central Bellas Artes, CDMX','Preparando Pedido: Verificación de calidad, certificado de autenticidad y preparación en almacén.','2026-11-23 18:02:53'),
(22,8,'en_transito','Centro de Empaque ARCHIVUM, CDMX','Empaque Protegido: Embalaje de alta protección con sello de seguridad e inspección física completada.','2026-11-23 18:02:56'),
(23,8,'en_camino','Unidad de Envío en Ruta','En Camino: Traslado monitoreado hacia la dirección de entrega indicada en tu pedido.','2026-11-23 18:02:57'),
(24,8,'entregado','Domicilio del Cliente','Entregado en Domicilio: Recepción confirmada con entrega de tu producto y Certificado de Autenticidad.','2026-11-23 18:02:59'),
(25,9,'en_preparacion','Sucursal Central Bellas Artes, CDMX','Preparando Pedido: Verificación de calidad, certificado de autenticidad y preparación en almacén.','2026-11-23 18:11:03'),
(26,9,'en_transito','Centro de Empaque ARCHIVUM, CDMX','Empaque Protegido: Embalaje de alta protección con sello de seguridad e inspección física completada.','2026-11-23 18:11:07'),
(27,9,'en_camino','Unidad de Envío en Ruta','En Camino: Traslado monitoreado hacia la dirección de entrega indicada en tu pedido.','2026-11-23 18:11:08'),
(28,10,'en_preparacion','Sucursal Central Bellas Artes, CDMX','Preparando Pedido: Verificación de calidad, certificado de autenticidad y preparación en almacén.','2026-11-23 18:36:47'),
(29,10,'en_transito','Centro de Empaque ARCHIVUM, CDMX','Empaque Protegido: Embalaje de alta protección con sello de seguridad e inspección física completada.','2026-11-23 18:36:51'),
(30,10,'en_camino','Unidad de Envío en Ruta','En Camino: Traslado monitoreado hacia la dirección de entrega indicada en tu pedido.','2026-11-23 18:36:56'),
(31,11,'en_preparacion','Sucursal Central Bellas Artes, CDMX','Preparando Pedido: Verificación de calidad, certificado de autenticidad y preparación en almacén.','2026-11-23 18:57:06'),
(32,11,'en_transito','Centro de Empaque ARCHIVUM, CDMX','Empaque Protegido: Embalaje de alta protección con sello de seguridad e inspección física completada.','2026-11-23 18:57:08'),
(33,11,'en_camino','Unidad de Envío en Ruta','En Camino: Traslado monitoreado hacia la dirección de entrega indicada en tu pedido.','2026-11-23 18:57:19'),
(34,11,'entregado','Domicilio del Cliente','Entregado en Domicilio: Recepción confirmada con entrega de tu producto y Certificado de Autenticidad.','2026-11-23 18:57:20');
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
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
(17,259,11,'entrada',1,'Importación masiva [COL-2076.010]',NULL,NULL,'2026-11-23 06:18:55',NULL),
(18,261,11,'entrada',5,'Alta de producto [VIN-FS-1968-R51026] por Administrador ARCHIVUM',NULL,NULL,'2026-11-25 21:40:58',NULL),
(19,264,11,'entrada',5,'Alta de producto [CD-MJ-2013-887654156426] por Administrador ARCHIVUM',NULL,NULL,'2026-11-25 21:52:24',NULL),
(20,266,11,'entrada',6,'Alta de producto [VIN-JJ-2026-198029747416] por Administrador ARCHIVUM',NULL,NULL,'2026-11-25 22:11:38',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pagos`
--

LOCK TABLES `pagos` WRITE;
/*!40000 ALTER TABLE `pagos` DISABLE KEYS */;
INSERT INTO `pagos` VALUES
(1,6,'tarjeta','tarjeta',NULL,'PAS-ARC-PENNCGQE',NULL,NULL,3065.00,'aprobado','2026-11-23 07:07:33','2026-11-23 07:07:33','2026-11-23 07:07:33'),
(2,7,'tarjeta','tarjeta',NULL,'PAS-ARC-HEDLTF1A',NULL,NULL,24563.00,'aprobado','2026-11-23 15:35:16','2026-11-23 15:35:16','2026-11-23 15:35:16'),
(3,8,'tarjeta','tarjeta',NULL,'PAS-ARC-DWJ2IGKW',NULL,NULL,1566.55,'aprobado','2026-11-23 15:37:19','2026-11-23 15:37:19','2026-11-23 15:37:19'),
(4,9,'tarjeta','tarjeta',NULL,'PAS-ARC-A7BRVCTC',NULL,NULL,5500.00,'aprobado','2026-11-23 15:37:41','2026-11-23 15:37:41','2026-11-23 15:37:41'),
(5,10,'tarjeta','tarjeta',NULL,'PAS-ARC-67U2OGAH',NULL,NULL,4800.00,'aprobado','2026-11-23 15:38:19','2026-11-23 15:38:19','2026-11-23 15:38:19'),
(6,11,'tarjeta','tarjeta',NULL,'PAS-ARC-INMSNRSV',NULL,NULL,58000.00,'aprobado','2026-11-23 17:46:57','2026-11-23 17:46:57','2026-11-23 17:46:57'),
(7,12,'tarjeta','tarjeta',NULL,'PAS-ARC-4D2UYEEK',NULL,NULL,32000.00,'aprobado','2026-11-23 17:50:40','2026-11-23 17:50:40','2026-11-23 17:50:40'),
(8,13,'tarjeta','tarjeta',NULL,'PAS-ARC-TJX6DH6B',NULL,NULL,4800.00,'aprobado','2026-11-23 18:02:52','2026-11-23 18:02:52','2026-11-23 18:02:52'),
(9,14,'tarjeta','tarjeta',NULL,'PAS-ARC-XAR0WE6U',NULL,NULL,1000.00,'aprobado','2026-11-23 18:11:03','2026-11-23 18:11:03','2026-11-23 18:11:03'),
(10,15,'tarjeta','tarjeta',NULL,'PAS-ARC-CZFOJNCA',NULL,NULL,11494.00,'aprobado','2026-11-23 18:36:47','2026-11-23 18:36:47','2026-11-23 18:36:47'),
(11,16,'tarjeta','tarjeta',NULL,'PAS-ARC-UTQWA3MC',NULL,NULL,11200.00,'aprobado','2026-11-23 18:57:06','2026-11-23 18:57:06','2026-11-23 18:57:06');
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
INSERT INTO `password_reset_tokens` VALUES
('quijadaevelin82@gmail.com','$2y$12$67kacjhkLF85q1Dd/0vuYOea2bcJbty87woMxDq5rwVjRr3I23kBC','2026-11-23 06:41:38');
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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedidos`
--

LOCK TABLES `pedidos` WRITE;
/*!40000 ALTER TABLE `pedidos` DISABLE KEYS */;
INSERT INTO `pedidos` VALUES
(6,'ARC-20261123-NEW0B',5,2,'entregado',3065.00,0.00,3065.00,'2026-11-23 07:07:33','2026-11-23 07:07:42'),
(7,'ARC-20261123-SQZIZ',5,3,'entregado',24563.00,0.00,24563.00,'2026-11-23 15:35:15','2026-11-23 15:35:27'),
(8,'ARC-20261123-VMBDB',5,4,'pagado',1566.55,0.00,1566.55,'2026-11-23 15:37:19','2026-11-23 15:37:19'),
(9,'ARC-20261123-F1NRM',5,5,'pagado',5500.00,0.00,5500.00,'2026-11-23 15:37:41','2026-11-23 15:37:41'),
(10,'ARC-20261123-CSL6L',5,6,'pagado',4800.00,0.00,4800.00,'2026-11-23 15:38:19','2026-11-23 15:38:19'),
(11,'ARC-20261123-VPXCC',5,7,'enviado',58000.00,0.00,58000.00,'2026-11-23 17:46:57','2026-11-23 17:47:01'),
(12,'ARC-20261123-EXKD3',5,8,'enviado',32000.00,0.00,32000.00,'2026-11-23 17:50:40','2026-11-23 17:50:43'),
(13,'ARC-20261123-WARHQ',5,9,'entregado',4800.00,0.00,4800.00,'2026-11-23 18:02:52','2026-11-23 18:02:59'),
(14,'ARC-20261123-1U1YM',5,10,'enviado',1000.00,0.00,1000.00,'2026-11-23 18:11:03','2026-11-23 18:11:07'),
(15,'ARC-20261123-AINDZ',5,11,'enviado',11494.00,0.00,11494.00,'2026-11-23 18:36:47','2026-11-23 18:36:51'),
(16,'ARC-20261123-IOOS0',5,12,'entregado',11200.00,0.00,11200.00,'2026-11-23 18:57:06','2026-11-23 18:57:20');
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
(22,120,'ARC-LIB-1967-001','COA-BIB-2026-014','Ejemplar único 1/1 catalogado en bóveda climatizada (VERY FINE (VF) · PIEZA ÚNICA 1/1).','vendida','2026-11-22 04:15:56','2026-11-23 17:50:40'),
(23,121,'ARC-LIB-1955-002','COA-BIB-2026-019','Ejemplar único 1/1 catalogado en bóveda climatizada (NEAR MINT (NM) · PIEZA ÚNICA 1/1 · ADQUISICIÓN SELECTA).','disponible','2026-11-22 04:15:56','2026-11-22 04:15:56'),
(24,122,'ARC-LIB-1949-003','COA-BIB-2026-022','Ejemplar único 1/1 catalogado en bóveda climatizada (MINT (M) · PIEZA ÚNICA 1/1 · ADQUISICIÓN SELECTA).','vendida','2026-11-22 04:15:56','2026-11-23 17:46:57'),
(25,133,'ARC-LIB-1845-010','COA-BIB-2026-031','Ejemplar único 1/1 catalogado en bóveda climatizada (VERY FINE (VF) · TERROR GÓTICO · GRABADOS XILOGRÁFICOS).','disponible','2026-11-22 04:15:56','2026-11-23 18:55:14'),
(26,134,'ARC-LIB-1955-011','COA-BIB-2026-034','Ejemplar único 1/1 catalogado en bóveda climatizada (MINT (M) · PIEZA ÚNICA 1/1 · CIENCIA FICCIÓN DISTÓPICA).','disponible','2026-11-22 04:15:56','2026-11-22 04:15:56'),
(27,149,'ARC-LIB-1605-015','COA-BIB-2026-045','Ejemplar único 1/1 catalogado en bóveda climatizada (MINT (M) · CLÁSICOS UNIVERSALES · ESTUCHE DE TELA).','vendida','2026-11-22 04:15:56','2026-11-23 18:11:03'),
(28,150,'ARC-LIB-1320-016','COA-BIB-2026-048','Ejemplar único 1/1 catalogado en bóveda climatizada (NEAR MINT (NM) · PIEZA ÚNICA 1/1 · GRAN FOLIO DORÉ).','disponible','2026-11-22 04:15:56','2026-11-22 04:15:56'),
(29,156,'ARC-VIN-1959-001','COA-AUD-2026-101','Ejemplar único 1/1 catalogado en bóveda climatizada (MINT (M) · PIEZA ÚNICA 1/1 · ADQUISICIÓN SELECTA).','disponible','2026-11-22 04:15:56','2026-11-22 04:15:56'),
(30,157,'ARC-VIN-1957-002','COA-AUD-2026-104','Ejemplar único 1/1 catalogado en bóveda climatizada (NEAR MINT (NM) · PIEZA ÚNICA 1/1 · ADQUISICIÓN SELECTA).','disponible','2026-11-22 04:15:56','2026-11-23 06:39:53'),
(31,159,'ARC-VIN-1969-004','COA-AUD-2026-112','Ejemplar único 1/1 catalogado en bóveda climatizada (MINT (M) · PIEZA ÚNICA 1/1 · ADQUISICIÓN SELECTA).','disponible','2026-11-22 04:15:56','2026-11-22 04:15:56'),
(32,168,'ARC-VIN-1990-001','COA-MUS-2026-101','Ejemplar único 1/1 catalogado en bóveda climatizada (NEAR MINT (NM) · PIEZA ÚNICA 1/1).','disponible','2026-11-22 04:15:56','2026-11-23 18:08:35'),
(33,179,'ARC-VIN-2024-002','COA-MUS-2026-102','Ejemplar único 1/1 catalogado en bóveda climatizada (MINT (M) · PIEZA ÚNICA 1/1).','disponible','2026-11-22 04:15:57','2026-11-22 04:15:57'),
(34,183,'ARC-MEM-1928-001','COA-NOT-2026-301','Ejemplar único 1/1 catalogado en bóveda climatizada (COA NOTARIADO · PIEZA ÚNICA 1/1).','disponible','2026-11-22 04:15:57','2026-11-22 04:15:57'),
(35,184,'ARC-MEM-1940-004','COA-NOT-2026-320','Ejemplar único 1/1 catalogado en bóveda climatizada (MINT (M) · PIEZA ÚNICA 1/1 · ADQUISICIÓN SELECTA).','disponible','2026-11-22 04:15:57','2026-11-22 04:15:57'),
(36,185,'ARC-MEM-1910-005','COA-NOT-2026-335','Ejemplar único 1/1 catalogado en bóveda climatizada (MINT (M) · PIEZA ÚNICA 1/1 · ADQUISICIÓN SELECTA).','disponible','2026-11-22 04:15:57','2026-11-22 04:15:57'),
(37,186,'ARC-MEM-1934-002','COA-NOT-2026-309','Ejemplar único 1/1 catalogado en bóveda climatizada (MINT (M) · COA NOTARIADO · ADQUISICIÓN SELECTA).','vendida','2026-11-22 04:15:57','2026-11-22 04:15:57'),
(38,193,'ARC-COL-1933-001','COA-COL-2026-001','Ejemplar único 1/1 catalogado en bóveda climatizada (MINT (M) · MONEDA HISTÓRICA RARA).','disponible','2026-11-22 04:15:57','2026-11-23 18:43:19'),
(39,194,'ARC-COL-1794-002','COA-COL-2026-002','Ejemplar único 1/1 catalogado en bóveda climatizada (VERY FINE (VF) · PRIMER DÓLAR FEDERAL).','disponible','2026-11-22 04:15:57','2026-11-22 04:15:57'),
(40,195,'ARC-COL-1344-003','COA-COL-2026-003','Ejemplar único 1/1 catalogado en bóveda climatizada (NEAR MINT (NM) · PIEZA ÚNICA MEDIEVAL).','disponible','2026-11-22 04:15:57','2026-11-22 04:15:57'),
(41,196,'ARC-COL-0723-004','COA-COL-2026-004','Ejemplar único 1/1 catalogado en bóveda climatizada (MINT (M) · CALIFATO OMEYA).','disponible','2026-11-22 04:15:57','2026-11-23 15:14:31'),
(42,223,'ARC-FIG-1992-005','COA-FIG-2026-005','Ejemplar único 1/1 catalogado en bóveda climatizada (VERY FINE (VF) · ARTE POPULAR INDÍGENA).','disponible','2026-11-22 04:15:57','2026-11-22 04:15:57'),
(43,245,'ARC-DIS-2026-006','COA-BIB-2027-006','Alta por importación masiva Excel/CSV.','disponible','2026-11-23 06:01:10','2026-11-23 06:01:10'),
(44,247,'BAS-1/1-CS2026','COA-COL-2026-002','Alta por importación masiva Excel/CSV.','vendida','2026-11-23 06:01:10','2026-11-23 18:36:47'),
(45,248,'COL-SID-ST1616','COA-COL-2026-003','Alta por importación masiva Excel/CSV.','disponible','2026-11-23 06:01:10','2026-11-23 06:01:10'),
(46,253,'ARC-DIS-2026-005','COA-BIB-2027-005','Alta por importación masiva Excel/CSV.','disponible','2026-11-23 06:09:54','2026-11-23 06:09:54'),
(51,258,'COL-SID-ST1678','COA-COL-2026-010','Alta por importación masiva Excel/CSV.','vendida','2026-11-23 06:16:45','2026-11-23 07:07:33'),
(52,259,'COL-TOP-MLB52-MM311-SGC89','COA-COL-2026-015','Alta por importación masiva Excel/CSV.','disponible','2026-11-23 06:18:55','2026-11-23 15:34:18');
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
) ENGINE=InnoDB AUTO_INCREMENT=166 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES
(71,4,'Cien Años de Soledad (1ª Edición Sudamericana)','Pieza Única (1/1) custodiada en Almacén General: Tiraje inicial de Buenos Aires (Mayo 1967) con prueba de galera firmada. Cubierta original con galeón en la selva diseñada por Iris Alba.','productos/9b2xdCyABitnYZYoAAhn8J5OjYrrB4NGxAvBBSJ1.jpg',32000.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-23 06:57:32',NULL),
(72,4,'Pedro Páramo (Edición Príncipe FCE)','Pieza Única (1/1) custodiada en Sucursal Coyoacán: Primera edición de 1955 en la Colección Letras Mexicanas N.º 19 con firma autógrafa de Juan Rulfo en guarda anterior.',NULL,44500.00,0,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-23 06:57:19',NULL),
(73,4,'El Aleph (Ejemplar Dedicado por el Autor)','Pieza Única (1/1) custodiada en Sucursal Central Bellas Artes: Primera edición impresa por Editorial Losada (1949) con dedicatoria manuscrita de Jorge Luis Borges.','productos/gYa5QlrlkdwvAIGMlq3ldGpHoZuGe8ygDtIKpg2b.jpg',58000.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-23 06:56:33',NULL),
(74,4,'El Laberinto de la Soledad (1ª Edición Cuadernos Americanos)','Stock Masivo en Sucursal Central Bellas Artes: Edición de tiraje múltiple impresa en los talleres de Cuadernos Americanos en papel Chamois. Permite adquirir múltiples ejemplares.','productos/qYnCho0DwirzbkIQJ1PrhVvyhXI9QRBZWdW6kcXG.jpg',16900.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-23 06:56:19',NULL),
(75,4,'Aura (Edición Conmemorativa Biblioteca Era Coyoacán)','Stock Masivo en Sucursal Coyoacán: Tiraje regular encuadernado en tela editorial por Ediciones Era. Disponible para compra de varias unidades sin bloqueo exclusivo.','productos/aVTFPNupBVuqv8AqdLxNykqPMmHV8d0R3tME0hoB.webp',6800.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-23 06:56:02',NULL),
(76,4,'Rayuela (Tiraje Clásico Editorial Sudamericana)','Stock Masivo en Almacén General: Edición conmemorativa de tiraje amplio con tablero de dirección de lectura. Existencias múltiples disponibles para despacho inmediato.','productos/TCPLCpmh1SnaGKU280WLmOfcZPFzSCiWiP89POg9.webp',8400.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-23 06:55:47',NULL),
(77,4,'Piedra de Sol y Obra Poética (Primera Edición 1957)','Pieza de Poesía de Alto Valor Curatorial: Primera edición del poema circular de 584 versos endecasílabos numerados. Ejemplar de colección impreso en papel especial.','productos/mJvRjktDKa0jbFxCDpRi4FhhQU5o2cvndCFB5xZZ.jpg',24500.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-23 06:55:32',NULL),
(78,4,'Visión de los Vencidos: Relaciones Indígenas de la Conquista','Obra Fundamental de Historia: Testimonios de los informantes de Sahagún traducidos del náhuatl por Ángel María Garibay con textos y notas de Miguel León-Portilla.',NULL,9800.00,0,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-23 06:55:09',NULL),
(79,4,'La Estructura de las Revoluciones Científicas (1ª Edición FCE)','Obra Cumbre de Ciencia y Epistemología: Traducción clásica al español del tratado pionero de Thomas Kuhn sobre los cambios de paradigma en la investigación científica.','productos/7HzDLydxgzIHkTURYQu6UJMifjJKndEvoRs0aUPs.jpg',11200.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-23 06:54:25',NULL),
(80,4,'Narraciones Extraordinarias & El Cuervo (Edición Ilustrada 1945)','Pieza de Terror Gótico en Bóveda Coyoacán: Traducción canónica de Charles Baudelaire y notas de Julio Cortázar. Encuadernación en tela con grabados xilográficos originales.','productos/p9Cf6Nskf3dQFBA3Vr6G1QtqVH6HutScpRqC1q8b.webp',18500.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-23 06:54:12',NULL),
(81,4,'Fahrenheit 451 (Primera Edición en Español Minotauro 1955)','Pieza Única (1/1) en Sucursal Central Bellas Artes: Primera edición hispanoamericana traducida por Francisco Porrúa con sobrecubierta tipográfica original intacta.','productos/TLYiBzkepp04SsB7cHRFLORSJA1IGPiPXW1O7BH9.jpg',26000.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-23 06:53:18',NULL),
(82,4,'Crónicas Marcianas (Edición Príncipe con Prólogo de Borges 1955)','Stock Masivo en Almacén General: Célebre edición con prólogo de Jorge Luis Borges reflexionando sobre la soledad y la condición humana en el cosmos.','productos/DDJgwHy3Hd6EbSR0LA67OVwBhiLBakis3xI3bHm9.jpg',21500.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-23 06:52:59',NULL),
(83,4,'El Señor de los Anillos: La Comunidad del Anillo (1ª Edición Minotauro 1977)','Obra Cumbre de Fantasía Épica: Primera traducción autorizada al español con sobrecubierta ilustrada y mapa desplegable de la Tierra Media en perfecto estado.','productos/Yp21v7nB63v6WboOV3gMWawFicNu2LvUSVwstArn.jpg',300.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-23 06:52:29',NULL),
(84,4,'El Nombre de la Rosa (1ª Edición en Castellano Lumen 1982)','Pieza Selecta de Misterio & Semiótica: Primera tirada en castellano con sobrecubierta original de diseño medieval. Clásico del thriller histórico monástico.','productos/X4kWplGRxzkWUJRimbhbzdRoKhLkCuFO6MKjm3Ja.jpg',250.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-23 06:52:22',NULL),
(85,4,'Estudio en Escarlata & El Sabueso de los Baskerville (Edición Anotada 1928)','Clásico de Novela Policíaca y Misterio: Primera traducción de la Colección Misterio de Editorial Juventud con grabados e ilustraciones de época.','productos/w4o5WSAutGgYS2vUk76XEpDfmhmw7q6VP7ApUm9R.webp',280.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-23 06:52:40',NULL),
(86,4,'Así Habló Zaratustra: Un Libro para Todos y para Nadie (Edición Aguilar 1932)','Encuadernación de Lujo en Piel: Edición de la célebre Colección Crisol con cantos dorados y papel biblia. Obra fundamental de la filosofía continental moderna.','productos/w3PmtPgXy1u6PJEWZiZEP1nHRm9i4COxdwSt2aYz.jpg',380.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-23 06:52:15',NULL),
(87,4,'El Banquete y Fedón (Traducción Clásica Bibliotheca Scriptorum 1944)','Filosofía Griega Clásica: Edición bilingüe griego-español en papel de hilo fabricado especialmente para la imprenta universitaria de la UNAM.','productos/zeytArWzUEWrUhsrWamTQN6XnDoEdGvcF0ib5c2T.jpg',360.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-23 06:52:05',NULL),
(88,4,'Don Quijote de la Mancha (Edición Conmemorativa Facsimilar RAE 1947)','Pieza Cumbre de Clásicos Universales: Edición monumental conmemorativa del IV Centenario de Cervantes en dos volúmenes en papel de tina con estuche de tela inglesa.','productos/dgIYo3PHjBqj7MFrwT0MfScUdOzwhYJfXeS6yHN5.jpg',1000.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-23 06:51:40',NULL),
(89,4,'La Divina Comedia (Edición Bilingüe con Grabados de Gustave Doré 1921)','Gran Folio Monumental: Encuadernado en piel gofrada con 135 láminas calcográficas de Gustave Doré sobre el Infierno, Purgatorio y Paraíso.','productos/RoHPqdVCnxcwTtWnv4jkG8HHqvJ1XLqrSnNG9eSM.webp',500.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-23 06:51:49',NULL),
(90,4,'Confieso que he Vivido: Memorias (Primera Edición Seix Barral 1974)','Biografía & Crónica Esencial: Primera edición póstuma de las memorias de Pablo Neruda preparada por Matilde Urrutia. Colección Biblioteca Breve.',NULL,15600.00,0,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-23 06:50:38',NULL),
(91,4,'Historia Verdadera de la Conquista de la Nueva España (Edición Crítica 1960)','Crónica Fundamental de Indias: Edición canónica de la Colección Sepan Cuantos N.º 5 con estudio y mapas facsimilares de la ruta de Cortés.',NULL,8900.00,0,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-23 06:50:43',NULL),
(92,5,'Kind of Blue (Mono 1st Press 6-Eye)','Pieza Única (1/1) en Sucursal Central Bellas Artes: Matriz original XLP 47324-1A con etiqueta Columbia «6-eye» deep groove y firma certificada.',NULL,18500.00,0,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-23 06:50:48',NULL),
(93,5,'Blue Train (Plastylite RVG Ear Press)','Pieza Única (1/1) en Sucursal Coyoacán: Grabado por Rudy Van Gelder con sello «ear» en surco muerto y dirección 47 West 63rd NYC.',NULL,27500.00,0,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-23 06:50:54',NULL),
(94,5,'A Love Supreme (Gatefold 1st Impulse! Mono)','Stock Masivo en Sucursal Central Bellas Artes: Carpeta desplegable laminada con estampado VAN GELDER. Existencias múltiples disponibles.',NULL,16200.00,0,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-23 06:51:00',NULL),
(95,5,'Abbey Road (UK 1st Press Misaligned Apple)','Pieza Única (1/1) en Almacén General: Prensado británico matriz YEX 749-2 / 750-1 con manzana desalineada en contraportada.',NULL,22900.00,0,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-23 06:51:28',NULL),
(96,5,'The Dark Side of the Moon (1st Japan Black Triangle CD)','Stock Masivo en Sucursal Coyoacán: Edición en Disco Compacto prensada por Toshiba-EMI en Japón (CP35-3017). Permite adquirir múltiples unidades.','productos/C3qtAHVSU9LoQlb9SU4WFgz187Cz84fDeAZXu3pt.jpg',530.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-23 06:51:54',NULL),
(97,5,'Led Zeppelin IV (Prensado Audiófilo Atlantic 180g)','Stock Masivo en Almacén General: Reedición audiófila de tiraje regular en vinilo virgen de 180 gramos masterizado desde las cintas analógicas.',NULL,5900.00,0,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-23 06:51:34',NULL),
(98,5,'OK Computer (Collector\'s Edition 2-CD Parlophone)','Edición Maestra en Disco Compacto: Edición doble de 1997 con estuche digipak de colección, libreto con ilustraciones de Stanley Donwood y lados B masterizados.','productos/Q456A28SU5bldfSxRvIVCataUpdvgzyxOcZnpPhn.webp',4800.00,1,0,NULL,NULL,'2026-11-22 04:15:56','2026-11-23 06:48:15',NULL),
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
(110,5,'Buena Vista Social Club (Edición CD Remasterizada World Circuit)','Obra Fonográfica Esencial en Disco Compacto: Grabaciones de La Habana bajo la producción de Ry Cooder, masterizado digital con folleto historiográfico de 48 páginas.',NULL,3950.00,0,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 06:47:46',NULL),
(111,6,'Carta Manuscrita Bellas Artes (1928)','Pieza Única (1/1) en Sucursal Central Bellas Artes: Pluma estilográfica con tinta ferrogálica original sobre papel membretado con COA notariado.',NULL,9400.00,0,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 06:47:36',NULL),
(112,6,'Manuscrito Autógrafo Casa Azul Coyoacán (1940)','Pieza Única (1/1) en Sucursal Coyoacán: Misiva autógrafa en papel de algodón fechada en Villa Coyoacán con sello lacrado original.',NULL,36800.00,0,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 06:47:31',NULL),
(113,6,'Medalla Conmemorativa del Centenario (1910)','Pieza Única (1/1) en Almacén General: Medalla oficial en plata ley .925 acuñada por la Casa de Moneda en 1910 con estuche de terciopelo original.',NULL,29500.00,0,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 06:47:25',NULL),
(114,6,'Fotografía en Plata sobre Gelatina Firmada (1934)','Copia de época (vintage print) revelada en cuarto oscuro por el estudio original, con sello húmedo al reverso y paspartú libre de ácido.',NULL,41000.00,0,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 06:47:22',NULL),
(115,6,'Partitura Autógrafa de Estudio en Sol Menor (1912)','Lote limitado en Sucursal Central Bellas Artes: 2 cuadernos musicales de 12 folios con anotaciones de dinámica a lápiz. Conservado en carpeta museográfica.',NULL,21500.00,0,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 06:47:18',NULL),
(116,6,'Programa Inaugural Palacio de Bellas Artes (1934)','Stock Masivo en Sucursal Central Bellas Artes: Folleto conmemorativo oficial de la inauguración del Palacio de Bellas Artes en septiembre de 1934.',NULL,7800.00,0,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 06:47:06',NULL),
(117,6,'Litografía Taller de Gráfica Popular Coyoacán (1954)','Stock Masivo en Sucursal Coyoacán: Grabado en linóleo de tiraje amplio impreso sobre papel de algodón en Coyoacán. Disponible en múltiples unidades.',NULL,5400.00,0,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 06:47:11',NULL),
(118,6,'Kit de Conservación Museográfica & Estuche Antiácido','Stock Masivo en Almacén General: Set profesional de fundas de polipropileno grado archivo, guantes de nitrilo y caja libre de ácido para libros y vinilos.',NULL,3200.00,0,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 06:46:54',NULL),
(119,6,'Estatua Evangelion Test Type-01 (Edición Limitada Kaiyodo 1997)','Figuras Coleccionables (Cultura Pop & Anime): Escultura en resina pintada a mano supervisada por Gainax Japón. Incluye pedestal con número de serie de tiraje.',NULL,18900.00,0,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 06:46:47',NULL),
(120,6,'Double Eagle de 1933 (EE. UU.)','Moneda de oro de $20 que el gobierno ordenó fundir durante la Gran Depresión. Pieza de valor histórico incalculable.','productos/Double-Eagle-de-1933.jpg',18872250.00,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 05:55:47',NULL),
(121,6,'Dólar Flowing Hair de 1794 (EE. UU.)','El primer dólar de plata acuñado por el gobierno federal estadounidense.','productos/Dolar-Flowing-Hair-de-1794.jpg',175000.00,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 05:55:47',NULL),
(122,6,'Florín de Eduardo III de 1344 (Inglaterra)','Legendaria moneda de oro medieval de la cual solo se conocen tres ejemplares en todo el mundo.','productos/Florin-de-Eduardo-III-de-1344.jpg',7000000.00,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 05:55:47',NULL),
(123,6,'Dinar de Oro Omeya del año 723 d.C.','Acuñada en la región de la actual Arabia Saudita con oro proveniente de una mina del propio Califa.','productos/Dinar-de-Oro-Omeya-del-año-723.jpg',95600000.00,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 05:55:47',NULL),
(124,6,'Monedas de 8 Reales (Época Colonial / Spanish Dollars)','Conocidas mundialmente como columnarios o piezas de 8, fueron la primera moneda de uso internacional.','productos/Monedas-Reales.jpg',5500.00,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 05:55:47',NULL),
(125,6,'Centenario de Oro (50 Pesos)','Contiene 37.5 gramos de oro puro y presenta el clásico diseño del Ángel de la Independencia.','productos/Centenario-de-Oro.jpg',92250.00,0,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 06:46:37',NULL),
(126,6,'Moneda de Plata Libertad (Onza Troy)','Una de las monedas de inversión (bullion) de plata más hermosas y cotizadas del mundo.','productos/Moneda-Libertad.jpg',1250.00,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 05:55:47',NULL),
(127,6,'Monedas Conmemorativas de 20 Pesos (Familia C y C1)','Piezas recientes conmemorativas (como la Fundación de Veracruz o el Bicentenario de la Independencia).','productos/Monedas-Conmemorativas.jpg',100.00,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 05:55:47',NULL),
(128,6,'Familia AA y A (Billetes Desmonetizados)','Billetes antiguos impresos por la American Bank Note Company y de fabricación nacional.','productos/Familia-AAA.jpg',325.00,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 05:55:47',NULL),
(129,6,'Pop Mart Labubu (Designer Toy Coleccionable)','Figura de diseñador caracterizada por orejas puntiagudas, cuerpo peludo, sonrisa traviesa con 9 dientes y rostro de vinilo.','productos/Pop-Mart.jpg',1100.00,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 05:55:47',NULL),
(130,6,'Funko Pop! Edición Especial Coleccionable','Figura de vinilo con cabeza grande y diseño estilizado de cultura pop.','productos/Funko-Pop.jpg',1075.00,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 05:55:47',NULL),
(131,6,'Lego Sets Temáticos y de Colección (Edición Especial)','Set de construcción de edición de coleccionista con miles de piezas y minifiguras exclusivas.','productos/lego.jpg',19999.00,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 05:55:47',NULL),
(132,6,'Estatua Anubis Egipcia con Recipiente Base','Estatua decorativa de acabado negro con detalles dorados e iconografía del antiguo Egipto.','productos/Estatua-Anubis.jpg',773.08,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 05:55:47',NULL),
(133,6,'Figura de Cerámica \"Storyteller\" de Cochiti','Pieza artesanal tradicional de Snowflake Flower, ca. 1992, narrativa tradicional en arcilla.','productos/Figura-ceramica.jpg',4914.54,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 05:55:47',NULL),
(134,6,'Exquisito Joyero de Estilo Egipcio Decorado con Anubis','Joyero ornamental decorado con motivos y deidades de la mitología egipcia antigua.','productos/Exquisito-Joyero.jpg',600.49,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 05:55:47',NULL),
(135,6,'Figura Dragon Ball Z Super Saiyan Goku (Banpresto Japón 1993)','Figuras Coleccionables (Cultura Pop & Anime): Figura vintage con caja sellada original de Toei Animation Japón. Conservada en acrílico anti-UV de coleccionista.','productos/pURPIuY5YUTPvNs5V0NuCOACVAXYA0rdg10gsJVB.jpg',14200.00,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 06:46:16',NULL),
(136,6,'Robot Mecánico de Hojalata Space Trooper (Japón Vintage 1965)','Juguetes y Objetos Manufacturados: Juguete mecánico a cuerda en hojalata litografiada manufacturado por Yonezawa Toys Japón. Mecanismo de marcha y chispa plenamente funcional.',NULL,21800.00,0,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 06:46:04',NULL),
(137,6,'Billete Revolucionario 10 Pesos Ejército Constitucionalista (1914)','Monedas y Billetes Históricos: Emisión oficial del Gobierno Provisional de México firmada en Monclova. Papel moneda histórico en estado sin circular (UNC).','productos/paiPRw6iKSvFPyK4TkoNDxvKiIMdvW2cGgNfRCgz.jpg',500.00,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 06:45:40',NULL),
(138,6,'Moneda Virreinal 8 Reales Columnario de Plata (1768)','Monedas y Billetes Históricos: Moneda de 8 Reales acuñada a volante en la Real Casa de Moneda de México bajo Carlos III. Plata pura con pátina histórica intocada.',NULL,38500.00,0,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 06:45:21',NULL),
(139,6,'Consola Nintendo Game & Watch Ball (Primera Edición Japón 1980)','Coleccionables de Videojuegos: El primer sistema portátil creado por Gunpei Yokoi para Nintendo (Modelo AC-01). En caja de colección con manual y batería sellada.','productos/Ov8dBeHpr9qzg7jBmxx6rzvIFBVrcyrIfVdQyYNz.webp',2500.00,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 06:45:57',NULL),
(140,6,'Cartucho The Legend of Zelda (Gold Cartridge NES 1987 Sellado)','Coleccionables de Videojuegos: Cartucho dorado original para Nintendo Entertainment System con batería de respaldo interna y sello pericial de graduación Wata/VGA.','productos/lMStJFZVoRaFgiGxdH3QPglfHsNjnBzPESNRtTbh.avif',2600.00,1,0,NULL,NULL,'2026-11-22 04:15:57','2026-11-23 07:35:42',NULL),
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
(152,4,'Cómo ser youtuber','Este libro mezcla una historia biográfica con consejos para quienes quieren empezar en redes sociales. Exi relata cómo pasó de ser un adolescente sin motivación en los estudios a convertirse en un creador de contenido reconocido en España y Latinoamérica','productos/eYHeYy3Fgl2TSMLSPkz6QpWRG9ltOtsi83r3BB5R.jpg',200.00,1,0,NULL,NULL,'2026-11-23 06:09:54','2026-11-23 07:36:47',NULL),
(153,4,'Auronplay el libro','Con su característico estilo directo, sarcástico y sin filtros, repasa su vida desde su infancia en Badalona, su paso por la escuela, sus primeras amistades y fiestas, hasta sus inicios en internet','productos/vHKpgYflGJSIj0WAnRHtKR7YWsMGX92LxWe7nfou.jpg',138.00,1,0,NULL,NULL,'2026-11-23 06:09:54','2026-11-23 06:39:34',NULL),
(154,5,'Back in black','Séptimo álbum de estudio de la banda australiana AC/DC y el primero grabado con el vocalista Brian Johnson tras el fallecimiento de Bon Scott. Producido por Robert John «Mutt» Lange, este disco emblemático de portada completamente negra en señal de luto incluye 10 clásicos imprescindibles del rock','productos/59ku0yscQyCH9x4N0Vdn8q1gpaf5imKSHz2g38Wz.jpg',938.00,1,0,NULL,NULL,'2026-11-23 06:09:54','2026-11-23 06:39:34',NULL),
(162,6,'Micky Mantle','Considerada el \"Santo Grial\" del coleccionismo deportivo de posguerra. Pertenece a la última tirada de 1952 cuya mercancía no vendida fue arrojada al océano Atlántico por la propia compañía, lo que provocó una escasez extrema.','productos/9x8MOv2UAXphugNEuTxIaW70fAAsbMqhIeQUAvsr.jpg',12600000.00,1,0,NULL,NULL,'2026-11-23 06:18:55','2026-11-23 06:39:35',NULL),
(163,5,'Rain In My Heart','Este prensado analógico de 12 pulgadas en formato estereofónico cuenta con los arreglos y la producción del maestro Don Costa. Una pieza clásica de vocal jazz y pop tradicional que reúne diez interpretaciones emblemáticas, incluyendo \"Rain In My Heart\", \"Cycles\", \"Little Green Apples\" y \"Gentle On My Mind\".','productos/1HL0V4cl2WoW0LIBPKatIhbqwyCtABQvnS9sayJm.webp',150.00,1,0,NULL,NULL,'2026-11-25 21:40:58','2026-11-25 21:40:58',NULL),
(164,5,'El Amor Manda','Edición especial en CD del cuarto álbum de estudio de la cantante mexicana María José, De Noche (reedición de 14 canciones lanzada bajo el sello Sony Music / OCESA Seitrack). Incluye el exitoso tema principal de telenovela «El Amor Manda», además de éxitos como «Tú Ya Sabes A Mí», «Prefiero Ser Su Amante» y «El Brillo De La Luna». Disco compacto en formato Jewel Case original.','productos/jxYSgpkqMiZTYpXe0VHbQTVLXPkmlY1dLdaKBMsT.webp',150.00,1,0,NULL,NULL,'2026-11-25 21:52:24','2026-11-25 21:52:24',NULL),
(165,5,'Gotas de Fuego','Edición especial de colección en vinilo LP color dorado traslúcido con efecto humo (Mystik Sparkle) del álbum recopilatorio Gotas de Fuego de José José, lanzado en 2026 por Sony Music México. Reúne 12 baladas emblemáticas del Príncipe de la Canción, incluyendo «Gotas de Fuego», «Insaciable Amante», «Amor Para los Dos», «Farolero», «Te Recordaré», «Cirano», «Infiel» y «New York, New York (En Inglés)».','productos/rmyeOIA6gOL4UNwzJNtfWc5EAEqtIXKxU9fysQqO.webp',899.00,1,0,NULL,NULL,'2026-11-25 22:11:38','2026-11-25 22:11:38',NULL);
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
(4,74,'Preventa Exclusiva VIP: Programa Inaugural Palacio de Bellas Artes (Edición Especial)','Acceso anticipado exclusivo para Clientes Frecuentes (5+ compras) antes de la apertura oficial al público general. Incluye carpeta museográfica conmemorativa.','porcentaje',15.00,1,'2026-11-20 04:15:57','2026-11-24 04:15:57','PREVENTA ACTIVA · ACCESO ANTICIPADO VIP','2026-11-20','2026-12-02',1,'2026-11-22 04:15:57','2026-11-25 01:23:45'),
(5,75,'Apertura Oficial: A Love Supreme (Lanzamiento Audiófilo 180g)','Promoción desbloqueada en Fase de Apertura para todos los clientes comunes de la tienda. Prensado audiófilo con carpeta desplegable laminada.','porcentaje',12.00,1,'2026-11-17 04:15:57','2026-11-21 04:15:57','EDICIÓN ESPECIAL · APERTURA PÚBLICA','2026-11-17','2026-11-27',1,'2026-11-22 04:15:57','2026-11-25 01:23:45'),
(6,76,'Edición Especial: Led Zeppelin IV (Prensado Audiófilo 180g)','Lanzamiento especial en Fase de Preventa Anticipada para Clientes Frecuentes VIP (5+ compras). Masterizado desde las cintas analógicas originales.','monto',900.00,1,'2026-11-21 04:15:57','2026-11-25 04:15:57','EDICIÓN ESPECIAL · PREVENTA VIP','2026-11-21','2026-12-04',1,'2026-11-22 04:15:57','2026-11-25 01:23:45');
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recuperacion_contrasenas`
--

LOCK TABLES `recuperacion_contrasenas` WRITE;
/*!40000 ALTER TABLE `recuperacion_contrasenas` DISABLE KEYS */;
INSERT INTO `recuperacion_contrasenas` VALUES
(1,13,'2e8a1ccd1f3386e968d0e0048e78d23886a223a4529d328ae88847bc4bdab087','2026-11-23 07:41:38',NULL,'2026-11-23 06:41:38','2026-11-23 06:41:38');
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
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservas_stock`
--

LOCK TABLES `reservas_stock` WRITE;
/*!40000 ALTER TABLE `reservas_stock` DISABLE KEYS */;
INSERT INTO `reservas_stock` VALUES
(1,157,NULL,5,1,'2026-11-23 06:25:40','expirada','2026-11-23 06:20:40','2026-11-23 06:39:53'),
(2,258,6,5,1,NULL,'confirmada','2026-11-23 07:07:27','2026-11-23 07:07:33'),
(3,237,NULL,5,1,'2026-11-23 07:40:09','expirada','2026-11-23 07:35:09','2026-11-23 14:23:24'),
(4,137,NULL,5,1,'2026-11-30 07:36:14','liberada','2026-11-23 07:36:14','2026-11-23 07:36:17'),
(5,250,7,5,1,NULL,'confirmada','2026-11-23 07:36:31','2026-11-23 15:35:16'),
(6,196,NULL,5,1,'2026-11-23 14:53:05','expirada','2026-11-23 14:48:05','2026-11-23 15:14:31'),
(7,259,NULL,5,1,'2026-11-23 15:39:14','liberada','2026-11-23 15:34:14','2026-11-23 15:34:18'),
(8,228,7,5,1,NULL,'confirmada','2026-11-23 15:34:35','2026-11-23 15:35:16'),
(9,246,7,5,1,NULL,'confirmada','2026-11-23 15:34:47','2026-11-23 15:35:16'),
(10,217,7,5,1,NULL,'confirmada','2026-11-23 15:34:58','2026-11-23 15:35:16'),
(11,125,7,5,1,NULL,'confirmada','2026-11-23 15:35:07','2026-11-23 15:35:16'),
(12,169,8,5,1,NULL,'confirmada','2026-11-23 15:37:15','2026-11-23 15:37:19'),
(13,199,9,5,1,NULL,'confirmada','2026-11-23 15:37:29','2026-11-23 15:37:41'),
(14,164,10,5,1,NULL,'confirmada','2026-11-23 15:37:53','2026-11-23 15:38:19'),
(15,122,11,5,1,NULL,'confirmada','2026-11-23 17:46:45','2026-11-23 17:46:57'),
(16,120,12,5,1,NULL,'confirmada','2026-11-23 17:50:32','2026-11-23 17:50:40'),
(17,168,NULL,5,1,'2026-11-23 17:56:39','expirada','2026-11-23 17:51:39','2026-11-23 18:02:19'),
(18,164,13,5,1,NULL,'confirmada','2026-11-23 17:51:50','2026-11-23 18:02:52'),
(19,168,NULL,5,1,'2026-11-23 18:08:35','expirada','2026-11-23 18:03:35','2026-11-23 18:08:35'),
(20,149,14,5,1,NULL,'confirmada','2026-11-23 18:10:46','2026-11-23 18:11:03'),
(21,247,15,5,1,NULL,'confirmada','2026-11-23 18:36:33','2026-11-23 18:36:47'),
(22,193,NULL,5,1,'2026-11-23 18:43:20','expirada','2026-11-23 18:38:20','2026-11-23 18:43:19'),
(23,132,16,5,1,NULL,'confirmada','2026-11-23 18:42:10','2026-11-23 18:57:06'),
(24,133,NULL,5,1,'2026-11-23 18:52:57','expirada','2026-11-23 18:47:57','2026-11-23 18:55:14'),
(25,123,NULL,5,1,'2026-12-02 01:24:34','liberada','2026-11-25 01:24:34','2026-11-25 01:24:39'),
(26,125,NULL,5,1,'2026-12-02 01:24:44','activa','2026-11-25 01:24:44','2026-11-25 01:24:44'),
(27,124,NULL,5,1,'2026-12-02 01:24:53','activa','2026-11-25 01:24:53','2026-11-25 01:24:53'),
(28,123,NULL,5,1,'2026-12-02 01:32:58','activa','2026-11-25 01:32:58','2026-11-25 01:32:58');
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
('4IzWmrpU6pk2OcmXpmzvO6SYYBoUlWf2h79ef4Kw',NULL,'172.19.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/158.0.0.0 Safari/537.36 Edg/158.0.0.0','eyJfdG9rZW4iOiJXb1BtRXlnWU1kcTRuV09zb0hVdFpnZzJkMDkwdmZVT3JvU2UxUzNtIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwIiwicm91dGUiOiJwdWJsaWMuaG9tZSJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19',1795641281),
('xvHo65ton4Zt5VFKAlqnzQw2Saly6ei6tMG1rPHf',NULL,'172.19.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/158.0.0.0 Safari/537.36 Edg/158.0.0.0','eyJfdG9rZW4iOiJYZjZkUnpjRmJyaWxpYnlCdWRRQWJCeWVvemFMRVJPcUlMajFabDZMIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC9jYXRhbG9nb1wvdmluaWxvcyIsInJvdXRlIjoicHVibGljLnZpbmlsb3MifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJwYXNzd29yZF9oYXNoX3dlYiI6ImE4MDkzNjVlNzA3MWRjODc3MTIxYTVlNDQyNjMxMzZmODA5NzY3ZTEwOWNjOTQ2MTllNjg5MDNhY2E1MGU4ODAiLCJhcmNoaXZ1bV90YWJfc2Vzc2lvbnMiOnsidGFiX213d21nZ2owX2dwdXdyNjJwNjduOWozanRrZnIxIjp7InVzZXJfaWQiOjExLCJjYXJyaXRvX2FyY2hpdnVtIjpbXSwiY2Fycml0b19wcmVjaW9zX2NvbmdlbGFkb3MiOltdLCJjYXJyaXRvX3Jlc2VydmFzX2lkcyI6W10sImNhcnJpdG9fZXhwaXJhX2VuIjpudWxsLCJhdXRoZW50aWNhdGVkX2F0IjoiMjAyNi0xMS0yNVQyMTozMjo1MiswMDowMCIsInVwZGF0ZWRfYXQiOiIyMDI2LTExLTI1VDIyOjExOjM4KzAwOjAwIn19fQ==',1795644825);
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
(11,6,'2026-11-23 07:31:56','2026-11-23 07:31:56'),
(12,7,'2026-11-23 07:31:56','2026-11-23 07:31:56'),
(13,10,'2026-11-23 06:20:32','2026-11-23 06:20:32'),
(14,8,'2026-11-23 07:31:57','2026-11-23 07:31:57'),
(15,9,'2026-11-23 07:31:57','2026-11-23 07:31:57');
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
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES
(11,'Administrador ARCHIVUM','admin@archivum.com','$2y$12$l8ECJ8z9KxriKWp2H3W.DeZGfoBstt67WFFsdh.owt5zy6RBIFy0.',1,NULL,NULL,'2026-11-23 03:23:24','2026-11-23 07:31:56',NULL),
(12,'Encargado Compras','compras1@archivum.com','$2y$12$lEwU6NbdK0vTTRtk3QJmnepkRZd9pkAl25AwT8Fz1S5k1ArJNDxRG',1,NULL,NULL,'2026-11-23 05:39:14','2026-11-23 07:31:56',NULL),
(13,'Evelyn','quijadaevelin82@gmail.com','$2y$12$0V0gcOlVkYw1UFRo2sic6OiIb8Z/7Fz.uL/5W.mggWYK07SoniAuu',1,NULL,NULL,'2026-11-23 06:20:32','2026-11-23 06:20:32',NULL),
(14,'Encargado Ventas','ventas1@archivum.com','$2y$12$OgSZlvA0vdfdzIGE2gT6hORzbpB/S3AAT00JWSVryRUc4SeBmm/.i',1,NULL,NULL,'2026-11-23 07:13:45','2026-11-23 07:31:57',NULL),
(15,'Encargado Almacen','almacen1@archivum.com','$2y$12$9GVADqqIayH0PHEHsPY1f.fmZI6CooJ/kFPKfyAZluwrHxW9Dod36',1,NULL,NULL,'2026-11-23 07:13:45','2026-11-23 07:31:57',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=166 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `variantes_sku`
--

LOCK TABLES `variantes_sku` WRITE;
/*!40000 ALTER TABLE `variantes_sku` DISABLE KEYS */;
INSERT INTO `variantes_sku` VALUES
(71,71,'LIB-1967-SUD','VERY FINE (VF) · PIEZA ÚNICA 1/1','productos/9b2xdCyABitnYZYoAAhn8J5OjYrrB4NGxAvBBSJ1.jpg',32000.00,1,'2026-11-22 04:15:56','2026-11-23 06:57:32'),
(72,72,'LIB-1955-FCE','NEAR MINT (NM) · PIEZA ÚNICA 1/1 · ADQUISICIÓN SELECTA',NULL,44500.00,0,'2026-11-22 04:15:56','2026-11-23 06:57:19'),
(73,73,'LIB-1949-LOS','MINT (M) · PIEZA ÚNICA 1/1 · ADQUISICIÓN SELECTA','productos/gYa5QlrlkdwvAIGMlq3ldGpHoZuGe8ygDtIKpg2b.jpg',58000.00,1,'2026-11-22 04:15:56','2026-11-23 06:56:34'),
(74,74,'LIB-1950-FCE','VERY FINE (VF) · TIRAJE MÚLTIPLE','productos/qYnCho0DwirzbkIQJ1PrhVvyhXI9QRBZWdW6kcXG.jpg',16900.00,1,'2026-11-22 04:15:56','2026-11-23 06:56:19'),
(75,75,'LIB-1962-ERA','NEAR MINT (NM) · TIRAJE REGULAR · TERROR GÓTICO','productos/aVTFPNupBVuqv8AqdLxNykqPMmHV8d0R3tME0hoB.webp',6800.00,1,'2026-11-22 04:15:56','2026-11-23 06:56:02'),
(76,76,'LIB-1963-SUD','MINT (M) · TIRAJE MÚLTIPLE','productos/TCPLCpmh1SnaGKU280WLmOfcZPFzSCiWiP89POg9.webp',8400.00,1,'2026-11-22 04:15:56','2026-11-23 06:55:47'),
(77,77,'LIB-1957-FCE','MINT (M) · PRIMERA EDICIÓN · POESÍA','productos/mJvRjktDKa0jbFxCDpRi4FhhQU5o2cvndCFB5xZZ.jpg',24500.00,1,'2026-11-22 04:15:56','2026-11-23 06:55:32'),
(78,78,'LIB-1959-UNAM','NEAR MINT (NM) · HISTORIA · BIBLIOTECA DEL ESTUDIANTE',NULL,9800.00,0,'2026-11-22 04:15:56','2026-11-23 06:55:09'),
(79,79,'LIB-1971-FCE','VERY FINE (VF) · CIENCIA & FILOSOFÍA','productos/7HzDLydxgzIHkTURYQu6UJMifjJKndEvoRs0aUPs.jpg',11200.00,1,'2026-11-22 04:15:56','2026-11-23 06:54:25'),
(80,80,'LIB-1845-POE','VERY FINE (VF) · TERROR GÓTICO · GRABADOS XILOGRÁFICOS','productos/p9Cf6Nskf3dQFBA3Vr6G1QtqVH6HutScpRqC1q8b.webp',18500.00,1,'2026-11-22 04:15:56','2026-11-23 06:54:12'),
(81,81,'LIB-1955-MIN','MINT (M) · PIEZA ÚNICA 1/1 · CIENCIA FICCIÓN DISTÓPICA','productos/TLYiBzkepp04SsB7cHRFLORSJA1IGPiPXW1O7BH9.jpg',26000.00,1,'2026-11-22 04:15:56','2026-11-23 06:53:18'),
(82,82,'LIB-1955-BUE','NEAR MINT (NM) · CIENCIA FICCIÓN CLÁSICA','productos/DDJgwHy3Hd6EbSR0LA67OVwBhiLBakis3xI3bHm9.jpg',21500.00,1,'2026-11-22 04:15:56','2026-11-23 06:52:59'),
(83,83,'LIB-1977-TOL','VERY FINE (VF) · FANTASÍA ÉPICA · MAPAS DESPLEGABLES','productos/Yp21v7nB63v6WboOV3gMWawFicNu2LvUSVwstArn.jpg',300.00,1,'2026-11-22 04:15:56','2026-11-23 06:52:29'),
(84,84,'LIB-1982-LUM','NEAR MINT (NM) · MISTERIO MONÁSTICO & THRILLER','productos/X4kWplGRxzkWUJRimbhbzdRoKhLkCuFO6MKjm3Ja.jpg',250.00,1,'2026-11-22 04:15:56','2026-11-23 06:52:22'),
(85,85,'LIB-1928-JUV','VERY FINE (VF) · POLICÍACO CLÁSICO SHERLOCK HOLMES','productos/w4o5WSAutGgYS2vUk76XEpDfmhmw7q6VP7ApUm9R.webp',280.00,1,'2026-11-22 04:15:56','2026-11-23 06:52:40'),
(86,86,'LIB-1932-AGU','MINT (M) · FILOSOFÍA MODERNA · ENCUADERNACIÓN EN PIEL','productos/w3PmtPgXy1u6PJEWZiZEP1nHRm9i4COxdwSt2aYz.jpg',380.00,1,'2026-11-22 04:15:56','2026-11-23 06:52:15'),
(87,87,'LIB-1944-UNA','NEAR MINT (NM) · FILOSOFÍA CLÁSICA GRIEGA','productos/zeytArWzUEWrUhsrWamTQN6XnDoEdGvcF0ib5c2T.jpg',360.00,1,'2026-11-22 04:15:56','2026-11-23 06:52:05'),
(88,88,'LIB-1947-RAE','MINT (M) · CLÁSICOS UNIVERSALES · ESTUCHE DE TELA','productos/dgIYo3PHjBqj7MFrwT0MfScUdOzwhYJfXeS6yHN5.jpg',1000.00,1,'2026-11-22 04:15:56','2026-11-23 06:51:40'),
(89,89,'LIB-1921-DIV','NEAR MINT (NM) · PIEZA ÚNICA 1/1 · GRAN FOLIO DORÉ','productos/RoHPqdVCnxcwTtWnv4jkG8HHqvJ1XLqrSnNG9eSM.webp',500.00,1,'2026-11-22 04:15:56','2026-11-23 06:51:49'),
(90,90,'LIB-1974-NER','VERY FINE (VF) · MEMORIAS & BIOGRAFÍA · EDICIÓN PÓSTUMA',NULL,15600.00,0,'2026-11-22 04:15:56','2026-11-23 06:50:38'),
(91,91,'LIB-1960-POR','MINT (M) · CRÓNICA DE INDIAS · EDICIÓN CRÍTICA',NULL,8900.00,0,'2026-11-22 04:15:56','2026-11-23 06:50:43'),
(92,92,'VIN-1959-COL','MINT (M) · PIEZA ÚNICA 1/1 · ADQUISICIÓN SELECTA',NULL,18500.00,0,'2026-11-22 04:15:56','2026-11-23 06:50:48'),
(93,93,'VIN-1957-BN1577','NEAR MINT (NM) · PIEZA ÚNICA 1/1 · ADQUISICIÓN SELECTA',NULL,27500.00,0,'2026-11-22 04:15:56','2026-11-23 06:50:54'),
(94,94,'VIN-1965-IMP77','VERY FINE (VF) · VINILO LP 180G',NULL,16200.00,0,'2026-11-22 04:15:56','2026-11-23 06:51:00'),
(95,95,'VIN-1969-PCS7088','MINT (M) · PIEZA ÚNICA 1/1 · ADQUISICIÓN SELECTA',NULL,22900.00,0,'2026-11-22 04:15:56','2026-11-23 06:51:28'),
(96,96,'CD-1983-CP35','MINT (M) · CD COMPACT DISC · ADQUISICIÓN SELECTA','productos/C3qtAHVSU9LoQlb9SU4WFgz187Cz84fDeAZXu3pt.jpg',530.00,1,'2026-11-22 04:15:56','2026-11-23 06:51:54'),
(97,97,'VIN-1971-LED4','MINT (M) · VINILO LP 180G',NULL,5900.00,0,'2026-11-22 04:15:56','2026-11-23 06:51:34'),
(98,98,'CD-1997-RAD1','MINT (M) · CD COMPACT DISC','productos/Q456A28SU5bldfSxRvIVCataUpdvgzyxOcZnpPhn.webp',4800.00,1,'2026-11-22 04:15:56','2026-11-23 06:48:15'),
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
(110,110,'CD-1997-BVSC','MINT (M) · CD COMPACT DISC',NULL,3950.00,0,'2026-11-22 04:15:57','2026-11-23 06:47:46'),
(111,111,'MEM-1928-AUT','COA NOTARIADO · PIEZA ÚNICA 1/1',NULL,9400.00,0,'2026-11-22 04:15:57','2026-11-23 06:47:36'),
(112,112,'MEM-1940-COY','MINT (M) · PIEZA ÚNICA 1/1 · ADQUISICIÓN SELECTA',NULL,36800.00,0,'2026-11-22 04:15:57','2026-11-23 06:47:31'),
(113,113,'MEM-1910-ALM','MINT (M) · PIEZA ÚNICA 1/1 · ADQUISICIÓN SELECTA',NULL,29500.00,0,'2026-11-22 04:15:57','2026-11-23 06:47:25'),
(114,114,'MEM-1934-FOT','MINT (M) · COA NOTARIADO · ADQUISICIÓN SELECTA',NULL,41000.00,0,'2026-11-22 04:15:57','2026-11-23 06:47:22'),
(115,115,'MEM-1912-PAR','VERY FINE (VF) · ADQUISICIÓN SELECTA',NULL,21500.00,0,'2026-11-22 04:15:57','2026-11-23 06:47:18'),
(116,116,'MEM-1934-PBA','VERY FINE (VF) · TIRAJE MÚLTIPLE',NULL,7800.00,0,'2026-11-22 04:15:57','2026-11-23 06:47:06'),
(117,117,'MEM-1954-COY','NEAR MINT (NM) · TIRAJE MÚLTIPLE',NULL,5400.00,0,'2026-11-22 04:15:57','2026-11-23 06:47:11'),
(118,118,'MEM-1968-IMP','MINT (M) · ACCESORIO DE BÓVEDA',NULL,3200.00,0,'2026-11-22 04:15:57','2026-11-23 06:46:54'),
(119,119,'COL-1997-EVA1','MINT (M) · FIGURA ANIME · EDICIÓN LIMITADA',NULL,18900.00,0,'2026-11-22 04:15:57','2026-11-23 06:46:47'),
(120,120,'COL-1933-DOU','MINT (M) · MONEDA HISTÓRICA RARA',NULL,18872250.00,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(121,121,'COL-1794-FLO','VERY FINE (VF) · PRIMER DÓLAR FEDERAL',NULL,175000.00,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(122,122,'COL-1344-EDU','NEAR MINT (NM) · PIEZA ÚNICA MEDIEVAL',NULL,7000000.00,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(123,123,'COL-0723-DIN','MINT (M) · CALIFATO OMEYA',NULL,95600000.00,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(124,124,'COL-1800-REA','VERY FINE (VF) · COLONIAL',NULL,5500.00,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(125,125,'COL-1947-CEN','MINT (M) · BULLION MEXICANO',NULL,92250.00,0,'2026-11-22 04:15:57','2026-11-23 06:46:37'),
(126,126,'COL-2020-LIB','MINT (M) · ONZA PLATA PURA',NULL,1250.00,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(127,127,'COL-2023-20P','VERY FINE (VF) · COMMEMORATIVE',NULL,100.00,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(128,128,'COL-1975-BIL','FINE (F) · NOTAFILIA HISTÓRICA',NULL,325.00,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(129,129,'COL-2023-POP','MINT (M) · DESIGNER ART TOY',NULL,1100.00,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(130,130,'COL-2022-FUN','MINT (M) · VINYL FIGURE',NULL,1075.00,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(131,131,'COL-2021-LEG','MINT (M) · MASTER BUILDER SET',NULL,19999.00,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(132,132,'COL-2023-ANU','MINT (M) · FIGURA ORNAMENTAL',NULL,773.08,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(133,133,'COL-1992-COC','VERY FINE (VF) · ARTE POPULAR INDÍGENA',NULL,4914.54,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(134,134,'COL-2023-JOY','MINT (M) · ARTESANÍA DECORATIVA',NULL,600.49,1,'2026-11-22 04:15:57','2026-11-22 04:15:57'),
(135,135,'COL-1993-DBZG','MINT (M) · CULTURA POP & ANIME · EN CAJA ORIGINAL','productos/pURPIuY5YUTPvNs5V0NuCOACVAXYA0rdg10gsJVB.jpg',14200.00,1,'2026-11-22 04:15:57','2026-11-23 06:46:16'),
(136,136,'COL-1965-ROB','VERY FINE (VF) · JUGUETE MECÁNICO VINTAGE',NULL,21800.00,0,'2026-11-22 04:15:57','2026-11-23 06:46:04'),
(137,137,'COL-1914-BIL','MINT (M) · NUMISMÁTICA & NOTAFILIA REVOLUCIONARIA','productos/paiPRw6iKSvFPyK4TkoNDxvKiIMdvW2cGgNfRCgz.jpg',500.00,1,'2026-11-22 04:15:57','2026-11-23 06:45:40'),
(138,138,'COL-1768-COL8','VERY FINE (VF) · MONEDAS & BILLETES HISTÓRICOS',NULL,38500.00,0,'2026-11-22 04:15:57','2026-11-23 06:45:21'),
(139,139,'COL-1980-NIN','NEAR MINT (NM) · VIDEOJUEGOS RETRO EN CAJA','productos/Ov8dBeHpr9qzg7jBmxx6rzvIFBVrcyrIfVdQyYNz.webp',2500.00,1,'2026-11-22 04:15:57','2026-11-23 06:45:57'),
(140,140,'COL-1987-ZEL','MINT (M) · CARTUCHO DORADO DE COLECCIÓN · VIDEOJUEGOS','productos/lMStJFZVoRaFgiGxdH3QPglfHsNjnBzPESNRtTbh.avif',2600.00,1,'2026-11-22 04:15:57','2026-11-23 07:35:42'),
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
(152,152,'LIB-2026-004','MINT (M) · Cómo ser youtuber','productos/eYHeYy3Fgl2TSMLSPkz6QpWRG9ltOtsi83r3BB5R.jpg',200.00,1,'2026-11-23 06:09:54','2026-11-23 07:36:47'),
(153,153,'LIB-2026-006','MINT (M) · Auronplay el libro','productos/vHKpgYflGJSIj0WAnRHtKR7YWsMGX92LxWe7nfou.jpg',138.00,1,'2026-11-23 06:09:54','2026-11-23 06:39:34'),
(154,154,'DIS-2026-005','MINT(M) · PIEZA ÚNICA 1/1 · Back in black','productos/59ku0yscQyCH9x4N0Vdn8q1gpaf5imKSHz2g38Wz.jpg',938.00,1,'2026-11-23 06:09:54','2026-11-23 06:39:34'),
(162,162,'COL-2076.010','MINT (M) · PIEZA ÚNICA 1/1 · Micky Mantle','productos/9x8MOv2UAXphugNEuTxIaW70fAAsbMqhIeQUAvsr.jpg',12600000.00,1,'2026-11-23 06:18:55','2026-11-23 06:39:35'),
(163,163,'VIN-FS-1968-R51026','MINT (M) · Rain In My Heart','productos/1HL0V4cl2WoW0LIBPKatIhbqwyCtABQvnS9sayJm.webp',150.00,1,'2026-11-25 21:40:58','2026-11-25 21:40:58'),
(164,164,'CD-MJ-2013-887654156426','MINT (M) · El Amor Manda','productos/jxYSgpkqMiZTYpXe0VHbQTVLXPkmlY1dLdaKBMsT.webp',150.00,1,'2026-11-25 21:52:24','2026-11-25 21:52:24'),
(165,165,'VIN-JJ-2026-198029747416','MINT (M) · Gotas de Fuego','productos/rmyeOIA6gOL4UNwzJNtfWc5EAEqtIXKxU9fysQqO.webp',899.00,1,'2026-11-25 22:11:38','2026-11-25 22:11:38');
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

-- Dump completed on 2026-11-25 22:19:39
