<!DOCTYPE html>
<html>
    <head>
        <title>Página Principal</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="estilos/estilos.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Archivo de lista compartida -->
        <script src="js/productos.js"></script>
        <script src="js/carrito.js"></script>
    </head>
    <body onload="cargarMasVendidos();">
        <!--Estructura básica-->
        <!--Navbar -->
        <nav class="navbar navbar-expand-sm navbar-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php">
                    <img src="img/logoap1.png" alt="Avatar Logo" style="width: 180px;" class="logo-img">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="collapsibleNavbar">
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Productos</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="productos.php?categoria=mieles">Mieles</a></li>
                                <li><a class="dropdown-item" href="productos.php?categoria=packs">Packs</a></li>
                                <li><a class="dropdown-item" href="productos.php?categoria=derivados">Derivados</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="productos.php?">Todo</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="tiendas.php">Tiendas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="empresa.php">Quienes Somos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contacto.php">Contacto</a>
                        </li>
                    </ul>
                </div>
                <!-- Envoltorio de los botones de login y carrito -->
                <div class="d-flex align-items-center ms-auto">
                    <button type="button" class="btn border-0 bg-transparent p-0 position-relative" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCarrito">
                        <img src="img/carrito.png" style="width: 20px; height: 20px; object-fit: contain;">
                        <!-- contador dinámico -->
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="contadorCarrito">
                            0
                        </span>
                    </button>
                    <button type="button" class="btn border-0 bg-transparent p-0" data-bs-toggle="modal" data-bs-target="#myModal">
                        <img src="img/loginicon1.png" style="width: 20px; height: 20px; object-fit: contain;">
                    </button>
                </div>
            </div>
        </nav>

        <!-- Cartas de contenido -->
        <div class="container my-5">
            <h2 class="text-center mb-4">Nuestros Productos Más Vendidos</h2>

            <!-- Contenedor de productos mas vendidos dinamico -->
            <div class="row g-4" id="contenedorMasVendidos"></div>
                
            <!-- Boton de ver más -->
            <div class="col-12 text-center mt-4">
                <a href="productos.php" class="btn btn-primary">Ver Más Productos</a>
            </div>

        </div>
        
        <script>
            function cargarMasVendidos() {
                const contenedor = document.getElementById("contenedorMasVendidos");

                // Busco los primeros 4 elementos más vendidos 
                const masVendidos = responseAPI.data.slice(0, 4);

                masVendidos.forEach(prod => {
                    const col = document.createElement("div");
                    col.className = "col-12 col-sm-6 col-lg-3";

                    col.innerHTML =`
                        <div class="card h-100 shadow-sm">
                            <img src="${prod.imagen}" class="card-img-top" alt="${prod.nombre}">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">${prod.nombre}</h5>
                                <p class="card-text text-muted">${prod.descripcion}</p>
                                <h6 class="text-warning fw-bold mb-3">${formatearPrecio(prod.precio)}</h6>
                                <a href="detalle-producto.php?id=${prod.id}" class="btn btn-primary mt-auto">Ver Detalles</a>
                            </div>
                        </div>
                    `;

                    contenedor.appendChild(col);
                });
            }
            function formatearPrecio(valor) {
                return '$' + valor.toLocaleString('es-CL');
            }
        </script>

        <!-- Carousel -->
       <div class="container my-4">
            <div class="row justify-content-center">
                <div class="col-12 col-md-12 col-lg-10">
                    <h2 class="text-center mb-4">Conoce sobre nuestros Apiarios</h2>
                    </h2>
                    <div id="demo" class="carousel slide" data-bs-ride="carousel">

                    <!-- Indicators/dots -->
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#demo" data-bs-slide-to="0" class="active"></button>
                        <button type="button" data-bs-target="#demo" data-bs-slide-to="1"></button>
                        <button type="button" data-bs-target="#demo" data-bs-slide-to="2"></button>
                    </div>

                    <!-- The slideshow/carousel -->
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                        <img src="img/apiarios/ap1.jpg" alt="Apiario 1" class="d-block w-100">
                        </div>
                        <div class="carousel-item">
                        <img src="img/apiarios/ap2.jpg" alt="Apiario 2" class="d-block w-100">
                        </div>
                        <div class="carousel-item">
                        <img src="img/apiarios/ap3.jpg" alt="Apiario 3" class="d-block w-100">
                        </div>
                    </div>

                    <!-- Left and right controls/icons -->
                    <button class="carousel-control-prev" type="button" data-bs-target="#demo" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#demo" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                    </div>
                </div>
            </div>
       </div>

       <!-- Contenido adicional -->
       <div class="container my-5">
            <div class="row">
                <div class="col-12 col-md-6">
                    <h2>Sobre Nosotros</h2>
                    <p>Somos una empresa dedicada a la producción y comercialización de miel de alta calidad. Nuestro compromiso es ofrecer productos naturales y saludables, provenientes de nuestros propios apiarios.</p>
                </div>
                <div class="col-12 col-md-6">
                    <img src="img/nosotros.jpg" class="img-fluid" alt="Sobre Nosotros">
                </div>
            </div>
        </div>

        <!--Footer-->
        <div class="container-fluid bg-dark">
            <div class="row">
                <div class="col-4"></div>
                <div class="col-4" style="color:white"><strong>MirelaRivero@2026</strong></div>
                <div class="col-4"></div>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal" id="myModal">
            <div class="modal-dialog">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">Formulario de Registro</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <form action="/action_page.php">
                            <div class="mb-3 mt-3">
                                <label for="email" class="form-label">Email:</label>
                                <input type="email" class="form-control" id="email" placeholder="Ingresa tu email" name="email">
                            </div>
                            <div class="mb-3">
                                <label for="pwd" class="form-label">Contraseña:</label>
                                <input type="password" class="form-control" id="pwd" placeholder="Ingresa tu contraseña" name="pswd">
                            </div>
                            <div class="form-check mb-3">
                                <label class="form-check-label">
                                    <input class="form-check-input" type="checkbox" name="remember"> Recuérdame
                                </label>
                            </div>
                            <button type="submit" class="btn btn-primary">Enviar</button>
                        </form>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                    </div>

                </div>
            </div>
        </div>
        <!-- Panel lateral del carrito -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasCarrito" aria-labelledby="offcanvasCarritoLabel">
            <div class="offcanvas-header bg-light border-bottom">
                <h5 class="offcanvas-title fw-bold" id="offcanvasCarritoLabel">Carrito de Compras</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>

            <!-- Productos que se añaden -->
            <div class="offcanvas-body d-flex flex-column">
                <!-- Lista de productos -->
                <div id="listaCarrito" class="flex-grow-1 overflow-auto">
                    <!-- si el carrito esta vacio se mostrara un mensaje -->
                     <div class="text-center text-muted my-5" id="carritoVacio">
                        <p class="fs-4">🍯</p>
                        <small>Tu carrito está vacío</small>
                     </div>
                </div>

                <!-- Contenido del carrito -->
                <div class="border-top pt-3 mt-auto">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-bold fs-5">Total a Pagar: </span>
                        <span class="fw-bold fs-5 text-warning" id="precioTotalCarrito">$0</span>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-primary btn-lg" onclick="irAlCheckout()">
                            Ir a pagar
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="vaciarCarrito()">
                            Vaciar Carrito
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>