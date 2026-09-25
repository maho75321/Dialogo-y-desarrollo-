<?php

require_once __DIR__ . '/helpers.php';

function admin_iniciar_sesion()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function admin_site_root_url()
{
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $pos = strpos($script, '/public/');

    if ($pos !== false) {
        return substr($script, 0, $pos);
    }

    return '';
}

function admin_public_url($ruta = 'index.php')
{
    return admin_site_root_url() . '/public/' . ltrim($ruta, '/');
}

function admin_asset_url($ruta)
{
    return admin_site_root_url() . '/assets/' . ltrim($ruta, '/');
}

function admin_media_url($ruta)
{
    if (empty($ruta)) {
        return '';
    }

    if (preg_match('#^https?://#i', $ruta)) {
        return $ruta;
    }

    return admin_site_root_url() . '/' . ltrim($ruta, '/');
}

function admin_base_url()
{
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $pos = strpos($script, '/admin/');

    if ($pos !== false) {
        return substr($script, 0, $pos + strlen('/admin'));
    }

    return admin_public_url('admin');
}

function admin_url($ruta = '')
{
    $base = rtrim(admin_base_url(), '/');

    if ($ruta === '') {
        return $base . '/';
    }

    return $base . '/' . ltrim($ruta, '/');
}

function admin_redirect($url)
{
    header('Location: ' . $url);
    exit;
}

function admin_rol_actual()
{
    return $_SESSION['rol'] ?? '';
}

function admin_rol_permitido($rol)
{
    return in_array($rol, ['admin', 'autor'], true);
}

function admin_usuario_id_actual()
{
    return (int) ($_SESSION['usuario_id'] ?? 0);
}

function admin_es_admin()
{
    return admin_rol_actual() === 'admin';
}

function admin_es_autor()
{
    return admin_rol_actual() === 'autor';
}

function admin_denegar_acceso($mensaje = 'Acceso denegado.')
{
    http_response_code(403);

    $admin_titulo = 'Acceso denegado';
    $admin_seccion = 'dashboard';
    require __DIR__ . '/admin-header.php';
    ?>
    <section class="admin-page-head">
        <div>
            <p class="admin-kicker">Permisos</p>
            <h1>Acceso denegado</h1>
        </div>
        <a class="admin-btn" href="<?php echo e(admin_url('index.php')); ?>">Volver</a>
    </section>

    <div class="admin-alert admin-alert-error"><?php echo e($mensaje); ?></div>
    <?php
    require __DIR__ . '/admin-footer.php';
    exit;
}

function admin_requerir_admin()
{
    if (!admin_es_admin()) {
        admin_denegar_acceso('No tienes permisos para acceder a este modulo.');
    }
}

function admin_modulo_permitido($modulo)
{
    if (admin_es_admin()) {
        return true;
    }

    if (admin_es_autor()) {
        return in_array($modulo, ['dashboard', 'noticias', 'reportajes', 'cambiar-password'], true);
    }

    return false;
}

function admin_requerir_modulo($modulo)
{
    if (!admin_modulo_permitido($modulo)) {
        admin_denegar_acceso('No tienes permisos para acceder a este modulo.');
    }
}

function admin_puede_gestionar_registro($tabla, array $registro)
{
    if (admin_es_admin()) {
        return true;
    }

    if (!admin_es_autor() || !in_array($tabla, ['noticias', 'reportajes'], true)) {
        return false;
    }

    return isset($registro['usuario_id'])
        && (int) $registro['usuario_id'] === admin_usuario_id_actual();
}

function admin_requerir_propiedad($tabla, array $registro)
{
    if (!admin_puede_gestionar_registro($tabla, $registro)) {
        admin_denegar_acceso('No puedes editar ni eliminar contenido de otro usuario.');
    }
}

function admin_forzar_usuario_id_creacion(PDO $conexion, $tabla, array &$datos)
{
    unset($datos['usuario_id']);

    if (admin_columna_existe($conexion, $tabla, 'usuario_id')) {
        $datos['usuario_id'] = admin_usuario_id_actual();
    }
}

function admin_bloquear_cambio_usuario_id(array &$datos)
{
    unset($datos['usuario_id']);
}

