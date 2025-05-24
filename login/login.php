<?php 
    require_once("../utiles/variables.php");
    require_once("../utiles/funciones.php");

    session_start();

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(!isset($_POST["email"]) || !isset($_POST["contrasena"]) ||
            empty($_POST["email"]) || empty($_POST["contrasena"])){
            $error = "Por favor, completa todos los campos.";
        } elseif(!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)){
            $error = "El formato del email no es correcto.";
        } else {
            $clave = $_POST["contrasena"];
            $conexion = conectarPDO($host, $user, $password, $bbdd);

            $sql = "SELECT id, rol_id, contrasena, estado FROM usuarios WHERE email = :email";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(':email', $_POST["email"]);
            $stmt->execute();

            if($stmt->rowCount() > 0){
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

                if($resultado["estado"] == 0){
                    $error = "Tu cuenta está inactivada, comprueba tu correo o contacta con un administrador.";
                } elseif(password_verify($clave, $resultado["contrasena"])){
                    $_SESSION["usuarioId"] = $resultado["id"];
                    $_SESSION["rol"] = $resultado["rol_id"];
                    header("Location: ../index.php");
                    exit();
                } else {
                    $error = "Email o contraseña incorrectos.";
                }
            } else {
                $error = "Email o contraseña incorrectos.";
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
                <h3>Inicia sesión</h3>
                <form method="POST" action="" id="loginForm">
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico:</label>
                        <input type="email" class="form-control" id="email" name="email" required
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                
                    <div class="mb-3">
                        <label for="contrasena" class="form-label">Contraseña:</label>
                        <input type="password" class="form-control" id="contrasena" name="contrasena" required minlength="6">
                        <small id="passwordHelp" class="form-text text-muted">La contraseña debe tener al menos 6 caracteres.</small>
                    </div>
                   
                    <button type="submit" class="btn btn-primary">Iniciar sesión</button>
                </form>

                <?php if (isset($error)): ?>
                    <p class="text-danger mt-3"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>

                <p class="mt-3">¿No tienes una cuenta? <a href="registro.php" style="color:blue">Regístrate aquí</a></p>
                <p class="mt-3">¿Has olvidado tu contraseña? <a href="recordar.php" style="color:blue">Recupérala aquí</a></p>
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

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(event) {
            const password = document.getElementById('contrasena').value;
            if (password.length < 6) {
                event.preventDefault();
                alert("La contraseña debe tener al menos 6 caracteres.");
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
