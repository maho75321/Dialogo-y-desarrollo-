-- Migración de roles para revista_digital
-- Ejecutar en phpMyAdmin sobre la base existente ANTES de crear usuarios con rol autor.

USE revista_digital;

ALTER TABLE `usuarios`
  MODIFY `rol` ENUM('admin','autor') NOT NULL DEFAULT 'autor';

UPDATE `usuarios`
SET `rol` = 'admin'
WHERE `email` = '017200915e@uandina.edu.pe';
