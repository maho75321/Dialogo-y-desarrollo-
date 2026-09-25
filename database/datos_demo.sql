-- Datos de demostración para revista_digital.
-- Todo el contenido textual está marcado como material de prueba.
-- Las imágenes y PDFs se referencian por ruta; no se guardan binarios en MySQL.

USE revista_digital;

SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

START TRANSACTION;

SET @admin_email := '017200915e@uandina.edu.pe';
-- Hash bcrypt de la contraseña temporal de desarrollo (Admin12345!).
-- Solo se usa al CREAR la cuenta en instalaciones nuevas; jamás se toca
-- password_hash de una cuenta existente.
SET @admin_hash := '$2y$10$URMTMrq.64EFB421qrBtke2KhhpzjkXaYvNoIYoNJf9OGVU.VqNu2';

INSERT INTO usuarios (nombres, ap_paterno, ap_materno, email, password_hash, rol)
SELECT 'Administrador', 'DDP', NULL, @admin_email, @admin_hash, 'admin'
WHERE NOT EXISTS (
    SELECT 1 FROM usuarios WHERE email = @admin_email
);

UPDATE usuarios
SET nombres = 'Administrador',
    ap_paterno = 'DDP',
    ap_materno = NULL,
    rol = 'admin'
WHERE email = @admin_email;

SET @usuario_demo_id := (
    SELECT id FROM usuarios WHERE email = @admin_email LIMIT 1
);

INSERT INTO autores (nombres, ap_paterno, ap_materno, nickname, es_nickname)
SELECT 'Autora Demo', 'NTEP', NULL, 'Redacción Demo NTEP', 1
WHERE NOT EXISTS (SELECT 1 FROM autores WHERE nickname = 'Redacción Demo NTEP');

INSERT INTO autores (nombres, ap_paterno, ap_materno, nickname, es_nickname)
SELECT 'Editor Demo', 'Territorial', NULL, 'Editor de Prueba', 1
WHERE NOT EXISTS (SELECT 1 FROM autores WHERE nickname = 'Editor de Prueba');

INSERT INTO autores (nombres, ap_paterno, ap_materno, nickname, es_nickname)
SELECT 'Cronista Demo', 'Regional', NULL, NULL, 0
WHERE NOT EXISTS (
    SELECT 1 FROM autores
    WHERE nombres = 'Cronista Demo' AND ap_paterno = 'Regional'
);

SET @autor_demo_1 := (SELECT id FROM autores WHERE nickname = 'Redacción Demo NTEP' LIMIT 1);
SET @autor_demo_2 := (SELECT id FROM autores WHERE nickname = 'Editor de Prueba' LIMIT 1);
SET @autor_demo_3 := (SELECT id FROM autores WHERE nombres = 'Cronista Demo' AND ap_paterno = 'Regional' LIMIT 1);
SET @hay_destacado := (SELECT COUNT(*) FROM reportajes WHERE es_destacado = 1);

INSERT INTO reportajes (
    titulo, resumen_corto, desarrollo, foto_principal, pdf_adjunto,
    fecha_publicacion, es_destacado, autor_id, usuario_id
)
SELECT
    '[Demo] Reportaje destacado de prueba sobre diálogo territorial',
    'Contenido de demostración para validar la portada, el módulo de reportajes y el bloque destacado.',
    'Material de prueba. Este reportaje simula una pieza editorial y no describe hechos reales atribuidos a Diálogo y Desarrollo Perú. Sirve para verificar el diseño, la lectura de datos y el flujo de administración.',
    'assets/img/reportaje-01.jpg',
    NULL,
    '2025-07-31',
    IF(@hay_destacado = 0, 1, 0),
    @autor_demo_1,
    @usuario_demo_id
WHERE NOT EXISTS (
    SELECT 1 FROM reportajes
    WHERE titulo = '[Demo] Reportaje destacado de prueba sobre diálogo territorial'
);

