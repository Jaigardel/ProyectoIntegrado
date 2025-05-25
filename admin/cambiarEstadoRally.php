<?php
session_start();
require_once("../utiles/variables.php");
require_once("../utiles/funciones.php");

if (!isset($_SESSION["rol"]) || $_SESSION["rol"] != 1) {
    header("Location: ../index.php");
    exit();
}

if (!isset($_GET["rally_id"]) || !isset($_GET["nuevo_estado"])) {
    header("Location: gestionRallys.php");
    exit();
}

$rallyId = $_GET["rally_id"];
$nuevoEstado = $_GET["nuevo_estado"];

$conexion = conectarPDO($host, $user, $password, $bbdd);

// Solo permitir un rally activo a la vez
if ($nuevoEstado == 1) {
    $sqlActivos = "SELECT COUNT(*) FROM rallys WHERE estado = 1";
    $stmt = $conexion->query($sqlActivos);
    $hayActivo = $stmt->fetchColumn();

    if ($hayActivo > 0) {
        $_SESSION["mensaje"] = [
            "tipo" => "warning",
            "texto" => "⚠️ Ya hay un rally activo. Debes desactivarlo antes de activar otro."
        ];
        header("Location: gestionRallys.php");
        exit();
    }
}

// Actualiza estado si pasa la validación
$sql = "UPDATE rallys SET estado = :estado WHERE id = :id";
$stmt = $conexion->prepare($sql);
$stmt->bindParam(':estado', $nuevoEstado);
$stmt->bindParam(':id', $rallyId);
$stmt->execute();

$_SESSION["mensaje"] = [
    "tipo" => "success",
    "texto" => "✅ Estado del rally actualizado correctamente."
];

header("Location: gestionRallys.php");
exit();

?>
