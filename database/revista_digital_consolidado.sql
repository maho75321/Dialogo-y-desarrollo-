-- ============================================================
-- REVISTA DIGITAL — SQL CONSOLIDADO CANÓNICO
-- Base: revista_digital (MySQL 8 / MariaDB 10.4+, utf8mb4)
-- ============================================================
-- Reúne en UN solo archivo idempotente (se puede ejecutar varias
-- veces sin borrar ni duplicar nada):
--   basedatos.txt, revista_digital.sql (esquema base),
--   database/actualizar_estado.sql,
--   database/actualizar_noticias_contenido.sql,
--   database/actualizar_podcasts_audio.sql,
--   database/migracion_recuperacion.sql,
--   database/actualizar_recuperacion.sql,
--   database/migracion_roles.sql (solo la parte segura).
--
-- Los archivos originales quedaron archivados en database/legacy/
-- como referencia histórica (NO los ejecutes: basedatos.txt y
-- revista_digital.sql hacen DROP/INSERT con id fijo y BORRAN datos).
--
-- Datos de prueba: database/datos_demo.sql (idempotente, separado).
--
-- Orden: 1) CREATEs  2) ALTERs idempotentes  3) seed opcional.
-- ============================================================

CREATE DATABASE IF NOT EXISTS revista_digital
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE revista_digital;

SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 1. USUARIOS (login del panel; NO confundir con `autores`)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nombres` VARCHAR(100) NOT NULL,
  `ap_paterno` VARCHAR(100) NOT NULL,
  `ap_materno` VARCHAR(100) NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL COMMENT 'Hash bcrypt; jamás texto plano',
  `rol` ENUM('admin','autor') NOT NULL DEFAULT 'autor',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 2. AUTORES (firma editorial de reportajes; NO son logins)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `autores` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nombres` VARCHAR(100) NOT NULL,
  `ap_paterno` VARCHAR(100) NULL,
  `ap_materno` VARCHAR(100) NULL,
  `nickname` VARCHAR(100) NULL,
  `es_nickname` TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 3. REPORTAJES (con `estado` de borradores ya incluido)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `reportajes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `titulo` VARCHAR(255) NOT NULL,
  `resumen_corto` VARCHAR(500) NULL,
  `desarrollo` LONGTEXT NOT NULL COMMENT 'HTML sanitizado del editor',
  `foto_principal` VARCHAR(255) NULL COMMENT 'Ruta assets/img/... o uploads/...',
  `pdf_adjunto` VARCHAR(255) NULL,
  `fecha_publicacion` DATE NOT NULL,
  `es_destacado` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Solo 1 en 1: portada',
  `autor_id` INT UNSIGNED NULL COMMENT 'Firma editorial; NULL = Redacción',
  `usuario_id` INT UNSIGNED NOT NULL COMMENT 'Dueño (propiedad por usuario_id)',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `estado` ENUM('borrador','publicado') NOT NULL DEFAULT 'publicado'
    COMMENT 'borrador: solo visible en admin; publicado: visible al publico',
  CONSTRAINT `fk_reportajes_autor` FOREIGN KEY (`autor_id`)
    REFERENCES `autores` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_reportajes_usuario` FOREIGN KEY (`usuario_id`)
    REFERENCES `usuarios` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  KEY `idx_reportajes_fecha` (`fecha_publicacion`),
  KEY `idx_reportajes_destacado` (`es_destacado`),
  KEY `idx_reportajes_autor` (`autor_id`),
  KEY `idx_reportajes_usuario` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 4. FOTOS DE GALERÍA DE REPORTAJES
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `reportajes_fotos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `reportaje_id` INT UNSIGNED NOT NULL,
  `url_foto` VARCHAR(255) NOT NULL,
  `orden` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `descripcion` VARCHAR(255) NULL,
  CONSTRAINT `fk_reportajes_fotos_reportaje` FOREIGN KEY (`reportaje_id`)
    REFERENCES `reportajes` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
  KEY `idx_reportajes_fotos_reportaje` (`reportaje_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 5. NOTICIAS (con `estado` + `contenido` ya incluidos)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `noticias` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `titulo` VARCHAR(255) NOT NULL,
  `foto` VARCHAR(255) NULL,
  `link_externo` VARCHAR(500) NULL COMMENT 'Si existe, la tarjeta enlaza fuera',
  `fecha_publicacion` DATE NOT NULL,
  `usuario_id` INT UNSIGNED NOT NULL,
  `estado` ENUM('borrador','publicado') NOT NULL DEFAULT 'publicado'
    COMMENT 'borrador: solo visible en admin; publicado: visible al publico',
  `contenido` TEXT NULL
    COMMENT 'Cuerpo de la noticia con formato HTML sanitizado',
  CONSTRAINT `fk_noticias_usuario` FOREIGN KEY (`usuario_id`)
    REFERENCES `usuarios` (`id`) ON UPDATE CASCADE,
  KEY `idx_noticias_fecha` (`fecha_publicacion`),
  KEY `idx_noticias_usuario` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 6. BOLETINES NTEP (sin `estado`: siempre visibles)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `boletines` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `numero_boletin` VARCHAR(50) NOT NULL UNIQUE,
  `resumen` VARCHAR(500) NULL,
  `foto_portada` VARCHAR(255) NULL,
  `archivo_pdf` VARCHAR(255) NOT NULL,
  `fecha_publicacion` DATE NOT NULL,
  `usuario_id` INT UNSIGNED NOT NULL,
  CONSTRAINT `fk_boletines_usuario` FOREIGN KEY (`usuario_id`)
    REFERENCES `usuarios` (`id`) ON UPDATE CASCADE,
  KEY `idx_boletines_fecha` (`fecha_publicacion`),
  KEY `idx_boletines_usuario` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 7. PODCASTS (con `archivo_audio`, `url_embed` NULL y `estado`)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `podcasts` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `titulo` VARCHAR(255) NOT NULL,
  `url_embed` VARCHAR(500) NULL COMMENT 'YouTube / YouTube Music / Spotify normalizado a embed',
  `archivo_audio` VARCHAR(255) NULL COMMENT 'MP3/M4A subido a uploads/podcasts',
  `fecha_publicacion` DATE NOT NULL,
  `usuario_id` INT UNSIGNED NOT NULL,
  `estado` ENUM('borrador','publicado') NOT NULL DEFAULT 'publicado'
    COMMENT 'borrador: solo visible en admin; publicado: visible al publico',
  CONSTRAINT `fk_podcasts_usuario` FOREIGN KEY (`usuario_id`)
    REFERENCES `usuarios` (`id`) ON UPDATE CASCADE,
  KEY `idx_podcasts_fecha` (`fecha_publicacion`),
  KEY `idx_podcasts_usuario` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 8. VIDEOS (con `estado`)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `videos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `titulo` VARCHAR(255) NOT NULL,
  `url_embed` VARCHAR(500) NOT NULL COMMENT 'YouTube normalizado a embed',
  `fecha_publicacion` DATE NOT NULL,
  `usuario_id` INT UNSIGNED NOT NULL,
  `estado` ENUM('borrador','publicado') NOT NULL DEFAULT 'publicado'
    COMMENT 'borrador: solo visible en admin; publicado: visible al publico',
  CONSTRAINT `fk_videos_usuario` FOREIGN KEY (`usuario_id`)
    REFERENCES `usuarios` (`id`) ON UPDATE CASCADE,
  KEY `idx_videos_fecha` (`fecha_publicacion`),
  KEY `idx_videos_usuario` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 9. RECUPERACIÓN DE CONTRASEÑA (tabla + columnas de código)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `recuperacion_password` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` INT UNSIGNED NOT NULL,
  `token_hash` CHAR(64) NOT NULL COMMENT 'SHA-256 del token; el claro solo viaja por correo',
  `expira_en` DATETIME NOT NULL,
  `usado` TINYINT(1) NOT NULL DEFAULT 0,
  `creado_en` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `codigo_hash` CHAR(64) NULL COMMENT 'SHA-256 del codigo de 6 digitos',
  `intentos` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Intentos consumidos',
  `intentos_max` TINYINT UNSIGNED NOT NULL DEFAULT 5 COMMENT 'Intentos máximos',
  UNIQUE KEY `uq_token_hash` (`token_hash`),
  KEY `idx_recuperacion_usuario` (`usuario_id`),
  CONSTRAINT `fk_recuperacion_usuario` FOREIGN KEY (`usuario_id`)
    REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 10. ALTERS IDEMPOTENTES (para bases creadas con el esquema viejo;
--     en instalaciones nuevas no hacen nada porque las columnas
--     ya existen. Repetibles sin error en MariaDB 10.4+ / MySQL 8.)
-- ============================================================
ALTER TABLE `noticias` ADD COLUMN IF NOT EXISTS `estado`
  ENUM('borrador','publicado') NOT NULL DEFAULT 'publicado'
  COMMENT 'borrador: solo visible en admin; publicado: visible al publico';
