<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/admin-helpers.php';

admin_iniciar_sesion();

if (!empty($_SESSION['usuario_id']) && admin_rol_permitido($_SESSION['rol'] ?? '')) {
    admin_redirect(admin_url('index.php'));
}

$errorLogin = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        admin_validar_csrf();

        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if (!admin_columna_existe($conexion, 'usuarios', 'password_hash')) {
            throw new RuntimeException('La columna password_hash no existe en usuarios.');
        }

        $stmt = $conexion->prepare('SELECT * FROM usuarios WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        $activo = true;
        if ($usuario && admin_columna_existe($conexion, 'usuarios', 'activo')) {
            $activo = (int) ($usuario['activo'] ?? 0) === 1;
        }

        $hash = is_array($usuario) ? (string) ($usuario['password_hash'] ?? '') : '';
        $rol = is_array($usuario) ? (string) ($usuario['rol'] ?? '') : '';

        if ($usuario
            && $activo
            && $hash !== ''
            && admin_rol_permitido($rol)
            && password_verify($password, $hash)
        ) {
            session_regenerate_id(true);
            $_SESSION['usuario_id'] = (int) $usuario['id'];
            $_SESSION['nombre'] = trim(($usuario['nombres'] ?? '') . ' ' . ($usuario['ap_paterno'] ?? ''));
            $_SESSION['email'] = $usuario['email'];
            $_SESSION['rol'] = $rol;

            admin_redirect(admin_url('index.php'));
        }

        $errorLogin = 'Credenciales incorrectas.';
    } catch (Throwable $error) {
        $errorLogin = 'No se pudo iniciar sesión. Inténtalo nuevamente.';
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login administrador - DDP</title>
    <link href="https://fonts.googleapis.com/css?family=Cabin:400,500,600&amp;subset=latin-ext,vietnamese" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(admin_asset_url('css/style-starter.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(admin_asset_url('css/style.css')); ?>">
</head>
<body class="admin-login-body">
    <main class="admin-login-card">
        <a class="admin-login-back" href="<?php echo e(admin_public_url('index.php')); ?>">Volver al sitio</a>
        <div class="admin-login-brand">
            <span class="admin-brand-mark">DDP</span>
            <div>
                <p>Diálogo y Desarrollo Perú</p>
                <h1>Administrador</h1>
            </div>
        </div>

        <?php if ($errorLogin): ?>
            <div class="admin-alert admin-alert-error"><?php echo e($errorLogin); ?></div>
        <?php endif; ?>

        <form method="post" class="admin-form">
            <?php echo admin_csrf_campo(); ?>
            <label class="admin-field" for="email">
                <span>Correo electrónico</span>
                <input id="email" type="email" name="email" value="<?php echo e($email); ?>" required autocomplete="email">
            </label>
            <label class="admin-field" for="password">
                <span>Contraseña</span>
                <input id="password" type="password" name="password" required autocomplete="current-password">
            </label>
            <button class="admin-btn admin-btn-primary admin-btn-full" type="submit">Iniciar sesión</button>
        </form>

        <p style="margin-top:1rem;text-align:center;"><a href="<?php echo e(admin_url('recuperar-password.php')); ?>">¿Olvidaste tu contraseña?</a></p>
    </main>
</body>
</html>