INSERT INTO reportajes (
    titulo, resumen_corto, desarrollo, foto_principal, pdf_adjunto,
    fecha_publicacion, es_destacado, autor_id, usuario_id
)
SELECT
    '[Demo] Crónica de prueba sobre participación comunitaria',
    'Resumen de demostración para una crónica de prueba.',
    'Material de prueba. Texto ficticio usado para comprobar listados, fechas, autores y navegación hacia la ficha de reportaje.',
    'assets/img/reportaje-02.jpg',
    NULL,
    '2025-07-24',
    0,
    @autor_demo_2,
    @usuario_demo_id
WHERE NOT EXISTS (
    SELECT 1 FROM reportajes
    WHERE titulo = '[Demo] Crónica de prueba sobre participación comunitaria'
);

INSERT INTO reportajes (
    titulo, resumen_corto, desarrollo, foto_principal, pdf_adjunto,
    fecha_publicacion, es_destacado, autor_id, usuario_id
)
SELECT
    '[Demo] Reportaje de prueba sobre acuerdos locales',
    'Resumen de demostración para validar el módulo editorial.',
    'Material de prueba. El contenido no corresponde a una publicación real; permite revisar el formato de párrafos y la relación con fotografías.',
    'assets/img/reportaje-03.jpg',
    NULL,
    '2025-07-17',
    0,
    @autor_demo_3,
    @usuario_demo_id
WHERE NOT EXISTS (
    SELECT 1 FROM reportajes
    WHERE titulo = '[Demo] Reportaje de prueba sobre acuerdos locales'
);

INSERT INTO reportajes (
    titulo, resumen_corto, desarrollo, foto_principal, pdf_adjunto,
    fecha_publicacion, es_destacado, autor_id, usuario_id
)
SELECT
    '[Demo] Historia de prueba sobre desarrollo regional',
    'Resumen ficticio para comprobar la grilla de reportajes.',
    'Material de prueba. Esta historia es demostrativa y se utiliza para revisar estilos, paginación y lectura mediante PDO.',
    'assets/img/reportaje-04.jpg',
    NULL,
    '2025-07-10',
    0,
    NULL,
    @usuario_demo_id
WHERE NOT EXISTS (
    SELECT 1 FROM reportajes
    WHERE titulo = '[Demo] Historia de prueba sobre desarrollo regional'
);

INSERT INTO reportajes (
    titulo, resumen_corto, desarrollo, foto_principal, pdf_adjunto,
    fecha_publicacion, es_destacado, autor_id, usuario_id
)
SELECT
    '[Demo] Especial de prueba sobre información ciudadana',
    'Resumen de demostración para completar cinco reportajes.',
    'Material de prueba. La pieza existe únicamente para validar la carga de contenido y no debe publicarse como información real.',
    'assets/img/reportaje-05.jpg',
    NULL,
    '2025-07-03',
    0,
    @autor_demo_1,
    @usuario_demo_id
WHERE NOT EXISTS (
    SELECT 1 FROM reportajes
    WHERE titulo = '[Demo] Especial de prueba sobre información ciudadana'
);

SET @reportaje_1 := (SELECT id FROM reportajes WHERE titulo = '[Demo] Reportaje destacado de prueba sobre diálogo territorial' LIMIT 1);
SET @reportaje_2 := (SELECT id FROM reportajes WHERE titulo = '[Demo] Crónica de prueba sobre participación comunitaria' LIMIT 1);
SET @reportaje_3 := (SELECT id FROM reportajes WHERE titulo = '[Demo] Reportaje de prueba sobre acuerdos locales' LIMIT 1);
SET @reportaje_4 := (SELECT id FROM reportajes WHERE titulo = '[Demo] Historia de prueba sobre desarrollo regional' LIMIT 1);
SET @reportaje_5 := (SELECT id FROM reportajes WHERE titulo = '[Demo] Especial de prueba sobre información ciudadana' LIMIT 1);

INSERT INTO reportajes_fotos (reportaje_id, url_foto, orden, descripcion)
SELECT @reportaje_1, 'assets/img/reportaje-01.jpg', 1, 'Fotografía de demostración para galería.'
WHERE @reportaje_1 IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM reportajes_fotos WHERE reportaje_id = @reportaje_1 AND url_foto = 'assets/img/reportaje-01.jpg');

