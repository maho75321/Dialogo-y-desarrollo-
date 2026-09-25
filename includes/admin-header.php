<?php

require_once __DIR__ . '/admin-helpers.php';

$admin_titulo = $admin_titulo ?? 'Panel administrador';
$admin_seccion = $admin_seccion ?? 'dashboard';
$admin_nav = [
    'dashboard' => ['Dashboard', 'index.php', 'fa fa-dashboard'],
    'reportajes' => ['Reportajes', 'reportajes/index.php', 'fa fa-newspaper-o'],
    'noticias' => ['Noticias', 'noticias/index.php', 'fa fa-bullhorn'],
    'boletines' => ['Boletines', 'boletines/index.php', 'fa fa-file-pdf-o'],
    'podcasts' => ['Podcasts', 'podcasts/index.php', 'fa fa-headphones'],
    'videos' => ['Videos', 'videos/index.php', 'fa fa-youtube-play'],
    'autores' => ['Autores', 'autores/index.php', 'fa fa-users'],
    'usuarios' => ['Usuarios', 'usuarios/index.php', 'fa fa-user'],
    'cambiar-password' => ['Contraseña', 'cambiar-password.php', 'fa fa-lock'],
];
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($admin_titulo); ?> - DDP</title>
    <link href="https://fonts.googleapis.com/css?family=Cabin:400,500,600&amp;subset=latin-ext,vietnamese" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(admin_asset_url('css/style-starter.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(admin_asset_url('css/style.css')); ?>">
</head>
<body class="admin-body">
    <header class="admin-topbar">
        <a class="admin-brand" href="<?php echo e(admin_url('index.php')); ?>">
            <span class="admin-brand-mark">DDP</span>
            <span>Administrador</span>
        </a>
        <nav class="admin-top-actions" aria-label="Acciones administrativas">
            <a href="<?php echo e(admin_public_url('index.php')); ?>">Volver al sitio</a>
            <a href="<?php echo e(admin_url('logout.php')); ?>">Cerrar sesión</a>
        </nav>
    </header>

    <div class="admin-shell">
        <aside class="admin-sidebar">
            <nav aria-label="Menú administrativo">
                <?php foreach ($admin_nav as $clave => $item): ?>
                    <?php if (!admin_modulo_permitido($clave)) { continue; } ?>
                    <a class="<?php echo $admin_seccion === $clave ? 'activo' : ''; ?>" href="<?php echo e(admin_url($item[1])); ?>">
                        <span class="<?php echo e($item[2]); ?>" aria-hidden="true"></span>
                        <?php echo e($item[0]); ?>
                    </a>
                <?php endforeach; ?>
            </nav>
        </aside>

        <main class="admin-main">
