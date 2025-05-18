<?php 
    require_once("../utiles/variables.php");
    require_once("../utiles/funciones.php");

    session_start();
    if (!isset($_SESSION["rol"]) ||  $_SESSION["rol"] != 1 || !isset($_SESSION["usuarioId"])) {
        header("Location: ../index.php");
        exit();
    }


    $conexion = conectarPDO($host, $user, $password, $bbdd);
    $sql = "SELECT id, nombre, apellidos, email, estado FROM usuarios WHERE rol_id = 2";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $conexion = null;

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
                <p class="mb-0"><a href="../admin.php">Panel de Control</a>, <a href="../usuario.php">Ver mis Fotos</a> o <a href="../login/cerrarSesion.php">Cerrar Sesion</a></p>
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
            <div style="background-color: white; border-radius: 10px; padding: 20px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); max-width: 800px; margin: auto;">   
                <h3 class="mb-4 text-center">Crear Nuevo Administrador</h3>
                    <form action="crearAdministrador.php" method="POST" class="mx-auto" style="max-width: 600px;">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="apellidos" class="form-label">Apellidos</label>
                            <input type="text" class="form-control" id="apellidos" name="apellidos" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Crear Administrador</button>
                    </form>
                </div>
                <hr class="my-5">  

                <h3 class="mb-4 text-center">Gestión de Usuarios</h3>
                <table class="table table-bordered table-hover text-center">
                    <thead class="table-primary">
                        <tr>
                            <th>Nombre</th>
                            <th>Apellidos</th>
                            <th>Email</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                                <td><?= htmlspecialchars($usuario['apellidos']) ?></td>
                                <td><?= htmlspecialchars($usuario['email']) ?></td>
                                <td>
                                    <?= $usuario['estado'] ? 'Activo' : 'Inactivo' ?>
                                </td>
                                <td>
                                    <a href="cambiarEstadoUsuario.php?usuario_id=<?= $usuario['id'] ?>&nuevo_estado=<?= $usuario['estado'] ? 0 : 1 ?>" 
                                    class="btn btn-sm btn-<?= $usuario['estado'] ? 'danger' : 'success' ?>">
                                        <?= $usuario['estado'] ? 'Inactivar' : 'Activar' ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                
                            
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
</html>
