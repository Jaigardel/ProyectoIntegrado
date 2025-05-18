<?php 
    require_once("../utiles/variables.php");
    require_once("../utiles/funciones.php");

    session_start();
    if (!isset($_SESSION["rol"]) ||  $_SESSION["rol"] != 1 || !isset($_SESSION["usuarioId"])) {
        header("Location: ../index.php");
        exit();
    }

    $conexion = conectarPDO($host, $user, $password, $bbdd);

    $sql = "SELECT id, nombre FROM categorias";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();

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
            <div class="col-1" style="background-color: aliceblue;"></div>
            <div class="col-10 my-5 text-center" style="background-color: white; border-radius: 10px; padding: 20px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); max-width: 800px; margin: auto;">
                <h3>Gestión de Rallys</h3>
                <?php
                    // Consultar todos los rallys
                    $sqlRallys = "SELECT id, titulo, descripcion, fecha_inicio, fecha_fin, estado FROM rallys ORDER BY fecha_inicio DESC";
                    $stmtRallys = $conexion->prepare($sqlRallys);
                    $stmtRallys->execute();
                    $rallys = $stmtRallys->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <table class="table table-striped mt-3">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Descripción</th>
                            <th>Inicio</th>
                            <th>Fin</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rallys as $rally): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($rally['titulo']); ?></td>
                                <td><?php echo htmlspecialchars($rally['descripcion']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($rally['fecha_inicio'])); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($rally['fecha_fin'])); ?></td>
                                <td>
                                    <?php
                                        switch ($rally['estado']) {
                                            case 0:
                                                echo 'Inactivo';
                                                break;
                                            case 1:
                                                echo 'Activo';
                                                break;
                                            case 2:
                                                echo 'Finalizado';
                                                break;
                                        }
                                    ?>
                                </td>

                                <td>
                               
                                        <?php
                                            if ($rally['estado'] == 0): ?>                                              
                                                <a href="cambiarEstadoRally.php?rally_id=<?php echo $rally['id']; ?>&nuevo_estado=1" class="btn btn-success btn-sm" style="min-width: 100px;">Activar</a>
                                                <a href="borrarRally.php?rally_id=<?php echo $rally['id']; ?>" class="btn btn-outline-danger btn-sm" style="min-width: 100px;" onclick="return confirm('¿Estás seguro de que deseas borrar este rally? Esta acción no se puede deshacer.');">Borrar</a>
                                            <?php elseif ($rally['estado'] == 1): ?>              
                                                <a href="cambiarEstadoRally.php?rally_id=<?php echo $rally['id']; ?>&nuevo_estado=0" class="btn btn-danger btn-sm" style="min-width: 100px;">Desactivar</a>
                                                <a href="cambiarEstadoRally.php?rally_id=<?php echo $rally['id']; ?>&nuevo_estado=2" class="btn btn-warning btn-sm" style="min-width: 100px;">Finalizar</a>
                                            <?php else: ?>
                                                <span class="text-muted">Finalizado</span>
                                            <?php endif; 
                                        ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="col-1" style="background-color: aliceblue;"></div>
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