function admin_listar(PDO $conexion, $tabla, $orden, $filtrarPropiedad = false)
{
    if (!admin_nombre_sql_valido($tabla)) {
        throw new InvalidArgumentException('Nombre de tabla no válido.');
    }

    $ordenesPermitidos = [
        'fecha_publicacion DESC, id DESC',
        'nombres ASC, ap_paterno ASC, id DESC',
        'nombres ASC, ap_paterno ASC, id ASC',
        'email ASC, id DESC',
        'id DESC',
    ];

    if (!in_array($orden, $ordenesPermitidos, true)) {
        $orden = 'id DESC';
    }

    $sql = 'SELECT * FROM `' . $tabla . '`';
    $params = [];

    if ($filtrarPropiedad && admin_es_autor() && admin_columna_existe($conexion, $tabla, 'usuario_id')) {
        $sql .= ' WHERE usuario_id = :usuario_id';
        $params['usuario_id'] = admin_usuario_id_actual();
    }

    $sql .= ' ORDER BY ' . $orden;
    $stmt = $conexion->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function admin_nombre_sql_valido($nombre)
{
    return is_string($nombre) && preg_match('/^[a-zA-Z0-9_]+$/', $nombre);
}

function admin_columnas_tabla(PDO $conexion, $tabla)
{
    static $cache = [];

    if (!admin_nombre_sql_valido($tabla)) {
        throw new InvalidArgumentException('Nombre de tabla no válido.');
    }

    if (!isset($cache[$tabla])) {
        $stmt = $conexion->query('SHOW COLUMNS FROM `' . $tabla . '`');
        $cache[$tabla] = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'Field');
    }

    return $cache[$tabla];
}

function admin_columna_existe(PDO $conexion, $tabla, $columna)
{
    return in_array($columna, admin_columnas_tabla($conexion, $tabla), true);
}

function admin_csrf_token()
{
    admin_iniciar_sesion();

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function admin_csrf_campo()
{
    return '<input type="hidden" name="csrf_token" value="' . e(admin_csrf_token()) . '">';
}

function admin_validar_csrf()
{
    admin_iniciar_sesion();
    $token = $_POST['csrf_token'] ?? '';

    if (!is_string($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        throw new RuntimeException('La solicitud no pudo validarse. Inténtalo nuevamente.');
    }
}

function admin_flash($tipo, $mensaje)
{
    admin_iniciar_sesion();
    $_SESSION['admin_flash'][] = [
        'tipo' => $tipo,
        'mensaje' => $mensaje,
    ];
}

function admin_flash_obtener()
{
    admin_iniciar_sesion();
    $mensajes = $_SESSION['admin_flash'] ?? [];
    unset($_SESSION['admin_flash']);

    return $mensajes;
}

function admin_render_mensajes($errores = [])
{
    foreach (admin_flash_obtener() as $mensaje) {
        $clase = $mensaje['tipo'] === 'ok' ? 'admin-alert-ok' : 'admin-alert-error';
        echo '<div class="admin-alert ' . e($clase) . '">' . e($mensaje['mensaje']) . '</div>';
    }

    foreach ($errores as $error) {
        echo '<div class="admin-alert admin-alert-error">' . e($error) . '</div>';
    }
}

function admin_usuario_nombre()
{
    return $_SESSION['nombre'] ?? 'Administrador';
}

function admin_nombre_autor_fila($autor)
{
    return nombreAutorBase(
        $autor['nombres'] ?? '',
        $autor['ap_paterno'] ?? '',
        $autor['ap_materno'] ?? '',
        $autor['nickname'] ?? '',
        $autor['es_nickname'] ?? 0
    );
}

function admin_id_parametro()
{
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    return $id && $id > 0 ? (int) $id : null;
}

function admin_fecha_valida($fecha)
{
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
        return false;
    }

    [$anio, $mes, $dia] = array_map('intval', explode('-', $fecha));

    return checkdate($mes, $dia, $anio);
}

function admin_url_valida($url)
{
    // Fuente de verdad: urlExternaValida() (exige http/https).
    return urlExternaValida($url) !== '';
}

function admin_normalizar_youtube_embed($url)
{
    // Alias histórico: toda normalización pasa por embedUrl()
    // (YouTube, YouTube Music, youtu.be, shorts, Spotify).
    return admin_normalizar_media_embed($url);
}

function admin_normalizar_media_embed($url)
{
    return embedUrl($url);
}

function admin_recortar($texto, $limite = 90)
{
    $texto = trim((string) $texto);

    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
        if (mb_strlen($texto, 'UTF-8') <= $limite) {
            return $texto;
        }

        return mb_substr($texto, 0, $limite - 3, 'UTF-8') . '...';
    }

    if (strlen($texto) <= $limite) {
        return $texto;
    }

    return substr($texto, 0, $limite - 3) . '...';
}

