<?php 
require_once("../utiles/variables.php");
require_once("../utiles/funciones.php");

session_start();
if (!isset($_SESSION["rol"]) ||  $_SESSION["rol"] != 1 || !isset($_SESSION["usuarioId"])) {
    header("Location: ../index.php");
    exit();
}

if (isset($_GET['id']) && isset($_GET['estado'])) {
    $foto_id = $_GET['id'];
    $nuevo_estado = $_GET['estado'];

    if ($nuevo_estado == 1 || $nuevo_estado == 2 || $nuevo_estado == 0) {
        $conexion = conectarPDO($host, $user, $password, $bbdd);

        $sql = "UPDATE fotos SET estado = :estado WHERE id = :foto_id";
        $stmt = $conexion->prepare($sql);

        $stmt->bindParam(':estado', $nuevo_estado);
        $stmt->bindParam(':foto_id', $foto_id);

        if ($stmt->execute()) {
            header("Location: fotosAdmin.php?mensaje=Estado actualizado correctamente");
        } else {
            header("Location: fotosAdmin.php?error=Hubo un problema al actualizar el estado");
        }
    } else {
        header("Location: fotosAdmin.php?error=Estado no válido");
    }
} else {
    header("Location: fotosAdmin.php?error=Datos no válidos");
}
exit();
?>
