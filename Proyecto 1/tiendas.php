<!DOCTYPE html>
<html>
    <head>
        <title>Tiendas</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="estilos/estilos.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    </head>
    <body>
        <!--Estructura básica-->
        <!--Navbar -->
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
                                <li><a class="dropdown-item" href="a">Todo</a></li>
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
        <!--Conteiner -->
        <div class="container-fluid bg-success">
            <a href="empresa.php">Empresa</a><br>
            <a href="servicios.php">Servicios</a><br>
            <a href="productos.php">Productos</a><br>
            <a href="contacto.php">Contacto</a><br>
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