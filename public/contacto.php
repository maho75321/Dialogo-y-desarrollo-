<?php

use PHPMailer\PHPMailer\PHPMailer;

require_once '../includes/helpers.php';

$pagina_activa = 'contacto';
$titulo_pagina = 'Contacto - Diálogo y Desarrollo Perú';
$avisoOk = '';
$avisoError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim((string) ($_POST['nombre'] ?? ''));
    $correo = trim((string) ($_POST['correo'] ?? ''));
    $mensaje = trim((string) ($_POST['mensaje'] ?? ''));

    if ($nombre === '' || $correo === '' || $mensaje === '') {
        $avisoError = 'Completa nombre, correo y mensaje.';
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $avisoError = 'Ingresa un correo electrónico válido.';
    } else {
        $mailConfig = file_exists('../config/mail.php') ? require '../config/mail.php' : [];
        if (!is_array($mailConfig)) {
            $mailConfig = [];
        }
        $pass = trim((string) ($mailConfig['password'] ?? ''));

        if ($pass === '' || $pass === 'xxxx xxxx xxxx xxxx') {
            $avisoError = 'El envío no está disponible por el momento. Escríbenos a info@dialogoydesarrollo.com.pe.';
        } else {
            try {
                require_once dirname(__DIR__) . '/vendor/autoload.php';

                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = (string) ($mailConfig['host'] ?? 'smtp.gmail.com');
                $mail->Port = (int) ($mailConfig['port'] ?? 587);
                $mail->SMTPAuth = true;
                $mail->Username = (string) ($mailConfig['username'] ?? '');
                $mail->Password = str_replace(' ', '', $pass);
                $mail->SMTPSecure = ((string) ($mailConfig['encryption'] ?? 'tls') === 'ssl')
                    ? PHPMailer::ENCRYPTION_SMTPS
                    : PHPMailer::ENCRYPTION_STARTTLS;
                $mail->CharSet = 'UTF-8';
                $mail->Timeout = 20;

                $mail->setFrom(
                    (string) ($mailConfig['from_email'] ?? $mail->Username),
                    (string) ($mailConfig['from_name'] ?? 'Revista Digital DDP')
                );
                $mail->addAddress('info@dialogoydesarrollo.com.pe');
                $mail->addReplyTo($correo, $nombre);

                $mail->isHTML(true);
                $mail->Subject = 'Contacto web: ' . mb_substr($nombre, 0, 80);
                $mail->Body = '<p><strong>Nombre:</strong> ' . e($nombre) . '</p>'
                    . '<p><strong>Correo:</strong> ' . e($correo) . '</p>'
                    . '<p><strong>Mensaje:</strong></p><p>' . nl2br(e($mensaje)) . '</p>';
                $mail->AltBody = "Nombre: {$nombre}\nCorreo: {$correo}\n\n{$mensaje}";

                $mail->send();
                $avisoOk = 'Tu mensaje fue enviado. Te responderemos pronto.';
            } catch (Throwable $error) {
                $avisoError = 'No se pudo enviar el mensaje. Inténtalo nuevamente.';
            }
        }
    }
}

require_once '../includes/header.php';

?>

<main class="pagina-interior">
    <section class="breadcrumb-area">
        <div class="contenedor">
            <h1>Contacto</h1>
            <p class="migas"><a href="index.php">Inicio</a> / Contacto</p>
        </div>
    </section>

    <section class="section-block">
        <div class="contenedor contacto-grid">
            <div>
                <h2>Escríbenos</h2>
                <p>Puedes escribirnos a <a href="mailto:info@dialogoydesarrollo.com.pe">info@dialogoydesarrollo.com.pe</a> o usar este formulario.</p>

                <?php if ($avisoOk): ?>
                    <p class="aviso-ok"><?php echo e($avisoOk); ?></p>
                <?php endif; ?>
                <?php if ($avisoError): ?>
                    <p class="aviso-error"><?php echo e($avisoError); ?></p>
                <?php endif; ?>

                <form class="form-contacto" method="post" action="contacto.php">
                    <label>
                        Nombre
                        <input type="text" name="nombre" required>
                    </label>
                    <label>
                        Correo
                        <input type="email" name="correo" required>
                    </label>
                    <label>
                        Mensaje
                        <textarea name="mensaje" rows="6" required></textarea>
                    </label>
                    <button type="submit" class="btn-primary">Enviar</button>
                </form>
            </div>
            <div>
                <h2>Redes</h2>
                <ul class="lista-enlaces">
                    <li><a href="https://www.facebook.com/DialogoyDesarrolloPeru" target="_blank" rel="noreferrer">Facebook</a></li>
                    <li><a href="https://www.tiktok.com/@dialogo.y.desarrollo" target="_blank" rel="noreferrer">TikTok</a></li>
                    <li><a href="https://www.instagram.com/dialogo.y.desarrollo/" target="_blank" rel="noreferrer">Instagram</a></li>
                </ul>
            </div>
        </div>
    </section>
</main>

<?php require_once '../includes/footer.php'; ?>
