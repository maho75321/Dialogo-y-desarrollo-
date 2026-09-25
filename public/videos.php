<?php

require_once '../config/database.php';
require_once '../includes/helpers.php';

list($videos, $pagina, $paginas) = obtenerPaginado(
    $conexion,
    'videos',
    "estado = 'publicado'",
    'fecha_publicacion DESC',
    12
);

$pagina_activa = 'videos';
$titulo_pagina = 'Videos - Diálogo y Desarrollo Perú';
require_once '../includes/header.php';

?>

<main class="pagina-interior">
    <section class="breadcrumb-area">
        <div class="contenedor">
            <h1>Videos</h1>
            <p class="migas"><a href="index.php">Inicio</a> / Videos</p>
        </div>
    </section>

    <section class="section-block">
        <div class="contenedor">
            <?php if ($videos): ?>
                <div class="grid-cuatro">
                    <?php foreach ($videos as $video): ?>
                        <?php $vcard = videoCardDatos($video); ?>
                        <article class="tarjeta-media">
                            <?php if ($vcard['thumb'] !== ''): ?>
                                <div class="mini-cover mini-cover-img">
                                    <img src="<?php echo e($vcard['thumb']); ?>" alt="" loading="lazy">
                                    <span class="fa fa-play mini-play" aria-hidden="true"></span>
                                </div>
                            <?php else: ?>
                                <div class="mini-cover">
                                    <span class="fa fa-play" aria-hidden="true"></span>
                                </div>
                            <?php endif; ?>
                            <?php if ($vcard['modo'] === 'iframe'): ?>
                                <div class="media-frame">
                                    <?php echo iframeSeguro($vcard['embed'], $video['titulo']); ?>
                                </div>
                            <?php elseif ($vcard['externa'] !== ''): ?>
                                <a class="media-link-card media-video-card" href="<?php echo e($vcard['externa']); ?>" target="_blank" rel="noopener noreferrer">
                                    <span class="fa fa-play" aria-hidden="true"></span>
                                    <strong>Ver video</strong>
                                </a>
                            <?php else: ?>
                                <p class="estado-vacio">Video no disponible.</p>
                            <?php endif; ?>
                            <p class="fecha"><?php echo e(formatearFecha($video['fecha_publicacion'])); ?></p>
                            <h3><?php echo e($video['titulo']); ?></h3>
                            <div class="card-actions">
                                <?php if ($vcard['modo'] === 'iframe'): ?>
                                    <button type="button" class="btn-secondary btn-mini" data-modal-abrir data-modo="iframe" data-src="<?php echo e($vcard['embed']); ?>" data-titulo="<?php echo e($video['titulo']); ?>">Ampliar</button>
                                <?php elseif ($vcard['externa'] !== ''): ?>
                                    <a class="btn-link" href="<?php echo e($vcard['externa']); ?>" target="_blank" rel="noopener noreferrer">Abrir en plataforma</a>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <?php if ($paginas > 1): ?>
                    <nav class="paginacion" aria-label="Paginación">
                        <ul>
                            <?php if ($pagina > 1): ?>
                                <li><a href="videos.php?pagina=<?php echo $pagina - 1; ?>">Ant</a></li>
                            <?php endif; ?>
                            <?php for ($i = 1; $i <= $paginas; $i++): ?>
                                <li><a href="videos.php?pagina=<?php echo $i; ?>" class="<?php echo $i === $pagina ? 'activo' : ''; ?>"><?php echo $i; ?></a></li>
                            <?php endfor; ?>
                            <?php if ($pagina < $paginas): ?>
                                <li><a href="videos.php?pagina=<?php echo $pagina + 1; ?>">Sig</a></li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php else: ?>
                <p class="estado-vacio">Aún no hay videos. Agrégalos en la tabla videos.</p>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php require_once '../includes/footer.php'; ?>
