-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-11-2025 a las 05:44:50
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `luminary`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignaturas`
--

CREATE TABLE `asignaturas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `asignaturas`
--

INSERT INTO `asignaturas` (`id`, `nombre`) VALUES
(9, 'Ciencias'),
(4, 'Estudios Sociales'),
(7, 'Filosofía'),
(3, 'Inglés'),
(6, 'Inglés Comunicativo'),
(8, 'Instrumental'),
(1, 'Lenguaje'),
(2, 'Matemáticas'),
(5, 'TICs');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cursos`
--

CREATE TABLE `cursos` (
  `id` int(11) NOT NULL,
  `nivel` varchar(10) NOT NULL,
  `letra` char(1) NOT NULL,
  `profesor_jefe_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cursos`
--

INSERT INTO `cursos` (`id`, `nivel`, `letra`, `profesor_jefe_id`) VALUES
(1, '1°', 'A', 7),
(2, '1°', 'B', 1),
(3, '1°', 'C', NULL),
(4, '2°', 'A', NULL),
(5, '2°', 'B', NULL),
(6, '2°', 'C', NULL),
(7, '2°', 'D', NULL),
(8, '2°', 'E', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `curso_asignatura`
--

CREATE TABLE `curso_asignatura` (
  `id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `asignatura_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `curso_asignatura`
--

INSERT INTO `curso_asignatura` (`id`, `curso_id`, `asignatura_id`) VALUES
(58, 1, 1),
(59, 1, 2),
(56, 1, 3),
(55, 1, 4),
(60, 1, 5),
(57, 1, 6),
(54, 1, 9),
(17, 2, 1),
(18, 2, 2),
(15, 2, 3),
(14, 2, 4),
(19, 2, 5),
(16, 2, 6),
(13, 2, 9);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `curso_profesor`
--

CREATE TABLE `curso_profesor` (
  `id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `asignatura_id` int(11) NOT NULL,
  `profesor_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `curso_profesor`
--

INSERT INTO `curso_profesor` (`id`, `curso_id`, `asignatura_id`, `profesor_id`) VALUES
(3, 2, 4, 1),
(5, 1, 4, 8),
(7, 1, 3, 6),
(8, 2, 3, 6),
(9, 1, 2, 7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estudiantes`
--

