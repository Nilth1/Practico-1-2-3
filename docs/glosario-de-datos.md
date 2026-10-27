# Glosario de datos — Biblionline

Formato del modelo del docente: nombre de la entidad, tipo (Maestra / Subordinada + entidad de la que sale la foránea), descripción y datos con su tipo.

---

**entidad: rol**
- Tipo entidad: Maestra
- Se albergan los roles que puede tener una cuenta del sitio (Administrador, Usuario). Define a qué sección puede entrar cada persona.
  - [id_rol (int ai), nombre (varchar(30) not null unique), descripcion (varchar(150) null)]

**entidad: autor**
- Tipo entidad: Maestra
- Se albergan los autores de los libros, revistas o audiolibros del catálogo.
  - [id_autor (int ai), nombre (varchar(120) not null), nacionalidad (varchar(60) null)]

**entidad: editorial**
- Tipo entidad: Maestra
- Se albergan las editoriales que publican los artículos de la biblioteca.
  - [id_editorial (int ai), nombre (varchar(120) not null unique)]

**entidad: categoria**
- Tipo entidad: Maestra
- Se albergan las categorías o géneros con los que se clasifican los artículos (Fantasía, Infantil, Deportes…).
  - [id_categoria (int ai), nombre (varchar(60) not null unique)]

**entidad: tipo_articulo**
- Tipo entidad: Maestra
- Se albergan los formatos de artículo que maneja la biblioteca: Libro, Revista o Audiolibro.
  - [id_tipo (int ai), nombre (varchar(30) not null unique)]

**entidad: usuario**
- Tipo entidad: Subordinada (rol)
- Se albergan los datos personales y de acceso de las personas registradas: nombre, email y contraseña (guardada con hash bcrypt), su estado y el rol que determina si es Administrador o Usuario.
  - [id_usuario (int ai), nombre (varchar(60) not null), apellido (varchar(60) not null), email (varchar(120) not null unique), contrasena (varchar(255) not null), estado (enum('Activo','Inactivo') not null), fecha_registro (datetime not null), id_rol (int foranea rol:id_rol)]

**entidad: articulo**
- Tipo entidad: Subordinada (autor, editorial, categoria, tipo_articulo)
- Se albergan los artículos del catálogo (libros, revistas, audiolibros) con su información comercial: título, descripción, imagen, precio, precio de oferta (null si no está en oferta), si es destacado y si está disponible para mostrarse en la landing.
  - [id_articulo (int ai), titulo (varchar(200) not null), descripcion (text null), imagen (varchar(500) null), precio (decimal(10,2) not null), precio_oferta (decimal(10,2) null), destacado (tinyint(1) not null), disponible (tinyint(1) not null), fecha_alta (datetime not null), id_autor (int foranea autor:id_autor), id_editorial (int foranea editorial:id_editorial null), id_categoria (int foranea categoria:id_categoria), id_tipo (int foranea tipo_articulo:id_tipo)]

**entidad: favorito**
- Tipo entidad: Subordinada (usuario, articulo)
- Se albergan los artículos que cada usuario marcó como favoritos con el icono del marcador en el catálogo completo (relación muchos a muchos entre usuario y articulo). La clave primaria es compuesta.
  - [id_usuario (int foranea usuario:id_usuario), id_articulo (int foranea articulo:id_articulo), fecha (datetime not null)]

---

**entidad: pedido**
- Tipo entidad: Subordinada (usuario, articulo)
- Se albergan los artículos que un usuario compró o solicitó desde el catálogo completo, junto con el precio que pagó (que puede ser el de oferta) y la fecha. Un usuario no puede pedir dos veces el mismo artículo.
  - [id_pedido (int ai), precio_pagado (decimal(10,2) not null), fecha (datetime not null), id_usuario (int foranea usuario:id_usuario), id_articulo (int foranea articulo:id_articulo)]
