<?php
/**
 * Editor de texto enriquecido propio (contenteditable).
 * El contenido se sanitiza en el servidor con sanitizar_html() al guardar
 * y al mostrar; aquí solo se facilita el formato (negrita, cursiva,
 * subrayado, títulos, listas, enlaces, alineación, citas, tamaño y
 * familia de letra, quitar formato) más vista previa antes de guardar.
 */

function admin_editor_enriquecido($nombre, $valor, $etiqueta = 'Contenido')
{
    $id = 'ed_' . preg_replace('/[^a-zA-Z0-9_]/', '_', (string) $nombre);
    $semilla = sanitizar_html(admin_campo_valor([$nombre => $valor], $nombre));
    ob_start();
    ?>
    <div class="admin-field admin-field-wide">
        <span><?php echo e($etiqueta); ?></span>
        <div class="ed-toolbar" data-ed-toolbar="<?php echo e($id); ?>">
            <button type="button" data-ed="bold" title="Negrita"><strong>B</strong></button>
            <button type="button" data-ed="italic" title="Cursiva"><em>I</em></button>
            <button type="button" data-ed="underline" title="Subrayado"><u>U</u></button>
            <select data-ed-block title="Títulos y párrafos">
                <option value="p">Párrafo</option>
                <option value="h2">Título</option>
                <option value="h3">Subtítulo</option>
                <option value="h4">Subtítulo 2</option>
                <option value="blockquote">Cita</option>
            </select>
            <button type="button" data-ed="insertUnorderedList" title="Viñetas">• Lista</button>
            <button type="button" data-ed="insertOrderedList" title="Numerada">1. Lista</button>
            <button type="button" data-ed="link" title="Enlace">Enlace</button>
            <button type="button" data-ed="justifyLeft" title="Izquierda">◧ Izq</button>
            <button type="button" data-ed="justifyCenter" title="Centrada">▦ Cen</button>
            <button type="button" data-ed="justifyRight" title="Derecha">◨ Der</button>
            <select data-ed-size title="Tamaño de letra">
                <option value="">Tamaño</option>
                <option value="14px">Pequeño</option>
                <option value="16px">Normal</option>
                <option value="20px">Grande</option>
                <option value="26px">Muy grande</option>
            </select>
            <select data-ed-font title="Familia tipográfica">
                <option value="">Letra</option>
                <option value="Arial, sans-serif">Arial</option>
                <option value="Georgia, serif">Georgia</option>
                <option value="Verdana, sans-serif">Verdana</option>
                <option value="'Times New Roman', serif">Times</option>
            </select>
            <button type="button" data-ed="removeFormat" title="Quitar formato">Limpiar</button>
            <button type="button" data-ed-preview="<?php echo e($id); ?>" title="Vista previa">Vista previa</button>
        </div>
        <div class="ed-area rich-content" id="<?php echo e($id); ?>" contenteditable="true"><?php echo $semilla; ?></div>
        <input type="hidden" name="<?php echo e($nombre); ?>" id="<?php echo e($id); ?>_input" value="">
        <div class="ed-preview rich-content" id="<?php echo e($id); ?>_preview" hidden></div>
    </div>
    <?php
    return (string) ob_get_clean();
}

function admin_editor_script()
{
    ob_start();
    ?>
    <script>
    (function () {
        if (window.__edInit) {
            return;
        }

        window.__edInit = true;

        try {
            document.execCommand('styleWithCSS', false, true);
        } catch (e) {}

        function areaDe(toolbar) {
            var id = toolbar.getAttribute('data-ed-toolbar');
            return document.getElementById(id);
        }

        function envolverSeleccion(area, etiqueta, estilo) {
            var sel = window.getSelection();

            if (!sel || sel.rangeCount === 0 || sel.isCollapsed) {
                return false;
            }

            var rango = sel.getRangeAt(0);

            if (!area.contains(rango.commonAncestorContainer)) {
                return false;
            }

            var span = document.createElement(etiqueta);
            span.setAttribute('style', estilo);

            try {
                rango.surroundContents(span);
            } catch (err) {
                return false;
            }

            sel.removeAllRanges();
            return true;
        }

        document.querySelectorAll('[data-ed-toolbar]').forEach(function (toolbar) {
            toolbar.addEventListener('click', function (evento) {
                var btn = evento.target.closest('[data-ed]');

                if (!btn) {
                    return;
                }

                evento.preventDefault();
                var area = areaDe(toolbar);

                if (!area) {
                    return;
                }

                area.focus();
                var cmd = btn.getAttribute('data-ed');

                if (cmd === 'link') {
                    var url = window.prompt('URL del enlace (https://...)', 'https://');

                    if (url === null) {
                        return;
                    }

                    url = url.trim();

                    if (!/^(https?:\/\/|mailto:|#[^\s]*|\/[^\\\s]*)$/i.test(url)) {
                        window.alert('URL no válida. Usa https://, mailto:, #ancla o ruta /...');
                        return;
                    }

                    try {
                        document.execCommand('createLink', false, url);
                    } catch (e) {}
                } else {
                    try {
                        document.execCommand(cmd, false, null);
                    } catch (e) {}
                }
            });

            toolbar.querySelectorAll('[data-ed-block]').forEach(function (sel) {
                sel.addEventListener('change', function () {
                    var area = areaDe(toolbar);

                    if (area) {
                        area.focus();
                    }

                    try {
                        document.execCommand('formatBlock', false, sel.value);
                    } catch (e) {}
                    sel.selectedIndex = 0;
                    if (area) { area.focus(); }
                });
            });

            var selSize = toolbar.querySelector('[data-ed-size]');

            if (selSize) {
                selSize.addEventListener('change', function () {
                    var area = areaDe(toolbar);

                    if (area) {
                        area.focus();
                    }

                    if (selSize.value) {
                        envolverSeleccion(area, 'span', 'font-size: ' + selSize.value);
                    }
                    selSize.selectedIndex = 0;
                    if (area) { area.focus(); }
                });
            }

            var selFont = toolbar.querySelector('[data-ed-font]');

            if (selFont) {
                selFont.addEventListener('change', function () {
                    var area = areaDe(toolbar);

                    if (area) {
                        area.focus();
                    }

                    if (selFont.value) {
                        envolverSeleccion(area, 'span', 'font-family: ' + selFont.value);
                    }
                    selFont.selectedIndex = 0;
                    if (area) { area.focus(); }
                });
            }
        });

        document.querySelectorAll('[data-ed-preview]').forEach(function (btn) {
            btn.addEventListener('click', function (evento) {
                evento.preventDefault();
                var id = btn.getAttribute('data-ed-preview');
                var area = document.getElementById(id);
                var vista = document.getElementById(id + '_preview');

                if (!area || !vista) {
                    return;
                }

                vista.innerHTML = area.innerHTML;
                vista.hidden = !vista.hidden;
                btn.textContent = vista.hidden ? 'Vista previa' : 'Ocultar vista previa';
            });
        });

        document.querySelectorAll('form').forEach(function (form) {
            form.addEventListener('submit', function () {
                form.querySelectorAll('[data-ed-toolbar]').forEach(function (toolbar) {
                    var area = areaDe(toolbar);
                    var hidden = document.getElementById(toolbar.getAttribute('data-ed-toolbar') + '_input');

                    if (area && hidden) {
                        hidden.value = area.innerHTML;
                    }
                });
            });
        });
    })();
    </script>
    <?php
    return (string) ob_get_clean();
}
