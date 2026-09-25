<?php

require_once '../config/database.php';
require_once '../includes/helpers.php';

list($boletines, $pagina, $paginas) = obtenerPaginado(
    $conexion,
    'boletines',
    '1 = 1',
    'fecha_publicacion DESC',
    9
);

$pagina_activa = 'boletines';
$titulo_pagina = 'Boletines NTEP - Diálogo y Desarrollo Perú';
require_once '../includes/header.php';

?>

<main class="pagina-interior">
    <section class="breadcrumb-area">
        <div class="contenedor">
            <h1>Boletines NTEP</h1>
            <p class="migas"><a href="index.php">Inicio</a> / Boletines</p>
        </div>
    </section>

    <section class="section-block">
        <div class="contenedor">
            <?php if ($boletines): ?>
                <div class="lista-reportajes tres-cols">
                    <?php foreach ($boletines as $boletin): ?>
                        <?php $pdfUrl = boletinPdfDisponible($boletin); ?>
                        <article class="tarjeta-reportaje">
                            <?php if ($pdfUrl !== ''): ?>
                            <a href="<?php echo e($pdfUrl); ?>" class="tarjeta-link" target="_blank" rel="noopener noreferrer">
                                <div class="tarjeta-imagen">
                                    <?php echo htmlImagen($boletin['foto_portada'], 'Boletín ' . $boletin['numero_boletin'], 'media-fit'); ?>
                                </div>
                                <div class="tarjeta-contenido">
                                    <p class="fecha"><?php echo e(formatearFecha($boletin['fecha_publicacion'])); ?></p>
                                    <h3><?php echo e(formatearNumeroBoletin($boletin['numero_boletin'])); ?></h3>
                                    <?php if (!empty($boletin['resumen'])): ?>
                                        <p><?php echo e($boletin['resumen']); ?></p>
                                    <?php endif; ?>
                                    <span class="btn-link">Ver boletín →</span>
                                </div>
                            </a>
                            <?php else: ?>
                                <div class="tarjeta-imagen">
                                    <?php echo htmlImagen($boletin['foto_portada'], 'Boletín ' . $boletin['numero_boletin'], 'media-fit'); ?>
                                </div>
                                <div class="tarjeta-contenido">
                                    <p class="fecha"><?php echo e(formatearFecha($boletin['fecha_publicacion'])); ?></p>
                                    <h3><?php echo e(formatearNumeroBoletin($boletin['numero_boletin'])); ?></h3>
                                    <?php if (!empty($boletin['resumen'])): ?>
                                        <p><?php echo e($boletin['resumen']); ?></p>
                                    <?php endif; ?>
                                    <span class="estado-vacio">PDF no disponible por el momento.</span>
                                </div>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>

                <?php if ($paginas > 1): ?>
                    <nav class="paginacion" aria-label="Paginación">
                        <ul>
                            <?php if ($pagina > 1): ?>
                                <li><a href="boletines.php?pagina=<?php echo $pagina - 1; ?>">Ant</a></li>
                            <?php endif; ?>
                            <?php for ($i = 1; $i <= $paginas; $i++): ?>
                                <li><a href="boletines.php?pagina=<?php echo $i; ?>" class="<?php echo $i === $pagina ? 'activo' : ''; ?>"><?php echo $i; ?></a></li>
                            <?php endfor; ?>
                            <?php if ($pagina < $paginas): ?>
                                <li><a href="boletines.php?pagina=<?php echo $pagina + 1; ?>">Sig</a></li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php else: ?>
                <p class="estado-vacio">Aún no hay boletines. Agrégalos en la tabla boletines.</p>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php require_once '../includes/footer.php'; ?>
