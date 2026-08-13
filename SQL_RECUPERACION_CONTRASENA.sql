-- Script SQL para agregar funcionalidad de recuperación de contraseña
-- Ejecutar este script en tu base de datos p_supermercado

-- Agregar columnas para recuperación de contraseña a la tabla usuarios
-- Si estas columnas ya existen, este script las ignorará

ALTER TABLE `usuarios` ADD COLUMN IF NOT EXISTS `reset_token` VARCHAR(255) NULL DEFAULT NULL AFTER `estado`;
ALTER TABLE `usuarios` ADD COLUMN IF NOT EXISTS `reset_expires` DATETIME NULL DEFAULT NULL AFTER `reset_token`;

-- Crear índice para búsquedas rápidas por token (opcional pero recomendado para rendimiento)
ALTER TABLE `usuarios` ADD INDEX IF NOT EXISTS `idx_reset_token` (`reset_token`);

-- Verificar que las columnas se agregaron correctamente
-- SELECT * FROM usuarios LIMIT 1;
