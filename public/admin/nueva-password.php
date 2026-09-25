<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/admin-helpers.php';

admin_iniciar_sesion();

if (!empty($_SESSION['usuario_id']) && admin_rol_permitido($_SESSION['rol'] ?? '')) {
    admin_redirect(admin_url('index.php'));
}

$token = (string) ($_GET['token'] ?? ($_POST['token'] ?? ''));
$token = trim($token);

$errores = [];
$tokenValido = false;
$registro = null;

if ($token === '' || !preg_match('/^[a-f0-9]{64}$/i', $token)) {
    $errores[] = 'El enlace de restablecimiento no es válido. Solicita uno nuevo.';
} else {
    $tokenHash = hash('sha256', strtolower($token));

    $stmt = $conexion->prepare('
        SELECT r.*, u.email, (r.expira_en > NOW()) AS vigente
        FROM recuperacion_password r
        INNER JOIN usuarios u ON u.id = r.usuario_id
        WHERE r.token_hash = :thash
        LIMIT 1
    ');
    $stmt->execute(['thash' => $tokenHash]);
    $registro = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$registro) {
        $errores[] = 'El enlace de restablecimiento no es válido. Solicita uno nuevo.';
    } elseif ((int) ($registro['usado'] ?? 1) === 1) {
        $errores[] = 'Este enlace ya fue utilizado. Solicita uno nuevo.';
    } elseif ((int) ($registro['vigente'] ?? 0) !== 1) {
        // El vencimiento se compara con NOW() de MySQL, la misma base
        // temporal usada al crear el token (evita desfases de zona horaria con PHP).
        $errores[] = 'Este enlace ha vencido. Solicita uno nuevo.';
    } else {
        $tokenValido = true;
    }
}

$exito = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $tokenValido && $registro) {
    try {
        admin_validar_csrf();

        $nueva = (string) ($_POST['password_nueva'] ?? '');
        $confirmacion = (string) ($_POST['password_confirm'] ?? '');

        if (strlen($nueva) < 8) {
            $errores[] = 'La nueva contraseña debe tener al menos 8 caracteres.';
        }

        if ($nueva !== $confirmacion) {
            $errores[] = 'La confirmación de la nueva contraseña no coincide.';
        }

        if (!$errores) {
            // password_hash() genera un hash seguro (bcrypt) compatible con password_verify() del login.
            $hash = password_hash($nueva, PASSWORD_DEFAULT);

            $update = $conexion->prepare('UPDATE usuarios SET password_hash = :hash WHERE id = :id');
            $update->execute([
                'hash' => $hash,
                'id' => (int) $registro['usuario_id'],
            ]);

            $marcar = $conexion->prepare('UPDATE recuperacion_password SET usado = 1 WHERE id = :id');
            $marcar->execute(['id' => (int) $registro['id']]);

            // Limpia el proceso de recuperación guardado en sesión.
            unset($_SESSION['recup_email'], $_SESSION['recup_uid']);

            $exito = true;
            $tokenValido = false;
        }
    } catch (Throwable $error) {
        $errores[] = 'No se pudo actualizar la contraseña. Inténtalo nuevamente.';
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nueva contraseña - DDP</title>
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
                <h1>Nueva contraseña</h1>
            </div>
        </div>

        <?php foreach ($errores as $error): ?>
            <div class="admin-alert admin-alert-error"><?php echo e($error); ?></div>
        <?php endforeach; ?>

        <?php if ($exito): ?>
            <div class="admin-alert admin-alert-ok">Contraseña actualizada correctamente. Ya puedes iniciar sesión.</div>
            <p><a class="admin-btn admin-btn-primary admin-btn-full" href="<?php echo e(admin_url('login.php')); ?>">Ir al login</a></p>
        <?php elseif ($tokenValido): ?>
            <form method="post" class="admin-form">
                <?php echo admin_csrf_campo(); ?>
                <input type="hidden" name="token" value="<?php echo e(strtolower($token)); ?>">
                <label class="admin-field" for="password_nueva">
                    <span>Nueva contraseña * (mínimo 8 caracteres)</span>
                    <input id="password_nueva" type="password" name="password_nueva" required autocomplete="new-password">
                </label>
                <label class="admin-field" for="password_confirm">
                    <span>Confirmar nueva contraseña *</span>
                    <input id="password_confirm" type="password" name="password_confirm" required autocomplete="new-password">
                </label>
                <button class="admin-btn admin-btn-primary admin-btn-full" type="submit">Guardar nueva contraseña</button>
            </form>
        <?php else: ?>
            <p><a class="admin-btn admin-btn-primary admin-btn-full" href="<?php echo e(admin_url('recuperar-password.php')); ?>">Solicitar un nuevo enlace</a></p>
        <?php endif; ?>
    </main>
</body>
</html>
