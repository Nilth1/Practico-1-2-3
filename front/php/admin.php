<?php
// front/php/admin.php  (SOLO Administrador)
//   GET ?accion=resumen   -> contadores + usuario conectado
//   GET ?accion=articulos -> listado completo de artículos (lectura; el ABML llega en el Práctico 3)
//   GET ?accion=usuarios  -> listado de usuarios
require __DIR__ . '/conexion.php';

$admin  = exigirRolApi('Administrador');   // 401 si no hay sesión, 403 si no es admin
$accion = $_GET['accion'] ?? '';

try {
    $pdo = conectar();
    switch ($accion) {

    case 'resumen':
        salida(['status' => 'success', 'data' => [
            'admin'       => $admin,
            'articulos'   => (int)$pdo->query("SELECT COUNT(*) FROM articulo")->fetchColumn(),
            'disponibles' => (int)$pdo->query("SELECT COUNT(*) FROM articulo WHERE disponible = 1")->fetchColumn(),
            'ofertas'     => (int)$pdo->query("SELECT COUNT(*) FROM articulo WHERE precio_oferta IS NOT NULL")->fetchColumn(),
            'usuarios'    => (int)$pdo->query("SELECT COUNT(*) FROM usuario u JOIN rol r ON r.id_rol = u.id_rol WHERE r.nombre = 'Usuario'")->fetchColumn(),
        ]]);

    case 'articulos':
        $f = $pdo->query("SELECT a.id_articulo AS id, a.titulo, a.imagen, a.precio, a.precio_oferta, a.disponible,
                                 au.nombre AS autor, c.nombre AS categoria, t.nombre AS tipo
                          FROM articulo a
                          JOIN autor au ON au.id_autor = a.id_autor
                          JOIN categoria c ON c.id_categoria = a.id_categoria
                          JOIN tipo_articulo t ON t.id_tipo = a.id_tipo
                          ORDER BY a.id_articulo")->fetchAll();
        salida(['status' => 'success', 'data' => $f]);

    case 'usuarios':
        $f = $pdo->query("SELECT u.id_usuario AS id, u.nombre, u.apellido, u.email, u.estado, u.fecha_registro, r.nombre AS rol
                          FROM usuario u JOIN rol r ON r.id_rol = u.id_rol
                          ORDER BY u.id_usuario")->fetchAll();
        salida(['status' => 'success', 'data' => $f]);

    default:
        salida(['status' => 'error', 'message' => 'Acción desconocida.'], 400);
    }
} catch (PDOException $e) {
    error_log('admin.php: ' . $e->getMessage());
    salida(['status' => 'error', 'message' => 'Error de base de datos.'], 500);
}
