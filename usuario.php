<?php
require_once("utiles/variables.php");
require_once("utiles/funciones.php");

session_start();
if (!isset($_SESSION["rol"]) || !isset($_SESSION["usuarioId"])) {
    header("Location: index.php");
    exit();
}

$conexion = conectarPDO($host, $user, $password, $bbdd);

$sqlUsuario = "SELECT nombre, avatar FROM usuarios WHERE id = $_SESSION[usuarioId]";

$resultado = resultadoConsulta($conexion, $sqlUsuario);

$registroUsu = $resultado->fetch(PDO::FETCH_ASSOC);

cerrarPDO();

$conexion = conectarPDO($host, $user, $password, $bbdd);
$usuario_id = $_SESSION["usuarioId"];

// Fotos de rally ACTIVO (estado = 1)
$sqlActivas = "SELECT f.id AS foto_id, f.titulo AS foto_titulo, f.descripcion AS descripcion, f.url AS url, f.estado AS fotoEstado,
        COUNT(DISTINCT v.id) AS total_votos
        FROM fotos f
        LEFT JOIN votos v ON f.id = v.foto_id
        JOIN rallys r ON f.rally_id = r.id
        WHERE f.usuario_id = :usuario_id AND r.estado = 1
        GROUP BY f.id, f.titulo, f.descripcion, f.url, f.estado";

$stmtActivas = $conexion->prepare($sqlActivas);
$stmtActivas->execute([':usuario_id' => $usuario_id]);
$fotosActivas = $stmtActivas->fetchAll(PDO::FETCH_ASSOC);

// Fotos de rally FINALIZADO (estado = 0)
$sqlHistorial = "SELECT f.id AS foto_id, f.titulo AS foto_titulo, f.descripcion AS descripcion, f.url AS url, f.estado AS fotoEstado,
        COUNT(DISTINCT v.id) AS total_votos
        FROM fotos f
        LEFT JOIN votos v ON f.id = v.foto_id
        JOIN rallys r ON f.rally_id = r.id
        WHERE f.usuario_id = :usuario_id AND r.estado != 1
        GROUP BY f.id, f.titulo, f.descripcion, f.url, f.estado";

