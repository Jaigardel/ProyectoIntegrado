<?php 
    require_once("../utiles/variables.php");
    require_once("../utiles/funciones.php");

    session_start();
    if (!isset($_SESSION["rol"]) ||  $_SESSION["rol"] != 1 || !isset($_SESSION["usuarioId"])) {
        header("Location: ../index.php");
        exit();
    }

    $conexion = conectarPDO($host, $user, $password, $bbdd);
    
    $sqlUsuario = "SELECT nombre, avatar FROM usuarios WHERE id = $_SESSION[usuarioId]";
    
    $resultado = resultadoConsulta($conexion, $sqlUsuario);

    $registroUsu = $resultado->fetch(PDO::FETCH_ASSOC);
    
    cerrarPDO();

    $conexion = conectarPDO($host, $user, $password, $bbdd);
    // Usuarios activos o inactivos
    $sqlActivos = "SELECT id, nombre, apellidos, email, estado FROM usuarios WHERE rol_id = 2 AND estado IN (0, 1)";
    $stmt = $conexion->prepare($sqlActivos);
    $stmt->execute();
    $usuariosActivos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Usuarios bloqueados
    $sqlBloqueados = "SELECT id, nombre, apellidos, email FROM usuarios WHERE rol_id = 2 AND estado = 2";
    $stmt = $conexion->prepare($sqlBloqueados);
    $stmt->execute();
    $usuariosBloqueados = $stmt->fetchAll(PDO::FETCH_ASSOC);


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

            <div class="d-flex justify-content-end align-items-center">
                    <ul class="navbar-nav text-center">
                        <?php if ($_SESSION["rol"] == 1) { ?>
                            <li class="nav-item">
                                <a class="nav-link text-white" href="../admin.php">Panel de Control</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white" href="../usuario.php">Ver mis Fotos</a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link text-white" href="../login/cerrarSesion.php">Cerrar Sesión</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white d-flex align-items-center" href="../perfil.php">
                                    <i class="bi bi-person-fill me-1 text-white"></i> Hola,
                                    <?php echo $registroUsu["nombre"] ?> 
                                </a>
                            </li>
                        </ul>
                        <img src="<?= htmlspecialchars($registroUsu['avatar']) ?>" alt="Avatar" width="50" height="50" class="rounded-circle ms-2" style="object-fit: cover;">
                        <?php } elseif ($_SESSION["rol"] == 2) { ?>
                            <li class="nav-item">
                                <a class="nav-link text-white" href="../usuario.php">Ver mis Fotos</a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link text-white" href="../login/cerrarSesion.php">Cerrar Sesión</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white d-flex align-items-center" href="../perfil.php">
                                    <i class="bi bi-person-fill me-1 text-white"></i> Hola,
                                    <?php echo $registroUsu["nombre"] ?> 
                                </a>
                            </li>
                        </ul>
                        <img src="<?= htmlspecialchars($registroUsu['avatar']) ?>" alt="Avatar" width="50" height="50" class="rounded-circle ms-2" style="object-fit: cover;">
                        <?php } else { ?>
                            <li class="nav-item">
                                <a class="nav-link text-white" href="../login/login.php">Iniciar Sesión</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white" href="../login/registro.php">Registrarse</a>
                            </li>
                        <?php } ?>
                    
                    </div>
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
                <hr class="my-5 text-white">  

<h3 class="mb-4 text-center text-white">Usuarios Activos / Inactivos</h3>
<table class="table table-bordered table-hover text-center">
<thead class="table-primary">
<tr>
    <th>Nombre</th>
    <th>Apellidos</th>
    <th>Email</th>
    <th>Estado</th>
    <th>Acción</th>
    <th>Bloqueo</th>
</tr>
</thead>
<tbody>
<?php foreach ($usuariosActivos as $usuario): ?>
<tr>
    <td><?= htmlspecialchars($usuario['nombre']) ?></td>
    <td><?= htmlspecialchars($usuario['apellidos']) ?></td>
    <td><?= htmlspecialchars($usuario['email']) ?></td>
    <td><?= $usuario['estado'] ? 'Activo' : 'Inactivo' ?></td>
    <td>
        <a href="cambiarEstadoUsuario.php?usuario_id=<?= $usuario['id'] ?>&nuevo_estado=<?= $usuario['estado'] ? 0 : 1 ?>" 
            class="btn btn-sm btn-<?= $usuario['estado'] ? 'danger' : 'success' ?>">
            <?= $usuario['estado'] ? 'Inactivar' : 'Activar' ?>
        </a>
    </td>
    <td>
        <a href="cambiarEstadoUsuario.php?usuario_id=<?= $usuario['id'] ?>&nuevo_estado=2" 
            class="btn btn-sm btn-warning">
            Bloquear
        </a>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<h3 class="mb-4 text-center text-white">Usuarios Bloqueados</h3>
<table class="table table-bordered table-hover text-center">
<thead class="table-danger">
<tr>
    <th>Nombre</th>
    <th>Apellidos</th>
    <th>Email</th>
    <th>Acción</th>
</tr>
</thead>
<tbody>
<?php foreach ($usuariosBloqueados as $usuario): ?>
<tr>
    <td><?= htmlspecialchars($usuario['nombre']) ?></td>
    <td><?= htmlspecialchars($usuario['apellidos']) ?></td>
    <td><?= htmlspecialchars($usuario['email']) ?></td>
    <td>
        <a href="cambiarEstadoUsuario.php?usuario_id=<?= $usuario['id'] ?>&nuevo_estado=0" 
            class="btn btn-sm btn-success">
            Desbloquear
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
</html>
