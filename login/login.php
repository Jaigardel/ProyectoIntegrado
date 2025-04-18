<?php 
    require_once("../utiles/variables.php");
    require_once("../utiles/funciones.php");

    session_start();
    if($_SERVER["REQUEST_METHOD"] == "POST"){

        if(!isset($_POST["email"]) || !isset($_POST["contrasena"])){
            $error = "Por favor, completa todos los campos.";
            header("Refresh: 2; url=login.php");
            exit();
        }
        if(empty($_POST["email"]) || empty($_POST["contrasena"])){
            $error = "Por favor, completa todos los campos.";
            header("Refresh: 2; url=login.php");
            exit();
        }
        if(!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)){
            $error = "El formato del email no es correcto.";
            header("Refresh: 2; url=login.php");
            exit();
        }
        $clave = $_POST["contrasena"];
        $conexion = conectarPDO($host, $user, $password, $bbdd);
        
        $sql = "SELECT id, rol_id, contrasena FROM usuarios WHERE email = :email";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':email', $_POST["email"]);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            if(password_verify($clave, $resultado["contrasena"])){
                $_SESSION["usuarioId"] = $resultado["id"];
                $_SESSION["rol"] = $resultado["rol_id"];
                header("Location: ../index.php");
                exit();
            }
        }
        $error = "Email o contraseña incorrectos.";
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
        <div class="container-fluid">
            <section class="d-flex justify-content-between align-items-center">
                <img class="logo mb-0" src="../imagenes/logo.webp" alt="Logo de la pagina, imagen de una camara">
                <h2 class="mb-0">Rally Fotográfico</h2>
                <div>.</div>
            </section>
            <nav class="nav justify-content-around mt-3 grid-nav">
                <a href="../index.php" class="nav-link text-white">Inicio</a>
                <a href="../galeriaActiva.php" class="nav-link text-white">Galería Activa</a>
                <a href="../todasGalerias.php" class="nav-link text-white">Todas Las Galerías</a>
                <a href="../fotosGanadoras.php" class="nav-link text-white">Fotos Ganadoras</a>
            </nav>
        </div>
    </header>


    <main class="container-fluid flex-grow-1">
        <div class="row h-100">
            <div class="col-10 my-5 text-center" style="background-color: white; border-radius: 10px; padding: 20px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); max-width: 400px; margin: auto;">
                <h3>Inicia sesión</h3>
                <form method="POST" action="" id="loginForm">
                   
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico:</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                
                    <div class="mb-3">
                        <label for="contrasena" class="form-label">Contraseña:</label>
                        <input type="password" class="form-control" id="contrasena" name="contrasena" required>
                    </div>
                   
                    <button type="submit" class="btn btn-primary">Iniciar sesión</button>
                </form>

                <?php if (isset($error)): ?>
                    <p class="text-danger mt-3"><?php echo $error; ?></p>
                <?php endif; ?>

                <p class="mt-3">¿No tienes una cuenta? <a href="registro.php" style="color:blue">Regístrate aquí</a></p>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>