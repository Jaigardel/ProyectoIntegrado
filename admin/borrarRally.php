<?php
require_once("../utiles/variables.php");
require_once("../utiles/funciones.php");

session_start();
if (!isset($_SESSION["rol"]) || $_SESSION["rol"] != 1) {
    header("Location: ../index.php");
    exit();
}

if (!isset($_GET["rally_id"])) {
    header("Location: gestionRallys.php");
    exit();
}

$rallyId = $_GET["rally_id"];
$conexion = conectarPDO($host, $user, $password, $bbdd);

$sqlCheck = "SELECT estado FROM rallys WHERE id = :id";
$stmtCheck = $conexion->prepare($sqlCheck);
$stmtCheck->bindParam(':id', $rallyId);
$stmtCheck->execute();
$rally = $stmtCheck->fetch(PDO::FETCH_ASSOC);

if ($rally && $rally['estado'] == 0) {
    $sqlFotos = "SELECT COUNT(*) FROM fotos WHERE rally_id = :id";
    $stmtFotos = $conexion->prepare($sqlFotos);
    $stmtFotos->bindParam(':id', $rallyId);
    $stmtFotos->execute();
    $numFotos = $stmtFotos->fetchColumn();

    if ($numFotos == 0) {
        $sqlDelete = "DELETE FROM rallys WHERE id = :id";
        $stmtDelete = $conexion->prepare($sqlDelete);
        $stmtDelete->bindParam(':id', $rallyId);
        $stmtDelete->execute();

        echo "<div class='alert alert-success text-center'>✅ Rally borrado correctamente. Redirigiendo...</div>";
    } else {
        echo "<div class='alert alert-warning text-center'>⚠️ No se puede borrar el rally porque tiene fotos asociadas.</div>";
    }
} else {
    echo "<div class='alert alert-danger text-center'>⚠️ Solo se pueden borrar rallys inactivos.</div>";
}

header("Refresh: 3; url=gestionRallys.php");
exit();
?>
