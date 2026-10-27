<?php
// front/catalogo.php — Catálogo completo (solo para usuarios con sesión iniciada)
require __DIR__ . '/php/conexion.php';
$u = exigirSesionPagina();
$perfil = rutaPorRol($u['rol']);
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Catálogo completo - Biblionline</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="/dist/css/dashboard.css" rel="stylesheet">
    <link href="/dist/css/catalogo.css" rel="stylesheet">
</head>

<body class="dash">
    <header class="dash-topbar d-flex align-items-center justify-content-between">
        <a href="/landing.html" class="d-flex align-items-center gap-2 text-decoration-none text-dark fw-bold">
            <img src="/dist/img/logo.png" onerror="this.onerror=null;this.src='/dist/img/logo.ico'" alt="" style="height: 30px;"> Biblionline
        </a>
        <div class="d-flex align-items-center gap-2">
            <a href="/landing.html" class="btn btn-light border btn-sm rounded-pill px-3"><i class="bi bi-arrow-left"></i> <span class="d-none d-sm-inline">Volver</span></a>
            <a href="<?= htmlspecialchars($perfil, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-light border btn-sm rounded-pill px-3"><i class="bi bi-person"></i> <span class="d-none d-sm-inline">Mi perfil</span></a>
            <button id="btn-logout" class="btn btn-outline-danger btn-sm rounded-pill px-3"><i class="bi bi-box-arrow-right"></i> <span class="d-none d-sm-inline">Cerrar sesión</span></button>
        </div>
    </header>

    <main class="catalogo-wrap">
        <div class="text-center mb-4">
            <h1 class="fw-bold h2 mb-1">Catálogo completo</h1>
            <p class="text-muted mb-0" id="contador">Cargando...</p>
        </div>
        <div id="grid" class="row g-4"></div>
    </main>

    <!-- Modal: descripción -->
    <div class="modal fade" id="modal-desc" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg">
                <div class="modal-header centrado">
                    <h2 class="modal-title h4 fw-bold text-center" id="desc-titulo"></h2>
                </div>
                <div class="modal-body text-center px-4">
                    <p class="text-muted small mb-3" id="desc-meta"></p>
                    <p class="mb-0" id="desc-texto"></p>
                </div>
                <div class="modal-footer centrado">
                    <button type="button" class="btn btn-primary rounded-pill px-5" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: comprar -->
    <div class="modal fade" id="modal-compra" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content shadow-lg text-center">
                <div class="modal-header centrado">
                    <h2 class="modal-title h5 fw-bold text-center" id="compra-titulo"></h2>
                </div>
                <div class="modal-body px-4">
                    <img id="compra-img" src="" alt="" style="max-height: 150px; max-width: 100%; object-fit: contain;" class="mb-3">
                    <div class="text-muted small mb-1">Total a pagar</div>
                    <div class="precio-grande" id="compra-precio"></div>
                    <div id="compra-viejo" class="mt-1"></div>
                </div>
                <div class="modal-footer centrado gap-2">
                    <button type="button" class="btn btn-light border rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" id="btn-pagar" class="btn btn-success rounded-pill px-4 fw-semibold">Pagar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Hoja de pago -->
    <div id="pago-overlay" class="pago-overlay" hidden>
        <div class="pago-sheet" role="dialog" aria-modal="true" aria-label="Pago">
            <div class="pago-cabecera">
                <img src="/dist/img/logo.png" onerror="this.onerror=null;this.src='/dist/img/logo.ico'" alt="">
                <div><strong>Biblionline</strong><small><i class="bi bi-lock-fill"></i> Pago seguro</small></div>
            </div>
            <div class="pago-fila"><span>Artículo</span><span id="pago-titulo"></span></div>
            <div class="pago-fila"><span>Tarjeta</span><span><i class="bi bi-credit-card-2-front me-1"></i>Visa •••• 4242</span></div>
            <div class="pago-fila pago-total"><span>Total</span><span id="pago-total"></span></div>
            <div class="pago-estado">
                <div class="est-proc"><div class="spinner-pago"></div><span>Procesando pago…</span></div>
                <div class="est-ok">
                    <svg class="check-verde" viewBox="0 0 52 52"><circle cx="26" cy="26" r="25"/><path d="M14 27 l8 8 l16 -16"/></svg>
                    <span>Pago completado</span>
                </div>
                <div class="est-error"><div class="x-error"><i class="bi bi-x-circle-fill"></i></div><span id="pago-error">No se pudo completar el pago.</span></div>
            </div>
            <p class="pago-nota">Pago simulado con fines educativos. No se realiza ningún cobro real.</p>
        </div>
    </div>

    <div id="aviso" class="aviso-flotante"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    <script src="/dist/js/api.js"></script>
    <script src="/dist/js/catalogo.js"></script>
</body>

</html>
