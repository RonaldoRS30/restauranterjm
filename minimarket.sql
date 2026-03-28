-- --------------------------------------------------------
-- Table structure for table `usuarios`
-- --------------------------------------------------------

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','vendedor') DEFAULT 'vendedor',
  `id_sucursal` int(11) DEFAULT NULL,
  `estado` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------
-- Datos iniciales
-- --------------------------------------------------------

INSERT INTO `usuarios` (`id`, `nombre`, `usuario`, `password`, `rol`, `id_sucursal`, `estado`) VALUES
(1, 'Administrador del Sistema', 'admin', '0192023a7bbd73250516f069df18b500', 'admin', 1, 1);

-- --------------------------------------------------------
-- Índices
-- --------------------------------------------------------

ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`);

-- --------------------------------------------------------
-- AUTO_INCREMENT
-- --------------------------------------------------------

ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;