// front/dist/js/admin.js — lógica del Dashboard de Administración

// Si la sesión expira o los permisos fallan, volvemos al login
function manejarNoAutorizado(r) {
    if (r && (r.status === 'error') && /Iniciá sesión|Acceso denegado/.test(r.message || '')) {
        location.href = '/dist/pages/login.html?aviso=sesion';
        return true;
    }
    return false;
}

document.getElementById('btn-logout').addEventListener('click', async () => {
    await API.logout();
    location.href = '/landing.html';
});

function dinero(n) { return '$' + Number(n).toLocaleString('es-UY') + ' UYU'; }

async function cargarResumen() {
    const r = await API.adminResumen();
    if (manejarNoAutorizado(r) || r.status !== 'success') return;
    document.getElementById('st-articulos').textContent = r.data.articulos;
    document.getElementById('st-disponibles').textContent = r.data.disponibles;
    document.getElementById('st-ofertas').textContent = r.data.ofertas;
    document.getElementById('st-usuarios').textContent = r.data.usuarios;
}

async function cargarArticulos() {
    const tbody = document.getElementById('tabla-articulos');
    const r = await API.adminArticulos();
    if (manejarNoAutorizado(r)) return;
    if (r.status !== 'success') {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger py-4">' + esc(r.message) + '</td></tr>';
        return;
    }
    tbody.innerHTML = r.data.map(a => {
        const precio = a.precio_oferta !== null
            ? `<span class="text-success fw-bold">${dinero(a.precio_oferta)}</span><br><small class="text-decoration-line-through text-muted">${dinero(a.precio)}</small>`
            : dinero(a.precio);
        const estado = Number(a.disponible)
            ? '<span class="badge bg-success-subtle text-success-emphasis">Disponible</span>'
            : '<span class="badge bg-secondary-subtle text-secondary-emphasis">No disponible</span>';
        return `<tr>
            <td><img src="${esc(a.imagen)}" alt=""></td>
            <td class="fw-semibold">${esc(a.titulo)}</td>
            <td>${esc(a.autor)}</td>
            <td>${esc(a.categoria)}</td>
            <td>${esc(a.tipo)}</td>
            <td>${precio}</td>
            <td>${estado}</td>
            <td class="text-end">
                <button class="btn btn-sm btn-outline-secondary" disabled title="Práctico 3"><i class="bi bi-pencil"></i></button>
                <button class="btn btn-sm btn-outline-danger" disabled title="Práctico 3"><i class="bi bi-trash"></i></button>
            </td></tr>`;
    }).join('');
}

async function cargarUsuarios() {
    const tbody = document.getElementById('tabla-usuarios');
    const r = await API.adminUsuarios();
    if (manejarNoAutorizado(r)) return;
    if (r.status !== 'success') {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger py-4">' + esc(r.message) + '</td></tr>';
        return;
    }
    tbody.innerHTML = r.data.map(u => `<tr>
        <td class="fw-semibold">${esc(u.nombre)} ${esc(u.apellido)}</td>
        <td>${esc(u.email)}</td>
        <td><span class="badge ${u.rol === 'Administrador' ? 'bg-primary' : 'bg-light text-dark border'}">${esc(u.rol)}</span></td>
        <td>${esc(u.estado)}</td>
        <td class="text-muted small">${esc(String(u.fecha_registro).slice(0, 10))}</td></tr>`).join('');
}

cargarResumen();
cargarArticulos();
cargarUsuarios();
