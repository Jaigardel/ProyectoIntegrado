<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("Content-Type: application/json");
require_once("utiles/variables.php");
require_once("utiles/funciones.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$usuarioId = $data["usuario_id"];
$nombre = $data["nombre"];
$apellidos = $data["apellidos"];
$contrasena = $data["contrasena"];
$avatar = $data["avatar"];

try {
    $conexion = conectarPDO($host, $user, $password, $bbdd);

    $sql = "UPDATE usuarios SET nombre = ?, apellidos = ?";
    $params = [$nombre, $apellidos];

    if ($avatar) {
        $sql .= ", avatar = ?";
        $params[] = $avatar;
    }

    if ($contrasena) {
        $token = bin2hex(random_bytes(16));
        $sql .= ", token = ?";
        $sql .= ", contrasena = ?";
        $params[] = $token;
        $params[] = password_hash($contrasena, PASSWORD_DEFAULT);
    }

    $sql .= " WHERE id = ?";
    $params[] = $usuarioId;

    $stmt = $conexion->prepare($sql);
    $stmt->execute($params);

    if ($contrasena) {
        $stmtMail = $conexion->prepare("SELECT email FROM usuarios WHERE id = ?");
        $stmtMail->execute([$usuarioId]);
        $email = $stmtMail->fetchColumn();
        $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.zoho.eu';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'rallyfotografico@zohomail.eu'; 
                    $mail->Password = 'avLs Y7PP 8T4i'; 
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $mail->Port = 465;
                    $mail->CharSet = 'UTF-8';

                    // Remitente y destinatario
                    $mail->setFrom('rallyfotografico@zohomail.eu', 'Rally Fotográfico');
                    $mail->addAddress($email, $nombre);

                    // Contenido del correo
                    $mail->isHTML(true);
                    $mail->Subject = 'Cambio de contraseña - Rally Fotográfico';

                    $urlValidacion = "https://jaigardel.byethost18.com/login/establecer.php?email=" . urlencode($email) . "&token=" . urlencode($token);

                    $mail->Body = "
                        <h2>Hola " . htmlspecialchars($nombre) . "!</h2>
                        <p>Se ha detectado un cambio de su contraseña en <strong>Rally Fotográfico</strong>.</p>
                        <p>Si solicitaste esta acción, puedes ignorar este correo.</p>
                        <p>Si no fuiste tú, puedes restablecer tu contraseña haciendo clic en el siguiente enlace:</p>
                        <p><a href='" . htmlspecialchars($urlValidacion) . "'>Restablecer contraseña</a></p>
                        <hr>
                        <p>Saludos,<br>Equipo Rally Fotográfico</p>
                    ";

                    $mail->send();
                } catch (Exception $e) {
                    error_log("Error al enviar email: " . $mail->ErrorInfo);
                }
    }

    cerrarPDO();
    echo json_encode(["success" => true]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
