<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/admin-helpers.php';

admin_iniciar_sesion();

if (!empty($_SESSION['usuario_id']) && admin_rol_permitido($_SESSION['rol'] ?? '')) {
    admin_redirect(admin_url('index.php'));
}

$emailSesion = trim((string) ($_SESSION['recup_email'] ?? ''));
$email = $emailSesion !== '' ? $emailSesion : trim((string) ($_GET['email'] ?? ($_POST['email'] ?? '')));
$emailBloqueado = $emailSesion !== '';
$codigo = trim((string) ($_POST['codigo'] ?? ''));

$errores = [];
$info = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        admin_validar_csrf();

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Ingresa un correo electrónico válido.');
        }

        if ($emailBloqueado && $email !== $emailSesion) {
            throw new RuntimeException('La solicitud no pudo validarse. Pide un nuevo código.');
        }

        if (!preg_match('/^\d{6}$/', $codigo)) {
            $errores[] = 'Ingresa el código de 6 dígitos que recibiste en tu correo.';
        } else {
            $stmt = $conexion->prepare('SELECT id FROM usuarios WHERE email = :email LIMIT 1');
            $stmt->execute(['email' => $email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            $registro = null;

            if ($usuario) {
                $stmt = $conexion->prepare('
                    SELECT *, (expira_en > NOW()) AS vigente
                    FROM recuperacion_password
                    WHERE usuario_id = :uid AND usado = 0 AND codigo_hash IS NOT NULL
                    ORDER BY id DESC
                    LIMIT 1
                ');
                $stmt->execute(['uid' => (int) $usuario['id']]);
                $registro = $stmt->fetch(PDO::FETCH_ASSOC);
            }

            if (!$registro) {
                $errores[] = 'No hay una solicitud vigente. Pide un nuevo código.';
            } elseif ((int) ($registro['vigente'] ?? 0) !== 1
                || (int) ($registro['intentos'] ?? 0) >= (int) ($registro['intentos_max'] ?? 5)
            ) {
                $marcar = $conexion->prepare('UPDATE recuperacion_password SET usado = 1 WHERE id = :id');
                $marcar->execute(['id' => (int) $registro['id']]);
                $errores[] = 'El código ha vencido o superó los intentos. Solicita uno nuevo.';
            } elseif (!hash_equals((string) $registro['codigo_hash'], hash('sha256', $codigo))) {
                $sumar = $conexion->prepare('UPDATE recuperacion_password SET intentos = intentos + 1 WHERE id = :id');
                $sumar->execute(['id' => (int) $registro['id']]);
                $restan = (int) ($registro['intentos_max'] ?? 5) - (int) ($registro['intentos'] ?? 0) - 1;
                $errores[] = $restan > 0
                    ? 'El código ingresado no es correcto. Te quedan ' . $restan . ' intento(s).'
                    : 'El código ingresado no es correcto. Solicita uno nuevo.';
            } else {
                // Código correcto: se consume (no reutilizable) y se emite el
                // token interno que autoriza el cambio en nueva-password.php.
                $token = bin2hex(random_bytes(32));

                $emitir = $conexion->prepare('
                    UPDATE recuperacion_password
                    SET codigo_hash = NULL, token_hash = :thash
                    WHERE id = :id
                ');
                $emitir->execute([
                    'thash' => hash('sha256', strtolower($token)),
                    'id' => (int) $registro['id'],
                ]);

                admin_redirect(admin_url('nueva-password.php?token=' . urlencode($token)));
            }
        }
    } catch (Throwable $error) {
        $errores[] = 'No se pudo verificar el código. Inténtalo nuevamente.';
    }
} elseif ($email !== '') {
    $info = 'Enviamos un código de 6 dígitos a tu correo. Vence en 10 minutos.';
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verificar código - DDP</title>
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
                <h1>Verificar código</h1>
            </div>
        </div>

        <?php if ($info): ?>
            <div class="admin-alert admin-alert-ok"><?php echo e($info); ?></div>
        <?php endif; ?>

        <?php foreach ($errores as $error): ?>
            <div class="admin-alert admin-alert-error"><?php echo e($error); ?></div>
        <?php endforeach; ?>

        <form method="post" class="admin-form">
            <?php echo admin_csrf_campo(); ?>
            <label class="admin-field" for="email">
                <span>Correo electrónico</span>
                <?php if ($emailBloqueado): ?>
                    <input id="email" type="email" value="<?php echo e($email); ?>" disabled autocomplete="email">
                    <input type="hidden" name="email" value="<?php echo e($email); ?>">
                <?php else: ?>
                    <input id="email" type="email" name="email" value="<?php echo e($email); ?>" required autocomplete="email">
                <?php endif; ?>
            </label>
            <label class="admin-field" for="codigo">
                <span>Código de 6 dígitos *</span>
                <input id="codigo" type="text" name="codigo" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" required autocomplete="one-time-code">
            </label>
            <button class="admin-btn admin-btn-primary admin-btn-full" type="submit">Verificar</button>
        </form>

        <p style="margin-top:1rem;"><a href="<?php echo e(admin_url('recuperar-password.php')); ?>">Pedir un nuevo código</a></p>
    </main>
</body>
</html>
