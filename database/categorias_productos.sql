-- Base restaurante: categorías (logo + título) y productos enlazados
-- Ejecutar en la base `restaurante` (o la que uses en database.php)

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(150) NOT NULL,
  `slug` varchar(160) NOT NULL,
  `logo` varchar(255) DEFAULT NULL COMMENT 'Ruta relativa desde la raíz del sitio, ej. uploads/categorias/xxx.png',
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `productos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `descripcion` text,
  `precio` decimal(10,2) DEFAULT NULL COMMENT 'Referencia / uso futuro POS',
  `stock` int(11) NOT NULL DEFAULT 0,
  `precio_costo` decimal(10,2) DEFAULT NULL,
  `precio_venta` decimal(10,2) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_productos_categoria` (`id_categoria`),
  CONSTRAINT `fk_productos_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
