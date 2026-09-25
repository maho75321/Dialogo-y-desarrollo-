<?php

function admin_crear_directorio_seguro($directorio)
{
    if (!is_dir($directorio)) {
        mkdir($directorio, 0775, true);
    }

    $htaccess = rtrim($directorio, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '.htaccess';

    if (!file_exists($htaccess)) {
        file_put_contents(
            $htaccess,
            "Options -Indexes\n<FilesMatch \"\\.(php|phtml|php3|php4|php5|phar|cgi|pl|asp|aspx|jsp)$\">\n    Require all denied\n</FilesMatch>\n"
        );
    }
}

function admin_configuracion_upload($tipo)
{
    $raiz = dirname(__DIR__);

    $configuraciones = [
        'imagen' => [
            'extensiones' => ['jpg', 'jpeg', 'png', 'webp'],
            'mimes' => ['image/jpeg', 'image/png', 'image/webp'],
            // Límite máximo por imagen: 5 MB (validado en el servidor).
            'max' => 5 * 1024 * 1024,
            'max_legible' => '5 MB',
            'directorio' => $raiz . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'uploads',
            'relativo' => 'assets/img/uploads',
        ],
        'pdf' => [
            'extensiones' => ['pdf'],
            'mimes' => ['application/pdf', 'application/x-pdf'],
            'max' => 8 * 1024 * 1024,
            'max_legible' => '8 MB',
            'directorio' => $raiz . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'boletines',
            'relativo' => 'uploads/boletines',
        ],
        'audio' => [
            'extensiones' => ['mp3', 'm4a', 'mp4', 'ogg', 'oga', 'wav'],
            'mimes' => [
                'audio/mpeg', 'audio/mp3', 'audio/mp4', 'audio/x-m4a', 'audio/aac',
                'audio/ogg', 'audio/vorbis', 'audio/wav', 'audio/x-wav', 'video/mp4',
            ],
            // Podcast típico de 5-15 min: 25 MB es suficiente sin saturar el servidor.
            'max' => 25 * 1024 * 1024,
            'max_legible' => '25 MB',
            'directorio' => $raiz . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'podcasts',
            'relativo' => 'uploads/podcasts',
        ],
    ];

    if (!isset($configuraciones[$tipo])) {
        throw new InvalidArgumentException('Tipo de archivo no soportado.');
    }

    return $configuraciones[$tipo];
}

function admin_archivo_multiple($campo, $indice)
{
    if (empty($_FILES[$campo]) || !isset($_FILES[$campo]['name'][$indice])) {
        return null;
    }

    return [
        'name' => $_FILES[$campo]['name'][$indice],
        'type' => $_FILES[$campo]['type'][$indice],
        'tmp_name' => $_FILES[$campo]['tmp_name'][$indice],
        'error' => $_FILES[$campo]['error'][$indice],
        'size' => $_FILES[$campo]['size'][$indice],
    ];
}

function admin_biblioteca_directorios()
{
    $raiz = dirname(__DIR__);

    return [
        $raiz . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img',
        $raiz . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'uploads',
        $raiz . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'noticias',
        $raiz . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'reportajes',
        $raiz . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'boletines',
        $raiz . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'podcasts',
    ];
}

function admin_biblioteca_imagenes()
{
    static $cache = null;

    if ($cache !== null) {
        return $cache;
    }

    $raiz = dirname(__DIR__);
    $permitidas = ['jpg', 'jpeg', 'png', 'webp'];
    $lista = [];

    foreach (admin_biblioteca_directorios() as $directorio) {
        if (!is_dir($directorio)) {
            continue;
        }

        foreach (scandir($directorio) as $archivo) {
            if ($archivo === '.' || $archivo === '..' || $archivo === '.htaccess') {
                continue;
            }

            $rutaFisica = $directorio . DIRECTORY_SEPARATOR . $archivo;

            if (!is_file($rutaFisica)) {
                continue;
            }

            $extension = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));

            if (!in_array($extension, $permitidas, true)) {
                continue;
            }

            $relativa = substr($rutaFisica, strlen($raiz) + 1);
            $relativa = str_replace(DIRECTORY_SEPARATOR, '/', $relativa);
            $lista[] = $relativa;
        }
    }

    sort($lista);
    $cache = $lista;

    return $cache;
}