CREATE TABLE `estudiantes` (
  `id` int(11) NOT NULL,
  `matricula_id` int(11) DEFAULT NULL,
  `curso_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estudiantes`
--

INSERT INTO `estudiantes` (`id`, `matricula_id`, `curso_id`) VALUES
(3, 8, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `matriculas`
--

CREATE TABLE `matriculas` (
  `id` int(11) NOT NULL,
  `nombre_estudiante` varchar(100) NOT NULL,
  `apellidos_estudiante` varchar(100) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `rut_estudiante` varchar(20) NOT NULL,
  `serie_carnet_estudiante` varchar(20) NOT NULL,
  `direccion_estudiante` varchar(255) NOT NULL,
  `correo_estudiante` varchar(150) NOT NULL,
  `telefono_estudiante` varchar(30) NOT NULL,
  `curso_preferido` int(11) NOT NULL,
  `jornada_preferida` varchar(100) NOT NULL,
  `nombre_apoderado` varchar(150) NOT NULL,
  `rut_apoderado` varchar(20) NOT NULL,
  `direccion_apoderado` varchar(255) NOT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `estado` varchar(50) NOT NULL DEFAULT 'Pendiente',
  `estudiante_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `matriculas`
--

INSERT INTO `matriculas` (`id`, `nombre_estudiante`, `apellidos_estudiante`, `fecha_nacimiento`, `rut_estudiante`, `serie_carnet_estudiante`, `direccion_estudiante`, `correo_estudiante`, `telefono_estudiante`, `curso_preferido`, `jornada_preferida`, `nombre_apoderado`, `rut_apoderado`, `direccion_apoderado`, `fecha_registro`, `estado`, `estudiante_id`) VALUES
(8, 'ADRIAN', 'MATURANA', '2001-01-18', '20.727.173-K', '54654462', 'FGDGFG', 'adrian.buscando123@gmail.com', '(564) 564-5645', 1, 'Mañana', 'Noemí Muñoz Vilchez', '8751644k', 'RIO TOLTEN 1640', '2025-11-23 17:02:15', 'Activa', 3),
(9, 'Romina', 'Maturana Muñoz', '1985-09-17', '17.793.251-5', '54654462', 'Estero Maintelahue 1100', 'adrian.buscando123@gmail.com', '957982068', 1, 'Mañana', 'Noemí Muñoz Vilchez', '8751644k', 'Estero Maintelahue 1100', '2025-11-23 21:33:23', 'Pendiente', NULL),
(10, 'ADRIAN', 'MATURANA', '2001-03-18', '24.654.679-7', '5465454', 'FGDGFG', 'adrian.buscando123@gmail.com', '(564) 564-5645', 1, 'Mañana', 'Noemí Muñoz Vilchez', '8751644k', 'FGDGFG', '2025-11-23 23:04:37', 'Pendiente', NULL),
(11, 'ADRIAN', 'MATURANA', '8132-03-12', '17.793.251-5', '54654462', 'FGDGFG', 'adrian.buscando123@gmail.com', '(564) 564-5645', 1, 'Mañana', 'Noemí Muñoz Vilchez', '8751644k', 'RIO TOLTEN 1640', '2025-11-23 23:27:27', 'Pendiente', NULL),
(12, 'ADRIAN', 'MATURANA', '4566-03-12', '20.727.173-K', '54654462', 'FGDGFG', 'adrian.buscando123@gmail.com', '(564) 564-5645', 1, 'Mañana', 'Noemí Muñoz Vilchez', '8751644k', 'RIO TOLTEN 1640', '2025-11-23 23:31:25', 'Pendiente', NULL),
(13, 'ADRIAN', 'MATURANA', '2011-03-18', '20.727.173-K', '54654462', 'FGDGFG', 'adrian.buscando123@gmail.com', '(564) 564-5645', 1, 'Mañana', 'Noemí Muñoz Vilchez', '8751644k', 'RIO TOLTEN 1640', '2025-11-23 23:48:34', 'Pendiente', NULL),
(14, 'ADRIAN', 'MATURANA', '2001-03-18', '20.727.173-K', '54654462', 'FGDGFG', 'adrian.buscando123@gmail.com', '(564) 564-5645', 1, 'Mañana', 'Noemí Muñoz Vilchez', '8751644k', 'FGDGFG', '2025-11-23 23:52:14', 'Pendiente', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notas`
--

CREATE TABLE `notas` (
  `id` int(11) NOT NULL,
  `estudiante_id` int(11) NOT NULL,
  `profesor_id` int(11) NOT NULL,
  `asignatura_id` int(11) NOT NULL,
  `nota1` decimal(2,1) DEFAULT NULL,
  `nota2` decimal(2,1) DEFAULT NULL,
  `nota3` decimal(2,1) DEFAULT NULL,
  `nota4` decimal(2,1) DEFAULT NULL,
  `nota5` decimal(2,1) DEFAULT NULL,
  `nota6` decimal(2,1) DEFAULT NULL,
  `nota7` decimal(2,1) DEFAULT NULL,
  `nota8` decimal(2,1) DEFAULT NULL,
  `nota9` decimal(2,1) DEFAULT NULL,
  `Σ` decimal(4,2) GENERATED ALWAYS AS (ifnull(`nota1`,0) + ifnull(`nota2`,0) + ifnull(`nota3`,0) + ifnull(`nota4`,0) + ifnull(`nota5`,0) + ifnull(`nota6`,0) + ifnull(`nota7`,0) + ifnull(`nota8`,0) + ifnull(`nota9`,0)) STORED,
  `x` decimal(4,2) GENERATED ALWAYS AS (round((ifnull(`nota1`,0) + ifnull(`nota2`,0) + ifnull(`nota3`,0) + ifnull(`nota4`,0) + ifnull(`nota5`,0) + ifnull(`nota6`,0) + ifnull(`nota7`,0) + ifnull(`nota8`,0) + ifnull(`nota9`,0)) / nullif((`nota1` is not null) + (`nota2` is not null) + (`nota3` is not null) + (`nota4` is not null) + (`nota5` is not null) + (`nota6` is not null) + (`nota7` is not null) + (`nota8` is not null) + (`nota9` is not null),0),2)) STORED,
  `x̄` decimal(2,1) GENERATED ALWAYS AS (round((ifnull(`nota1`,0) + ifnull(`nota2`,0) + ifnull(`nota3`,0) + ifnull(`nota4`,0) + ifnull(`nota5`,0) + ifnull(`nota6`,0) + ifnull(`nota7`,0) + ifnull(`nota8`,0) + ifnull(`nota9`,0)) / nullif((`nota1` is not null) + (`nota2` is not null) + (`nota3` is not null) + (`nota4` is not null) + (`nota5` is not null) + (`nota6` is not null) + (`nota7` is not null) + (`nota8` is not null) + (`nota9` is not null),0),2)) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notas`
--

INSERT INTO `notas` (`id`, `estudiante_id`, `profesor_id`, `asignatura_id`, `nota1`, `nota2`, `nota3`, `nota4`, `nota5`, `nota6`, `nota7`, `nota8`, `nota9`) VALUES
(2, 3, 1, 4, 7.0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `rol` enum('admin','editor','usuario') NOT NULL DEFAULT 'usuario',
  `asignatura` enum('Lenguaje','Matemáticas','Estudios Sociales','Ingles','Ingles Comunicativo','TIC','Filosofía','Instrumental') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `correo`, `contrasena`, `fecha_registro`, `rol`, `asignatura`) VALUES
(1, 'Adrián Maturana Muñoz', 'maturana.or.adrian@gmail.com', '$2y$10$IPgMPJZGdI31iCSFkuQgGuG5mx2eipa9BXnaJDUfeI3QHsc/6LXJ.', '2025-06-11 04:31:42', 'editor', 'Estudios Sociales'),
(3, 'Director', 'director@gmail.com', '$2y$10$G0X0FLMq9Kxnza/VgGue8eEOQLmzE0uw2ec//c7XnnLB95tIH.9ou', '2025-06-11 05:55:39', 'admin', NULL),
(6, 'Noemi', 'adrian.buscando123@gmail.com', '$2y$10$6UyE41deZkLLkzCTgFF5MuXUiXy1RSt/VgriqprhRvVg9k9tkGY12', '2025-06-13 00:09:28', 'editor', 'Ingles'),
(7, 'Orlando', 'dsadas@gmail.com', '$2y$10$lm4l/9ojULtLywXL04zeQue50b8fHzWuhgx1n9K9KvTVVtAqNgAZ2', '2025-06-13 02:40:07', 'editor', 'Matemáticas'),
(8, 'Ivania Sánchez', 'a@g.com', '$2y$10$wwIEUn43NJOLaakQ2VUUQuQVgTdNlgBQQUCmjLx3CuK29/ZGneuXq', '2025-06-21 11:56:14', 'editor', 'Estudios Sociales'),
(9, 'Noemi', '1@gmail.com', '$2y$10$FX36VJmpln7F5F3TW97QLeCYep21d88AlAR4KUcN.ccKJx/KDGVM.', '2025-11-13 04:36:48', 'editor', '');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `asignaturas`
--
ALTER TABLE `asignaturas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `cursos`
--
ALTER TABLE `cursos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nivel` (`nivel`,`letra`);

--
-- Indices de la tabla `curso_asignatura`
--
ALTER TABLE `curso_asignatura`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `curso_id` (`curso_id`,`asignatura_id`),
  ADD KEY `asignatura_id` (`asignatura_id`);

--
-- Indices de la tabla `curso_profesor`
--
ALTER TABLE `curso_profesor`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `curso_id` (`curso_id`,`asignatura_id`),
  ADD KEY `asignatura_id` (`asignatura_id`),
  ADD KEY `profesor_id` (`profesor_id`);

--
-- Indices de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `curso_id` (`curso_id`),
  ADD KEY `fk_estudiante_matricula` (`matricula_id`);

--
-- Indices de la tabla `matriculas`
--
ALTER TABLE `matriculas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_curso_preferido` (`curso_preferido`),
  ADD KEY `fk_matriculas_estudiante` (`estudiante_id`);

--
-- Indices de la tabla `notas`
--
ALTER TABLE `notas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `estudiante_id` (`estudiante_id`),
  ADD KEY `profesor_id` (`profesor_id`),
  ADD KEY `asignatura_id` (`asignatura_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `asignaturas`
--
ALTER TABLE `asignaturas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `curso_asignatura`
--
ALTER TABLE `curso_asignatura`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT de la tabla `curso_profesor`
--
ALTER TABLE `curso_profesor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `matriculas`
--
ALTER TABLE `matriculas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `notas`
--
ALTER TABLE `notas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `curso_asignatura`
--
ALTER TABLE `curso_asignatura`
  ADD CONSTRAINT `curso_asignatura_ibfk_1` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`),
  ADD CONSTRAINT `curso_asignatura_ibfk_2` FOREIGN KEY (`asignatura_id`) REFERENCES `asignaturas` (`id`);

--
-- Filtros para la tabla `curso_profesor`
--
ALTER TABLE `curso_profesor`
  ADD CONSTRAINT `curso_profesor_ibfk_1` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`),
  ADD CONSTRAINT `curso_profesor_ibfk_2` FOREIGN KEY (`asignatura_id`) REFERENCES `asignaturas` (`id`),
  ADD CONSTRAINT `curso_profesor_ibfk_3` FOREIGN KEY (`profesor_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  ADD CONSTRAINT `estudiantes_ibfk_1` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`),
  ADD CONSTRAINT `fk_estudiante_matricula` FOREIGN KEY (`matricula_id`) REFERENCES `matriculas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `matriculas`
--
ALTER TABLE `matriculas`
  ADD CONSTRAINT `fk_curso_preferido` FOREIGN KEY (`curso_preferido`) REFERENCES `cursos` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_matriculas_estudiante` FOREIGN KEY (`estudiante_id`) REFERENCES `estudiantes` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `notas`
--
ALTER TABLE `notas`
  ADD CONSTRAINT `notas_ibfk_1` FOREIGN KEY (`estudiante_id`) REFERENCES `estudiantes` (`id`),
  ADD CONSTRAINT `notas_ibfk_2` FOREIGN KEY (`profesor_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `notas_ibfk_3` FOREIGN KEY (`asignatura_id`) REFERENCES `asignaturas` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
