const librosRespaldo = [
    {
        titulo: "Historia de dos ciudades",
        imagen: "dist/img/historia-de-dos-ciudades.jpg",
        precio: 255,
        destacado: false
    },
    {
        titulo: "El señor de los anillos",
        imagen: "dist/img/el-senior-de-los-anillos.jpg",
        precio: 325,
        destacado: false
    },
    {
        titulo: "Don Quijote de la Mancha",
        imagen: "dist/img/don-quijote.jpg",
        precio: 499,
        destacado: true
    },
    {
        titulo: "El Principito",
        imagen: "dist/img/el-principito.jpg",
        precio: 179,
        destacado: false
    },
    {
        titulo: "Harry Potter y la piedra filosofal",
        imagen: "dist/img/piedra-filosofal.jpg",
        precio: 399,
        destacado: false
    }
];

const ofertasRespaldo = [
    {
        titulo: "Uruguay en los mundiales",
        imagen: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR-oPgN2m9eQ_ZK980u4cJK-xLy5hVKkL8yZPfOZWdikg&s=10",
        precioActual: 239,
        precioOriginal: 399,
        descuento: "40% OFF"
    },
    {
        titulo: "Documental de Diego Forlan",
        imagen: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQP_07ZnyhnJr1FrG_9y7EhNtcL5eS-NRmYRtUHTClejg&s=10",
        precioActual: 58,
        precioOriginal: 250,
        descuento: "23% OFF"
    }
];

const contenedor = document.getElementById("contenedor-libros");
const contenedorOfertas = document.getElementById("contenedor-ofertas");

function mostrarLibros(listaLibros) {
    const columnasLibros = listaLibros.map(libro => {
        if (libro.destacado) {
            return `
                <div class="col-12 col-sm-6 col-md-4 col-lg">
                    <div class="card shadow border-primary text-center p-3 libro-destacado bg-white h-100">
                        <span class="badge bg-warning text-dark position-absolute top-0 start-50 translate-middle text-uppercase fw-bold">TOP 1</span>
                        <img src="${esc(libro.imagen)}" class="card-img-top img-fluid mx-auto mt-2" alt="${esc(libro.titulo)}" style="max-height: 220px; object-fit: contain;">
                        <div class="card-body px-1 py-2 d-flex flex-column justify-content-end">
                            <h5 class="fw-bold text-dark fs-5">${esc(libro.titulo)}</h5>
                            <p class="text-success fw-bold mb-0 fs-5">$${libro.precio} UYU</p>
                        </div>
                    </div>
                </div>
            `;
        } else {
            return `
                <div class="col-12 col-sm-6 col-md-4 col-lg">
                    <div class="card h-100 shadow-sm border text-center p-3">
                        <img src="${esc(libro.imagen)}" class="card-img-top img-fluid mx-auto" alt="${esc(libro.titulo)}" style="max-height: 180px; object-fit: contain;">
                        <div class="card-body px-1 py-2 d-flex flex-column justify-content-end">
                            <h5 class="fw-bold text-dark fs-6">${esc(libro.titulo)}</h5>
                            <p class="text-primary fw-bold mb-0">$${libro.precio} UYU</p>
                        </div>
                    </div>
                </div>
            `;
        }
    }).join("");

    contenedor.innerHTML = columnasLibros;
}

function mostrarOfertas(listaOfertas) {
    const cajasOfertas = listaOfertas.map(libro => {
        return `
            <div class="col-12 col-sm-6">
                <div class="card h-100 shadow-sm border-0 p-3 bg-white">
                    <div class="row g-0 align-items-center h-100">
                        <div class="col-4 text-center position-relative">
                            <span class="badge bg-danger position-absolute top-0 start-0 translate-middle fw-bold" style="z-index: 3; margin-left: 10px; margin-top: 5px;">${libro.descuento}</span>
                            <img src="${esc(libro.imagen)}" class="img-fluid rounded" alt="${esc(libro.titulo)}" style="max-height: 140px; object-fit: contain;">
                        </div>
                        <div class="col-8">
                            <div class="card-body py-0 ps-3 pe-1">
                                <h5 class="fw-bold text-dark fs-6 mb-2">${esc(libro.titulo)}</h5>
                                <p class="text-success fw-bold mb-1 fs-5">$${libro.precioActual} UYU</p>
                                <p class="text-danger text-decoration-line-through small mb-0">$${libro.precioOriginal} UYU</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }).join("");

    contenedorOfertas.innerHTML = cajasOfertas;
}

// ============================================================
// Catálogo dinámico: viene de la base de datos (php/catalogo.php).
// Si falla la conexión se muestran los libros de respaldo.
// ============================================================
async function cargarCatalogo() {
    const r = await API.catalogo();
    if (r.status === 'success') {
        mostrarLibros(r.data.populares);
        mostrarOfertas(r.data.ofertas);
    } else {
        mostrarLibros(librosRespaldo);
        mostrarOfertas(ofertasRespaldo);
    }
}

// ============================================================
// Zona de usuario del header:
//  - sin sesión  -> botones Ingresar / Registrarse
//  - con sesión  -> menú con "Mi perfil" (o "Panel de administración") y "Cerrar sesión"
// ============================================================
const zonaUsuario = document.getElementById("zona-usuario");

function pintarSinSesion() {
    zonaUsuario.innerHTML = `
        <a href="dist/pages/login.html" class="btn btn-outline-primary btn-sm rounded-pill px-2 px-sm-3">
            <i class="bi bi-box-arrow-in-right d-sm-none"></i><span class="d-none d-sm-inline">Ingresar</span>
        </a>
        <a href="dist/pages/register.html" class="btn btn-primary btn-sm rounded-pill px-3 d-none d-sm-inline-block">Registrarse</a>
    `;
}

function pintarConSesion(usuario, destino) {
    const esAdmin = usuario.rol === "Administrador";
    // Con sesión iniciada desaparecen Ingresar / Registrarse y queda solo el icono de persona
    zonaUsuario.innerHTML = `
        <div class="dropdown d-flex align-items-center">
            <button class="btn btn-light btn-sm rounded-circle border d-flex align-items-center justify-content-center p-0"
                style="width: 36px; height: 36px; font-size: 18px;" type="button" data-bs-toggle="dropdown" title="${esc(usuario.nombre)} ${esc(usuario.apellido)}" aria-label="Mi cuenta">
                <i class="bi bi-person-fill"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                <li class="px-3 py-1 small text-muted">${esc(usuario.nombre)} ${esc(usuario.apellido)}<br><span class="badge bg-primary-subtle text-primary-emphasis">${esc(usuario.rol)}</span></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-menu-item" href="${esc(destino)}">${esAdmin ? "Panel de administración" : "Mi perfil"}</a></li>
                <li><button type="button" id="btn-cerrar-sesion" class="dropdown-menu-item text-danger">Cerrar sesión</button></li>
            </ul>
        </div>
    `;
    document.getElementById("btn-cerrar-sesion").addEventListener("click", async () => {
        await API.logout();
        location.reload();
    });
}

async function iniciarHeader() {
    const r = await API.sesion();
    if (r.status === "success" && r.logueado) {
        pintarConSesion(r.usuario, r.destino);
    } else {
        pintarSinSesion();
    }
}

iniciarHeader();
cargarCatalogo();
