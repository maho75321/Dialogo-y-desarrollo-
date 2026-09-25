<?php

require_once __DIR__ . '/admin-helpers.php';
require_once __DIR__ . '/upload.php';
require_once __DIR__ . '/editor.php';

function admin_agregar_campo_si_existe(PDO $conexion, $tabla, array &$campos, array $campo)
{
    if (admin_columna_existe($conexion, $tabla, $campo['name'])) {
        $campos[$campo['name']] = $campo;
    }
}

function admin_crud_config($tipo, PDO $conexion)
{
    switch ($tipo) {
        case 'noticias':
            $tabla = 'noticias';
            $campos = [];
            admin_agregar_campo_si_existe($conexion, $tabla, $campos, ['name' => 'titulo', 'label' => 'Título', 'type' => 'text', 'required' => true, 'list' => true]);
            admin_agregar_campo_si_existe($conexion, $tabla, $campos, ['name' => 'resumen', 'label' => 'Resumen', 'type' => 'textarea', 'list' => false]);
            admin_agregar_campo_si_existe($conexion, $tabla, $campos, ['name' => 'contenido', 'label' => 'Contenido', 'type' => 'richtext', 'list' => false]);
            admin_agregar_campo_si_existe($conexion, $tabla, $campos, ['name' => 'foto', 'label' => 'Imagen', 'type' => 'image', 'list' => true]);
            admin_agregar_campo_si_existe($conexion, $tabla, $campos, ['name' => 'link_externo', 'label' => 'Enlace externo', 'type' => 'url', 'list' => true]);
            admin_agregar_campo_si_existe($conexion, $tabla, $campos, ['name' => 'fecha_publicacion', 'label' => 'Fecha', 'type' => 'date', 'required' => true, 'list' => true]);
            admin_agregar_campo_si_existe($conexion, $tabla, $campos, ['name' => 'activo', 'label' => 'Publicado', 'type' => 'checkbox', 'list' => true]);
            admin_agregar_campo_si_existe($conexion, $tabla, $campos, ['name' => 'estado', 'label' => 'Estado', 'type' => 'estado', 'list' => true]);

            return [
                'tabla' => $tabla,
                'slug' => 'noticias',
                'titulo' => 'Noticias',
                'singular' => 'noticia',
                'seccion' => 'noticias',
                'orden' => 'fecha_publicacion DESC, id DESC',
                'campos' => $campos,
            ];

        case 'boletines':
            $tabla = 'boletines';
            $campos = [];
            admin_agregar_campo_si_existe($conexion, $tabla, $campos, ['name' => 'numero_boletin', 'label' => 'Número', 'type' => 'text', 'required' => true, 'list' => true]);
            admin_agregar_campo_si_existe($conexion, $tabla, $campos, ['name' => 'anio', 'label' => 'Año', 'type' => 'number', 'list' => true]);
            admin_agregar_campo_si_existe($conexion, $tabla, $campos, ['name' => 'resumen', 'label' => 'Resumen', 'type' => 'textarea', 'list' => true]);
            admin_agregar_campo_si_existe($conexion, $tabla, $campos, ['name' => 'foto_portada', 'label' => 'Portada', 'type' => 'image', 'list' => true]);
            admin_agregar_campo_si_existe($conexion, $tabla, $campos, ['name' => 'archivo_pdf', 'label' => 'PDF', 'type' => 'pdf', 'required_on_create' => true, 'list' => true]);
            admin_agregar_campo_si_existe($conexion, $tabla, $campos, ['name' => 'fecha_publicacion', 'label' => 'Fecha', 'type' => 'date', 'required' => true, 'list' => true]);

            return [
                'tabla' => $tabla,
                'slug' => 'boletines',
                'titulo' => 'Boletines NTEP',
                'singular' => 'boletín',
                'seccion' => 'boletines',
                'orden' => 'fecha_publicacion DESC, id DESC',
                'campos' => $campos,
            ];

        case 'podcasts':
            $tabla = 'podcasts';
            $campos = [];
            admin_agregar_campo_si_existe($conexion, $tabla, $campos, ['name' => 'titulo', 'label' => 'Título', 'type' => 'text', 'required' => true, 'list' => true]);
            admin_agregar_campo_si_existe($conexion, $tabla, $campos, ['name' => 'descripcion', 'label' => 'Descripción', 'type' => 'textarea', 'list' => false]);
            admin_agregar_campo_si_existe($conexion, $tabla, $campos, ['name' => 'archivo_audio', 'label' => 'Audio MP3/M4A (solo audio, sin video)', 'type' => 'audio', 'list' => true]);
            admin_agregar_campo_si_existe($conexion, $tabla, $campos, ['name' => 'url_embed', 'label' => 'Enlace de YouTube, YouTube Music o Spotify (opcional si subes audio)', 'type' => 'url', 'normalize' => 'media', 'list' => true]);
            admin_agregar_campo_si_existe($conexion, $tabla, $campos, ['name' => 'estado', 'label' => 'Estado', 'type' => 'estado', 'list' => true]);
            admin_agregar_campo_si_existe($conexion, $tabla, $campos, ['name' => 'imagen', 'label' => 'Imagen', 'type' => 'image', 'list' => true]);
            admin_agregar_campo_si_existe($conexion, $tabla, $campos, ['name' => 'foto', 'label' => 'Imagen', 'type' => 'image', 'list' => true]);
            admin_agregar_campo_si_existe($conexion, $tabla, $campos, ['name' => 'fecha_publicacion', 'label' => 'Fecha', 'type' => 'date', 'required' => true, 'list' => true]);

            return [
                'tabla' => $tabla,
                'slug' => 'podcasts',
                'titulo' => 'Podcasts',
                'singular' => 'podcast',
                'seccion' => 'podcasts',
                'orden' => 'fecha_publicacion DESC, id DESC',
                'campos' => $campos,
            ];

        case 'videos':
            $tabla = 'videos';
            $campos = [];
            admin_agregar_campo_si_existe($conexion, $tabla, $campos, ['name' => 'titulo', 'label' => 'Título', 'type' => 'text', 'required' => true, 'list' => true]);
            admin_agregar_campo_si_existe($conexion, $tabla, $campos, ['name' => 'descripcion', 'label' => 'Descripción', 'type' => 'textarea', 'list' => false]);
            admin_agregar_campo_si_existe($conexion, $tabla, $campos, ['name' => 'url_embed', 'label' => 'Enlace de YouTube o embed', 'type' => 'url', 'required' => true, 'normalize' => 'youtube', 'list' => true]);
            admin_agregar_campo_si_existe($conexion, $tabla, $campos, ['name' => 'estado', 'label' => 'Estado', 'type' => 'estado', 'list' => true]);
            admin_agregar_campo_si_existe($conexion, $tabla, $campos, ['name' => 'imagen', 'label' => 'Imagen', 'type' => 'image', 'list' => true]);
            admin_agregar_campo_si_existe($conexion, $tabla, $campos, ['name' => 'foto', 'label' => 'Imagen', 'type' => 'image', 'list' => true]);
            admin_agregar_campo_si_existe($conexion, $tabla, $campos, ['name' => 'fecha_publicacion', 'label' => 'Fecha', 'type' => 'date', 'required' => true, 'list' => true]);

            return [
                'tabla' => $tabla,
                'slug' => 'videos',
                'titulo' => 'Videos',
                'singular' => 'video',
                'seccion' => 'videos',
                'orden' => 'fecha_publicacion DESC, id DESC',
                'campos' => $campos,
            ];

        case 'autores':
            $tabla = 'autores';
            $campos = [];
            admin_agregar_campo_si_existe($conexion, $tabla, $campos, ['name' => 'nombres', 'label' => 'Nombres', 'type' => 'text', 'required' => true, 'list' => true]);
            admin_agregar_campo_si_existe($conexion, $tabla, $campos, ['name' => 'ap_paterno', 'label' => 'Apellido paterno', 'type' => 'text', 'list' => true]);
            admin_agregar_campo_si_existe($conexion, $tabla, $campos, ['name' => 'ap_materno', 'label' => 'Apellido materno', 'type' => 'text', 'list' => false]);
            admin_agregar_campo_si_existe($conexion, $tabla, $campos, ['name' => 'nickname', 'label' => 'Nickname', 'type' => 'text', 'list' => true]);
            admin_agregar_campo_si_existe($conexion, $tabla, $campos, ['name' => 'es_nickname', 'label' => 'Usar nickname', 'type' => 'checkbox', 'list' => true]);
            admin_agregar_campo_si_existe($conexion, $tabla, $campos, ['name' => 'biografia', 'label' => 'Biografía', 'type' => 'textarea', 'list' => false]);
            admin_agregar_campo_si_existe($conexion, $tabla, $campos, ['name' => 'foto', 'label' => 'Fotografía', 'type' => 'image', 'list' => true]);

            return [
                'tabla' => $tabla,
                'slug' => 'autores',
                'titulo' => 'Autores',
                'singular' => 'autor',
                'seccion' => 'autores',
                'orden' => 'nombres ASC, ap_paterno ASC, id DESC',
                'campos' => $campos,
            ];
    }

    throw new InvalidArgumentException('Módulo no soportado.');
}

