CREATE DATABASE IF NOT EXISTS `modulo1`;
USE `MODULO1`;

CREATE TABLE IF NOT EXISTS `TIPO_ENCUESTA` (
  `id_tipo_encuesta` integer PRIMARY KEY AUTO_INCREMENT,
  `nombre_tipo` varchar(100) NOT NULL,
  `descripcion` text,
  `estado_activo` boolean NOT NULL DEFAULT true
);

CREATE TABLE IF NOT EXISTS `ADMINISTRATIVO` (
  `id_funcionario` integer PRIMARY KEY AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `email` varchar(150) UNIQUE NOT NULL,
  `usuario` varchar(50) UNIQUE NOT NULL,
  `contrasena` varchar(200) NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `es_medico` boolean NOT NULL DEFAULT false,
  `estado_activo` boolean NOT NULL DEFAULT true
);

CREATE TABLE IF NOT EXISTS `ENCUESTA` (
  `id_encuesta` integer PRIMARY KEY AUTO_INCREMENT,
  `titulo` varchar(100) NOT NULL,
  `descripcion` text,
  `estado` varchar(30) NOT NULL,
  `id_tipo_encuesta` integer NOT NULL,
  `id_funcionario_creador` integer NOT NULL,
  `estado_activo` boolean NOT NULL DEFAULT true
);

