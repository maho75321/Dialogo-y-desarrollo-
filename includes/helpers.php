<?php

function e($valor)
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function mostrarAutor($reportaje)
{
    if (empty($reportaje['autor_id'])) {
        return 'Redacción';
    }

    return nombreAutorBase(
        $reportaje['autor_nombres'] ?? '',
        $reportaje['autor_paterno'] ?? '',
        $reportaje['autor_materno'] ?? '',
        $reportaje['nickname'] ?? '',
        $reportaje['es_nickname'] ?? 0
    );
}

function formatearFecha($fecha)
{
    if (empty($fecha)) {
        return '';
    }

    $meses = [
        1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr',
        5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago',
        9 => 'Set', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic',
    ];

    $time = strtotime($fecha);
    $mes = $meses[(int) date('n', $time)] ?? date('M', $time);

    return $mes . ' ' . date('d, Y', $time);
}

function formatearNumeroBoletin($numero)
{
    $numero = trim((string) $numero);

    if ($numero === '') {
        return '';
    }

    if (preg_match('/^(Nº|N°|No\.?)\s*/iu', $numero)) {
        return $numero;
    }

    return 'Nº ' . $numero;
}

function rutaMedia($ruta)
{
    if (empty($ruta)) {
        return '';
    }

    if (preg_match('#^https?://#i', $ruta)) {
        return $ruta;
    }

    return '../' . ltrim($ruta, '/');
}

function rutaFisicaMedia($ruta)
{
    if (empty($ruta) || preg_match('#^https?://#i', $ruta)) {
        return null;
    }

    return dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, ltrim($ruta, '/'));
}

function mediaDisponible($ruta)
{
    if (empty($ruta)) {
        return false;
    }

    if (preg_match('#^https?://#i', $ruta)) {
        return true;
    }

    $rutaFisica = rutaFisicaMedia($ruta);

    return $rutaFisica && is_file($rutaFisica);
}

function mediaHostNormalizado($host)
{
    $host = strtolower((string) $host);

    if (strpos($host, 'www.') === 0) {
        return substr($host, 4);
    }

    return $host;
}

function youtubeVideoIdValido($id)
{
    return is_string($id) && preg_match('/^[a-zA-Z0-9_-]{6,20}$/', $id);
}

function spotifyIdValido($id)
{
    return is_string($id) && preg_match('/^[a-zA-Z0-9]{10,80}$/', $id);
}

function embedUrl($url)
{
    $url = trim((string) $url);

    if (empty($url)) {
        return '';
    }

    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return '';
    }

    $partes = parse_url($url);
    $host = mediaHostNormalizado($partes['host'] ?? '');
    $path = trim($partes['path'] ?? '', '/');
    parse_str($partes['query'] ?? '', $query);

    if ($host === 'youtu.be') {
        $segmentos = explode('/', $path);
        $videoId = $segmentos[0] ?? '';

        return youtubeVideoIdValido($videoId) ? 'https://www.youtube.com/embed/' . $videoId : '';
    }

    if (in_array($host, ['youtube.com', 'm.youtube.com', 'music.youtube.com', 'youtube-nocookie.com'], true)) {
        $segmentos = $path === '' ? [] : explode('/', $path);
        $videoId = '';

        if (($segmentos[0] ?? '') === 'watch') {
            $videoId = $query['v'] ?? '';
        } elseif (in_array($segmentos[0] ?? '', ['embed', 'shorts', 'live'], true)) {
            $videoId = $segmentos[1] ?? '';
        }

        return youtubeVideoIdValido($videoId) ? 'https://www.youtube.com/embed/' . $videoId : '';
    }

    if (in_array($host, ['open.spotify.com', 'player.spotify.com'], true)) {
        $segmentos = $path === '' ? [] : explode('/', $path);

        if (($segmentos[0] ?? '') === 'embed') {
            $tipo = $segmentos[1] ?? '';
            $id = $segmentos[2] ?? '';
        } else {
            $tipo = $segmentos[0] ?? '';
            $id = $segmentos[1] ?? '';
        }

        $tiposPermitidos = ['episode', 'show', 'track', 'album', 'playlist'];

        if (in_array($tipo, $tiposPermitidos, true) && spotifyIdValido($id)) {
            return 'https://open.spotify.com/embed/' . $tipo . '/' . $id;
        }
    }

    return '';
}

function esUrlEmbebible($url)
{
    return embedUrl($url) !== '';
}