function admin_crud_titulo_registro(array $registro)
{
    if (isset($registro['titulo'])) {
        return $registro['titulo'];
    }

    if (isset($registro['numero_boletin'])) {
        return 'Nº ' . $registro['numero_boletin'];
    }

    if (isset($registro['nombres'])) {
        return admin_nombre_autor_fila($registro);
    }

    return 'Registro #' . ($registro['id'] ?? '');
}

function admin_crud_render_valor(array $campo, array $registro)
{
    $nombre = $campo['name'];
    $valor = $registro[$nombre] ?? null;

    if ($valor === null || $valor === '') {
        return '<span class="admin-muted">Sin dato</span>';
    }

    switch ($campo['type']) {
        case 'image':
            return '<img class="admin-thumb" src="' . e(admin_media_url($valor)) . '" alt="">';

        case 'pdf':
            return '<a href="' . e(admin_media_url($valor)) . '" target="_blank" rel="noopener noreferrer">Ver PDF</a>';

        case 'audio':
            return '<audio controls preload="none" src="' . e(admin_media_url($valor)) . '" style="max-width:220px"></audio>';

        case 'url':
            return '<a href="' . e($valor) . '" target="_blank" rel="noopener noreferrer">' . e(admin_recortar($valor, 42)) . '</a>';

        case 'date':
            return e(formatearFecha($valor));

        case 'checkbox':
            return ((int) $valor === 1) ? 'Sí' : 'No';

        case 'estado':
            return ((string) $valor === 'borrador')
                ? '<span class="admin-badge-borrador">BORRADOR</span>'
                : 'Publicado';

        case 'textarea':
            return e(admin_recortar($valor, 80));
    }

    return e(admin_recortar($valor, 80));
}

function admin_crud_index(PDO $conexion, array $config)
{
    admin_requerir_modulo($config['seccion']);

    $registros = admin_listar(
        $conexion,
        $config['tabla'],
        $config['orden'],
        in_array($config['tabla'], ['noticias', 'reportajes'], true)
    );
    $camposListables = array_filter($config['campos'], function ($campo) {
        return !empty($campo['list']);
    });

    $admin_titulo = $config['titulo'];
    $admin_seccion = $config['seccion'];
    require __DIR__ . '/admin-header.php';
    ?>
    <section class="admin-page-head">
        <div>
            <p class="admin-kicker">Gestión</p>
            <h1><?php echo e($config['titulo']); ?></h1>
        </div>
        <a class="admin-btn admin-btn-primary" href="<?php echo e(admin_url($config['slug'] . '/crear.php')); ?>">
            <span class="fa fa-plus" aria-hidden="true"></span>
            Crear <?php echo e($config['singular']); ?>
        </a>
    </section>

    <?php admin_render_mensajes(); ?>

    <section class="admin-card">
        <?php if ($registros): ?>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <?php foreach ($camposListables as $campo): ?>
                                <th><?php echo e($campo['label']); ?></th>
                            <?php endforeach; ?>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($registros as $registro): ?>
                            <tr>
                                <?php foreach ($camposListables as $campo): ?>
                                    <td><?php echo admin_crud_render_valor($campo, $registro); ?></td>
                                <?php endforeach; ?>
                                <td class="admin-actions">
                                    <?php if (admin_puede_gestionar_registro($config['tabla'], $registro)): ?>
                                        <a href="<?php echo e(admin_url($config['slug'] . '/editar.php?id=' . (int) $registro['id'])); ?>">Editar</a>
                                        <a class="admin-danger-link" href="<?php echo e(admin_url($config['slug'] . '/eliminar.php?id=' . (int) $registro['id'])); ?>">Eliminar</a>
                                    <?php else: ?>
                                        <span class="admin-muted">Sin permiso</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="admin-empty">No hay registros todavía.</p>
        <?php endif; ?>
    </section>
    <?php
    require __DIR__ . '/admin-footer.php';
}

function admin_crud_faltantes_publicar(array $config, array $registroFinal)
{
    $faltan = [];

    foreach ($config['campos'] as $nombre => $campo) {
        if (($campo['type'] ?? '') === 'estado') {
            continue;
        }

        if (!empty($campo['required']) || !empty($campo['required_on_create'])) {
            $valor = $registroFinal[$nombre] ?? null;

            if ($valor === null || trim((string) $valor) === '') {
                $faltan[] = $campo['label'];
            }
        }
    }

    return $faltan;
}

function admin_crud_extraer_datos(PDO $conexion, array $config, $editando, array $registro, array &$errores, $modo = 'publicar')
{
    $datos = [];
    // "Guardar borrador" no exige los campos obligatorios; "Publicar" sí.
    $esBorrador = ($modo === 'borrador');

    foreach ($config['campos'] as $nombre => $campo) {
        $tipo = $campo['type'];

        if ($tipo === 'estado') {
            continue;
        }

        if ($tipo === 'image' || $tipo === 'pdf' || $tipo === 'audio') {
            $archivoTipo = $tipo === 'pdf' ? 'pdf' : ($tipo === 'audio' ? 'audio' : 'imagen');
            $actual = $editando ? ($registro[$nombre] ?? null) : null;
            $quiereEliminar = isset($_POST[$nombre . '_eliminar']) && (string) $_POST[$nombre . '_eliminar'] === '1';
            $elegidaRaw = trim((string) ($_POST[$nombre . '_existente'] ?? ''));
            // La galería solo aplica a imágenes; PDF/audio solo suben o se borran.
            $rutaElegida = ($tipo === 'image' && $elegidaRaw !== '')
                ? admin_biblioteca_validar($elegidaRaw, $archivoTipo)
                : '';
            $requerido = !$editando && !empty($campo['required_on_create']) && $rutaElegida === '';
            $ruta = admin_guardar_archivo_subido($_FILES[$nombre] ?? null, $archivoTipo, $errores, $requerido);

            if ($ruta !== null) {
                if (!empty($actual) && $actual !== $ruta) {
                    admin_eliminar_fisico_si_subido($actual);
                }
                $datos[$nombre] = $ruta;
            } elseif ($rutaElegida !== '') {
                $datos[$nombre] = $rutaElegida;
            } elseif ($quiereEliminar) {
                if (!empty($actual)) {
                    admin_eliminar_fisico_si_subido($actual);
                }
                $datos[$nombre] = null;
            }

            continue;
        }

        if ($tipo === 'checkbox') {
            $datos[$nombre] = isset($_POST[$nombre]) ? 1 : 0;
            continue;
        }

        if ($tipo === 'richtext') {
            // Contenido enriquecido: se sanitiza en el servidor antes de guardar.
            $sanitizado = sanitizar_html((string) ($_POST[$nombre] ?? ''));
            $datos[$nombre] = $sanitizado === '' ? null : $sanitizado;
            continue;
        }

        $valor = trim((string) ($_POST[$nombre] ?? ''));

        if (!$esBorrador && !empty($campo['required']) && $valor === '') {
            $errores[] = 'El campo "' . $campo['label'] . '" es obligatorio.';
            continue;
        }

        if ($valor !== '' && $tipo === 'date' && !$esBorrador && !admin_fecha_valida($valor)) {
            $errores[] = 'La fecha de "' . $campo['label'] . '" no es válida.';
            continue;
        }

        if ($esBorrador && $tipo === 'date' && $valor === '') {
            // Referencia interna para el borrador (no visible al público).
            $valor = date('Y-m-d');
        }

        if ($valor !== '' && $tipo === 'url' && !$esBorrador && !admin_url_valida($valor)) {
            $errores[] = 'El campo "' . $campo['label'] . '" debe ser una URL válida (incluye https://).';
            continue;
        }

        $normalizar = $campo['normalize'] ?? '';

        if ($valor !== '' && $normalizar === 'youtube' && !$esBorrador) {
            $normalizado = admin_normalizar_youtube_embed($valor);

            if ($normalizado === '') {
                $errores[] = 'El campo "' . $campo['label'] . '" debe ser un enlace válido de YouTube (watch, youtu.be, music.youtube.com, shorts o embed).';
                continue;
            }

            $valor = $normalizado;
        }

        if ($valor !== '' && $normalizar === 'media' && !$esBorrador) {
            $normalizado = admin_normalizar_media_embed($valor);

            if ($normalizado === '') {
                $errores[] = 'El campo "' . $campo['label'] . '" debe ser un enlace válido de YouTube, YouTube Music o Spotify.';
                continue;
            }

            $valor = $normalizado;
        }

        if ($tipo === 'number') {
            $datos[$nombre] = $valor === '' ? null : (int) $valor;
        } else {
            // En borrador se guarda '' en vez de NULL para no violar
            // columnas NOT NULL (título, etc.); al publicar se valida todo.
            $datos[$nombre] = ($valor === '' && !$esBorrador) ? null : $valor;
        }
    }

    if (array_key_exists('usuario_id', $_POST)) {
        unset($datos['usuario_id']);
    }

    if ($editando) {
        admin_bloquear_cambio_usuario_id($datos);
    } else {
        admin_forzar_usuario_id_creacion($conexion, $config['tabla'], $datos);
    }

    if (admin_columna_existe($conexion, $config['tabla'], 'estado')) {
        $datos['estado'] = $esBorrador ? 'borrador' : 'publicado';
    }

    return $datos;
}

