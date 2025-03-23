<?php 
    require_once("utiles/variables.php");
    require_once("utiles/funciones.php");
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
<body>
    <header class="bg-primary text-white py-3">
        <div class="container-fluid">
            <section class="d-flex justify-content-between align-items-center">
                <img class="logo mb-0" src="./imagenes/logo.webp" alt="Logo de la pagina, imagen de una camara">
                <h2 class="mb-0">Rally Fotográfico</h2>
                <p class="mb-0"><a href="/login/login.php">Identifícate</a> o <a href="/login/registro.php">Crea una cuenta</a></p>
            </section>
            <nav class="nav justify-content-around mt-3 grid-nav">
                <a href="index.php" class="nav-link text-white">Inicio</a>
                <a href="galeriaActiva.php" class="nav-link text-white">Galería Activa</a>
                <a href="todasGalerias.php" class="nav-link text-white">Todas Las Galerías</a>
                <a href="fotosGanadoras.php" class="nav-link text-white">Fotos Ganadoras</a>
                <a href="subirFotos.php" class="nav-link text-white">Subir Fotos</a>
            </nav>
        </div>
    </header>


    <main class="container-fluid">
        <div class="row">
            
            <div class="col-1" style="background-color: aliceblue; min-height: 100%;"></div>

            <div class="col-10 my-5 ">
                <div class="d-flex justify-content-center align-items-center">
                    <h2 class="h2 text-danger" style="background: rgba(245, 245, 220, 0.7); max-width: max-content;">
                        Tiempo Restante:
                    </h2>
                </div>
                <div class="row d-flex align-items-center">
                    <div class="col-md-4 order-1 order-md-2 text-center my-2">
                        <div class="animation">
                            <img src="./imagenes/pinguino.gif" class="img-fluid" alt="Animación">
                        </div>
                    </div>
                    <div class="col-md-6 order-2 order-md-1" style="background: rgba(245, 245, 220, 0.7); font-weight: bold; margin-left: 5%; max-width: 90%;">
                        <h2>Tema</h2>
                        <p>
                            Descripcion.
                        </p>
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
