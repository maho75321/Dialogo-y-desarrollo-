<?php

require_once '../config/database.php';
require_once '../includes/helpers.php';

$pagina_activa = 'inicio';
$titulo_pagina = 'DDP Noticias - Diálogo y Desarrollo Perú';

// Portada: únicamente el reportaje marcado con es_destacado = 1.
// Si no hay ninguno, se muestra estado vacío (sin usar otro como destacado).
$consulta_destacado = $conexion->prepare(
    consultaReportajeBase() . "
    WHERE r.es_destacado = 1 AND r.estado = 'publicado'
    ORDER BY r.fecha_publicacion DESC
    LIMIT 1
"
);
$consulta_destacado->execute();
$destacado = $consulta_destacado->fetch(PDO::FETCH_ASSOC);

$sql_reportajes = consultaReportajeBase() . " WHERE r.estado = 'publicado'";
$parametros_reportajes = [];

if ($destacado) {
    $sql_reportajes .= " AND r.id <> :destacado_id";
    $parametros_reportajes['destacado_id'] = (int) $destacado['id'];
}

$consulta_reportajes = $conexion->prepare(
    $sql_reportajes . "
    ORDER BY r.fecha_publicacion DESC
    LIMIT 3
"
);
$consulta_reportajes->execute($parametros_reportajes);
$reportajes = $consulta_reportajes->fetchAll(PDO::FETCH_ASSOC);

