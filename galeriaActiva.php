<?php 
    require_once("utiles/variables.php");
    require_once("utiles/funciones.php");

    session_start();
    if (!isset($_SESSION["rol"])) {
        $_SESSION["rol"] = 0;
    }

    $conexion = conectarPDO($host, $user, $password, $bbdd);
    
    // 1. Obtener el rally activo
$sqlRally = "SELECT * FROM rallys WHERE estado = 1 ORDER BY id DESC LIMIT 1";
$resultadoRally = resultadoConsulta($conexion, $sqlRally);
$rally = $resultadoRally->fetch(PDO::FETCH_ASSOC);

$hayGaleriaActiva = $rally ? true : false;

$fotos = [];
if ($hayGaleriaActiva) {
    $fechaFin = $rally["fecha_fin"];

    // 2. Obtener fotos activas del rally activo
    $sqlFotos = "SELECT * FROM fotos WHERE rally_id = :rally_id AND estado = 1 ORDER BY id DESC";
    $stmtFotos = $conexion->prepare($sqlFotos);
    $stmtFotos->execute([':rally_id' => $rally["id"]]);
    $fotos = $stmtFotos->fetchAll(PDO::FETCH_ASSOC);
}

 

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["foto_id"])) {
        $fotoId = $_POST["foto_id"];
        $ipUsuario = obtenerIPUsuario();
    
        if (isset($_POST["quitar_voto"])) {
            $sqlDelete = "DELETE FROM votos WHERE foto_id = :foto_id AND ip = :ip";
            $stmtDelete = $conexion->prepare($sqlDelete);
            $stmtDelete->execute([
                ':foto_id' => $fotoId,
                ':ip' => $ipUsuario
            ]);
            echo "<div class='alert alert-info text-center'>🗑️ Tu voto fue eliminado.</div>";
        } else {
            $sql = "SELECT COUNT(*) FROM votos WHERE foto_id = :foto_id AND ip = :ip";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([
                ':foto_id' => $fotoId,
                ':ip' => $ipUsuario
            ]);
    
            if ($stmt->fetchColumn() == 0) {
                $sqlInsert = "INSERT INTO votos (foto_id, ip) VALUES (:foto_id, :ip)";
                $stmtInsert = $conexion->prepare($sqlInsert);
                $stmtInsert->execute([
                    ':foto_id' => $fotoId,
                    ':ip' => $ipUsuario
                ]);
                echo "<div class='alert alert-success text-center'>✅ ¡Gracias por tu voto!</div>";
            } else {
                echo "<div class='alert alert-warning text-center'>⚠️ Ya votaste por esta foto.</div>";
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
    <link rel="stylesheet" href="./estilos/estilos.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        
        <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
            <ul class="navbar-nav text-center">
                <li class="nav-item">
                    <a class="nav-link text-white" href="index.php">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="galeriaActiva.php">Galería Activa</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="todasGalerias.php">Todas Las Galerías</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="fotosGanadoras.php">Fotos Ganadoras</a>
                </li>
            </ul>

            <ul class="navbar-nav text-center">
                <?php if ($_SESSION["rol"] == 1){ ?>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="admin.php">Panel de Control</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="usuario.php">Ver mis Fotos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="./login/cerrarSesion.php">Cerrar Sesión</a>
                    </li>
                <?php }elseif ($_SESSION["rol"] == 2){ ?>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="usuario.php">Ver mis Fotos</a>
                    </li>
                    <li class="nav-item">
                <?php }else{?>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="login/login.php">Iniciar Sesión</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="login/registro.php">Registrarse</a>
                    </li>
                <?php } ?>

        </div>
    </header>

    <main class="container-fluid flex-grow-1">
        <div class="row h-100">
            <?php if ($hayGaleriaActiva): ?>
            <div class="d-flex justify-content-center align-items-center" style="background: rgba(245, 245, 220, 0.7); max-width: 100%;">
                <h2 class="h2 text-danger">
                    Tiempo Restante: 
                    <span id="contador" class="fs-4 fw-bold text-danger"></span>
                </h2>
            </div>
            <?php endif ?>

            <div class="col-1"></div>

            <div class="col-10 my-5 text-white">
                <?php if ($hayGaleriaActiva): ?>
                    <a href="subirFotos.php?rallyId=<?= $rally["id"] ?>" 
                    id="botonSubir" 
                    class="btn btn-warning"
                    style="position: absolute;  right: 20px; z-index: 1050;">
                        📤 Subir Foto
                    </a>
                <?php endif; ?>
                <?php if ($hayGaleriaActiva): ?>
                    <div class="row d-flex align-items-center">
                        <div class="col-md-4 order-1 order-md-2 text-center my-2">
                            <div class="container my-5">
                                <h3 class="text-center">📊 Resultados de la votación en el momento de cargar la página</h3>
                                <canvas id="graficoVotos" style="max-width: 250px;"></canvas>
                            </div>
                        </div>
                        <div class="col-md-6 order-2 order-md-1 text-white" style="font-weight: bold; margin-left: 5%; max-width: 90%;">
                            <h2><?php echo $rally["titulo"]?></h2>
                            <p><?php echo $rally["descripcion"]?></p>
                        </div>
                    </div>
                    <?php if (!empty($fotos)): ?>
                <hr style="border: 1px solid #ccc;">
                <h3 class="text-center">📸 Galería de Fotos</h3>        
                <div class="row">
                    <?php foreach ($fotos as $foto): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 d-flex flex-column align-items-center text-center">
                                <div class="card-header">
                                    <h3 class="h5"><?= $foto["titulo"] ?></h3>
                                </div>
                                <div class="card-img-container" style="height: 200px; overflow: hidden; display: flex; justify-content: center; align-items: center; background-color:rgb(215, 227, 239);">
                                    <img loading="lazy" src="<?= $foto["url"] ?>" style="max-height: 100%; max-width: 100%; object-fit: contain;" alt="<?= $foto["titulo"] ?>">
                                </div>
                                <div class="card-body d-flex flex-column justify-content-between align-items-center">
                                    <p class="card-text"><?= $foto["descripcion"] ?></p>
                                    <?= renderBotonVoto($foto["id"], $conexion) ?>
                                    <br><button class="btn btn-primary ampliar-btn" data-url="<?= $foto["url"] ?>" data-titulo="<?= $foto["titulo"] ?>" data-descripcion="<?= $foto["descripcion"] ?>">Ampliar</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center">
                    📸 Aún no hay fotos en esta galería. ¡Sé el primero en subir una!
                </div>
            <?php endif; ?>
                <?php else: ?>
                    <div class="alert alert-warning text-center">
                        ⚠️ No hay galería activa en este momento.
                    </div>
                <?php endif; ?>
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

    <div class="popup" id="popup" aria-live="assertive">
        <span class="cerrar" id="cerrar">X</span>
        <h1 class="subsubtitulo text-center h4" id="popupTitulo"></h1>
        <img id="popupImg" src="" alt="" style="max-width: 50%; height: auto; margin: 0 auto; display: block;">
        <p class="subsubtitulo text-center" id="popupDescripcion"></p>
    </div>

    <script>
        document.querySelectorAll('.ampliar-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const titulo = btn.getAttribute('data-titulo');
                const url = btn.getAttribute('data-url');
                const descripcion = btn.getAttribute('data-descripcion') || 'Descripción no disponible.';

                document.getElementById('popupTitulo').textContent = titulo;
                document.getElementById('popupImg').src = url;
                document.getElementById('popupImg').alt = titulo;
                document.getElementById('popupDescripcion').textContent = descripcion;

                document.getElementById('popup').classList.add('mostrar');
            });
        });

        document.getElementById('cerrar').addEventListener('click', () => {
            document.getElementById('popup').classList.remove('mostrar');
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === "Escape") {
                document.getElementById('popup').classList.remove('mostrar');
            }
        });
    </script>

    <script>
        function scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    </script>
    <script>

        const fechaFin = new Date("<?php echo $fechaFin; ?>");

        function actualizarContador() {
            const ahora = new Date();
            const diferencia = fechaFin - ahora;

            if (diferencia <= 0) {
                document.getElementById("contador").textContent = "⏰ ¡El concurso ha terminado!";
                clearInterval(intervalo);
                return;
            }

            const dias = Math.floor(diferencia / (1000 * 60 * 60 * 24));
            const horas = Math.floor((diferencia % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutos = Math.floor((diferencia % (1000 * 60 * 60)) / (1000 * 60));
            const segundos = Math.floor((diferencia % (1000 * 60)) / 1000);

            document.getElementById("contador").textContent =
                ` ${dias}d ${horas}h ${minutos}m ${segundos}s`;
        }

        const intervalo = setInterval(actualizarContador, 1000);
        actualizarContador(); 
    </script>

    <script>
        fetch('votos.php')
        .then(response => response.json())
        .then(data => {

            const votos = data.map(foto => foto.votos);
            const labels = data.map(foto => foto.titulo);

            const datosVotos = votos.every(voto => voto === 0) ? [1] : votos; 
            const labelsVotos = votos.every(voto => voto === 0) ? ["No hay votos"] : labels; 

            const ctx = document.getElementById('graficoVotos').getContext('2d');
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labelsVotos,
                    datasets: [{
                        label: 'Votos',
                        data: datosVotos,
                        backgroundColor: ['#FF5733', '#33FF57', '#3357FF', '#FF33A1', '#FFD700'],
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return tooltipItem.label + ': ' + tooltipItem.raw + ' votos';
                                }
                            }
                        }
                    }
                }
            });
        })
        .catch(error => {
            console.error('Error al obtener los datos:', error);
        });

    </script>
    <script>
    window.addEventListener("scroll", function () {
        const boton = document.getElementById("botonSubir");
        const scrollY = window.scrollY || window.pageYOffset;

        // Activa sticky al llegar a 250px de scroll vertical
        if (scrollY >= 250) {
            boton.classList.add("sticky");
        } else {
            boton.classList.remove("sticky");
        }
    });
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