function esAudioLocal($ruta)
{
    if (!is_string($ruta) || $ruta === '' || preg_match('#^https?://#i', $ruta)) {
        return false;
    }

    $extension = strtolower(pathinfo($ruta, PATHINFO_EXTENSION));

    if (!in_array($extension, ['mp3', 'ogg', 'oga', 'wav', 'm4a', 'mp4'], true)) {
        return false;
    }

    return mediaDisponible($ruta);
}

function mimeAudioPorExtension($extension)
{
    $mapa = [
        'mp3' => 'audio/mpeg',
        'ogg' => 'audio/ogg',
        'oga' => 'audio/ogg',
        'wav' => 'audio/wav',
        'm4a' => 'audio/mp4',
        'mp4' => 'audio/mp4',
    ];

    return $mapa[strtolower((string) $extension)] ?? '';
}

function podcastAudioLocal($podcast)
{
    // Prioridad: nuevo campo archivo_audio; luego url_embed local (datos heredados).
    // Devuelve la ruta local lista para <audio> o '' si no hay audio subido.
    if (is_array($podcast)) {
        $archivo = trim((string) ($podcast['archivo_audio'] ?? ''));

        if ($archivo !== '' && esAudioLocal($archivo)) {
            return $archivo;
        }

        $url = trim((string) ($podcast['url_embed'] ?? ''));

        if ($url !== '' && esAudioLocal($url)) {
            return $url;
        }
    }

    return '';
}

function iframeSeguro($src, $titulo)
{
    return '<iframe src="' . e($src) . '" title="' . e($titulo)
        . '" loading="lazy" allow="autoplay; encrypted-media; fullscreen; picture-in-picture" allowfullscreen></iframe>';
}

function urlExternaValida($url)
{
    $url = trim((string) $url);

    if ($url === '' || !filter_var($url, FILTER_VALIDATE_URL)) {
        return '';
    }

    $esquema = strtolower((string) parse_url($url, PHP_URL_SCHEME));

    return in_array($esquema, ['http', 'https'], true) ? $url : '';
}

function idYoutubeDeEmbed($embed)
{
    if (!is_string($embed) || $embed === '') {
        return '';
    }

    if (preg_match('#^https://www\.youtube\.com/embed/([a-zA-Z0-9_-]{6,20})$#', $embed, $m)) {
        return $m[1];
    }

    return '';
}

function miniaturaYoutube($videoId)
{
    if (!youtubeVideoIdValido($videoId)) {
        return '';
    }

    return 'https://i.ytimg.com/vi/' . $videoId . '/hqdefault.jpg';
}

function htmlImagen($ruta, $alt = '', $class = 'img-fluid')
{
    if (mediaDisponible($ruta)) {
        return '<img src="' . e(rutaMedia($ruta)) . '" alt="' . e($alt) . '" class="' . e($class) . '">';
    }

    return '<div class="' . e($class) . ' img-placeholder" aria-hidden="true"></div>';
}

function consultaReportajeBase()
{
    return "
        SELECT
            r.*,
            a.nombres AS autor_nombres,
            a.ap_paterno AS autor_paterno,
            a.ap_materno AS autor_materno,
            a.nickname,
            a.es_nickname
        FROM reportajes r
        LEFT JOIN autores a ON r.autor_id = a.id
    ";
}

function htmlEtiquetasPermitidas()
{
    return [
        'p' => [],
        'br' => [],
        'strong' => [],
        'b' => [],
        'em' => [],
        'i' => [],
        'u' => [],
        'h2' => [],
        'h3' => [],
        'h4' => [],
        'ul' => [],
        'ol' => [],
        'li' => [],
        'blockquote' => [],
        'a' => ['href', 'title'],
        'span' => ['style'],
    ];
}

function htmlEstiloSeguro($estilo)
{
    $permitidas = [
        'text-align' => '/^(left|center|right|justify)$/i',
        'font-size' => '/^([1-9]|[12][0-9]|3[0-6])px$/',
        'font-family' => "/^(Arial|Georgia|Verdana|Helvetica|Tahoma|'Trebuchet MS'|'Times New Roman'|sans-serif|serif|monospace)(,\s*(Arial|Georgia|Verdana|Helvetica|Tahoma|'Trebuchet MS'|'Times New Roman'|sans-serif|serif|monospace))*$/i",
    ];
    $salida = [];

    foreach (explode(';', (string) $estilo) as $declaracion) {
        $partes = explode(':', $declaracion, 2);

        if (count($partes) !== 2) {
            continue;
        }

        $prop = strtolower(trim($partes[0]));
        $valor = trim($partes[1]);

        if (isset($permitidas[$prop]) && preg_match($permitidas[$prop], $valor)) {
            $salida[] = $prop . ': ' . $valor;
        }
    }

    return $salida ? implode('; ', $salida) : '';
}

