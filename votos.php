<?php
require_once("utiles/variables.php");
require_once("utiles/funciones.php");

$conexion = conectarPDO($host, $user, $password, $bbdd);

$sql = "SELECT f.titulo, COUNT(v.id) AS votos 
        FROM fotos f 
        LEFT JOIN votos v ON f.id = v.foto_id
        LEFT JOIN rallys r ON f.rally_id = r.id 
        WHERE r.estado = 1 AND f.estado = 1 
        GROUP BY f.id";

$stmt = $conexion->prepare($sql);
$stmt->execute();

$votos = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($votos);
