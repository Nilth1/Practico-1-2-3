<?php
// front/php/perfil.php  (usuario logueado, cualquier rol)
//   POST ?accion=actualizar { nombre, apellido }
//   POST ?accion=password   { actual, nueva, nueva2 }
require __DIR__ . '/conexion.php';

$u = usuarioActual();
if (!$u) salida(['status' => 'error', 'message' => 'Iniciá sesión para continuar.'], 401);

$accion = $_GET['accion'] ?? '';
$in = entrada();

try {
    $pdo = conectar();
    switch ($accion) {

    case 'actualizar':
        $nombre = trim($in['nombre'] ?? ''); $apellido = trim($in['apellido'] ?? '');
        if ($nombre === '' || $apellido === '') salida(['status' => 'error', 'message' => 'Completá nombre y apellido.'], 400);
        if (mb_strlen($nombre) > 60 || mb_strlen($apellido) > 60) salida(['status' => 'error', 'message' => 'Algún campo es demasiado largo.'], 400);
        $pdo->prepare("UPDATE usuario SET nombre = ?, apellido = ? WHERE id_usuario = ?")->execute([$nombre, $apellido, $u['id']]);
        $_SESSION['nombre'] = $nombre; $_SESSION['apellido'] = $apellido;
        salida(['status' => 'success', 'message' => 'Datos actualizados.']);

    case 'password':
        $actual = (string)($in['actual'] ?? ''); $nueva = (string)($in['nueva'] ?? ''); $nueva2 = (string)($in['nueva2'] ?? '');
        if ($actual === '' || $nueva === '') salida(['status' => 'error', 'message' => 'Completá todos los campos.'], 400);
        if (strlen($nueva) < 8) salida(['status' => 'error', 'message' => 'La nueva contraseña debe tener al menos 8 caracteres.'], 400);
        if ($nueva !== $nueva2) salida(['status' => 'error', 'message' => 'Las contraseñas nuevas no coinciden.'], 400);
        $st = $pdo->prepare("SELECT contrasena FROM usuario WHERE id_usuario = ?");
        $st->execute([$u['id']]);
        if (!password_verify($actual, (string)$st->fetchColumn())) salida(['status' => 'error', 'message' => 'La contraseña actual es incorrecta.'], 403);
        $pdo->prepare("UPDATE usuario SET contrasena = ? WHERE id_usuario = ?")->execute([password_hash($nueva, PASSWORD_DEFAULT), $u['id']]);
        salida(['status' => 'success', 'message' => 'Contraseña cambiada.']);

    default:
        salida(['status' => 'error', 'message' => 'Acción desconocida.'], 400);
    }
} catch (PDOException $e) {
    error_log('perfil.php: ' . $e->getMessage());
    salida(['status' => 'error', 'message' => 'Error de base de datos.'], 500);
}