function htmlEnlaceSeguro(DOMElement $a)
{
    $href = trim((string) $a->getAttribute('href'));

    if ($href === '') {
        return false;
    }

    // Bloquea javascript:, data:, vbscript: y esquemas peligrosos.
    if (preg_match('#^\s*(javascript|data|vbscript|file|ftp)\s*:#i', $href)) {
        return false;
    }

    $esExterno = (bool) preg_match('#^https?://#i', $href);

    if (!$esExterno && !preg_match('#^(mailto:|#[^"\s]*|/[^"\s]*|[^:"\'<>\s]+)$#', $href)) {
        return false;
    }

    if ($esExterno && !filter_var($href, FILTER_VALIDATE_URL)) {
        return false;
    }

    $a->setAttribute('href', $href);
    $a->setAttribute('rel', 'noopener noreferrer');

    if ($esExterno) {
        $a->setAttribute('target', '_blank');
    } else {
        $a->removeAttribute('target');
    }

    return true;
}

function sanitizar_html($html)
{
    $html = (string) $html;

    if (trim($html) === '') {
        return '';
    }

    $permitidas = htmlEtiquetasPermitidas();
    // Se eliminan con todo su contenido (pueden ejecutar código).
    $eliminarConContenido = [
        'script', 'style', 'iframe', 'object', 'embed', 'link', 'meta',
        'form', 'input', 'button', 'select', 'textarea', 'noscript',
        'template', 'frame', 'frameset', 'applet', 'base', 'title',
    ];

    $doc = new DOMDocument('1.0', 'UTF-8');
    libxml_use_internal_errors(true);
    $doc->loadHTML(
        '<!DOCTYPE html><html><body><div id="raiz">' . $html . '</div></body></html>',
        LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
    );
    libxml_clear_errors();

    $raiz = $doc->getElementById('raiz');

    if (!$raiz) {
        return '';
    }

    $procesar = function ($nodo) use (&$procesar, $permitidas, $eliminarConContenido) {
        $hijos = [];

        foreach ($nodo->childNodes as $hijo) {
            $hijos[] = $hijo;
        }

        foreach ($hijos as $hijo) {
            if ($hijo->nodeType === XML_COMMENT_NODE) {
                $nodo->removeChild($hijo);
                continue;
            }

            if ($hijo->nodeType !== XML_ELEMENT_NODE) {
                continue;
            }

            $tag = strtolower($hijo->nodeName);

            if (in_array($tag, $eliminarConContenido, true)) {
                $nodo->removeChild($hijo);
                continue;
            }

            if (!isset($permitidas[$tag])) {
                // Etiqueta no permitida: se conserva solo su texto/contenido.
                $procesar($hijo);
                while ($hijo->firstChild) {
                    $nodo->insertBefore($hijo->firstChild, $hijo);
                }
                $nodo->removeChild($hijo);
                continue;
            }

            // Limpia atributos: quita eventos (onclick, onerror, ...) y todo
            // lo no permitido; valida style y enlaces.
            $attrs = [];

            foreach ($hijo->attributes as $attr) {
                $attrs[] = $attr->nodeName;
            }

            foreach ($attrs as $nombreAttr) {
                $minus = strtolower($nombreAttr);

                if (strpos($minus, 'on') === 0) {
                    $hijo->removeAttribute($nombreAttr);
                    continue;
                }

                if (!in_array($minus, $permitidas[$tag], true)) {
                    $hijo->removeAttribute($nombreAttr);
                    continue;
                }

                if ($tag === 'span' && $minus === 'style') {
                    $limpio = htmlEstiloSeguro($hijo->getAttribute($nombreAttr));

                    if ($limpio === '') {
                        $hijo->removeAttribute($nombreAttr);
                    } else {
                        $hijo->setAttribute('style', $limpio);
                    }
                }
            }

            if ($tag === 'a' && !htmlEnlaceSeguro($hijo)) {
                // Enlace peligroso: se deja solo su texto.
                $procesar($hijo);
                while ($hijo->firstChild) {
                    $nodo->insertBefore($hijo->firstChild, $hijo);
                }
                $nodo->removeChild($hijo);
                continue;
            }

            $procesar($hijo);
        }
    };

    $procesar($raiz);

    $salida = '';

    foreach ($raiz->childNodes as $hijo) {
        $salida .= $doc->saveHTML($hijo);
    }

    return trim($salida);
}

