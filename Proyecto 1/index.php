<!DOCTYPE html>
<html>
    <head>
        <title>Página Principal</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="estilos/estilos.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    </head>
    <body>
        <!--Estructura básica-->
        <!--Navbar -->
        <nav class="navbar navbar-expand-sm navbar-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php">
                    <img src="img/logoap.png" alt="Avatar Logo" style="width: 180px;" class="logo-img">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="collapsibleNavbar">
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="a" role="button" data-bs-toggle="dropdown">Productos</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="a">Mieles</a></li>
                                <li><a class="dropdown-item" href="a">Packs</a></li>
                                <li><a class="dropdown-item" href="a">Derivados</a></li>
                                <li><a class="dropdown-item" href="productos.php">Todo</a></li>
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
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#myModal">Acceso</button>
            </div>
        </nav>

        <!-- Cartas de contenido -->
        <div class="container my-5">
            <h2 class="text-center mb-4">Nuestros Productos Más Vendidos</h2>
            <div class="row g-4">
                <!-- Producto 1 -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card h-100 shadow-sm">
                            <img src="img/mieles/miel1.png" class="card-img-top" alt="Miel 1">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">Miel 1</h5>
                                <p class="card-text">Descripción de la miel 1.</p>
                                <a href="#" class="btn btn-primary mt-auto">Ver Detalles</a>
                            </div>
                        </div>
                </div>
                <!-- Producto 2 -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card h-100 shadow-sm">
                            <img src="img/mieles/miel2.png" class="card-img-top" alt="Miel 2">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">Miel 2</h5>
                                <p class="card-text">Descripción de la miel 2.</p>
                                <a href="#" class="btn btn-primary mt-auto">Ver Detalles</a>
                            </div>
                        </div>
                </div>
                <!-- Producto 3 -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card h-100 shadow-sm">
                            <img src="img/mieles/miel3.png" class="card-img-top" alt="Miel 3">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">Miel 3</h5>
                                <p class="card-text">Descripción de la miel 3.</p>
                                <a href="#" class="btn btn-primary mt-auto">Ver Detalles</a>
                            </div>
                        </div>
                </div>
                <!-- Producto 4 -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card h-100 shadow-sm">
                            <img src="img/mieles/miel4.png" class="card-img-top" alt="Miel 4">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">Miel 4</h5>
                                <p class="card-text">Descripción de la miel 4.</p>
                                <a href="#" class="btn btn-primary mt-auto">Ver Detalles</a>
                            </div>
                        </div>
                </div>
                <!-- Boton de ver más -->
                <div class="col-12 text-center mt-4">
                    <a href="productos.php" class="btn btn-primary">Ver Más Productos</a>
                </div>

            </div>
        </div>

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
    </body>
</html>