ALTER TABLE `reportajes` ADD COLUMN IF NOT EXISTS `estado`
  ENUM('borrador','publicado') NOT NULL DEFAULT 'publicado'
  COMMENT 'borrador: solo visible en admin; publicado: visible al publico';
ALTER TABLE `podcasts` ADD COLUMN IF NOT EXISTS `estado`
  ENUM('borrador','publicado') NOT NULL DEFAULT 'publicado'
  COMMENT 'borrador: solo visible en admin; publicado: visible al publico';
ALTER TABLE `videos` ADD COLUMN IF NOT EXISTS `estado`
  ENUM('borrador','publicado') NOT NULL DEFAULT 'publicado'
  COMMENT 'borrador: solo visible en admin; publicado: visible al publico';

ALTER TABLE `noticias` ADD COLUMN IF NOT EXISTS `contenido`
  TEXT NULL DEFAULT NULL
  COMMENT 'Cuerpo de la noticia con formato HTML sanitizado';

ALTER TABLE `podcasts` ADD COLUMN IF NOT EXISTS `archivo_audio`
  VARCHAR(255) NULL AFTER `url_embed`;

ALTER TABLE `recuperacion_password` ADD COLUMN IF NOT EXISTS `codigo_hash`
  CHAR(64) NULL DEFAULT NULL
  COMMENT 'SHA-256 del codigo de 6 digitos';
ALTER TABLE `recuperacion_password` ADD COLUMN IF NOT EXISTS `intentos`
  TINYINT UNSIGNED NOT NULL DEFAULT 0
  COMMENT 'Intentos consumidos';
ALTER TABLE `recuperacion_password` ADD COLUMN IF NOT EXISTS `intentos_max`
  TINYINT UNSIGNED NOT NULL DEFAULT 5
  COMMENT 'Intentos máximos';

-- Normalizaciones seguras (no tocan datos, solo definición):
ALTER TABLE `usuarios` MODIFY `rol` ENUM('admin','autor') NOT NULL DEFAULT 'autor';
ALTER TABLE `podcasts` MODIFY `url_embed` VARCHAR(500) NULL;

-- NOTA sobre claves foráneas en bases viejas: vienen incluidas en los
-- CREATE de arriba. Si tu base es anterior y no tiene alguna FK,
-- agrégala manualmente con ALTER TABLE ... ADD CONSTRAINT ...
-- (verifica antes en INFORMATION_SCHEMA.TABLE_CONSTRAINTS).
-- NO se incluye el UPDATE ciego de migracion_roles.sql que forzaba
-- rol=admin por email: el admin inicial se crea con el seed de abajo.

-- ============================================================
-- 11. ADMIN INICIAL (seed mínimo, idempotente: no toca la clave
--     si la cuenta ya existe; solo asegura rol/nombres)
-- ============================================================
SET @admin_email := '017200915e@uandina.edu.pe';
SET @admin_hash := '$2y$10$URMTMrq.64EFB421qrBtke2KhhpzjkXaYvNoIYoNJf9OGVU.VqNu2';

INSERT INTO `usuarios` (`nombres`, `ap_paterno`, `ap_materno`, `email`, `password_hash`, `rol`)
SELECT 'Administrador', 'DDP', NULL, @admin_email, @admin_hash, 'admin'
WHERE NOT EXISTS (SELECT 1 FROM `usuarios` WHERE `email` = @admin_email);

UPDATE `usuarios`
SET `nombres` = 'Administrador', `ap_paterno` = 'DDP', `rol` = 'admin'
WHERE `email` = @admin_email;

-- Contenido de demostración (opcional): importar después
-- database/datos_demo.sql desde phpMyAdmin o consola:
--   mysql -u root revista_digital < database/datos_demo.sql
