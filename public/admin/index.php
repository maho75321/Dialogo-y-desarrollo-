<?php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/admin-helpers.php';

admin_requerir_modulo('dashboard');

$soloPropios = admin_es_autor();

$estadisticas = [
    'reportajes' => ['Reportajes', admin_contar($conexion, 'reportajes', $soloPropios), 'reportajes/index.php', 'reportajes/crear.php', 'fa fa-newspaper-o'],
    'noticias' => ['Noticias', admin_contar($conexion, 'noticias', $soloPropios), 'noticias/index.php', 'noticias/crear.php', 'fa fa-bullhorn'],
];

if (admin_es_admin()) {
    $estadisticas['boletines'] = ['Boletines', admin_contar($conexion, 'boletines'), 'boletines/index.php', 'boletines/crear.php', 'fa fa-file-pdf-o'];
    $estadisticas['podcasts'] = ['Podcasts', admin_contar($conexion, 'podcasts'), 'podcasts/index.php', 'podcasts/crear.php', 'fa fa-headphones'];
    $estadisticas['videos'] = ['Videos', admin_contar($conexion, 'videos'), 'videos/index.php', 'videos/crear.php', 'fa fa-youtube-play'];
    $estadisticas['autores'] = ['Autores', admin_contar($conexion, 'autores'), 'autores/index.php', 'autores/crear.php', 'fa fa-users'];
    $estadisticas['usuarios'] = ['Usuarios', admin_contar($conexion, 'usuarios'), 'usuarios/index.php', 'usuarios/crear.php', 'fa fa-user'];
}

$admin_titulo = 'Dashboard';
$admin_seccion = 'dashboard';
require_once __DIR__ . '/../../includes/admin-header.php';
?>

<section class="admin-page-head">
    <div>
        <p class="admin-kicker">Panel principal</p>
        <h1>Hola, <?php echo e(admin_usuario_nombre()); ?></h1>
    </div>
    <div class="admin-head-actions">
        <a class="admin-btn" href="<?php echo e(admin_url('cambiar-password.php')); ?>">Cambiar contraseña</a>
        <a class="admin-btn" href="<?php echo e(admin_public_url('index.php')); ?>">Volver al sitio</a>
        <a class="admin-btn admin-btn-danger" href="<?php echo e(admin_url('logout.php')); ?>">Cerrar sesión</a>
    </div>
</section>

<?php admin_render_mensajes(); ?>

<section class="admin-stats-grid">
    <?php foreach ($estadisticas as $stat): ?>
        <article class="admin-stat-card">
            <span class="<?php echo e($stat[4]); ?>" aria-hidden="true"></span>
            <div>
                <p><?php echo e($stat[0]); ?></p>
                <strong><?php echo (int) $stat[1]; ?></strong>
            </div>
        </article>
    <?php endforeach; ?>
</section>

<section class="admin-dashboard-grid">
    <article class="admin-card">
        <h2>Crear contenido</h2>
        <div class="admin-link-grid">
            <?php foreach ($estadisticas as $clave => $stat): ?>
                <?php if (!admin_modulo_permitido($clave)) { continue; } ?>
                <a href="<?php echo e(admin_url($stat[3])); ?>">
                    <span class="fa fa-plus" aria-hidden="true"></span>
                    Crear <?php echo e(strtolower($stat[0])); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </article>

    <article class="admin-card">
        <h2>Editar contenido</h2>
        <div class="admin-link-grid">
            <?php foreach ($estadisticas as $clave => $stat): ?>
                <?php if (!admin_modulo_permitido($clave)) { continue; } ?>
                <a href="<?php echo e(admin_url($stat[2])); ?>">
                    <span class="fa fa-pencil" aria-hidden="true"></span>
                    Gestionar <?php echo e(strtolower($stat[0])); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </article>
</section>

<?php require_once __DIR__ . '/../../includes/admin-footer.php'; ?>