function admin_biblioteca_validar($ruta, $tipo = 'imagen')
{
    $ruta = trim((string) $ruta);

    if ($ruta === '' || preg_match('#^https?://#i', $ruta)) {
        return '';
    }

    // Sin rutas absolutas ni salida del proyecto.
    if (strpos($ruta, '..') !== false || strpos($ruta, '\\') !== false || $ruta[0] === '/') {
        return '';
    }

    $config = admin_configuracion_upload($tipo);
    $extension = strtolower(pathinfo($ruta, PATHINFO_EXTENSION));

    if (!in_array($extension, $config['extensiones'], true)) {
        return '';
    }

    $raiz = dirname(__DIR__);
    $fisica = $raiz . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $ruta);

    if (!is_file($fisica)) {
        return '';
    }

    // Solo dentro de los directorios permitidos de la biblioteca.
    foreach (admin_biblioteca_directorios() as $directorio) {
        $directorio = rtrim($directorio, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if (strpos($fisica, $directorio) === 0) {
            return str_replace(DIRECTORY_SEPARATOR, '/', $ruta);
        }
    }

    return '';
}

function admin_eliminar_fisico_si_subido($ruta)
{
    if (empty($ruta) || preg_match('#^https?://#i', $ruta)) {
        return false;
    }

    // Solo se borra el archivo físico si vive en carpetas de subidas
    // generadas (no las imágenes semilla de assets/img/).
    $prefijosBorrables = ['assets/img/uploads/', 'uploads/'];
    $esBorrable = false;

    foreach ($prefijosBorrables as $prefijo) {
        if (strpos($ruta, $prefijo) === 0) {
            $esBorrable = true;
            break;
        }
    }

    if (!$esBorrable) {
        return false;
    }

    $raiz = dirname(__DIR__);
    $fisica = $raiz . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, ltrim($ruta, '/'));

    if (is_file($fisica)) {
        @unlink($fisica);

        return true;
    }

    return false;
}

function admin_guardar_archivo_subido($archivo, $tipo, array &$errores, $requerido = false)
{
    if (!$archivo || ($archivo['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        if ($requerido) {
            $errores[] = 'Debes seleccionar un archivo.';
        }

        return null;
    }

    if (($archivo['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        $errores[] = 'No se pudo subir el archivo.';
        return null;
    }

    if (empty($archivo['tmp_name']) || !is_uploaded_file($archivo['tmp_name'])) {
        $errores[] = 'El archivo subido no es válido.';
        return null;
    }

    $config = admin_configuracion_upload($tipo);
    $nombreOriginal = (string) ($archivo['name'] ?? '');
    $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));
    // Bloqueo explícito de ejecutables y formatos con riesgo (scripts,
    // HTML/SVG con JavaScript, binarios del servidor), además de la lista blanca.
    $extensionesBloqueadas = [
        'php', 'phtml', 'php3', 'php4', 'php5', 'phar', 'cgi', 'pl',
        'asp', 'aspx', 'jsp', 'js', 'html', 'htm', 'svg', 'swf',
        'exe', 'sh', 'py', 'htaccess',
    ];

    if (in_array($extension, $extensionesBloqueadas, true)
        || !in_array($extension, $config['extensiones'], true)
    ) {
        $errores[] = 'El tipo de archivo no está permitido. Solo: ' . implode(', ', $config['extensiones']) . '.';
        return null;
    }

    if (($archivo['size'] ?? 0) > $config['max']) {
        $errores[] = 'El archivo supera el tamaño máximo permitido de ' . ($config['max_legible'] ?? '5 MB') . '. No se guardó el archivo.';
        return null;
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = $finfo ? finfo_file($finfo, $archivo['tmp_name']) : '';

    if ($finfo) {
        finfo_close($finfo);
    }

    if (!in_array($mime, $config['mimes'], true)) {
        $errores[] = 'El contenido real del archivo no coincide con el tipo permitido.';
        return null;
    }

    admin_crear_directorio_seguro($config['directorio']);

    if (!is_writable($config['directorio'])) {
        $errores[] = 'El directorio de destino no tiene permisos de escritura.';
        return null;
    }

    $nombreSeguro = bin2hex(random_bytes(16)) . '.' . $extension;
    $destino = rtrim($config['directorio'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $nombreSeguro;

    if (!move_uploaded_file($archivo['tmp_name'], $destino)) {
        $errores[] = 'No se pudo guardar el archivo subido.';
        return null;
    }

    return $config['relativo'] . '/' . $nombreSeguro;
}
