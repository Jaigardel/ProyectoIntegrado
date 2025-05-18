<?php 
    require_once("../utiles/variables.php");
    require_once("../utiles/funciones.php");

   
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
                    $sqlInsert = "INSERT INTO usuarios (nombre, apellidos, email, contrasena, rol_id, estado) VALUES (:nombre, :apellidos, :email, :contrasena, 2, 0)";
                    $stmtInsert = $conexion->prepare($sqlInsert);
                    $stmtInsert->bindParam(':nombre', $nombre);
                    $stmtInsert->bindParam(':apellidos', $apellidos);
                    $stmtInsert->bindParam(':email', $email);
                    $stmtInsert->bindParam(':contrasena', $password);
                    if($stmtInsert->execute()){
                        $exito = "Usuario registrado con éxito. Un administrador validará tu cuenta lo antes posible.";
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
        <div class="container-fluid">
            <section class="d-flex justify-content-between align-items-center">
                <img class="logo mb-0" src="../imagenes/logo.webp" alt="Logo de la pagina, imagen de una camara">
                <h2 class="mb-0">
                    <a href="../index.php" class="titulo-link">Rally Fotográfico</a>
                </h2>
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
                    <?php if (isset($error)): ?>
                        <p class="text-danger mt-3"><?php echo $error; ?></p>
                    <?php endif; ?>
                    <?php if (isset($exito)): ?>
                        <p class="text-success mt-3"><?php echo $exito; ?></p>
                    <?php endif; ?>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>