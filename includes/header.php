<?php
require_once __DIR__ . '/helpers.php';

$pagina_activa = $pagina_activa ?? 'inicio';
$titulo_pagina = $titulo_pagina ?? 'DDP Noticias - Diálogo y Desarrollo Perú';
$logo_archivo = '';
foreach (['logo.png', 'logo.jpg', 'logo.jpeg', 'logo.webp'] as $logo_candidato) {
    if (file_exists(__DIR__ . '/../assets/img/' . $logo_candidato)) {
        $logo_archivo = $logo_candidato;
        break;
    }
}
$logo = $logo_archivo !== '';
$css_principal = __DIR__ . '/../assets/css/style.css';
$css_base = __DIR__ . '/../assets/css/style-starter.css';
$version_css_principal = file_exists($css_principal) ? filemtime($css_principal) : time();
$version_css_base = file_exists($css_base) ? filemtime($css_base) : $version_css_principal;
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo e($titulo_pagina); ?></title>
    <link href="https://fonts.googleapis.com/css?family=Cabin:400,500,600&amp;subset=latin-ext,vietnamese" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style-starter.css?v=<?php echo (int) $version_css_base; ?>">
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo (int) $version_css_principal; ?>">
</head>
<body>

<header id="site-header" class="fixed-top">
    <div class="container">
        <nav class="navbar navbar-expand-lg stroke">
            <a class="navbar-brand" href="index.php">
                <?php if ($logo): ?>
                    <img src="../assets/img/<?php echo e($logo_archivo); ?>" alt="Diálogo y Desarrollo Perú" title="Diálogo y Desarrollo Perú" style="height:75px;">
                <?php else: ?>
                    <span class="logo-fallback">DDP</span>
                <?php endif; ?>
            </a>
            <button class="navbar-toggler collapsed bg-gradient" type="button" data-toggle="collapse"
                data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false"
                aria-label="Abrir menú">
                <span class="navbar-toggler-icon fa icon-expand fa-bars"></span>
                <span class="navbar-toggler-icon fa icon-close fa-times"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item <?php echo $pagina_activa === 'inicio' ? 'active' : ''; ?>">
                        <a class="nav-link" href="index.php">Inicio</a>
                    </li>
                    <li class="nav-item <?php echo $pagina_activa === 'actualidad' ? 'active' : ''; ?>">
                        <a class="nav-link" href="noticias.php">Actualidad</a>
                    </li>
                    <li class="nav-item <?php echo $pagina_activa === 'reportajes' ? 'active' : ''; ?>">
                        <a class="nav-link" href="reportajes.php">Reportajes</a>
                    </li>
                    <li class="nav-item <?php echo $pagina_activa === 'podcasts' ? 'active' : ''; ?>">
                        <a class="nav-link" href="podcasts.php">Podcast</a>
                    </li>
                    <li class="nav-item <?php echo $pagina_activa === 'boletines' ? 'active' : ''; ?>">
                        <a class="nav-link" href="boletines.php">Boletín NTEP</a>
                    </li>
                    <li class="nav-item <?php echo $pagina_activa === 'alianzas' ? 'active' : ''; ?>">
                        <a class="nav-link" href="alianzas.php">Alianzas</a>
                    </li>
                    <li class="nav-item <?php echo $pagina_activa === 'sobre' ? 'active' : ''; ?>">
                        <a class="nav-link" href="sobre.php">Sobre D&amp;D</a>
                    </li>
                    <li class="nav-item <?php echo $pagina_activa === 'admin' ? 'active' : ''; ?>">
                        <a class="nav-link" href="admin/login.php">Administrador</a>
                    </li>
                    <li class="ml-lg-2 mt-lg-0 mt-3">
                        <a href="contacto.php" class="btn btn-style btn-outline-secondary">Contacto</a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</header>
