-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-09-2026 a las 21:23:27
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `revista_digital`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `autores`
--

CREATE TABLE `autores` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `ap_paterno` varchar(100) DEFAULT NULL,
  `ap_materno` varchar(100) DEFAULT NULL,
  `nickname` varchar(100) DEFAULT NULL,
  `es_nickname` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `autores`
--

INSERT INTO `autores` (`id`, `nombres`, `ap_paterno`, `ap_materno`, `nickname`, `es_nickname`) VALUES
(1, 'Redacción', 'DDP', NULL, 'Redacción DDP', 1),
(2, 'Autor de demostración', 'Uno', 'Prueba', NULL, 0),
(3, 'Autor de demostración', 'Dos', 'Prueba', NULL, 0),
(7, 'Redacción', 'DDP', NULL, 'Redacción DDP', 1),
(8, 'Equipo', 'Editorial', NULL, 'Equipo Editorial', 1),
(9, 'Redacción', 'Diálogo y Desarrollo', NULL, 'DDP Noticias', 1),
(13, 'Autora Demo', 'NTEP', NULL, 'Redacción Demo NTEP', 1),
(14, 'Editor Demo', 'Territorial', NULL, 'Editor de Prueba', 1),
(15, 'Cronista Demo', 'Regional', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `boletines`
--

CREATE TABLE `boletines` (
  `id` int(10) UNSIGNED NOT NULL,
  `numero_boletin` varchar(50) NOT NULL,
  `resumen` varchar(500) DEFAULT NULL,
  `foto_portada` varchar(255) DEFAULT NULL,
  `archivo_pdf` varchar(255) NOT NULL,
  `fecha_publicacion` date NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `boletines`
--

INSERT INTO `boletines` (`id`, `numero_boletin`, `resumen`, `foto_portada`, `archivo_pdf`, `fecha_publicacion`, `usuario_id`) VALUES
(1, 'Nº 45', 'Promueven megaproyectos turísticos por S/ 2,400 millones. Invertirán S/ 9 millones en zonas rurales de Cusco. La producción láctea se duplica en Cajamarca.', 'assets/img/boletin-45.jpg', 'uploads/boletines/boletin-45.pdf', '2025-08-28', 1),
(2, 'Nº 44', 'Información sobre inversión pública, desarrollo regional, educación y actividades productivas del país.', 'assets/img/boletin-44.jpg', 'uploads/boletines/boletin-44.pdf', '2025-08-21', 1),
(3, 'Nº 43', 'Noticias sobre canon, regalías, proyectos regionales y oportunidades de desarrollo sostenible.', 'assets/img/boletin-43.jpg', 'uploads/boletines/boletin-43.pdf', '2025-08-14', 1),
(10, 'NTEP-DEMO-01', 'Boletín de demostración para validar portada, PDF y listado NTEP.', 'assets/img/boletin-01.jpg', 'uploads/boletines/ntep-01.pdf', '2025-07-31', 1),
(11, 'NTEP-DEMO-02', 'Material de prueba para comprobar el módulo de boletines.', 'assets/img/boletin-01.jpg', 'uploads/boletines/ntep-02.pdf', '2025-07-24', 1),
(12, 'NTEP-DEMO-03', 'Resumen ficticio de demostración; no corresponde a una edición real.', 'assets/img/boletin-01.jpg', 'uploads/boletines/ntep-03.pdf', '2025-07-17', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `noticias`
--

CREATE TABLE `noticias` (
  `id` int(10) UNSIGNED NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `link_externo` varchar(500) DEFAULT NULL,
  `fecha_publicacion` date NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `noticias`
--

INSERT INTO `noticias` (`id`, `titulo`, `foto`, `link_externo`, `fecha_publicacion`, `usuario_id`) VALUES
(1, 'Perú espera respuesta de India para cerrar las negociaciones de un tratado comercial', 'assets/img/noticia-peru-india.jpg', 'https://www.reuters.com/world/india/peru-awaits-india-response-trade-talks-near-end-minister-says-2026-09-11/', '2026-09-11', 1),
(2, 'Perú se incorpora al Escudo de las Américas para combatir el crimen organizado', 'assets/img/noticia-escudo-americas.jpg', 'https://www.reuters.com/world/americas/peru-join-us-anti-drug-coalition-after-rubio-visit-2026-09-10/', '2026-09-10', 1),
(3, 'El Gobierno peruano define nuevos representantes diplomáticos en el extranjero', 'assets/img/noticia-diplomacia-peru.jpg', 'https://elpais.com/america/2026-09-09/keiko-fujimori-premia-con-cargos-diplomaticos-a-figuras-polemicas-o-sin-experiencia.html', '2026-09-09', 1),
(4, 'El comercio exterior peruano busca ampliar sus mercados en Asia', 'assets/img/noticia-comercio-exterior.jpg', 'https://www.reuters.com/world/india/peru-awaits-india-response-trade-talks-near-end-minister-says-2026-09-11/', '2026-09-08', 1),
(5, 'La lucha contra la criminalidad transnacional ocupa la agenda del Gobierno', 'assets/img/noticia-criminalidad-transnacional.jpg', 'https://www.reuters.com/world/americas/peru-join-us-anti-drug-coalition-after-rubio-visit-2026-09-10/', '2026-09-06', 1),
(6, 'Perú busca fortalecer su cooperación internacional en seguridad y defensa', 'assets/img/noticia-cooperacion-seguridad.jpg', 'https://elpais.com/america/2026-09-10/peru-se-incorpora-al-escudo-de-las-americas-de-trump-y-refuerza-su-alianza-en-seguridad-con-estados-unidos.html', '2026-09-05', 1),
(7, 'Una tumba Chimú intacta revela nuevos datos sobre la élite de Chan Chan', 'assets/img/noticia-tumba-chimu.jpg', 'https://elpais.com/america/2026-09-10/una-tumba-intacta-durante-600-anos-abre-una-ventana-al-mundo-de-la-elite-chimu-en-peru.html', '2026-09-10', 1),
(8, 'Chan Chan vuelve a ser centro de atención por un importante hallazgo arqueológico', 'assets/img/noticia-chan-chan.jpg', 'https://elpais.com/america/2026-09-10/una-tumba-intacta-durante-600-anos-abre-una-ventana-al-mundo-de-la-elite-chimu-en-peru.html', '2026-09-09', 1),
(9, 'Cusco mantiene el desafío de convertir el canon minero y gasífero en desarrollo', 'assets/img/noticia-canon-cusco.jpg', 'https://www.dialogoydesarrollo.com.pe/a-que-se-destino-el-canon-minero-y-gasifero-en-cusco.html', '2026-09-04', 1),
(10, 'El canon debe traducirse en obras de alto impacto para las regiones', 'assets/img/noticia-obras-canon.jpg', 'https://www.dialogoydesarrollo.com.pe/como-evitar-que-el-canon-del-boom-minero-termine-en-obras-de-poco-impacto.html', '2026-09-02', 1),
(11, 'La educación regional necesita alianzas para reducir las brechas de aprendizaje', 'assets/img/noticia-educacion-regional.jpg', 'https://www.dialogoydesarrollo.com.pe/742-escolares-de-taca-y-raccaya-reciben-kits-educativos.html', '2026-08-29', 1),
(12, 'Las regiones buscan nuevas estrategias para impulsar el desarrollo sostenible', 'assets/img/noticia-desarrollo-regional.jpg', 'https://www.dialogoydesarrollo.com.pe/', '2026-08-25', 1),
(13, 'Estados Unidos fortalece sus alianzas de seguridad en América Latina', 'assets/img/noticia-estados-unidos-america-latina.jpg', 'https://www.reuters.com/world/americas/peru-join-us-anti-drug-coalition-after-rubio-visit-2026-09-10/', '2026-09-10', 1),
(14, 'Perú y Colombia se suman a una coalición internacional contra el crimen organizado', 'assets/img/noticia-peru-colombia-coalicion.jpg', 'https://www1.folha.uol.com.br/mundo/2026/09/peru-passa-a-integrar-coalizao-dos-estados-unidos-contra-o-crime-organizado.shtml', '2026-09-09', 1),
(15, 'China observa con atención el acercamiento de Perú hacia Estados Unidos', 'assets/img/noticia-china-peru.jpg', 'https://www.reuters.com/world/americas/peru-join-us-anti-drug-coalition-after-rubio-visit-2026-09-10/', '2026-09-08', 1),
(16, 'América Latina debate nuevas formas de cooperación frente al crimen transnacional', 'assets/img/noticia-america-latina.jpg', 'https://www1.folha.uol.com.br/mundo/2026/09/peru-passa-a-integrar-coalizao-dos-estados-unidos-contra-o-crime-organizado.shtml', '2026-09-06', 1),
(17, 'Perú busca ampliar su presencia comercial en el mercado de India', 'assets/img/noticia-mercado-india.jpg', 'https://www.reuters.com/world/india/peru-awaits-india-response-trade-talks-near-end-minister-says-2026-09-11/', '2026-09-05', 1),
(18, 'Las relaciones entre China, Estados Unidos y América Latina entran en una nueva etapa', 'assets/img/noticia-relaciones-internacionales.jpg', 'https://www.reuters.com/world/americas/peru-join-us-anti-drug-coalition-after-rubio-visit-2026-09-10/', '2026-09-01', 1),
(19, 'Universidades públicas administran casi S/900 millones de canon, regalías y otros recursos determinados', 'assets/img/noticia-universidades-canon.jpg', 'https://www.dialogoydesarrollo.com.pe/', '2026-09-11', 1),
(20, 'Más de 730 mineros con Reinfo vigente o suspendido participan en las elecciones regionales y municipales', 'assets/img/noticia-mineros-reinfo.jpg', 'https://www.dialogoydesarrollo.com.pe/', '2026-08-28', 1),
(21, 'Quiruvilca: el pueblo perforado por la minería ilegal', 'assets/img/noticia-quiruvilca.jpg', 'https://www.dialogoydesarrollo.com.pe/', '2026-08-18', 1),
(22, 'Cómo evitar que el canon del boom minero termine en obras de poco impacto', 'assets/img/noticia-canon-obras.jpg', 'https://www.dialogoydesarrollo.com.pe/', '2026-08-12', 1),
(23, 'Canon minero en La Libertad: mucho dinero ejecutado, pocas brechas cerradas', 'assets/img/noticia-canon-la-libertad.jpg', 'https://www.dialogoydesarrollo.com.pe/', '2026-08-08', 1),
(24, 'Perú espera respuesta de India para cerrar las negociaciones de un tratado comercial', 'assets/img/noticia-peru-india.jpg', 'https://www.reuters.com/world/india/peru-awaits-india-response-trade-talks-near-end-minister-says-2026-09-11/', '2026-09-11', 1),
(25, 'Perú se incorpora a una coalición internacional contra el crimen organizado', 'assets/img/noticia-coalicion-seguridad.jpg', 'https://www.reuters.com/world/americas/peru-join-us-anti-drug-coalition-after-rubio-visit-2026-09-10/', '2026-09-10', 1),
(26, 'Una tumba Chimú intacta revela nuevos datos sobre la élite de Chan Chan', 'assets/img/noticia-tumba-chimu.jpg', 'https://elpais.com/america/2026-09-10/una-tumba-intacta-durante-600-anos-abre-una-ventana-al-mundo-de-la-elite-chimu-en-peru.html', '2026-09-10', 1),
(27, 'Perú fortalece su cooperación internacional en seguridad', 'assets/img/noticia-cooperacion-internacional.jpg', 'https://elpais.com/america/2026-09-10/peru-se-incorpora-al-escudo-de-las-americas-de-trump-y-refuerza-su-alianza-en-seguridad-con-estados-unidos.html', '2026-09-09', 1),
(28, 'Perú y Colombia participan en una coalición contra el crimen organizado', 'assets/img/noticia-peru-colombia.jpg', 'https://www1.folha.uol.com.br/mundo/2026/09/peru-passa-a-integrar-coalizao-dos-estados-unidos-contra-o-crime-organizado.shtml', '2026-09-09', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `podcasts`
--

CREATE TABLE `podcasts` (
  `id` int(10) UNSIGNED NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `url_embed` varchar(500) NOT NULL,
  `fecha_publicacion` date NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `podcasts`
--

INSERT INTO `podcasts` (`id`, `titulo`, `url_embed`, `fecha_publicacion`, `usuario_id`) VALUES
(1, 'Aumentan los casos de hackeo de WhatsApp y delitos informáticos en el país', 'https://www.youtube.com/watch?v=HDqFEsHfdzA', '2026-08-30', 1),
(2, 'La ciberseguridad y su impacto en la seguridad nacional', 'https://www.youtube.com/watch?v=cj_PnxiiLoc', '2026-08-23', 1),
(3, 'Minería Ilegal: La Principal Actividad Delictiva Ambiental', 'https://www.youtube.com/watch?v=s89r2aDq4HU', '2026-08-16', 1),
(4, 'El rol de la inversión pública en el desarrollo territorial', 'https://www.youtube.com/watch?v=aBLyxN3L9qM', '2026-08-09', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reportajes`
--

CREATE TABLE `reportajes` (
  `id` int(10) UNSIGNED NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `resumen_corto` varchar(500) DEFAULT NULL,
  `desarrollo` longtext NOT NULL,
  `foto_principal` varchar(255) DEFAULT NULL,
  `pdf_adjunto` varchar(255) DEFAULT NULL,
  `fecha_publicacion` date NOT NULL,
  `es_destacado` tinyint(1) NOT NULL DEFAULT 0,
  `autor_id` int(10) UNSIGNED DEFAULT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `reportajes`
--

INSERT INTO `reportajes` (`id`, `titulo`, `resumen_corto`, `desarrollo`, `foto_principal`, `pdf_adjunto`, `fecha_publicacion`, `es_destacado`, `autor_id`, `usuario_id`, `created_at`, `updated_at`) VALUES
(21, 'Los peligros de trabajar en un socavón ilegal', 'La minería ilegal expone a miles de trabajadores a derrumbes, accidentes, explotación laboral y condiciones inseguras.', '<p>Trabajar en un socavón ilegal implica riesgos permanentes para la vida y la salud de las personas.</p>\r\n     <p>La falta de supervisión, equipos de protección, ventilación adecuada y protocolos de seguridad incrementa la posibilidad de accidentes graves.</p>\r\n     <p>El problema también está relacionado con la informalidad laboral, la ausencia del Estado y el crecimiento de economías ilegales.</p>\r\n     <p>Este reportaje analiza las condiciones de trabajo y los peligros que enfrentan las personas que ingresan diariamente a estos espacios.</p>', 'assets/img/reportaje-socavon-ilegal.jpg', NULL, '2026-09-01', 1, 1, 1, '2026-09-11 12:59:21', '2026-09-11 12:59:21'),
(22, 'Canon y regalías mineras y gasíferas sostienen más del 70 % del presupuesto en Cusco', 'Los recursos provenientes de la minería y del gas tienen un peso determinante en el presupuesto de los gobiernos regionales y municipales del Cusco.', '<p>El canon y las regalías mineras y gasíferas representan una fuente fundamental de financiamiento para las obras y servicios públicos en Cusco.</p>\r\n     <p>A pesar del volumen de recursos transferidos, persisten brechas en salud, educación, transporte, agua y saneamiento.</p>\r\n     <p>El reportaje examina la dependencia de los gobiernos subnacionales respecto de las actividades extractivas y los desafíos para convertir esos ingresos en desarrollo sostenible.</p>', 'assets/img/reportaje-canon-cusco.jpg', NULL, '2026-08-28', 0, 1, 1, '2026-09-11 12:59:21', '2026-09-11 12:59:21'),
(23, 'La violencia ligada a economías ilegales se expande por todo el Perú', 'La expansión de actividades ilegales está vinculada con nuevas formas de violencia, amenazas y debilitamiento de la seguridad en distintas regiones del país.', '<p>Las economías ilegales generan redes de poder que afectan a comunidades, trabajadores y autoridades.</p>\r\n     <p>La minería ilegal, el tráfico de insumos y otras actividades ilícitas pueden relacionarse con extorsión, violencia y control territorial.</p>\r\n     <p>El reportaje aborda cómo estas dinámicas se extienden fuera de sus zonas tradicionales y plantean nuevos desafíos para el Estado y la ciudadanía.</p>', 'assets/img/reportaje-economias-ilegales.jpg', NULL, '2026-08-18', 0, 2, 1, '2026-09-11 12:59:21', '2026-09-11 12:59:21'),
(24, 'Bancada Reinfo: los nuevos aliados de la informalidad en el Congreso', 'El debate sobre el Registro Integral de Formalización Minera evidencia las tensiones entre formalización, minería informal y decisiones políticas.', '<p>El Registro Integral de Formalización Minera, conocido como Reinfo, se encuentra en el centro de la discusión sobre el futuro de la minería informal e ilegal.</p>\r\n     <p>La ampliación de plazos y las decisiones legislativas generan posiciones enfrentadas entre quienes defienden la formalización y quienes advierten sobre el uso del registro para mantener actividades fuera de la ley.</p>\r\n     <p>El reportaje revisa el debate político y sus posibles consecuencias para las comunidades, el ambiente y la lucha contra la minería ilegal.</p>', 'assets/img/reportaje-reinfo.jpg', NULL, '2026-08-12', 0, 2, 1, '2026-09-11 12:59:21', '2026-09-11 12:59:21'),
(25, 'Alianza entre UGEL Melgar, Minsur y Enseña Perú impulsará aprendizajes en estudiantes de Nuñoa', 'Una alianza entre instituciones educativas, una empresa minera y una organización especializada busca fortalecer los aprendizajes de estudiantes de Nuñoa.', '<p>La iniciativa reúne a la UGEL Melgar, Minsur y Enseña Perú con el objetivo de contribuir al fortalecimiento de los aprendizajes.</p>\r\n     <p>El trabajo conjunto busca mejorar las oportunidades educativas de los estudiantes y acompañar a las comunidades educativas.</p>\r\n     <p>Este tipo de alianzas plantea la importancia de coordinar esfuerzos entre el sector público, el sector privado y las organizaciones sociales para atender las brechas educativas.</p>', 'assets/img/reportaje-educacion-nunoa.jpg', NULL, '2026-08-05', 0, 3, 1, '2026-09-11 12:59:21', '2026-09-11 12:59:21'),
(26, 'El canon minero como motor de desarrollo social en 2025', 'El canon puede convertirse en una herramienta para financiar infraestructura, educación, salud y proyectos de desarrollo regional.', '<p>El canon minero constituye una de las principales fuentes de recursos para numerosas regiones del Perú.</p>\r\n     <p>Sin embargo, la transferencia de recursos no garantiza por sí sola la reducción de brechas sociales.</p>\r\n     <p>La planificación, la transparencia y la calidad del gasto son fundamentales para que estos ingresos se conviertan en obras útiles para la población.</p>', 'assets/img/reportaje-canon-desarrollo.jpg', NULL, '2026-07-30', 0, 1, 1, '2026-09-11 12:59:21', '2026-09-11 12:59:21'),
(27, 'Canon minero en La Libertad: mucho dinero ejecutado, pocas brechas cerradas', 'La ejecución presupuestal debe evaluarse no solo por el monto gastado, sino por los resultados concretos para la población.', '<p>La Libertad recibe importantes recursos provenientes del canon minero.</p>\r\n     <p>A pesar de los niveles de ejecución presupuestal, todavía existen necesidades pendientes en servicios básicos, infraestructura y atención social.</p>\r\n     <p>El reportaje plantea la necesidad de medir el impacto real de las inversiones públicas.</p>', 'assets/img/reportaje-canon-la-libertad.jpg', NULL, '2026-07-24', 0, 2, 1, '2026-09-11 12:59:21', '2026-09-11 12:59:21'),
(28, '742 escolares de Taca y Raccaya reciben kits educativos', 'La entrega de materiales educativos busca contribuir a la continuidad de los aprendizajes en comunidades rurales.', '<p>Los estudiantes de Taca y Raccaya recibieron kits educativos como parte de una iniciativa de apoyo a la educación rural.</p>\r\n     <p>La entrega de materiales representa una ayuda para las familias y docentes de las comunidades beneficiarias.</p>\r\n     <p>Estas acciones muestran la importancia de coordinar esfuerzos para reducir las brechas educativas.</p>', 'assets/img/reportaje-kits-educativos.jpg', NULL, '2026-07-18', 0, 3, 1, '2026-09-11 12:59:21', '2026-09-11 12:59:21'),
(39, '[Demo] Reportaje destacado de prueba sobre diálogo territorial', 'Contenido de demostración para validar la portada, el módulo de reportajes y el bloque destacado.', 'Material de prueba. Este reportaje simula una pieza editorial y no describe hechos reales atribuidos a Diálogo y Desarrollo Perú. Sirve para verificar el diseño, la lectura de datos y el flujo de administración.', 'assets/img/reportaje-01.jpg', NULL, '2025-07-31', 0, 13, 1, '2026-09-11 13:47:26', '2026-09-11 13:47:26'),
(40, '[Demo] Crónica de prueba sobre participación comunitaria', 'Resumen de demostración para una crónica de prueba.', 'Material de prueba. Texto ficticio usado para comprobar listados, fechas, autores y navegación hacia la ficha de reportaje.', 'assets/img/reportaje-02.jpg', NULL, '2025-07-24', 0, 14, 1, '2026-09-11 13:47:26', '2026-09-11 13:47:26'),
(41, '[Demo] Reportaje de prueba sobre acuerdos locales', 'Resumen de demostración para validar el módulo editorial.', 'Material de prueba. El contenido no corresponde a una publicación real; permite revisar el formato de párrafos y la relación con fotografías.', 'assets/img/reportaje-03.jpg', NULL, '2025-07-17', 0, 15, 1, '2026-09-11 13:47:26', '2026-09-11 13:47:26'),
(42, '[Demo] Historia de prueba sobre desarrollo regional', 'Resumen ficticio para comprobar la grilla de reportajes.', 'Material de prueba. Esta historia es demostrativa y se utiliza para revisar estilos, paginación y lectura mediante PDO.', 'assets/img/reportaje-04.jpg', NULL, '2025-07-10', 0, NULL, 1, '2026-09-11 13:47:26', '2026-09-11 13:47:26'),
(43, '[Demo] Especial de prueba sobre información ciudadana', 'Resumen de demostración para completar cinco reportajes.', 'Material de prueba. La pieza existe únicamente para validar la carga de contenido y no debe publicarse como información real.', 'assets/img/reportaje-05.jpg', NULL, '2025-07-03', 0, 13, 1, '2026-09-11 13:47:26', '2026-09-11 13:47:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reportajes_fotos`
--

CREATE TABLE `reportajes_fotos` (
  `id` int(10) UNSIGNED NOT NULL,
  `reportaje_id` int(10) UNSIGNED NOT NULL,
  `url_foto` varchar(255) NOT NULL,
  `orden` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `reportajes_fotos`
--

INSERT INTO `reportajes_fotos` (`id`, `reportaje_id`, `url_foto`, `orden`, `descripcion`) VALUES
(1, 21, 'assets/img/reportaje-socavon-ilegal-01.jpg', 1, 'Trabajadores y condiciones de seguridad en zonas mineras.'),
(2, 21, 'assets/img/reportaje-socavon-ilegal-02.jpg', 2, 'Interior de una zona de explotación minera.'),
(3, 22, 'assets/img/reportaje-canon-cusco-01.jpg', 1, 'Obras públicas financiadas con recursos del canon.'),
(4, 23, 'assets/img/reportaje-economias-ilegales-01.jpg', 1, 'Actividad económica y desarrollo territorial.'),
(5, 24, 'assets/img/reportaje-reinfo-01.jpg', 1, 'Debate sobre la formalización minera.'),
(6, 25, 'assets/img/reportaje-educacion-nunoa-01.jpg', 1, 'Estudiantes de comunidades rurales.'),
(7, 26, 'assets/img/reportaje-canon-desarrollo-01.jpg', 1, 'Infraestructura y desarrollo regional.'),
(8, 27, 'assets/img/reportaje-canon-la-libertad-01.jpg', 1, 'Obras y necesidades de la población.'),
(9, 28, 'assets/img/reportaje-kits-educativos-01.jpg', 1, 'Entrega de materiales educativos.'),
(15, 39, 'assets/img/reportaje-01.jpg', 1, 'Fotografía de demostración para galería.'),
(16, 40, 'assets/img/reportaje-02.jpg', 1, 'Imagen de prueba asociada al reportaje.'),
(17, 41, 'assets/img/reportaje-03.jpg', 1, 'Fotografía demo para validar relación.'),
(18, 42, 'assets/img/reportaje-04.jpg', 1, 'Imagen de demostración.'),
(19, 43, 'assets/img/reportaje-05.jpg', 1, 'Fotografía de prueba.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `ap_paterno` varchar(100) NOT NULL,
  `ap_materno` varchar(100) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `rol` enum('admin','autor') NOT NULL DEFAULT 'autor',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombres`, `ap_paterno`, `ap_materno`, `email`, `password_hash`, `rol`, `created_at`) VALUES
(1, 'Administrador', 'DDP', NULL, 'admin@gmail.com.pe', '$2y$10$ldkKJR54icYPB6zAzANHYO6a154AigufgiuOgH05rmhyeT3S/7Ic.', 'admin', '2026-09-11 12:51:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `videos`
--

CREATE TABLE `videos` (
  `id` int(10) UNSIGNED NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `url_embed` varchar(500) NOT NULL,
  `fecha_publicacion` date NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `videos`
--

INSERT INTO `videos` (`id`, `titulo`, `url_embed`, `fecha_publicacion`, `usuario_id`) VALUES
(1, 'Política Nacional Multisectorial para la Pequeña Minería y Minería Artesanal al 2030', 'https://www.youtube.com/watch?v=qlwbri5hXXk', '2026-08-30', 1),
(2, '¿REINFO otra vez?: Congresistas critican sobre la posibilidad de ampliarlo', 'https://www.youtube.com/watch?v=k1piXYhHdOA', '2026-08-23', 1),
(3, 'MINEROS ILEGALES de PATAZ llevan una VIDA DE LUJOS', 'https://www.youtube.com/watch?v=Gc_uyTC8KOc', '2026-08-16', 1),
(4, 'Minería ilegal: ¿Qué es y para qué se creó el REINFO y por qué causa controversia?', 'https://www.youtube.com/watch?v=BK7uCGpR4T8', '2026-08-09', 1),
(14, '[Demo] Video de prueba sobre información pública 02', 'https://www.youtube.com/embed/00000000006', '2026-07-19', 1),
(15, '[Demo] Video de prueba sobre encuentros regionales 03', 'https://www.youtube.com/embed/00000000007', '2026-07-12', 1),
(16, '[Demo] Video de prueba sobre ciudadanía 04', 'https://www.youtube.com/embed/00000000008', '2026-07-05', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `autores`
--
ALTER TABLE `autores`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `boletines`
--
ALTER TABLE `boletines`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_boletin` (`numero_boletin`),
  ADD KEY `idx_boletines_fecha` (`fecha_publicacion`),
  ADD KEY `idx_boletines_usuario` (`usuario_id`);

--
-- Indices de la tabla `noticias`
--
ALTER TABLE `noticias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_noticias_fecha` (`fecha_publicacion`),
  ADD KEY `idx_noticias_usuario` (`usuario_id`);

--
-- Indices de la tabla `podcasts`
--
ALTER TABLE `podcasts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_podcasts_fecha` (`fecha_publicacion`),
  ADD KEY `idx_podcasts_usuario` (`usuario_id`);

--
-- Indices de la tabla `reportajes`
--
ALTER TABLE `reportajes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reportajes_fecha` (`fecha_publicacion`),
  ADD KEY `idx_reportajes_destacado` (`es_destacado`),
  ADD KEY `idx_reportajes_autor` (`autor_id`),
  ADD KEY `idx_reportajes_usuario` (`usuario_id`);

--
-- Indices de la tabla `reportajes_fotos`
--
ALTER TABLE `reportajes_fotos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reportajes_fotos_reportaje` (`reportaje_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_videos_fecha` (`fecha_publicacion`),
  ADD KEY `idx_videos_usuario` (`usuario_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `autores`
--
ALTER TABLE `autores`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `boletines`
--
ALTER TABLE `boletines`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `noticias`
--
ALTER TABLE `noticias`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT de la tabla `podcasts`
--
ALTER TABLE `podcasts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `reportajes`
--
ALTER TABLE `reportajes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT de la tabla `reportajes_fotos`
--
ALTER TABLE `reportajes_fotos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `videos`
--
ALTER TABLE `videos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `boletines`
--
ALTER TABLE `boletines`
  ADD CONSTRAINT `fk_boletines_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `noticias`
--
ALTER TABLE `noticias`
  ADD CONSTRAINT `fk_noticias_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `podcasts`
--
ALTER TABLE `podcasts`
  ADD CONSTRAINT `fk_podcasts_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `reportajes`
--
ALTER TABLE `reportajes`
  ADD CONSTRAINT `fk_reportajes_autor` FOREIGN KEY (`autor_id`) REFERENCES `autores` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reportajes_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `reportajes_fotos`
--
ALTER TABLE `reportajes_fotos`
  ADD CONSTRAINT `fk_reportajes_fotos_reportaje` FOREIGN KEY (`reportaje_id`) REFERENCES `reportajes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `videos`
--
ALTER TABLE `videos`
  ADD CONSTRAINT `fk_videos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
