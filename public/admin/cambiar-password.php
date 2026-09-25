<?php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/admin-helpers.php';

admin_requerir_modulo('cambiar-password');

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        admin_validar_csrf();

        $actual = (string) ($_POST['password_actual'] ?? '');
        $nueva = (string) ($_POST['password_nueva'] ?? '');
        $confirmacion = (string) ($_POST['password_confirm'] ?? '');

        $stmt = $conexion->prepare('SELECT password_hash FROM usuarios WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => admin_usuario_id_actual()]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario || empty($usuario['password_hash']) || !password_verify($actual, $usuario['password_hash'])) {
            $errores[] = 'La contraseña actual no es correcta.';
        }

        if (strlen($nueva) < 8) {
            $errores[] = 'La nueva contraseña debe tener al menos 8 caracteres.';
        }

        if ($nueva !== $confirmacion) {
            $errores[] = 'La confirmación de la nueva contraseña no coincide.';
        }

        if (!$errores) {
            $update = $conexion->prepare('UPDATE usuarios SET password_hash = :hash WHERE id = :id');
            $update->execute([
                'hash' => password_hash($nueva, PASSWORD_DEFAULT),
                'id' => admin_usuario_id_actual(),
            ]);

            admin_flash('ok', 'Contraseña actualizada correctamente.');
            admin_redirect(admin_url('cambiar-password.php'));
        }
    } catch (Throwable $error) {
        $errores[] = 'No se pudo actualizar la contraseña. Inténtalo nuevamente.';
    }
}

$admin_titulo = 'Cambiar contraseña';
$admin_seccion = 'cambiar-password';
require_once __DIR__ . '/../../includes/admin-header.php';
?>

<section class="admin-page-head">
    <div>
        <p class="admin-kicker">Cuenta</p>
        <h1>Cambiar contraseña</h1>
    </div>
    <a class="admin-btn" href="<?php echo e(admin_url('index.php')); ?>">Volver</a>
</section>

<?php admin_render_mensajes($errores); ?>

<form class="admin-card admin-form" method="post">
    <?php echo admin_csrf_campo(); ?>
    <div class="admin-form-grid">
        <label class="admin-field" for="password_actual">
            <span>Contraseña actual *</span>
            <input id="password_actual" type="password" name="password_actual" required autocomplete="current-password">
        </label>
        <label class="admin-field" for="password_nueva">
            <span>Nueva contraseña *</span>
            <input id="password_nueva" type="password" name="password_nueva" required autocomplete="new-password">
        </label>
        <label class="admin-field" for="password_confirm">
            <span>Confirmar nueva contraseña *</span>
            <input id="password_confirm" type="password" name="password_confirm" required autocomplete="new-password">
        </label>
    </div>
    <div class="admin-form-actions">
        <button class="admin-btn admin-btn-primary" type="submit">Guardar</button>
        <a class="admin-btn" href="<?php echo e(admin_url('index.php')); ?>">Cancelar</a>
    </div>
</form>

<?php require_once __DIR__ . '/../../includes/admin-footer.php'; ?>
