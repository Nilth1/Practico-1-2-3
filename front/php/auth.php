<?php
// front/php/auth.php
// Endpoints de autenticación:
//   POST ?accion=login      { email, password, rol }
//   POST ?accion=register   { nombre, apellido, email, password, password2 }
//   POST ?accion=logout
//   GET  ?accion=sesion     -> datos del usuario logueado (o logueado:false)
require __DIR__ . '/conexion.php';

$accion = $_GET['accion'] ?? '';

try {
    switch ($accion) {

    case 'sesion':
        $u = usuarioActual();
        salida(['status' => 'success', 'logueado' => (bool)$u, 'usuario' => $u, 'destino' => $u ? rutaPorRol($u['rol']) : null]);

    case 'login':
        $in    = entrada();
        $email = strtolower(trim($in['email'] ?? ''));
        $pass  = (string)($in['password'] ?? '');
        $rolElegido = trim($in['rol'] ?? 'Usuario');

        if ($email === '' || $pass === '') salida(['status' => 'error', 'message' => 'Completá todos los campos.'], 400);
        if (!in_array($rolElegido, ['Usuario', 'Administrador'], true)) salida(['status' => 'error', 'message' => 'Rol inválido.'], 400);

        $pdo = conectar();
        $st  = $pdo->prepare("SELECT u.id_usuario, u.nombre, u.apellido, u.email, u.contrasena, r.nombre AS rol
                              FROM usuario u JOIN rol r ON r.id_rol = u.id_rol
                              WHERE u.email = ? AND u.estado = 'Activo' LIMIT 1");
        $st->execute([$email]);
        $u = $st->fetch();

        if (!$u || !password_verify($pass, $u['contrasena'])) {
            salida(['status' => 'error', 'message' => 'Email o contraseña incorrectos.'], 401);
        }
        // El rol elegido en el formulario tiene que coincidir con el rol real de la cuenta
        if ($u['rol'] !== $rolElegido) {
            $msg = $rolElegido === 'Administrador'
                ? 'Esta cuenta no tiene permisos de Administrador.'
                : 'Esta cuenta es de Administrador. Elegí "Administrador" para ingresar.';
            salida(['status' => 'error', 'message' => $msg], 403);
        }

        iniciarSesion();
        session_regenerate_id(true);
        $_SESSION['id_usuario'] = (int)$u['id_usuario'];
        $_SESSION['nombre']     = $u['nombre'];
        $_SESSION['apellido']   = $u['apellido'];
        $_SESSION['email']      = $u['email'];
        $_SESSION['rol']        = $u['rol'];

        salida(['status' => 'success', 'message' => 'Inicio de sesión exitoso.',
                'data' => ['nombre' => $u['nombre'], 'rol' => $u['rol'], 'redirect' => rutaPorRol($u['rol'])]]);

    case 'register':
        $in = entrada();
        $nombre   = trim($in['nombre'] ?? '');
        $apellido = trim($in['apellido'] ?? '');
        $email    = strtolower(trim($in['email'] ?? ''));
        $pass     = (string)($in['password'] ?? '');
        $pass2    = (string)($in['password2'] ?? '');

        if ($nombre === '' || $apellido === '' || $email === '' || $pass === '') salida(['status' => 'error', 'message' => 'Completá todos los campos.'], 400);
        if (mb_strlen($nombre) > 60 || mb_strlen($apellido) > 60 || mb_strlen($email) > 120) salida(['status' => 'error', 'message' => 'Algún campo es demasiado largo.'], 400);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) salida(['status' => 'error', 'message' => 'El email no es válido.'], 400);
        if (strlen($pass) < 8) salida(['status' => 'error', 'message' => 'La contraseña debe tener al menos 8 caracteres.'], 400);
        if ($pass !== $pass2) salida(['status' => 'error', 'message' => 'Las contraseñas no coinciden.'], 400);

        $pdo = conectar();
        $st = $pdo->prepare("SELECT COUNT(*) FROM usuario WHERE email = ?");
        $st->execute([$email]);
        if ($st->fetchColumn() > 0) salida(['status' => 'error', 'message' => 'Ese email ya está registrado.'], 409);

        // Todo registro público es rol "Usuario". Un administrador solo se crea desde la base de datos.
        $st = $pdo->prepare("INSERT INTO usuario (nombre, apellido, email, contrasena, id_rol)
                             VALUES (?, ?, ?, ?, (SELECT id_rol FROM rol WHERE nombre = 'Usuario'))");
        $st->execute([$nombre, $apellido, $email, password_hash($pass, PASSWORD_DEFAULT)]);
        $id = (int)$pdo->lastInsertId();

        // Queda logueado directamente
        iniciarSesion();
        session_regenerate_id(true);
        $_SESSION['id_usuario'] = $id;
        $_SESSION['nombre']     = $nombre;
        $_SESSION['apellido']   = $apellido;
        $_SESSION['email']      = $email;
        $_SESSION['rol']        = 'Usuario';

        salida(['status' => 'success', 'message' => 'Cuenta creada correctamente.',
                'data' => ['nombre' => $nombre, 'rol' => 'Usuario', 'redirect' => rutaPorRol('Usuario')]], 201);

    case 'logout':
        iniciarSesion();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        salida(['status' => 'success', 'message' => 'Sesión cerrada correctamente.']);

    default:
        salida(['status' => 'error', 'message' => 'Acción desconocida.'], 400);
    }
} catch (PDOException $e) {
    error_log('auth.php: ' . $e->getMessage());
    salida(['status' => 'error', 'message' => 'Error de base de datos. ¿Está levantado el contenedor "db" y corriste init.sql?'], 500);
}