INSERT INTO reportajes_fotos (reportaje_id, url_foto, orden, descripcion)
SELECT @reportaje_2, 'assets/img/reportaje-02.jpg', 1, 'Imagen de prueba asociada al reportaje.'
WHERE @reportaje_2 IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM reportajes_fotos WHERE reportaje_id = @reportaje_2 AND url_foto = 'assets/img/reportaje-02.jpg');

INSERT INTO reportajes_fotos (reportaje_id, url_foto, orden, descripcion)
SELECT @reportaje_3, 'assets/img/reportaje-03.jpg', 1, 'Fotografía demo para validar relación.'
WHERE @reportaje_3 IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM reportajes_fotos WHERE reportaje_id = @reportaje_3 AND url_foto = 'assets/img/reportaje-03.jpg');

INSERT INTO reportajes_fotos (reportaje_id, url_foto, orden, descripcion)
SELECT @reportaje_4, 'assets/img/reportaje-04.jpg', 1, 'Imagen de demostración.'
WHERE @reportaje_4 IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM reportajes_fotos WHERE reportaje_id = @reportaje_4 AND url_foto = 'assets/img/reportaje-04.jpg');

INSERT INTO reportajes_fotos (reportaje_id, url_foto, orden, descripcion)
SELECT @reportaje_5, 'assets/img/reportaje-05.jpg', 1, 'Fotografía de prueba.'
WHERE @reportaje_5 IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM reportajes_fotos WHERE reportaje_id = @reportaje_5 AND url_foto = 'assets/img/reportaje-05.jpg');

INSERT INTO noticias (titulo, foto, link_externo, fecha_publicacion, usuario_id)
SELECT '[Demo] Noticia reciente de prueba 01', 'assets/img/noticia-01.jpg', NULL, '2026-09-10', @usuario_demo_id
WHERE NOT EXISTS (SELECT 1 FROM noticias WHERE titulo = '[Demo] Noticia reciente de prueba 01');

INSERT INTO noticias (titulo, foto, link_externo, fecha_publicacion, usuario_id)
SELECT '[Demo] Noticia reciente de prueba 02', 'assets/img/noticia-02.jpg', NULL, '2026-09-09', @usuario_demo_id
WHERE NOT EXISTS (SELECT 1 FROM noticias WHERE titulo = '[Demo] Noticia reciente de prueba 02');

INSERT INTO noticias (titulo, foto, link_externo, fecha_publicacion, usuario_id)
SELECT '[Demo] Noticia reciente de prueba 03', 'assets/img/noticia-03.jpg', NULL, '2026-09-08', @usuario_demo_id
WHERE NOT EXISTS (SELECT 1 FROM noticias WHERE titulo = '[Demo] Noticia reciente de prueba 03');

INSERT INTO noticias (titulo, foto, link_externo, fecha_publicacion, usuario_id)
SELECT '[Demo] Noticia reciente de prueba 04', 'assets/img/noticia-04.jpg', NULL, '2026-09-07', @usuario_demo_id
WHERE NOT EXISTS (SELECT 1 FROM noticias WHERE titulo = '[Demo] Noticia reciente de prueba 04');

INSERT INTO noticias (titulo, foto, link_externo, fecha_publicacion, usuario_id)
SELECT '[Demo] Noticia reciente de prueba 05', 'assets/img/noticia-05.jpg', NULL, '2026-09-06', @usuario_demo_id
WHERE NOT EXISTS (SELECT 1 FROM noticias WHERE titulo = '[Demo] Noticia reciente de prueba 05');

INSERT INTO boletines (numero_boletin, resumen, foto_portada, archivo_pdf, fecha_publicacion, usuario_id)
SELECT 'NTEP-DEMO-01', 'Boletín de demostración para validar portada, PDF y listado NTEP.', 'assets/img/boletin-01.jpg', 'uploads/boletines/ntep-01.pdf', '2025-07-31', @usuario_demo_id
WHERE NOT EXISTS (SELECT 1 FROM boletines WHERE numero_boletin = 'NTEP-DEMO-01');

