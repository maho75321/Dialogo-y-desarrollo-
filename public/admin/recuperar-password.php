<?php

use PHPMailer\PHPMailer\PHPMailer;

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/admin-helpers.php';

admin_iniciar_sesion();

if (!empty($_SESSION['usuario_id']) && admin_rol_permitido($_SESSION['rol'] ?? '')) {
    admin_redirect(admin_url('index.php'));
}

$mailConfig = file_exists(__DIR__ . '/../../config/mail.php')
    ? require __DIR__ . '/../../config/mail.php'
    : [];
if (!is_array($mailConfig)) {
    $mailConfig = [];
}

// Minutos de validez del código de verificación.
define('RECUP_CODIGO_MINUTOS', 10);

/**
 * Solo entorno local EXPLÍCITO (APP_ENV=local o MAIL_DEBUG=1).
 * En cualquier otro caso el código JAMÁS se muestra en pantalla:
 * se envía por Gmail o se informa que el correo no está configurado.
 */
function recup_modo_local()
{
    return getenv('APP_ENV') === 'local' || getenv('MAIL_DEBUG') === '1';
}

function recup_mail_configurado(array $mailConfig)
{
    $pass = trim((string) ($mailConfig['password'] ?? ''));

    return $pass !== '' && $pass !== 'xxxx xxxx xxxx xxxx';
}

