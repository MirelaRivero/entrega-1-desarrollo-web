<html>
    <head>
        <title>Página Productos</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            const responseAPI = {
                "status" : 200,
                "message" : "Productos obtenidos correctamente",
                "data" : [{"id" : "1", "nombre" : "Pelota"}, {"id" : "2", "nombre" : "Bate"}, {"id" : "3", "nombre" : "Zapatillas"}]

            }
            function cargarProducto() {
                let cmb = document.getElementById("cmbProducto");
                responseAPI.data.forEach(producto => {
                    let opt = document.createElement("option");
                    opt.setAttribute("value", producto.id);
                    opt.innerText = producto.nombre;
                    cmb.appendChild(opt);
                });
            }
        </script>
    </head>
    <body onload="cargarProducto()">
        <!--Estructura básica-->
        <!--Navbar -->
        <nav class="navbar navbar-expand-sm bg-dark navbar-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php">Logo</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="collapsibleNavbar">
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="a" role="button" data-bs-toggle="dropdown">Empresa</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="empresa.php">Conocenos</a></li>
                                <li><a class="dropdown-item" href="a">Misión</a></li>
                                <li><a class="dropdown-item" href="a">Visión</a></li>
                                <li><a class="dropdown-item" href="a">Valores</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="servicios.php">Servicios</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="productos.php">Productos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contacto.php">Contacto</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <div>
            <select id="cmbProducto" name="cmbProducto"></select>
        </div>

        <!--Conteiner -->
        
        <!--Footer-->
        <div class="container-fluid bg-danger">
            <div class="row">
                <div class="col-4"></div>
                <div class="col-4" style="color:white"><strong>MirelaRivero@2026</strong></div>
                <div class="col-4"></div>
            </div>
        </div>
    </body>
</html>