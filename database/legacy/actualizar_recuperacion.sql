-- Migración: códigos de verificación de 6 dígitos para recuperación.
-- Ejecutar UNA vez en phpMyAdmin (o consola mysql) sobre revista_digital.
-- Solo AGREGA columnas nuevas; no toca datos ni renombra nada existente.
-- Si alguna columna ya existe, esa línea fallará sin afectar a las demás.

USE revista_digital;

-- SHA-256 del código numérico de 6 dígitos (el código en claro solo viaja por Gmail).
ALTER TABLE `recuperacion_password`
  ADD COLUMN `codigo_hash` CHAR(64) NULL DEFAULT NULL
  COMMENT 'SHA-256 del codigo de verificacion';

-- Intentos consumidos al validar el código.
ALTER TABLE `recuperacion_password`
  ADD COLUMN `intentos` TINYINT UNSIGNED NOT NULL DEFAULT 0
  COMMENT 'Intentos de verificacion consumidos';

-- Máximo de intentos permitidos por código.
ALTER TABLE `recuperacion_password`
  ADD COLUMN `intentos_max` TINYINT UNSIGNED NOT NULL DEFAULT 5
  COMMENT 'Intentos maximos permitidos';
