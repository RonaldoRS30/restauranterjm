-- Extensión restaurante: quitar precio genérico, mesas y pedidos vinculados a productos
-- Ejecutar sobre la misma BD del proyecto (después de tener tablas `categorias` y `productos`).

SET NAMES utf8mb4;

-- Quitar un precio: se elimina `precio`; quedan precio_costo y precio_venta (POS / margen).
-- Si MySQL devuelve error "Unknown column", la columna ya fue eliminada: sigue con el resto del script.
ALTER TABLE productos DROP COLUMN precio;

-- Mesas / puntos de atención en salón
CREATE TABLE IF NOT EXISTS mesas (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  codigo VARCHAR(20) NOT NULL,
  nombre VARCHAR(80) NOT NULL,
  capacidad TINYINT UNSIGNED NOT NULL DEFAULT 4,
  zona VARCHAR(60) DEFAULT NULL COMMENT 'Salón, terraza, barra…',
  estado ENUM('libre','ocupada') NOT NULL DEFAULT 'libre',
  activo TINYINT(1) NOT NULL DEFAULT 1,
  orden INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_mesas_codigo (codigo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Cabecera de comanda / pedido
CREATE TABLE IF NOT EXISTS pedidos (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  mesa_id INT UNSIGNED DEFAULT NULL,
  tipo ENUM('salon','para_llevar','delivery','barra') NOT NULL DEFAULT 'salon',
  estado ENUM('abierto','en_cocina','cuenta_cerrada','anulado') NOT NULL DEFAULT 'abierto',
  usuario_id INT UNSIGNED DEFAULT NULL COMMENT 'Cajero / mesero que abre',
  notas VARCHAR(500) DEFAULT NULL,
  servicio_pct DECIMAL(5,2) NOT NULL DEFAULT 10.00 COMMENT 'Porcentaje sobre subtotal',
  subtotal DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  servicio_monto DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  total DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  cerrado_at TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_pedidos_mesa (mesa_id),
  KEY idx_pedidos_estado_fecha (estado, created_at),
  CONSTRAINT fk_pedidos_mesa FOREIGN KEY (mesa_id) REFERENCES mesas (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Líneas del pedido (snapshot de precio de venta)
CREATE TABLE IF NOT EXISTS pedido_detalle (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  pedido_id INT UNSIGNED NOT NULL,
  producto_id INT NOT NULL,
  cantidad DECIMAL(10,2) NOT NULL DEFAULT 1.00,
  precio_unitario DECIMAL(10,2) NOT NULL,
  subtotal_linea DECIMAL(12,2) NOT NULL,
  notas VARCHAR(255) DEFAULT NULL,
  estado_linea ENUM('pendiente','preparando','listo','entregado') NOT NULL DEFAULT 'pendiente',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_detalle_pedido (pedido_id),
  KEY idx_detalle_producto (producto_id),
  CONSTRAINT fk_detalle_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- producto_id referencia lógica a productos.id (FK opcional si coinciden tipos en tu BD)

INSERT INTO mesas (codigo, nombre, capacidad, zona, estado, activo, orden) VALUES
('M01', 'Mesa 1', 4, 'Salón', 'libre', 1, 1),
('M02', 'Mesa 2', 4, 'Salón', 'libre', 1, 2),
('M03', 'Mesa 3', 2, 'Ventana', 'libre', 1, 3),
('M04', 'Mesa 4', 4, 'Salón', 'libre', 1, 4),
('M05', 'Mesa 5', 6, 'Familiar', 'libre', 1, 5),
('B01', 'Barra 1', 1, 'Barra', 'libre', 1, 10),
('LLEVAR', 'Para llevar', 1, 'Mostrador', 'libre', 1, 90),
('DELIVERY', 'Delivery', 1, 'Despacho', 'libre', 1, 91)
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);
