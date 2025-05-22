<?php 
    require_once("utiles/variables.php");
    require_once("utiles/funciones.php");

    session_start();
    if (!isset($_SESSION["rol"])) {
        $_SESSION["rol"] = 0;
    }

    $conexion = conectarPDO($host, $user, $password, $bbdd);
    
    $sql = "SELECT u.nombre AS ganador, u.apellidos AS ganadorApellidos, vf.descripcion AS descripcion, vf.titulo AS foto_titulo, vf.url AS enlace, vf.total_votos, r.titulo AS rally_titulo
        FROM (SELECT f.id AS foto_id, f.rally_id, f.usuario_id, f.titulo, f.descripcion, f.url, COUNT(v.id) AS total_votos
        FROM fotos f
        LEFT JOIN votos v ON f.id = v.foto_id
        GROUP BY f.id, f.rally_id, f.usuario_id, f.titulo, f.descripcion, f.url
        ) AS vf
        JOIN usuarios u ON vf.usuario_id = u.id
        JOIN rallys r ON vf.rally_id = r.id
        WHERE vf.total_votos = (
            SELECT MAX(vf2.total_votos)
            FROM (SELECT f2.id, f2.rally_id, COUNT(v2.id) AS total_votos
                FROM fotos f2
                LEFT JOIN votos v2 ON f2.id = v2.foto_id
                GROUP BY f2.id, f2.rally_id
            ) AS vf2
            WHERE vf2.rally_id = vf.rally_id 
        )
        AND r.estado = 2;";
    
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
        <nav class="navbar navbar-expand-lg navbar-dark bg-transparent">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img class="logo me-2" src="./imagenes/logo.webp" alt="Logo de la pagina, imagen de una camara">
            <span class="titulo-link">Rally Fotográfico</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
            <ul class="navbar-nav text-center">
                <li class="nav-item">
                    <a class="nav-link text-white" href="index.php">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="galeriaActiva.php">Galería Activa</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="todasGalerias.php">Todas Las Galerías</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="fotosGanadoras.php">Fotos Ganadoras</a>
                </li>
            </ul>

            <ul class="navbar-nav text-center">
                <?php if ($_SESSION["rol"] == 1){ ?>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="admin.php">Panel de Control</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="usuario.php">Ver mis Fotos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="./login/cerrarSesion.php">Cerrar Sesión</a>
                    </li>
                <?php }elseif ($_SESSION["rol"] == 2){ ?>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="usuario.php">Ver mis Fotos</a>
                    </li>
                    <li class="nav-item">
                <?php }else{?>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="login/login.php">Iniciar Sesión</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="login/registro.php">Registrarse</a>
                    </li>
                <?php } ?>

        </div>
    </header>


    <main class="container-fluid flex-grow-1">
        <div class="row h-100">
            <div class="col-1"></div>

            <div class="col-10 my-5">
               <div class="row">
                        <?php 
                           
                            while($registro = $resultado->fetch(PDO::FETCH_ASSOC)){
                                echo '<div class="col-md-4 mb-4">
                                <div class="card h-100 d-flex flex-column align-items-center text-center">
                                    <div class="card-header">
                                        <h3 class="h5">'. $registro["rally_titulo"] .'</h3>
                                    </div>
                                    <div class="card-img-container" style="height: 200px; overflow: hidden; display: flex; justify-content: center; align-items: center; background-color:rgb(215, 227, 239);">
                                        <img loading="lazy" src="'. $registro["enlace"] .'" style="max-height: 100%; max-width: 100%; object-fit: contain;" alt="'. $registro["foto_titulo"] .'">
                                    </div>
                                    <div class="card-body d-flex flex-column justify-content-between align-items-center">
                                        <h5 class="card-title h5">'. $registro["foto_titulo"] .'</h5>
                                        <p class="card-text">Fotógraf@: ' . $registro["ganador"] . " " . $registro["ganadorApellidos"] .'.</p>
                                        <button class="btn btn-primary ampliar-btn" data-url="' . $registro["enlace"] . '" data-titulo="' . $registro["foto_titulo"] . '" data-descripcion="' . $registro["descripcion"] . '">Ampliar</button>
                                    </div>
                                </div></div>';

                            };
                            cerrarPDO($conexion);
                        ?>
                                
                    </div>
               </div>
            
    
            <div class="col-1"></div>
        </div>
    </main>
    
    

    <footer class="bg-primary text-white text-center py-3">
        <div class="row d-flex justify-content-between">
            <div class="col-sm-4 order-1 order-md-2 text-center">
                <h3 class="h5">Contacto</h3>
                <p>Email: email@email.com</p>
                <p>Teléfono: (+34) 123 456 789</p>
            </div>
            <div class="col-sm-4 order-1 order-md-2 text-center text-white">
                <h3 class="h5">Links</h3>
                <p>
                    <a href="#" target="_blank" class="mx-2" aria-label="Instagram">
                        <i class="bi bi-instagram fs-2"></i>
                    </a>
                    <a href="#" target="_blank" class="mx-2" aria-label="Facebook">
                        <i class="bi bi-facebook fs-2"></i>
                    </a>
                    <a href="#" target="_blank" class="mx-2" aria-label="Twitter">
                        <i class="bi bi-twitter fs-2"></i>
                    </a>
                </p>
            </div>

        </div>
        
    </footer>
    <button class="boton-sticky" onclick="scrollToTop()">↑</button>


    <div class="popup" id="popup" aria-live="assertive">
        <span class="cerrar" id="cerrar">X</span>
        <h1 class="subsubtitulo text-center h4" id="popupTitulo"></h1>
        <img id="popupImg" src="" alt="" style="max-width: 100%; height: auto; margin: 0 auto; display: block;">
        <p class="subsubtitulo text-center" id="popupDescripcion"></p>
    </div>

    <script>
        document.querySelectorAll('.ampliar-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const titulo = btn.getAttribute('data-titulo');
                const url = btn.getAttribute('data-url');
                const descripcion = btn.getAttribute('data-descripcion') || 'Descripción no disponible.';

                document.getElementById('popupTitulo').textContent = titulo;
                document.getElementById('popupImg').src = url;
                document.getElementById('popupImg').alt = titulo;
                document.getElementById('popupDescripcion').textContent = descripcion;

                document.getElementById('popup').classList.add('mostrar');
            });
        });

        document.getElementById('cerrar').addEventListener('click', () => {
            document.getElementById('popup').classList.remove('mostrar');
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === "Escape") {
                document.getElementById('popup').classList.remove('mostrar');
            }
        });
    </script>
    <script>
        function scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
