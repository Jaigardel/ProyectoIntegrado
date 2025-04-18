<?php
require_once("../utiles/variables.php");
require_once("../utiles/funciones.php");

session_start();
if (!isset($_SESSION["rol"]) || $_SESSION["rol"] != 1) {
    header("Location: ../index.php");
    exit();
}

if (isset($_GET["usuario_id"], $_GET["nuevo_estado"])) {
    $conexion = conectarPDO($host, $user, $password, $bbdd);
    $sql = "UPDATE usuarios SET estado = :nuevo_estado WHERE id = :usuario_id AND rol_id = 2";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':nuevo_estado', $_GET["nuevo_estado"]);
    $stmt->bindParam(':usuario_id', $_GET["usuario_id"]);
    $stmt->execute();
}
header("Location: gestionUsuarios.php");
exit();
?>
