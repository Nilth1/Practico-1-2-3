<?php
// front/admin.php — Dashboard de Administración
// Protección: si no hay sesión -> login; si el rol no es Administrador -> /usuario.php
require __DIR__ . '/php/conexion.php';
$admin = exigirRolPagina('Administrador');
$nombreCompleto = htmlspecialchars($admin['nombre'] . ' ' . $admin['apellido'], ENT_QUOTES, 'UTF-8');
$email = htmlspecialchars($admin['email'], ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - Biblionline</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="/dist/css/dashboard.css" rel="stylesheet">
</head>

<body class="dash">
    <div class="d-flex">

        <!-- Menú lateral (escritorio) -->
        <aside class="dash-sidebar d-none d-lg-block">
            <a href="/landing.html" class="marca"><img src="/dist/img/logo.png" onerror="this.onerror=null;this.src='/dist/img/logo.ico'" alt=""> Biblionline</a>
            <small class="text-muted text-uppercase px-4 d-block mb-2" style="font-size: 11px; letter-spacing: .5px;">Administración</small>
            <nav class="nav flex-column dash-nav">
                <a class="nav-link active" href="#resumen"><i class="bi bi-grid"></i> Resumen</a>
                <a class="nav-link" href="#articulos"><i class="bi bi-book"></i> Artículos</a>
                <a class="nav-link" href="#usuarios"><i class="bi bi-people"></i> Usuarios</a>
                <a class="nav-link" href="/landing.html"><i class="bi bi-box-arrow-up-right"></i> Ver sitio público</a>
            </nav>
        </aside>

        <!-- Menú lateral (móvil) -->
        <div class="offcanvas offcanvas-start" tabindex="-1" id="menu-movil">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title">Biblionline</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body px-0 dash-nav">
                <nav class="nav flex-column">
                    <a class="nav-link" href="#resumen" data-bs-dismiss="offcanvas"><i class="bi bi-grid"></i> Resumen</a>
                    <a class="nav-link" href="#articulos" data-bs-dismiss="offcanvas"><i class="bi bi-book"></i> Artículos</a>
                    <a class="nav-link" href="#usuarios" data-bs-dismiss="offcanvas"><i class="bi bi-people"></i> Usuarios</a>
                    <a class="nav-link" href="/landing.html"><i class="bi bi-box-arrow-up-right"></i> Ver sitio público</a>
                </nav>
            </div>
        </div>

        <div class="flex-grow-1" style="min-width: 0;">
            <!-- Barra superior: usuario conectado + cerrar sesión -->
            <header class="dash-topbar d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-light border d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#menu-movil">
                        <i class="bi bi-list"></i>
                    </button>
                    <h1 class="h5 mb-0 fw-bold">Dashboard de Administración</h1>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="text-end d-none d-sm-block lh-sm">
                        <div class="fw-semibold small" id="admin-nombre"><?= $nombreCompleto ?></div>
                        <div class="text-muted" style="font-size: 12px;"><?= $email ?> · Administrador</div>
                    </div>
                    <button id="btn-logout" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                        <i class="bi bi-box-arrow-right"></i> <span class="d-none d-sm-inline">Cerrar sesión</span>
                    </button>
                </div>
            </header>

            <main class="dash-contenido">
                <!-- Resumen -->
                <section id="resumen" class="mb-4">
                    <h2 class="h5 fw-bold mb-3">Bienvenido, <?= htmlspecialchars($admin['nombre'], ENT_QUOTES, 'UTF-8') ?> 👋</h2>
                    <div class="row g-3">
                        <div class="col-6 col-xl-3">
                            <div class="card stat-card p-3"><div class="d-flex align-items-center gap-3">
                                <div class="icono bg-primary-subtle text-primary"><i class="bi bi-book"></i></div>
                                <div><div class="numero" id="st-articulos">–</div><small class="text-muted">Artículos</small></div>
                            </div></div>
                        </div>
                        <div class="col-6 col-xl-3">
                            <div class="card stat-card p-3"><div class="d-flex align-items-center gap-3">
                                <div class="icono bg-success-subtle text-success"><i class="bi bi-check-circle"></i></div>
                                <div><div class="numero" id="st-disponibles">–</div><small class="text-muted">Disponibles</small></div>
                            </div></div>
                        </div>
                        <div class="col-6 col-xl-3">
                            <div class="card stat-card p-3"><div class="d-flex align-items-center gap-3">
                                <div class="icono bg-danger-subtle text-danger"><i class="bi bi-tag"></i></div>
                                <div><div class="numero" id="st-ofertas">–</div><small class="text-muted">En oferta</small></div>
                            </div></div>
                        </div>
                        <div class="col-6 col-xl-3">
                            <div class="card stat-card p-3"><div class="d-flex align-items-center gap-3">
                                <div class="icono bg-warning-subtle text-warning-emphasis"><i class="bi bi-people"></i></div>
                                <div><div class="numero" id="st-usuarios">–</div><small class="text-muted">Usuarios</small></div>
                            </div></div>
                        </div>
                    </div>
                </section>

                <!-- Artículos: espacio para el futuro ABML (Práctico 3) -->
                <section id="articulos" class="mb-4">
                    <div class="card panel-card">
                        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <span><i class="bi bi-book me-2 text-primary"></i>Administración de artículos</span>
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary btn-sm rounded-pill" disabled title="Disponible en el Práctico 3"><i class="bi bi-plus-lg"></i> Agregar</button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="alert alert-info border-0 rounded-0 mb-0 small">
                                hola profe fue tremendo viaje todo si intento agregar libros con imagenes me voy a quedar pelado perdon
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 tabla-mini">
                                    <thead class="table-light">
                                        <tr><th></th><th>Título</th><th>Autor</th><th>Categoría</th><th>Tipo</th><th>Precio</th><th>Estado</th><th class="text-end">Acciones</th></tr>
                                    </thead>
                                    <tbody id="tabla-articulos"><tr><td colspan="8" class="text-center text-muted py-4">Cargando...</td></tr></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Usuarios -->
                <section id="usuarios" class="mb-4">
                    <div class="card panel-card">
                        <div class="card-header"><i class="bi bi-people me-2 text-primary"></i>Usuarios registrados</div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr><th>Nombre</th><th>Email</th><th>Rol</th><th>Estado</th><th>Registro</th></tr>
                                </thead>
                                <tbody id="tabla-usuarios"><tr><td colspan="5" class="text-center text-muted py-4">Cargando...</td></tr></tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    <script src="/dist/js/api.js"></script>
    <script src="/dist/js/admin.js"></script>
</body>

</html>
