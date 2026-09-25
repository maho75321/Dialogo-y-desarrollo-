<?php

require_once '../config/database.php';
require_once '../includes/helpers.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    http_response_code(400);
    $pagina_activa = 'reportajes';
    $titulo_pagina = 'Reportaje no válido';
    require_once '../includes/header.php';
    echo '<main class="pagina-interior"><div class="contenedor"><p class="estado-vacio">Identificador del reportaje no válido.</p></div></main>';
    require_once '../includes/footer.php';
    exit;
}

// Los borradores no son públicos aunque se conozca su ID.
$consulta = $conexion->prepare(consultaReportajeBase() . " WHERE r.id = :id AND r.estado = 'publicado' LIMIT 1");
$consulta->execute(['id' => $id]);
$reportaje = $consulta->fetch(PDO::FETCH_ASSOC);

if (!$reportaje) {
    http_response_code(404);
    $pagina_activa = 'reportajes';
    $titulo_pagina = 'Reportaje no encontrado';
    require_once '../includes/header.php';
    echo '<main class="pagina-interior"><div class="contenedor"><p class="estado-vacio">El reportaje no existe.</p></div></main>';
    require_once '../includes/footer.php';
    exit;
}

$consulta_fotos = $conexion->prepare("
    SELECT *
    FROM reportajes_fotos
    WHERE reportaje_id = :id
    ORDER BY orden ASC, id ASC
");
$consulta_fotos->execute(['id' => $id]);
$fotos = $consulta_fotos->fetchAll(PDO::FETCH_ASSOC);

$pagina_activa = 'reportajes';
$titulo_pagina = $reportaje['titulo'] . ' - Diálogo y Desarrollo Perú';

require_once '../includes/header.php';

?>

<main class="pagina-reportaje">
    <div class="contenedor">
        <p class="migas">
            <a href="index.php">Inicio</a> / <a href="reportajes.php">Reportajes</a>
        </p>

        <p class="fecha"><?php echo e(formatearFecha($reportaje['fecha_publicacion'])); ?></p>
        <h1><?php echo e($reportaje['titulo']); ?></h1>
        <p class="autor">Por: <?php echo e(mostrarAutor($reportaje)); ?></p>

        <?php echo htmlImagen($reportaje['foto_principal'], $reportaje['titulo'], 'imagen-principal'); ?>

        <?php if (!empty($reportaje['resumen_corto'])): ?>
            <p class="resumen"><?php echo e($reportaje['resumen_corto']); ?></p>
        <?php endif; ?>

        <?php if (!empty($reportaje['desarrollo'])): ?>
            <div class="desarrollo rich-content rich-reportaje">
                <?php echo mostrar_contenido($reportaje['desarrollo']); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($fotos)): ?>
            <section class="galeria">
                <h2>Galería de fotografías</h2>
                <div class="galeria-grid">
                    <?php foreach ($fotos as $foto): ?>
                        <figure>
                            <?php echo htmlImagen($foto['url_foto'], $foto['descripcion'] ?: $reportaje['titulo'], 'media-fit'); ?>
                            <?php if (!empty($foto['descripcion'])): ?>
                                <figcaption><?php echo e($foto['descripcion']); ?></figcaption>
                            <?php endif; ?>
                        </figure>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if (!empty($reportaje['pdf_adjunto'])): ?>
            <p class="adjunto">
                <a class="btn-primary" href="<?php echo e(rutaMedia($reportaje['pdf_adjunto'])); ?>" target="_blank" rel="noopener noreferrer">
                    Leer PDF
                </a>
            </p>
        <?php endif; ?>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
