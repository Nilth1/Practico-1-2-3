<?php
// front/php/biblioteca.php  (usuario logueado, cualquier rol)
//   GET  ?accion=catalogo              -> todos los artículos disponibles + si son favorito / comprado por mí
//   POST ?accion=favorito  {id}        -> agrega o quita de favoritos (toggle)
//   POST ?accion=comprar   {id}        -> registra el pedido (el precio lo calcula el servidor)
//   GET  ?accion=listas                -> mis favoritos y mis pedidos
require __DIR__ . '/conexion.php';

$u = usuarioActual();
if (!$u) salida(['status' => 'error', 'message' => 'Iniciá sesión para continuar.'], 401);

$accion = $_GET['accion'] ?? '';
$in = entrada();
$idArticulo = (int)($in['id'] ?? 0);

function fmtArticulo(array $f): array {
    $f['id'] = (int)$f['id'];
    $f['precio'] = (float)$f['precio'];
    $f['precio_oferta'] = $f['precio_oferta'] !== null ? (float)$f['precio_oferta'] : null;
    $f['destacado'] = (bool)($f['destacado'] ?? false);
    if (array_key_exists('favorito', $f)) $f['favorito'] = (bool)$f['favorito'];
    if (array_key_exists('comprado', $f)) $f['comprado'] = (bool)$f['comprado'];
    return $f;
}

try {
    $pdo = conectar();
    switch ($accion) {

    case 'catalogo':
        $st = $pdo->prepare("SELECT a.id_articulo AS id, a.titulo, a.descripcion, a.imagen, a.precio, a.precio_oferta, a.destacado,
                                    au.nombre AS autor, c.nombre AS categoria, t.nombre AS tipo,
                                    EXISTS(SELECT 1 FROM favorito f WHERE f.id_articulo = a.id_articulo AND f.id_usuario = ?) AS favorito,
                                    EXISTS(SELECT 1 FROM pedido p WHERE p.id_articulo = a.id_articulo AND p.id_usuario = ?) AS comprado
                             FROM articulo a
                             JOIN autor au ON au.id_autor = a.id_autor
                             JOIN categoria c ON c.id_categoria = a.id_categoria
                             JOIN tipo_articulo t ON t.id_tipo = a.id_tipo
                             WHERE a.disponible = 1
                             ORDER BY a.id_articulo");
        $st->execute([$u['id'], $u['id']]);
        salida(['status' => 'success', 'data' => array_map('fmtArticulo', $st->fetchAll())]);

    case 'favorito':
        if ($idArticulo <= 0) salida(['status' => 'error', 'message' => 'Artículo inválido.'], 400);
        $st = $pdo->prepare("SELECT COUNT(*) FROM articulo WHERE id_articulo = ?");
        $st->execute([$idArticulo]);
        if (!$st->fetchColumn()) salida(['status' => 'error', 'message' => 'El artículo no existe.'], 404);

        $st = $pdo->prepare("SELECT COUNT(*) FROM favorito WHERE id_usuario = ? AND id_articulo = ?");
        $st->execute([$u['id'], $idArticulo]);
        if ($st->fetchColumn()) {
            $pdo->prepare("DELETE FROM favorito WHERE id_usuario = ? AND id_articulo = ?")->execute([$u['id'], $idArticulo]);
            salida(['status' => 'success', 'favorito' => false, 'message' => 'Quitado de favoritos.']);
        }
        $pdo->prepare("INSERT INTO favorito (id_usuario, id_articulo) VALUES (?, ?)")->execute([$u['id'], $idArticulo]);
        salida(['status' => 'success', 'favorito' => true, 'message' => 'Agregado a favoritos.']);

    case 'comprar':
        if ($idArticulo <= 0) salida(['status' => 'error', 'message' => 'Artículo inválido.'], 400);
        $st = $pdo->prepare("SELECT precio, precio_oferta FROM articulo WHERE id_articulo = ? AND disponible = 1");
        $st->execute([$idArticulo]);
        $art = $st->fetch();
        if (!$art) salida(['status' => 'error', 'message' => 'El artículo no está disponible.'], 404);

        $st = $pdo->prepare("SELECT COUNT(*) FROM pedido WHERE id_usuario = ? AND id_articulo = ?");
        $st->execute([$u['id'], $idArticulo]);
        if ($st->fetchColumn()) salida(['status' => 'error', 'message' => 'Ya tenés este artículo en tus pedidos.'], 409);

        $precio = $art['precio_oferta'] !== null ? $art['precio_oferta'] : $art['precio'];
        $pdo->prepare("INSERT INTO pedido (precio_pagado, id_usuario, id_articulo) VALUES (?, ?, ?)")->execute([$precio, $u['id'], $idArticulo]);
        salida(['status' => 'success', 'message' => 'Pedido realizado.'], 201);

    case 'listas':
        $st = $pdo->prepare("SELECT a.id_articulo AS id, a.titulo, a.imagen, a.precio, a.precio_oferta, au.nombre AS autor, f.fecha
                             FROM favorito f
                             JOIN articulo a ON a.id_articulo = f.id_articulo
                             JOIN autor au ON au.id_autor = a.id_autor
                             WHERE f.id_usuario = ? ORDER BY f.fecha DESC");
        $st->execute([$u['id']]);
        $favoritos = array_map('fmtArticulo', $st->fetchAll());

        $st = $pdo->prepare("SELECT a.id_articulo AS id, a.titulo, a.imagen, a.precio, a.precio_oferta, au.nombre AS autor,
                                    p.precio_pagado, p.fecha
                             FROM pedido p
                             JOIN articulo a ON a.id_articulo = p.id_articulo
                             JOIN autor au ON au.id_autor = a.id_autor
                             WHERE p.id_usuario = ? ORDER BY p.fecha DESC");
        $st->execute([$u['id']]);
        $pedidos = array_map(function ($f) { $f = fmtArticulo($f); $f['precio_pagado'] = (float)$f['precio_pagado']; return $f; }, $st->fetchAll());

        salida(['status' => 'success', 'data' => ['favoritos' => $favoritos, 'pedidos' => $pedidos]]);

    default:
        salida(['status' => 'error', 'message' => 'Acción desconocida.'], 400);
    }
} catch (PDOException $e) {
    error_log('biblioteca.php: ' . $e->getMessage());
    salida(['status' => 'error', 'message' => 'Error de base de datos.'], 500);
}