function contiene_html($texto)
{
    return is_string($texto) && $texto !== strip_tags($texto);
}

/**
 * Base compartida del nombre de autor (firma editorial).
 * Lógica única: si es_nickname=1 y hay nickname se usa; si no,
 * nombres + apellidos; si todo está vacío, 'Redacción'.
 * La usan mostrarAutor() (portada) y admin_nombre_autor_fila() (panel).
 */
function nombreAutorBase($nombres, $apPaterno, $apMaterno, $nickname, $esNickname)
{
    if ((int) $esNickname === 1 && trim((string) $nickname) !== '') {
        return trim((string) $nickname);
    }

    $nombre = trim(
        trim((string) $nombres)
        . ' ' . trim((string) $apPaterno)
        . ' ' . trim((string) $apMaterno)
    );

    return $nombre !== '' ? $nombre : 'Redacción';
}

/**
 * Paginado genérico para listados públicos.
 * Replica el patrón de reportajes.php (COUNT + LIMIT/OFFSET con enteros).
 * Devuelve [registros, pagina, paginas, total].
 */
function obtenerPaginado(PDO $conexion, $tabla, $where, $orden, $porPagina = 9)
{
    $pagina = max(1, (int) filter_input(INPUT_GET, 'pagina', FILTER_VALIDATE_INT));
    $total = (int) $conexion->query("SELECT COUNT(*) FROM `{$tabla}` WHERE {$where}")->fetchColumn();
    $paginas = max(1, (int) ceil($total / $porPagina));
    $pagina = min($pagina, $paginas);
    $offset = ($pagina - 1) * $porPagina;

    $stmt = $conexion->prepare(
        "SELECT * FROM `{$tabla}` WHERE {$where} ORDER BY {$orden} LIMIT :limite OFFSET :offset"
    );
    $stmt->bindValue(':limite', $porPagina, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    return [$stmt->fetchAll(PDO::FETCH_ASSOC), $pagina, $paginas, $total];
}

/**
 * Datos de tarjeta de podcast (una sola fuente de verdad).
 * Usado por portada y página de podcasts: evita la triple lógica
 * audio local -> iframe Spotify/YouTube/Music -> enlace externo.
 */
function podcastCardDatos(array $podcast)
{
    $audio = podcastAudioLocal($podcast);
    $embed = $audio === '' ? embedUrl($podcast['url_embed'] ?? '') : '';
    $modo = $audio !== '' ? 'audio' : ($embed !== '' ? 'iframe' : '');
    $src = $audio !== '' ? rutaMedia($audio) : $embed;
    // Solo URL externa válida: nunca se devuelve la cadena cruda (evita href="hola").
    $externa = $modo === '' ? urlExternaValida($podcast['url_embed'] ?? '') : '';

    return ['modo' => $modo, 'src' => $src, 'embed' => $embed, 'externa' => $externa, 'audio' => $audio];
}

/**
 * Datos de tarjeta de video (una sola fuente de verdad).
 */
function videoCardDatos(array $video)
{
    $embed = embedUrl($video['url_embed'] ?? '');
    $modo = $embed !== '' ? 'iframe' : '';
    $thumb = miniaturaYoutube(idYoutubeDeEmbed($embed));
    $externa = $modo === '' ? urlExternaValida($video['url_embed'] ?? '') : '';

    return ['modo' => $modo, 'embed' => $embed, 'thumb' => $thumb, 'externa' => $externa];
}

/**
 * ¿El PDF del boletín existe y se puede enlazar?
 * Evita href="" (recarga) y 404 cuando el archivo fue borrado.
 */
function boletinPdfDisponible(array $boletin)
{
    $pdf = trim((string) ($boletin['archivo_pdf'] ?? ''));

    if ($pdf === '') {
        return '';
    }

    return mediaDisponible($pdf) ? rutaMedia($pdf) : '';
}

function mostrar_contenido($texto)
{
    // Contenido con formato: se sanitiza al mostrar. Texto plano heredado:
    // se escapa y se conservan los saltos de línea.
    if (contiene_html((string) $texto)) {
        return sanitizar_html($texto);
    }

    return nl2br(e($texto));
}