function admin_crud_render_control(array $campo, array $registro, $editando)
{
    $nombre = $campo['name'];
    $tipo = $campo['type'];

    if ($tipo === 'richtext') {
        echo admin_editor_enriquecido($nombre, admin_campo_valor($registro, $nombre), $campo['label']);

        return;
    }

    $valor = admin_campo_valor($registro, $nombre);
    $id = 'campo_' . $nombre;
    $required = !empty($campo['required']) || (!$editando && !empty($campo['required_on_create']));
    ?>
    <label class="admin-field" for="<?php echo e($id); ?>">
        <span><?php echo e($campo['label']); ?><?php echo $required ? ' *' : ''; ?></span>
        <?php if ($tipo === 'textarea'): ?>
            <textarea id="<?php echo e($id); ?>" name="<?php echo e($nombre); ?>" rows="6" <?php echo $required ? 'required' : ''; ?>><?php echo e($valor); ?></textarea>
        <?php elseif ($tipo === 'date'): ?>
            <input id="<?php echo e($id); ?>" type="date" name="<?php echo e($nombre); ?>" value="<?php echo e($valor); ?>" <?php echo $required ? 'required' : ''; ?>>
        <?php elseif ($tipo === 'url'): ?>
            <input id="<?php echo e($id); ?>" type="url" name="<?php echo e($nombre); ?>" value="<?php echo e($valor); ?>" <?php echo $required ? 'required' : ''; ?>>
        <?php elseif ($tipo === 'number'): ?>
            <input id="<?php echo e($id); ?>" type="number" name="<?php echo e($nombre); ?>" value="<?php echo e($valor); ?>">
        <?php elseif ($tipo === 'checkbox'): ?>
            <span class="admin-checkline">
                <input id="<?php echo e($id); ?>" type="checkbox" name="<?php echo e($nombre); ?>" value="1" <?php echo ($_SERVER['REQUEST_METHOD'] === 'POST' ? isset($_POST[$nombre]) : (int) $valor === 1) ? 'checked' : ''; ?>>
                Activo
            </span>
        <?php elseif ($tipo === 'estado'): ?>
            <?php $estadoActual = $_SERVER['REQUEST_METHOD'] === 'POST' ? ($_POST['estado'] ?? $valor) : ($valor ?: 'borrador'); ?>
            <select id="<?php echo e($id); ?>" name="estado">
                <option value="borrador" <?php echo $estadoActual === 'borrador' ? 'selected' : ''; ?>>Borrador (no visible al público)</option>
                <option value="publicado" <?php echo $estadoActual === 'publicado' ? 'selected' : ''; ?>>Publicado</option>
            </select>
        <?php elseif ($tipo === 'image' || $tipo === 'pdf' || $tipo === 'audio'): ?>
            <?php
            $biblioteca = $tipo === 'image' ? admin_biblioteca_imagenes() : [];
            $existenteSel = (string) ($_POST[$nombre . '_existente'] ?? '');
            ?>
            <?php if (!empty($registro[$nombre])): ?>
                <span class="admin-current-file">
                    <?php if ($tipo === 'image'): ?>
                        <img class="admin-thumb" src="<?php echo e(admin_media_url($registro[$nombre])); ?>" alt="">
                        <small class="admin-muted"><?php echo e($registro[$nombre]); ?></small>
                    <?php elseif ($tipo === 'audio'): ?>
                        <audio controls preload="none" src="<?php echo e(admin_media_url($registro[$nombre])); ?>" style="max-width:260px"></audio>
                    <?php else: ?>
                        <a href="<?php echo e(admin_media_url($registro[$nombre])); ?>" target="_blank" rel="noopener noreferrer">Archivo actual</a>
                    <?php endif; ?>
                </span>
                <span class="admin-checkline">
                    <input type="checkbox" id="<?php echo e($id); ?>_eliminar" name="<?php echo e($nombre); ?>_eliminar" value="1" <?php echo isset($_POST[$nombre . '_eliminar']) ? 'checked' : ''; ?>>
                    <label for="<?php echo e($id); ?>_eliminar">Borrar / quitar <?php echo $tipo === 'image' ? 'esta foto' : 'este archivo'; ?></label>
                </span>
            <?php endif; ?>
            <?php if ($tipo === 'image'): ?>
                <span><small class="admin-muted">Elegir una foto que ya está en el servidor (assets/img o uploads):</small></span>
                <select name="<?php echo e($nombre); ?>_existente">
                    <option value="">— Conservar / subir nueva —</option>
                    <?php foreach ($biblioteca as $rutaBiblio): ?>
                        <option value="<?php echo e($rutaBiblio); ?>" <?php echo ($existenteSel !== '' ? $existenteSel : $valor) === $rutaBiblio ? 'selected' : ''; ?>><?php echo e($rutaBiblio); ?></option>
                    <?php endforeach; ?>
                </select>
                <span><small class="admin-muted">O subir otra desde tu PC:</small></span>
            <?php endif; ?>
            <input id="<?php echo e($id); ?>" type="file" name="<?php echo e($nombre); ?>" accept="<?php echo $tipo === 'pdf' ? 'application/pdf' : ($tipo === 'audio' ? 'audio/*,.mp3,.m4a,.mp4,.ogg,.oga,.wav' : 'image/jpeg,image/png,image/webp'); ?>" <?php echo $required ? 'required' : ''; ?>>
            <?php if ($tipo === 'audio'): ?>
                <small class="admin-muted">MP3, M4A, MP4 (solo audio), OGG u WAV. Máx. 25 MB. Este audio se reproduce sin video.</small>
            <?php endif; ?>
        <?php else: ?>
            <input id="<?php echo e($id); ?>" type="text" name="<?php echo e($nombre); ?>" value="<?php echo e($valor); ?>" <?php echo $required ? 'required' : ''; ?>>
        <?php endif; ?>
    </label>
    <?php
}

