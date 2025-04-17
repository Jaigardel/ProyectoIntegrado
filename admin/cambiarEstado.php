<?php 
require_once("../utiles/variables.php");
require_once("../utiles/funciones.php");

session_start();
if (!isset($_SESSION["rol"]) ||  $_SESSION["rol"] != 1 || !isset($_SESSION["usuarioId"])) {
    header("Location: ../index.php");
    exit();
}

// Verificar que se han enviado los parámetros necesarios
if (isset($_GET['id']) && isset($_GET['estado'])) {
    $foto_id = $_GET['id'];
    $nuevo_estado = $_GET['estado'];

    // Validar que el estado sea 1 o 2 (Activa o Cancelada)
    if ($nuevo_estado == 1 || $nuevo_estado == 2 || $nuevo_estado == 0) {
        // Conectar a la base de datos
        $conexion = conectarPDO($host, $user, $password, $bbdd);

        // Preparar la consulta SQL para actualizar el estado de la foto
        $sql = "UPDATE fotos SET estado = :estado WHERE id = :foto_id";
        $stmt = $conexion->prepare($sql);

        // Ejecutar la consulta con los parámetros
        $stmt->bindParam(':estado', $nuevo_estado, PDO::PARAM_INT);
        $stmt->bindParam(':foto_id', $foto_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Redirigir de nuevo a la página de fotos (con un mensaje de éxito)
            header("Location: fotosAdmin.php?mensaje=Estado actualizado correctamente");
        } else {
            // Si hay un error en la ejecución, redirigir con un mensaje de error
            header("Location: fotosAdmin.php?error=Hubo un problema al actualizar el estado");
        }
    } else {
        // Si el estado no es válido, redirigir con un mensaje de error
        header("Location: fotosAdmin.php?error=Estado no válido");
    }
} else {
    // Si no se han enviado los parámetros necesarios, redirigir con un error
    header("Location: fotosAdmin.php?error=Datos no válidos");
}
exit();
?>
