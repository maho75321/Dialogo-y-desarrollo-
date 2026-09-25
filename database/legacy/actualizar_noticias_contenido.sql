-- Migración: cuerpo con formato para noticias.
-- Ejecutar UNA vez en phpMyAdmin (o consola mysql) sobre revista_digital.
-- La tabla `noticias` NO tiene columna de contenido (verificado con DESCRIBE);
-- `reportajes` ya usa `desarrollo` (LONGTEXT), por eso no se toca.
-- Solo AGREGA la columna; no toca datos ni renombra nada existente.

USE revista_digital;

ALTER TABLE `noticias`
  ADD COLUMN `contenido` TEXT NULL DEFAULT NULL
  COMMENT 'Cuerpo de la noticia con formato HTML sanitizado';