function admin_crud_formulario(PDO $conexion, array $config, $id = null)
{
    admin_requerir_modulo($config['seccion']);

    $editando = $id !== null;
    $registro = $editando ? admin_obtener_por_id($conexion, $config['tabla'], $id) : [];
    $errores = [];

    if ($editando && !$registro) {
        admin_flash('error', 'El registro solicitado no existe.');
        admin_redirect(admin_url($config['slug'] . '/index.php'));
    }

    if ($editando) {
        admin_requerir_propiedad($config['tabla'], $registro);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            admin_validar_csrf();
            // Borradores: este módulo gestiona estado solo si la tabla tiene la columna.
            $gestionaEstado = isset($config['campos']['estado'])
                && admin_columna_existe($conexion, $config['tabla'], 'estado');
            $modo = ($gestionaEstado && (string) ($_POST['accion'] ?? '') === 'borrador')
                ? 'borrador'
                : 'publicar';
            $datos = admin_crud_extraer_datos($conexion, $config, $editando, $registro, $errores, $modo);

            if (!$errores && $modo === 'publicar' && $gestionaEstado) {
                $final = $editando ? array_merge($registro, $datos) : $datos;
                $faltan = admin_crud_faltantes_publicar($config, $final);

                if ($faltan) {
                    $errores[] = 'No se puede publicar. Falta: ' . implode(', ', $faltan) . '.';
                }

                // Podcasts: se publica solo-audio. Exige archivo de audio o enlace.
                if ($config['tabla'] === 'podcasts') {
                    $audioFinal = trim((string) ($final['archivo_audio'] ?? ''));
                    $urlFinal = trim((string) ($final['url_embed'] ?? ''));

                    if ($audioFinal === '' && $urlFinal === '') {
                        $errores[] = 'No se puede publicar. Falta: Audio MP3/M4A o enlace de YouTube Music / Spotify.';
                    }
                }
            }

            if (!$errores) {
                if ($editando) {
                    admin_actualizar($conexion, $config['tabla'], $datos, $id);
                    admin_flash('ok', $modo === 'borrador' ? 'Borrador guardado correctamente.' : 'Registro actualizado correctamente.');
                } else {
                    admin_insertar($conexion, $config['tabla'], $datos);
                    admin_flash('ok', $modo === 'borrador' ? 'Borrador creado correctamente.' : 'Registro creado correctamente.');
                }

                admin_redirect(admin_url($config['slug'] . '/index.php'));
            }
        } catch (Throwable $error) {
            $errores[] = 'No se pudo guardar el registro. Revisa los datos e inténtalo nuevamente.';
        }
    }

    $gestionaEstado = isset($config['campos']['estado']);

    $admin_titulo = ($editando ? 'Editar ' : 'Crear ') . $config['singular'];
    $admin_seccion = $config['seccion'];
    $tieneUploads = (bool) array_filter($config['campos'], function ($campo) {
        return in_array($campo['type'], ['image', 'pdf', 'audio'], true);
    });

    require __DIR__ . '/admin-header.php';
    ?>
    <section class="admin-page-head">
        <div>
            <p class="admin-kicker"><?php echo $editando ? 'Edición' : 'Nuevo registro'; ?></p>
            <h1><?php echo e($admin_titulo); ?></h1>
        </div>
        <a class="admin-btn" href="<?php echo e(admin_url($config['slug'] . '/index.php')); ?>">Volver</a>
    </section>

    <?php admin_render_mensajes($errores); ?>

    <form class="admin-card admin-form" method="post" <?php echo $tieneUploads ? 'enctype="multipart/form-data"' : ''; ?>>
        <?php echo admin_csrf_campo(); ?>
        <div class="admin-form-grid">
            <?php foreach ($config['campos'] as $campo): ?>
                <?php admin_crud_render_control($campo, $registro, $editando); ?>
            <?php endforeach; ?>
        </div>
        <div class="admin-form-actions">
            <?php if ($gestionaEstado): ?>
                <button class="admin-btn" type="submit" name="accion" value="borrador" formnovalidate>Guardar borrador</button>
                <button class="admin-btn admin-btn-primary" type="submit" name="accion" value="publicar">Publicar</button>
            <?php else: ?>
                <button class="admin-btn admin-btn-primary" type="submit">Guardar</button>
            <?php endif; ?>
            <a class="admin-btn" href="<?php echo e(admin_url($config['slug'] . '/index.php')); ?>">Cancelar</a>
        </div>
    </form>
    <?php echo admin_editor_script(); ?>
    <?php
    require __DIR__ . '/admin-footer.php';
}

function admin_crud_eliminar(PDO $conexion, array $config)
{
    admin_requerir_modulo($config['seccion']);

    $id = admin_id_parametro();

    if (!$id) {
        admin_flash('error', 'Identificador no válido.');
        admin_redirect(admin_url($config['slug'] . '/index.php'));
    }

    $registro = admin_obtener_por_id($conexion, $config['tabla'], $id);
    $errores = [];

    if (!$registro) {
        admin_flash('error', 'El registro solicitado no existe.');
        admin_redirect(admin_url($config['slug'] . '/index.php'));
    }

    admin_requerir_propiedad($config['tabla'], $registro);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            admin_validar_csrf();

            if ($config['tabla'] === 'autores') {
                $stmt = $conexion->prepare('SELECT COUNT(*) FROM reportajes WHERE autor_id = :id');
                $stmt->execute(['id' => $id]);

                if ((int) $stmt->fetchColumn() > 0) {
                    $errores[] = 'No se puede eliminar este autor porque tiene reportajes relacionados.';
                }
            }

            if (!$errores) {
                admin_eliminar_por_id($conexion, $config['tabla'], $id);
                admin_flash('ok', 'Registro eliminado correctamente.');
                admin_redirect(admin_url($config['slug'] . '/index.php'));
            }
        } catch (Throwable $error) {
            $errores[] = 'No se pudo eliminar el registro.';
        }
    }

    $admin_titulo = 'Eliminar ' . $config['singular'];
    $admin_seccion = $config['seccion'];
    require __DIR__ . '/admin-header.php';
    ?>
    <section class="admin-page-head">
        <div>
            <p class="admin-kicker">Confirmación</p>
            <h1>Eliminar <?php echo e($config['singular']); ?></h1>
        </div>
        <a class="admin-btn" href="<?php echo e(admin_url($config['slug'] . '/index.php')); ?>">Volver</a>
    </section>

    <?php admin_render_mensajes($errores); ?>

    <form class="admin-card admin-delete-box" method="post">
        <?php echo admin_csrf_campo(); ?>
        <p>Vas a eliminar <strong><?php echo e(admin_crud_titulo_registro($registro)); ?></strong>.</p>
        <p class="admin-muted">Esta acción no se puede deshacer desde el panel.</p>
        <div class="admin-form-actions">
            <button class="admin-btn admin-btn-danger" type="submit" onclick="return confirm('¿Confirmas que deseas eliminar este registro?');">Eliminar</button>
            <a class="admin-btn" href="<?php echo e(admin_url($config['slug'] . '/index.php')); ?>">Cancelar</a>
        </div>
    </form>
    <?php
    require __DIR__ . '/admin-footer.php';
}

function admin_reportajes_index(PDO $conexion)
{
    admin_requerir_modulo('reportajes');

    $sql = consultaReportajeBase();
    $params = [];

    if (admin_es_autor()) {
        $sql .= ' WHERE r.usuario_id = :usuario_id';
        $params['usuario_id'] = admin_usuario_id_actual();
    }

    $sql .= ' ORDER BY r.fecha_publicacion DESC, r.id DESC';
    $stmt = $conexion->prepare($sql);
    $stmt->execute($params);
    $reportajes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $admin_titulo = 'Reportajes';
    $admin_seccion = 'reportajes';
    require __DIR__ . '/admin-header.php';
    ?>
    <section class="admin-page-head">
        <div>
            <p class="admin-kicker">Gestión</p>
            <h1>Reportajes</h1>
        </div>
        <a class="admin-btn admin-btn-primary" href="<?php echo e(admin_url('reportajes/crear.php')); ?>">
            <span class="fa fa-plus" aria-hidden="true"></span>
            Crear reportaje
        </a>
    </section>

    <?php admin_render_mensajes(); ?>

    <section class="admin-card">
        <?php if ($reportajes): ?>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>Título</th>
                            <th>Autor</th>
                            <th>Fecha</th>
                            <th>Destacado</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reportajes as $reportaje): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($reportaje['foto_principal'])): ?>
                                        <img class="admin-thumb" src="<?php echo e(admin_media_url($reportaje['foto_principal'])); ?>" alt="">
                                    <?php else: ?>
                                        <span class="admin-muted">Sin imagen</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($reportaje['titulo']); ?></td>
                                <td><?php echo e(mostrarAutor($reportaje)); ?></td>
                                <td><?php echo e(formatearFecha($reportaje['fecha_publicacion'])); ?></td>
                                <td><?php echo (int) $reportaje['es_destacado'] === 1 ? '<span class="admin-badge-destacado">DESTACADO</span>' : 'No'; ?></td>
                                <td><?php echo (string) ($reportaje['estado'] ?? 'publicado') === 'borrador' ? '<span class="admin-badge-borrador">BORRADOR</span>' : 'Publicado'; ?></td>
                                <td class="admin-actions">
                                    <?php if (admin_puede_gestionar_registro('reportajes', $reportaje)): ?>
                                        <a href="<?php echo e(admin_url('reportajes/editar.php?id=' . (int) $reportaje['id'])); ?>">Editar</a>
                                        <a class="admin-danger-link" href="<?php echo e(admin_url('reportajes/eliminar.php?id=' . (int) $reportaje['id'])); ?>">Eliminar</a>
                                        <?php if (admin_es_admin()): ?>
                                            <form class="admin-inline-form" method="post" action="<?php echo e(admin_url('reportajes/destacar.php')); ?>">
                                                <?php echo admin_csrf_campo(); ?>
                                                <input type="hidden" name="id" value="<?php echo (int) $reportaje['id']; ?>">
                                                <?php if ((int) $reportaje['es_destacado'] === 1): ?>
                                                    <input type="hidden" name="accion" value="quitar">
                                                    <button type="submit">Quitar destacado</button>
                                                <?php else: ?>
                                                    <input type="hidden" name="accion" value="marcar">
                                                    <button type="submit">Marcar como destacado</button>
                                                <?php endif; ?>
                                            </form>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="admin-muted">Sin permiso</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="admin-empty">No hay reportajes todavía.</p>
        <?php endif; ?>
    </section>
    <?php
    require __DIR__ . '/admin-footer.php';
}

