<<<<<<< HEAD
# Biblionline — Prácticos 1, 2 y 3

Proyecto incremental de la Biblioteca Virtual (HTML, CSS, Bootstrap, JavaScript, PHP y MySQL con Docker).

## Cómo correrlo

```bash
docker compose down -v      # solo la 1ra vez: borra el volumen viejo para que se ejecute init.sql
docker compose up -d --build
```

- Sitio: http://localhost:8080
- phpMyAdmin: http://localhost:8081 (usuario `root`, contraseña `root_password`, servidor `db`)

La base `practcodb` se crea sola con `api/config/init.sql`. Si preferís hacerlo a mano, pegá ese archivo completo en la pestaña SQL de phpMyAdmin.

## Usuarios de prueba

| Rol | Email | Contraseña |
|---|---|---|
| Administrador | admin@biblionline.com | Admin1234 |
| Usuario | usuario@biblionline.com | Usuario1234 |

También se puede crear una cuenta nueva desde **Registrarse** (siempre queda con rol *Usuario*).

## Práctico 2 — qué hay y dónde

| Requisito | Dónde está |
|---|---|
| MER | `docs/MER.md` y `docs/MER.png` |
| Pasaje a tablas | `docs/pasaje-a-tablas.md` |
| Glosario de datos | `docs/glosario-de-datos.md` |
| Base de datos implementada | `api/config/init.sql` |
| Login / registro | `front/dist/pages/login.html`, `register.html`, `front/php/auth.php` |
| Roles (Administrador / Usuario) | tablas `rol` y `usuario`; el login valida que el rol elegido coincida con el de la cuenta |
| Dashboard del administrador | `front/admin.php` + `front/dist/js/admin.js` |
| Redirección según el rol | `auth.php` devuelve `/admin.php` o `/usuario.php` |
| Sección del usuario | `front/usuario.php` (perfil, cambio de contraseña, **Mis favoritos** y **Mis pedidos**) |
| Catálogo completo (solo con sesión) | `front/catalogo.php` + `dist/js/catalogo.js`: descripción, favoritos y compra con hoja de pago simulada; endpoints en `front/php/biblioteca.php` |
| Protección del acceso administrativo | `exigirRolPagina()` en `admin.php` (redirige) y `exigirRolApi()` en `php/admin.php` (401/403) |

## Estructura

```
api/config/init.sql          Base de datos (tablas + datos)
docs/                        MER, pasaje a tablas, glosario
front/
  landing.html               Landing pública (botones Ingresar/Registrarse o menú de perfil)
  admin.php                  Dashboard del administrador (protegido)
  usuario.php                Sección del usuario (protegida)
  php/                       conexion.php, auth.php, catalogo.php, biblioteca.php, admin.php, perfil.php
  catalogo.php               Catálogo completo (protegido, requiere sesión)
  dist/pages/                login.html, register.html
  dist/js/                   api.js, script.js, admin.js, catalogo.js
  dist/css/                  style.css, auth.css, dashboard.css, catalogo.css
```

## Notas

- El pago es una **simulación** (no se cobra nada). El precio del pedido lo calcula el servidor, no el navegador.
- Si cambia la base de datos hay que recrearla: `docker compose down -v` y `docker compose up -d --build`.

## Seguridad básica

Contraseñas con `password_hash` / `password_verify`, consultas preparadas (PDO), `session_regenerate_id` al loguear, cookie de sesión `HttpOnly` + `SameSite=Lax`, y el rol se valida siempre en el servidor.
=======
# Practico-1-2-3
Practico que va del 1 al 3 incrementalmente.
>>>>>>> Nilth1/main
