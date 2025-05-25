<?php 
session_start();
require_once("../utiles/variables.php");
require_once("../utiles/funciones.php");
require_once('../PHPMailer/src/PHPMailer.php');
require_once('../PHPMailer/src/SMTP.php');
require_once('../PHPMailer/src/Exception.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $conexion = conectarPDO($host, $user, $password, $bbdd);

    $sql = "SELECT id FROM usuarios WHERE email = :email";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':email', $_POST["email"]);
    $stmt->execute();
    if($stmt->rowCount() > 0){
        $error = "El email ya está registrado.";
    }else{
        if(isset($_POST["nombre"]) && isset($_POST["apellidos"]) && isset($_POST["password"]) && isset($_POST["confirmar_password"])){
            $nombre = $_POST["nombre"];
            $apellidos = $_POST["apellidos"];
            $email = $_POST["email"];
            $password = password_hash($_POST["password"], PASSWORD_BCRYPT);
            $confirmar_password = $_POST["confirmar_password"];

            if($_POST["password"] === $confirmar_password){
                $token = bin2hex(random_bytes(16)); 

                $sqlInsert = "INSERT INTO usuarios (nombre, apellidos, email, contrasena, rol_id, estado, token) VALUES (:nombre, :apellidos, :email, :contrasena, 2, 0, :token)";
                $stmtInsert = $conexion->prepare($sqlInsert);
                $stmtInsert->bindParam(':nombre', $nombre);
                $stmtInsert->bindParam(':apellidos', $apellidos);
                $stmtInsert->bindParam(':email', $email);
                $stmtInsert->bindParam(':contrasena', $password);
                $stmtInsert->bindParam(':token', $token);

                if($stmtInsert->execute()){
                    try {
                        $mail = new PHPMailer(true);
                        $mail->isSMTP();
                        $mail->Host = 'smtp.zoho.eu';
                        $mail->SMTPAuth = true;
                        $mail->Username = 'rallyfotografico@zohomail.eu'; 
                        $mail->Password = 'avLs Y7PP 8T4i';  
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                        $mail->Port = 465;
                        $mail->CharSet = 'UTF-8';

                        $mail->setFrom('rallyfotografico@zohomail.eu', 'Rally Fotográfico');
                        $mail->addAddress($email, $nombre);

                        $mail->isHTML(true);
                        $mail->Subject = 'Activa tu cuenta - Rally Fotográfico';

                        $urlActivacion = "https://jaigardel.byethost18.com/activar.php?email=" . urlencode($email) . "&token=" . urlencode($token);

                        $mail->Body = "
                            <h2>Hola " . htmlspecialchars($nombre) . "!</h2>
                            <p>Gracias por registrarte en <strong>Rally Fotográfico</strong>.</p>
                            <p>Para activar tu cuenta, haz clic en el siguiente enlace:</p>
                            <p><a href='$urlActivacion'>Activar mi cuenta</a></p>
                            <br>
                            <p>Si no solicitaste este registro, ignora este mensaje.</p>
                            <hr>
                            <p>Saludos,<br>Equipo Rally Fotográfico</p>
                        ";

                        $mail->send();
                        $_SESSION["mensajeResultado"] = "✅ Usuario registrado correctamente. Revisa tu correo para activar la cuenta.";
                        $_SESSION["estadoResultado"] = "exito";
                    } catch (Exception $e) {
                        $_SESSION["mensajeResultado"] = "❌ Error al enviar el correo: " . $mail->ErrorInfo;
                    }
                }else{
                    $error = "Error al registrar el usuario.";
                }
            }else{
                $error = "Las contraseñas no coinciden.";
            }
        }
    }
    cerrarPDO();
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
            <div class="col-1"></div>

            <div class="col-10 my-5">
                <div class="col-10 my-5 text-center" style="background-color: white; border-radius: 10px; padding: 20px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); max-width: 400px; margin: auto;">
                    <h3 class="text-center mb-4">Registro de Usuario</h3>
                    <form action="registro.php" method="POST" class="mx-auto" style="max-width: 600px;">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" required value="<?= !isset($exito) ? htmlspecialchars($_POST['nombre'] ?? '') : '' ?>">
                        </div>

                        <div class="mb-3">
                            <label for="apellidos" class="form-label">Apellidos</label>
                            <input type="text" name="apellidos" id="apellidos" class="form-control" required value="<?= !isset($exito) ? htmlspecialchars($_POST['apellidos'] ?? '') : '' ?>">
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico</label>
                            <input type="email" name="email" id="email" class="form-control" required value="<?= !isset($exito) ? htmlspecialchars($_POST['email'] ?? '') : '' ?>">
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" name="password" id="password" class="form-control" minlength="6" required>
                            <small id="passwordHelp" class="form-text text-muted">La contraseña debe tener al menos 6 caracteres.</small>
                        </div>

                        <div class="mb-3">
                            <label for="confirmar_password" class="form-label">Confirmar contraseña</label>
                            <input type="password" name="confirmar_password" id="confirmar_password" class="form-control" required>
                            <small id="passwordHelp" class="form-text text-muted">La contraseña debe tener al menos 6 caracteres.</small>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Registrarse</button>
                        </div>
                    </form>
                    <?php if (!empty($_SESSION["mensajeResultado"])){ ?>
                        <div id="mensaje" class="text-center my-3 fw-bold"
                            style="color: <?= $_SESSION['estadoResultado'] === 'exito' ? 'green' : 'red' ?>">
                            <?= $_SESSION["mensajeResultado"] ?>
                        </div>
                    <?php }; ?>
                    <p class="mt-3">¿Ya tienes una cuenta? <a href="login.php" style="color:blue">Inicia sesión aquí</a></p>
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

    <script>
        function scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>