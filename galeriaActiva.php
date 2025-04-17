<?php 
    require_once("utiles/variables.php");
    require_once("utiles/funciones.php");

    session_start();
    if (!isset($_SESSION["rol"])) {
        $_SESSION["rol"] = 0;
    }

    $conexion = conectarPDO($host, $user, $password, $bbdd);
    
    $sql = "SELECT f.id, f.titulo AS titulo, f.descripcion AS descripcionFoto, f.url AS url, r.titulo AS tema, r.descripcion AS descripcion, r.fecha_fin AS fin, r.id AS rally FROM fotos f JOIN rallys r ON f.rally_id = r.id WHERE r.estado = 1 AND f.estado = 1 ORDER BY f.id DESC";
    
    $resultado = resultadoConsulta($conexion, $sql);
    
    $foto = $resultado->fetch(PDO::FETCH_ASSOC);

    $fechaFin = $foto["fin"];

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["foto_id"])) {
        $fotoId = $_POST["foto_id"];
        $ipUsuario = obtenerIPUsuario();
    
        if (isset($_POST["quitar_voto"])) {
            // Eliminar el voto
            $sqlDelete = "DELETE FROM votos WHERE foto_id = :foto_id AND ip = :ip";
            $stmtDelete = $conexion->prepare($sqlDelete);
            $stmtDelete->execute([
                ':foto_id' => $fotoId,
                ':ip' => $ipUsuario
            ]);
            echo "<div class='alert alert-info text-center'>🗑️ Tu voto fue eliminado.</div>";
        } else {
            // Insertar voto si no existe
            $sql = "SELECT COUNT(*) FROM votos WHERE foto_id = :foto_id AND ip = :ip";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([
                ':foto_id' => $fotoId,
                ':ip' => $ipUsuario
            ]);
    
            if ($stmt->fetchColumn() == 0) {
                $sqlInsert = "INSERT INTO votos (foto_id, ip) VALUES (:foto_id, :ip)";
                $stmtInsert = $conexion->prepare($sqlInsert);
                $stmtInsert->execute([
                    ':foto_id' => $fotoId,
                    ':ip' => $ipUsuario
                ]);
                echo "<div class='alert alert-success text-center'>✅ ¡Gracias por tu voto!</div>";
            } else {
                echo "<div class='alert alert-warning text-center'>⚠️ Ya votaste por esta foto.</div>";
            }
        }
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
</head>
<body class="d-flex flex-column min-vh-100">
<header class="bg-primary text-white py-3">
        <div class="container-fluid">
            <section class="d-flex justify-content-between align-items-center">
                <img class="logo mb-0" src="./imagenes/logo.webp" alt="Logo de la pagina, imagen de una camara">
                <h2 class="mb-0">Rally Fotográfico</h2>
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
                <a href="subirFotos.php?rallyId=<?php echo $foto["rally"]?>" class="nav-link text-white">Subir Fotos</a>
            </nav>
        </div>
    </header>

    <main class="container-fluid flex-grow-1">
        <div class="row h-100">
            <div class="d-flex justify-content-center align-items-center" style="background: rgba(245, 245, 220, 0.7); max-width: 100%;">
                <h2 class="h2 text-danger">
                    Tiempo Restante: 
                    <span id="contador" class="fs-4 fw-bold text-danger"></span>
                </h2>
                
            </div>
            <div class="col-1" style="background-color: aliceblue;"></div>

            <div class="col-10 my-5 ">
                
                <div class="row d-flex align-items-center">
                    <div class="col-md-4 order-1 order-md-2 text-center my-2">
                        <div class="animation">
                            <img src="./imagenes/pinguino.gif" class="img-fluid" alt="Animación">
                        </div>
                    </div>
                    <div class="col-md-6 order-2 order-md-1" style="font-weight: bold; margin-left: 5%; max-width: 90%;">
                        <h2><?php echo $foto["tema"]?></h2>
                        <p><?php echo $foto["descripcion"]?></p>
                    </div>
                </div>
                <div class="row">
                    <?php
                        do {
                            echo '
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100 d-flex flex-column align-items-center text-center">
                                        <div class="card-header">
                                            <h3 class="h5">'. $foto["titulo"] .'</h3>
                                        </div>
                                        <div class="card-img-container" style="height: 200px; overflow: hidden; display: flex; justify-content: center; align-items: center; background-color:rgb(215, 227, 239);">
                                            <img src="'. $foto['url'] .'" style="max-height: 100%; max-width: 100%; object-fit: contain;" alt="'. $foto['titulo'] .'">
                                        </div>
                                        <div class="card-body d-flex flex-column justify-content-between align-items-center">
                                            <p class="card-text">' . $foto['descripcionFoto'] . '</p>
                                            ' . renderBotonVoto($foto['id'], $conexion) . '
                                        </div>
                                    </div>
                                </div>
                            ';
                        }while($foto = $resultado->fetch(PDO::FETCH_ASSOC))
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
    <script>

    const fechaFin = new Date("<?php echo $fechaFin; ?>");

    function actualizarContador() {
        const ahora = new Date();
        const diferencia = fechaFin - ahora;

        if (diferencia <= 0) {
            document.getElementById("contador").textContent = "⏰ ¡El concurso ha terminado!";
            clearInterval(intervalo);
            return;
        }

        const dias = Math.floor(diferencia / (1000 * 60 * 60 * 24));
        const horas = Math.floor((diferencia % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutos = Math.floor((diferencia % (1000 * 60 * 60)) / (1000 * 60));
        const segundos = Math.floor((diferencia % (1000 * 60)) / 1000);

        document.getElementById("contador").textContent =
            ` ${dias}d ${horas}h ${minutos}m ${segundos}s`;
    }

    const intervalo = setInterval(actualizarContador, 1000);
    actualizarContador(); 
</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
