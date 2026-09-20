<!DOCTYPE html>
<html>
    <head>
        <title>Catálogo de Miel</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="estilos/estilos.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    </head>
    <body onload="cargarTarjetas();">
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

        <div class="container my-5">
            <h2 class="text-center mb-4">Nuestros Productos</h2>
            <div class="row g-4" id="contenedorProductos"></div>
        </div>

        <script>
            const responseAPI = {
                "status": 200,
                "message": "Productos obtenidos exitosamente",
                "data": [
                    {
                    "id": 1,
                    "nombre": "Miel de Ulmo",
                    "precio": "$6.500",
                    "descripcion": "Aroma floral pronunciado y textura cremosa única.",
                    "imagen": "img/mieles/miel1.png"
                    },
                    {
                    "id": 2,
                    "nombre": "Miel Multifloral",
                    "precio": "$5.900",
                    "descripcion": "Proveniente del bosque nativo, suave y balanceada.",
                    "imagen": "img/mieles/miel2.png"
                    },
                    {
                    "id": 3,
                    "nombre": "Miel de Quillay",
                    "precio": "$6.200",
                    "descripcion": "Sabor ambarino intenso, ideal para endulzar infusiones.",
                    "imagen": "img/mieles/miel3.png"
                    },
                    {
                    "id": 4,
                    "nombre": "Miel de Propóleo",
                    "precio": "$7.000",
                    "descripcion": "Concentrado natural con propiedades antibacterianas.",
                    "imagen": "img/mieles/miel4.png"
                    },
                ]
            };

            function cargarTarjetas() {
                const contenedor = document.getElementById("contenedorProductos");

                responseAPI.data.forEach(prod => {
                    const col = document.createElement("div");
                    col.className = "col-12 col-sm-6 col-lg-3";

                    col.innerHTML =`
                        <div class="card h-100 shadow-sm">
                            <img src="${prod.imagen}" class="card-img-top" alt="${prod.nombre}">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">${prod.nombre}</h5>
                                <p class="card-text text-muted">${prod.descripcion}</p>
                                <h6 class="text-warning fw-bold mb-3">${prod.precio}</h6>
                                <a href="detalle-producto.php?id=${prod.id}" class="btn btn-primary mt-auto">Ver Detalles</a>
                            </div>
                        </div>
                    `;

                    contenedor.appendChild(col);
                });
            }
        </script>

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