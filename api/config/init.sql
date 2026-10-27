-- ========================================================
-- BIBLIONLINE - Practico 2: Base de datos
-- Se ejecuta automaticamente la 1ra vez que levanta MySQL (docker compose up)
-- o se puede pegar entero en phpMyAdmin (http://localhost:8081).
-- Usuarios de prueba:
--   Administrador -> admin@biblionline.com   / Admin1234
--   Usuario       -> usuario@biblionline.com / Usuario1234
-- ========================================================

SET NAMES utf8mb4;
CREATE DATABASE IF NOT EXISTS practcodb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE practcodb;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS pedido;
DROP TABLE IF EXISTS favorito;
DROP TABLE IF EXISTS articulo;
DROP TABLE IF EXISTS usuario;
DROP TABLE IF EXISTS tipo_articulo;
DROP TABLE IF EXISTS categoria;
DROP TABLE IF EXISTS editorial;
DROP TABLE IF EXISTS autor;
DROP TABLE IF EXISTS rol;
SET FOREIGN_KEY_CHECKS = 1;

-- ========================================================
-- 1. TABLAS MAESTRAS (sin claves foraneas)
-- ========================================================

CREATE TABLE rol (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(30) NOT NULL UNIQUE,
    descripcion VARCHAR(150) NULL
) ENGINE=InnoDB;

CREATE TABLE autor (
    id_autor INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    nacionalidad VARCHAR(60) NULL
) ENGINE=InnoDB;

CREATE TABLE editorial (
    id_editorial INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE categoria (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(60) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE tipo_articulo (
    id_tipo INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(30) NOT NULL UNIQUE      -- Libro, Revista, Audiolibro
) ENGINE=InnoDB;

-- ========================================================
-- 2. TABLAS SUBORDINADAS (con claves foraneas)
-- ========================================================

CREATE TABLE usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(60) NOT NULL,
    apellido VARCHAR(60) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,       -- hash bcrypt (password_hash de PHP)
    estado ENUM('Activo','Inactivo') NOT NULL DEFAULT 'Activo',
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    id_rol INT NOT NULL,
    CONSTRAINT fk_usuario_rol FOREIGN KEY (id_rol) REFERENCES rol(id_rol)
) ENGINE=InnoDB;

CREATE TABLE articulo (
    id_articulo INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    descripcion TEXT NULL,
    imagen VARCHAR(500) NULL,               -- ruta local (dist/img/..) o URL
    precio DECIMAL(10,2) NOT NULL,
    precio_oferta DECIMAL(10,2) NULL,       -- NULL = sin oferta
    destacado TINYINT(1) NOT NULL DEFAULT 0,
    disponible TINYINT(1) NOT NULL DEFAULT 1,
    fecha_alta DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    id_autor INT NOT NULL,
    id_editorial INT NULL,
    id_categoria INT NOT NULL,
    id_tipo INT NOT NULL,
    CONSTRAINT fk_articulo_autor FOREIGN KEY (id_autor) REFERENCES autor(id_autor),
    CONSTRAINT fk_articulo_editorial FOREIGN KEY (id_editorial) REFERENCES editorial(id_editorial),
    CONSTRAINT fk_articulo_categoria FOREIGN KEY (id_categoria) REFERENCES categoria(id_categoria),
    CONSTRAINT fk_articulo_tipo FOREIGN KEY (id_tipo) REFERENCES tipo_articulo(id_tipo)
) ENGINE=InnoDB;

-- Relacion N:M entre usuario y articulo (boton "favoritos" de la landing)
CREATE TABLE favorito (
    id_usuario INT NOT NULL,
    id_articulo INT NOT NULL,
    fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_usuario, id_articulo),
    CONSTRAINT fk_favorito_usuario FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE,
    CONSTRAINT fk_favorito_articulo FOREIGN KEY (id_articulo) REFERENCES articulo(id_articulo) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Pedidos: articulos que un usuario "compro" / solicito desde el catalogo completo.
-- Guarda el precio que pago (puede ser el de oferta). Un usuario no repite el mismo articulo.
CREATE TABLE pedido (
    id_pedido INT AUTO_INCREMENT PRIMARY KEY,
    precio_pagado DECIMAL(10,2) NOT NULL,
    fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    id_usuario INT NOT NULL,
    id_articulo INT NOT NULL,
    UNIQUE KEY uq_pedido_usuario_articulo (id_usuario, id_articulo),
    CONSTRAINT fk_pedido_usuario FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE,
    CONSTRAINT fk_pedido_articulo FOREIGN KEY (id_articulo) REFERENCES articulo(id_articulo) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ========================================================
-- 3. DATOS INICIALES
-- ========================================================

INSERT INTO rol (nombre, descripcion) VALUES
('Administrador', 'Gestiona la biblioteca desde el dashboard'),
('Usuario', 'Lector registrado de la biblioteca');

-- Contrasenas hasheadas con bcrypt: Admin1234 / Usuario1234
INSERT INTO usuario (nombre, apellido, email, contrasena, id_rol) VALUES
('Admin', 'Biblionline', 'admin@biblionline.com',
 '$2y$10$qFRHMgEe.7uOdeQi.DtxSud8zF2qO7kovRqw5B4UALlDjVzD6rX/m',
 (SELECT id_rol FROM rol WHERE nombre = 'Administrador')),
('Lector', 'Demo', 'usuario@biblionline.com',
 '$2y$10$FtIiUvl6IB6tYcUfxNNX9eSf3Hn4pmawLE46v9y3vpfF4nCZ9o232',
 (SELECT id_rol FROM rol WHERE nombre = 'Usuario'));

INSERT INTO tipo_articulo (nombre) VALUES ('Libro'), ('Revista'), ('Audiolibro');

INSERT INTO categoria (nombre) VALUES
('Novela clásica'), ('Fantasía'), ('Infantil'), ('Deportes'), ('Documental');

INSERT INTO autor (nombre, nacionalidad) VALUES
('Charles Dickens', 'Reino Unido'),
('J. R. R. Tolkien', 'Reino Unido'),
('Miguel de Cervantes', 'España'),
('Antoine de Saint-Exupéry', 'Francia'),
('J. K. Rowling', 'Reino Unido'),
('Varios autores', NULL);

INSERT INTO editorial (nombre) VALUES
('Penguin Clásicos'), ('Minotauro'), ('Alfaguara'), ('Salamandra'), ('Ediciones Varias');

-- Catalogo "Libros mas populares" (los de la landing del Practico 1)
INSERT INTO articulo (titulo, descripcion, imagen, precio, destacado, id_autor, id_editorial, id_categoria, id_tipo) VALUES
('Historia de dos ciudades', 'Novela ambientada en Londres y París durante la Revolución Francesa.',
 'dist/img/historia-de-dos-ciudades.jpg', 255, 0,
 (SELECT id_autor FROM autor WHERE nombre='Charles Dickens'),
 (SELECT id_editorial FROM editorial WHERE nombre='Penguin Clásicos'),
 (SELECT id_categoria FROM categoria WHERE nombre='Novela clásica'),
 (SELECT id_tipo FROM tipo_articulo WHERE nombre='Libro')),
('El señor de los anillos', 'La épica aventura de la Comunidad del Anillo por la Tierra Media.',
 'dist/img/el-senior-de-los-anillos.jpg', 325, 0,
 (SELECT id_autor FROM autor WHERE nombre='J. R. R. Tolkien'),
 (SELECT id_editorial FROM editorial WHERE nombre='Minotauro'),
 (SELECT id_categoria FROM categoria WHERE nombre='Fantasía'),
 (SELECT id_tipo FROM tipo_articulo WHERE nombre='Libro')),
('Don Quijote de la Mancha', 'Las aventuras del ingenioso hidalgo y su fiel escudero Sancho Panza.',
 'dist/img/don-quijote.jpg', 499, 1,
 (SELECT id_autor FROM autor WHERE nombre='Miguel de Cervantes'),
 (SELECT id_editorial FROM editorial WHERE nombre='Alfaguara'),
 (SELECT id_categoria FROM categoria WHERE nombre='Novela clásica'),
 (SELECT id_tipo FROM tipo_articulo WHERE nombre='Libro')),
('El Principito', 'Un pequeño príncipe viaja de planeta en planeta y aprende sobre la vida.',
 'dist/img/el-principito.jpg', 179, 0,
 (SELECT id_autor FROM autor WHERE nombre='Antoine de Saint-Exupéry'),
 (SELECT id_editorial FROM editorial WHERE nombre='Salamandra'),
 (SELECT id_categoria FROM categoria WHERE nombre='Infantil'),
 (SELECT id_tipo FROM tipo_articulo WHERE nombre='Libro')),
('Harry Potter y la piedra filosofal', 'Un niño descubre que es mago y entra al Colegio Hogwarts.',
 'dist/img/piedra-filosofal.jpg', 399, 0,
 (SELECT id_autor FROM autor WHERE nombre='J. K. Rowling'),
 (SELECT id_editorial FROM editorial WHERE nombre='Salamandra'),
 (SELECT id_categoria FROM categoria WHERE nombre='Fantasía'),
 (SELECT id_tipo FROM tipo_articulo WHERE nombre='Libro'));

-- Ofertas del Mundial (precio_oferta no es NULL)
INSERT INTO articulo (titulo, descripcion, imagen, precio, precio_oferta, id_autor, id_editorial, id_categoria, id_tipo) VALUES
('Uruguay en los mundiales', 'Repaso por la historia de la selección uruguaya en las copas del mundo.',
 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR-oPgN2m9eQ_ZK980u4cJK-xLy5hVKkL8yZPfOZWdikg&s=10',
 399, 239,
 (SELECT id_autor FROM autor WHERE nombre='Varios autores'),
 (SELECT id_editorial FROM editorial WHERE nombre='Ediciones Varias'),
 (SELECT id_categoria FROM categoria WHERE nombre='Deportes'),
 (SELECT id_tipo FROM tipo_articulo WHERE nombre='Libro')),
('Documental de Diego Forlán', 'Documental sobre la carrera de Diego Forlán.',
 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQP_07ZnyhnJr1FrG_9y7EhNtcL5eS-NRmYRtUHTClejg&s=10',
 250, 58,
 (SELECT id_autor FROM autor WHERE nombre='Varios autores'),
 (SELECT id_editorial FROM editorial WHERE nombre='Ediciones Varias'),
 (SELECT id_categoria FROM categoria WHERE nombre='Documental'),
 (SELECT id_tipo FROM tipo_articulo WHERE nombre='Audiolibro'));