function admin_obtener_por_id(PDO $conexion, $tabla, $id)
{
    if (!admin_nombre_sql_valido($tabla)) {
        throw new InvalidArgumentException('Nombre de tabla no válido.');
    }

    $stmt = $conexion->prepare('SELECT * FROM `' . $tabla . '` WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function admin_insertar(PDO $conexion, $tabla, array $datos)
{
    if (!admin_nombre_sql_valido($tabla)) {
        throw new InvalidArgumentException('Nombre de tabla no válido.');
    }

    foreach (array_keys($datos) as $columna) {
        if (!admin_nombre_sql_valido($columna)) {
            throw new InvalidArgumentException('Nombre de columna no válido.');
        }
    }

    $columnas = array_keys($datos);
    $sqlColumnas = implode(', ', array_map(function ($columna) {
        return '`' . $columna . '`';
    }, $columnas));
    $sqlParametros = implode(', ', array_map(function ($columna) {
        return ':' . $columna;
    }, $columnas));

    $stmt = $conexion->prepare(
        'INSERT INTO `' . $tabla . '` (' . $sqlColumnas . ') VALUES (' . $sqlParametros . ')'
    );
    $stmt->execute($datos);

    return (int) $conexion->lastInsertId();
}

function admin_actualizar(PDO $conexion, $tabla, array $datos, $id)
{
    if (!admin_nombre_sql_valido($tabla)) {
        throw new InvalidArgumentException('Nombre de tabla no válido.');
    }

    foreach (array_keys($datos) as $columna) {
        if (!admin_nombre_sql_valido($columna)) {
            throw new InvalidArgumentException('Nombre de columna no válido.');
        }
    }

    if (!$datos) {
        return;
    }

    $asignaciones = implode(', ', array_map(function ($columna) {
        return '`' . $columna . '` = :' . $columna;
    }, array_keys($datos)));

    $datos['id'] = $id;
    $stmt = $conexion->prepare('UPDATE `' . $tabla . '` SET ' . $asignaciones . ' WHERE id = :id');
    $stmt->execute($datos);
}

function admin_eliminar_por_id(PDO $conexion, $tabla, $id)
{
    if (!admin_nombre_sql_valido($tabla)) {
        throw new InvalidArgumentException('Nombre de tabla no válido.');
    }

    $stmt = $conexion->prepare('DELETE FROM `' . $tabla . '` WHERE id = :id');
    $stmt->execute(['id' => $id]);
}

function admin_contar(PDO $conexion, $tabla, $soloPropios = false)
{
    if (!admin_nombre_sql_valido($tabla)) {
        throw new InvalidArgumentException('Nombre de tabla no válido.');
    }

    $sql = 'SELECT COUNT(*) FROM `' . $tabla . '`';
    $params = [];

    if ($soloPropios && admin_columna_existe($conexion, $tabla, 'usuario_id')) {
        $sql .= ' WHERE usuario_id = :usuario_id';
        $params['usuario_id'] = admin_usuario_id_actual();
    }

    $stmt = $conexion->prepare($sql);
    $stmt->execute($params);

    return (int) $stmt->fetchColumn();
}

function admin_campo_valor($registro, $campo, $porDefecto = '')
{
    if (isset($_POST[$campo])) {
        return $_POST[$campo];
    }

    return $registro[$campo] ?? $porDefecto;
}
