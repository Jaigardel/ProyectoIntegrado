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
        <nav class="navbar navbar-expand-lg navbar-dark bg-transparent">
            <div class="container-fluid">
                <a class="navbar-brand d-flex align-items-center" href="index.php">
                    <img class="logo me-2" src="../imagenes/logo.webp" alt="Logo de la pagina, imagen de una camara">
                    <span class="titulo-link">Rally Fotográfico</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse navbar-collapse-center justify-content-between" id="navbarNav">
                    <ul class="navbar-nav mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link text-white" href="../index.php">Inicio</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="../galeriaActiva.php">Galería Activa</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="../todasGalerias.php">Todas Las Galerías</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="../fotosGanadoras.php">Fotos Ganadoras</a></li>
                    </ul>

                    <div class="d-flex align-items-center gap-2">
                        <ul class="navbar-nav mb-2 mb-lg-0">
                            <?php if ($_SESSION["rol"] == 1): ?>
                                <li class="nav-item"><a class="nav-link text-white" href="../admin.php">Panel de Control</a></li>
                                <li class="nav-item"><a class="nav-link text-white" href="../usuario.php">Ver mis Fotos</a></li>
                                <li class="nav-item"><a class="nav-link text-white" href="../login/cerrarSesion.php">Cerrar Sesión</a></li>
                                <li class="nav-item">
                                    <a class="nav-link text-white d-flex align-items-center" href="perfil.php">
                                        <i class="bi bi-person-fill me-1"></i> Hola, <?= htmlspecialchars($registroUsu["nombre"]) ?>
                                    </a>
                                </li>
                            <?php elseif ($_SESSION["rol"] == 2): ?>
                                <li class="nav-item"><a class="nav-link text-white" href="../usuario.php">Ver mis Fotos</a></li>
                                <li class="nav-item"><a class="nav-link text-white" href="../login/cerrarSesion.php">Cerrar Sesión</a></li>
                                <li class="nav-item">
                                    <a class="nav-link text-white d-flex align-items-center" href="perfil.php">
                                        <i class="bi bi-person-fill me-1"></i> Hola, <?= htmlspecialchars($registroUsu["nombre"]) ?>
                                    </a>
                                </li>
                            <?php else: ?>
                                <li class="nav-item"><a class="nav-link text-white" href="../login/login.php">Iniciar Sesión</a></li>
                                <li class="nav-item"><a class="nav-link text-white" href="../login/registro.php">Registrarse</a></li>
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
            <div class="col-10 my-5 text-center" style="background-color: white; border-radius: 10px; padding: 20px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); max-width: 800px; margin: auto;">

                <h3>Crear nuevo Rally</h3>

                <form id="formularioFoto" name="formularioFoto">
                    <div class="mb-3">
                    <label for="inputArchivo" class="form-label">Selecciona una imagen (jpg, png):</label>
                    <input type="file" class="form-control" id="inputArchivo" accept="image/jpeg, image/png" required>
                    </div>

                    <div class="mb-3">
                    <label for="categoria" class="form-label">Selecciona una categoría:</label>
                    <select class="form-select" id="categoria" required>
                        <option value="" disabled selected>Selecciona una categoría</option>
                        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo $row['nombre']; ?></option>
                        <?php } ?>
                    </select>
                    </div>
                    <div class="mn-3">
                        <label for="fecha_inicio" class="form-label">Fecha de inicio:</label>
                        <input type="datetime-local" class="form-control" id="fecha_inicio" required>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_fin" class="form-label">Fecha de fin:</label>
                        <input type="datetime-local" class="form-control" id="fecha_fin" required>
                    </div>

                    <div class="mb-3">
                    <label for="tituloFoto" class="form-label">Título:</label>
                    <input type="text" class="form-control" id="tituloFoto" required>
                    </div>

                    <div class="mb-3">
                    <label for="descripcionFoto" class="form-label">Descripción:</label>
                    <textarea class="form-control" id="descripcionFoto" rows="3" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" id="uploadBtn">Subir Rally</button>
                </form>

                <p id="mensaje" class="mt-3"></p>
                <img id="imagenPrevia" src="" width="300" class="mt-3" style="display: none; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); position: relative; top: 10px; left: 50%; transform: translateX(-50%);">
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
    <script type="module">
        import { subirImagen } from "../scripts/supabase.js";

        document.getElementById("inputArchivo").addEventListener("change", () => {
        const archivo = document.getElementById("inputArchivo").files[0];
        if (archivo) {

            const tamanioMaximo = 50 * 1024 * 1024; // 50 MB en bytes

            if (archivo.size > tamanioMaximo) {
                document.getElementById("mensaje").textContent = "❌ El archivo es demasiado grande. El tamaño máximo permitido es de 50 MB.";
                document.getElementById("imagenPrevia").style.display = "none"; 
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
            const img = document.getElementById("imagenPrevia");
            img.src = e.target.result;
            img.style.display = "block";
            };
            reader.readAsDataURL(archivo);
        }
        });

        document.getElementById("formularioFoto").addEventListener("submit", async (e) => {
        e.preventDefault();

        const archivo = document.getElementById("inputArchivo").files[0];
        const titulo = document.getElementById("tituloFoto").value.trim();
        const descripcion = document.getElementById("descripcionFoto").value.trim();
        const categoria = parseInt(document.getElementById("categoria").value);
        const fechaInicio = (document.getElementById("fecha_inicio").value).replace('T', ' ');
        const fechaFin = (document.getElementById("fecha_fin").value).replace('T', ' ');

        if (!archivo || !titulo || !descripcion) {
            document.getElementById("mensaje").textContent = "❌ Todos los campos son obligatorios.";
            document.getElementById("mensaje").style.color = "red";
            return;
        }

        const resultado = await subirImagen(archivo);

        if (resultado.data) {
            const url = `https://xlwpajxruqllvilzaeaa.supabase.co/storage/v1/object/public/fotos/${resultado.data.path}`;

            const datos = {
            url,
            usuario_id: <?php echo $_SESSION["usuarioId"]?>, 
            categoria_id: categoria,
            fecha_inicio: fechaInicio,
            fecha_fin: fechaFin,
            titulo,
            descripcion
            };

            const respuesta = await fetch("crearRally.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(datos)
            });

            document.getElementById("mensaje").textContent = "✅ Imagen subida correctamente.";
            document.getElementById("mensaje").style.color =  "green";
            setTimeout(() => {
                    document.getElementById("imagenPrevia").src = "";
                    document.getElementById("imagenPrevia").style.display = "none";
                    document.getElementById("formularioFoto").reset();
                    document.getElementById("mensaje").textContent = "";
                }, 3000);
        } else {
            document.getElementById("mensaje").textContent = "❌ Error al subir la imagen.";
            document.getElementById("mensaje").style.color = "red";
        }
        
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>