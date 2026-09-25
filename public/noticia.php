<?php

require_once '../config/database.php';
require_once '../includes/helpers.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    http_response_code(400);
    $pagina_activa = 'actualidad';
    $titulo_pagina = 'Noticia no válida';
    require_once '../includes/header.php';
    echo '<main class="pagina-interior"><div class="contenedor"><p class="estado-vacio">Identificador de la noticia no válido.</p></div></main>';
    require_once '../includes/footer.php';
    exit;
}

// Solo noticias publicadas: los borradores no son públicos aunque se conozca su ID.
$consulta = $conexion->prepare("SELECT * FROM noticias WHERE id = :id AND estado = 'publicado' LIMIT 1");
$consulta->execute(['id' => $id]);
$noticia = $consulta->fetch(PDO::FETCH_ASSOC);

if (!$noticia) {
    http_response_code(404);
    $pagina_activa = 'actualidad';
    $titulo_pagina = 'Noticia no encontrada';
    require_once '../includes/header.php';
    echo '<main class="pagina-interior"><div class="contenedor"><p class="estado-vacio">La noticia no existe.</p></div></main>';
    require_once '../includes/footer.php';
    exit;
}

$pagina_activa = 'actualidad';
$titulo_pagina = $noticia['titulo'] . ' - Diálogo y Desarrollo Perú';

require_once '../includes/header.php';

?>

<main class="pagina-reportaje pagina-noticia">
    <div class="contenedor">
        <p class="migas">
            <a href="index.php">Inicio</a> / <a href="noticias.php">Noticias</a>
        </p>

        <p class="fecha"><?php echo e(formatearFecha($noticia['fecha_publicacion'])); ?></p>
        <h1><?php echo e($noticia['titulo']); ?></h1>

        <?php echo htmlImagen($noticia['foto'], $noticia['titulo'], 'imagen-principal'); ?>

        <?php if (!empty($noticia['contenido'])): ?>
            <div class="desarrollo rich-content rich-noticia">
                <?php echo mostrar_contenido($noticia['contenido']); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($noticia['link_externo'])): ?>
            <p class="adjunto">
                <a class="btn-primary" href="<?php echo e($noticia['link_externo']); ?>" target="_blank" rel="noopener noreferrer">
                    Ver fuente original
                </a>
            </p>
        <?php endif; ?>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
