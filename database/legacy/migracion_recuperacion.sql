-- Migración: tabla de recuperación de contraseña para revista_digital.
-- Ejecutar UNA vez en phpMyAdmin (o consola mysql) sobre la base existente.
-- Es SEGURO repetirla: usa IF NOT EXISTS y no toca datos ni tablas existentes.
-- No cambia nombres de columnas. No elimina nada.

USE revista_digital;

CREATE TABLE IF NOT EXISTS `recuperacion_password` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_id` INT UNSIGNED NOT NULL,
  `token_hash` CHAR(64) NOT NULL COMMENT 'SHA-256 del token; el token en claro solo viaja por correo',
  `expira_en` DATETIME NOT NULL,
  `usado` TINYINT(1) NOT NULL DEFAULT 0,
  `creado_en` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_token_hash` (`token_hash`),
  KEY `idx_recuperacion_usuario` (`usuario_id`),
  CONSTRAINT `fk_recuperacion_usuario`
    FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
