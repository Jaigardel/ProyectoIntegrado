<?php
require_once("../utiles/variables.php");
require_once("../utiles/funciones.php");

session_start();
if (!isset($_SESSION["rol"]) || $_SESSION["rol"] != 1) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $apellidos = $_POST["apellidos"];
    $email = $_POST["email"];
    $clave = password_hash($_POST["password"], PASSWORD_BCRYPT);

    $conexion = conectarPDO($host, $user, $password, $bbdd);

    $sqlCheck = "SELECT COUNT(*) FROM usuarios WHERE email = :email";
    $stmtCheck = $conexion->prepare($sqlCheck);
    $stmtCheck->bindParam(':email', $email);
    $stmtCheck->execute();
    if($stmtCheck->fetchColumn() > 0) {
        echo "<div class='alert alert-danger text-center'>⚠️ El email ya está registrado.</div>";
        header("Refresh: 2; url=crearAdministrador.php");
        exit();
    }
    $sql = "INSERT INTO usuarios (nombre, apellidos, email, contrasena, rol_id, estado)
            VALUES (:nombre, :apellidos, :email, :contrasena, 1, 1)";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':apellidos', $apellidos);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':contrasena', $clave);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        echo "<div class='alert alert-success text-center'>✅ Administrador creado correctamente.</div>";
    } else {
        echo "<div class='alert alert-danger text-center'>❌ Error al crear el administrador.</div>";
    }
    cerrarPDO();
    header("Refresh: 2; url=gestionUsuarios.php");
    exit();
}


?>