$consulta_noticias = $conexion->prepare("
    SELECT * FROM noticias
    WHERE estado = 'publicado'
    ORDER BY fecha_publicacion DESC
    LIMIT 3
");
$consulta_noticias->execute();
$noticias = $consulta_noticias->fetchAll(PDO::FETCH_ASSOC);

$consulta_boletin = $conexion->prepare("
    SELECT * FROM boletines
    ORDER BY fecha_publicacion DESC
    LIMIT 1
");
$consulta_boletin->execute();
$boletin = $consulta_boletin->fetch(PDO::FETCH_ASSOC);

$consulta_podcasts = $conexion->prepare("
    SELECT * FROM podcasts
    WHERE estado = 'publicado'
    ORDER BY fecha_publicacion DESC
    LIMIT 4
");
$consulta_podcasts->execute();
$podcasts = $consulta_podcasts->fetchAll(PDO::FETCH_ASSOC);

$consulta_videos = $conexion->prepare("
    SELECT * FROM videos
    WHERE estado = 'publicado'
    ORDER BY fecha_publicacion DESC
    LIMIT 4
");
$consulta_videos->execute();
$videos = $consulta_videos->fetchAll(PDO::FETCH_ASSOC);

require_once '../includes/header.php';

?>

<main class="homepage">

    <section class="breadcrumb-area home-heading">
        <div class="contenedor">
            <h2>Reportajes</h2>
        </div>
    </section>

    <section class="hero-section">
        <div class="contenedor hero-layout">
            <?php if ($destacado): ?>
                <article class="destacado-card">
                    <a href="reportaje.php?id=<?php echo (int) $destacado['id']; ?>" class="destacado-imagen">
                        <?php echo htmlImagen($destacado['foto_principal'], $destacado['titulo'], 'media-fit'); ?>
                    </a>
                    <div class="destacado-contenido">
                        <p class="fecha"><?php echo e(formatearFecha($destacado['fecha_publicacion'])); ?></p>
                        <h1>
                            <a href="reportaje.php?id=<?php echo (int) $destacado['id']; ?>">
                                <?php echo e($destacado['titulo']); ?>
                            </a>
                        </h1>
                        <p class="resumen"><?php echo e($destacado['resumen_corto']); ?></p>
                        <a href="reportaje.php?id=<?php echo (int) $destacado['id']; ?>" class="btn-link">Ver reportaje completo</a>
                    </div>
                </article>
            <?php else: ?>
                <article class="destacado-card vacio">
                    <div class="destacado-contenido">
                        <span class="eyebrow">Reportaje</span>
                        <h1>Aún no hay reportaje destacado</h1>
                        <p class="resumen">El administrador puede marcar uno desde el panel, en Reportajes, con “Marcar como destacado”.</p>
                    </div>
                </article>
            <?php endif; ?>
        </div>
    </section>

    <section class="section-reportajes section-block">
        <div class="contenedor">
            <?php if ($reportajes): ?>
                <div class="lista-reportajes">
                    <?php foreach ($reportajes as $reportaje): ?>
                        <article class="tarjeta-reportaje">
                            <a href="reportaje.php?id=<?php echo (int) $reportaje['id']; ?>" class="tarjeta-link">
                                <div class="tarjeta-imagen">
                                    <?php echo htmlImagen($reportaje['foto_principal'], $reportaje['titulo'], 'media-fit'); ?>
                                </div>
                                <div class="tarjeta-contenido">
                                    <p class="fecha"><?php echo e(formatearFecha($reportaje['fecha_publicacion'])); ?></p>
                                    <h3><?php echo e($reportaje['titulo']); ?></h3>
                                    <span class="btn-link">Leer →</span>
                                </div>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>
                <div class="ver-todos">
                    <a href="reportajes.php">Ver todos</a>
                </div>
            <?php else: ?>
                <p class="estado-vacio">No hay reportajes para mostrar.</p>
            <?php endif; ?>
        </div>
    </section>

    <section class="breadcrumb-area home-heading" id="actualidad">
        <div class="contenedor">
            <h2>Noticias Recientes</h2>
        </div>
    </section>

    <section class="section-block">
        <div class="contenedor">
            <?php if ($noticias): ?>
                <div class="lista-reportajes">
                    <?php foreach ($noticias as $noticia): ?>
                        <?php
                        $destino_noticia = !empty($noticia['link_externo'])
                            ? $noticia['link_externo']
                            : 'noticia.php?id=' . (int) $noticia['id'];
                        $noticia_externa = !empty($noticia['link_externo']);
                        ?>
                        <article class="tarjeta-reportaje">
                            <a href="<?php echo e($destino_noticia); ?>" class="tarjeta-link" <?php echo $noticia_externa ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
                                <div class="tarjeta-imagen">
                                    <?php echo htmlImagen($noticia['foto'], $noticia['titulo'], 'media-fit media-square'); ?>
                                </div>
                                <div class="tarjeta-contenido">
                                    <p class="fecha"><?php echo e(formatearFecha($noticia['fecha_publicacion'])); ?></p>
                                    <h3><?php echo e($noticia['titulo']); ?></h3>
                                    <span class="btn-link">Leer →</span>
                                </div>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>
                <div class="ver-todos">
                    <a href="noticias.php">Ver todos</a>
                </div>
            <?php else: ?>
                <p class="estado-vacio">No hay noticias publicadas todavía.</p>
            <?php endif; ?>
        </div>
    </section>

    <section class="boletin-home">
        <div class="contenedor boletin-home-grid">
            <div>
                <h2>Boletín NTEP Año <?php echo $boletin ? date('Y', strtotime($boletin['fecha_publicacion'])) : date('Y'); ?></h2>
                <?php if ($boletin): ?>
                    <?php if (!empty($boletin['resumen'])): ?>
                        <div class="boletin-resumen"><?php echo nl2br(e($boletin['resumen'])); ?></div>
                    <?php endif; ?>
                    <p class="boletin-meta">
                        <strong><?php echo e(formatearNumeroBoletin($boletin['numero_boletin'])); ?></strong>
                        <span><?php echo e(formatearFecha($boletin['fecha_publicacion'])); ?></span>
                    </p>
                    <?php $boletinPdfUrl = boletinPdfDisponible($boletin); ?>
                    <?php if ($boletinPdfUrl !== ''): ?>
                        <a href="<?php echo e($boletinPdfUrl); ?>" class="btn-primary" target="_blank" rel="noopener noreferrer">Ver boletín</a>
                    <?php else: ?>
                        <p class="estado-vacio">PDF no disponible por el momento.</p>
                    <?php endif; ?>
                    <a href="boletines.php" class="btn-secondary">Ver todos</a>
                <?php else: ?>
                    <p>Cuando cargues ediciones en la tabla boletines, la última aparecerá aquí.</p>
                    <a href="boletines.php" class="btn-secondary">Ver todos</a>
                <?php endif; ?>
            </div>
            <div class="boletin-portada">
                <?php echo htmlImagen($boletin['foto_portada'] ?? '', $boletin ? 'Portada del boletín ' . $boletin['numero_boletin'] : 'Portada del boletín', 'media-fit alto-portada'); ?>
            </div>
        </div>
    </section>

    <section class="section-block podcast-home">
        <div class="contenedor">
            <div class="section-header section-header-center">
                <h2>Podcast</h2>
            </div>
            <?php if ($podcasts): ?>
                <div class="grid-cuatro mini-media-grid">
                    <?php foreach ($podcasts as $podcast): ?>
                        <?php $pcard = podcastCardDatos($podcast); ?>
                        <?php
                        $podcast_modo = $pcard['modo'];
                        $podcast_src = $pcard['src'];
                        $podcast_embed = $pcard['embed'];
                        $podcast_ext = $pcard['externa'];
                        ?>
                        <article class="tarjeta-media mini-media-card">
                            <div class="mini-cover">
                                <span class="fa fa-headphones" aria-hidden="true"></span>
                            </div>
                            <p class="fecha"><?php echo e(formatearFecha($podcast['fecha_publicacion'])); ?></p>
                            <h3><?php echo e($podcast['titulo']); ?></h3>
                            <?php if ($podcast_modo === 'audio'): ?>
                                <audio class="mini-player" controls preload="none">
                                    <source src="<?php echo e($podcast_src); ?>">
                                    Tu navegador no soporta el reproductor de audio.
                                </audio>
                            <?php elseif ($podcast_modo === 'iframe'): ?>
                                <div class="media-frame media-frame-mini">
                                    <?php echo iframeSeguro($podcast_embed, $podcast['titulo']); ?>
                                </div>
                            <?php elseif ($podcast_ext !== ''): ?>
                                <a class="btn-link" href="<?php echo e($podcast_ext); ?>" target="_blank" rel="noopener noreferrer">Escuchar episodio</a>
                            <?php else: ?>
                                <p class="estado-vacio">Sin audio disponible.</p>
                            <?php endif; ?>
                            <div class="card-actions">
                                <?php if ($podcast_modo === 'audio'): ?>
                                    <button type="button" class="btn-secondary btn-mini" data-modal-abrir data-modo="audio" data-src="<?php echo e($podcast_src); ?>" data-titulo="<?php echo e($podcast['titulo']); ?>">Ampliar</button>
                                <?php elseif ($podcast_modo === 'iframe'): ?>
                                    <button type="button" class="btn-secondary btn-mini" data-modal-abrir data-modo="iframe" data-src="<?php echo e($podcast_embed); ?>" data-titulo="<?php echo e($podcast['titulo']); ?>">Ampliar</button>
                                <?php endif; ?>
                                <?php if ($podcast_modo === 'iframe'): ?>
                                    <a class="btn-link" href="<?php echo e($podcast_embed); ?>" target="_blank" rel="noopener noreferrer">Abrir en plataforma</a>
                                <?php elseif ($podcast_ext !== ''): ?>
                                    <a class="btn-link" href="<?php echo e($podcast_ext); ?>" target="_blank" rel="noopener noreferrer">Abrir en plataforma</a>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
                <div class="ver-todos">
                    <a href="podcasts.php" class="btn-primary">Ver todos</a>
                </div>
            <?php else: ?>
                <p class="estado-vacio">No hay podcasts publicados todavía.</p>
            <?php endif; ?>
        </div>
    </section>

    <section class="section-block especiales-home">
        <div class="contenedor">
            <div class="section-header section-header-center">
                <h2>Especiales</h2>
            </div>
            <?php if ($videos): ?>
                <div class="grid-cuatro mini-media-grid">
                    <?php foreach ($videos as $video): ?>
                        <?php $vcard = videoCardDatos($video); ?>
                        <?php
                        $video_embed = $vcard['embed'];
                        $video_modo = $vcard['modo'];
                        $video_thumb = $vcard['thumb'];
                        $video_ext = $vcard['externa'];
                        ?>
                        <article class="tarjeta-media mini-media-card">
                            <?php if ($video_modo !== 'iframe'): ?>
                                <?php if ($video_thumb !== ''): ?>
                                    <div class="mini-cover mini-cover-img">
                                        <img src="<?php echo e($video_thumb); ?>" alt="" loading="lazy">
                                        <span class="fa fa-play mini-play" aria-hidden="true"></span>
                                    </div>
                                <?php else: ?>
                                    <div class="mini-cover">
                                        <span class="fa fa-play" aria-hidden="true"></span>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                            <p class="fecha"><?php echo e(formatearFecha($video['fecha_publicacion'])); ?></p>
                            <h3><?php echo e($video['titulo']); ?></h3>
                            <?php if ($video_modo === 'iframe'): ?>
                                <div class="media-frame media-frame-mini">
                                    <?php echo iframeSeguro($video_embed, $video['titulo']); ?>
                                </div>
                            <?php else: ?>
                                <p class="estado-vacio">Sin reproductor integrado.</p>
                            <?php endif; ?>
                            <div class="card-actions">
                                <?php if ($video_modo !== ''): ?>
                                    <button type="button" class="btn-secondary btn-mini" data-modal-abrir data-modo="iframe" data-src="<?php echo e($video_embed); ?>" data-titulo="<?php echo e($video['titulo']); ?>">Ampliar</button>
                                <?php endif; ?>
                                <?php if ($video_ext !== ''): ?>
                                    <a class="btn-link" href="<?php echo e($video_ext); ?>" target="_blank" rel="noopener noreferrer">Abrir en plataforma</a>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
                <div class="ver-todos">
                    <a href="videos.php" class="btn-primary">Ver todos</a>
                </div>
            <?php else: ?>
                <p class="estado-vacio">No hay videos especiales publicados todavía.</p>
            <?php endif; ?>
        </div>
    </section>

    <section class="about-section">
        <div class="contenedor about-grid">
            <div class="about-copy">
                <span class="eyebrow">DDP Noticias</span>
                <h3>Diálogo y Desarrollo Perú</h3>
                <p>
                    Somos un espacio de periodismo independiente que busca visibilizar las acciones
                    de diálogo en el país desde una mirada constructiva.
                </p>
                <a href="sobre.php" class="btn-primary">Nosotros</a>
            </div>
            <div class="about-image">
                <img src="../assets/img/logo2.jpg" alt="Diálogo y Desarrollo Perú" class="media-fit alto-banner">
            </div>
        </div>
    </section>

</main>

<?php require_once '../includes/footer.php'; ?>