$mensajeOk = '';
$mensajeError = '';
$codigoDesarrollo = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        admin_validar_csrf();

        $email = trim((string) ($_POST['email'] ?? ''));

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Ingresa un correo electrónico válido.');
        }

        // Mensaje genérico: no revela si el correo existe o no.
        $mensajeOk = 'Si el correo está registrado, recibirás un código de verificación.';

        $stmt = $conexion->prepare('SELECT id, email FROM usuarios WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            // Código numérico de 6 dígitos con fuente segura (CSPRNG, no predecible).
            $codigo = (string) random_int(100000, 999999);
            $codigoHash = hash('sha256', $codigo);
            // Token interno de reserva: permite el paso a nueva-password.php
            // sin exponer jamás el código fuera del correo.
            $reservaHash = hash('sha256', bin2hex(random_bytes(32)));

            // Invalida códigos/enlaces anteriores sin usar de este usuario.
            $invalidar = $conexion->prepare('UPDATE recuperacion_password SET usado = 1 WHERE usuario_id = :uid AND usado = 0');
            $invalidar->execute(['uid' => (int) $usuario['id']]);

            $insert = $conexion->prepare('
                INSERT INTO recuperacion_password
                    (usuario_id, token_hash, codigo_hash, expira_en, usado, intentos, intentos_max)
                VALUES
                    (:uid, :thash, :chash, DATE_ADD(NOW(), INTERVAL :mins MINUTE), 0, 0, 5)
            ');
            $conexion->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
            $insert->execute([
                'uid' => (int) $usuario['id'],
                'thash' => $reservaHash,
                'chash' => $codigoHash,
                'mins' => RECUP_CODIGO_MINUTOS,
            ]);

            // El proceso se mantiene en sesión (el código nunca viaja en URL).
            $_SESSION['recup_email'] = $usuario['email'];
            $_SESSION['recup_uid'] = (int) $usuario['id'];

            if (recup_mail_configurado($mailConfig)) {
                $smtpUser = (string) ($mailConfig['username'] ?? '');

                if ($smtpUser === '' || !filter_var($smtpUser, FILTER_VALIDATE_EMAIL)) {
                    throw new RuntimeException('Servicio de correo mal configurado.');
                }

                require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = (string) ($mailConfig['host'] ?? 'smtp.gmail.com');
                $mail->Port = (int) ($mailConfig['port'] ?? 587);
                $mail->SMTPAuth = true;
                $mail->Username = $smtpUser;
                // La contraseña de aplicación va sin espacios.
                $mail->Password = str_replace(' ', '', trim((string) ($mailConfig['password'] ?? '')));
                $mail->SMTPSecure = ((string) ($mailConfig['encryption'] ?? 'tls') === 'ssl')
                    ? PHPMailer::ENCRYPTION_SMTPS
                    : PHPMailer::ENCRYPTION_STARTTLS;
                $mail->CharSet = 'UTF-8';
                $mail->Timeout = 20;

                $fromEmail = (string) ($mailConfig['from_email'] ?? $mail->Username);
                $fromName = (string) ($mailConfig['from_name'] ?? 'Revista Digital DDP');
                $mail->setFrom($fromEmail, $fromName);
                $mail->addAddress($usuario['email']);

                $mail->isHTML(true);
                $mail->Subject = 'Tu código de verificación — Revista Digital DDP';
                $mail->Body = '<p>Hola,</p>'
                    . '<p>Tu código de verificación es:</p>'
                    . '<p style="font-size:28px;letter-spacing:6px;"><strong>' . e($codigo) . '</strong></p>'
                    . '<p>Vence en ' . RECUP_CODIGO_MINUTOS . ' minutos y solo puede usarse una vez. '
                    . 'Si no solicitaste este cambio, ignora este mensaje.</p>';
                $mail->AltBody = "Hola,\n\nTu código de verificación es: {$codigo}\n\n"
                    . 'Vence en ' . RECUP_CODIGO_MINUTOS . " minutos y solo puede usarse una vez.\n"
                    . 'Si no solicitaste este cambio, ignora este mensaje.';

                $mail->send();

                admin_redirect(admin_url('verificar-codigo.php?email=' . urlencode($usuario['email'])));
            } elseif (recup_modo_local()) {
                // SOLO entorno local explícito: muestra el código para pruebas
                // sin Gmail. En producción este bloque nunca se ejecuta.
                $codigoDesarrollo = $codigo;
            } else {
                // Sin SMTP configurado: mensaje administrativo claro, sin
                // exponer secretos ni rutas internas y sin mostrar el código.
                $mensajeOk = 'El envío de correos no está disponible por el momento. Contacta al administrador.';
            }
        }
    } catch (Throwable $error) {
        $mensajeError = 'No se pudo procesar la solicitud. Inténtalo nuevamente.';
        $mensajeOk = '';
        $codigoDesarrollo = '';
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recuperar contraseña - DDP</title>
    <link href="https://fonts.googleapis.com/css?family=Cabin:400,500,600&amp;subset=latin-ext,vietnamese" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(admin_asset_url('css/style-starter.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(admin_asset_url('css/style.css')); ?>">
</head>
<body class="admin-login-body">
    <main class="admin-login-card">
        <a class="admin-login-back" href="<?php echo e(admin_url('login.php')); ?>">Volver al login</a>
        <div class="admin-login-brand">
            <span class="admin-brand-mark">DDP</span>
            <div>
                <p>Diálogo y Desarrollo Perú</p>
                <h1>Recuperar contraseña</h1>
            </div>
        </div>

        <p>Ingresa el correo de tu cuenta y te enviaremos un código de verificación.</p>

        <?php if ($mensajeError): ?>
            <div class="admin-alert admin-alert-error"><?php echo e($mensajeError); ?></div>
        <?php endif; ?>

        <?php if ($mensajeOk): ?>
            <div class="admin-alert admin-alert-ok"><?php echo e($mensajeOk); ?></div>
        <?php endif; ?>

        <?php if ($codigoDesarrollo !== ''): ?>
            <div class="admin-alert admin-alert-ok">
                <strong>SOLO ENTORNO LOCAL</strong> (sin SMTP configurado).<br>
                Tu código es: <strong><?php echo e($codigoDesarrollo); ?></strong><br>
                <a href="<?php echo e(admin_url('verificar-codigo.php?email=' . urlencode($email))); ?>">Continuar a verificar el código</a>
            </div>
        <?php endif; ?>

        <form method="post" class="admin-form">
            <?php echo admin_csrf_campo(); ?>
            <label class="admin-field" for="email">
                <span>Correo electrónico</span>
                <input id="email" type="email" name="email" value="<?php echo e($email); ?>" required autocomplete="email">
            </label>
            <button class="admin-btn admin-btn-primary admin-btn-full" type="submit">Enviar código</button>
        </form>

        <p style="margin-top:1rem;"><a href="<?php echo e(admin_url('login.php')); ?>">Volver a iniciar sesión</a></p>
    </main>
</body>
</html>
