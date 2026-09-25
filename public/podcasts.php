<?php

require_once '../config/database.php';
require_once '../includes/helpers.php';

list($podcasts, $pagina, $paginas) = obtenerPaginado(
    $conexion,
    'podcasts',
    "estado = 'publicado'",
    'fecha_publicacion DESC',
    12
);

$pagina_activa = 'podcasts';
$titulo_pagina = 'Podcast - Diálogo y Desarrollo Perú';
require_once '../includes/header.php';

?>

<main class="pagina-interior">
    <section class="breadcrumb-area">
        <div class="contenedor">
            <h1>Podcast</h1>
            <p class="migas"><a href="index.php">Inicio</a> / Podcast</p>
        </div>
    </section>

    <section class="section-block">
        <div class="contenedor">
            <?php if ($podcasts): ?>
                <div class="grid-cuatro">
                    <?php foreach ($podcasts as $podcast): ?>
                        <?php $pcard = podcastCardDatos($podcast); ?>
                        <article class="tarjeta-media">
                            <div class="mini-cover">
                                <span class="fa fa-headphones" aria-hidden="true"></span>
                            </div>
                            <?php if ($pcard['modo'] === 'audio'): ?>
                                <?php $mimeAudio = mimeAudioPorExtension(strtolower(pathinfo($pcard['audio'], PATHINFO_EXTENSION))); ?>
                                <audio controls preload="none">
                                    <source src="<?php echo e($pcard['src']); ?>" <?php echo $mimeAudio !== '' ? 'type="' . e($mimeAudio) . '"' : ''; ?>>
                                    Tu navegador no soporta el reproductor de audio.
                                </audio>
                            <?php elseif ($pcard['modo'] === 'iframe'): ?>
                                <div class="media-frame media-frame-mini">
                                    <?php echo iframeSeguro($pcard['embed'], $podcast['titulo']); ?>
                                </div>
                            <?php elseif ($pcard['externa'] !== ''): ?>
                                <a class="media-link-card" href="<?php echo e($pcard['externa']); ?>" target="_blank" rel="noopener noreferrer">
                                    <span class="fa fa-headphones" aria-hidden="true"></span>
                                    <strong>Escuchar episodio</strong>
                                </a>
                            <?php else: ?>
                                <p class="estado-vacio">Episodio no disponible.</p>
                            <?php endif; ?>
                            <p class="fecha"><?php echo e(formatearFecha($podcast['fecha_publicacion'])); ?></p>
                            <h3><?php echo e($podcast['titulo']); ?></h3>
                            <div class="card-actions">
                                <?php if ($pcard['modo'] === 'audio'): ?>
                                    <button type="button" class="btn-secondary btn-mini" data-modal-abrir data-modo="audio" data-src="<?php echo e($pcard['src']); ?>" data-titulo="<?php echo e($podcast['titulo']); ?>">Ampliar</button>
                                <?php elseif ($pcard['modo'] === 'iframe'): ?>
                                    <button type="button" class="btn-secondary btn-mini" data-modal-abrir data-modo="iframe" data-src="<?php echo e($pcard['embed']); ?>" data-titulo="<?php echo e($podcast['titulo']); ?>">Ampliar</button>
                                    <a class="btn-link" href="<?php echo e($pcard['embed']); ?>" target="_blank" rel="noopener noreferrer">Abrir en plataforma</a>
                                <?php elseif ($pcard['externa'] !== ''): ?>
                                    <a class="btn-link" href="<?php echo e($pcard['externa']); ?>" target="_blank" rel="noopener noreferrer">Abrir en plataforma</a>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <?php if ($paginas > 1): ?>
                    <nav class="paginacion" aria-label="Paginación">
                        <ul>
                            <?php if ($pagina > 1): ?>
                                <li><a href="podcasts.php?pagina=<?php echo $pagina - 1; ?>">Ant</a></li>
                            <?php endif; ?>
                            <?php for ($i = 1; $i <= $paginas; $i++): ?>
                                <li><a href="podcasts.php?pagina=<?php echo $i; ?>" class="<?php echo $i === $pagina ? 'activo' : ''; ?>"><?php echo $i; ?></a></li>
                            <?php endfor; ?>
                            <?php if ($pagina < $paginas): ?>
                                <li><a href="podcasts.php?pagina=<?php echo $pagina + 1; ?>">Sig</a></li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php else: ?>
                <p class="estado-vacio">Aún no hay podcasts. Agrégalos en la tabla podcasts.</p>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php require_once '../includes/footer.php'; ?>
