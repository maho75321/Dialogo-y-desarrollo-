<?php

require_once '../includes/helpers.php';

$pagina_activa = 'alianzas';
$titulo_pagina = 'Alianzas - Diálogo y Desarrollo Perú';
require_once '../includes/header.php';

?>

<main class="pagina-interior">
    <section class="breadcrumb-area">
        <div class="contenedor">
            <h1>Alianzas</h1>
            <p class="migas"><a href="index.php">Inicio</a> / Alianzas</p>
        </div>
    </section>

    <section class="section-block">
        <div class="contenedor">
            <p>
                Diálogo y Desarrollo Perú trabaja con instituciones públicas, empresas y
                organizaciones sociales para llevar información útil a los territorios.
            </p>
            <p class="estado-vacio">
                Estamos actualizando el directorio de aliados con sus logos.
                Si deseas proponer una alianza, escríbenos desde la página de contacto.
            </p>
            <a href="contacto.php" class="btn-primary">Proponer alianza</a>
        </div>
    </section>
</main>

<?php require_once '../includes/footer.php'; ?>
