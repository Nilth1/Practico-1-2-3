<?php
// front/usuario.php — Sección del Usuario (perfil / configuración)
// Protección: si no hay sesión -> login; si es Administrador -> /admin.php
require __DIR__ . '/php/conexion.php';
$u = exigirRolPagina('Usuario');
$h = fn($t) => htmlspecialchars($t, ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mi perfil - Biblionline</title>
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
            <a href="/catalogo.php" class="btn btn-primary btn-sm rounded-pill px-3"><i class="bi bi-book"></i> <span class="d-none d-sm-inline">Catálogo completo</span></a>
            <a href="/landing.html" class="btn btn-light border btn-sm rounded-pill px-3"><i class="bi bi-house"></i> <span class="d-none d-sm-inline">Inicio</span></a>
            <button id="btn-logout" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                <i class="bi bi-box-arrow-right"></i> <span class="d-none d-sm-inline">Cerrar sesión</span>
            </button>
        </div>
    </header>

    <main class="container dash-contenido" style="max-width: 820px;">
        <div class="d-flex align-items-center gap-3 mb-4">
            <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold fs-3"
                style="width: 64px; height: 64px;"><?= $h(mb_strtoupper(mb_substr($u['nombre'], 0, 1))) ?></div>
            <div>
                <h1 class="h4 fw-bold mb-0">Hola, <?= $h($u['nombre']) ?></h1>
                <small class="text-muted"><?= $h($u['email']) ?> · Usuario</small>
            </div>
        </div>

        <div id="alerta" class="alert d-none py-2 small" role="alert"></div>

        <div class="row g-4">
            <div class="col-12 col-md-6">
                <div class="card panel-card h-100">
                    <div class="card-header"><i class="bi bi-person me-2 text-primary"></i>Mis datos</div>
                    <div class="card-body">
                        <form id="form-datos">
                            <div class="mb-3"><label class="form-label small fw-semibold">Nombre</label>
                                <input id="nombre" class="form-control" maxlength="60" value="<?= $h($u['nombre']) ?>" required></div>
                            <div class="mb-3"><label class="form-label small fw-semibold">Apellido</label>
                                <input id="apellido" class="form-control" maxlength="60" value="<?= $h($u['apellido']) ?>" required></div>
                            <div class="mb-3"><label class="form-label small fw-semibold">Email</label>
                                <input class="form-control" value="<?= $h($u['email']) ?>" disabled></div>
                            <button class="btn btn-primary rounded-pill px-4" type="submit">Guardar cambios</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card panel-card h-100">
                    <div class="card-header"><i class="bi bi-shield-lock me-2 text-primary"></i>Cambiar contraseña</div>
                    <div class="card-body">
                        <form id="form-pass">
                            <div class="mb-3"><label class="form-label small fw-semibold">Contraseña actual</label>
                                <input id="actual" type="password" class="form-control" autocomplete="current-password" required></div>
                            <div class="mb-3"><label class="form-label small fw-semibold">Nueva contraseña</label>
                                <input id="nueva" type="password" class="form-control" minlength="8" autocomplete="new-password" required></div>
                            <div class="mb-3"><label class="form-label small fw-semibold">Repetir nueva contraseña</label>
                                <input id="nueva2" type="password" class="form-control" minlength="8" autocomplete="new-password" required></div>
                            <button class="btn btn-outline-primary rounded-pill px-4" type="submit">Cambiar contraseña</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-1">
            <div class="col-12 col-lg-6" id="favoritos">
                <div class="card panel-card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-bookmark-fill me-2 text-warning"></i>Mis favoritos</span>
                        <span class="badge bg-warning-subtle text-warning-emphasis" id="cant-fav">0</span>
                    </div>
                    <div class="list-group list-group-flush" id="lista-fav"><div class="p-4 text-center text-muted">Cargando...</div></div>
                </div>
            </div>
            <div class="col-12 col-lg-6" id="pedidos">
                <div class="card panel-card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-bag-check-fill me-2 text-success"></i>Mis pedidos</span>
                        <span class="badge bg-success-subtle text-success-emphasis" id="cant-ped">0</span>
                    </div>
                    <div class="list-group list-group-flush" id="lista-ped"><div class="p-4 text-center text-muted">Cargando...</div></div>
                </div>
            </div>
        </div>
    </main>

    <script src="/dist/js/api.js"></script>
    <script>
        const alerta = document.getElementById('alerta');
        function aviso(msg, tipo) { alerta.className = 'alert alert-' + tipo + ' py-2 small'; alerta.textContent = msg; window.scrollTo({ top: 0, behavior: 'smooth' }); }

        document.getElementById('btn-logout').addEventListener('click', async () => {
            await API.logout();
            location.href = '/landing.html';
        });

        document.getElementById('form-datos').addEventListener('submit', async (e) => {
            e.preventDefault();
            const r = await API.perfilActualizar({
                nombre: document.getElementById('nombre').value.trim(),
                apellido: document.getElementById('apellido').value.trim()
            });
            aviso(r.message, r.status === 'success' ? 'success' : 'danger');
        });

        document.getElementById('form-pass').addEventListener('submit', async (e) => {
            e.preventDefault();
            const r = await API.perfilPassword({
                actual: document.getElementById('actual').value,
                nueva: document.getElementById('nueva').value,
                nueva2: document.getElementById('nueva2').value
            });
            aviso(r.message, r.status === 'success' ? 'success' : 'danger');
            if (r.status === 'success') e.target.reset();
        });

        // ---- Favoritos y pedidos ----
        const dinero = (n) => '$' + Number(n).toLocaleString('es-UY') + ' UYU';
        const vacio = (txt) => `<div class="p-4 text-center text-muted">${txt}<br><a href="/catalogo.php" class="btn btn-sm btn-outline-primary rounded-pill mt-2">Ir al catálogo completo</a></div>`;
        const miniatura = (l) => `<img src="${esc(l.imagen)}" alt="" style="width:44px;height:58px;object-fit:contain;background:#f4f7fb;border-radius:6px;">`;

        async function cargarListas() {
            const r = await API.biblioListas();
            if (r.status !== 'success') {
                document.getElementById('lista-fav').innerHTML = document.getElementById('lista-ped').innerHTML =
                    '<div class="p-4 text-center text-danger">' + esc(r.message) + '</div>';
                return;
            }
            const { favoritos, pedidos } = r.data;
            document.getElementById('cant-fav').textContent = favoritos.length;
            document.getElementById('cant-ped').textContent = pedidos.length;

            document.getElementById('lista-fav').innerHTML = favoritos.length ? favoritos.map(l => `
                <div class="list-group-item d-flex align-items-center gap-3">
                    ${miniatura(l)}
                    <div class="flex-grow-1">
                        <div class="fw-semibold">${esc(l.titulo)}</div>
                        <div class="small text-muted">${esc(l.autor)} · ${dinero(l.precio_oferta !== null ? l.precio_oferta : l.precio)}</div>
                    </div>
                    <button class="btn btn-sm btn-outline-secondary rounded-circle" data-quitar="${l.id}" title="Quitar de favoritos"><i class="bi bi-bookmark-x"></i></button>
                </div>`).join('') : vacio('Todavía no marcaste favoritos.');

            document.getElementById('lista-ped').innerHTML = pedidos.length ? pedidos.map(l => `
                <div class="list-group-item d-flex align-items-center gap-3">
                    ${miniatura(l)}
                    <div class="flex-grow-1">
                        <div class="fw-semibold">${esc(l.titulo)}</div>
                        <div class="small text-muted">${esc(l.autor)} · ${esc(String(l.fecha).slice(0, 10))}</div>
                    </div>
                    <span class="badge bg-success-subtle text-success-emphasis fs-6">${dinero(l.precio_pagado)}</span>
                </div>`).join('') : vacio('Todavía no hiciste pedidos.');
        }

        document.getElementById('lista-fav').addEventListener('click', async (e) => {
            const b = e.target.closest('[data-quitar]');
            if (!b) return;
            b.disabled = true;
            await API.biblioFavorito(Number(b.dataset.quitar));
            cargarListas();
        });

        cargarListas();
    </script>
</body>

</html>
