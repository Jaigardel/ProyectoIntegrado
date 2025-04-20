<?php
require_once("../utiles/variables.php");
require_once("../utiles/funciones.php");

session_start();
    if (!isset($_SESSION["rol"]) || $_SESSION["rol"] != 1) {
        echo "❌ No tienes permiso para acceder a esta página.";
        header("Location: ../index.php");
        exit;
    }

$conexion = conectarPDO($host, $user, $password, $bbdd);


$data = json_decode(file_get_contents("php://input"), true);

if(isset($data["url"]) && isset($data["usuario_id"]) && isset($data["categoria_id"]) && isset($data["fecha_inicio"]) && isset($data["fecha_fin"]) && isset($data["titulo"]) && isset($data["descripcion"])){
    $url = $data["url"];
    $usuarioId = $data["usuario_id"];
    $categoriaId = $data["categoria_id"];
    $fechaInicio = $data["fecha_inicio"];
    $fechaFin = $data["fecha_fin"];
    $titulo = $data["titulo"];
    $descripcion = $data["descripcion"];

    $sql = "INSERT INTO rallys (usuario_id, categoria_id, fecha_inicio, fecha_fin, titulo, descripcion, url, estado) 
        VALUES (:usuario_id, :categoria_id, :fecha_inicio, :fecha_fin, :titulo, :descripcion, :url, 0)";

    $stmt = $conexion->prepare($sql);

    if ($stmt->execute([
        ":usuario_id" => $usuarioId,
        ":categoria_id" => $categoriaId,
        ":fecha_inicio" => $fechaInicio,
        ":fecha_fin" => $fechaFin,
        ":titulo" => $titulo,
        ":descripcion" => $descripcion,
        ":url" => $url
    ])) {
        echo "✅ Rally guardado en la base de datos.";
    } else {
        echo "❌ Error al guardar el rally en la base de datos.";
    }

} else {
    echo "❌ Faltan datos para guardar el rally.";
}

cerrarPDO();