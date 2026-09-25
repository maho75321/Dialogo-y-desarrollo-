-- Migración: sistema de borradores para noticias, reportajes, podcasts y videos.
-- Ejecutar UNA vez en phpMyAdmin (o consola mysql) sobre revista_digital.
-- Solo AGREGA la columna `estado`; no toca datos ni renombra nada existente.
-- El valor por defecto 'publicado' conserva la visibilidad actual de todo
-- el contenido; los borradores se gestionan desde el panel admin.
-- Si alguna columna ya existe, esa línea fallará sin afectar a las demás.

USE revista_digital;

ALTER TABLE `noticias`
  ADD COLUMN `estado` ENUM('borrador','publicado') NOT NULL DEFAULT 'publicado'
  COMMENT 'borrador: solo visible en admin; publicado: visible al publico';

ALTER TABLE `reportajes`
  ADD COLUMN `estado` ENUM('borrador','publicado') NOT NULL DEFAULT 'publicado'
  COMMENT 'borrador: solo visible en admin; publicado: visible al publico';

ALTER TABLE `podcasts`
  ADD COLUMN `estado` ENUM('borrador','publicado') NOT NULL DEFAULT 'publicado'
  COMMENT 'borrador: solo visible en admin; publicado: visible al publico';

ALTER TABLE `videos`
  ADD COLUMN `estado` ENUM('borrador','publicado') NOT NULL DEFAULT 'publicado'
  COMMENT 'borrador: solo visible en admin; publicado: visible al publico';
