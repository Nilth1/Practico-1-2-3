# Pasaje a tablas

Reglas aplicadas:
- Cada entidad pasa a una tabla; los atributos pasan a campos y el identificador pasa a clave primaria (PK).
- Relación **1:N** → la clave primaria del lado "1" viaja como clave foránea (FK) a la tabla del lado "N".
- Relación **N:M** → se crea una tabla intermedia con las dos claves como PK compuesta (`favorito`).

| Tabla | Campos | PK | FK |
|---|---|---|---|
| **rol** | id_rol, nombre, descripcion | id_rol | — |
| **autor** | id_autor, nombre, nacionalidad | id_autor | — |
| **editorial** | id_editorial, nombre | id_editorial | — |
| **categoria** | id_categoria, nombre | id_categoria | — |
| **tipo_articulo** | id_tipo, nombre | id_tipo | — |
| **usuario** | id_usuario, nombre, apellido, email, contrasena, estado, fecha_registro, id_rol | id_usuario | id_rol → rol(id_rol) |
| **articulo** | id_articulo, titulo, descripcion, imagen, precio, precio_oferta, destacado, disponible, fecha_alta, id_autor, id_editorial, id_categoria, id_tipo | id_articulo | id_autor → autor(id_autor) · id_editorial → editorial(id_editorial) · id_categoria → categoria(id_categoria) · id_tipo → tipo_articulo(id_tipo) |
| **pedido** | id_pedido, precio_pagado, fecha, id_usuario, id_articulo | id_pedido | id_usuario → usuario(id_usuario) · id_articulo → articulo(id_articulo) · UNIQUE (id_usuario, id_articulo) |
| **favorito** | id_usuario, id_articulo, fecha | (id_usuario, id_articulo) | id_usuario → usuario(id_usuario) · id_articulo → articulo(id_articulo) |

Notación abreviada:

```
rol(id_rol, nombre, descripcion)
autor(id_autor, nombre, nacionalidad)
editorial(id_editorial, nombre)
categoria(id_categoria, nombre)
tipo_articulo(id_tipo, nombre)
usuario(id_usuario, nombre, apellido, email, contrasena, estado, fecha_registro, #id_rol)
articulo(id_articulo, titulo, descripcion, imagen, precio, precio_oferta, destacado, disponible, fecha_alta,
         #id_autor, #id_editorial, #id_categoria, #id_tipo)
favorito(#id_usuario, #id_articulo, fecha)
pedido(id_pedido, precio_pagado, fecha, #id_usuario, #id_articulo)
```
(# = clave foránea). El script completo está en `api/config/init.sql`.
