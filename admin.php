<?php 
    require_once("utiles/variables.php");
    require_once("utiles/funciones.php");

    session_start();
    if (!isset($_SESSION["rol"]) ||  $_SESSION["rol"] != 1 || !isset($_SESSION["usuarioId"])) {
        header("Location: index.php");
        exit();
    }

?>  
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rally Fotográfico</title>
    <link rel="icon" href="/imagenes/favicon.png" type="image/png">
    <link rel="stylesheet" href="./estilos/estilos.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .menu-container {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            transition: transform 0.2s ease-in-out;
        }
        .card:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <header class="bg-primary text-white py-3">
        <div class="container-fluid">
            <section class="d-flex justify-content-between align-items-center">
                <img class="logo mb-0" src="./imagenes/logo.webp" alt="Logo de la pagina, imagen de una camara">
                <h2 class="mb-0">Rally Fotográfico</h2>
                <p class="mb-0"><a href="admin.php">Panel de Control</a>, <a href="usuario.php">Ver mis Fotos</a> o <a href="./login/cerrarSesion.php">Cerrar Sesion</a></p>
            </section>
            <nav class="nav justify-content-around mt-3 grid-nav">
                <a href="index.php" class="nav-link text-white">Inicio</a>
                <a href="galeriaActiva.php" class="nav-link text-white">Galería Activa</a>
                <a href="todasGalerias.php" class="nav-link text-white">Todas Las Galerías</a>
                <a href="fotosGanadoras.php" class="nav-link text-white">Fotos Ganadoras</a>
            </nav>
        </div>
    </header>


    <main class="container-fluid flex-grow-1">
        <div class="row h-100">
            <div class="col-1" style="background-color: aliceblue;"></div>

            <div class="col-10 my-5">
                <div class="container menu-container">
                    <div class="row text-center justify-content-center gap-4">
                        <div class="col-10 col-md-5 d-flex align-items-stretch">
                            <div class="card shadow border-primary h-100 w-100">
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <div>
                                        <h5 class="card-title">Gestionar Fotos</h5>
                                        <p class="card-text">Administra las fotos subidas por los usuarios.</p>
                                    </div>
                                    <a href="./admin/fotosAdmin.php" class="btn btn-primary mt-3">Ir</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-10 col-md-5 d-flex align-items-stretch">
                            <div class="card shadow border-success h-100 w-100">
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <div>
                                        <h5 class="card-title">Gestión de Usuarios</h5>
                                        <p class="card-text">Ver, editar o eliminar usuarios registrados.</p>
                                    </div>
                                    <a href="./admin/gestionUsuarios.php" class="btn btn-success mt-3">Ir</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-10 col-md-5 d-flex align-items-stretch">
                            <div class="card shadow border-info h-100 w-100">
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <div>
                                        <h5 class="card-title">Gestión de Rallys</h5>
                                        <p class="card-text">Organiza y administra los rallys fotográficos.</p>
                                    </div>
                                    <a href="./admin/gestionRallys.php" class="btn btn-info mt-3">Ir</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-10 col-md-5 d-flex align-items-stretch">
                            <div class="card shadow border-info h-100 w-100">
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <div>
                                        <h5 class="card-title">Añadir Rallys</h5>
                                        <p class="card-text">Añade nuevos rallys fotográficos.</p>
                                    </div>
                                    <a href="./admin/anadirRallys.php" class="btn btn-warning mt-3">Ir</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
    
            <div class="col-1" style="background-color: aliceblue;"></div>
        </div>
    </main>
    
    

    <footer class="bg-primary text-white text-center py-3">
        <div class="row d-flex justify-content-between">
            <div class="col-sm-4 order-1 order-md-2 text-center">
                <h3 class="h5">Contacto</h3>
                <p>Email: email@email.com</p>
                <p>Teléfono: (+34) 123 456 789</p>
            </div>
            <div class="col-sm-4 order-1 order-md-2 text-center">
                <h3 class="h5">Links</h3>
                <p>
                    <a href="#" target="_blank" class="text-dark mx-2">
                        <i class="bi bi-instagram fs-2"></i>
                    </a>
                    <a href="#" target="_blank" class="text-dark mx-2">
                        <i class="bi bi-facebook fs-2"></i>
                    </a>
                    <a href="#" target="_blank" class="text-dark mx-2">
                        <i class="bi bi-twitter fs-2"></i>
                    </a>
                </p>
            </div>

        </div>
        
    </footer>
    <button class="boton-sticky" onclick="scrollToTop()">↑</button>

    <script>
        function scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
