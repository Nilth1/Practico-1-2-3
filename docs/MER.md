# Modelo Entidad-Relación (MER) — Biblionline

![MER](MER.png)

**Entidades:** `rol`, `usuario`, `autor`, `editorial`, `categoria`, `tipo_articulo`, `articulo`, `pedido`.
Las claves primarias están subrayadas (en amarillo en el diagrama).

## Relaciones y cardinalidades

| Relación | Entidad A | Entidad B | Cardinalidad | Explicación |
|---|---|---|---|---|
| tiene | rol (0,N) | usuario (1,1) | 1 : N | Un rol lo tienen muchos usuarios; cada usuario tiene exactamente un rol. |
| escribe | autor (0,N) | articulo (1,1) | 1 : N | Un autor escribe muchos artículos; cada artículo tiene un autor. |
| publica | editorial (0,N) | articulo (0,1) | 1 : N | Una editorial publica muchos artículos; un artículo puede no tener editorial. |
| clasifica | categoria (0,N) | articulo (1,1) | 1 : N | Una categoría agrupa muchos artículos; cada artículo tiene una categoría. |
| es de tipo | tipo_articulo (0,N) | articulo (1,1) | 1 : N | Libro / Revista / Audiolibro. |
| marca como favorito | usuario (0,N) | articulo (0,N) | N : M | Un usuario marca muchos favoritos y un artículo puede ser favorito de muchos usuarios. Atributo propio: `fecha`. Se resuelve con la tabla `favorito`. |
| realiza | usuario (0,N) | pedido (1,1) | 1 : N | Un usuario realiza muchos pedidos; cada pedido es de un solo usuario. |
| incluye | articulo (0,N) | pedido (1,1) | 1 : N | Un artículo puede estar en muchos pedidos; cada pedido es de un solo artículo. Un usuario no repite el mismo artículo (clave única). |

## Versión editable (Mermaid, GitHub la dibuja sola)

```mermaid
erDiagram
    rol ||--o{ usuario : "tiene"
    autor ||--o{ articulo : "escribe"
    editorial |o--o{ articulo : "publica"
    categoria ||--o{ articulo : "clasifica"
    tipo_articulo ||--o{ articulo : "es de tipo"
    usuario ||--o{ favorito : "marca"
    articulo ||--o{ favorito : "es marcado"
    usuario ||--o{ pedido : "realiza"
    articulo ||--o{ pedido : "incluye"

    rol { int id_rol PK
          string nombre
          string descripcion }
    usuario { int id_usuario PK
              string nombre
              string apellido
              string email
              string contrasena
              string estado
              datetime fecha_registro
              int id_rol FK }
    autor { int id_autor PK
            string nombre
            string nacionalidad }
    editorial { int id_editorial PK
                string nombre }
    categoria { int id_categoria PK
                string nombre }
    tipo_articulo { int id_tipo PK
                    string nombre }
    articulo { int id_articulo PK
               string titulo
               string descripcion
               string imagen
               decimal precio
               decimal precio_oferta
               boolean destacado
               boolean disponible
               datetime fecha_alta
               int id_autor FK
               int id_editorial FK
               int id_categoria FK
               int id_tipo FK }
    pedido { int id_pedido PK
             decimal precio_pagado
             datetime fecha
             int id_usuario FK
             int id_articulo FK }
    favorito { int id_usuario PK,FK
               int id_articulo PK,FK
               datetime fecha }
```
