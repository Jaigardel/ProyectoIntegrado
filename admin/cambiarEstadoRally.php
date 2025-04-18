<?php
require_once("../utiles/variables.php");
require_once("../utiles/funciones.php");

session_start();
if (!isset($_SESSION["rol"]) || $_SESSION["rol"] != 1) {
    header("Location: ../index.php");
    exit();
}

if (isset($_GET['rally_id'], $_GET['nuevo_estado'])) {
    $conexion = conectarPDO($host, $user, $password, $bbdd);

    $rally_id = intval($_GET['rally_id']);
    $nuevo_estado = intval($_GET['nuevo_estado']);

    $sql = "UPDATE rallys SET estado = :nuevo_estado WHERE id = :rally_id";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(":nuevo_estado", $nuevo_estado);
    $stmt->bindParam(":rally_id", $rally_id);
    $stmt->execute();
}


header("Location: gestionRallys.php");
exit();
?>
