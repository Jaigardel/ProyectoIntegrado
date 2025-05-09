<?php 
    require_once("utiles/variables.php");
    require_once("utiles/funciones.php");

    session_start();
    if (!isset($_SESSION["rol"])) {
        $_SESSION["rol"] = 0;
    }


    $conexion = conectarPDO($host, $user, $password, $bbdd);
    
    $sql = "SELECT id, titulo, descripcion, fecha_inicio, fecha_fin, url FROM rallys WHERE estado != 0";
    
    $resultado = resultadoConsulta($conexion, $sql);
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
</head>
<body class="d-flex flex-column min-vh-100">
<header class="bg-primary text-white py-3">
        <div class="container-fluid">
            <section class="d-flex justify-content-between align-items-center">
                <img class="logo mb-0" src="./imagenes/logo.webp" alt="Logo de la pagina, imagen de una camara">
                <h2 class="mb-0">
                    <a href="index.php" class="titulo-link">Rally Fotográfico</a>
                </h2>
                <?php if ($_SESSION["rol"] == 1) { ?>
                    <p class="mb-0"><a href="admin.php">Panel de Control</a>, <a href="usuario.php">Ver mis Fotos</a> o <a href="./login/cerrarSesion.php">Cerrar Sesion</a></p>
                <?php } else if($_SESSION["rol"] == 2) { ?>
                    <p class="mb-0"><a href="usuario.php">Ver mis Fotos</a> o <a href="./login/cerrarSesion.php">Cerrar Sesion</a></p>

                <?php } else{ ?>
                        <p class="mb-0"><a href="login/login.php">Identifícate</a> o <a href="login/registro.php">Crea una cuenta</a></p>
                <?php }?>
                
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
                <div class="row">
                    <?php 
                        while($registro = $resultado->fetch(PDO::FETCH_ASSOC)){
                            $fechaInicio = new DateTime($registro["fecha_inicio"]);
                            $fechaFin = new DateTime($registro["fecha_fin"]);
                        
                            echo '<div class="col-md-4 mb-4">
                                <div class="card h-100 d-flex flex-column align-items-center text-center">
                                    <div class="card-header">
                                        <h3 class="h5">'. $registro["titulo"] .'</h3>
                                    </div>
                                    <div class="card-img-container" style="height: 200px; overflow: hidden; display: flex; justify-content: center; align-items: center; background-color:rgb(215, 227, 239);">
                                        <img src="'. $registro["url"] .'" style="max-height: 100%; max-width: 100%; object-fit: contain;" alt="'. $registro["titulo"] .'">
                                    </div>
                                    <div class="card-body d-flex flex-column justify-content-between align-items-center">
                                        <p class="card-text">Duración: ' . $fechaInicio->format('d-m-Y') . " al " . $fechaFin->format('d-m-Y'). "</p>"; 
                                        if($fechaFin < new DateTime()){
                                            echo '<strong>Finalizado</strong>
                                            <br><a href="./galeria.php?id=' . $registro["id"] . '" class="btn btn-primary ampliar-btn">Ver Fotos</a>';
                                        } else {
                                            echo '<strong>En curso</strong>
                                            <br><a href="./galeriaActiva.php" class="btn btn-primary ampliar-btn">Ir a la galeria</a>';
                                        }
                                        echo '<br>
                                        
                                    </div>
                                </div>
                            </div>';
                        }
                        cerrarPDO($conexion);
                    ?>
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
