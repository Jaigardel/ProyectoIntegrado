<?php
session_start();

if (!isset($_SESSION["rol"])) {
    header("Location: index.php");
    exit();
}

if (!isset($_SESSION["usuarioId"])) {
    $_SESSION["usuarioId"] = 0;
}

require_once("utiles/variables.php");
require_once("utiles/funciones.php");

try {

    $conexion = conectarPDO($host, $user, $password, $bbdd);

    $sqlUsuario = "SELECT nombre, apellidos, email, contrasena, avatar FROM usuarios WHERE id = $_SESSION[usuarioId]";

    $resultado = resultadoConsulta($conexion, $sqlUsuario);

    $registroUsu = $resultado->fetch(PDO::FETCH_ASSOC);

    cerrarPDO();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
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


    <main class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6 bg-white p-4 rounded shadow text-center" style="max-width: 400px;">
                <h4 class="mb-4">Editar Perfil <img src="<?= htmlspecialchars($registroUsu['avatar']) ?>" alt="Avatar actual" width="100" class="mt-2 rounded"></h4>
                <form id="formPerfil">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre:</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($registroUsu['nombre']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="apellidos" class="form-label">Apellidos:</label>
                        <input type="text" class="form-control" id="apellidos" name="apellidos" value="<?= htmlspecialchars($registroUsu['apellidos']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="avatar" class="form-label">Cambiar Avatar (opcional):</label>
                        <input type="file" class="form-control" id="avatar" name="avatar" accept="image/jpeg, image/png">
                        <img id="avatarPreview" alt="" src="" width="100" class="mt-2 rounded">
                    </div>

                    <div class="mb-3">
                        <label for="contrasena" class="form-label">Nueva Contraseña (opcional):</label>
                        <input type="password" class="form-control" id="contrasena" name="contrasena">
                    </div>

                    <div class="mb-3">
                        <label for="confirmarContrasena" class="form-label">Confirmar Nueva Contraseña:</label>
                        <input type="password" class="form-control" id="confirmarContrasena" name="confirmarContrasena">
                    </div>

                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    <p id="mensajePerfil" class="mt-2"></p>
                </form>
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

    <button class="boton-sticky" onclick="scrollToTop()">↑</button>
    <script>
        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }
    </script>
    <script>
        document.getElementById("avatar").addEventListener("change", function() {
            const archivo = this.files[0];
            const preview = document.getElementById("avatarPreview");

            if (archivo) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.alt = "Vista previa del avatar";
                    preview.style.display = "block";
                };
                reader.readAsDataURL(archivo);
            } else {
                preview.style.display = "none";
            }
        });
    </script>
    <script type="module">
        import {
            subirImagen
        } from "./scripts/supabase.js";

        const form = document.getElementById("formPerfil");
        const mensaje = document.getElementById("mensajePerfil");

        form.addEventListener("submit", async (e) => {
            e.preventDefault();
            mensaje.textContent = "";

            const nombre = document.getElementById("nombre").value.trim();
            const apellidos = document.getElementById("apellidos").value.trim();
            const contrasena = document.getElementById("contrasena").value;
            const confirmar = document.getElementById("confirmarContrasena").value;
            const avatarFile = document.getElementById("avatar").files[0];

            if (contrasena && contrasena !== confirmar) {
                mensaje.textContent = "❌ Las contraseñas no coinciden.";
                mensaje.style.color = "red";
                return;
            }
            if (contrasena && contrasena.length < 6) {
                mensaje.textContent = "❌ La contraseña debe tener al menos 6 caracteres.";
                mensaje.style.color = "red";
                return;
            }

            let avatarURL = null;

            if (avatarFile) {
                const resultado = await subirImagen(avatarFile, `avatars/avatar_<?php echo $_SESSION["usuarioId"]; ?>`);
                if (resultado.data) {
                    avatarURL = `https://xlwpajxruqllvilzaeaa.supabase.co/storage/v1/object/public/fotos/${resultado.data.path}`;
                } else {
                    mensaje.textContent = "❌ Error al subir el avatar.";
                    mensaje.style.color = "red";
                    return;
                }
            }

            const datos = {
                usuario_id: <?php echo $_SESSION["usuarioId"]; ?>,
                nombre,
                apellidos,
                contrasena: contrasena || null,
                avatar: avatarURL
            };

            const res = await fetch("guardar_perfil.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(datos)
            });

            const resultado = await res.json();
            console.log(resultado);
            if (resultado.success) {
                mensaje.textContent = "✅ Perfil actualizado correctamente.";
                mensaje.style.color = "green";
            } else {
                mensaje.textContent = "❌ Error: " + resultado.error;
                mensaje.style.color = "red";
            }
        });
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>