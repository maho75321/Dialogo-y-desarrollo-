<?php
/**
 * Archivo temporal para restablecer la cuenta administradora.
 * Ejecutar una sola vez desde el navegador y ELIMINAR este archivo después.
 *
 * URL: http://localhost/revista-digital/database/restablecer_admin.php
 */

require_once dirname(__DIR__) . '/config/database.php';

$correo = '017200915e@uandina.edu.pe';
$claveTemporal = 'Admin12345!';
$hash = password_hash($claveTemporal, PASSWORD_DEFAULT);

try {
    $stmt = $conexion->prepare('SELECT id FROM usuarios WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $correo]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        $update = $conexion->prepare('
            UPDATE usuarios
            SET password_hash = :hash,
                rol = :rol,
                nombres = :nombres,
                ap_paterno = :ap_paterno
            WHERE email = :email
        ');
        $update->execute([
            'hash' => $hash,
            'rol' => 'admin',
            'nombres' => 'Administrador',
            'ap_paterno' => 'DDP',
            'email' => $correo,
        ]);
        $mensaje = 'La cuenta ' . $correo . ' fue actualizada. Rol: admin. Contraseña temporal: ' . $claveTemporal;
    } else {
        $insert = $conexion->prepare('
            INSERT INTO usuarios (nombres, ap_paterno, ap_materno, email, password_hash, rol)
            VALUES (:nombres, :ap_paterno, NULL, :email, :hash, :rol)
        ');
        $insert->execute([
            'nombres' => 'Administrador',
            'ap_paterno' => 'DDP',
            'email' => $correo,
            'hash' => $hash,
            'rol' => 'admin',
        ]);
        $mensaje = 'Se creó la cuenta ' . $correo . '. Rol: admin. Contraseña temporal: ' . $claveTemporal;
    }

    $ok = true;
} catch (Throwable $error) {
    $ok = false;
    $mensaje = 'No se pudo restablecer la cuenta. Ejecuta primero database/migracion_roles.sql y verifica que exista la columna password_hash.';
}

header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Restablecer administrador</title>
</head>
<body>
    <h1><?php echo $ok ? 'Operación completada' : 'Error'; ?></h1>
    <p><?php echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?></p>
    <p><strong>Importante:</strong> elimina este archivo (database/restablecer_admin.php) después de usarlo.</p>
</body>
</html>