INSERT INTO boletines (numero_boletin, resumen, foto_portada, archivo_pdf, fecha_publicacion, usuario_id)
SELECT 'NTEP-DEMO-02', 'Material de prueba para comprobar el módulo de boletines.', 'assets/img/boletin-01.jpg', 'uploads/boletines/ntep-02.pdf', '2025-07-24', @usuario_demo_id
WHERE NOT EXISTS (SELECT 1 FROM boletines WHERE numero_boletin = 'NTEP-DEMO-02');

INSERT INTO boletines (numero_boletin, resumen, foto_portada, archivo_pdf, fecha_publicacion, usuario_id)
SELECT 'NTEP-DEMO-03', 'Resumen ficticio de demostración; no corresponde a una edición real.', 'assets/img/boletin-01.jpg', 'uploads/boletines/ntep-03.pdf', '2025-07-17', @usuario_demo_id
WHERE NOT EXISTS (SELECT 1 FROM boletines WHERE numero_boletin = 'NTEP-DEMO-03');

INSERT INTO podcasts (titulo, url_embed, fecha_publicacion, usuario_id)
SELECT '[Demo] Podcast de prueba sobre diálogo 01', 'https://www.youtube.com/embed/00000000001', '2026-07-26', @usuario_demo_id
WHERE NOT EXISTS (SELECT 1 FROM podcasts WHERE titulo = '[Demo] Podcast de prueba sobre diálogo 01');

INSERT INTO podcasts (titulo, url_embed, fecha_publicacion, usuario_id)
SELECT '[Demo] Podcast de prueba sobre territorio 02', 'https://www.youtube.com/embed/00000000002', '2026-07-19', @usuario_demo_id
WHERE NOT EXISTS (SELECT 1 FROM podcasts WHERE titulo = '[Demo] Podcast de prueba sobre territorio 02');

INSERT INTO podcasts (titulo, url_embed, fecha_publicacion, usuario_id)
SELECT '[Demo] Podcast de prueba sobre participación 03', 'https://www.youtube.com/embed/00000000003', '2026-07-12', @usuario_demo_id
WHERE NOT EXISTS (SELECT 1 FROM podcasts WHERE titulo = '[Demo] Podcast de prueba sobre participación 03');

INSERT INTO podcasts (titulo, url_embed, fecha_publicacion, usuario_id)
SELECT '[Demo] Podcast de prueba sobre desarrollo 04', 'https://www.youtube.com/embed/00000000004', '2026-07-05', @usuario_demo_id
WHERE NOT EXISTS (SELECT 1 FROM podcasts WHERE titulo = '[Demo] Podcast de prueba sobre desarrollo 04');

INSERT INTO videos (titulo, url_embed, fecha_publicacion, usuario_id)
SELECT '[Demo] Video de prueba sobre diálogo 01', 'https://www.youtube.com/embed/00000000005', '2026-07-26', @usuario_demo_id
WHERE NOT EXISTS (SELECT 1 FROM videos WHERE titulo = '[Demo] Video de prueba sobre diálogo 01');

INSERT INTO videos (titulo, url_embed, fecha_publicacion, usuario_id)
SELECT '[Demo] Video de prueba sobre información pública 02', 'https://www.youtube.com/embed/00000000006', '2026-07-19', @usuario_demo_id
WHERE NOT EXISTS (SELECT 1 FROM videos WHERE titulo = '[Demo] Video de prueba sobre información pública 02');

INSERT INTO videos (titulo, url_embed, fecha_publicacion, usuario_id)
SELECT '[Demo] Video de prueba sobre encuentros regionales 03', 'https://www.youtube.com/embed/00000000007', '2026-07-12', @usuario_demo_id
WHERE NOT EXISTS (SELECT 1 FROM videos WHERE titulo = '[Demo] Video de prueba sobre encuentros regionales 03');

INSERT INTO videos (titulo, url_embed, fecha_publicacion, usuario_id)
SELECT '[Demo] Video de prueba sobre ciudadanía 04', 'https://www.youtube.com/embed/00000000008', '2026-07-05', @usuario_demo_id
WHERE NOT EXISTS (SELECT 1 FROM videos WHERE titulo = '[Demo] Video de prueba sobre ciudadanía 04');

COMMIT;