function admin_reportaje_obtener(PDO $conexion, $id)
{
    $stmt = $conexion->prepare(consultaReportajeBase() . ' WHERE r.id = :id LIMIT 1');
    $stmt->execute(['id' => $id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function admin_reportaje_fotos(PDO $conexion, $id)
{
    $stmt = $conexion->prepare('SELECT * FROM reportajes_fotos WHERE reportaje_id = :id ORDER BY orden ASC, id ASC');
    $stmt->execute(['id' => $id]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function admin_reportaje_autores(PDO $conexion)
{
    return $conexion->query('SELECT * FROM autores ORDER BY nombres ASC, ap_paterno ASC, id ASC')->fetchAll(PDO::FETCH_ASSOC);
}

function admin_reportaje_formulario(PDO $conexion, $id = null)
{
    admin_requerir_modulo('reportajes');

    $editando = $id !== null;
    $reportaje = $editando ? admin_reportaje_obtener($conexion, $id) : [];
    $fotosActuales = $editando ? admin_reportaje_fotos($conexion, $id) : [];
    $autores = admin_reportaje_autores($conexion);
    $errores = [];
    $tienePdf = admin_columna_existe($conexion, 'reportajes', 'pdf_adjunto');
    $gestionaEstado = admin_columna_existe($conexion, 'reportajes', 'estado');

    if ($editando && !$reportaje) {
        admin_flash('error', 'El reportaje solicitado no existe.');
        admin_redirect(admin_url('reportajes/index.php'));
    }

    if ($editando) {
        admin_requerir_propiedad('reportajes', $reportaje);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            admin_validar_csrf();

            $titulo = trim((string) ($_POST['titulo'] ?? ''));
            $resumen = trim((string) ($_POST['resumen_corto'] ?? ''));
            // Contenido enriquecido: se sanitiza en el servidor antes de guardar.
            $desarrollo = sanitizar_html((string) ($_POST['desarrollo'] ?? ''));
            $desarrolloVacio = trim(strip_tags($desarrollo)) === '';
            $fecha = trim((string) ($_POST['fecha_publicacion'] ?? ''));
            $autorId = trim((string) ($_POST['autor_id'] ?? ''));
            $esDestacado = isset($_POST['es_destacado']) ? 1 : 0;
            // Borrador: guarda avances sin exigir campos; Publicar sí los exige.
            $esBorrador = $gestionaEstado && (string) ($_POST['accion'] ?? '') === 'borrador';

            if (!admin_es_admin()) {
                $esDestacado = $editando ? (int) ($reportaje['es_destacado'] ?? 0) : 0;
            }

            if (!$esBorrador && $titulo === '') {
                $errores[] = 'El título es obligatorio.';
            }

            if (!$esBorrador && $desarrolloVacio) {
                $errores[] = 'El contenido completo es obligatorio.';
            }

            if (!$esBorrador && ($fecha === '' || !admin_fecha_valida($fecha))) {
                $errores[] = 'La fecha no es válida.';
            }

            if ($esBorrador && $fecha !== '' && !admin_fecha_valida($fecha)) {
                $errores[] = 'La fecha no es válida.';
            }

            $autorIdFinal = null;
            if ($autorId !== '') {
                $autorIdFinal = filter_var($autorId, FILTER_VALIDATE_INT);

                if (!$autorIdFinal || $autorIdFinal <= 0) {
                    $errores[] = 'El autor seleccionado no es válido.';
                } else {
                    $stmtAutor = $conexion->prepare('SELECT COUNT(*) FROM autores WHERE id = :id');
                    $stmtAutor->execute(['id' => $autorIdFinal]);

                    if ((int) $stmtAutor->fetchColumn() === 0) {
                        $errores[] = 'El autor seleccionado no existe.';
                    }
                }
            }

            // Borrador nuevo sin fecha: se usa la fecha actual como referencia
            // interna (el borrador no es visible al público); al publicar se
            // exige igualmente que la fecha exista.
            $fechaFinal = $fecha;

            if ($fechaFinal === '' && $esBorrador && !$editando) {
                $fechaFinal = date('Y-m-d');
            } elseif ($fechaFinal === '' && $editando) {
                $fechaFinal = $reportaje['fecha_publicacion'] ?? null;
            }

            $datos = [
                'titulo' => $titulo,
                'resumen_corto' => $resumen === '' ? null : $resumen,
                'desarrollo' => $desarrollo,
                'fecha_publicacion' => $fechaFinal,
                'es_destacado' => $esDestacado,
                'autor_id' => $autorIdFinal,
            ];

            if ($gestionaEstado) {
                $datos['estado'] = $esBorrador ? 'borrador' : 'publicado';

                if (!$esBorrador) {
                    $final = $editando ? array_merge($reportaje, array_filter($datos, function ($v) {
                        return $v !== null;
                    })) : $datos;
                    $faltan = [];

                    if (trim((string) ($final['titulo'] ?? '')) === '') {
                        $faltan[] = 'Título';
                    }

                    if (trim(strip_tags((string) ($final['desarrollo'] ?? ''))) === '') {
                        $faltan[] = 'Contenido completo';
                    }

                    if (trim((string) ($final['fecha_publicacion'] ?? '')) === '') {
                        $faltan[] = 'Fecha';
                    }

                    if ($faltan) {
                        $errores[] = 'No se puede publicar. Falta: ' . implode(', ', $faltan) . '.';
                    }
                }
            }

            admin_bloquear_cambio_usuario_id($datos);

            $nuevasFotos = [];

            if (!$errores) {
                $fotoPrincipalActual = $editando ? ($reportaje['foto_principal'] ?? null) : null;
                $quiereBorrarPrincipal = isset($_POST['foto_principal_eliminar']) && (string) $_POST['foto_principal_eliminar'] === '1';
                $principalElegida = admin_biblioteca_validar(trim((string) ($_POST['foto_principal_existente'] ?? '')), 'imagen');
                $fotoPrincipal = admin_guardar_archivo_subido($_FILES['foto_principal'] ?? null, 'imagen', $errores, false);
                if ($fotoPrincipal !== null) {
                    if (!empty($fotoPrincipalActual) && $fotoPrincipalActual !== $fotoPrincipal) {
                        admin_eliminar_fisico_si_subido($fotoPrincipalActual);
                    }
                    $datos['foto_principal'] = $fotoPrincipal;
                } elseif ($principalElegida !== '') {
                    $datos['foto_principal'] = $principalElegida;
                } elseif ($quiereBorrarPrincipal) {
                    if (!empty($fotoPrincipalActual)) {
                        admin_eliminar_fisico_si_subido($fotoPrincipalActual);
                    }
                    $datos['foto_principal'] = null;
                }

                if ($tienePdf) {
                    $pdfActual = $editando ? ($reportaje['pdf_adjunto'] ?? null) : null;
                    $quiereBorrarPdf = isset($_POST['pdf_adjunto_eliminar']) && (string) $_POST['pdf_adjunto_eliminar'] === '1';
                    $pdf = admin_guardar_archivo_subido($_FILES['pdf_adjunto'] ?? null, 'pdf', $errores, false);
                    if ($pdf !== null) {
                        if (!empty($pdfActual) && $pdfActual !== $pdf) {
                            admin_eliminar_fisico_si_subido($pdfActual);
                        }
                        $datos['pdf_adjunto'] = $pdf;
                    } elseif ($quiereBorrarPdf) {
                        if (!empty($pdfActual)) {
                            admin_eliminar_fisico_si_subido($pdfActual);
                        }
                        $datos['pdf_adjunto'] = null;
                    }
                }

                $totalNuevas = isset($_FILES['fotos_nuevas']['name']) && is_array($_FILES['fotos_nuevas']['name'])
                    ? count($_FILES['fotos_nuevas']['name'])
                    : 0;

                for ($i = 0; $i < $totalNuevas; $i++) {
                    $archivo = admin_archivo_multiple('fotos_nuevas', $i);
                    $ruta = admin_guardar_archivo_subido($archivo, 'imagen', $errores, false);

                    if ($ruta !== null) {
                        $nuevasFotos[] = [
                            'url_foto' => $ruta,
                            'descripcion' => trim((string) ($_POST['descripciones_nuevas'][$i] ?? '')) ?: null,
                            'orden' => max(0, (int) ($_POST['ordenes_nuevas'][$i] ?? 0)),
                        ];
                    }
                }

                // Galería: agregar fotos que ya están en assets/img o uploads.
                $fotosBiblio = $_POST['fotos_biblioteca'] ?? [];
                if (is_array($fotosBiblio)) {
                    foreach ($fotosBiblio as $rutaBiblio) {
                        $rutaValida = admin_biblioteca_validar($rutaBiblio, 'imagen');

                        if ($rutaValida === '') {
                            continue;
                        }

                        $nuevasFotos[] = [
                            'url_foto' => $rutaValida,
                            'descripcion' => null,
                            'orden' => 0,
                        ];
                    }
                }
            }

            if (!$errores) {
                $conexion->beginTransaction();

                if ($editando) {
                    admin_bloquear_cambio_usuario_id($datos);
                    admin_actualizar($conexion, 'reportajes', $datos, $id);
                    $reportajeId = $id;
                } else {
                    admin_forzar_usuario_id_creacion($conexion, 'reportajes', $datos);
                    $reportajeId = admin_insertar($conexion, 'reportajes', $datos);
                }

                if ($esDestacado) {
                    $stmtDestacado = $conexion->prepare('UPDATE reportajes SET es_destacado = 0 WHERE id <> :id');
                    $stmtDestacado->execute(['id' => $reportajeId]);
                }

                if ($editando) {
                    $idsPermitidos = array_flip(array_map('intval', array_column($fotosActuales, 'id')));
                    $descripciones = $_POST['foto_descripcion'] ?? [];
                    $ordenes = $_POST['foto_orden'] ?? [];
                    $eliminar = $_POST['foto_eliminar'] ?? [];

                    foreach ($idsPermitidos as $fotoId => $valor) {
                        if (isset($eliminar[$fotoId])) {
                            $stmtRutaFoto = $conexion->prepare('SELECT url_foto FROM reportajes_fotos WHERE id = :id AND reportaje_id = :reportaje_id LIMIT 1');
                            $stmtRutaFoto->execute(['id' => $fotoId, 'reportaje_id' => $reportajeId]);
                            $rutaFotoBorrar = (string) ($stmtRutaFoto->fetchColumn() ?: '');
                            $stmtEliminarFoto = $conexion->prepare('DELETE FROM reportajes_fotos WHERE id = :id AND reportaje_id = :reportaje_id');
                            $stmtEliminarFoto->execute(['id' => $fotoId, 'reportaje_id' => $reportajeId]);
                            if ($rutaFotoBorrar !== '') {
                                admin_eliminar_fisico_si_subido($rutaFotoBorrar);
                            }
                            continue;
                        }

                        $stmtActualizarFoto = $conexion->prepare('
                            UPDATE reportajes_fotos
                            SET descripcion = :descripcion, orden = :orden
                            WHERE id = :id AND reportaje_id = :reportaje_id
                        ');
                        $stmtActualizarFoto->execute([
                            'descripcion' => trim((string) ($descripciones[$fotoId] ?? '')) ?: null,
                            'orden' => max(0, (int) ($ordenes[$fotoId] ?? 0)),
                            'id' => $fotoId,
                            'reportaje_id' => $reportajeId,
                        ]);
                    }
                }

                foreach ($nuevasFotos as $foto) {
                    $stmtNuevaFoto = $conexion->prepare('
                        INSERT INTO reportajes_fotos (reportaje_id, url_foto, descripcion, orden)
                        VALUES (:reportaje_id, :url_foto, :descripcion, :orden)
                    ');
                    $stmtNuevaFoto->execute([
                        'reportaje_id' => $reportajeId,
                        'url_foto' => $foto['url_foto'],
                        'descripcion' => $foto['descripcion'],
                        'orden' => $foto['orden'],
                    ]);
                }

                $conexion->commit();
                admin_flash('ok', $esBorrador ? 'Borrador guardado correctamente.' : ($editando ? 'Reportaje actualizado correctamente.' : 'Reportaje creado correctamente.'));
                admin_redirect(admin_url('reportajes/index.php'));
            }
        } catch (Throwable $error) {
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }

            $errores[] = 'No se pudo guardar el reportaje. Revisa los datos e inténtalo nuevamente.';
        }
    }

    $admin_titulo = $editando ? 'Editar reportaje' : 'Crear reportaje';
    $admin_seccion = 'reportajes';
    require __DIR__ . '/admin-header.php';
    ?>
    <section class="admin-page-head">
        <div>
            <p class="admin-kicker"><?php echo $editando ? 'Edición' : 'Nuevo registro'; ?></p>
            <h1><?php echo e($admin_titulo); ?></h1>
        </div>
        <a class="admin-btn" href="<?php echo e(admin_url('reportajes/index.php')); ?>">Volver</a>
    </section>

    <?php admin_render_mensajes($errores); ?>

    <form class="admin-card admin-form" method="post" enctype="multipart/form-data">
        <?php echo admin_csrf_campo(); ?>
        <div class="admin-form-grid">
            <label class="admin-field admin-field-wide" for="titulo">
                <span>Título *</span>
                <input id="titulo" type="text" name="titulo" value="<?php echo e(admin_campo_valor($reportaje, 'titulo')); ?>" required>
            </label>

            <label class="admin-field" for="fecha_publicacion">
                <span>Fecha *</span>
                <input id="fecha_publicacion" type="date" name="fecha_publicacion" value="<?php echo e(admin_campo_valor($reportaje, 'fecha_publicacion', date('Y-m-d'))); ?>" required>
            </label>

            <label class="admin-field" for="autor_id">
                <span>Autor</span>
                <select id="autor_id" name="autor_id">
                    <option value="">Redacción</option>
                    <?php foreach ($autores as $autor): ?>
                        <?php
                        $autorSeleccionado = (string) admin_campo_valor($reportaje, 'autor_id') === (string) $autor['id'];
                        ?>
                        <option value="<?php echo (int) $autor['id']; ?>" <?php echo $autorSeleccionado ? 'selected' : ''; ?>>
                            <?php echo e(admin_nombre_autor_fila($autor)); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <?php if (admin_es_admin()): ?>
            <label class="admin-field admin-check-field" for="es_destacado">
                <span>Destacado</span>
                <span class="admin-checkline">
                    <input id="es_destacado" type="checkbox" name="es_destacado" value="1" <?php echo ($_SERVER['REQUEST_METHOD'] === 'POST' ? isset($_POST['es_destacado']) : (int) ($reportaje['es_destacado'] ?? 0) === 1) ? 'checked' : ''; ?>>
                    Mostrar como reportaje destacado
                </span>
            </label>
            <?php endif; ?>

            <label class="admin-field admin-field-wide" for="resumen_corto">
                <span>Resumen</span>
                <textarea id="resumen_corto" name="resumen_corto" rows="3"><?php echo e(admin_campo_valor($reportaje, 'resumen_corto')); ?></textarea>
            </label>

            <?php echo admin_editor_enriquecido('desarrollo', admin_campo_valor($reportaje, 'desarrollo'), 'Contenido completo *'); ?>

            <div class="admin-field" id="campo_foto_principal">
                <span>Imagen principal</span>
                <?php
                $biblioPrincipal = admin_biblioteca_imagenes();
                $principalSel = (string) ($_POST['foto_principal_existente'] ?? '');
                ?>
                <?php if (!empty($reportaje['foto_principal'])): ?>
                    <span class="admin-current-file"><img class="admin-thumb" src="<?php echo e(admin_media_url($reportaje['foto_principal'])); ?>" alt=""><small class="admin-muted"><?php echo e($reportaje['foto_principal']); ?></small></span>
                    <span class="admin-checkline">
                        <input type="checkbox" id="foto_principal_eliminar" name="foto_principal_eliminar" value="1" <?php echo isset($_POST['foto_principal_eliminar']) ? 'checked' : ''; ?>>
                        <label for="foto_principal_eliminar">Borrar / quitar esta foto</label>
                    </span>
                <?php endif; ?>
                <span><small class="admin-muted">Elegir una foto que ya está en el servidor (assets/img, logo.jpg incluido, o uploads):</small></span>
                <select name="foto_principal_existente">
                    <option value="">— Conservar / subir nueva —</option>
                    <?php foreach ($biblioPrincipal as $rutaBiblio): ?>
                        <option value="<?php echo e($rutaBiblio); ?>" <?php echo ($principalSel !== '' ? $principalSel : ($reportaje['foto_principal'] ?? '')) === $rutaBiblio ? 'selected' : ''; ?>><?php echo e($rutaBiblio); ?></option>
                    <?php endforeach; ?>
                </select>
                <span><small class="admin-muted">O subir otra desde tu PC:</small></span>
                <input id="foto_principal" type="file" name="foto_principal" accept="image/jpeg,image/png,image/webp">
            </div>

            <?php if ($tienePdf): ?>
                <div class="admin-field">
                    <span>PDF adjunto</span>
                    <?php if (!empty($reportaje['pdf_adjunto'])): ?>
                        <span class="admin-current-file"><a href="<?php echo e(admin_media_url($reportaje['pdf_adjunto'])); ?>" target="_blank" rel="noopener noreferrer">Archivo actual</a></span>
                        <span class="admin-checkline">
                            <input type="checkbox" id="pdf_adjunto_eliminar" name="pdf_adjunto_eliminar" value="1" <?php echo isset($_POST['pdf_adjunto_eliminar']) ? 'checked' : ''; ?>>
                            <label for="pdf_adjunto_eliminar">Borrar / quitar este PDF</label>
                        </span>
                    <?php endif; ?>
                    <input id="pdf_adjunto" type="file" name="pdf_adjunto" accept="application/pdf">
                </div>
            <?php endif; ?>
        </div>

        <?php if ($editando && $fotosActuales): ?>
            <div class="admin-subsection">
                <h2>Fotografías actuales</h2>
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Foto</th>
                                <th>Descripción</th>
                                <th>Orden</th>
                                <th>Eliminar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($fotosActuales as $foto): ?>
                                <tr>
                                    <td><img class="admin-thumb" src="<?php echo e(admin_media_url($foto['url_foto'])); ?>" alt=""></td>
                                    <td><input type="text" name="foto_descripcion[<?php echo (int) $foto['id']; ?>]" value="<?php echo e($foto['descripcion']); ?>"></td>
                                    <td><input class="admin-small-input" type="number" min="0" name="foto_orden[<?php echo (int) $foto['id']; ?>]" value="<?php echo (int) $foto['orden']; ?>"></td>
                                    <td><input type="checkbox" name="foto_eliminar[<?php echo (int) $foto['id']; ?>]" value="1"></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <div class="admin-subsection">
            <h2>Agregar fotografías</h2>
            <p class="admin-muted">Sube desde tu PC o marca fotos que ya están en el servidor para agregarlas a la galería.</p>
            <div class="admin-photo-grid">
                <?php for ($i = 0; $i < 5; $i++): ?>
                    <div class="admin-photo-row">
                        <input type="file" name="fotos_nuevas[]" accept="image/jpeg,image/png,image/webp">
                        <input type="text" name="descripciones_nuevas[]" placeholder="Descripción">
                        <input type="number" min="0" name="ordenes_nuevas[]" placeholder="Orden">
                    </div>
                <?php endfor; ?>
            </div>
            <h3 style="margin-top:12px">Fotos del servidor (assets/img / uploads)</h3>
            <div class="admin-photo-grid">
                <?php foreach (admin_biblioteca_imagenes() as $rutaBiblio): ?>
                    <label class="admin-checkline" style="display:flex;gap:8px;align-items:center">
                        <input type="checkbox" name="fotos_biblioteca[]" value="<?php echo e($rutaBiblio); ?>">
                        <img class="admin-thumb" src="<?php echo e(admin_media_url($rutaBiblio)); ?>" alt="" style="width:60px;height:45px;object-fit:cover">
                        <small class="admin-muted"><?php echo e($rutaBiblio); ?></small>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="admin-form-actions">
            <?php if ($gestionaEstado): ?>
                <button class="admin-btn" type="submit" name="accion" value="borrador" formnovalidate>Guardar borrador</button>
                <button class="admin-btn admin-btn-primary" type="submit" name="accion" value="publicar">Publicar</button>
            <?php else: ?>
                <button class="admin-btn admin-btn-primary" type="submit">Guardar</button>
            <?php endif; ?>
            <a class="admin-btn" href="<?php echo e(admin_url('reportajes/index.php')); ?>">Cancelar</a>
        </div>
    </form>
    <?php echo admin_editor_script(); ?>
    <?php
    require __DIR__ . '/admin-footer.php';
}

function admin_reportaje_eliminar(PDO $conexion)
{
    admin_requerir_modulo('reportajes');

    $id = admin_id_parametro();

    if (!$id) {
        admin_flash('error', 'Identificador no válido.');
        admin_redirect(admin_url('reportajes/index.php'));
    }

    $reportaje = admin_reportaje_obtener($conexion, $id);
    $errores = [];

    if (!$reportaje) {
        admin_flash('error', 'El reportaje solicitado no existe.');
        admin_redirect(admin_url('reportajes/index.php'));
    }

    admin_requerir_propiedad('reportajes', $reportaje);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            admin_validar_csrf();
            admin_eliminar_por_id($conexion, 'reportajes', $id);
            admin_flash('ok', 'Reportaje eliminado correctamente.');
            admin_redirect(admin_url('reportajes/index.php'));
        } catch (Throwable $error) {
            $errores[] = 'No se pudo eliminar el reportaje.';
        }
    }

    $admin_titulo = 'Eliminar reportaje';
    $admin_seccion = 'reportajes';
    require __DIR__ . '/admin-header.php';
    ?>
    <section class="admin-page-head">
        <div>
            <p class="admin-kicker">Confirmación</p>
            <h1>Eliminar reportaje</h1>
        </div>
        <a class="admin-btn" href="<?php echo e(admin_url('reportajes/index.php')); ?>">Volver</a>
    </section>

    <?php admin_render_mensajes($errores); ?>

    <form class="admin-card admin-delete-box" method="post">
        <?php echo admin_csrf_campo(); ?>
        <p>Vas a eliminar <strong><?php echo e($reportaje['titulo']); ?></strong>.</p>
        <p class="admin-muted">Las fotografías relacionadas se eliminarán por la relación de la base de datos.</p>
        <div class="admin-form-actions">
            <button class="admin-btn admin-btn-danger" type="submit" onclick="return confirm('¿Confirmas que deseas eliminar este reportaje?');">Eliminar</button>
            <a class="admin-btn" href="<?php echo e(admin_url('reportajes/index.php')); ?>">Cancelar</a>
        </div>
    </form>
    <?php
    require __DIR__ . '/admin-footer.php';
}

function admin_reportaje_destacar(PDO $conexion)
{
    // Solo admin: el autor recibe 403 y no puede manipular la URL.
    admin_requerir_admin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        admin_redirect(admin_url('reportajes/index.php'));
    }

    try {
        admin_validar_csrf();

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $accion = (string) ($_POST['accion'] ?? '');

        if (!$id || $id <= 0 || !in_array($accion, ['marcar', 'quitar'], true)) {
            admin_flash('error', 'Petición no válida.');
            admin_redirect(admin_url('reportajes/index.php'));
        }

        $reportaje = admin_reportaje_obtener($conexion, $id);

        if (!$reportaje) {
            admin_flash('error', 'El reportaje solicitado no existe.');
            admin_redirect(admin_url('reportajes/index.php'));
        }

        // Solo puede existir un destacado: transacción que desmarca todos
        // y luego marca únicamente el seleccionado.
        $conexion->beginTransaction();

        if ($accion === 'marcar') {
            $conexion->exec('UPDATE reportajes SET es_destacado = 0');
            $stmt = $conexion->prepare('UPDATE reportajes SET es_destacado = 1 WHERE id = :id');
            $stmt->execute(['id' => $id]);
        } else {
            $stmt = $conexion->prepare('UPDATE reportajes SET es_destacado = 0 WHERE id = :id');
            $stmt->execute(['id' => $id]);
        }

        $conexion->commit();

        // Comprueba el resultado real en la base de datos.
        $verificar = admin_reportaje_obtener($conexion, $id);
        $quedo = (int) ($verificar['es_destacado'] ?? -1);
        $esperado = $accion === 'marcar' ? 1 : 0;

        if ($verificar && $quedo === $esperado) {
            admin_flash(
                'ok',
                $accion === 'marcar'
                    ? 'Reportaje "' . $verificar['titulo'] . '" marcado como destacado.'
                    : 'Destacado retirado del reportaje "' . $verificar['titulo'] . '".'
            );
        } else {
            admin_flash('error', 'No se pudo actualizar el destacado. Inténtalo nuevamente.');
        }
    } catch (Throwable $error) {
        if ($conexion->inTransaction()) {
            $conexion->rollBack();
        }

        admin_flash('error', 'No se pudo actualizar el destacado. Inténtalo nuevamente.');
    }

    admin_redirect(admin_url('reportajes/index.php'));
}

function admin_usuarios_index(PDO $conexion)
{
    admin_requerir_admin();

    $usuarios = admin_listar($conexion, 'usuarios', 'email ASC, id DESC');

    $admin_titulo = 'Usuarios';
    $admin_seccion = 'usuarios';
    require __DIR__ . '/admin-header.php';
    ?>
    <section class="admin-page-head">
        <div>
            <p class="admin-kicker">Gestión</p>
            <h1>Usuarios</h1>
        </div>
        <a class="admin-btn admin-btn-primary" href="<?php echo e(admin_url('usuarios/crear.php')); ?>">
            <span class="fa fa-plus" aria-hidden="true"></span>
            Crear usuario
        </a>
    </section>

    <?php admin_render_mensajes(); ?>

    <section class="admin-card">
        <?php if ($usuarios): ?>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?php echo e(trim($usuario['nombres'] . ' ' . $usuario['ap_paterno'])); ?></td>
                                <td><?php echo e($usuario['email']); ?></td>
                                <td><?php echo e($usuario['rol']); ?></td>
                                <td class="admin-actions">
                                    <a href="<?php echo e(admin_url('usuarios/editar.php?id=' . (int) $usuario['id'])); ?>">Editar</a>
                                    <?php if ((int) $usuario['id'] !== admin_usuario_id_actual()): ?>
                                        <a class="admin-danger-link" href="<?php echo e(admin_url('usuarios/eliminar.php?id=' . (int) $usuario['id'])); ?>">Eliminar</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="admin-empty">No hay usuarios todavía.</p>
        <?php endif; ?>
    </section>
    <?php
    require __DIR__ . '/admin-footer.php';
}

function admin_usuarios_formulario(PDO $conexion, $id = null)
{
    admin_requerir_admin();

    $editando = $id !== null;
    $usuario = $editando ? admin_obtener_por_id($conexion, 'usuarios', $id) : [];
    $errores = [];

    if ($editando && !$usuario) {
        admin_flash('error', 'El usuario solicitado no existe.');
        admin_redirect(admin_url('usuarios/index.php'));
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            admin_validar_csrf();

            $nombres = trim((string) ($_POST['nombres'] ?? ''));
            $apPaterno = trim((string) ($_POST['ap_paterno'] ?? ''));
            $apMaterno = trim((string) ($_POST['ap_materno'] ?? ''));
            $email = trim((string) ($_POST['email'] ?? ''));
            $rol = trim((string) ($_POST['rol'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');
            $password2 = (string) ($_POST['password_confirm'] ?? '');

            if ($nombres === '') {
                $errores[] = 'El nombre es obligatorio.';
            }

            if ($apPaterno === '') {
                $errores[] = 'El apellido paterno es obligatorio.';
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errores[] = 'El correo electrónico no es válido.';
            }

            if (!admin_rol_permitido($rol)) {
                $errores[] = 'El rol debe ser admin o autor.';
            }

            if ($editando && (int) $usuario['id'] === admin_usuario_id_actual() && $rol !== 'admin') {
                $errores[] = 'No puedes quitarte el rol de administrador a ti mismo.';
            }

            if (!$editando && $password === '') {
                $errores[] = 'La contraseña es obligatoria.';
            }

            if ($password !== '' && strlen($password) < 8) {
                $errores[] = 'La contraseña debe tener al menos 8 caracteres.';
            }

            if ($password !== $password2) {
                $errores[] = 'La confirmación de contraseña no coincide.';
            }

            if (!$errores) {
                $stmtEmail = $conexion->prepare('SELECT id FROM usuarios WHERE email = :email AND id <> :id LIMIT 1');
                $stmtEmail->execute([
                    'email' => $email,
                    'id' => $editando ? (int) $id : 0,
                ]);

                if ($stmtEmail->fetch()) {
                    $errores[] = 'Ya existe un usuario con ese correo electrónico.';
                }
            }

            if (!$errores) {
                $datos = [
                    'nombres' => $nombres,
                    'ap_paterno' => $apPaterno,
                    'ap_materno' => $apMaterno === '' ? null : $apMaterno,
                    'email' => $email,
                    'rol' => $rol,
                ];

                if ($password !== '') {
                    $datos['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
                }

                if ($editando) {
                    admin_actualizar($conexion, 'usuarios', $datos, $id);
                    admin_flash('ok', 'Usuario actualizado correctamente.');
                } else {
                    admin_insertar($conexion, 'usuarios', $datos);
                    admin_flash('ok', 'Usuario creado correctamente.');
                }

                admin_redirect(admin_url('usuarios/index.php'));
            }
        } catch (Throwable $error) {
            $errores[] = 'No se pudo guardar el usuario. Revisa los datos e inténtalo nuevamente.';
        }
    }

    $admin_titulo = $editando ? 'Editar usuario' : 'Crear usuario';
    $admin_seccion = 'usuarios';
    require __DIR__ . '/admin-header.php';
    ?>
    <section class="admin-page-head">
        <div>
            <p class="admin-kicker"><?php echo $editando ? 'Edición' : 'Nuevo registro'; ?></p>
            <h1><?php echo e($admin_titulo); ?></h1>
        </div>
        <a class="admin-btn" href="<?php echo e(admin_url('usuarios/index.php')); ?>">Volver</a>
    </section>

    <?php admin_render_mensajes($errores); ?>

    <form class="admin-card admin-form" method="post">
        <?php echo admin_csrf_campo(); ?>
        <div class="admin-form-grid">
            <label class="admin-field" for="nombres">
                <span>Nombres *</span>
                <input id="nombres" type="text" name="nombres" value="<?php echo e(admin_campo_valor($usuario, 'nombres')); ?>" required>
            </label>
            <label class="admin-field" for="ap_paterno">
                <span>Apellido paterno *</span>
                <input id="ap_paterno" type="text" name="ap_paterno" value="<?php echo e(admin_campo_valor($usuario, 'ap_paterno')); ?>" required>
            </label>
            <label class="admin-field" for="ap_materno">
                <span>Apellido materno</span>
                <input id="ap_materno" type="text" name="ap_materno" value="<?php echo e(admin_campo_valor($usuario, 'ap_materno')); ?>">
            </label>
            <label class="admin-field" for="email">
                <span>Correo electrónico *</span>
                <input id="email" type="email" name="email" value="<?php echo e(admin_campo_valor($usuario, 'email')); ?>" required>
            </label>
            <label class="admin-field" for="rol">
                <span>Rol *</span>
                <select id="rol" name="rol" required>
                    <?php $rolActual = (string) admin_campo_valor($usuario, 'rol', 'autor'); ?>
                    <option value="admin" <?php echo $rolActual === 'admin' ? 'selected' : ''; ?>>Administrador</option>
                    <option value="autor" <?php echo $rolActual === 'autor' ? 'selected' : ''; ?>>Autor</option>
                </select>
            </label>
            <label class="admin-field" for="password">
                <span><?php echo $editando ? 'Nueva contraseña' : 'Contraseña *'; ?></span>
                <input id="password" type="password" name="password" <?php echo $editando ? '' : 'required'; ?> autocomplete="new-password">
            </label>
            <label class="admin-field" for="password_confirm">
                <span>Confirmar contraseña<?php echo $editando ? '' : ' *'; ?></span>
                <input id="password_confirm" type="password" name="password_confirm" <?php echo $editando ? '' : 'required'; ?> autocomplete="new-password">
            </label>
        </div>
        <div class="admin-form-actions">
            <button class="admin-btn admin-btn-primary" type="submit">Guardar</button>
            <a class="admin-btn" href="<?php echo e(admin_url('usuarios/index.php')); ?>">Cancelar</a>
        </div>
    </form>
    <?php
    require __DIR__ . '/admin-footer.php';
}

function admin_usuarios_eliminar(PDO $conexion)
{
    admin_requerir_admin();

    $id = admin_id_parametro();

    if (!$id) {
        admin_flash('error', 'Identificador no válido.');
        admin_redirect(admin_url('usuarios/index.php'));
    }

    if ($id === admin_usuario_id_actual()) {
        admin_flash('error', 'No puedes eliminar tu propio usuario.');
        admin_redirect(admin_url('usuarios/index.php'));
    }

    $usuario = admin_obtener_por_id($conexion, 'usuarios', $id);
    $errores = [];

    if (!$usuario) {
        admin_flash('error', 'El usuario solicitado no existe.');
        admin_redirect(admin_url('usuarios/index.php'));
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            admin_validar_csrf();

            if ($id === admin_usuario_id_actual()) {
                throw new RuntimeException('No puedes eliminar tu propio usuario.');
            }

            admin_eliminar_por_id($conexion, 'usuarios', $id);
            admin_flash('ok', 'Usuario eliminado correctamente.');
            admin_redirect(admin_url('usuarios/index.php'));
        } catch (Throwable $error) {
            $errores[] = 'No se pudo eliminar el usuario. Puede tener contenido relacionado.';
        }
    }

    $admin_titulo = 'Eliminar usuario';
    $admin_seccion = 'usuarios';
    require __DIR__ . '/admin-header.php';
    ?>
    <section class="admin-page-head">
        <div>
            <p class="admin-kicker">Confirmación</p>
            <h1>Eliminar usuario</h1>
        </div>
        <a class="admin-btn" href="<?php echo e(admin_url('usuarios/index.php')); ?>">Volver</a>
    </section>

    <?php admin_render_mensajes($errores); ?>

    <form class="admin-card admin-delete-box" method="post">
        <?php echo admin_csrf_campo(); ?>
        <p>Vas a eliminar <strong><?php echo e($usuario['email']); ?></strong>.</p>
        <p class="admin-muted">Esta acción no se puede deshacer desde el panel.</p>
        <div class="admin-form-actions">
            <button class="admin-btn admin-btn-danger" type="submit" onclick="return confirm('¿Confirmas que deseas eliminar este usuario?');">Eliminar</button>
            <a class="admin-btn" href="<?php echo e(admin_url('usuarios/index.php')); ?>">Cancelar</a>
        </div>
    </form>
    <?php
    require __DIR__ . '/admin-footer.php';
}
