// front/dist/js/catalogo.js — catálogo completo: descripción, favoritos y compra con hoja de pago

let libros = [];
let libroActual = null;
let pagando = false;

const $ = (id) => document.getElementById(id);
const espera = (ms) => new Promise(r => setTimeout(r, ms));
const dinero = (n) => '$' + Number(n).toLocaleString('es-UY') + ' UYU';
const precioFinal = (l) => (l.precio_oferta !== null ? l.precio_oferta : l.precio);

let avisoTimer;
function aviso(msg) {
    const a = $('aviso');
    a.textContent = msg;
    a.classList.add('visible');
    clearTimeout(avisoTimer);
    avisoTimer = setTimeout(() => a.classList.remove('visible'), 2200);
}

$('btn-logout').addEventListener('click', async () => {
    await API.logout();
    location.href = '/landing.html';
});

function tarjeta(l) {
    const oferta = l.precio_oferta !== null;
    const desc = oferta ? Math.round((1 - l.precio_oferta / l.precio) * 100) : 0;
    const boton = l.comprado
        ? '<button class="btn btn-success rounded-pill flex-fill" disabled><i class="bi bi-check-lg"></i> Comprado</button>'
        : `<button class="btn btn-primary rounded-pill flex-fill" data-accion="comprar" data-id="${l.id}">Comprar</button>`;
    return `
    <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
        <div class="libro-card h-100 d-flex flex-column">
            ${oferta ? `<span class="badge bg-danger badge-oferta">-${desc}%</span>` : ''}
            <button class="btn-fav ${l.favorito ? 'activo' : ''}" data-accion="fav" data-id="${l.id}"
                title="${l.favorito ? 'Quitar de favoritos' : 'Agregar a favoritos'}" aria-label="Favorito">
                <i class="bi ${l.favorito ? 'bi-bookmark-fill' : 'bi-bookmark'}"></i>
            </button>
            <div class="libro-portada"><img src="${esc(l.imagen)}" alt="${esc(l.titulo)}"></div>
            <div class="p-3 d-flex flex-column flex-grow-1">
                <h3 class="h6 fw-bold mb-1">${esc(l.titulo)}</h3>
                <div class="text-muted small mb-2">${esc(l.autor)} · ${esc(l.tipo)}</div>
                <div class="mb-3 mt-auto">
                    <span class="fw-bold text-success fs-5">${dinero(precioFinal(l))}</span>
                    ${oferta ? `<span class="precio-viejo">${dinero(l.precio)}</span>` : ''}
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary rounded-pill flex-fill" data-accion="desc" data-id="${l.id}">Descripción</button>
                    ${boton}
                </div>
            </div>
        </div>
    </div>`;
}

function pintar() {
    $('grid').innerHTML = libros.map(tarjeta).join('');
    $('contador').textContent = libros.length + (libros.length === 1 ? ' artículo disponible' : ' artículos disponibles');
}

async function cargar() {
    const r = await API.biblioCatalogo();
    if (r.status === 'error' && /Iniciá sesión/.test(r.message || '')) {
        location.href = '/dist/pages/login.html?aviso=sesion';
        return;
    }
    if (r.status !== 'success') {
        $('contador').textContent = r.message || 'No se pudo cargar el catálogo.';
        return;
    }
    libros = r.data;
    pintar();
}

// ---------- Eventos de las tarjetas ----------
$('grid').addEventListener('click', async (e) => {
    const b = e.target.closest('[data-accion]');
    if (!b) return;
    const l = libros.find(x => x.id === Number(b.dataset.id));
    if (!l) return;

    if (b.dataset.accion === 'desc') {
        $('desc-titulo').textContent = l.titulo;
        $('desc-meta').textContent = `${l.autor} · ${l.categoria} · ${l.tipo}`;
        $('desc-texto').textContent = l.descripcion || 'Este artículo todavía no tiene descripción.';
        bootstrap.Modal.getOrCreateInstance($('modal-desc')).show();
    }

    if (b.dataset.accion === 'fav') {
        b.disabled = true;
        const r = await API.biblioFavorito(l.id);
        if (r.status === 'success') {
            l.favorito = r.favorito;
            pintar();
            aviso(r.message);
        } else {
            b.disabled = false;
            aviso(r.message || 'No se pudo actualizar favoritos.');
        }
    }

    if (b.dataset.accion === 'comprar') {
        libroActual = l;
        $('compra-titulo').textContent = l.titulo;
        $('compra-img').src = l.imagen;
        $('compra-precio').textContent = dinero(precioFinal(l));
        $('compra-viejo').innerHTML = l.precio_oferta !== null
            ? `<span class="precio-viejo ms-0">${dinero(l.precio)}</span> <span class="badge bg-danger">Oferta</span>` : '';
        bootstrap.Modal.getOrCreateInstance($('modal-compra')).show();
    }
});

// ---------- Pago ----------
$('btn-pagar').addEventListener('click', async () => {
    if (!libroActual || pagando) return;
    pagando = true;
    const l = libroActual;
    bootstrap.Modal.getOrCreateInstance($('modal-compra')).hide();
    await espera(250);

    const ov = $('pago-overlay');
    $('pago-titulo').textContent = l.titulo;
    $('pago-total').textContent = dinero(precioFinal(l));
    ov.classList.remove('ok', 'error');
    ov.hidden = false;
    requestAnimationFrame(() => requestAnimationFrame(() => ov.classList.add('visible')));

    // El servidor registra el pedido; la animación dura como mínimo 1,6 s
    const [r] = await Promise.all([API.biblioComprar(l.id), espera(1600)]);

    if (r.status === 'success') {
        ov.classList.add('ok');
        l.comprado = true;
        await espera(1800);
    } else {
        $('pago-error').textContent = r.message || 'No se pudo completar el pago.';
        ov.classList.add('error');
        await espera(2400);
    }

    ov.classList.remove('visible');
    await espera(350);
    ov.hidden = true;
    ov.classList.remove('ok', 'error');
    pagando = false;
    pintar();
    if (r.status === 'success') aviso('¡Listo! Lo encontrás en "Mis pedidos" de tu perfil.');
});

cargar();
