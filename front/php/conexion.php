<?php
// front/php/conexion.php
// Conexión a la base de datos + sesión + helpers compartidos por todos los endpoints.
// Los valores por defecto coinciden con el docker-compose.yml.

function conectar(): PDO {
    $host = getenv('DB_HOST') ?: 'db';
    $name = getenv('DB_NAME') ?: 'practcodb';
    $user = getenv('DB_USER') ?: 'usuario';
    $pass = getenv('DB_PASS') ?: 'password';

    $pdo = new PDO("mysql:host=$host;dbname=$name;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    return $pdo;
}

function iniciarSesion(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }
}

/** Responde JSON y corta la ejecución. */
function salida(array $datos, int $codigo = 200): void {
    http_response_code($codigo);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    exit;
}

/** Lee el cuerpo JSON de la petición (o $_POST como respaldo). */
function entrada(): array {
    $json = json_decode(file_get_contents('php://input'), true);
    return is_array($json) ? $json : $_POST;
}

/** A dónde mandar a cada rol después de iniciar sesión. */
function rutaPorRol(string $rol): string {
    return $rol === 'Administrador' ? '/admin.php' : '/usuario.php';
}

/** Devuelve el usuario logueado (array) o null. */
function usuarioActual(): ?array {
    iniciarSesion();
    if (empty($_SESSION['id_usuario'])) return null;
    return [
        'id'       => (int)$_SESSION['id_usuario'],
        'nombre'   => $_SESSION['nombre'],
        'apellido' => $_SESSION['apellido'],
        'email'    => $_SESSION['email'],
        'rol'      => $_SESSION['rol'],
    ];
}

/** Para endpoints JSON: corta con 401/403 si no hay sesión o el rol no corresponde. */
function exigirRolApi(string $rol): array {
    $u = usuarioActual();
    if (!$u) salida(['status' => 'error', 'message' => 'Iniciá sesión para continuar.'], 401);
    if ($u['rol'] !== $rol) salida(['status' => 'error', 'message' => 'Acceso denegado: permisos insuficientes.'], 403);
    return $u;
}

/** Para páginas PHP: redirige si no hay sesión o el rol no corresponde. */
function exigirRolPagina(string $rol): array {
    $u = usuarioActual();
    if (!$u) {
        header('Location: /dist/pages/login.html?aviso=sesion');
        exit;
    }
    if ($u['rol'] !== $rol) {
        header('Location: ' . rutaPorRol($u['rol']));
        exit;
    }
    return $u;
}

/** Para páginas PHP: exige estar logueado (cualquier rol). */
function exigirSesionPagina(): array {
    $u = usuarioActual();
    if (!$u) {
        header('Location: /dist/pages/login.html?aviso=sesion');
        exit;
    }
    return $u;
}
