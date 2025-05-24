<?php

session_start();

require_once("../utiles/variables.php");
require_once("../utiles/funciones.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

$_SESSION["errorEmail"] = false;
if ($_SERVER["REQUEST_METHOD"]=="POST") {
    $email = obtenerValorCampo("email");

    if (!validarEmail($email)) {
        $_SESSION["errorEmail"] = true;
    } else {
        $conexion = conectarPDO($host, $user, $password, $bbdd);
        $sql = "SELECT email, nombre FROM usuarios WHERE email=?";
        $resultado = $conexion->prepare($sql);
        $resultado->bindParam(1, $email);
        $resultado->execute();
        if ($resultado->rowCount() != 1) {
            $_SESSION["errorEmail"] = true;
        } else {
            $usuario = $resultado->fetch(PDO::FETCH_ASSOC);
            cerrarPDO();

            $token = bin2hex(openssl_random_pseudo_bytes(16));

            $conexion = conectarPDO($host, $user, $password, $bbdd);
            $sql = "UPDATE usuarios SET estado=0, token=? WHERE email=?";
            $resultado = $conexion->prepare($sql);
            $resultado->bindParam(1, $token);
            $resultado->bindParam(2, $email);
            $resultado->execute();

            if ($resultado->rowCount() > 0) {
                $mail = new PHPMailer(true);
                try {
                    // Configuración SMTP
                    $mail->isSMTP();
                    $mail->Host = 'smtp.zoho.eu';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'rallyfotografico@zohomail.eu'; 
                    $mail->Password = 'avLs Y7PP 8T4i'; 
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $mail->Port = 465;
                    $mail->CharSet = 'UTF-8';

                    // Remitente y destinatario
                    $mail->setFrom('rallyfotografico@zohomail.eu', 'Rally Fotográfico');
                    $mail->addAddress($email, $usuario['nombre']);

                    // Contenido del correo
                    $mail->isHTML(true);
                    $mail->Subject = 'Recuperación de contraseña - Rally Fotográfico';

                    $urlValidacion = "https://jaigardel.byethost18.com/establecer.php?email=" . urlencode($email) . "&token=" . urlencode($token);

                    $mail->Body = "
                        <h2>Hola " . htmlspecialchars($usuario['nombre']) . "!</h2>
                        <p>Has solicitado recuperar tu contraseña en <strong>Rally Fotográfico</strong>.</p>
                        <p>Para establecer una nueva contraseña, haz clic en el siguiente enlace:</p>
                        <p><a href='$urlValidacion'>Recuperar contraseña</a></p>
                        <br>
                        <p>Si no solicitaste esta acción, puedes ignorar este correo.</p>
                        <hr>
                        <p>Saludos,<br>Equipo Rally Fotográfico</p>
                    ";

                    $mail->send();
                    $_SESSION["mensajeResultado"] = "✅ Operación realizada con éxito. Revisa tu correo para cambiar la contraseña.";
                    $_SESSION["estadoResultado"] = "exito";
                } catch (Exception $e) {
                    $_SESSION["mensajeResultado"] = "❌ Error al enviar el correo: " . $mail->ErrorInfo;

                }
            } else {
                $_SESSION["mensajeResultado"] = "❌ No se ha podido realizar la operación: ";

            }
            cerrarPDO();
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
            <h3>Recordar Contraseña</h3>
            <?php if (!empty($_SESSION["mensajeResultado"])){ ?>
                <div id="mensaje" class="text-center my-3 fw-bold"
                    style="color: <?= $_SESSION['estadoResultado'] === 'exito' ? 'green' : 'red' ?>">
                    <?= $_SESSION["mensajeResultado"] ?>
                </div>
            <?php }; ?>

            <form method="POST" action="recordar.php" id="recordarForm">
                <div class="m-3 text-center">
                    <label for="email" class="form-label">Correo Electrónico:</label>
                    <input type="email" class="form-control" id="email" name="email" required
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>

                <?php if (!empty($_SESSION["errorEmail"])): ?>
                    <p class="text-danger">El email no es válido o no existe</p>
                <?php endif; ?>

                <button type="submit" class="btn btn-primary w-100">Enviar enlace</button>
            </form>

            <p class="mt-3"><a href="login.php">Volver al Login</a></p>
        </div>
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
</body>
</html>