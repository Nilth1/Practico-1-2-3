-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Generation Time: Oct 27, 2026 at 10:50 PM
-- Server version: 8.0.46
-- PHP Version: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `practcodb`
--

-- --------------------------------------------------------

--
-- Table structure for table `articulo`
--

CREATE TABLE `articulo` (
  `id_articulo` int NOT NULL,
  `titulo` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `imagen` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `precio_oferta` decimal(10,2) DEFAULT NULL,
  `destacado` tinyint(1) NOT NULL DEFAULT '0',
  `disponible` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_alta` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_autor` int NOT NULL,
  `id_editorial` int DEFAULT NULL,
  `id_categoria` int NOT NULL,
  `id_tipo` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `articulo`
--

INSERT INTO `articulo` (`id_articulo`, `titulo`, `descripcion`, `imagen`, `precio`, `precio_oferta`, `destacado`, `disponible`, `fecha_alta`, `id_autor`, `id_editorial`, `id_categoria`, `id_tipo`) VALUES
(1, 'Historia de dos ciudades', 'Novela ambientada en Londres y París durante la Revolución Francesa.', 'dist/img/historia-de-dos-ciudades.jpg', 255.00, NULL, 0, 1, '2026-10-27 22:21:54', 1, 1, 1, 1),
(2, 'El señor de los anillos', 'La épica aventura de la Comunidad del Anillo por la Tierra Media.', 'dist/img/el-senior-de-los-anillos.jpg', 325.00, NULL, 0, 1, '2026-10-27 22:21:54', 2, 2, 2, 1),
(3, 'Don Quijote de la Mancha', 'Las aventuras del ingenioso hidalgo y su fiel escudero Sancho Panza.', 'dist/img/don-quijote.jpg', 499.00, NULL, 1, 1, '2026-10-27 22:21:54', 3, 3, 1, 1),
(4, 'El Principito', 'Un pequeño príncipe viaja de planeta en planeta y aprende sobre la vida.', 'dist/img/el-principito.jpg', 179.00, NULL, 0, 1, '2026-10-27 22:21:54', 4, 4, 3, 1),
(5, 'Harry Potter y la piedra filosofal', 'Un niño descubre que es mago y entra al Colegio Hogwarts.', 'dist/img/piedra-filosofal.jpg', 399.00, NULL, 0, 1, '2026-10-27 22:21:54', 5, 4, 2, 1),
(6, 'Uruguay en los mundiales', 'Repaso por la historia de la selección uruguaya en las copas del mundo.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR-oPgN2m9eQ_ZK980u4cJK-xLy5hVKkL8yZPfOZWdikg&s=10', 399.00, 239.00, 0, 1, '2026-10-27 22:21:54', 6, 5, 4, 1),
(7, 'Documental de Diego Forlán', 'Documental sobre la carrera de Diego Forlán.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQP_07ZnyhnJr1FrG_9y7EhNtcL5eS-NRmYRtUHTClejg&s=10', 250.00, 58.00, 0, 1, '2026-10-27 22:21:54', 6, 5, 5, 3);

-- --------------------------------------------------------

--
-- Table structure for table `autor`
--

CREATE TABLE `autor` (
  `id_autor` int NOT NULL,
  `nombre` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nacionalidad` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `autor`
--

INSERT INTO `autor` (`id_autor`, `nombre`, `nacionalidad`) VALUES
(1, 'Charles Dickens', 'Reino Unido'),
(2, 'J. R. R. Tolkien', 'Reino Unido'),
(3, 'Miguel de Cervantes', 'España'),
(4, 'Antoine de Saint-Exupéry', 'Francia'),
(5, 'J. K. Rowling', 'Reino Unido'),
(6, 'Varios autores', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `categoria`
--

CREATE TABLE `categoria` (
  `id_categoria` int NOT NULL,
  `nombre` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categoria`
--

INSERT INTO `categoria` (`id_categoria`, `nombre`) VALUES
(4, 'Deportes'),
(5, 'Documental'),
(2, 'Fantasía'),
(3, 'Infantil'),
(1, 'Novela clásica');

-- --------------------------------------------------------

--
-- Table structure for table `editorial`
--

CREATE TABLE `editorial` (
  `id_editorial` int NOT NULL,
  `nombre` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `editorial`
--

INSERT INTO `editorial` (`id_editorial`, `nombre`) VALUES
(3, 'Alfaguara'),
(5, 'Ediciones Varias'),
(2, 'Minotauro'),
(1, 'Penguin Clásicos'),
(4, 'Salamandra');

-- --------------------------------------------------------

--
-- Table structure for table `favorito`
--

CREATE TABLE `favorito` (
  `id_usuario` int NOT NULL,
  `id_articulo` int NOT NULL,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `favorito`
--

INSERT INTO `favorito` (`id_usuario`, `id_articulo`, `fecha`) VALUES
(2, 1, '2026-10-27 22:23:53');

-- --------------------------------------------------------

--
-- Table structure for table `pedido`
--

CREATE TABLE `pedido` (
  `id_pedido` int NOT NULL,
  `precio_pagado` decimal(10,2) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_usuario` int NOT NULL,
  `id_articulo` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pedido`
--

INSERT INTO `pedido` (`id_pedido`, `precio_pagado`, `fecha`, `id_usuario`, `id_articulo`) VALUES
(1, 325.00, '2026-10-27 22:23:25', 1, 2),
(2, 58.00, '2026-10-27 22:23:57', 2, 7),
(3, 179.00, '2026-10-27 22:24:11', 2, 4);

-- --------------------------------------------------------

--
-- Table structure for table `rol`
--

CREATE TABLE `rol` (
  `id_rol` int NOT NULL,
  `nombre` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rol`
--

INSERT INTO `rol` (`id_rol`, `nombre`, `descripcion`) VALUES
(1, 'Administrador', 'Gestiona la biblioteca desde el dashboard'),
(2, 'Usuario', 'Lector registrado de la biblioteca');

-- --------------------------------------------------------

--
-- Table structure for table `tipo_articulo`
--

CREATE TABLE `tipo_articulo` (
  `id_tipo` int NOT NULL,
  `nombre` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tipo_articulo`
--

INSERT INTO `tipo_articulo` (`id_tipo`, `nombre`) VALUES
(3, 'Audiolibro'),
(1, 'Libro'),
(2, 'Revista');

-- --------------------------------------------------------

--
-- Table structure for table `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int NOT NULL,
  `nombre` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contrasena` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('Activo','Inactivo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Activo',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_rol` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nombre`, `apellido`, `email`, `contrasena`, `estado`, `fecha_registro`, `id_rol`) VALUES
(1, 'Admin', 'Biblionline', 'admin@biblionline.com', '$2y$10$qFRHMgEe.7uOdeQi.DtxSud8zF2qO7kovRqw5B4UALlDjVzD6rX/m', 'Activo', '2026-10-27 22:21:53', 1),
(2, 'test', 'test', 'usuario@biblionline.com', '$2y$10$FtIiUvl6IB6tYcUfxNNX9eSf3Hn4pmawLE46v9y3vpfF4nCZ9o232', 'Activo', '2026-10-27 22:21:53', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `articulo`
--
ALTER TABLE `articulo`
  ADD PRIMARY KEY (`id_articulo`),
  ADD KEY `fk_articulo_autor` (`id_autor`),
  ADD KEY `fk_articulo_editorial` (`id_editorial`),
  ADD KEY `fk_articulo_categoria` (`id_categoria`),
  ADD KEY `fk_articulo_tipo` (`id_tipo`);

--
-- Indexes for table `autor`
--
ALTER TABLE `autor`
  ADD PRIMARY KEY (`id_autor`);

--
-- Indexes for table `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id_categoria`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indexes for table `editorial`
--
ALTER TABLE `editorial`
  ADD PRIMARY KEY (`id_editorial`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indexes for table `favorito`
--
ALTER TABLE `favorito`
  ADD PRIMARY KEY (`id_usuario`,`id_articulo`),
  ADD KEY `fk_favorito_articulo` (`id_articulo`);

--
-- Indexes for table `pedido`
--
ALTER TABLE `pedido`
  ADD PRIMARY KEY (`id_pedido`),
  ADD UNIQUE KEY `uq_pedido_usuario_articulo` (`id_usuario`,`id_articulo`),
  ADD KEY `fk_pedido_articulo` (`id_articulo`);

--
-- Indexes for table `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`id_rol`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indexes for table `tipo_articulo`
--
ALTER TABLE `tipo_articulo`
  ADD PRIMARY KEY (`id_tipo`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indexes for table `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_usuario_rol` (`id_rol`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `articulo`
--
ALTER TABLE `articulo`
  MODIFY `id_articulo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `autor`
--
ALTER TABLE `autor`
  MODIFY `id_autor` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id_categoria` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `editorial`
--
ALTER TABLE `editorial`
  MODIFY `id_editorial` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pedido`
--
ALTER TABLE `pedido`
  MODIFY `id_pedido` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `rol`
--
ALTER TABLE `rol`
  MODIFY `id_rol` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tipo_articulo`
--
ALTER TABLE `tipo_articulo`
  MODIFY `id_tipo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `articulo`
--
ALTER TABLE `articulo`
  ADD CONSTRAINT `fk_articulo_autor` FOREIGN KEY (`id_autor`) REFERENCES `autor` (`id_autor`),
  ADD CONSTRAINT `fk_articulo_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id_categoria`),
  ADD CONSTRAINT `fk_articulo_editorial` FOREIGN KEY (`id_editorial`) REFERENCES `editorial` (`id_editorial`),
  ADD CONSTRAINT `fk_articulo_tipo` FOREIGN KEY (`id_tipo`) REFERENCES `tipo_articulo` (`id_tipo`);

--
-- Constraints for table `favorito`
--
ALTER TABLE `favorito`
  ADD CONSTRAINT `fk_favorito_articulo` FOREIGN KEY (`id_articulo`) REFERENCES `articulo` (`id_articulo`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_favorito_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE;

--
-- Constraints for table `pedido`
--
ALTER TABLE `pedido`
  ADD CONSTRAINT `fk_pedido_articulo` FOREIGN KEY (`id_articulo`) REFERENCES `articulo` (`id_articulo`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pedido_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE;

--
-- Constraints for table `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
