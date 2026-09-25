<?php

require_once '../config/database.php';
require_once '../includes/helpers.php';

list($noticias, $pagina, $paginas) = obtenerPaginado(
    $conexion,
    'noticias',
    "estado = 'publicado'",
    'fecha_publicacion DESC',
    9
);

$pagina_activa = 'actualidad';
$titulo_pagina = 'Noticias - Diálogo y Desarrollo Perú';
require_once '../includes/header.php';

?>

<main class="pagina-interior">
    <section class="breadcrumb-area">
        <div class="contenedor">
            <h1>Noticias Recientes</h1>
            <p class="migas"><a href="index.php">Inicio</a> / Noticias</p>
        </div>
    </section>

    <section class="section-block">
        <div class="contenedor">
            <?php if ($noticias): ?>
                <div class="lista-reportajes tres-cols">
                    <?php foreach ($noticias as $noticia): ?>
                        <?php
                        $destino = !empty($noticia['link_externo'])
                            ? $noticia['link_externo']
                            : 'noticia.php?id=' . (int) $noticia['id'];
                        $esExterno = !empty($noticia['link_externo']);
                        ?>
                        <article class="tarjeta-reportaje">
                            <a href="<?php echo e($destino); ?>" class="tarjeta-link" <?php echo $esExterno ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
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

                <?php if ($paginas > 1): ?>
                    <nav class="paginacion" aria-label="Paginación">
                        <ul>
                            <?php if ($pagina > 1): ?>
                                <li><a href="noticias.php?pagina=<?php echo $pagina - 1; ?>">Ant</a></li>
                            <?php endif; ?>
                            <?php for ($i = 1; $i <= $paginas; $i++): ?>
                                <li><a href="noticias.php?pagina=<?php echo $i; ?>" class="<?php echo $i === $pagina ? 'activo' : ''; ?>"><?php echo $i; ?></a></li>
                            <?php endfor; ?>
                            <?php if ($pagina < $paginas): ?>
                                <li><a href="noticias.php?pagina=<?php echo $pagina + 1; ?>">Sig</a></li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php else: ?>
                <p class="estado-vacio">Aún no hay noticias. Agrégalas en la tabla noticias.</p>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php require_once '../includes/footer.php'; ?>
