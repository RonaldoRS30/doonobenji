<?php
function sendemail($imagen, $mail_setFromName, $mail_addAddress, $txt_message, $mail_subject, $template, $mostrar_mensaje) {
    require 'PHPMailer/PHPMailerAutoload.php';
    $mail = new PHPMailer;

    // Configuración SMTP
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';                  // Servidor SMTP
    $mail->SMTPAuth   = true;                              // Habilitar autenticación
    $mail->Username   = 'jhordanroly22@gmail.com';         // Tu correo Gmail
    $mail->Password   = 'rwjt pjni zkrc xjfb';            // Contraseña de aplicación Gmail
    $mail->SMTPSecure = 'tls';                             // Seguridad TLS
    $mail->Port       = 587;                               // Puerto TLS

    // Remitente
    $mail_setFromEmail = 'jhordanroly22@gmail.com';
    $mail->setFrom($mail_setFromEmail, $mail_setFromName);
    $mail->addReplyTo($mail_setFromEmail, $mail_setFromName);

    // Destinatario
    $mail->addAddress($mail_addAddress);

    // Imagen adjunta
    $mail->AddEmbeddedImage($imagen, "my-attach", $imagen);

    // Cuerpo del mensaje
    $message = file_get_contents($template);
    $message = str_replace('{{first_name}}', $mail_setFromName, $message);
    $message = str_replace('{{message}}', $txt_message, $message);
    $message = str_replace('{{customer_email}}', $mail_setFromEmail, $message);

    $mail->isHTML(true);
    $mail->Subject = $mail_subject;
    $mail->msgHTML($message);

    // Envío
    $envio = $mail->send();
    if ($mostrar_mensaje) {
        if (!$envio) {
            echo '<p style="color:red">No se pudo enviar el mensaje..';
            echo 'Error de correo: ' . $mail->ErrorInfo . "</p>";
        } else {
            echo '<p style="color:green">Tu mensaje ha sido enviado!</p>';
        }
    }
}
?>
