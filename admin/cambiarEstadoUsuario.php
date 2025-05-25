<?php
require_once("../utiles/variables.php");
require_once("../utiles/funciones.php");
require_once('../PHPMailer/src/PHPMailer.php');
require_once('../PHPMailer/src/SMTP.php');
require_once('../PHPMailer/src/Exception.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
if (!isset($_SESSION["rol"]) || $_SESSION["rol"] != 1) {
    header("Location: ../index.php");
    exit();
}

if (isset($_GET['usuario_id']) && isset($_GET['nuevo_estado'])) {
    $usuarioId = (int)$_GET['usuario_id'];
    $nuevoEstado = (int)$_GET['nuevo_estado'];

    $conexion = conectarPDO($host, $user, $password, $bbdd);
    $sqlconsulta = "SELECT email, nombre, estado FROM usuarios WHERE id = :id";
    $stmt = $conexion->prepare($sqlconsulta);
    $stmt->bindParam(':id', $usuarioId, PDO::PARAM_INT);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);  
    $estadoOriginal = $usuario['estado'];
    $email = $usuario['email'];
    $nombre = $usuario['nombre'];

    $sql = "UPDATE usuarios SET estado = :estado" . ($estadoOriginal == 2 ? ", token = :token" : "") . " WHERE id = :id ";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':estado', $nuevoEstado, PDO::PARAM_INT);
    $stmt->bindParam(':id', $usuarioId, PDO::PARAM_INT);
    if ($estadoOriginal == 2) {
        $token = bin2hex(random_bytes(16)); 
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
    }
    $stmt->execute();
    $conexion = null;
    cerrarPDO();
    if($nuevoEstado == 2){
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

                    $mail->setFrom('rallyfotografico@zohomail.eu', 'Rally Fotográfico');
                    $mail->addAddress($email, $nombre);

                    $mail->isHTML(true);
                    $mail->Subject = 'Cuenta Bloqueada - Rally Fotográfico';

                    $mail->Body = "
                        <h2>Hola " . htmlspecialchars($usuario['nombre']) . "!</h2>
                        <p>Tu cuenta ha sido bloqueada en <strong>Rally Fotográfico</strong>.</p>
                        <p>Para restablecer su cuenta contacte con un administrador.</p>
                        <hr>
                        <p>Saludos,<br>Equipo Rally Fotográfico</p>
                    ";

                    $mail->send();

                } catch (Exception $e) {
                    $mail->ErrorInfo;
                }
    }
    if ($estadoOriginal == 2 && $nuevoEstado != 2) {
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

                    $mail->setFrom('rallyfotografico@zohomail.eu', 'Rally Fotográfico');
                    $mail->addAddress($email, $nombre);

                    $mail->isHTML(true);
                    $mail->Subject = 'Cuenta Desbloqueada - Rally Fotográfico';

                    $urlValidacion = "https://jaigardel.byethost18.com/login/establecer.php?email=" . urlencode($email) . "&token=" . urlencode($token);

                    $mail->Body = "
                        <h2>Hola " . htmlspecialchars($usuario['nombre']) . "!</h2>
                        <p>Tu cuenta ha sido desbloqueada en <strong>Rally Fotográfico</strong>.</p>
                        <p>Para establecer una nueva contraseña, haz clic en el siguiente enlace:</p>
                        <p><a href='$urlValidacion'>Recuperar contraseña</a></p>
                        <br>
                        <p>Si no solicitaste esta acción, puedes ignorar este correo.</p>
                        <hr>
                        <p>Saludos,<br>Equipo Rally Fotográfico</p>
                    ";

                    $mail->send();

                } catch (Exception $e) {
                    $mail->ErrorInfo;
                }
    }

}

header("Location: gestionUsuarios.php"); 
exit();
?>
