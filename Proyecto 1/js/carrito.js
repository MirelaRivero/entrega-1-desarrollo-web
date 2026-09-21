// Obtener productos guardados
function obtenerCarrito() {
    const carritoGuardado = localStorage.getItem('carritoMiel');
    return carritoGuardado ? JSON.parse(carritoGuardado) : [];
}

// Guardar el estado actual del carrito
function guardarCarrito(carrito) {
    localStorage.setItem('carritoMiel', JSON.stringify(carrito));
    actualizarVistaCarrito();
}

// Añadir el producto o incrementarlo si ya existe
function agregarAlCarrito(idProducto, cantidad = 1) {
    const carrito = obtenerCarrito();
    const productoInfo = responseAPI.data.find(p => p.id === idProducto);

    if (!productoInfo) return;

    const indexExistente = carrito.findIndex(item => item.id === idProducto);

    if (indexExistente !== -1) {
        carrito[indexExistente].cantidad += cantidad;
    } else {
        carrito.push({
            id: productoInfo.id,
            nombre: productoInfo.nombre,
            precio: productoInfo.precio,
            imagen: productoInfo.imagen,
            cantidad: cantidad
        });
    }

    guardarCarrito(carrito);
}

// Cambiar cantidad desde botones del carrito
function cambiarCantidad(idProducto, delta) {
    let carrito = obtenerCarrito();
    const item = carrito.find(p => p.id === idProducto);

    if (item) {
        item.cantidad += delta;
        if (item.cantidad <= 0) {
            carrito = carrito.filter(p => p.id !== idProducto);
        }
    }
    guardarCarrito(carrito);
}

// Eliminar un producto del carrito
function eliminarDelCarrito(idProducto) {
    let carrito = obtenerCarrito();
    carrito = carrito.filter(p => p.id !== idProducto);
    guardarCarrito(carrito);
}

// Vaciar el carrito completo
function vaciarCarrito() {
    if (confirm("¿Estas seguro de vaciar el carrito?")) {
        guardarCarrito([]);
    }
}

// Actualizar el icono de cantidad de productos en el carrito
function actualizarVistaCarrito() {
    const carrito = obtenerCarrito();

    const badge = document.getElementById('contadorCarrito');
    if (badge) {
        const totalItems = carrito.reduce((acc, item) => acc + item.cantidad, 0);
        badge.innerText = totalItems;
        badge.className = totalItems > 0
            ? "position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
            : "position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary"
    }

    const lista = document.getElementById('listaCarrito');
    const totalTexto = document.getElementById('precioTotalCarrito');

    if (!lista || !totalTexto) return;

    if (carrito.length === 0) {
        lista.innerHTML = `
            <div class="text-center text-muted my-5" id="carritoVacio">
                <p class="fs-4">🍯</p>
                <small>Tu carrito está vacío</small>
            </div>
        `;
        totalTexto.innerText = '$0';
        return;
    }

    // Si tiene productos se reflejan en el carrito
    let htmlItems = '';
    let totalAcumulado = 0;

    carrito.forEach(item => {
        const subTotal = item.precio * item.cantidad;
        totalAcumulado += subTotal;

        htmlItems += `
            <div class="card mb-3 border-0 shadow-sm">
                <div class="card-body p-2 d-flex align-items-center">
                    <img src="${item.imagen}" alt="${item.nombre}" style="width: 55px; height: 55px; object-fit: contain;" class="me-2 rounded">
                    <div class="flex-grow-1">
                        <h6 class="mb-0 text-truncate" style="max-width: 140px;">${item.nombre}</h6>
                        <small class="text-muted">${formatearPrecio(item.precio)} c/u</small>
                        <div class="fw-bold text-dark mt-1">${formatearPrecio(subTotal)}</div>
                    </div>
                    <!-- Botones de cantidad -->
                    <div class="d-flex align-items-center gap-1">
                        <button class="btn btn-sm btn-outline-secondary px-2 py-0" onclick="cambiarCantidad(${item.id}, -1)">-</button>
                        <span class="fw-semibold px-1">${item.cantidad}</span>
                        <button class="btn btn-sm btn-outline-secondary px-2 py-0" onclick="cambiarCantidad(${item.id}, 1)">+</button>
                        <button class="btn btn-sm text-danger ms-1" onclick="eliminarDelCarrito(${item.id})" title="Eliminar">🗑️</button>
                    </div>
                </div>
            </div>
        `; 
    });
    lista.innerHTML = htmlItems;
    totalTexto.innerText = formatearPrecio(totalAcumulado);
}

// Boton para iniciar la compra
function irAlCheckout() {
    const carrito = obtenerCarrito();
    if (carrito.length === 0) {
        alert("Agrega al menos un producto al carrito antes de continuar.");
        return;
    }
    window.location.href="checkout.php";
}
// Listener para inicializar el carrito apenas se carguen las paginas
document.addEventListener('DOMContentLoaded', actualizarVistaCarrito);