CREATE TABLE IF NOT EXISTS `PACIENTE` (
  `id_paciente` integer PRIMARY KEY AUTO_INCREMENT,
  `cedula` varchar(20) UNIQUE NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `email` varchar(100),
  `telefono` varchar(20),
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado_activo` boolean NOT NULL DEFAULT true
);

CREATE TABLE IF NOT EXISTS `PREGUNTA` (
  `id_pregunta` integer PRIMARY KEY AUTO_INCREMENT,
  `id_encuesta` integer NOT NULL,
  `texto_pregunta` text NOT NULL,
  `tipo_pregunta` varchar(50) NOT NULL,
  `orden` integer NOT NULL,
  `obligatoria` boolean NOT NULL DEFAULT false,
  `estado_activo` boolean NOT NULL DEFAULT true
);

CREATE TABLE IF NOT EXISTS `OPCION_RESPUESTA` (
  `id_opcion` integer PRIMARY KEY AUTO_INCREMENT,
  `id_pregunta` integer NOT NULL,
  `texto_opcion` varchar(200) NOT NULL,
  `valor` varchar(100),
  `estado_activo` boolean NOT NULL DEFAULT true
);

CREATE TABLE IF NOT EXISTS `RESPUESTA_ENCUESTA` (
  `id_respuesta` integer PRIMARY KEY AUTO_INCREMENT,
  `id_encuesta` integer NOT NULL,
  `id_paciente` integer NOT NULL,
  `fecha_respuesta` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `anonima` boolean NOT NULL DEFAULT false
);

CREATE TABLE IF NOT EXISTS `RESPUESTA_DETALLE` (
  `id_detalle` integer PRIMARY KEY AUTO_INCREMENT,
  `id_respuesta_encuesta` integer NOT NULL,
  `id_pregunta` integer NOT NULL,
  `id_opcion` integer NULL, 
  `texto_libre` text
);

CREATE TABLE IF NOT EXISTS `CATEGORIA_DOCUMENTO` (
  `id_categoria` integer PRIMARY KEY AUTO_INCREMENT,
  `nombre_categoria` varchar(100) UNIQUE NOT NULL,
  `descripcion` text,
  `estado_activo` boolean NOT NULL DEFAULT true,
  `archivo_url` varchar(500) NOT NULL
);

CREATE TABLE IF NOT EXISTS `SUBCATEGORIA_DOCUMENTO` (
  `id_subcategoria` integer PRIMARY KEY AUTO_INCREMENT,
  `id_categoria` integer NOT NULL,
  `nombre_subcategoria` varchar(100) NOT NULL,
  `descripcion` text,
  `estado_activo` boolean NOT NULL DEFAULT true,
  CONSTRAINT `fk_subcat_cat` FOREIGN KEY (`id_categoria`) 
    REFERENCES `CATEGORIA_DOCUMENTO` (`id_categoria`)
);

CREATE TABLE IF NOT EXISTS `DOCUMENTO` (
  `id_documento` integer PRIMARY KEY AUTO_INCREMENT,
  `titulo` varchar(200) NOT NULL,
  `descripcion` text,
  `archivo_url` varchar(500) NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime,
  `estado_activo` boolean NOT NULL DEFAULT true,
  `id_subcategoria` integer NULL,
  `id_categoria` integer NULL,
  `id_funcionario_creador` integer NULL
);

DROP INDEX IF EXISTS `PREGUNTA_index_0` ON `PREGUNTA`;
CREATE UNIQUE INDEX `PREGUNTA_index_0` ON `PREGUNTA` (`id_encuesta`, `orden`);

-- Claves foráneas
ALTER TABLE `ENCUESTA` ADD FOREIGN KEY (`id_tipo_encuesta`) REFERENCES `TIPO_ENCUESTA` (`id_tipo_encuesta`);
ALTER TABLE `ENCUESTA` ADD FOREIGN KEY (`id_funcionario_creador`) REFERENCES `ADMINISTRATIVO` (`id_funcionario`);

ALTER TABLE `PREGUNTA` ADD FOREIGN KEY (`id_encuesta`) REFERENCES `ENCUESTA` (`id_encuesta`);

ALTER TABLE `OPCION_RESPUESTA` ADD FOREIGN KEY (`id_pregunta`) REFERENCES `PREGUNTA` (`id_pregunta`);

ALTER TABLE `RESPUESTA_ENCUESTA` ADD FOREIGN KEY (`id_encuesta`) REFERENCES `ENCUESTA` (`id_encuesta`);
ALTER TABLE `RESPUESTA_ENCUESTA` ADD FOREIGN KEY (`id_paciente`) REFERENCES `PACIENTE` (`id_paciente`);

ALTER TABLE `RESPUESTA_DETALLE` ADD FOREIGN KEY (`id_respuesta_encuesta`) REFERENCES `RESPUESTA_ENCUESTA` (`id_respuesta`);
ALTER TABLE `RESPUESTA_DETALLE` ADD FOREIGN KEY (`id_pregunta`) REFERENCES `PREGUNTA` (`id_pregunta`);
ALTER TABLE `RESPUESTA_DETALLE` ADD FOREIGN KEY (`id_opcion`) REFERENCES `OPCION_RESPUESTA` (`id_opcion`);

ALTER TABLE `DOCUMENTO` ADD FOREIGN KEY (`id_subcategoria`) REFERENCES `SUBCATEGORIA_DOCUMENTO` (`id_subcategoria`);
ALTER TABLE `DOCUMENTO` ADD FOREIGN KEY (`id_categoria`) REFERENCES `CATEGORIA_DOCUMENTO` (`id_categoria`);
ALTER TABLE `DOCUMENTO` ADD FOREIGN KEY (`id_funcionario_creador`) REFERENCES `ADMINISTRATIVO` (`id_funcionario`);

INSERT INTO `ADMINISTRATIVO` (`id_funcionario`, `nombre`, `apellido`, `email`, `usuario`, `contrasena`, `fecha_registro`) VALUES
(1, 'Admin', 'Sistema', 'admin@hospital.com', 'admin', '$2a$12$eImiTXuWVxfM37uY4JANjOL.8/OHqA4/y1z2H.g0B5eXNqXz9G', NOW());

INSERT INTO `TIPO_ENCUESTA` (`id_tipo_encuesta`, `nombre_tipo`, `descripcion`, `estado_activo`) VALUES
(1, 'Satisfacción General', 'Evaluación de atención al paciente', TRUE),
(2, 'Seguimiento Médico', 'Monitoreo posterior a consulta o procedimiento', TRUE);

-- Categorías (5 columnas)
INSERT INTO `CATEGORIA_DOCUMENTO` (`id_categoria`, `nombre_categoria`, `descripcion`, `archivo_url`, `estado_activo`) VALUES
(1, 'Consentimientos Informados', 'Documentos legales de autorización', '/assets/img/cats/consent.png', TRUE),
(2, 'Guías de Cuidados', 'Instructivos de recuperación e higiene', '/assets/img/cats/guias.png', TRUE);

-- Subcategorías (asociadas a las categorías anteriores)
INSERT INTO `SUBCATEGORIA_DOCUMENTO` (`id_subcategoria`, `id_categoria`, `nombre_subcategoria`, `descripcion`, `estado_activo`) VALUES
(1, 1, 'Consentimiento Quirúrgico', 'Autorización para intervenciones de cirugía', TRUE),
(2, 2, 'Cuidados Post-Operatorios', 'Instrucciones para cuidados en casa', TRUE);

INSERT INTO `PACIENTE` (`id_paciente`, `cedula`, `nombre`, `apellido`, `email`, `telefono`, `fecha_registro`) VALUES
(1, '12345678', 'Juan', 'Pérez', 'juan.perez@example.com', '+59899123456', NOW());
