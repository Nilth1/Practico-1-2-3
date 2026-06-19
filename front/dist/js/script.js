const libros = [
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

const librosOferta = [
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
                        <img src="${libro.imagen}" class="card-img-top img-fluid mx-auto mt-2" alt="${libro.titulo}" style="max-height: 220px; object-fit: contain;">
                        <div class="card-body px-1 py-2 d-flex flex-column justify-content-end">
                            <h5 class="fw-bold text-dark fs-5">${libro.titulo}</h5>
                            <p class="text-success fw-bold mb-0 fs-5">$${libro.precio} UYU</p>
                        </div>
                    </div>
                </div>
            `;
        } else {
            return `
                <div class="col-12 col-sm-6 col-md-4 col-lg">
                    <div class="card h-100 shadow-sm border text-center p-3">
                        <img src="${libro.imagen}" class="card-img-top img-fluid mx-auto" alt="${libro.titulo}" style="max-height: 180px; object-fit: contain;">
                        <div class="card-body px-1 py-2 d-flex flex-column justify-content-end">
                            <h5 class="fw-bold text-dark fs-6">${libro.titulo}</h5>
                            <p class="text-primary fw-bold mb-0">$${libro.precio} UYU</p>
                        </div>
                    </div>
                </div>
            `;
        }
    }).join("");

    contenedor.innerHTML = columnasLibros;
}

function mostrarOfertas() {
    const cajasOfertas = librosOferta.map(libro => {
        return `
            <div class="col-12 col-sm-6">
                <div class="card h-100 shadow-sm border-0 p-3 bg-white">
                    <div class="row g-0 align-items-center h-100">
                        <div class="col-4 text-center position-relative">
                            <span class="badge bg-danger position-absolute top-0 start-0 translate-middle fw-bold" style="z-index: 3; margin-left: 10px; margin-top: 5px;">${libro.descuento}</span>
                            <img src="${libro.imagen}" class="img-fluid rounded" alt="${libro.titulo}" style="max-height: 140px; object-fit: contain;">
                        </div>
                        <div class="col-8">
                            <div class="card-body py-0 ps-3 pe-1">
                                <h5 class="fw-bold text-dark fs-6 mb-2">${libro.titulo}</h5>
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

mostrarLibros(libros);
mostrarOfertas();