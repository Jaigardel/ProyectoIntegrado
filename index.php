<?php 
    require_once("utiles/variables.php");
    require_once("utiles/funciones.php");

    $conexion = conectarPDO($host, $user, $password, $bbdd);
    
    $sql = "SELECT titulo, r.url AS url FROM rallys r WHERE estado = 1";
    
    $resultado = resultadoConsulta($conexion, $sql);

    $registro = $resultado->fetch(PDO::FETCH_ASSOC);
    
    cerrarPDO();

    $conexion = conectarPDO($host, $user, $password, $bbdd);
    
    $sql = "SELECT u.nombre AS ganador, u.apellidos AS ganadorApellidos, f.titulo AS titulo_ganadora, f.url AS foto_ganadora
        FROM fotos f
        JOIN usuarios u ON f.usuario_id = u.id
        JOIN rallys r ON f.rally_id = r.id
        LEFT JOIN (
            SELECT foto_id, COUNT(*) AS votos
            FROM votos
            GROUP BY foto_id
        ) v ON f.id = v.foto_id
        WHERE r.id = (SELECT id FROM rallys WHERE estado = 0 ORDER BY id DESC LIMIT 1)
        ORDER BY votos DESC
        LIMIT 1;";
    
    $resultado = resultadoConsulta($conexion, $sql);

    $registro2 = $resultado->fetch(PDO::FETCH_ASSOC);
    
    cerrarPDO();
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
                <h2 class="mb-0">Rally Fotográfico</h2>
                <p class="mb-0"><a href="/login/login.php">Identifícate</a> o <a href="/login/registro.php">Crea una cuenta</a></p>
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
                <div class="row d-flex align-items-center">
                    <div class="col-md-4 order-1 order-md-2 text-center my-2">
                        <div class="animation">
                            <img src="./imagenes/pinguino.gif" class="img-fluid" alt="Animación">
                        </div>
                    </div>
                    <div class="col-md-6 order-2 order-md-1" style="background: rgba(245, 245, 220, 0.7); font-weight: bold; margin-left: 5%; max-width: 90%;">
                        <h2>¡Bienvenidos a nuestra web de fotografía!</h2>
                        <p>
                            Explora nuestra comunidad de fotógrafos y participa en nuestros concursos semanales para ganar premios.
                        </p>
                        <p>
                            En esta web encontrarás un sinfín de temas que explorar y concursos en los que participar.
                            Consulta nuestras galerías y no dudes en apuntarte cuando quieras.
                        </p>
                    </div>
                </div>
    
                <section class="container my-5">
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 d-flex flex-column align-items-center text-center">
                                <img src="<?php echo $registro["url"]?>" class="card-img-top" alt="Foto destacada">
                                <div class="card-body d-flex flex-column justify-content-between align-items-center">
                                    <h5 class="card-title">Tema de la Semana</h5>
                                    <p class="card-text"><?php echo $registro["titulo"]?>.</p>
                                    <a href="./galeriaActiva.php" class="btn btn-primary">Ir a la galería</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 d-flex flex-column align-items-center text-center">
                                <img src="./imagenes/fotografo.jpg" class="card-img-top" alt="Foto destacada">
                                <div class="card-body d-flex flex-column justify-content-between align-items-center">
                                    <h5 class="card-title">Ganador de la última competición</h5>
                                    <p class="card-text"><?php echo "$registro2[ganador] $registro2[ganadorApellidos]"?>.</p>
                                    <button class="btn btn-primary" onclick="mostrar()">Ver foto</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 d-flex flex-column align-items-center text-center">
                                <img src="./imagenes/camaras.jpg" class="card-img-top" alt="Foto destacada">
                                <div class="card-body d-flex flex-column justify-content-between align-items-center">
                                    <h5 class="card-title">Herramientas de fotografía</h5>
                                    <p class="card-text">Explora los siguientes artículos en esta tienda de confianza.</p>
                                    <a href="https://www.fotocasion.es/?srsltid=AfmBOorQ3uI3rOmzrXQYf_8LfPxHqRp-p5lztrGvUKRK3i767WloK2FI" target="_blank" class="btn btn-primary">Ir a la tienda</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
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
                <p>
                <?php  
                    $ipUsuario = obtenerIPUsuario();
                    echo "Tu IP es: " . $ipUsuario;
                ?>
                </p>
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

    <div class="popup" id="popup" aria-live="assertive">
        <span class="cerrar" id="cerrar">X</span>
        <p class="subsubtitulo text-center"><?php echo $registro2["titulo_ganadora"]?></p>
        <img src="<?php echo $registro2["foto_ganadora"]?>" alt="<?php echo $registro2["titulo_ganadora"]?>" style="max-width: 100%; height: auto; margin: 0 auto; display: block;">
    </div>

    <script>
        function mostrar(){
            document.getElementById('popup').classList.add('mostrar');
        }

        document.getElementById('cerrar').addEventListener('click', () => {
            document.getElementById('popup').classList.remove('mostrar');
        });
        document.addEventListener('keydown', (e) => {
            if(e.key === "Escape")
            document.getElementById('popup').classList.remove('mostrar');
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
