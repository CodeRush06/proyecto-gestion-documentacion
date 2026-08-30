-- Aqui va el init.sql
CREATE DATABASE IF NOT EXISTS `MODULO1`;
USE `MODULO1`;

CREATE TABLE `TIPO_ENCUESTA` (
  `id_tipo_encuesta` integer PRIMARY KEY AUTO_INCREMENT,
  `nombre_tipo` varchar(100) NOT NULL,
  `descripcion` text,
  `estado_activo` boolean NOT NULL DEFAULT true
);

CREATE TABLE `ADMINISTRATIVO` (
  `id_funcionario` integer PRIMARY KEY AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `email` varchar(150) UNIQUE NOT NULL,
  `usuario` varchar(50) UNIQUE NOT NULL,
  `contrasena` varchar(200) NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `ENCUESTA` (
  `id_encuesta` integer PRIMARY KEY AUTO_INCREMENT,
  `titulo` varchar(100) NOT NULL,
  `descripcion` text,
  `estado` varchar(30) NOT NULL,
  `id_tipo_encuesta` integer NOT NULL,
  `id_funcionario_creador` integer NOT NULL
);

CREATE TABLE `PACIENTE` (
  `id_paciente` integer PRIMARY KEY AUTO_INCREMENT,
  `cedula` varchar(20) UNIQUE NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `email` varchar(100),
  `telefono` varchar(20),
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `PREGUNTA` (
  `id_pregunta` integer PRIMARY KEY AUTO_INCREMENT,
  `id_encuesta` integer NOT NULL,
  `texto_pregunta` text NOT NULL,
  `tipo_pregunta` varchar(50) NOT NULL,
  `orden` integer NOT NULL,
  `obligatoria` boolean NOT NULL DEFAULT false
);

CREATE TABLE `OPCION_RESPUESTA` (
  `id_opcion` integer PRIMARY KEY AUTO_INCREMENT,
  `id_pregunta` integer NOT NULL,
  `texto_opcion` varchar(200) NOT NULL,
  `valor` varchar(100)
);

CREATE TABLE `RESPUESTA_ENCUESTA` (
  `id_respuesta` integer PRIMARY KEY AUTO_INCREMENT,
  `id_encuesta` integer NOT NULL,
  `id_paciente` integer NOT NULL,
  `fecha_respuesta` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `anonima` boolean NOT NULL DEFAULT false
);

CREATE TABLE `RESPUESTA_DETALLE` (
  `id_detalle` integer PRIMARY KEY AUTO_INCREMENT,
  `id_respuesta_encuesta` integer NOT NULL,
  `id_pregunta` integer NOT NULL,
  `id_opcion` integer NULL, 
  `texto_libre` text
);

CREATE TABLE `CATEGORIA_DOCUMENTO` (
  `id_categoria` integer PRIMARY KEY AUTO_INCREMENT,
  `nombre_categoria` varchar(100) UNIQUE NOT NULL,
  `descripcion` text,
  `estado_activo` boolean NOT NULL DEFAULT true,
  `archivo_url` varchar(500) NOT NULL
);

CREATE TABLE `SUBCATEGORIA_DOCUMENTO` (
  `id_subcategoria` integer PRIMARY KEY AUTO_INCREMENT,
  `id_categoria` integer NOT NULL,
  `nombre_subcategoria` varchar(100) NOT NULL,
  `descripcion` text,
  `estado_activo` boolean NOT NULL DEFAULT true,
  CONSTRAINT `fk_subcat_cat` FOREIGN KEY (`id_categoria`) 
    REFERENCES `CATEGORIA_DOCUMENTO` (`id_categoria`)
);

CREATE TABLE `DOCUMENTO` (
  `id_documento` integer PRIMARY KEY AUTO_INCREMENT,
  `titulo` varchar(200) NOT NULL,
  `descripcion` text,
  `archivo_url` varchar(500) NOT NULL,
  `codigo_qr` varchar(225),
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime,
  `estado` varchar(30) NOT NULL,
  `id_subcategoria` integer NULL,
  `id_funcionario_creador` integer NULL
);

CREATE TABLE `ACCESO_DOCUMENTO` (
  `id_acceso` integer PRIMARY KEY AUTO_INCREMENT,
  `id_documento` integer NOT NULL,
  `id_paciente` integer NOT NULL,
  `fecha_acceso` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dispositivo` varchar(100),
  `ip_address` varchar(45)
);

CREATE UNIQUE INDEX `PREGUNTA_index_0` ON `PREGUNTA` (`id_encuesta`, `orden`);

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
ALTER TABLE `DOCUMENTO` ADD FOREIGN KEY (`id_funcionario_creador`) REFERENCES `ADMINISTRATIVO` (`id_funcionario`);

ALTER TABLE `ACCESO_DOCUMENTO` ADD FOREIGN KEY (`id_documento`) REFERENCES `DOCUMENTO` (`id_documento`);
ALTER TABLE `ACCESO_DOCUMENTO` ADD FOREIGN KEY (`id_paciente`) REFERENCES `PACIENTE` (`id_paciente`);
-- datos de prueba
INSERT INTO `ADMINISTRATIVO` (`id_funcionario`, `nombre`, `apellido`, `email`, `usuario`, `contrasena`, `fecha_registro`) VALUES
(1, 'Admin', 'Sistema', 'admin@hospital.com', 'admin', '$2a$12$eImiTXuWVxfM37uY4JANjOL.8/OHqA4/y1z2H.g0B5eXNqXz9G', NOW());

INSERT INTO `TIPO_ENCUESTA` (`id_tipo_encuesta`, `nombre_tipo`, `descripcion`, `estado_activo`) VALUES
(1, 'Satisfacción General', 'Evaluación de atención al paciente', TRUE),
(2, 'Seguimiento Médico', 'Monitoreo posterior a consulta o procedimiento', TRUE);

INSERT INTO `CATEGORIA_DOCUMENTO` (`id_categoria`, `nombre_categoria`, `descripcion`, `archivo_url`, `estado_activo`, `id_categoria_padre`) VALUES
(1, 'Consentimientos Informados', 'Documentos legales de autorización', '/assets/img/cats/consent.png', TRUE, NULL),
(2, 'Guías de Cuidados', 'Instructivos de recuperación e higiene', '/assets/img/cats/guias.png', TRUE, NULL),
(3, 'Consentimiento Quirúrgico', 'Autorización para intervenciones de cirugía', '/assets/img/cats/cirugia.png', TRUE, 1),
(4, 'Cuidados Post-Operatorios', 'Instrucciones para cuidados en casa', '/assets/img/cats/postop.png', TRUE, 2);

INSERT INTO `PACIENTE` (`id_paciente`, `cedula`, `nombre`, `apellido`, `email`, `telefono`, `fecha_registro`) VALUES
(1, '12345678', 'Juan', 'Pérez', 'juan.perez@example.com', '+59899123456', NOW());
