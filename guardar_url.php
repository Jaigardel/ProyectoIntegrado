<?php
require_once("utiles/variables.php");
require_once("utiles/funciones.php");

session_start();
    if (!isset($_SESSION["rol"]) || $_SESSION["rol"] == 0) {
        echo "❌ No tienes permiso para acceder a esta página.";
        header("Location: index.php");
        exit;
    }

$conexion = conectarPDO($host, $user, $password, $bbdd);

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data["url"]) && isset($data["usuario_id"]) && isset($data["rally_id"]) && isset($data["titulo"]) && isset($data["descripcion"])) {
    $url = $data["url"];
    $usuarioId = $data["usuario_id"];
    $rallyId = $data["rally_id"];
    $titulo = $data["titulo"];
    $descripcion = $data["descripcion"];

    $sql = "INSERT INTO fotos (usuario_id, rally_id, titulo, descripcion, url, estado) 
            VALUES (:usuario_id, :rally_id, :titulo, :descripcion, :url, 0)";
    
    $stmt = $conexion->prepare($sql);

    if ($stmt->execute([
        ":usuario_id" => $usuarioId,
        ":rally_id" => $rallyId,
        ":titulo" => $titulo,
        ":descripcion" => $descripcion,
        ":url" => $url
    ])) {
        echo "✅ URL guardada en la base de datos.";
    } else {
        echo "❌ Error al guardar en la base de datos.";
    }
} else {
    echo "❌ Faltan datos para guardar la imagen.";
}

cerrarPDO();
?>
