<?php

require_once '../config/database.php';
require_once '../includes/helpers.php';

$por_pagina = 9;
$pagina = max(1, (int) filter_input(INPUT_GET, 'pagina', FILTER_VALIDATE_INT));

$total = (int) $conexion->query("SELECT COUNT(*) FROM reportajes WHERE estado = 'publicado'")->fetchColumn();
$paginas = max(1, (int) ceil($total / $por_pagina));
$pagina = min($pagina, $paginas);
$offset = ($pagina - 1) * $por_pagina;

$consulta = $conexion->prepare(
    consultaReportajeBase() . "
    WHERE r.estado = 'publicado'
    ORDER BY r.fecha_publicacion DESC
    LIMIT :limite OFFSET :offset
"
);
$consulta->bindValue(':limite', $por_pagina, PDO::PARAM_INT);
$consulta->bindValue(':offset', $offset, PDO::PARAM_INT);
$consulta->execute();
$reportajes = $consulta->fetchAll(PDO::FETCH_ASSOC);

$pagina_activa = 'reportajes';
$titulo_pagina = 'Reportajes - Diálogo y Desarrollo Perú';
require_once '../includes/header.php';

?>

<main class="pagina-interior">
    <section class="breadcrumb-area">
        <div class="contenedor">
            <h1>Reportajes</h1>
            <p class="migas"><a href="index.php">Inicio</a> / Reportajes</p>
        </div>
    </section>

    <section class="section-block">
        <div class="contenedor">
            <?php if ($reportajes): ?>
                <div class="lista-reportajes tres-cols">
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

                <?php if ($paginas > 1): ?>
                    <nav class="paginacion" aria-label="Paginación">
                        <ul>
                            <?php if ($pagina > 1): ?>
                                <li><a href="reportajes.php?pagina=<?php echo $pagina - 1; ?>">Ant</a></li>
                            <?php endif; ?>
                            <?php for ($i = 1; $i <= $paginas; $i++): ?>
                                <li><a href="reportajes.php?pagina=<?php echo $i; ?>" class="<?php echo $i === $pagina ? 'activo' : ''; ?>"><?php echo $i; ?></a></li>
                            <?php endfor; ?>
                            <?php if ($pagina < $paginas): ?>
                                <li><a href="reportajes.php?pagina=<?php echo $pagina + 1; ?>">Sig</a></li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php else: ?>
                <p class="estado-vacio">Aún no hay reportajes. Agrégalos en la tabla reportajes.</p>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php require_once '../includes/footer.php'; ?>
