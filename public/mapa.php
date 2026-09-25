<?php

require_once '../includes/helpers.php';

$pagina_activa = 'mapa';
$titulo_pagina = 'Mapa interactivo - Diálogo y Desarrollo Perú';
require_once '../includes/header.php';

?>

<main class="pagina-interior">
    <section class="breadcrumb-area">
        <div class="contenedor">
            <h1>Mapa Interactivo</h1>
            <p class="migas"><a href="index.php">Inicio</a> / Mapa</p>
        </div>
    </section>

    <section class="section-block">
        <div class="contenedor">
            <div class="mapa-responsive">
                <iframe src="https://www.arcgis.com/apps/dashboards/2043d99f304045dea3a1c73918c3e974" title="Mapa interactivo" allowfullscreen loading="lazy"></iframe>
            </div>
            <p class="mapa-fallback">
                Si el mapa no carga aquí, puedes
                <a href="https://www.arcgis.com/apps/dashboards/2043d99f304045dea3a1c73918c3e974" target="_blank" rel="noopener noreferrer">abrirlo directamente en ArcGIS</a>.
            </p>
            <div class="texto-largo">
                <h2>La minería ilegal no genera desarrollo para los territorios donde opera.</h2>
            </div>
        </div>
    </section>
</main>

<?php require_once '../includes/footer.php'; ?>
