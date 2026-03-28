-- Si ya creaste `productos` con el script anterior, ejecuta esto una vez.
ALTER TABLE `productos`
  ADD COLUMN `precio_costo` decimal(10,2) DEFAULT NULL AFTER `precio`,
  ADD COLUMN `precio_venta` decimal(10,2) DEFAULT NULL AFTER `precio_costo`;