$stmtHistorial = $conexion->prepare($sqlHistorial);
$stmtHistorial->execute([':usuario_id' => $usuario_id]);
$fotosHistorial = $stmtHistorial->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rally Fotográfico</title>
    <link rel="icon" href="/imagenes/favicon.png" type="image/png">
    <link rel="stylesheet" href="./estilos/estilos.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="d-flex flex-column min-vh-100">
    <header class="bg-primary text-white py-3">
        <nav class="navbar navbar-expand-lg navbar-dark bg-transparent">
            <div class="container-fluid">
                <a class="navbar-brand d-flex align-items-center" href="index.php">
                    <img class="logo me-2" src="./imagenes/logo.webp" alt="Logo de la pagina, imagen de una camara">
                    <span class="titulo-link">Rally Fotográfico</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse navbar-collapse-center justify-content-between" id="navbarNav">
                    <ul class="navbar-nav mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link text-white" href="index.php">Inicio</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="galeriaActiva.php">Galería Activa</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="todasGalerias.php">Todas Las Galerías</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="fotosGanadoras.php">Fotos Ganadoras</a></li>
                    </ul>

                    <div class="d-flex align-items-center gap-2">
                        <ul class="navbar-nav mb-2 mb-lg-0">
                            <?php if ($_SESSION["rol"] == 1): ?>
                                <li class="nav-item"><a class="nav-link text-white" href="admin.php">Panel de Control</a></li>
                                <li class="nav-item"><a class="nav-link text-white" href="usuario.php">Ver mis Fotos</a></li>
                                <li class="nav-item"><a class="nav-link text-white" href="./login/cerrarSesion.php">Cerrar Sesión</a></li>
                                <li class="nav-item">
                                    <a class="nav-link text-white d-flex align-items-center" href="perfil.php">
                                        <i class="bi bi-person-fill me-1"></i> Hola, <?= htmlspecialchars($registroUsu["nombre"]) ?>
                                    </a>
                                </li>
                            <?php elseif ($_SESSION["rol"] == 2): ?>
                                <li class="nav-item"><a class="nav-link text-white" href="usuario.php">Ver mis Fotos</a></li>
                                <li class="nav-item"><a class="nav-link text-white" href="./login/cerrarSesion.php">Cerrar Sesión</a></li>
                                <li class="nav-item">
                                    <a class="nav-link text-white d-flex align-items-center" href="perfil.php">
                                        <i class="bi bi-person-fill me-1"></i> Hola, <?= htmlspecialchars($registroUsu["nombre"]) ?>
                                    </a>
                                </li>
                            <?php else: ?>
                                <li class="nav-item"><a class="nav-link text-white" href="login/login.php">Iniciar Sesión</a></li>
                                <li class="nav-item"><a class="nav-link text-white" href="login/registro.php">Registrarse</a></li>
                            <?php endif; ?>
                        </ul>

                        <?php if ($_SESSION["rol"] == 1 || $_SESSION["rol"] == 2): ?>
                            <img src="<?= htmlspecialchars($registroUsu['avatar']) ?>" alt="Avatar"
                                class="rounded-circle d-none d-md-block"
                                width="50" height="50" style="object-fit: cover;">
                        <?php endif; ?>

                    </div>

                    <?php if ($_SESSION["rol"] != 0) { ?>
                        <div class="w-100 text-center mt-3 d-lg-none">
                            <img src="<?= htmlspecialchars($registroUsu['avatar']) ?>" alt="Avatar" width="70" height="70" class="rounded-circle" style="object-fit: cover;">
                        </div>
                    <?php } ?>

                </div>
            </div>
        </nav>
    </header>


    <main class="container-fluid flex-grow-1">
        <div class="row h-100">
            <div class="col-1"></div>

            <div class="col-10 my-5">
                <div class="row text-white">
                    <div class="text-center">
                        <h2 class="h2">Fotos de la galería activa</h2>
                    </div>

                    <?php if (count($fotosActivas) === 0): ?>
                        <div class="col-12 text-center my-4">
                            <p class="text-muted">No hay fotos en la galería activa.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($fotosActivas as $foto): ?>
                            <?php
                            $estado = $foto['fotoEstado'];
                            $borderClass = match ($estado) {
                                0 => 'border border-5 border-secondary',
                                1 => 'border border-5 border-success',
                                2 => 'border border-5 border-danger',
                                default => '',
                            };
                            $estadoTexto = match ($estado) {
                                0 => ['Pendiente de Revisión', 'text-secondary'],
                                1 => ['Activa', 'text-success'],
                                2 => ['Cancelada', 'text-danger'],
                                default => ['Desconocido', 'text-muted'],
                            };
                            ?>

                            <div class="col-md-4 mb-4">
                                <div class="card h-100 d-flex flex-column align-items-center text-center <?= $borderClass ?>">
                                    <div class="card-header">
                                        <h3 class="h5"><?= htmlspecialchars($foto["foto_titulo"]) ?></h3>
                                    </div>
                                    <div class="card-img-container" style="height: 200px; overflow: hidden; display: flex; justify-content: center; align-items: center; background-color:rgb(215, 227, 239);">
                                        <img loading="lazy" src="<?= htmlspecialchars($foto['url']) ?>" style="max-height: 100%; max-width: 100%; object-fit: contain;" alt="<?= htmlspecialchars($foto['foto_titulo']) ?>">
                                    </div>
                                    <div class="card-body d-flex flex-column justify-content-between align-items-center">
                                        <p class="card-text"><?= htmlspecialchars($foto['descripcion']) ?></p>
                                        <p class="card-text">Votos: <?= $foto['total_votos'] ?></p>

                                        <?php if ($estado == 0): ?>
                                            <div class="d-flex gap-2 mt-2">
                                                <a href="modificarFoto.php?id=<?= $foto['foto_id'] ?>" class="btn btn-sm btn-outline-primary">Modificar</a>
                                                <a href="eliminarFoto.php?id=<?= $foto['foto_id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Seguro que deseas eliminar esta foto?')">Eliminar</a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-footer w-100 <?= $estadoTexto[1] ?>">
                                        <strong><?= $estadoTexto[0] ?></strong>
                                    </div>
                                </div>
                            </div>

                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <hr class="my-5 text-white">

                <div class="row mt-5 text-white">
                    <div class="text-center">
                        <h2 class="h2">Historial de fotos</h2>
                    </div>

                    <?php if (count($fotosHistorial) === 0): ?>
                        <div class="col-12 text-center my-4">
                            <p class="text-muted">No hay fotos en el historial.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($fotosHistorial as $foto): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card h-100 d-flex flex-column align-items-center text-center">
                                    <div class="card-header">
                                        <h3 class="h5"><?= htmlspecialchars($foto["foto_titulo"]) ?></h3>
                                    </div>
                                    <div class="card-img-container" style="height: 200px; overflow: hidden; display: flex; justify-content: center; align-items: center; background-color:rgb(215, 227, 239);">
                                        <img loading="lazy" src="<?= htmlspecialchars($foto['url']) ?>" style="max-height: 100%; max-width: 100%; object-fit: contain;" alt="<?= htmlspecialchars($foto['foto_titulo']) ?>">
                                    </div>
                                    <div class="card-body d-flex flex-column justify-content-between align-items-center">
                                        <p class="card-text"><?= htmlspecialchars($foto['descripcion']) ?></p>
                                        <p class="card-text">Votos: <?= $foto['total_votos'] ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
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
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>