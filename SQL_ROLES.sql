-- Script SQL de ejemplo para la tabla de roles
-- Este archivo muestra cómo debe estar configurada la tabla de roles en tu base de datos

-- Estructura de la tabla roles (si aún no existe)
CREATE TABLE IF NOT EXISTS `roles` (
  `id_rol` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id_rol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Roles requeridos para el sistema de permisos
-- IMPORTANTE: Mantener estos IDs tal como se muestran

INSERT INTO `roles` (`id_rol`, `nombre`, `descripcion`, `estado`) VALUES
(1, 'Administrador', 'Acceso completo al sistema', 1),
(2, 'Cajero', 'Acceso a módulos de ventas y caja', 1),
(3, 'Bodeguero', 'Acceso a módulos de inventario y compras', 1);

-- Relación en la tabla usuarios
-- La tabla usuarios debe tener la columna fk_rol que apunta a roles.id_rol
-- Ejemplo de estructura:

CREATE TABLE IF NOT EXISTS `usuarios` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `usuario` varchar(50) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `correo` varchar(100),
  `telefono` varchar(20),
  `fk_rol` int(11) NOT NULL,
  `estado` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id_usuario`),
  FOREIGN KEY (`fk_rol`) REFERENCES `roles`(`id_rol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Si necesitas cambiar los IDs de los roles, actualiza ambas tablas:
-- UPDATE roles SET id_rol = 4 WHERE nombre = 'Bodeguero';
-- UPDATE usuarios SET fk_rol = 4 WHERE fk_rol = 3;

-- IMPORTANTE: Después de cambiar los IDs, actualiza config/permisos.php:
-- const ROLE_ADMIN = 1;      // ID del Administrador
-- const ROLE_CAJERO = 2;     // ID del Cajero
-- const ROLE_BODEGUERO = 3;  // ID del Bodeguero (cambiar según sea necesario)

