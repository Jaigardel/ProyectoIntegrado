<?php

    require_once("utiles/variables.php");
    require_once("utiles/funciones.php");

    session_start();
    if (!isset($_SESSION["usuarioId"]) || $_SESSION["rol"] == 0) {
        header("Location: index.php");
        exit();
    }
    if (!isset($_GET["id"])) {
        header("Location: index.php");
        exit();
    }

    $conexion = conectarPDO($host, $user, $password, $bbdd);
    $fotoId = $_GET["id"];
    $usuarioId = $_SESSION["usuarioId"];
    $sql = "SELECT id, titulo, descripcion
            FROM fotos 
            WHERE id = :fotoId AND usuario_id = :usuarioId AND estado = 0";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':fotoId', $fotoId);
    $stmt->bindParam(':usuarioId', $usuarioId);
    $stmt->execute();
    if ($stmt->rowCount() == 0) {
        echo "<div class='alert alert-danger text-center'>⚠️ No se ha encontrado la foto o no tienes permiso para eliminarla.</div>";
        if($_SESSION["rol"] == 1){
            header("Refresh: 2; url=./admin/fotosAdmin.php");
        }else{
            header("Refresh: 2; url=usuario.php");
        }
        exit();
    }else{
        $sql = "DELETE FROM fotos WHERE id = :fotoId AND usuario_id = :usuarioId";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':fotoId', $fotoId);
        $stmt->bindParam(':usuarioId', $usuarioId);
        $stmt->execute();
        echo "<div class='alert alert-success text-center'>✅ Foto eliminada correctamente.</div>";
        if($_SESSION["rol"] == 1){
            header("Refresh: 2; url=./admin/fotosAdmin.php");
        }else{
            header("Refresh: 2; url=usuario.php");
        }
        exit();
    }
?>

