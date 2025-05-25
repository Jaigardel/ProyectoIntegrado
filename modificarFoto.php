<?php

    require_once("utiles/variables.php");
    require_once("utiles/funciones.php");

    session_start();
    if (!isset($_SESSION["usuarioId"]) || $_SESSION["rol"] == 0) {
        header("Location: index.php");
        exit();
    }
    if (!isset($_GET["id"])) {
        header("Location: index.php");
        exit();
    }
    $conexion = conectarPDO($host, $user, $password, $bbdd);
    
    $sqlUsuario = "SELECT nombre, avatar FROM usuarios WHERE id = $_SESSION[usuarioId]";
    
    $resultado = resultadoConsulta($conexion, $sqlUsuario);

    $registroUsu = $resultado->fetch(PDO::FETCH_ASSOC);
    
    cerrarPDO();

    $conexion = conectarPDO($host, $user, $password, $bbdd);
    $fotoId = $_GET["id"];
    $usuarioId = $_SESSION["usuarioId"];
    $sql = "SELECT id, titulo, descripcion, url
            FROM fotos 
            WHERE id = :fotoId AND usuario_id = :usuarioId AND estado = 0";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':fotoId', $fotoId);
    $stmt->bindParam(':usuarioId', $usuarioId);
    $stmt->execute();
    if ($stmt->rowCount() == 0) {
        echo "<div class='alert alert-danger text-center'>⚠️ No se ha encontrado la foto o no tienes permiso para modificarla.</div>";
        if($_SESSION["rol"] == 1){
            header("Refresh: 2; url=./admin/fotosAdmin.php");
        }else{
            header("Refresh: 2; url=usuario.php");
        }
        exit();
    }else{
        if($_SERVER["REQUEST_METHOD"] === "POST"){
            $titulo = obtenerValorCampo("titulo");
            $descripcion = obtenerValorCampo("descripcion");
            $errores = [];
            if (empty($titulo)) {
                $errores[] = "El título no puede estar vacío.";
            }
            if (empty($descripcion)) {
                $errores[] = "La descripción no puede estar vacía.";
            }
            if (count($errores) > 0) {
                foreach ($errores as $error) {
                    echo "<div class='alert alert-danger text-center'>⚠️ $error</div>";
                }
                header("Refresh: 2; url=modificarFoto.php?id=$fotoId");
                exit();
            }
            $sql = "UPDATE fotos SET titulo=:titulo, descripcion = :descripcion WHERE id = :fotoId AND usuario_id = :usuarioId";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(':titulo', $titulo);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':fotoId', $fotoId);
            $stmt->bindParam(':usuarioId', $usuarioId);
            $stmt->execute();
            
            echo "<div class='alert alert-success text-center'>✅ Foto modificada correctamente.</div>";
            if($_SESSION["rol"] == 1){
                header("Refresh: 2; url=./admin/fotosAdmin.php");
            }else{
                header("Refresh: 2; url=usuario.php");
            }
            exit();
        }
        $foto = $stmt->fetch(PDO::FETCH_ASSOC);
        $titulo = $foto["titulo"];
        $descripcion = $foto["descripcion"];
        $fotoId = $foto["id"];
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
                <div class="collapse navbar-collapse navbar-collapse-center justify-content-between" id="navbarNav">
                    <ul class="navbar-nav mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link text-white" href="index.php">Inicio</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="galeriaActiva.php">Galería Activa</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="todasGalerias.php">Todas Las Galerías</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="fotosGanadoras.php">Fotos Ganadoras</a></li>
                    </ul>

                    <div class="d-flex align-items-center gap-2">
                        <ul class="navbar-nav mb-2 mb-lg-0">
                            <?php if ($_SESSION["rol"] == 1): ?>
                                <li class="nav-item"><a class="nav-link text-white" href="admin.php">Panel de Control</a></li>
                                <li class="nav-item"><a class="nav-link text-white" href="usuario.php">Ver mis Fotos</a></li>
                                <li class="nav-item"><a class="nav-link text-white" href="./login/cerrarSesion.php">Cerrar Sesión</a></li>
                                <li class="nav-item">
                                    <a class="nav-link text-white d-flex align-items-center" href="perfil.php">
                                        <i class="bi bi-person-fill me-1"></i> Hola, <?= htmlspecialchars($registroUsu["nombre"]) ?>
                                    </a>
                                </li>
                            <?php elseif ($_SESSION["rol"] == 2): ?>
                                <li class="nav-item"><a class="nav-link text-white" href="usuario.php">Ver mis Fotos</a></li>
                                <li class="nav-item"><a class="nav-link text-white" href="./login/cerrarSesion.php">Cerrar Sesión</a></li>
                                <li class="nav-item">
                                    <a class="nav-link text-white d-flex align-items-center" href="perfil.php">
                                        <i class="bi bi-person-fill me-1"></i> Hola, <?= htmlspecialchars($registroUsu["nombre"]) ?>
                                    </a>
                                </li>
                            <?php else: ?>
                                <li class="nav-item"><a class="nav-link text-white" href="login/login.php">Iniciar Sesión</a></li>
                                <li class="nav-item"><a class="nav-link text-white" href="login/registro.php">Registrarse</a></li>
                            <?php endif; ?>
                        </ul>

                        <?php if ($_SESSION["rol"] == 1 || $_SESSION["rol"] == 2): ?>
                            <img src="<?= htmlspecialchars($registroUsu['avatar']) ?>" alt="Avatar"
                                class="rounded-circle d-none d-md-block"
                                width="50" height="50" style="object-fit: cover;">
                        <?php endif; ?>

                    </div>

                    <?php if ($_SESSION["rol"] != 0) { ?>
                        <div class="w-100 text-center mt-3 d-lg-none">
                            <img src="<?= htmlspecialchars($registroUsu['avatar']) ?>" alt="Avatar" width="70" height="70" class="rounded-circle" style="object-fit: cover;">
                        </div>
                    <?php } ?>

                </div>
            </div>
        </nav>
    </header>
    <main class="container-fluid flex-grow-1">
        <div class="row h-100">
            <div class="col-1"></div>
            <div class="col-10 my-5 text-center" style="background-color: white; border-radius: 10px; padding: 20px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); max-width: 800px; margin: auto;">

                <h3>Modificar foto</h3>

                <form id="formularioFoto" method="post" action="modificarFoto.php?id=<?php echo $fotoId ?>">

                    <img src="<?php echo $foto["url"] ?>" alt="<?php echo $foto["titulo"] ?>" style="max-width: 70%; height: auto; border-radius: 10px; margin-bottom: 20px;">

                    <div class="mb-3">
                    <label for="tituloFoto" class="form-label">Título:</label>
                    <input type="text" class="form-control" name="titulo" value="<?php echo $foto["titulo"] ?>" required>
                    </div>

                    <div class="mb-3">
                    <label for="descripcionFoto" class="form-label">Descripción:</label>
                    <textarea class="form-control" name="descripcion" rows="3" required><?php echo $foto["descripcion"] ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Modificar Imagen</button>
                    <?php if($_SESSION["rol"] == 1){ ?>
                        <a href="./admin/fotosAdmin.php" class="btn btn-secondary">Volver</a>
                    <?php }else{ ?>
                        <a href="usuario.php" class="btn btn-secondary">Volver</a>
                    <?php } ?>
                </form>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>         
<?php
    }
?>
</html>