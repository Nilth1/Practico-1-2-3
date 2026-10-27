<?php
// front/php/catalogo.php  (público)
// GET -> artículos disponibles para la landing: { populares: [...], ofertas: [...] }
require __DIR__ . '/conexion.php';

try {
    $pdo = conectar();
    $filas = $pdo->query("SELECT a.id_articulo AS id, a.titulo, a.imagen, a.precio, a.precio_oferta, a.destacado,
                                 au.nombre AS autor, c.nombre AS categoria, t.nombre AS tipo
                          FROM articulo a
                          JOIN autor au ON au.id_autor = a.id_autor
                          JOIN categoria c ON c.id_categoria = a.id_categoria
                          JOIN tipo_articulo t ON t.id_tipo = a.id_tipo
                          WHERE a.disponible = 1
                          ORDER BY a.id_articulo")->fetchAll();

    $populares = []; $ofertas = [];
    foreach ($filas as $f) {
        $precio = (float)$f['precio'];
        if ($f['precio_oferta'] !== null) {
            $of = (float)$f['precio_oferta'];
            $ofertas[] = [
                'id' => (int)$f['id'], 'titulo' => $f['titulo'], 'imagen' => $f['imagen'],
                'precioActual' => $of, 'precioOriginal' => $precio,
                'descuento' => round((1 - $of / $precio) * 100) . '% OFF',
            ];
        } else {
            $populares[] = [
                'id' => (int)$f['id'], 'titulo' => $f['titulo'], 'imagen' => $f['imagen'],
                'precio' => $precio, 'destacado' => (bool)$f['destacado'],
            ];
        }
    }
    salida(['status' => 'success', 'data' => ['populares' => $populares, 'ofertas' => $ofertas]]);
} catch (PDOException $e) {
    error_log('catalogo.php: ' . $e->getMessage());
    salida(['status' => 'error', 'message' => 'No se pudo leer el catálogo.'], 500);
}
