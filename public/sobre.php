<?php

require_once '../includes/helpers.php';

$pagina_activa = 'sobre';
$titulo_pagina = 'Sobre D&D - Diálogo y Desarrollo Perú';
require_once '../includes/header.php';

?>

<main class="pagina-interior">
    <section class="breadcrumb-area">
        <div class="contenedor">
            <h1>Sobre D&amp;D</h1>
            <p class="migas"><a href="index.php">Inicio</a> / Sobre D&amp;D</p>
        </div>
    </section>

    <section class="section-block">
        <div class="contenedor texto-largo">
            <h2>Diálogo y Desarrollo Perú</h2>
            <p>
                Somos un espacio de periodismo independiente que busca visibilizar las acciones
                de diálogo en el país desde una mirada constructiva.
            </p>
            <p>
                Publicamos reportajes, noticias, boletines NTEP, podcasts y videos sobre minería,
                territorio, inversión pública y desarrollo regional.
            </p>
            <p>
                Contacto: <a href="mailto:info@dialogoydesarrollo.com.pe">info@dialogoydesarrollo.com.pe</a>
            </p>
        </div>
    </section>
</main>

<?php require_once '../includes/footer.php'; ?>
