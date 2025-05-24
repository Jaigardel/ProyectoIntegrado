<?php
session_start();
require_once("../utiles/variables.php");
require_once("../utiles/funciones.php");

if(isset($_GET['email']) && isset($_GET['token'])){
    $email = $_GET['email'];
    $token = $_GET['token'];

    $conexion = conectarPDO($host, $user, $password, $bbdd);

    $sql = "SELECT estado, token FROM usuarios WHERE email = :email";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if($stmt->rowCount() > 0){
        $usuario = $stmt->fetch();

        if($usuario['estado'] == 1){
            $mensaje = "La cuenta ya está activada.";
        } elseif($usuario['token'] === $token) {
            // Activar usuario
            $sqlUpdate = "UPDATE usuarios SET estado = 1, token = NULL WHERE email = :email";
            $stmtUpdate = $conexion->prepare($sqlUpdate);
            $stmtUpdate->bindParam(':email', $email);
            if($stmtUpdate->execute()){
                $mensaje = "Cuenta activada con éxito. Ya puedes iniciar sesión.";
            } else {
                $mensaje = "Error al activar la cuenta.";
            }
        } else {
            $mensaje = "Token inválido o expirado.";
        }
    } else {
        $mensaje = "Usuario no encontrado.";
    }
    cerrarPDO();
} else {
    $mensaje = "Parámetros inválidos.";
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
    <h1>Activar Cuenta</h1>
    <p><?php echo htmlspecialchars($mensaje); ?></p>
    <p><a href="login.php" class="mt-3 btn btn-primary w-100">Volver al Login</a></p>
            </div>
            </main>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
