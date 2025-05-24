<?php
if(!isset($_SESSION["errorClave"])){
    $_SESSION["errorClave"] = false;
}
require_once("../utiles/variables.php");
require_once("../utiles/funciones.php");

$email = obtenerValorCampo("email");
$token = obtenerValorCampo("token");
$longitudMinima = 6;
$logintudMaxima = 20;
$conexion = conectarPDO($host, $user, $password, $bbdd);
            $sql = "SELECT token FROM usuarios WHERE email=?";
            $resultado = $conexion->prepare($sql);
			$resultado->bindParam(1, $email);
            $resultado->execute();
            if($resultado->rowCount()>0){
                $row = $resultado->fetch();
                $tokenBBDD = $row["token"];
                cerrarPDO();
            }

if($email == "" || $token == ""){
    echo "<h1 class='error'>Acceso Denegado.</h1>";
    header("Refresh: 3; url=../index.php");
    exit();
}elseif($token != $tokenBBDD){
    echo "<h1 class='error'>Error de autenticación, el enlace no es válido despues de un uso.</h1>";
    header("Refresh: 3; url=login.php");
    exit();
}else{
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $clave1 = obtenerValorCampo("clave1");
        $clave2 = obtenerValorCampo("clave2");

       if($clave1 != $clave2){
            $_SESSION["errorClave"] = true;
        } elseif(!validarLongitudCadena($clave1, $longitudMinima, $logintudMaxima)){
            $_SESSION["errorClave"] = true;
        } else {
            $conexion = conectarPDO($host, $user, $password, $bbdd);
            $sql = "UPDATE usuarios SET estado=1, contrasena=?, token = null WHERE email=?";
            $resultado = $conexion->prepare($sql);
            $clave = password_hash($clave1, PASSWORD_BCRYPT);
            $resultado->bindParam(1, $clave);
            $resultado->bindParam(2, $email);
            $resultado->execute();

            if ($resultado->rowCount() > 0) {
                $_SESSION["mensajeResultado"] = "✅ Contraseña cambiada correctamente.";
                $_SESSION["estadoResultado"] = "exito";
            } else {
                $_SESSION["mensajeResultado"] = "❌ No se ha podido cambiar la contraseña.";
                $_SESSION["estadoResultado"] = "error";
            }
            cerrarPDO();
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
    <link rel="stylesheet" href="../estilos/estilos.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
    <header class="bg-primary text-white py-3">
        <nav class="navbar navbar-expand-lg navbar-dark bg-transparent">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="../index.php">
            <img class="logo me-2" src="../imagenes/logo.webp" alt="Logo de la pagina, imagen de una camara">
            <span class="titulo-link">Rally Fotográfico</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
            <ul class="navbar-nav text-center">
                <li class="nav-item">
                    <a class="nav-link text-white" href="../index.php">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="../galeriaActiva.php">Galería Activa</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="../todasGalerias.php">Todas Las Galerías</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="../fotosGanadoras.php">Fotos Ganadoras</a>
                </li>
            </ul>

            <ul class="navbar-nav text-center">
                <?php if ($_SESSION["rol"] == 1){ ?>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="../admin.php">Panel de Control</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="../usuario.php">Ver mis Fotos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="cerrarSesion.php">Cerrar Sesión</a>
                    </li>
                <?php }elseif ($_SESSION["rol"] == 2){ ?>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="../usuario.php">Ver mis Fotos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="cerrarSesion.php">Cerrar Sesión</a>
                    </li>
                <?php }else{?>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="../login/login.php">Iniciar Sesión</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="../login/registro.php">Registrarse</a>
                    </li>
                <?php } ?>

        </div>
    </header>

    <main class="container-fluid flex-grow-1">
        <div class="row h-100">
            <div class="col-10 my-5 text-center" style="background-color: white; border-radius: 10px; padding: 20px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); max-width: 400px; margin: auto;">
    <h1>Nueva Contraseña</h1>     
    <?php if (!empty($_SESSION["mensajeResultado"])){ ?>
        <div id="mensaje" class="text-center my-3 fw-bold"
            style="color: <?= $_SESSION['estadoResultado'] === 'exito' ? 'green' : 'red' ?>">
            <?= $_SESSION["mensajeResultado"] ?>
        </div>
    <?php }; ?>
            <form method="POST" action="establecer.php" id="establecerForm">
            <div class="m-3 text-center">
                <label for="clave1" class="form-label">Nueva Contraseña:</label>
                <input type="password" class="form-control" id="clave1" name="clave1" required>
            </div>

            <div class="m-3 text-center">
                <label for="clave2" class="form-label">Repetir Contraseña:</label>
                <input type="password" class="form-control" id="clave2" name="clave2" required>
            </div>

            <?php if (!empty($_SESSION["errorClave"])): ?>
                <p class="text-danger text-center">La clave no tiene al menos 6 caracteres o no coincide</p>
            <?php endif; ?>

            <input type="hidden" name="email" value="<?= htmlspecialchars($email ?? '') ?>">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">

            <div class="m-3 text-center">
                <button type="submit" class="btn btn-primary w-100">Establecer Contraseña</button>
            </div>
        </form>

    <p><a href="login.php" class="mt-3 btn btn-primary w-100">Volver al Login</a></p>
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
    <?php if (!empty($_SESSION["estadoResultado"]) && $_SESSION["estadoResultado"] === "exito"){
        unset($_SESSION["mensajeResultado"]);
        unset($_SESSION["estadoResultado"]);
    ?>
        
<script>
    setTimeout(() => {
        window.location.href = "login.php";
    }, 3000); 
</script>
<?php }; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php }?>