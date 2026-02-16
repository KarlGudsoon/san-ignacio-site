-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-02-2026 a las 01:54:31
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

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
(6, 'Artes Visuales'),
(9, 'Ciencias'),
(4, 'Estudios Sociales'),
(7, 'Filosofía'),
(3, 'Inglés'),
(8, 'Instrumental'),
(1, 'Lenguaje'),
(2, 'Matemáticas'),
(5, 'TIC');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bloques_horarios`
--

CREATE TABLE `bloques_horarios` (
  `id` int(11) NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `orden` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `bloques_horarios`
--

INSERT INTO `bloques_horarios` (`id`, `hora_inicio`, `hora_fin`, `orden`) VALUES
(1, '08:15:00', '09:00:00', 1),
(2, '09:00:00', '09:45:00', 2),
(3, '09:45:00', '10:30:00', 3),
(4, '10:45:00', '11:30:00', 4),
(5, '11:30:00', '12:15:00', 5),
(6, '12:25:00', '13:00:00', 6);

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
(8, '2°', 'E', NULL),
(9, '2°', 'F', NULL);

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
(72, 1, 1),
(73, 1, 2),
(70, 1, 3),
(68, 1, 4),
(74, 1, 5),
(71, 1, 6),
(69, 1, 7),
(67, 1, 9),
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
(7, 1, 3, 6),
(8, 2, 3, 6),
(9, 1, 2, 7),
(10, 1, 9, 15),
(11, 1, 6, 1),
(12, 1, 4, 1),
(13, 1, 7, 1),
(14, 1, 1, 1),
(15, 1, 5, 6);

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
(1, 114, 1),
(2, 115, 1),
(3, 116, 1),
(4, 117, 1),
(5, 118, 1),
(6, 119, 1),
(7, 120, 1),
(8, 121, 1),
(9, 122, 1),
(10, 123, 1),
(11, 124, 1),
(12, 125, 1),
(13, 126, 1),
(14, 127, 1),
(15, 128, 1),
(16, 129, 1),
(17, 130, 1),
(18, 131, 1),
(19, 132, 1),
(20, 133, 1),
(21, 134, 1),
(22, 135, 1),
(23, 136, 1),
(24, 137, 1),
(25, 138, 1),
(26, 139, 1),
(27, 140, 1),
(28, 141, 1),
(29, 142, 1),
(30, 143, 1),
(31, 144, 1),
(32, 145, 1),
(33, 146, 1),
(34, 147, 1),
(35, 148, 2),
(36, 149, 2),
(37, 150, 2),
(38, 151, 2),
(39, 152, 2),
(40, 153, 2),
(41, 154, 2),
(42, 155, 2),
(43, 156, 2),
(44, 157, 2),
(45, 158, 2),
(46, 159, 2),
(47, 160, 2),
(48, 161, 2),
(49, 162, 2),
(50, 163, 2),
(51, 164, 2),
(52, 165, 2),
(53, 166, 2),
(54, 167, 2),
(55, 168, 2),
(56, 169, 2),
(57, 170, 2),
(58, 171, 2),
(59, 172, 2),
(60, 173, 2),
(61, 174, 2),
(62, 175, 2),
(63, 176, 2),
(64, 177, 2),
(65, 178, 2),
(66, 179, 2),
(67, 180, 2),
(68, 181, 2),
(69, 182, 2),
(70, 183, 2),
(71, 184, 2),
(72, 185, 2),
(73, 186, 2),
(74, 187, 2),
(75, 188, 2),
(76, 189, 3),
(77, 190, 3),
(78, 191, 3),
(79, 192, 3),
(80, 193, 3),
(81, 194, 3),
(82, 195, 3),
(83, 196, 3),
(84, 197, 3),
(85, 198, 3),
(86, 199, 3),
(87, 200, 3),
(88, 201, 3),
(89, 202, 3),
(90, 203, 3),
(91, 204, 3),
(92, 205, 3),
(93, 206, 3),
(94, 207, 3),
(95, 208, 3),
(96, 209, 3),
(97, 210, 3),
(98, 211, 3),
(99, 212, 3),
(100, 213, 3),
(101, 214, 3),
(102, 215, 3),
(103, 216, 3),
(104, 217, 3),
(105, 218, 3),
(106, 219, 3),
(107, 220, 3),
(108, 221, 3),
(109, 222, 3),
(110, 223, 3),
(111, 224, 3),
(112, 225, 3),
(113, 226, 3),
(114, 227, 3),
(115, 228, 3),
(116, 229, 3),
(117, 230, 3),
(118, 231, 3),
(119, 232, 4),
(120, 233, 4),
(121, 234, 4),
(122, 235, 4),
(123, 236, 4),
(124, 237, 4),
(125, 238, 4),
(126, 239, 4),
(127, 240, 4),
(128, 241, 4),
(129, 242, 4),
(130, 243, 4),
(131, 244, 4),
(132, 245, 4),
(133, 246, 4),
(134, 247, 4),
(135, 248, 4),
(136, 249, 4),
(137, 250, 4),
(138, 251, 4),
(139, 252, 4),
(140, 253, 4),
(141, 254, 4),
(142, 255, 4),
(143, 256, 4),
(144, 257, 4),
(145, 258, 4),
(146, 259, 5),
(147, 260, 5),
(148, 261, 5),
(149, 262, 5),
(150, 263, 5),
(151, 264, 5),
(152, 265, 5),
(153, 266, 5),
(154, 267, 5),
(155, 268, 5),
(156, 269, 5),
(157, 270, 5),
(158, 271, 5),
(159, 272, 5),
(160, 273, 5),
(161, 274, 5),
(162, 275, 5),
(163, 276, 5),
(164, 277, 5),
(165, 278, 5),
(166, 279, 5),
(167, 280, 5),
(168, 281, 5),
(169, 282, 5),
(170, 283, 5),
(171, 284, 5),
(172, 285, 5),
(173, 286, 5),
(174, 287, 5),
(175, 288, 5),
(176, 289, 5),
(177, 290, 5),
(178, 291, 5),
(179, 292, 5),
(180, 293, 5),
(181, 294, 5),
(182, 295, 5),
(183, 296, 5),
(184, 297, 5),
(185, 298, 6),
(186, 299, 6),
(187, 300, 6),
(188, 301, 6),
(189, 302, 6),
(190, 303, 6),
(191, 304, 6),
(192, 305, 6),
(193, 306, 6),
(194, 307, 6),
(195, 308, 6),
(196, 309, 6),
(197, 310, 6),
(198, 311, 6),
(199, 312, 6),
(200, 313, 6),
(201, 314, 6),
(202, 315, 6),
(203, 316, 6),
(204, 317, 6),
(205, 318, 6),
(206, 319, 6),
(207, 320, 6),
(208, 321, 6),
(209, 322, 6),
(210, 323, 6),
(211, 324, 6),
(212, 325, 6),
(213, 326, 6),
(214, 327, 6),
(215, 328, 6),
(216, 329, 6),
(217, 330, 6),
(218, 331, 6),
(219, 332, 6),
(220, 333, 6),
(221, 334, 6),
(222, 335, 6),
(223, 336, 6),
(224, 337, 6),
(225, 338, 6),
(226, 339, 7),
(227, 340, 7),
(228, 341, 7),
(229, 342, 7),
(230, 343, 7),
(231, 344, 7),
(232, 345, 7),
(233, 346, 7),
(234, 347, 7),
(235, 348, 7),
(236, 349, 7),
(237, 350, 7),
(238, 351, 7),
(239, 352, 7),
(240, 353, 7),
(241, 354, 7),
(242, 355, 7),
(243, 356, 7),
(244, 357, 7),
(245, 358, 7),
(246, 359, 7),
(247, 360, 7),
(248, 361, 7),
(249, 362, 7),
(250, 363, 7),
(251, 364, 7),
(252, 365, 7),
(253, 366, 7),
(254, 367, 7),
(255, 368, 7),
(256, 369, 7),
(257, 370, 7),
(258, 371, 7),
(259, 372, 8),
(260, 373, 8),
(261, 374, 8),
(262, 375, 8),
(263, 376, 8),
(264, 377, 8),
(265, 378, 8),
(266, 379, 8),
(267, 380, 8),
(268, 381, 8),
(269, 382, 8),
(270, 383, 8),
(271, 384, 8),
(272, 385, 8),
(273, 386, 8),
(274, 387, 8),
(275, 388, 8),
(276, 389, 8),
(277, 390, 8),
(278, 391, 8),
(279, 392, 8),
(280, 393, 8),
(281, 394, 8),
(282, 395, 8),
(283, 396, 8),
(284, 397, 8),
(285, 398, 8),
(286, 399, 8),
(287, 400, 8),
(288, 401, 8),
(289, 402, 8),
(290, 403, 8),
(291, 404, 9),
(292, 405, 9),
(293, 406, 9),
(294, 407, 9),
(295, 408, 9),
(296, 409, 9),
(297, 410, 9),
(298, 411, 9),
(299, 412, 9),
(300, 413, 9),
(301, 414, 9),
(302, 415, 9),
(303, 416, 9),
(304, 417, 9),
(305, 418, 9),
(306, 419, 9),
(307, 420, 9),
(308, 421, 9),
(309, 422, 9),
(310, 423, 9),
(311, 424, 9),
(312, 425, 9),
(313, 426, 9),
(314, 427, 9),
(315, 428, 9),
(316, 429, 9),
(317, 430, 9),
(318, 431, 9),
(319, 432, 9),
(320, 433, 9);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evaluaciones`
--

CREATE TABLE `evaluaciones` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `curso_profesor_id` int(11) NOT NULL,
  `tipo_id` int(11) NOT NULL,
  `fecha_aplicacion` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `evaluaciones`
--

INSERT INTO `evaluaciones` (`id`, `titulo`, `descripcion`, `curso_profesor_id`, `tipo_id`, `fecha_aplicacion`, `created_at`) VALUES
(1, 'Presentación interactiva', 'Crear presentación interactiva', 7, 3, '2026-02-16', '2026-02-15 23:08:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horarios`
--

CREATE TABLE `horarios` (
  `id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `dia` enum('lunes','martes','miercoles','jueves','viernes') NOT NULL,
  `bloque_id` int(11) NOT NULL,
  `asignatura_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `horarios`
--

INSERT INTO `horarios` (`id`, `curso_id`, `dia`, `bloque_id`, `asignatura_id`) VALUES
(1, 1, 'lunes', 2, 1),
(22, 1, 'lunes', 3, 2),
(23, 1, 'lunes', 4, 2),
(24, 1, 'martes', 1, 3),
(25, 1, 'martes', 2, 3),
(26, 1, 'martes', 3, 4),
(27, 1, 'martes', 4, 4),
(28, 1, 'miercoles', 1, 5),
(29, 1, 'miercoles', 2, 5),
(30, 1, 'miercoles', 3, 6),
(31, 1, 'miercoles', 4, 6),
(32, 1, 'jueves', 1, 7),
(33, 1, 'jueves', 2, 7),
(34, 1, 'jueves', 3, 8),
(35, 1, 'jueves', 4, 8),
(36, 1, 'viernes', 1, 9),
(37, 1, 'viernes', 2, 9),
(38, 1, 'viernes', 3, 1),
(39, 1, 'viernes', 4, 1),
(40, 1, 'lunes', 5, 4),
(41, 1, 'lunes', 6, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `matriculas`
--

CREATE TABLE `matriculas` (
  `id` int(11) NOT NULL,
  `nombre_estudiante` varchar(100) NOT NULL,
  `apellidos_estudiante` varchar(100) NOT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `rut_estudiante` varchar(20) NOT NULL,
  `serie_carnet_estudiante` varchar(20) DEFAULT NULL,
  `etnia_estudiante` varchar(50) DEFAULT NULL,
  `direccion_estudiante` varchar(255) DEFAULT NULL,
  `correo_estudiante` varchar(150) DEFAULT NULL,
  `telefono_estudiante` varchar(30) DEFAULT NULL,
  `hijos_estudiante` int(11) DEFAULT NULL,
  `situacion_especial_estudiante` varchar(100) DEFAULT 'Ninguna',
  `programa_estudiante` varchar(50) DEFAULT NULL,
  `curso_preferido` int(11) DEFAULT NULL,
  `jornada_preferida` varchar(100) DEFAULT NULL,
  `nombre_apoderado` varchar(150) DEFAULT NULL,
  `rut_apoderado` varchar(20) DEFAULT NULL,
  `parentezco_apoderado` varchar(50) DEFAULT NULL,
  `direccion_apoderado` varchar(255) DEFAULT NULL,
  `telefono_apoderado` varchar(15) DEFAULT NULL,
  `situacion_especial_apoderado` varchar(50) DEFAULT 'Ninguna',
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `estado` varchar(50) NOT NULL DEFAULT 'Pendiente',
  `estudiante_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `matriculas`
--

INSERT INTO `matriculas` (`id`, `nombre_estudiante`, `apellidos_estudiante`, `fecha_nacimiento`, `rut_estudiante`, `serie_carnet_estudiante`, `etnia_estudiante`, `direccion_estudiante`, `correo_estudiante`, `telefono_estudiante`, `hijos_estudiante`, `situacion_especial_estudiante`, `programa_estudiante`, `curso_preferido`, `jornada_preferida`, `nombre_apoderado`, `rut_apoderado`, `parentezco_apoderado`, `direccion_apoderado`, `telefono_apoderado`, `situacion_especial_apoderado`, `fecha_registro`, `estado`, `estudiante_id`) VALUES
(4, 'Jean Paul', 'Sotomayor Diaz', '2009-02-24', '22.957.711-5', '536.013.393', NULL, 'Belloto sxr Isla mocha', 'Jeanpoolsotomayor0@gmail.com', '935667727', NULL, 'Ninguna', NULL, 4, 'Mañana', 'Juan Pablo Sotomayor Perez', '13543535-k', NULL, 'Belloto sxr Isla mocha', '+56 9 8441 2711', 'Ninguna', '2025-12-01 22:36:45', 'Pendiente', NULL),
(6, 'jean paul', 'sotomayor diaz', '2009-02-24', '22.957.711-5', '536.013.393', NULL, 'Villa alemana', 'Jeanpoolsotomayor0@gmail.com', '935667727', NULL, 'Ninguna', NULL, 4, 'Mañana', 'Juan Pablo Sotomayor Perez', '13543535-k', NULL, 'Villa alemana', '+56 9 8441 2711', 'Ninguna', '2025-12-02 14:49:40', 'Pendiente', NULL),
(7, 'isidora agustina', 'flores ponce', '2008-06-18', '22.751.477-9', '515.911.577', '', 'esmeralda 201', 'isidoraagustinafloresponce@gmail.com', '931766036', 0, NULL, '0', 5, 'Mañana', 'elizabeth andrea ponce Lara', '9.918.517-1', NULL, 'esmeralda 201', '9 4526 8158', 'Ninguna', '2025-12-02 14:49:51', 'Pendiente', NULL),
(9, 'krishna anais', 'quezada arroyo', '2007-12-22', '22.585.263-4', 'b59.027.884', 'DIAGUITA', 'el abanderado 939', 'krishnaquezada07@gmail.com', '961672054', 0, NULL, '0', 5, 'Mañana', 'elisabeth beatriz arroyo aballay', '17807302', NULL, 'bernando leigthon 1550', '948677018', 'Ninguna', '2025-12-02 15:56:17', 'Pendiente', NULL),
(11, 'JESÚS IGNACIO', 'LÓPEZ SALAS', '2009-04-16', '23002633-5', '530788615', 'NO', 'PASAJE LOS CLARINES 1941 VILLA LOS JARDINES VILLA ALEMANA', 'jesus.lopez.salas11@gmail.com', '942289641', 0, NULL, '0', 1, '', 'KAREN SALAS TAPIA', '15099777-1', NULL, 'PASAJE LOS CLARINES 1941 VILLA LOS JARDINES VILLA ALEMANA', '66249952 / 4634', '', '2025-12-04 11:30:40', 'Pendiente', NULL),
(12, 'IGNACIA ANTONIA', 'NÚÑEZ MORALES', '2008-05-09', '22719700-5', '531504548', 'MAPUCHE', 'CAUPOLICAN 654 PEÑA BLANCA VILLA ALEMANA', 'ignacian.566@gmail.com', '940072372', 0, NULL, '0', 4, '', 'CYNTHIA MORALES HUAIQUIL', '17568768-8', NULL, 'CAUPOLICAN 654 PEÑA BLANCA VILLA ALEMANA', '966304698', 'NO', '2025-12-04 14:50:06', 'Pendiente', NULL),
(16, 'JAVIERA', 'PIZARRO BUSTAMANTE', '2008-03-21', '22.674.192-5', '534869195', 'Ninguna', 'RIO LONTUE1419 VILLA ALEMANA', 'Belenzbustamante22gmail.com', '989072616', NULL, 'NINGUNA', NULL, 1, 'Mañana', 'TATIANA ALEJANDRA BUSTAMANTE', '11827695-7', NULL, 'RIO LONTUE1419 VILLA ALEMANA', '961099922', 'Ninguna', '2025-12-06 13:43:58', 'Pendiente', NULL),
(17, 'JOUSSY', 'URRUTIA URRUTIA', '1991-10-20', '17.944.958-7', '524104536', 'Ninguna', 'LOURDES 807', 'jota.u.urrutia@gmail.com', '966119169', NULL, 'NINGUNA', NULL, 1, 'Mañana', 'FRANCISCA ELENA PRADO RIOS', '169717486', NULL, 'LOURDES 807', '938906886', 'Ninguna', '2025-12-08 21:17:10', 'Pendiente', NULL),
(18, 'SEBASTIÁN ESTEBAN', 'ARAOS GOMEZ', '1993-12-25', '18703013-7', '529681478', 'NO', 'PRIMERA 1011', 'sebastia.araos@gmail.com', '936116507', 0, 'Ninguna', 'NO', 3, 'Noche', '', '', '', '', '', 'Ninguna', '2025-12-09 10:03:25', 'Pendiente', NULL),
(19, 'PAULA ANTONELLA', 'MONTIEL CONCHA', '2009-01-16', '22931180-8', '', 'NO', 'AVENIDA JHON KENEDY 1849 VILLA ALEMANA', 'antitoo.mnt@gmail.com', '942833400', 0, 'Ninguna', 'NO', 4, 'Mañana', 'ROSA CONCHA MORENO', '11621907-7', 'Madre', 'AVENIDA JHON KENEDY 1849 VILLA ALEMANA', '932285473', 'Ninguna', '2025-12-09 11:20:07', 'Pendiente', NULL),
(20, 'NYCOLE ANDREA', 'BRUNEAU ALVARADO', '2008-07-25', '22780959-0', '521683471', 'Ninguna', 'BERNARDO OHIGGINS 380', 'carigoth@hotmail.com', '966973261', 0, NULL, '0', 1, '', 'CAROLINA ALVARADO', '17596083k', NULL, 'BERNARDO OHIGGINS 380', '966973261', 'Ninguna', '2025-12-09 15:21:55', 'Pendiente', NULL),
(21, 'MATÍAS', 'BORQUEZ BUSTAMANTE', '2005-02-03', '21.772.610-7', 'B5R.595.116', 'Ninguna', 'CARLOS SAAVEDRA 80# ECO VALLE 3', 'borkezmatiad22@gmail.com', '944368175', 0, NULL, '0', 8, '', 'MATÍAS ALEXANDER BORQUEZ BUSTAMANTE', '217726107', NULL, 'CARLOS SAAVEDRA 80# ECO VALLE 3', '944368175', 'Ninguna', '2025-12-10 19:22:47', 'Pendiente', NULL),
(22, 'MARCO ANTONIO', 'GAMBOA CASTILLO', '1997-10-04', '19.732.678-6', '516288419', 'Ninguna', 'SANTA SARA 1722 VILLA ALEMANA', 'arancibiapaloma499@gmail.com', '939730725', NULL, 'TRASTORNO ESPECÍFICO DEL APRENDIZAJE', NULL, 3, 'Noche', 'PALOMA ALMENDRA ARANCIBIA ESCOBEDO', '197909595', NULL, 'SANTA SARA 1722 VILLA ALEMANA', '9 39730725', 'Ninguna', '2025-12-10 21:59:27', 'Pendiente', NULL),
(23, 'MONSERRAT ANDREA', 'PEÑA ÁLVAREZ', '2009-03-30', '22991685-8', '525459803', 'NO', 'CALLE LONGITUDINAL 14 VILLA ALEMANA', 'monserratpena0830@gmail.com', '972713200', 0, 'Ninguna', 'NO', 4, 'Mañana', 'LORENA ÁLVAREZ SEGOVIA', '12623005-2', 'Madre', 'CALLE LONGITUDINAL 14 VILLA ALEMANA', '972284809', 'Ninguna', '2025-12-11 14:31:14', 'Pendiente', NULL),
(24, 'THIARE', 'VARGAS', '2005-05-15', '21.843.176-3', '536690232', 'Ninguna', 'DINAMARCA 1320 VILLA ALEMANA', 'thiare.vargas00@gmail.com', '+56985844316', NULL, 'NINGUNA', NULL, 4, 'Mañana', 'THIARE ESTEFANIA VARGAS VICENCIO', '21843176-3', NULL, 'DINAMARCA 1320 VILLA ALEMANA', '+56985844316', 'Ninguna', '2025-12-13 00:26:57', 'Pendiente', NULL),
(25, 'ANTONIA DENISSE', 'FREDES SILVA', '2008-12-31', '22915523-7', '525294420', 'NO', 'PASAJE ERNESTO CONCHA ALLENDE 1872 VILLA ALEMANA', 'antoniafredes11@gmail.com', '944135522', 0, 'Ninguna', 'NO', 4, 'Mañana', 'VICTOR SILVA SILVA', '7978320-K', 'Otro', 'AV. LAS PALMAS 0251 POBL. PRAT VILLA ALEMANA', '936272187', 'Ninguna', '2025-12-15 11:25:08', 'Pendiente', NULL),
(26, 'ESPERANZA PAZ', 'FIGUEROA CORDERO', '2003-11-20', '21.476.445-8', '528 886.583', 'Ninguna', 'RIO LIMARI 2686', 'esperanzaaa2003@gmail.com', '+56935730752', NULL, 'NINGUNA', NULL, 5, 'Mañana', 'CAMILA TRONCOSO MARINQUEZ', '17143129-8', NULL, 'ROMA 48 RECREATIVO VIÑA DEL MAR', '+56935972266', 'Ninguna', '2025-12-15 18:55:27', 'Pendiente', NULL),
(27, 'ALEJANDRO WILLIAMS', 'HALYBURTON HERNÁNDEZ', '2008-10-28', '22861022-4', '518442412', 'NO', 'PASAJE RIO AYSEN 1989 POBL. RIOS DEL SUR VILLA ALEMANA', '', '', 0, 'Ninguna', 'NO', 2, 'Tarde', 'PAULINA HERNÁNDEZ', '16330891-6', 'Madre', 'PASAJE RIO AYSEN 1989 POBL. RIOS DEL SUR VILLA ALEMANA', '979172570', 'Ninguna', '2025-12-16 12:17:03', 'Pendiente', NULL),
(28, 'ISIDORA VICTORIA', 'AROS AROS', '2009-09-06', '23119078-3', '521511281', 'NO', 'PATRICIO LYNCH 1260 VILLA ALEMANA', 'isidoraaros64@gmail.com', '986023454', 0, 'Programa Social', 'PIE GABRIELA MISTRAL', 8, 'Noche', 'CLAUDIA AROS', '16332256-0', 'Madre', 'PATRICIO LYNCH 1260 VILLA ALEMANA', '986023454', 'Ninguna', '2025-12-18 13:12:26', 'Pendiente', NULL),
(29, 'CLAUDIA FABIOLA', 'AROS VALENZUELA', '1986-12-01', '16332256-0', '110430255', 'NO', 'PATRICIO LYNCH 1260 VILLA ALEMANA', 'isidoraaros64@gmail.com', '986023454', 1, 'Ninguna', 'PIE GABRIELA MISTRAL', 8, 'Noche', '', '', '', '', '', 'Ninguna', '2025-12-18 13:15:48', 'Pendiente', NULL),
(30, 'NICOLÁS PATRICIO', 'GONZÁLEZ BERNAL', '1999-06-04', '20.270.694-0', '519383494', 'Ninguna', 'SEXTA 1265', 'nikolasgonza72@gmail.com', '945792471', NULL, 'NINGUNA', NULL, 1, 'Mañana', 'NICOLÁS PATRICIO GONZÁLEZ BERNAL', '202706940', NULL, 'SEXTA 1265', '945792471', 'Ninguna', '2025-12-21 00:32:55', 'Pendiente', NULL),
(31, 'MAGDALENA', 'LEONLEON', '2005-05-14', '22.349.623-7', '524212758', 'Ninguna', 'PAUL HARRIS 927', 'Leonmagdalenaamara@mail.com', '937549012', NULL, 'NINGUNA', NULL, 5, 'Mañana', 'MAGDALENA AMARA LEON LEON', '223496237', NULL, 'PAUL HARRIS 937', '937549012', 'Ninguna', '2025-12-21 12:43:39', 'Pendiente', NULL),
(32, 'MAGDALENA', 'AMARA LEON LEON', '2005-05-14', '22.349.623-7', '524.212.758', 'Ninguna', 'PAUL HARRIS 927', 'Leonmagdalenaamara@gmail.com', '937549012', 0, 'Ninguna', '', 4, NULL, 'MAGDALENA AMARA LEON LEON', '22349623-7', '', 'PAUL HARRIS 927', '937549012', 'Ninguna', '2025-12-21 12:47:36', 'Pendiente', NULL),
(33, 'SOFIA ANTONIA', 'BERRIOS BEECHEY', '2008-07-28', '22781228-1', 'B59614576', 'NO', 'BERNARDO LEIGHTON 54 PEÑA BLANCA', 'BERRIOSBEECHEY@GMAIL.COM', '933111131', 0, 'Ninguna', 'NO', 5, 'Mañana', 'ANA NICULCAR TOLEDO', '10184995-3', 'Otro', 'BERNARDO LEIGHTON 54', '993934599', 'Ninguna', '2025-12-22 10:53:53', 'Pendiente', NULL),
(34, 'DIEGO GIORDANO', 'VILLAON NICULCAR', '2008-11-24', '22882180-2', '527730849', 'MAPUCHE', 'BERNARDO LEIGHTON 54 PEÑA BLANCA', 'VILLALONDIEGO191@GMAIL.COM', '46648951', 0, 'Maternidad/Paternidad o Carga Familiar', 'NO', 5, 'Mañana', 'ANA NICULCAR TOLEDO', '10184995-3', 'Madre', 'BERNARDO LEIGHTON 54', '993934599', 'Ninguna', '2025-12-22 10:59:23', 'Pendiente', NULL),
(35, 'SEBASTIAN ALEJANDRO', 'VASQUEZ GUTIERREZ', '2009-04-22', '23003000-6', '536203726', 'NO', 'CALLE CENTRAL PONIENTE LOTE 1B TRONCOS VIEJOS', 'PASCALESPERANZA35@GMAIL.COM', '985598717', 0, 'Ninguna', 'PIE', 4, 'Mañana', 'NICOLE GUITERREZ ACEVEDO', '17464291-5', 'Madre', 'CALLE CENTRAL PONIENTE LOTE 1B TRONCOS VIEJOS', '985598717', 'Ninguna', '2025-12-22 12:33:39', 'Pendiente', NULL),
(36, 'MATIAS', 'ROMERO', '1997-09-02', '19.791.336-3', 'B61156208', 'Ninguna', 'PERU 1173, VILLA ALEMANA', 'varasdayenu816@gmail.com', '955248747', NULL, 'NINGUNA', NULL, 1, 'Mañana', 'DAYENU VARAS', '221199405', NULL, 'PERU 1173', '944083201', 'Ninguna', '2025-12-22 14:48:51', 'Pendiente', NULL),
(37, 'MILENCO JARET', 'PANTICH VASQUEZ', '2008-10-18', '22.849.323-6', 'B59103551', 'Otro', 'LAGO CHAPO#2510 POBL EL ALAMO VILLA ALEMANA', 'Milenkojaret@gmail.com', '+56932657075', 0, NULL, '0', 4, 'Mañana', 'TIARE VASQUEZ ARAOS', '15763035-0', NULL, 'LAGO CHAPO  N°2510', '+56978598091', 'Ninguna', '2025-12-22 20:30:53', 'Pendiente', NULL),
(38, 'SOFÍA IGNACIA', 'ARAYA URREA', '2008-02-21', '22663263-8', '517472765', 'NO', 'LOS APEROS 265 PEÑA BLANCA VILLA ALEMANA', 'urreasofia20@gmail.com', '944821088', 0, 'Ninguna', 'NO', 4, 'Mañana', 'CARLA URREA CALDERÓN', '15559844-1', 'Madre', 'LOS APEROS 265 PEÑA BLANCA VILLA ALEMANA', '942154621', 'Ninguna', '2025-12-23 11:51:49', 'Pendiente', NULL),
(39, 'DAMIÁN LEONARDO', 'LEÓN DE LA VEGA', '2009-06-20', '23058376-5', '525725462', 'NO', 'LOS BEDULES 702 TRONCOS VIEJOS VILLA ALEMANA', 'damianleon6611@gmail.com', '935053309', 0, 'Ninguna', 'NO', 6, 'Tarde', 'HELLEN DE LA VEGA', '15752794-0', 'Madre', 'LOS BEDULES 702 TRONCOS VIEJOS VILLA ALEMANA', '984595683', 'Ninguna', '2025-12-23 12:36:56', 'Pendiente', NULL),
(40, 'FERNANDA', 'SANCHEZ', '2009-07-01', '23.062.226-4', 'B5X241811', 'Ninguna', 'SAN RAFAE 2080', 'luzgalarce3@gmail.con', '997693404', 0, NULL, '0', 1, '', 'VERONICA GALARCE', '109271578', NULL, 'SAN RAFAEL 2080', '966776910', 'Ninguna', '2025-12-27 10:48:43', 'Pendiente', NULL),
(41, 'MARIANO', 'GARCIA GONZÁLEZ', '2008-05-10', '22.721.990-4', '536527244', 'Ninguna', 'CALLE VILLA ESMERALDA 1489', 'Mariano tde@gmail.com', '944141225', 0, NULL, '0', 4, '', 'SARA DE LAS MERCEDES', '12602486-k', NULL, 'CALLE VILLA ESMERALDA 1489', '947099833', 'Ninguna', '2025-12-28 16:00:11', 'Pendiente', NULL),
(42, 'JOSHUAN ALEXIS', 'PACHECO CASANUEVA', '2008-04-11', '22.696.442-8', '526824682', 'Ninguna', 'PABLOCION EL QUILLAI, PASAJE PAMPA DEL TAMARUGAL 2138', 'vemjofe@gmail.com', '955322795', 0, NULL, '0', 2, 'Tarde', 'MARCELA ANDREA CASANUEVA D ERY   / HUGO MELLA (SUPLENTE)', '185643549', NULL, 'PABLOCION EL QUILLAI, PASAJE PAMPA DEL TAMARUGAL 2138', '955322795 (SUPL', 'Ninguna', '2025-12-28 23:35:45', 'Pendiente', NULL),
(43, 'MARIO JAVIER', 'DIAZ', '1976-06-27', '21308302', '603685055', 'NO', 'SAN MARTIN 229 VILLA ALEMANA', 'mar.javierdiaz@gmail.com', '954802229', 1, 'Ninguna', 'NO', 1, 'Mañana', '', '', '', '', '', 'Ninguna', '2025-12-29 13:03:59', 'Pendiente', NULL),
(44, 'PATRICIO ALEJANDRO', 'SILVA FUENTES', '2008-06-20', '22749219-8', '', 'NO', 'BUENOS AIRES 1353 VILLA ALEMANA', '', '988689415', 0, 'Necesidades Educativas Especiales', 'PIE', 1, 'Mañana', 'AXEL PATRICIO SILVA OYARZO', '8329602-K', 'Padre', 'BUENOS AIRES 1353 VILLA ALEMANA', '988689415', 'Ninguna', '2025-12-29 13:24:20', 'Pendiente', NULL),
(45, 'MATHIAS IGNACIO', 'ALIAGA SEPÚLVEDA', '2007-08-26', '22494385-7', '', 'Ninguna', 'LAS ACACIAS 0235 CASA 32 PEÑA BLANCA VILLA ALEMANA', 'e.aliaga.quezada@gmail.com', '985282255', 0, 'Necesidades Educativas Especiales', 'NO', 5, 'Mañana', 'EDUARDO ALIAGA QUEZADA', '15762546-2', 'Padre', 'LAS ACACIAS 0235 CASA 32 PEÑA BLANCA VILLA ALEMANA', '985282255', 'Ninguna', '2025-12-30 10:42:05', 'Pendiente', NULL),
(46, 'MARTÍN ALONSO', 'ALMONACID NOVOA', '2008-05-11', '22718421-3', '', 'NO', 'PSJE. RÍO MAGDALENA 40 POBL. WILSON VILLA ALEMANA', 'piegabrielamistral.tripleta1@gmail.com', '958382335', 0, NULL, '0', 5, '', 'GINGER NOVOA / VALENTINA  LÓPEZ (988141485) TUTORA', '15714399-9', NULL, 'PSJE. RÍO MAGDALENA 40 POBL. WILSON VILLA ALEMANA', '930263459 / 988', 'Ninguna', '2025-12-30 10:49:30', 'Pendiente', NULL),
(47, 'ALEXANDER VINZENT', 'AMSTEINS ESCOBAR', '2008-03-24', '22676558-1', '', 'Ninguna', 'PSJE. DON VICENTE 0564 POBL. MORALEDA VILLA ALEMANA', '', '985855648', 0, 'Necesidades Educativas Especiales', 'NO', 5, 'Mañana', 'MARICEL ESCOBAR ANDÍA', '12879602-9', 'Madre', 'PSJE. DON VICENTE 0564 POBL. MORALEDA VILLA ALEMANA', '961902427', 'Ninguna', '2025-12-30 10:59:57', 'Pendiente', NULL),
(48, 'FLORENCIA AGUSTINA', 'BARRIENTOS QUIROGA', '2008-07-18', '22770738-0', '', 'NO', 'LOS DANIELES 1080 VILLA ALEMANA', '', '922013191', 1, 'Maternidad/Paternidad o Carga Familiar', '', 5, 'Mañana', 'SHARON QUIROGA ORTIZ', '16332791-0', 'Madre', 'LOS DANIELES 1080 VILLA ALEMANA', '923666833', 'Ninguna', '2025-12-30 11:04:47', 'Pendiente', NULL),
(49, 'LEANDRO MISAEL', 'BRAVO CAAMAÑO', '2008-04-15', '22698892-0', '', 'Ninguna', 'PSJE. UNO 0230 VILLA HIPÓDROMO VILLA ALEMANA', 'yocelync772@gmail.com', '936437191', 0, 'Ninguna', 'NO', 5, 'Mañana', 'YOCELYN CAAMAÑO', '17841622-7', 'Madre', 'PSJE. UNO 0230 VILLA HIPÓDROMO VILLA ALEMANA', '967544406', 'Ninguna', '2025-12-30 11:21:21', 'Pendiente', NULL),
(50, 'GABRIEL BENJAMÍN', 'CASTRO ARANCIBIA', '2007-01-11', '22306677-1', '', 'Ninguna', 'LOS CEREZOS 3155 VILLA ALEMANA', 'gavodospuntospe@gmail.com', '992533195', 0, 'Ninguna', 'NO', 5, 'Mañana', 'DANITZA ARANCIBIA VALENZUELA', '15070885-0', 'Madre', 'LOS CEREZOS 3155 VILLA ALEMANA', '969084694', 'Ninguna', '2025-12-30 11:27:03', 'Pendiente', NULL),
(51, 'ROSA ELENA', 'CONTRERAS ESPINOSA', '2004-05-07', '21569261-2', '', 'Ninguna', 'AV. PRIMERA 725 POBL. GUMERCINDO VILLA ALEMANA', 'rosicontreras654@gmail.com', '936880421', 0, 'Necesidades Educativas Especiales', 'NO', 5, 'Mañana', 'ROSA ESPINOSA JARA', '11520757-1', 'Madre', 'AV. PRIMERA 725 POBL. GUMERCINDO VILLA ALEMANA', '9368804219', 'Ninguna', '2025-12-30 11:32:34', 'Pendiente', NULL),
(52, 'TOMÁS', 'DELGADILLO PIÑA', '2008-06-12', '22746189-6', '', 'Ninguna', 'GONZÁLEZ 383 CERRO BARÓN VALPARAÍSO', '', '978005134', 0, 'Trastorno Específico del Aprendizaje', 'NO', 5, 'Mañana', 'CAROLINA PIÑA BETANCURT', '16755278-1', 'Madre', 'GONZÁLEZ 383 CERRO BARÓN VALPARAÍSO', '934184442', 'Ninguna', '2025-12-30 11:38:00', 'Pendiente', NULL),
(53, 'ISABELLA JUSTINA', 'FAJARDO ZUMELZU', '2008-06-14', '22743808-8', '', 'Ninguna', 'LA PLUMA  977 VILLA ALEMANA', 'alfonsinabelen36@gmail.com', '973696095', 0, 'Ninguna', 'NO', 4, NULL, 'ALFONSINA ZUMELZU', '15759293-9', 'Madre', 'LA PLUMA  977 VILLA ALEMANA', '973696095', 'Ninguna', '2025-12-30 11:47:14', 'Pendiente', NULL),
(54, 'MARTHINA ISIDORA', 'LIZAMA ARANCIBIA', '2007-05-15', '22399224-2', '', 'Ninguna', 'LAGO JANOSA 2675 VILLA ALEMANA', 'martinaconh@gmail.com', '927303413', 0, 'Ninguna', 'NO', 5, 'Mañana', 'PENÉLOPE ARANCIBIA VALENZUELA', '', 'Madre', 'LAGO JANOSA 2675 VILLA ALEMANA', '927303413', 'Ninguna', '2025-12-30 12:08:12', 'Pendiente', NULL),
(55, 'EMILIA IGNACIA', 'MÁRQUEZ DELLA CHIARA', '2009-01-23', '22928427-4', '', 'Ninguna', 'PSJE. DOS 420 VILLA TOLOUSSE VILLA ALEMANA', 'emiliamarquezd23@gmail.com', '994789472', 0, 'Condición de Salud Mental', 'NO', 5, 'Mañana', 'GINA DELLA CHIARA CUETO', '8552398-8', 'Madre', 'PSJE. DOS 420 VILLA TOLOUSSE VILLA ALEMANA', '942819280', 'Ninguna', '2025-12-30 12:14:26', 'Pendiente', NULL),
(56, 'JASON MATHIUS', 'MIRABAL SEPÚLVEDA', '2008-12-03', '22888546-0', '', 'Ninguna', 'UNIÓN 2303 LAS VEGAS VILLA ALEMANA', '', '976451287', 0, 'Necesidades Educativas Especiales', 'NO', 5, 'Mañana', 'GISSELE SEPÚLVEDA', '17275155-5', 'Madre', 'UNIÓN 2303 LAS VEGAS VILLA ALEMANA', '976451287', 'Ninguna', '2025-12-30 12:19:09', 'Pendiente', NULL),
(57, 'NICOLÁS JAVIER', 'MOLINA DE VASCONCELOS', '2008-11-19', '22877897-4', '', 'Ninguna', 'FEDERICO SANTA MARÍA 799 PEÑA BLANCA VILLA ALEMANA', '', '929758844', 0, 'Programa Social', '', 5, 'Mañana', 'LUIS MOLINA', '17118652-8', 'Padre', 'FEDERICO SANTA MARÍA 799 PEÑA BLANCA VILLA ALEMANA', '973172811', 'Ninguna', '2025-12-30 12:24:04', 'Pendiente', NULL),
(58, 'SEBASTIAN ANDRÉS', 'MOLINA DE VASCONCELOS', '2008-11-19', '22877913-K', '', 'Ninguna', 'FEDERICO SANTA MARÍA 799 PEÑA BLANCA VILLA ALEMANA', '', '929800555', 0, 'Programa Social', 'PIE GABRIELA MISTRAL', 5, 'Mañana', 'LUIS MOLINA', '17118652-8', 'Padre', 'FEDERICO SANTA MARÍA 799 PEÑA BLANCA VILLA ALEMANA', '973172811', 'Ninguna', '2025-12-30 12:34:55', 'Pendiente', NULL),
(59, 'LUKAS BENJAMÍN MAO-ZHIENG', 'PLAZA NAVARRETE', '2006-04-04', '22099037-0', '', 'Ninguna', 'PSJE. AZALEA PONIENTE 2797 VILLA ALEMANA', 'lukasplaza7@gmail.com', '937126321', 0, 'Ninguna', 'NO', 5, 'Mañana', 'DIEGO NEIRA NAVARRETE', '', 'Hermano/a', 'PSJE. AZALEA PONIENTE 2797 VILLA ALEMANA', '986431816', 'Ninguna', '2025-12-30 12:46:12', 'Pendiente', NULL),
(60, 'MARITZA ANDREA', 'ASTORGA CHAVÉZ', '1985-10-01', '16.105.416-K', 'B61.594.581', 'Ninguna', 'SANTA SARA, PASAJE AZULILLO 98', 'astorgachavezmaritza@gmail.com', '+56985127976', 0, NULL, '0', 1, '', 'NOAH ANNAÍS ASTORGA ASTORGA', '21325166k', NULL, 'SANTA SARA, PASAJE AZULILLO 98', '+56941629909', 'Ninguna', '2025-12-30 13:16:02', 'Pendiente', NULL),
(61, 'BASTIÁN ALEXANDER', 'MARDONES VIDAL', '2006-05-04', '22129179-4', 'B59870037', 'Ninguna', 'DEL VILLAR 1255 VILLA ALEMANA', 'vidalbastian134@gmail.com', '963059226', 0, 'Trastorno Específico del Aprendizaje', 'PIE', 8, 'Noche', 'ANDREA VIDAL', '13230123-9', 'Madre', 'DEL VILLAR 1255 VILLA ALEMANA', '961420788', 'Ninguna', '2025-12-30 14:09:53', 'Pendiente', NULL),
(62, 'MARIO ENRIQUE JESÚS', 'FLORES RAMÍREZ', '2006-05-20', '22738944-3', '525008848', 'Ninguna', 'TUCAPEL124  PEÑA BLANCA VILLA ALEMANA', 'themarioyt0134f0@gmail.com', '920467225', 0, NULL, '0', 4, '', 'CAROLINA CORTÉS/ CLARISA RAMÍREZ (994227565)', '13189955-6', NULL, 'TUCAPEL 115', '942049245', 'Ninguna', '2025-12-30 15:25:57', 'Pendiente', NULL),
(63, 'NICOLAS MAXIMILIANO', 'YAÑEZ GARCES', '2008-11-18', '22875609-1', '525.399.329', 'NO', 'CALLE VIA 1-7 2065 DEPTO E 33 VILLA ALEMANA', 'STEFANIA.IN@HOTMAIL.COM', '990146994', 0, 'Necesidades Educativas Especiales', 'PIE', 4, 'Mañana', 'INGRID STEFANIA GARCES CALDERON', '16813188-7', 'Madre', 'CALLE VIA 1-7 2065 DEPTO E 33 VILLA ALEMANA', '974411045', 'Ninguna', '2026-01-02 12:10:09', 'Pendiente', NULL),
(64, 'NAHUM ABRAHAM', 'CORTÉS TORREJÓN', '2008-01-02', '22.652.290-5', '521132248', 'Ninguna', 'CALLE CONGUILLIO', 'familiacortestorrejon@gmail.com', '910133286', NULL, 'NINGUNA', NULL, 4, 'Mañana', 'ROXANA ANGÉLICA TORREJÓN PEÑA', '114022985', NULL, 'TORREJONROXANA3@GMAIL.COM', '939700737', 'Ninguna', '2026-01-03 12:01:25', 'Pendiente', NULL),
(65, 'TOMÁS JOAQUIN', 'HUENCHUAN GUZMÁN', '2008-12-22', '22.920.558-7', '525751466', 'Mapuche', 'ALMIRANTE WILSON 831 PEÑABLANCA', 'tomatekoaquin@gmail.com', '+569867056814', NULL, 'NINGUNA', NULL, 1, 'Mañana', 'YERDOLINA DACNUVIA GUZMÁN GUZMÁN', '15275990-8', NULL, 'ALMIRANTE WILSON 831 PEÑABLANCA', '+56976030697', 'Ninguna', '2026-01-03 17:23:59', 'Pendiente', NULL),
(66, 'CRISTIAN SEBASTIÁN', 'PÉREZ VILCHES', '1996-06-08', '19193582-9', '', 'Ninguna', 'FEDERICO SANTA MARÍA 799 PEÑA BLANCA VILLA ALEMANA', '', '973172811', 0, 'Ninguna', 'NO', 5, 'Mañana', '', '', '', '', '', 'Ninguna', '2026-01-05 10:48:44', 'Pendiente', NULL),
(67, 'DANITZA ALBINA', 'PESCE PACHECO', '2009-06-07', '23046919-9', '', 'NO', 'LAS ROSAS 891 VILLA ALEMANA', '', '961883036 (DASHLA PACHECO)', 0, 'Programa Social', 'PIE GABRIELA MISTRAL', 5, 'Mañana', 'BÁRBARA ÁLVAREZ 942453728 (TUTORA) PROGRAMA MARCELA PAZ 222114809', '17144521-3', '', 'LAS ROSAS 891 VILLA ALEMANA', '961883036', 'Ninguna', '2026-01-05 10:57:14', 'Pendiente', NULL),
(68, 'SEBASTIÁN IGNACIO', 'RUBIO ORDTENES', '2009-01-13', '22924080-3', '', 'Ninguna', 'SAN ENRIQUE  1780 BLOCK 4 DEPTO. 203 PSJA. 3 VILLA ALEMANA', '', '94491175', 0, 'Programa Social', 'CHILE SOLIDARIO', 5, 'Mañana', 'CAROLINA ORDTENES', '15098793-8', 'Madre', 'SAN ENRIQUE  1780 BLOCK 4 DEPTO. 203 PSJA. 3 VILLA ALEMANA', '963426610', 'Ninguna', '2026-01-05 11:03:13', 'Pendiente', NULL),
(69, 'MARÍA -IGNACIA', 'SOLIZ BENVENUTO', '2009-08-17', '23103053-0', '', 'Ninguna', 'DIAZ 1187 VILLA ALEMANA', 'solizbenvenutomariaignacia@gmail.com', '94576034', 0, 'Condición de Salud Mental', 'NO', 5, 'Mañana', 'GABRIELA BENVENUTO', '15074650-7', 'Madre', 'DIAZ 1187 VILLA ALEMANA', '998811127', 'Ninguna', '2026-01-05 11:44:16', 'Pendiente', NULL),
(70, 'MACARENA ALEJANDRA', 'TAPIA VERGARA', '1999-05-01', '20023515-0', '', 'Ninguna', 'ERRAZURIZ VILLA ALEMANA', 'macarenayrosa@hotmail.com', '933644553', 1, 'Maternidad/Paternidad o Carga Familiar', 'NO', 5, 'Mañana', '', '', '', '', '', 'Ninguna', '2026-01-05 11:52:01', 'Pendiente', NULL),
(71, 'JOSE THOMAS', 'VERGARA MORA', '2007-02-10', '22327350-5', '', 'Ninguna', 'SUECIA 2303 POBL. LAS VEGAS VILLA ALEMANA', '', '974569366', 0, 'Ninguna', 'NO', 5, 'Mañana', 'GISSELE SEPÚLVEDA', '17275155-5', 'Otro', 'SUECIA 2303 POBL. LAS VEGAS VILLA ALEMANA', '976451287', 'Ninguna', '2026-01-05 12:18:35', 'Pendiente', NULL),
(72, 'BASTIÁN EXEQUIEL ALEJANDRO', 'ZÚÑIGA ARACENA', '2007-08-31', '22505358-8', '', 'Ninguna', 'DINAMARCA 1250 BLOCK  30 DEPTO 302 VILLA ALEMANA', '', '988844955', 0, 'Trastorno Específico del Aprendizaje', 'NO', 5, 'Mañana', 'JESSICA ARACENA ROJAS', '15763227-2', 'Madre', 'DINAMARCA 1250 BLOCK  30 DEPTO 302 VILLA ALEMANA', '972931866', 'Ninguna', '2026-01-05 12:24:58', 'Pendiente', NULL),
(73, 'DANIEL YSIDRO', 'BERMUDEZ FERNÁNDEZ', '2008-12-22', '28395036-0', '', 'Ninguna', 'GABRIEL DAZAROLA 1024 POBL. GUMERCINDO VILLA ALEMANA', '', '944121271', 0, 'Ninguna', 'NO', 1, 'Mañana', 'ELIZABETH ÁLVAREZ CASTILLO', '9374111-0', 'Otro', 'GABRIEL DAZAROLA 1024 POBL. GUMERCINDO VILLA ALEMANA', '87852213', 'NO', '2026-01-05 14:41:39', 'Pendiente', NULL),
(74, 'KIARA SOLEDAD', 'HENRÍQUEZ REUCAN', '2009-04-14', '22996949-8', 'B5J926954', 'Mapuche', 'GABRIEL DAZAROLA 1024 POBL. GUMERCINDO VILLA ALEMANA', 'kiarahenriquez707@gmail.com', '932796339', 0, 'Ninguna', 'NO', 1, 'Mañana', 'ELIZABETH ÁLVAREZ CASTILLO', '9374111-0', 'Madre', 'GABRIEL DAZAROLA 1024 POBL. GUMERCINDO VILLA ALEMANA', '87852213', 'NO', '2026-01-05 14:49:47', 'Pendiente', NULL),
(75, 'MONSERRAT ZAHARA', 'VILLEGAS LAGOS', '2008-07-15', '22.781.568-K', 'B5J773091', 'Ninguna', 'LOS ESPINOS J8, PARCELAS CAJON DE LEBU', 'macsiaddd@gmail.com', '950955658', NULL, 'NINGUNA', NULL, 8, 'Noche', 'NATALY ANDREA LAGOS VALENCIA', '16.967.798-0', NULL, 'LOS ESPINOS J8, PARCELAS CAJON DE LEBU', '963472452', 'Ninguna', '2026-01-05 14:56:36', 'Pendiente', NULL),
(76, 'ASMAA ALI', 'ANEIZAN', '2006-01-01', '25942081-4', '', 'Ninguna', 'IGNACIO CARRERA PINTO 0650CONDOMINIO VALLE DE LA LUNA 1 V.A.', 'asmaaneizan@gmail.com', '920603011', 0, 'Ninguna', 'NO', 7, NULL, 'ZEINA ALSALAMI', '', 'Madre', 'IGNACIO CARRERA PINTO 0650CONDOMINIO VALLE DE LA LUNA 1 VILLA ALEMANA', '920603011', '', '2026-01-06 11:25:02', 'Pendiente', NULL),
(77, 'HUSSEIN ALI', 'ANEIZAN', '2008-01-01', '25942095-4', '', 'Ninguna', 'IGNACIO CARRERA PINTO 0650CONDOMINIO VALLE DE LA LUNA 1 V.A.', 'asmaaneizan@gmail.com', '937674024', 0, 'Ninguna', 'NO', 7, 'Tarde', 'ASMAA ANEIZAN', '', 'Otro', 'IGNACIO CARRERA PINTO 0650CONDOMINIO VALLE DE LA LUNA 1 VILLA ALEMANA', '920603011', 'NO', '2026-01-06 11:34:17', 'Pendiente', NULL),
(78, 'ALEXANDER ISAY', 'ARMIJO VIDELA', '2009-02-16', '22952794-0', '', 'Ninguna', 'ANTIGUO CAMINO CARRETERO PSJE. 2 CASA 21 B V.A.', 'pri.videla90@gmail.com', '974491937', 0, 'Condición de Salud Mental', 'NO', 7, 'Tarde', 'PRISCILLA VIDELA', '17567175-7', 'Madre', 'ANTIGUO CAMINO CARRETERO PSJE. 2 CASA 21 B V.A.', '946547132', 'NO', '2026-01-06 11:42:32', 'Pendiente', NULL),
(79, 'ARIANNA DECIRET', 'OSORIO MORA', '2006-11-01', '28374508-2', '603852827', 'Ninguna', 'PSJE. G 2350 BL. H VILLA EL ROCIO VILLA ALEMANA', 'arianna.osorio.2022@gmail.com', '966544670', 1, 'Ninguna', 'mejor niñez valparaiso', 4, 'Mañana', 'ARIANNA DECIRET OSORIO MORA', '28374508-2', '', 'PSJE. G 2350 BL. H VILLA EL ROCIO VILLA ALEMANA', '923622730', 'luis manuel burgos (pareja)', '2026-01-06 12:11:09', 'Pendiente', NULL),
(80, 'KRISTEL ANDREA', 'REMEDI GALAZ', '1983-06-15', '15713994-0', '524473629', 'Ninguna', 'AVIADOR FIGUEROA 921 VILLA ALEMANA', 'kristelremedigalaz18@gmail.com', '979302849', 4, 'Ninguna', 'NO', 3, 'Noche', 'LEANDRO VILLAGRÁN', '', 'Otro', 'AVIADOR FIGUEROA 921 VILLA ALEMANA', '943404432', '', '2026-01-06 13:55:30', 'Pendiente', NULL),
(81, 'ESTRELLA BELÉN ANAÍS', 'ARREGUI FARÍAS', '2008-12-15', '22905873-8', '', 'Ninguna', 'TRONCAL SUR 28 LOMAS DE BELLAVISTA VILLA ALEMANA', '', '966828758', 0, 'Condición de Salud Mental', '', 7, 'Tarde', 'JENY FARÍAS FUENTES', '13634188-K', 'Madre', 'TRONCAL SUR 28 LOMAS DE BELLAVISTA VILLA ALEMANA', '939702758 / 952', 'JOAN MENA 21969742 (HERMANO)', '2026-01-06 15:09:53', 'Pendiente', NULL),
(82, 'RICARDO ANDRÉS', 'BAEZA CANCINO', '2008-05-19', '22729122-2', '', 'Ninguna', 'LA JARILLA 83 VILLA ALEMANA', '', '', 0, 'Programa Social', 'PIE GABRIELA MISTRAL', 7, 'Tarde', 'ARACELYS ABURTO CANCINO', '20316472-6', 'Hermano/a', '11 PONIENTE 5671 VIÑA DEL MAR', '978825251', 'NO', '2026-01-06 15:17:21', 'Pendiente', NULL),
(83, 'MAYLIN BELÉN', 'BARLARI GONZÁLEZ', '2008-07-05', '22762825-1', '', 'Ninguna', 'LAS ACACIAS CALLE LUCUMILLO 303 VILLA ALEMANA', '', '929606047', 1, 'Maternidad/Paternidad o Carga Familiar', 'NO', 7, 'Tarde', 'ELIZABETH GONZÁLEZ', '13653077-1', 'Madre', 'LAS ACACIAS CALLE LUCUMILLO 303 VILLA ALEMANA', '922138756', 'NO', '2026-01-06 15:26:51', 'Pendiente', NULL),
(84, 'KEVIN ALEJANDRO', 'BASUALTO ZAMORA', '2008-02-23', '22651348-5', '', 'Ninguna', 'PUMALIN 2178 VILLA ALEMANA', 'barbaraalejandrazamora@gmail.com', '983032396', 0, 'Necesidades Educativas Especiales', 'PIE', 7, 'Tarde', 'BARBARA ZAMORA', '15556981-6', 'Madre', 'PUMALIN 2178 VILLA ALEMANA', '983032396', 'CLAUDIO BASUALTO 949051822 (PADRE)', '2026-01-06 15:47:09', 'Pendiente', NULL),
(85, 'MIGUEL ANGEL', 'BRINCKFELDT MÉNDEZ', '2008-03-31', '22686371-0', '', 'Ninguna', 'TUAMAPU 560 PEÑA BLANCA VILLA ALEMANA', 'brokken545@icloud.com', '983339035', 1, 'Maternidad/Paternidad o Carga Familiar', 'LAZOS', 7, 'Tarde', 'SANDRA ZÚÑIGA LÍBANO', '10164830-3', 'Otro', 'TUAMAPU 560 PEÑA BLANCA VILLA ALEMANA', '989622897', 'MARCELO SEPÚLVEDA (LAZOS) 968260353', '2026-01-07 11:16:01', 'Pendiente', NULL),
(86, 'JORDAN ALEXIS', 'DÍAZ MAGNA', '2005-08-19', '21922033-2', '', 'Ninguna', 'LOS ZAPADORES 1636 BLOCK 16 DEPTO. 103 POBL. SAN JOSÉ V.A.', 'politamagna22@gmail.com', '957949771', 0, 'Condición de Salud Mental', 'NO', 7, 'Tarde', 'JASNA MAGNA', '12719206-5', 'Madre', 'LOS ZAPADORES 1636 BLOCK 16 DEPTO. 103 POBL. SAN JOSÉ V.A.', '942969320', 'NO', '2026-01-07 11:23:55', 'Pendiente', NULL),
(87, 'GLORIA XIMENA', 'MELIPIL GUTIÉRREZ', '1979-08-24', '13856460-6', '', 'Mapuche', 'SANTA FE 1380 VILLA ALEMANA', 'gloriamelipil@gmail.com', '982353133', 2, 'Maternidad/Paternidad o Carga Familiar', 'NO', 7, 'Tarde', 'GLORIA XIMENA  MELIPIL GUTIÉRREZ', '', '', '', '', '', '2026-01-07 11:56:06', 'Pendiente', NULL),
(88, 'FABIÁN ANDRÉ', 'OPAZO SAAVEDRA', '2007-06-30', '22434842-8', '', 'Ninguna', 'ANDES 030 PEÑA BLANCA VILLA ALEMANA', 'malussa_66@hotmail.com', '933992107', 0, 'Necesidades Educativas Especiales', 'NO', 7, 'Tarde', 'MARTA SAAVEDRA', '', 'Madre', 'ANDES 030 PEÑA BLANCA VILLA ALEMANA', '933992107', 'FRANCO OPAZO 983440928', '2026-01-07 12:03:11', 'Pendiente', NULL),
(89, 'CATALINA ANTONELLA', 'ORELLANA CARMONA', '2009-12-23', '23209073-1', '', 'Ninguna', 'SANTA ESTELA 23 VALPARAÍSO', '', '920716613', 0, 'Trastorno Específico del Aprendizaje', 'PSICÓLOGA', 7, 'Tarde', 'EVELYN CARMONA', '15753135-2', 'Madre', 'SANTA ESTELA 23 VALPARAÍSO', '996726969', 'NO', '2026-01-07 12:33:27', 'Pendiente', NULL),
(93, 'ANDY AMBER', 'CISTERNA LABARCA', '2008-04-09', '22697618-3', '536843951', 'Ninguna', 'SANTA SARA 1140 VILLA ALEMANA', '', '988141485', 0, 'Vulnerabilidad/Violencia', 'PIE GABRIELA MISTRAL', 1, 'Mañana', 'CAMILO GÁRATE', '', 'Hermano/a', 'SANTA SARA 1140 VILLA ALEMANA', '', 'VALENTINA LÓPEZ  988141485  (PIE GABRIELA MISTRAL)', '2026-01-08 10:58:50', 'Pendiente', NULL),
(94, 'JOSÉ MANUEL ANTONIO', 'OYARZÚN OLGUÍN', '2008-03-06', '22664003-7', '', 'Ninguna', '', '', '', 0, 'Ninguna', '', 7, 'Tarde', 'CLAUDIA OLGUÍN', '', '', '', '992104502', '', '2026-01-08 12:06:33', 'Pendiente', NULL),
(95, 'SAM', 'POLANCO REYES', '2008-08-08', '22790180-2', '', 'Ninguna', 'HERNAN TORRES 541', 'erika.eyes8@gmail.com', '972148879', 0, 'Condición de Salud Mental', 'APOYO SOCIO EMOCIONAL', 7, NULL, 'ERIKA REYES', '15609139-1', 'Madre', 'HERNAN TORRES 541', '972148879', 'TUTORA MARÍA JOSÉ OLIVARES', '2026-01-08 12:16:45', 'Pendiente', NULL),
(96, 'MILLARAY BELÉN', 'SOTO CALZADA', '2004-09-06', '21659039-2', '', 'Ninguna', 'CARLOS CONDELL 266 VILLA ALEMANA', '', '979733526', 0, 'Enfermedad Crónica/Grave', 'NO', 7, 'Tarde', 'ALICIA CALZADA VARAS', '12851653-0', 'Madre', 'CARLOS CONDELL 266 VILLA ALEMANA', '992602165', '', '2026-01-08 12:24:35', 'Pendiente', NULL),
(97, 'MONTSERRAT ESPERANZA', 'VELÁSQUEZ LOBOS', '2006-03-16', '22146704-3', '', 'Ninguna', 'LA CUMBRE 1168 BELLOTO SUR QUILPUÉ', 'monserratdavid5@gmail.com', '993575492', 1, 'Maternidad/Paternidad o Carga Familiar', 'NO', 7, 'Tarde', 'CELINDA LOBOS TAPIA', '6306535-8', 'Otro', 'LA CUMBRE 1168 BELLOTO SUR QUILPUÉ', '322337800', 'CONSTANZA VELÁSQUEZ (HERMANA) 994166744', '2026-01-08 12:38:01', 'Pendiente', NULL),
(98, 'NEYTHAN ELÍAS', 'VICTORIANO VÁSQUEZ', '2008-10-16', '22844610-6', '', 'Ninguna', 'CALLE VILLA ESMERALDA 1491', '', '978876516', 0, 'Programa Social', 'PIE GABRIELA MISTRAL', 7, 'Tarde', 'JOHANNA VÁSQUEZ SUÁREZ', '15079138-3', 'Madre', 'CALLE VILLA ESMERALDA 1491', '978876516', 'PIE GABRIELA MISTRAL', '2026-01-08 12:43:32', 'Pendiente', NULL),
(99, 'JAVIER IGNACIO', 'ZULETA ÁLVAREZ', '2005-12-14', '22000269-1', '', 'Ninguna', 'PASAJE RÍO LOA 512 VILLA ALEMANA', 'zuletajavier2005@gmail.com', '940256167', 0, 'Trastorno Específico del Aprendizaje', 'NO', 7, 'Tarde', 'ALEJANDRA ÁLVAREZ', '14613062-3', 'Madre', 'PASAJE RÍO LOA 512 VILLA ALEMANA', '968394084', 'NO', '2026-01-08 12:48:34', 'Pendiente', NULL),
(100, 'BELÉN', 'ROMERO ROJAS', '2004-01-24', '21.496.517-8', '536736588', 'Ninguna', 'MARCELINO CHAMPAGÑAT 1500', 'Belenrojas475@gmail.com', '76644679', NULL, 'MATERNIDAD/PATERNIDAD O CARGA FAMILIAR', NULL, 7, 'Tarde', 'BELÉN ANTONELLA ROMERO ROJAS', '214965178', NULL, 'MARCELINO CHAMPAGÑAT 1500', '76644679', 'Ninguna', '2026-01-08 14:14:21', 'Pendiente', NULL),
(101, 'MONSERRAT TRINIDAD', 'CORREA BONVALLET', '2010-10-09', '23294801-9', 'B5M197720', 'Ninguna', 'LAS TORTOLAS 2136 VILLA EL ROCIO V.A.', '', '910152551', 0, 'Programa Social', 'PRM TAHIEL', 2, 'Tarde', 'CLARA BONVALLET VALENZUELA', '13049700-4', 'Madre', 'LAS TORTOLAS 2136 VILLA EL ROCIO V.A.', '910152550', '', '2026-01-09 09:06:15', 'Pendiente', NULL),
(102, 'CATALINA', 'ORELLANA CARMONA', '2009-12-23', '23.209.073-1', '512140471', 'Ninguna', 'SANTA ESTELA 23', 'evellita.28@gmail.com', '996726969', NULL, 'NECESIDADES EDUCATIVAS ESPECIALES', NULL, 6, 'Tarde', 'EVELYN CARMONA MANCILLA', '15.753.135-2', NULL, 'SANTA ESTELA 23', '996726969', 'Ninguna', '2026-01-11 12:06:02', 'Pendiente', NULL),
(103, 'JANNY FRANCISCA', 'PAREDES VERGARA', '2001-03-06', '20.724.951-3', '522372124', 'Ninguna', 'PASAJE LAS PALMAS 23, AMPLIACIÓN PRAT', 'jajannyfrancisca24@gmial.com', '950346230', NULL, 'NINGUNA', NULL, 8, 'Noche', 'MARCELO BARRIENTOS', '19471470-k', NULL, 'PASAJE LAS PALMAS 23 AMPLIACIÓN PRAT', '987142064', 'Ninguna', '2026-01-11 23:04:28', 'Pendiente', NULL),
(104, 'JANNY FRANCISCA', 'PAREDES VERGARA', '2001-03-06', '20.724.951-3', '522372124', 'Ninguna', 'PASAJE LAS PALMAS 23, AMPLIACIÓN PRAT', 'jajannyfrancisca24@gmial.com', '950346230', NULL, 'NINGUNA', NULL, 8, 'Noche', 'MARCELO BARRIENTOS', '19471470-k', NULL, 'PASAJE LAS PALMAS 23 AMPLIACIÓN PRAT', '987142064', 'Ninguna', '2026-01-11 23:04:45', 'Pendiente', NULL),
(105, 'MARTIN', 'TENORIO  LEON', '2009-08-22', '231051414', '527852982', 'Ninguna', 'LOS MAKIS G8 CAJON DE LEBU', 'Martin.tenorio222009@gmail.com', '981416932', NULL, 'NINGUNA', NULL, 2, 'Tarde', 'CRISTIAN TENORIO', '142830523', NULL, 'LOS MAKIS G8 CAJON DE LEBU', '988092738', 'Ninguna', '2026-01-13 23:22:03', 'Pendiente', NULL),
(106, 'NATALY', 'CARTES', '2003-11-11', '214407396', '527520593', 'Ninguna', 'COLÓ COLO450', 'Natalycartes99@gmail.com', '945459571', NULL, 'MATERNIDAD/PATERNIDAD O CARGA FAMILIAR', NULL, 2, 'Tarde', 'NATALY ANDREA CARTES', '214407396', NULL, 'COLÓ COLÓ 540', '945459571', 'Ninguna', '2026-01-17 17:30:39', 'Pendiente', NULL),
(107, 'ELIZABETH', 'BOLADOS PINONES', '1979-12-18', '141105760', '520968045', 'Ninguna', 'NUEVA  HIPODROMO 975', 'bolados.elizabeth@gmail.com', '999335178', NULL, 'NINGUNA', NULL, 3, 'Noche', 'ELIZABETH', '141105760', NULL, 'NUEVA HIPÓDROMO 975', '99335178', 'Ninguna', '2026-01-18 17:09:00', 'Pendiente', NULL),
(108, 'JOSHUA IGNACIO', 'BARRIENTOS VALDES', '2002-11-25', '211854685', 'B59211517', 'Ninguna', 'SANTA FILOMENA 1133, MARÍA MERCEDES', 'Barrientos.marcelo2806@gmail.com', '968228957', NULL, 'NINGUNA', NULL, 3, 'Noche', 'MARCELO BARRIENTOS', '19471470-k', NULL, 'PASAJE LAS PALMAS 23, AMPLIACIÓN PRAT', '987142064', 'Ninguna', '2026-01-19 12:49:10', 'Pendiente', NULL),
(109, 'GAMAL FERNANDO', 'PLAZA VARGAS', '2007-10-10', '225239428', '536009740', 'Ninguna', 'MARQUE 1133', 'Plazagamal09@gmail.com', '954830428', NULL, 'NINGUNA', NULL, 2, 'Tarde', 'LESLIE MARIA', 'Vargas fernandez', NULL, 'MARQUE 1133', '967863465', 'Ninguna', '2026-01-19 15:37:36', 'Pendiente', NULL),
(110, 'DELFINA KATHERINE', 'MOLINA ARAYA', '1983-12-21', '157148478', '534376599', 'Ninguna', 'PASAJE CINCO 373 VILLA HIPODROMO, VILLA ALEMANA', 'Kata.kino29@gmail.com', '953255268', NULL, 'MATERNIDAD/PATERNIDAD O CARGA FAMILIAR', NULL, 2, 'Tarde', 'ANDREA VAITIARE ORTIZ MOLINA', '206889918', NULL, 'PASAJE CINCO 373 VILLA HIPODROMO, VILLA ALEMANA', '9 4697 4512', 'Ninguna', '2026-01-26 21:58:51', 'Pendiente', NULL),
(111, 'FELIPE ALONSO', 'GUTIÉRREZ MOYA', '2008-08-04', '227987413', 'B69013848', 'Ninguna', 'SARGENTO ALDEA 440 LIMACHE', 'yanitzamoya85@gmail.com', '943440963', NULL, 'NINGUNA', NULL, 2, 'Tarde', 'JANITZA ESTEPHANIE MOYA OLIVARES', '157141724', NULL, 'SARGENTO ALDEA 440 LIMACHE', '943440963', 'Ninguna', '2026-01-27 10:37:21', 'Pendiente', NULL),
(112, 'BRENDA NÚÑEZ', 'NÚÑEZ', '1984-01-12', '157619403', '534424668', 'Ninguna', 'PASAJE SANTA EMILIA #466', 'pascal14lazcano@gmail.com', '932648945', NULL, 'NINGUNA', NULL, 2, 'Tarde', 'BRENDA', '157619403', NULL, 'PASAJE SANTA EMILIA #466', '932648945', 'Ninguna', '2026-01-27 20:19:34', 'Pendiente', NULL),
(113, 'FELIPE BENJAMIN', 'VARAS LOPEZ', '2008-01-29', '22.629.778-2', '534956894', 'Ninguna', 'AVIADOR FIGUEROA 1321 CASA 8 VILLA ALEMANA', 'silvamorales.scarlett@gmail.com', '978573809', NULL, 'NINGUNA', NULL, 6, 'Tarde', 'SCARLETT ANDREA SILVA MORALES', '17.142.952-8', NULL, 'AVIADOR FIGUEROA 1321 CASA 8 VILLA ALEMANA', '968544791', 'Ninguna', '2026-01-30 13:17:13', 'Pendiente', NULL),
(114, 'TOMÁS', 'DELGADILLO PIÑA', '2008-06-12', '22746189-6', NULL, NULL, 'NELSON Nº 283. Cº BARON', '', '', NULL, 'Ninguna', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 1),
(115, 'MATHIAS IGNACIO', 'ALIAGA SEPÚLVEDA', '2007-08-26', '22494385-7', NULL, NULL, 'BRISAS DEL MAR DEP.21 BLOK 75', '', '2862378', NULL, 'Ninguna', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 2),
(116, 'DANITZA ALBINA', 'PESCE PACHECO', '2009-06-07', '23046919-9', NULL, NULL, 'SIMÓN ALAMOS', '', '', NULL, 'Ninguna', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 3),
(117, 'VICENTE ANTONIO', 'ARÁNGUIZ BORZONE', '2007-06-02', '22411258-0', NULL, NULL, 'LOS PLÁTANOS 3142 BELLOTO NORTE', 'MACARENABORZONE@GMAIL.COM', '', NULL, 'Ninguna', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 4),
(118, 'AYLEEN SALOMÉ', 'ARAYA BESARES', '2007-06-21', '22430261-4', NULL, NULL, 'ESMERALDA', '', '927211167', NULL, 'Ninguna', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 5),
(119, 'ISABELLA JUSTINA', 'FAJARDO ZUMELZU', '2008-06-14', '22743808-8', NULL, NULL, 'LOS ALMENDROS', 'VFAJARDORIVERO2@GMAIL.COM', '', NULL, 'Ninguna', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 6),
(120, 'JOSÉ THOMAS', 'VERGARA MORA', '2007-02-10', '22327350-5', NULL, NULL, 'LINCOYAN 1410 BLOCK D  DEPTO. 44 BELLOTO NORTE', 'JOSE.VERGARA@BARROSARANA.CMVA.CL', '3215027', NULL, 'Ninguna', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 7),
(121, 'FLORENCIA AGUSTINA', 'BARRIENTOS QUIROGA', '2008-07-18', '22770738-0', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 8),
(122, 'TONKA LEONOR', 'GARCÉS BALBOA', '2007-06-11', '22418770-K', NULL, NULL, 'MADRE SELVA 1994 VILLA LOS JARDINES', '', '', NULL, 'Ninguna', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 9),
(123, 'ROSA ELENA', 'CONTRERAS ESPINOSA', '2004-05-07', '21569261-2', NULL, NULL, 'AVENIDA PRIMERA', '', '', NULL, 'Ninguna', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 10),
(124, 'ALAN JESÚS', 'NAVARRETE PACHECO', '2008-02-26', '22670364-0', NULL, NULL, 'LA SERENA 1120 POBLACIÓN  ROSENQUIST', '', '51238138', NULL, 'Ninguna', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 11),
(125, 'MACARENA ALEJANDRA', 'TAPIA VERGARA', '1999-05-01', '20023515-0', NULL, NULL, 'POBLACION NUEVA ESPERANZA', '', '', NULL, 'Ninguna', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 12),
(126, 'MARIA-IGNACIA', 'SOLIZ BENVENUTO', '2009-08-17', '23103053-0', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 13),
(127, 'MARTHINA ISIDORA', 'LIZAMA ARANCIBIA', '2007-05-15', '22399224-2', NULL, NULL, 'LAGOS JANOSA', 'PENELOPEARANCIBIA@GMAIL.COM', '955343743', NULL, 'Ninguna', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 14),
(128, 'EMILIA IGNACIA', 'MÁRQUEZ DELLA CHIARA', '2009-01-23', '22928427-4', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 15),
(129, 'ALEXANDER VINZENT', 'AMSTEINS ESCOBAR', '2008-03-24', '22676558-1', NULL, NULL, 'HACIENDA SAN ISIDRO, EL PEUMO 48 PEÑA BLANCA', '', '', NULL, 'Ninguna', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 16),
(130, 'GABRIEL BENJAMÍN', 'CASTRO ARANCIBIA', '2007-01-13', '22306677-1', NULL, NULL, 'LOS AREZOS 3155 POB. ALBATROS', 'GABRIEL.CASTRO@COLEGIONACIONAL.CL', '2406679', NULL, 'Ninguna', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 17),
(131, 'JASON MATHIUS', 'MIRABAL SEPÚLVEDA', '2008-12-03', '22888546-0', NULL, NULL, 'UNION', '', '', NULL, 'Ninguna', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 18),
(132, 'LUKAS BENJAMÍN MAO-ZHIENG', 'NAVARRETE PLAZA', '2006-04-04', '22099037-0', NULL, NULL, 'PJE AZALEA PONIENTE 2797', 'LUKAS.PLAZA@COLEGIONACIONAL.CL', '2657511', NULL, 'Ninguna', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 19),
(133, 'SEBASTIÁN IGNACIO', 'RUBIO ORDTENES', '2009-01-13', '22924080-3', NULL, NULL, 'SAN ENRIQUE, BLOCK 4', '', '963426610', NULL, 'Ninguna', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 20),
(134, 'BASTIÁN EXEQUIEL ALEJANDRO', 'ZÚÑIGA ARACENA', '2007-08-31', '22505358-8', NULL, NULL, 'DINAMARCA. BLOCK 2', '', '', NULL, 'Ninguna', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 21),
(135, 'KONRAD ALEXANDER', 'MODINGER FERNÁNDEZ', '2003-02-14', '21234899-6', NULL, NULL, 'MARCHANT PEREIRA 3019', '', '', NULL, 'Ninguna', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 22),
(136, 'ISIDORA AGUSTINA', 'FLORES PONCE', '2008-06-18', '22751477-9', NULL, NULL, 'ESMERALDA 201 A', 'ELIAPL@HOTMAIL.COM', '', NULL, 'Ninguna', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 23),
(137, 'NICOLÁS JAVIER', 'MOLINA DE VASCONCELOS E SA', '2008-11-19', '22877897-4', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 24),
(138, 'SEBASTIÁN ANDRÉS', 'MOLINA DE VASCONCELOS E SA', '2008-11-19', '22877913-K', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 25),
(139, 'CRISTIAN SEBASTIÁN PEDRO', 'PÉREZ VILCHES', '1996-06-08', '19193582-9', NULL, NULL, 'SANTA MARIA 799 POBLACIÓN GONZALEZ PACHECO', '', '', NULL, 'Ninguna', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 26),
(140, 'MARTÍN ALONSO', 'ALMONACID NOVOA', '2008-05-11', '22718421-3', NULL, NULL, 'PASAJE RÍO MAGDALENA', 'GINGERSJMB@HOTMAIL.COM', '', NULL, 'Ninguna', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 27),
(141, 'ALONZO IGNACIO', 'CARVAJAL URIBE', '2009-02-13', '22946572-4', NULL, NULL, 'CARRERA 1560', '', '', NULL, 'Ninguna', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 28),
(142, 'BRANDÓN FRANCISCO', 'GÓMEZ URIBE', '2007-06-14', '22424911-K', NULL, NULL, 'IGNACIO CARRERA PINTO', '', '986540221', NULL, 'Ninguna', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 29),
(143, 'VANESSA YAQUELINE', 'BELLO PIMENTEL', '2007-12-28', '22597757-7', NULL, NULL, 'SAN ENRIQUE', 'CINDYPATRICIAPIMENTELESPINOZA@GMAIL.COM', '', NULL, 'Ninguna', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 30),
(144, 'BENJAMÍN IGNACIO', 'MIRANDA OLIVARES', '2007-11-16', '22559993-9', NULL, NULL, 'TRONCOS VIEJOS 2450', '', '', NULL, 'Ninguna', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 31),
(145, 'KRISHNA ANAÍS', 'QUEZADA ARROYO', '2007-12-22', '22585263-4', NULL, NULL, 'BERNARDO LEYTON', '', '', NULL, 'Ninguna', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 32),
(146, 'MAGDALENA AMARA', 'LEON LEÓN', '2005-05-14', '22349623-7', NULL, NULL, 'AVENIDA CUARTA PAUL HARRIS MANZANA 24 Nº 927', 'MAGDALENA.LEON.MMONTT@CMVA.CL', '', NULL, 'Ninguna', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 33),
(147, 'LEANDRO MISAEL', 'BRAVO CAAMAÑO', '2008-04-15', '22698892-0', NULL, NULL, 'PASAJE RÍO BELICE 3306, VILLA ALTOS DEL MAIPO', 'Y.CAAMANO@ALUMNOSDUOC.CL', '', NULL, 'Ninguna', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 34),
(148, 'NEYTHAN ELÍAS', 'VICTORIANO VÁSQUEZ', '2008-10-16', '22844610-6', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 35),
(149, 'CATALINA ANTONELLA', 'ORELLANA CARMONA', '2009-12-23', '23209073-1', NULL, NULL, 'SANTA ESTELA Nº 23 ROCUANT', '', '2374457', NULL, 'Ninguna', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 36),
(150, 'LUIS ORLANDO', 'MARTÍNEZ GONZÁLEZ', '2009-05-05', '23011233-9', NULL, NULL, 'CALLE RIO JORDAN R-25 POB ALVORADA F ALTO', '', '', NULL, 'Ninguna', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 37),
(151, 'MATHIAS IGNACIO', 'ALIAGA SEPÚLVEDA', '2007-08-26', '22494385-7', NULL, NULL, 'BRISAS DEL MAR DEP.21 BLOK 75', '', '2862378', NULL, 'Ninguna', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 38),
(152, 'DANITZA ALBINA', 'PESCE PACHECO', '2009-06-07', '23046919-9', NULL, NULL, 'SIMÓN ALAMOS', '', '', NULL, 'Ninguna', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 39),
(153, 'ROSA AURORA', 'LEÓN LEÓN', '1995-03-20', '19124341-2', NULL, NULL, 'SANTA MARGARITA 1425 ROSENQUIT', '', '99365963', NULL, 'Ninguna', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 40),
(154, 'MONTSERRAT ESPERANZA', 'VELÁSQUEZ LOBOS', '2006-06-16', '22146704-3', NULL, NULL, 'EL RETIRO 1168  5TO SECTOR BELLOTO SUR', '', '2337800', NULL, 'Ninguna', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 41),
(155, 'SOFÍA AGUSTINA', 'CÁRDENAS GONZALEZ', '2007-07-23', '22464798-0', NULL, NULL, 'TIERRAS ROJAS 641 EL BELLOTO', '', '', NULL, 'Ninguna', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 42),
(156, 'GINNA ANTONIETTA', 'LAGOMARSINO VISTOSO', '1990-05-27', '17356169-5', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 43),
(157, 'YESSENNIA LOURDES', 'OLAVE GUMERA', '1990-02-11', '17567190-0', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 44),
(158, 'ISABELLA JUSTINA', 'FAJARDO ZUMELZU', '2008-06-14', '22743808-8', NULL, NULL, 'LOS ALMENDROS', 'VFAJARDORIVERO2@GMAIL.COM', '', NULL, 'Ninguna', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 45),
(159, 'MADELAINE ROUSE', 'SÁEZ SÁNCHEZ', '2009-05-04', '23012353-5', NULL, NULL, 'POBL. SEBASTIANA CALLE PABLO NERUDA II Nº978', '', '', NULL, 'Ninguna', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 46),
(160, 'ÁLVARO IGNACIO', 'PÉREZ ARAVENA', '2007-07-31', '22481561-1', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 47),
(161, 'BENJAMÍN IGNACIO', 'MIRANDA OLIVARES', '2007-11-16', '22559993-9', NULL, NULL, 'TRONCOS VIEJOS 2450', '', '', NULL, 'Ninguna', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 48),
(162, 'SEBASTIÁN IGNACIO', 'BURGOS OLIVARES', '2007-01-03', '22307538-K', NULL, NULL, 'CASTELLON O238', '', '2560076', NULL, 'Ninguna', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 49),
(163, 'MIGUEL ANGEL', 'BRINCKFELDT MÉNDEZ', '2008-03-31', '22686371-0', NULL, NULL, 'TUAMAPU, CANAL MORALEDA', 'DANIELACISTERNASZ@GMAIL.COM', '', NULL, 'Ninguna', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 50),
(164, 'BRANDON ALEXANDER', 'BENAVIDES VICENCIO', '2007-05-08', '22396792-2', NULL, NULL, 'DINAMARCA 1320 HUANHUALI', '', '975426716', NULL, 'Ninguna', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 51),
(165, 'VANESSA YAQUELINE', 'BELLO PIMENTEL', '2007-12-28', '22597757-7', NULL, NULL, 'SAN ENRIQUE', 'CINDYPATRICIAPIMENTELESPINOZA@GMAIL.COM', '', NULL, 'Ninguna', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 52),
(166, 'FABIÁN ANDRÉ', 'OPAZO SAAVEDRA', '2007-06-30', '22434842-8', NULL, NULL, 'ANDRES 030 MORALEDA. PEÑA BLANCA', 'FRANFER_61@OUTLOOK.COM', '', NULL, 'Ninguna', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 53),
(167, 'ASMAA ALI', 'ANEIZAN', '2006-01-01', '25942081-4', NULL, NULL, 'IGNACIO CARRERA PINTO', 'ASMAA.ANEIZAN@LATINAIGO.CMVA.CL', '', NULL, 'Ninguna', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 54),
(168, 'ALEXANDER ISAY', 'ARMIJO VIDELA', '2009-02-16', '22952794-0', NULL, NULL, 'RAMON ANGEL JARA PASAJE 2LOTE21-B', 'RUAMA69@HOTMAIL.COM', '', NULL, 'Ninguna', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 55),
(169, 'KEVIN ALEJANDRO', 'BASUALTO ZAMORA', '2008-02-23', '22651348-5', NULL, NULL, 'PASAJE PUMALIN, VILLA EL QUILLAY', 'KEVIN.BASUALTO@LATINAIGO.CMVA.CL', '', NULL, 'Ninguna', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 56),
(170, 'MAGDALENA AMARA', 'LEON LEÓN', '2005-05-14', '22349623-7', NULL, NULL, 'AVENIDA CUARTA PAUL HARRIS MANZANA 24 Nº 927', 'MAGDALENA.LEON.MMONTT@CMVA.CL', '', NULL, 'Ninguna', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 57),
(171, 'JORDÁN ALEXIS', 'DÍAZ MAGNA', '2005-08-19', '21922033-2', NULL, NULL, 'LOS ZAPADORES 1636 B/16 D/103', '', '2560930', NULL, 'Ninguna', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 58);
INSERT INTO `matriculas` (`id`, `nombre_estudiante`, `apellidos_estudiante`, `fecha_nacimiento`, `rut_estudiante`, `serie_carnet_estudiante`, `etnia_estudiante`, `direccion_estudiante`, `correo_estudiante`, `telefono_estudiante`, `hijos_estudiante`, `situacion_especial_estudiante`, `programa_estudiante`, `curso_preferido`, `jornada_preferida`, `nombre_apoderado`, `rut_apoderado`, `parentezco_apoderado`, `direccion_apoderado`, `telefono_apoderado`, `situacion_especial_apoderado`, `fecha_registro`, `estado`, `estudiante_id`) VALUES
(172, 'HUSSEIN ALI', 'ANEIZAN', '2008-01-01', '25942095-4', NULL, NULL, 'IGNACIO CARRERA PINTO', 'HUSSEIN.ANEIZAN@LATINAIGO.CMVA.CL', '933247551', NULL, 'Ninguna', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 59),
(173, 'MARTÍN PATRICIO', 'RETAMAL RODRÍGUEZ', '2008-05-28', '22732405-8', NULL, NULL, 'PASAJE EL TABO', '', '983885397', NULL, 'Ninguna', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 60),
(174, 'JAVIER IGNACIO', 'ZULETA ÁLVAREZ', '2005-12-14', '22000269-1', NULL, NULL, 'PJE RIO LOA 512', '', '', NULL, 'Ninguna', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 61),
(175, 'GLORIA XIMENA', 'MELIPIL GUTIÉRREZ', '1979-08-24', '13856460-6', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 62),
(176, 'ALONZO IGNACIO', 'CARVAJAL URIBE', '2009-02-13', '22946572-4', NULL, NULL, 'CARRERA 1560', '', '', NULL, 'Ninguna', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 63),
(177, 'JUAN MANUEL ALEJANDRO', 'PUEBLA COLLADO', '2008-02-29', '22669256-8', NULL, NULL, 'PATRICIO LYNCH PALMILLA BAJA', '', '44231582', NULL, 'Ninguna', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 64),
(178, 'MILLARAY BELÉN', 'SOTO CALZADA', '2004-09-06', '21659039-2', NULL, NULL, 'CARLOS CONDELL', '', '', NULL, 'Ninguna', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 65),
(179, 'VICENTE ANTONIO', 'LOO FERNÁNDEZ', '2008-03-02', '22665520-4', NULL, NULL, 'DINAMARCA 1331 BLOCOK 18 DEPTO 104', '', '', NULL, 'Ninguna', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 66),
(180, 'ESTRELLA BELÉN ANAÍS', 'ARREGUI FARÍAS', '2008-12-15', '22905873-8', NULL, NULL, 'HIJUELAS CON EL BOLDO  LOTE 45', '', '', NULL, 'Ninguna', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 67),
(181, 'KRISHNA ANAÍS', 'QUEZADA ARROYO', '2007-12-22', '22585263-4', NULL, NULL, 'BERNARDO LEYTON', '', '', NULL, 'Ninguna', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 68),
(182, 'MAXIMILIANO ANGEL ANTONIO', 'ALIAGA ZAMORA', '2006-11-30', '22272932-7', NULL, NULL, 'LAS MANDARINAS', '', '', NULL, 'Ninguna', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 69),
(183, 'MAYLIN BELÉN', 'BARLARI GONZÁLEZ', '2008-07-05', '22762825-1', NULL, NULL, 'LAS ACACIAS 290 LUCUMILLO 303', 'ELIZABETHGONZALEZ593@GMAIL.COM', '', NULL, 'Ninguna', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 70),
(184, 'JEAN PAUL', 'SOTOMAYOR DÍAZ', '2009-02-24', '22957711-5', NULL, NULL, 'POBLACION DINAMARCA, BLOCK 2', 'JEAN.SOTOMAYOR@LATINAIGO.CMVA.CL', '78251955', NULL, 'Ninguna', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 71),
(185, 'PABLO ALEJANDRO', 'VELÁSQUEZ MUÑOZ', '2008-08-31', '22841316-K', NULL, NULL, 'CALLE BAQUEDANO', 'AGUSTIN.EDWARDS@DAEMLLAYLLAY.CL', '58969454', NULL, 'Ninguna', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 72),
(186, 'JOSÉ MANUEL ANTONIO', 'OYARZÚN OLGUÍN', '2008-03-06', '22664003-7', NULL, NULL, 'LOS CRISANTEMOS', 'CLAUJAVI80@HOTMAIL.COM', '', NULL, 'Ninguna', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 73),
(187, 'RICARDO ANDRÉS', 'BAEZA CANCINO', '2008-05-19', '22729122-2', NULL, NULL, 'BICENTENARIO PASAJE 8', '', '', NULL, 'Ninguna', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 74),
(188, 'AMANDA PAVLOVA', 'POLANCO REYES', '2008-08-08', '22790180-2', NULL, NULL, 'HERNAN TORRES 541,', 'ERIKA.EYES8@GMAIL.COM', '972148879', NULL, 'Ninguna', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 75),
(189, 'BRAYAN EDUARDO', 'ORELLANA NÚÑEZ', '1999-09-27', '20098988-0', NULL, NULL, 'FINLANDIA 2588 POBL RENE SCHNEIDER', '', '315103', NULL, 'Ninguna', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 76),
(190, 'THIARE DENISSE', 'ORELLANA NÚÑEZ', '2000-10-20', '20348301-5', NULL, NULL, 'FINLANDIA 2588 POBL. R  SCHNEIDER', '', '315103', NULL, 'Ninguna', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 77),
(191, 'BENJAMÍN VICENTE', 'HORMAZÁBAL ESCOBAR', '2006-11-29', '22272120-2', NULL, NULL, 'HUASCO', '', '3210455', NULL, 'Ninguna', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 78),
(192, 'LUCIANO EDUARDO', 'CASAS CÁCERES', '2001-03-23', '20726750-3', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 79),
(193, 'CONSTANZA ANDREA', 'OLIVARES TAPIA', '2007-05-24', '22543295-3', NULL, NULL, 'AV.MANUEL VIDAL N°2  CHORRILLOS', '', '67207061', NULL, 'Ninguna', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 80),
(194, 'AMARÚ ANTTONIA', 'RÍOS JARA', '2005-05-19', '21878690-1', NULL, NULL, '', '', '2823576', NULL, 'Ninguna', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 81),
(195, 'MATHIAS IGNACIO', 'ALIAGA SEPÚLVEDA', '2007-08-26', '22494385-7', NULL, NULL, 'BRISAS DEL MAR DEP.21 BLOK 75', '', '2862378', NULL, 'Ninguna', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 82),
(196, 'CLAUDIA FRANCISCA', 'ARAVENA VIDAL', '1994-01-23', '18535723-6', NULL, NULL, 'PJE. HISPANO 631, GUMERCINDO', '', '', NULL, 'Ninguna', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 83),
(197, 'JAVIERA FERNANDA', 'MALTINA RIVAS', '2003-08-09', '21376293-1', NULL, NULL, 'CALLE   2    Nº 1472   VALENCIA ALTO', '', '', NULL, 'Ninguna', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 84),
(198, 'FRANCISCO ANTONIO', 'VICENCIO TAPIA', '1994-09-02', '19449083-6', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 85),
(199, 'JOAN CARLOS', 'MENA FARÍAS', '2002-11-22', '21179704-5', NULL, NULL, 'GOMEZ CARREÑO', '', '', NULL, 'Ninguna', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 86),
(200, 'PATRICIA ANGÉLICA', 'LÓPEZ OLAVE', '1961-11-07', '9593391-2', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 87),
(201, 'BRANDON ALEXANDER', 'TAPIA ALTAMIRANO', '2007-11-09', '22551132-2', NULL, NULL, 'CALLE TEMUCO 587 POB.LA FRONTERA', '', '', NULL, 'Ninguna', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 88),
(202, 'SUSANA ROSINA', 'REYES ROSALES', '1987-03-02', '16643025-9', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 89),
(203, 'BENJAMIN IGNACIO', 'SAN MARTÍN ORTIZ', '2006-09-18', '22216034-0', NULL, NULL, 'PATRICIO LINCH  Nº 624 PALMILLA ALTA', 'CHRISTIANSANMARTIN20@GMAIL.COM', '', NULL, 'Ninguna', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 90),
(204, 'FRANCISCO JAVIER', 'MONTECINOS ROCO', '2007-02-04', '22325090-4', NULL, NULL, 'BAQUDANO 1151', '', '', NULL, 'Ninguna', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 91),
(205, 'KEVIN ALEXANDER', 'HERRERA PÉREZ', '2003-10-11', '21421107-6', NULL, NULL, 'PASAJE DINAMARCA 1320-B', '', '', NULL, 'Ninguna', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 92),
(206, 'BETZABÉ NICOL', 'ÁLVAREZ GÁLVEZ', '1992-03-18', '17643380-9', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 93),
(207, 'JEMIMA CECILIA', 'SILVA FUENTEALBA', '1975-01-20', '12718836-K', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 94),
(208, 'ÍTALO MAXIMILIANO', 'YANTÉN BAEZ', '1987-03-15', '16691183-4', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 95),
(209, 'JORGE LUIS', 'JORDÁN INOSTROZA', '1989-09-16', '17169712-3', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 96),
(210, 'AÍDA ERCILIA', 'PÉREZ ALMENDRAS', '1984-06-20', '15762888-7', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 97),
(211, 'VICENTE ANTONIO', 'LOO FERNÁNDEZ', '2008-03-02', '22665520-4', NULL, NULL, 'DINAMARCA 1331 BLOCOK 18 DEPTO 104', '', '', NULL, 'Ninguna', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 98),
(212, 'MONSERRAT ANDREA', 'LÓPEZ CARVAJAL', '2009-06-06', '23045577-5', NULL, NULL, 'SAN ENRIQUE  CALLE 1', '', '961521988', NULL, 'Ninguna', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 99),
(213, 'GRISEL SOLANGE', 'FERNÁNDEZ REYES', '1998-08-21', '19665281-7', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 100),
(214, 'MARCELO PAOLO', 'SOLARI GRANADOS', '2003-08-18', '21383638-2', NULL, NULL, 'VALENTIN LETELIER 461 PEÑA BLANCA', '', '2965124', NULL, 'Ninguna', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 101),
(215, 'ARTURO SAYD', 'CEBRERO ARAYA', '2007-09-10', '22499561-K', NULL, NULL, 'EL LIBRO', '', '972093842', NULL, 'Ninguna', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 102),
(216, 'MARJORIE XIMENA', 'GODOY PUEBLA', '1985-03-20', '15993346-6', NULL, NULL, 'PAUL HARRIS', 'MARJORIEGODOY@LITECVA.CL', '946388731', NULL, 'Ninguna', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 103),
(217, 'MARITZA DEL CARMEN', 'PÁEZ TRUJILLO', '1965-07-12', '10406152-4', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 104),
(218, 'JUAN MANUEL ALEJANDRO', 'PUEBLA COLLADO', '2008-02-29', '22669256-8', NULL, NULL, 'PATRICIO LYNCH PALMILLA BAJA', '', '44231582', NULL, 'Ninguna', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 105),
(219, 'ROSSANA MILENKA', 'NÚÑEZ GUERRA', '1981-12-07', '15768312-8', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 106),
(220, 'VALENTÍN DOMINGO', 'ZAMORA FERNÁNDEZ', '1973-05-06', '12104353-K', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 107),
(221, 'ZUNILDA MAGDALENA', 'CASTRO JARA', '1964-02-21', '10011252-3', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 108),
(222, 'IVO MIGUEL ANTONIO', 'TUDELA POBLETE', '2005-02-22', '21781248-8', NULL, NULL, 'ECHAURREN 349 PALMILLA BAJA', 'ALINOPOBLETENUTIZ.45@GMAIL.COM', '', NULL, 'Ninguna', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 109),
(223, 'FERNANDO MATÍAS', 'MUÑOZ EGUREN', '1990-06-23', '21364876-4', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 110),
(224, 'ALEXANDER ANTONIO', 'CORDERO MUÑOZ', '2002-09-21', '21131440-0', NULL, NULL, 'SANTA  FILOMENA    1213', '', '', NULL, 'Ninguna', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 111),
(225, 'FERNANDO IGNACIO', 'HOLM GODOY', '2007-10-26', '22537301-9', NULL, NULL, 'PAUL HARRIS Nº 718 POBLACIÓN GUMERCINDO', 'FERNANDOHOLM@LITECVA.CL', '967851893', NULL, 'Ninguna', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 112),
(226, 'PABLO TOMÁS', 'NAVARRO FIGUEROA', '2006-10-27', '22248511-8', NULL, NULL, 'SARGENTO ALDEA 285 CASA 41', 'PABLO.NAVARRO@CATALUNYA.CMVA.CL', '', NULL, 'Ninguna', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 113),
(227, 'FRANCISCO ALEJANDRO', 'QUIROZ NAVARRETE', '2008-01-17', '22616300-K', NULL, NULL, 'LIMA 118 POB.FUENTES', 'QUIROZLEONC@GMAIL.COM', '', NULL, 'Ninguna', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 114),
(228, 'DENISSE ALEJANDRA ESTEFANIA', 'VALDÉS CARVAJAL', '2008-04-01', '22687238-8', NULL, NULL, 'DINAMARCA 1250 BLOCK 35 DEPTO 302', '', '', NULL, 'Ninguna', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 115),
(229, 'JOSÉ JAVIER', 'YÁÑEZ COLLADO', '2002-03-01', '20998919-0', NULL, NULL, 'PSJE. FRANCISCA VILLA LOS AROMOS', '', '', NULL, 'Ninguna', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 116),
(230, 'KATIA ANAIZ', 'BARRERA VERGARA', '2007-03-24', '22358095-5', NULL, NULL, 'CASTRO  SOMASUR', '', '', NULL, 'Ninguna', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 117),
(231, 'JONATHAN MARCELO', 'MORENO JERIA', '2006-12-29', '22298122-0', NULL, NULL, 'JORGE INOSTROZA    889', '', '', NULL, 'Ninguna', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 118),
(232, 'PATRICIA ALEJANDRA', 'RIVERA ARAYA', '1988-05-18', '17014974-2', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 119),
(233, 'FABIÁN GEOVANNI', 'CAMPOS CONTRERAS', '2007-04-17', '22377443-1', NULL, NULL, 'PARCELA', '', '', NULL, 'Ninguna', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 120),
(234, 'SOFÍA VALENTINA', 'VILLENA ESPINOZA', '2007-04-29', '22388582-9', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 121),
(235, 'LUCAS MATEO', 'REYES COSMELLI', '2007-05-12', '22396686-1', NULL, NULL, 'PASAJE 806 CASA 812', '', '2945519', NULL, 'Ninguna', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 122),
(236, 'GABRIEL EMMANUEL', 'VIVANCO CASTILLO', '2007-11-08', '22552279-0', NULL, NULL, 'CALLE CENTRAL LOTE 12 POBLACION LOMAS DE BELLAVISTA', 'TAMYCASTILLO31@GMAIL.COM', '947937085', NULL, 'Ninguna', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 123),
(237, 'ISIDORA PAZ', 'MORALES CASTRO', '2008-12-30', '22909550-1', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 124),
(238, 'FRANCO ANDRÉS', 'GONZÁLEZ MORAGA', '2004-08-31', '21655983-5', NULL, NULL, 'LOS ZAPADORES 1645 BLOCK 11 DPTO 202 , VILLA ALEMANA', 'P.MORAGA.ASEOSMCVINA@GMAIL.COM', '954437036', NULL, 'Ninguna', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 125),
(239, 'ALVARO DENNY', 'KRUG LÓPEZ', '2005-11-01', '21968780-K', NULL, NULL, 'HIPODROMO, C. LOS DANIELES 1551 DEPTO. 204', 'LOPEZSORAYA2908@GMALI.COM', '998394857', NULL, 'Ninguna', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 126),
(240, 'ROSA VICTORIA', 'VENEGAS PULGAR', '2007-11-05', '22541722-9', NULL, NULL, 'SAN ENRIQUE 860', 'ROSAVENEGASPULGAR@GMAIL.COM', '', NULL, 'Ninguna', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 127),
(241, 'MAURICIO ANDRÉS', 'ROMÁN MOSCOSO', '2004-05-11', '21574216-4', NULL, NULL, 'BUENOS AIRES 1110  BAQUEDANO', '', '', NULL, 'Ninguna', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 128),
(242, 'IGNACIA BELÉN ANTONIA', 'LÓPEZ MONTENEGRO', '2007-01-10', '22304106-K', NULL, NULL, 'PALMILLA Nº 131', 'IGNACIA.LOPEZ@COLEGIONACIONAL.CL', '', NULL, 'Ninguna', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 129),
(243, 'VICENTE ANTONIO', 'GONZÁLEZ OROZCO', '2007-07-21', '22460516-1', NULL, NULL, 'ARTURO PRAT', '', '', NULL, 'Ninguna', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 130),
(244, 'BRANDÓN FRANCISCO', 'GÓMEZ URIBE', '2007-06-14', '22424911-K', NULL, NULL, 'IGNACIO CARRERA PINTO', '', '986540221', NULL, 'Ninguna', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 131),
(245, 'IAN FRANCISCO', 'HINOJOSA NOVA', '2006-10-13', '22239499-6', NULL, NULL, 'PASAJE. MIRIAM 1111', 'CHEHINOJOSA@GMAIL.COM', '2319464', NULL, 'Ninguna', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 132),
(246, 'CRISTIÁN GABRIEL', 'ORTIZ ZÚÑIGA', '2007-10-24', '22533789-6', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 133),
(247, 'BENJAMÍN ANTONIO', 'MARTÍNEZ AGUIRRE', '2006-12-08', '22277861-1', NULL, NULL, 'CARRERA 1811', '', '', NULL, 'Ninguna', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 134),
(248, 'ANASTASIA ALEJANDRA DANAIS', 'DÍAZ PEREIRA', '2007-02-22', '22337793-9', NULL, NULL, 'CALLE PAUL HARRIS', '', '57406959', NULL, 'Ninguna', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 135),
(249, 'MARÍA LEONTINA', 'ARMIJO VERA', '1973-10-07', '14453070-5', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 136),
(250, 'FERNANDA PAULINA', 'IBARRA SÁNCHEZ', '2007-06-20', '22424799-0', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 137),
(251, 'NICOL ANDREA', 'CASTRO GUAJARDO', '2005-08-24', '21921209-7', NULL, NULL, 'AV. NUEVA HIPODROMO', 'JACQUELINEGUAJARDO4@GMAIL.COM', '', NULL, 'Ninguna', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 138),
(252, 'MIDIA ADNAN', 'NABO', '1989-04-16', '25942132-2', NULL, NULL, 'I. CARRERA PINTO', 'CFTALEMAN@GMAIL.COM', '', NULL, 'Ninguna', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 139),
(253, 'CAMILA BELÉN', 'OSORIO ZÚÑIGA', '2007-05-22', '22407808-0', NULL, NULL, 'AVENIDA LA PALMA', 'MA.FLORESPSIPEDAGOGA@GMAIL.COM', '323243572', NULL, 'Ninguna', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 140),
(254, 'BENJAMÍN ALEJANDRO', 'AROS CABELLO', '2007-06-17', '22426016-4', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 141),
(255, 'BENJAMÍN GABRIEL', 'GARCÍA VICENCIO', '2007-04-16', '22377815-1', NULL, NULL, 'WAGNER', '', '977089647', NULL, 'Ninguna', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 142),
(256, 'JAVIERA PAZ', 'CHÁVEZ CABRERA', '2007-09-08', '22497419-1', NULL, NULL, 'PJE LAGO CHAPO  POB.EL ÁLAMO', '', '', NULL, 'Ninguna', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 143),
(257, 'HÉCTOR DANIEL', 'MONTEJO IRRIBARRA', '2007-09-25', '22507636-7', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 144),
(258, 'BRITANY ZUELY', 'TRONCOSO FERNÁNDEZ', '2004-03-09', '21529124-3', NULL, NULL, 'DINAMARCA CON SAN JOSE , BLOCK 19, DEP-103', 'JOSE.TRON35@GMAIL.COM', '', NULL, 'Ninguna', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 145),
(259, 'MARTINA TRINIDAD', 'PÉREZ CORTÉS', '2007-04-04', '22372290-3', NULL, NULL, 'CALLE CANAL  CHACAO 375 CANAL BEAGLE', '', '', NULL, 'Ninguna', NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 146),
(260, 'GABRIELA VALENTINA', 'SOLORZANO', '2007-09-21', '26637822-K', NULL, NULL, '', '', '935589841', NULL, 'Ninguna', NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 147),
(261, 'GABRIEL ALEXANDER', 'VEGA VENEGAS', '2008-03-06', '22662744-8', NULL, NULL, 'AV. OCTAVA 412 - B PARADERO 7 1/2', '', '', NULL, 'Ninguna', NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 148),
(262, 'BENJAMÍN OMAR ALFREDO', 'MUÑOZ ARRIAGADA', '2005-10-12', '21952915-5', NULL, NULL, 'PJE. LASCAR 2132, LOS PINOS', '', '', NULL, 'Ninguna', NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 149),
(263, 'BENJAMÍN IGNACIO', 'ARMIJO NEGRETE', '2007-09-15', '22505566-1', NULL, NULL, 'CALLE SANTIAGO 931. VILLA MONTECARLO. BELLOTO NORTE', '', '', NULL, 'Ninguna', NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 150),
(264, 'JAVIERA ALEJANDRA', 'GAMBOA SEPÚLVEDA', '2007-09-20', '22508301-0', NULL, NULL, 'LINCOYAN', '', '', NULL, 'Ninguna', NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 151),
(265, 'NICHOLAS JESÚS', 'PUENTE CORNEJO', '2003-11-22', '21450281-K', NULL, NULL, 'ALCALDE ALEJANDRO PERALTA', 'C.CORNEJO25@GMAIL.COM', '', NULL, 'Ninguna', NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 152),
(266, 'ANA PAULA', 'SILVA DÍAZ', '1991-08-26', '17944711-8', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 153),
(267, 'AGUSTINA PATRICIA', 'LÓPEZ AROS', '2007-06-30', '22430956-2', NULL, NULL, 'CALLE ARICA 450 PALMILLA ALTA VILLA ALEMANA', '', '2951288', NULL, 'Ninguna', NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 154),
(268, 'MASSIEL ALEXANDRA', 'ORDÓÑEZ CELEDÓN', '2007-10-26', '22536512-1', NULL, NULL, 'LOS NARCISOS 2090 LOS DANIELES BLOCK 2 DEPTO 402', 'KARINACELEDON31@GMAIL.COM', '', NULL, 'Ninguna', NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 155),
(269, 'PATRICIA INES', 'SAAVEDRA GÁLVEZ', '2007-03-06', '22348392-5', NULL, NULL, 'VOLCAN', '', '', NULL, 'Ninguna', NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 156),
(270, 'MATÍAS EDUARDO', 'SALAZAR VALENCIA', '2006-02-21', '22058549-2', NULL, NULL, 'LOS ESPINOS 402', 'SEPTIMOMELVINJONES2020@HOTMAIL.COM', '', NULL, 'Ninguna', NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 157),
(271, 'DAYENU BELEN', 'VARAS ARRIAGADA', '2006-05-14', '22119940-5', NULL, NULL, 'DINAMARCA', '', '', NULL, 'Ninguna', NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 158),
(272, 'IGNACIA ALEJANDRA BELÉN', 'AHUMADA FLORES', '2007-01-23', '22311606-K', NULL, NULL, 'EL MIRADOR 0481 VILLA ALEMANA', '', '318425', NULL, 'Ninguna', NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 159),
(273, 'CONSTANZA BELÉN', 'ALARCÓN FIGUEROA', '2007-11-01', '22540195-0', NULL, NULL, 'JERUSALÉN CON 7 Nº 1153', '', '2560215', NULL, 'Ninguna', NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 160),
(274, 'PÍA MONTSERRATT', 'QUIROZ FARÍAS', '2005-08-23', '21919490-0', NULL, NULL, 'LOS ZAPADORES 1636 B/16 DEPTO 105', '', '', NULL, 'Ninguna', NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 161),
(275, 'EMMA EDITH', 'ROJAS', '2006-09-08', '100499521-6', NULL, NULL, 'COLO COLO PASAJE DAVID TRUMBALL', '', '', NULL, 'Ninguna', NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 162),
(276, 'SEBASTIAN ENRIQUE', 'ENCINA CASTRO', '2006-08-16', '22188454-K', NULL, NULL, 'LAGOS TODOS LOS SANTOS 023 VILLA ARMAT', 'SEBASTIAN.ENCINA@BARROSARANA.CMVA.CL', '', NULL, 'Ninguna', NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 163),
(277, 'PAZ ANTONIA', 'GARCÍA DÍAZ', '2008-03-11', '22673008-7', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 164),
(278, 'IGNACIO ANDRÉS', 'ITE DE LA MAZA', '2007-06-24', '22460318-5', NULL, NULL, 'BERNARDO LEIGTHON', '', '', NULL, 'Ninguna', NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 165),
(279, 'HÉCTOR DANIEL', 'MONTEJO IRRIBARRA', '2007-09-25', '22507636-7', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 166),
(280, 'SEBASTIÁN ANDRÉS', 'CARRASCO VERA', '2005-08-18', '21914783-K', NULL, NULL, 'SANTA ANA 824', '', '', NULL, 'Ninguna', NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 167),
(281, 'AMARILY DANIELA ANTONIA', 'PACHECO CORTÉS', '2006-04-11', '22095613-K', NULL, NULL, 'TUCAPEL 115,POBL. LO BERMUDEZ.PEÑABLANCA', 'CAROLITABONITAUWU@GMAIL.COM', '2822224', NULL, 'Ninguna', NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 168),
(282, 'PAULA CONSTANZA', 'ARDILES DONOSO', '2007-03-09', '22350713-1', NULL, NULL, 'LOMAS DE BELLAVISTA', 'CHINA_JUAN@HOTMAIL.COM', '9477717716', NULL, 'Ninguna', NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 169),
(283, 'DANIEL ALEXANDER', 'CALDERÓN RUSS', '2006-08-29', '22198948-1', NULL, NULL, 'PJE BERLIN Nº7 POB UNIOPORT', '', '2115180', NULL, 'Ninguna', NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 170),
(284, 'WILLIAM MATÍAS NAHUM', 'CORTÉS GÓMEZ', '2007-08-01', '22467773-1', NULL, NULL, 'SAN IGNACIO', '', '965639097', NULL, 'Ninguna', NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 171),
(285, 'IGNACIA DANESKA', 'TAPIA CATALÁN', '2006-02-15', '22073757-8', NULL, NULL, 'PASAJE LOS PERALES Nº 3120 POBLACIÓN DON FERNANDO', 'IGNACIA.TAPIA@BARROSARANA.CMVA.CL', '', NULL, 'Ninguna', NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 172),
(286, 'BASTIÁN IGNACIO', 'PARRAGUIRRE VARGAS', '2007-04-30', '22385718-3', NULL, NULL, 'PASAJE ARAGON 0537, VILLA REINA', '', '', NULL, 'Ninguna', NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 173),
(287, 'MARTINA AMANDA', 'COVARRUBIAS HERNÁNDEZ', '2006-03-23', '22084369-6', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:22', 'Activa', 174),
(288, 'IGNACIO ANDRÉS', 'SILVA DÍAZ', '2003-07-18', '21340674-4', NULL, NULL, 'DINAMARCA   B-32  D-301', '', '', NULL, 'Ninguna', NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 175),
(289, 'GIOVANNI ANDRÉS', 'GARCÉS PALMA', '2007-03-19', '22378181-0', NULL, NULL, 'PASAJE LOS PIONEROS', '', '984906500', NULL, 'Ninguna', NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 176),
(290, 'GABRIELA MONSERRATT', 'MUÑOZ MUÑOZ', '2007-01-31', '22321903-9', NULL, NULL, 'CALLE DOLORES 830 PALMILLA ALTA, VILLA ALEMANA', 'YAJA_MUNOZLOZ@HOTMAIL.COM', '95574673', NULL, 'Ninguna', NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 177),
(291, 'CARLOS ALEJANDRO', 'MICHEAS MONDACA', '2008-05-06', '22719656-4', NULL, NULL, 'JOSE MIGUEL CARRERA', 'CARLOS.MICHEAS.M@EDUCAQUILPUE.CL', '', NULL, 'Ninguna', NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 178),
(292, 'JOAQUÍN ALONSO', 'LINEROS OLIVARES', '2006-02-12', '22051134-0', NULL, NULL, 'BERNARDO LEIGHTON 1040', 'GIULIANANADIE@GMAIL.COM', '', NULL, 'Ninguna', NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 179),
(293, 'MAXIMILIANO ALONSO', 'MEZA PEREIRA', '2007-12-10', '22576513-8', NULL, NULL, 'PJE. OLMO', 'GABRIELA.76.PEREIRA@GMAIL.COM', '', NULL, 'Ninguna', NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 180),
(294, 'EDUARDO IGNACIO', 'VALENZUELA BECERRA', '2007-07-04', '22435497-5', NULL, NULL, 'LAS ACACIAS', 'EDUARDO.VALENZUELA@MANUELMONTT.CMVA.CL', '', NULL, 'Ninguna', NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 181),
(295, 'AZÚMI YUKARY', 'CORNEJO NÚÑEZ', '2007-05-10', '22396669-1', NULL, NULL, 'LONCOCHE , LA FRONTERA', '', '', NULL, 'Ninguna', NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 182),
(296, 'MILA JANA', 'ROJAS PINTO', '2005-05-29', '21872224-5', NULL, NULL, 'LOS DAMASCOS N° 1664', 'RJASMILA7@GMAIL.COM', '', NULL, 'Ninguna', NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 183),
(297, 'EMMANUEL ALEJANDRO', 'ROMERO TORO', '2005-11-25', '21985414-5', NULL, NULL, 'LOS NARDOS 2550', '', '', NULL, 'Ninguna', NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 184),
(298, 'VINCHENNZO ANÍBAL', 'SORIAGALVARRO NÚÑEZ', '2006-04-23', '22120440-9', NULL, NULL, 'FINLANDIA 2598', '', '', NULL, 'Ninguna', NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 185),
(299, 'DANIEL ESTEBAN', 'PINTO DESIDER', '2006-08-25', '22192990-K', NULL, NULL, 'CALLEJON EL CULTO Nº101 BARRACITA', '', '', NULL, 'Ninguna', NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 186),
(300, 'LISSETE GRACIELA', 'RIQUELME ACEVEDO', '1995-06-07', '18998130-9', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 187),
(301, 'SARA', 'ALFARO GARZÓN', '2006-07-28', '24716507-K', NULL, NULL, 'CALLE MÉXICO 1951', '', '613910', NULL, 'Ninguna', NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 188),
(302, 'KAMILA ANTONELLA', 'LEZANA GAETE', '1996-10-11', '19424026-0', NULL, NULL, 'PROCYON N° 1018  VILLA ALDEBARÁN BELLOTO SUR', '', '2111349', NULL, 'Ninguna', NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 189),
(303, 'MARTINA BELÉN', 'LEÓN BENÍTEZ', '2008-03-11', '22672265-3', NULL, NULL, 'ALMIRANTE WILSON 977. BELLOTO NORTE', '', '', NULL, 'Ninguna', NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 190),
(304, 'IGNACIA BELÉN', 'PEREIRA OCARIZ', '1999-07-19', '20224606-0', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 191),
(305, 'ESCARLET ANTONIA', 'RETAMAL MUÑOZ', '2007-12-26', '22600850-0', NULL, NULL, 'SERENA 889', 'ALEJANDRAA_MM@HOTMAIL.COM', '', NULL, 'Ninguna', NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 192),
(306, 'MARTINA AMANDA', 'COVARRUBIAS HERNÁNDEZ', '2006-03-23', '22084369-6', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 193),
(307, 'NICOLÁS ANDRÉS', 'DÍAZ ARANCIBIA', '2007-10-31', '22542225-7', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 194),
(308, 'ALDEBARAN LEE', 'CANALES DÍAZ', '2006-06-20', '22146454-0', NULL, NULL, 'MARIA ISABEL BELLOTO', 'ALDEBARAN.CANALES@LATINAIGO.CMVA.CL', '322950764', NULL, 'Ninguna', NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 195),
(309, 'JAVIER ALEJANDRO', 'PLAZA VARGAS', '2006-01-01', '22017405-0', NULL, NULL, 'SANTA SARA 1620', 'PLAZAVARGASJAVIER47@GMAIL.COM', '993933634', NULL, 'Ninguna', NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 196),
(310, 'FERNANDO ANDRÉS', 'RAMÍREZ GARCÍA', '2001-09-22', '20850804-0', NULL, NULL, 'EUCALIPTUS, PJE. LAS MANDARINAS', '', '', NULL, 'Ninguna', NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 197),
(311, 'TOMÁS JOSUÉ', 'CASANOVA ZAVALA', '2008-07-09', '22769771-7', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 198),
(312, 'FRANCISCA ANASTASIA', 'BAHAMONDES JARA', '2007-06-15', '22420482-5', NULL, NULL, 'IGNACIO SERRANO 1863 B', '', '', NULL, 'Ninguna', NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 199),
(313, 'ZUNILDA MAGDALENA', 'CASTRO JARA', '1964-02-21', '10011252-3', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 200),
(314, 'JOAQUÍN ALONSO', 'LINEROS OLIVARES', '2006-02-12', '22051134-0', NULL, NULL, 'BERNARDO LEIGHTON 1040', 'GIULIANANADIE@GMAIL.COM', '', NULL, 'Ninguna', NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 201),
(315, 'MICHELLE NOEMÍ', 'JOHNS ABARZA', '2002-03-09', '21000153-0', NULL, NULL, 'PAUL HARRIS 594 GUMERCINDO', '', '', NULL, 'Ninguna', NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 202),
(316, 'MAURA ALEXANDRA', 'PUEBLA COLLADO', '2005-05-28', '21856306-6', NULL, NULL, 'PATRICIO LYNMCH', '', '0322534981', NULL, 'Ninguna', NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 203),
(317, 'GABRIELA MONSERRATT', 'MUÑOZ MUÑOZ', '2007-01-31', '22321903-9', NULL, NULL, 'CALLE DOLORES 830 PALMILLA ALTA, VILLA ALEMANA', 'YAJA_MUNOZLOZ@HOTMAIL.COM', '95574673', NULL, 'Ninguna', NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 204),
(318, 'FRANCISCA BEATRIZ', 'ARAYA OLIVARES', '2004-05-26', '21586605-K', NULL, NULL, 'CASTELLON', '', '', NULL, 'Ninguna', NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 205),
(319, 'BEATRIZ DEL CARMEN', 'AGUILERA ALCORCE', '1989-01-19', '17160501-6', NULL, NULL, 'TERCERA/LERIDA BLOCK A-20 DEPTO.308 POBL. GUMERCINDO', '', '2534580', NULL, 'Ninguna', NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 206),
(320, 'AZÚMI YUKARY', 'CORNEJO NÚÑEZ', '2007-05-10', '22396669-1', NULL, NULL, 'LONCOCHE , LA FRONTERA', '', '', NULL, 'Ninguna', NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 207),
(321, 'PAULINA ANDREA', 'ARAVENA GONZÁLEZ', '1985-12-02', '16330489-9', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 208),
(322, 'IGNACIO ANDRÉS', 'CHAPPA RODRÍGUEZ', '2006-08-17', '22192298-0', NULL, NULL, 'COVADONGA 465 , PEÑABLANCA', '', '', NULL, 'Ninguna', NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 209),
(323, 'KALID KARIN', 'LÍBANO ESPINOZA', '2008-08-04', '22793576-6', NULL, NULL, 'MALU GATICA', 'MACARENAESPINOZA36@GMAIL.COM', '', NULL, 'Ninguna', NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 210),
(324, 'JEAN PIERRE ANTONIO', 'JORQUERA BARLOW', '2008-08-13', '22801998-4', NULL, NULL, 'MARTA CALVIN 2412 TRONCOS VIEJOS', '', '', NULL, 'Ninguna', NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 211),
(325, 'MILLARAY ANTONELLA', 'CORREA MORALES', '2006-04-07', '22092362-2', NULL, NULL, 'DINAMARCA 1249 BLOCK 21 DPTO 203', '', '', NULL, 'Ninguna', NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 212),
(326, 'ARANTZA ROSE MARIE', 'VILLARROEL GÓMEZ', '2007-10-26', '22543940-0', NULL, NULL, 'LOS ZAPADORES, VILLA SAN JOSE', '', '', NULL, 'Ninguna', NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 213),
(327, 'MARIELA IVONNE', 'SUÁREZ GRANDI', '1990-06-08', '17568204-K', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 214),
(328, 'MAXIMILIANO ALONSO', 'MEZA PEREIRA', '2007-12-10', '22576513-8', NULL, NULL, 'PJE. OLMO', 'GABRIELA.76.PEREIRA@GMAIL.COM', '', NULL, 'Ninguna', NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 215),
(329, 'BELÉN ANTONELLA', 'ROMERO ROJAS', '2004-01-24', '21496517-8', NULL, NULL, 'ABTAO 238 PEÑABLANCA', '', '2722959', NULL, 'Ninguna', NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 216),
(330, 'GABRIEL EMMANUEL', 'VIVANCO CASTILLO', '2007-11-08', '22552279-0', NULL, NULL, 'CALLE CENTRAL LOTE 12 POBLACION LOMAS DE BELLAVISTA', 'TAMYCASTILLO31@GMAIL.COM', '947937085', NULL, 'Ninguna', NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 217),
(331, 'MAXIMILIANO BENJAMÍN', 'CONTRERAS ARANDA', '2005-07-04', '21921729-3', NULL, NULL, 'SAN RAFAEL 896', '', '978691262', NULL, 'Ninguna', NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 218),
(332, 'JAVIERA PAZ', 'CHÁVEZ CABRERA', '2007-09-08', '22497419-1', NULL, NULL, 'PJE LAGO CHAPO  POB.EL ÁLAMO', '', '', NULL, 'Ninguna', NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 219),
(333, 'KEVIN DANELL ALEJANDRO', 'CAMPOS DÍAZ', '2005-09-08', '21931670-4', NULL, NULL, 'HUANHUALI BLOCK 1549 DEPTO 42', 'GRISSEL.DIAZ@GMAIL.COM', '', NULL, 'Ninguna', NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 220),
(334, 'DAVY SEBASTIÁN ELÍAS ARIEL', 'MARTINEZ GÓMEZ', '2008-01-16', '22615611-9', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 221),
(335, 'WILLIAM MATÍAS NAHUM', 'CORTÉS GÓMEZ', '2007-08-01', '22467773-1', NULL, NULL, 'SAN IGNACIO', '', '965639097', NULL, 'Ninguna', NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 222),
(336, 'ANTONIA SOFÍA', 'ARREDONDO SOTOMAYOR', '2007-03-06', '22344863-1', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 223),
(337, 'KARLA ARACELLI', 'VALDIVIA ARAYA', '1999-02-08', '20067622-K', NULL, NULL, 'PIEDRA LOBO N° 1141', '', '', NULL, 'Ninguna', NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 224),
(338, 'MATÍAS WLADIMIR', 'PALMA FORMAS', '2008-06-14', '22747201-4', NULL, NULL, 'TOCORNAL 2374', '', '', NULL, 'Ninguna', NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 225),
(339, 'DILAN ANTHONY', 'BENÍTEZ ABARZÚA', '2007-10-03', '22520057-2', NULL, NULL, 'CALLE SEIS CASA 36 CERRO BARON', '', '', NULL, 'Ninguna', NULL, 7, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 226),
(340, 'PAULINA ALEJANDRA', 'VÁSQUEZ OYANEDEL', '2007-05-28', '22409510-4', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 7, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 227),
(341, 'FRANCISCA BELÉN', 'HERRERA MOLINA', '2001-01-16', '15201150-4', NULL, NULL, 'PASAJE ANCUD Nº 744 EL OASIS BELLOTO NORTE', '', '2982759', NULL, 'Ninguna', NULL, 7, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 228),
(342, 'JAVIERA MARCELA', 'ORELLANA ASTORGA', '2006-07-12', '22163368-7', NULL, NULL, 'LOS CARRERA 0242', '', '', NULL, 'Ninguna', NULL, 7, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 229),
(343, 'GLENY CONSTANZA', 'VELÁSQUEZ LOBOS', '2006-06-16', '22146697-7', NULL, NULL, 'EL RETIRO 1668 5TO SECTOR BELLOTO', '', '2337800', NULL, 'Ninguna', NULL, 7, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 230),
(344, 'BENJAMÍN FRANCISCO', 'CARRASCO FARÍAS', '2005-07-12', '21884336-0', NULL, NULL, 'LOS QUILLAYES', '', '', NULL, 'Ninguna', NULL, 7, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 231),
(345, 'JONATAN MANUEL', 'HERNÁNDEZ CHÁVEZ', '2006-10-01', '22237451-0', NULL, NULL, 'LINCOYAN 811, BELLOTO NORTE', '', '979285719', NULL, 'Ninguna', NULL, 7, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 232),
(346, 'GIOVANNI ANDRÉS', 'GARCÉS PALMA', '2007-03-19', '22378181-0', NULL, NULL, 'PASAJE LOS PIONEROS', '', '984906500', NULL, 'Ninguna', NULL, 7, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 233),
(347, 'BASTIÁN IGNACIO', 'GONZÁLEZ VERGARA', '2007-01-14', '22304293-7', NULL, NULL, 'LAGO JANOSA 2680 CONDOMINIO SAN ALBERTO LA FORESTA', 'BASTIAN.GONZALEZ@BARROSARANA.CMVA.CL', '', NULL, 'Ninguna', NULL, 7, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 234),
(348, 'ELÍAS FABIÁN', 'HERNÁNDEZ ARROYO', '2001-01-01', '20655048-1', NULL, NULL, 'MARÍA MERCEDES 1160, ROSENQUIST', '', '322563104', NULL, 'Ninguna', NULL, 7, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 235),
(349, 'MÓNICA DE LAS MERCEDES', 'OYANEDEL SAAVEDRA', '1982-04-01', '15437391-8', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 7, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 236),
(350, 'CHEILA DIANI', 'RIVERO CRUZ', '2004-10-15', '100751976-8', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 7, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 237),
(351, 'GONZALO ANDRÉS', 'VILLARROEL SOTOMAYOR', '1989-12-23', '17275325-6', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 7, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 238),
(352, 'ISAAC ALEJANDRO', 'RUBILAR CARTES', '2007-07-08', '22445193-8', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 7, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 239),
(353, 'RAFAEL ISAIAS ANTONIO', 'CARRASCO LONCOMILLA', '2005-07-17', '21891063-7', NULL, NULL, 'PJE. FREIRE 0652', 'RAFEALLONCOMILLA@GMAIL.COM', '', NULL, 'Ninguna', NULL, 7, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 240),
(354, 'THIARE ESTEFANÍA', 'VARGAS VICENCIO', '2005-05-15', '21843176-3', NULL, NULL, 'DINAMARCA 1320 HUANHUALI', '', '975426716', NULL, 'Ninguna', NULL, 7, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 241),
(355, 'BASTIÁN IGNACIO', 'PARRAGUIRRE VARGAS', '2007-04-30', '22385718-3', NULL, NULL, 'PASAJE ARAGON 0537, VILLA REINA', '', '', NULL, 'Ninguna', NULL, 7, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 242),
(356, 'SEBASTIÁN ALEXIS', 'LOBOS CANNOBBIO', '2005-01-19', '21763778-3', NULL, NULL, 'PASAJE ARMANDO CARRERA', '', '987948871', NULL, 'Ninguna', NULL, 7, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 243),
(357, 'DANIEL ALEXANDER', 'CALDERÓN RUSS', '2006-08-29', '22198948-1', NULL, NULL, 'PJE BERLIN Nº7 POB UNIOPORT', '', '2115180', NULL, 'Ninguna', NULL, 7, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 244),
(358, 'WILLIAM MATÍAS NAHUM', 'CORTÉS GÓMEZ', '2007-08-01', '22467773-1', NULL, NULL, 'SAN IGNACIO', '', '965639097', NULL, 'Ninguna', NULL, 7, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 245),
(359, 'NICOLÁS ANDRÉS', 'DÍAZ ARANCIBIA', '2007-10-31', '22542225-7', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 7, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 246),
(360, 'FERNANDO ANDRÉS', 'RAMÍREZ GARCÍA', '2001-09-22', '20850804-0', NULL, NULL, 'EUCALIPTUS, PJE. LAS MANDARINAS', '', '', NULL, 'Ninguna', NULL, 7, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 247),
(361, 'BENJAMÍN ALEJANDRO', 'AROS CABELLO', '2007-06-17', '22426016-4', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 7, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 248),
(362, 'BENJAMÍN GABRIEL', 'GARCÍA VICENCIO', '2007-04-16', '22377815-1', NULL, NULL, 'WAGNER', '', '977089647', NULL, 'Ninguna', NULL, 7, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 249),
(363, 'BRITANY ZUELY', 'TRONCOSO FERNÁNDEZ', '2004-03-09', '21529124-3', NULL, NULL, 'DINAMARCA CON SAN JOSE , BLOCK 19, DEP-103', 'JOSE.TRON35@GMAIL.COM', '', NULL, 'Ninguna', NULL, 7, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 250),
(364, 'ANA PAULA', 'SILVA DÍAZ', '1991-08-26', '17944711-8', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 7, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 251),
(365, 'DANIELA JESÚS', 'OLGUÍN SOTELO', '2004-08-06', '21631080-2', NULL, NULL, 'DAGOBERTO GODOY', '', '', NULL, 'Ninguna', NULL, 7, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 252),
(366, 'EDUARDO IGNACIO', 'VALENZUELA BECERRA', '2007-07-04', '22435497-5', NULL, NULL, 'LAS ACACIAS', 'EDUARDO.VALENZUELA@MANUELMONTT.CMVA.CL', '', NULL, 'Ninguna', NULL, 7, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 253),
(367, 'HÉCTOR ALEJANDRO', 'CARVAJAL CARVAJAL', '2007-07-06', '22437151-9', NULL, NULL, 'PLAZA AMERICA', '', '', NULL, 'Ninguna', NULL, 7, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 254),
(368, 'ANAHIS FRANCISCA', 'MALDONADO GÓMEZ', '2005-01-08', '21751618-8', NULL, NULL, 'PASAJE ATALAYA', 'SEPTIMOMELVINJONES2020@HOTMAIL.COM', '', NULL, 'Ninguna', NULL, 7, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 255),
(369, 'PATRICIA INES', 'SAAVEDRA GÁLVEZ', '2007-03-06', '22348392-5', NULL, NULL, 'VOLCAN', '', '', NULL, 'Ninguna', NULL, 7, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 256),
(370, 'AZÚMI YUKARY', 'CORNEJO NÚÑEZ', '2007-05-10', '22396669-1', NULL, NULL, 'LONCOCHE , LA FRONTERA', '', '', NULL, 'Ninguna', NULL, 7, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 257),
(371, 'MADELEINE DAYSI ANAÍS', 'SALDIVIA CAILEO', '2007-09-21', '22507082-2', NULL, NULL, 'BUTALCURA', '', '', NULL, 'Ninguna', NULL, 7, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 258),
(372, 'VÍCTOR MANUEL', 'PARRA SILVA', '2005-02-04', '21769626-7', NULL, NULL, 'PJE.LOISA 3758 POBL. MUJERES DEL FUTURO', '', '', NULL, 'Ninguna', NULL, 8, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 259),
(373, 'ISAAC PAOLO', 'ERAZO CARLOZA', '2005-08-29', '21919807-8', NULL, NULL, 'DAGOBERTO GODOY', '', '', NULL, 'Ninguna', NULL, 8, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 260),
(374, 'FRANCESCA NICOL', 'JARA RUBIO', '1996-05-31', '19193379-6', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 8, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 261),
(375, 'JOSÉ ANTONIO', 'ALVEAR ESPINA', '1992-05-11', '18036699-7', NULL, NULL, 'BDO. OHIGGINS Nº 279 NVA. AURORA', '', '', NULL, 'Ninguna', NULL, 8, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 262),
(376, 'LISSETE GRACIELA', 'RIQUELME ACEVEDO', '1995-06-07', '18998130-9', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 8, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 263),
(377, 'KEITHY BELEN', 'OLIVARES OLIVARES', '2005-12-20', '22006892-7', NULL, NULL, 'GABRIELA MISTRAL', 'KEITHYBELEN@GMAIL.COM', '', NULL, 'Ninguna', NULL, 8, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 264),
(378, 'BENJAMÍN FRANCISCO', 'CARRASCO FARÍAS', '2005-07-12', '21884336-0', NULL, NULL, 'LOS QUILLAYES', '', '', NULL, 'Ninguna', NULL, 8, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 265),
(379, 'NICOLÁS ESTEBAN', 'TRIGO RUBILAR', '2007-10-25', '22537728-6', NULL, NULL, 'VOLCAN MAIPO Nº 2602 C/LOS 4 PINOS', '', '', NULL, 'Ninguna', NULL, 8, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 266),
(380, 'CARLOS GABRIEL', 'CABRERA CARMONA', '1985-09-12', '16104814-3', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 8, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 267),
(381, 'LUIS RICARDO', 'STRELOW ARAYA', '1988-04-12', '16970250-0', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 8, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 268),
(382, 'BARBARA ESCARLET', 'GODOY VILLEGAS', '2005-09-25', '21943682-3', NULL, NULL, 'VIA 1-7 TRONCOS VIEJOS', 'BARBARAGODOY@LITECVA.CL', '940469792', NULL, 'Ninguna', NULL, 8, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 269),
(383, 'BELÉN ANTONELLA', 'ROMERO ROJAS', '2004-01-24', '21496517-8', NULL, NULL, 'ABTAO 238 PEÑABLANCA', '', '2722959', NULL, 'Ninguna', NULL, 8, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 270),
(384, 'ANAIS ANDREA', 'TAPIA ALTAMIRANO', '2006-08-14', '22186090-K', NULL, NULL, 'CALLE TEMUCO  POB.LA FRONTERA', '', '', NULL, 'Ninguna', NULL, 8, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 271);
INSERT INTO `matriculas` (`id`, `nombre_estudiante`, `apellidos_estudiante`, `fecha_nacimiento`, `rut_estudiante`, `serie_carnet_estudiante`, `etnia_estudiante`, `direccion_estudiante`, `correo_estudiante`, `telefono_estudiante`, `hijos_estudiante`, `situacion_especial_estudiante`, `programa_estudiante`, `curso_preferido`, `jornada_preferida`, `nombre_apoderado`, `rut_apoderado`, `parentezco_apoderado`, `direccion_apoderado`, `telefono_apoderado`, `situacion_especial_apoderado`, `fecha_registro`, `estado`, `estudiante_id`) VALUES
(385, 'CECILIA DE LAS MERCEDES', 'RODRÍGUEZ CARRASCO', '1961-06-13', '9231504-5', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 8, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 272),
(386, 'JUAN VICENTE IGNACIO', 'DÍAZ ROMÁN', '2004-12-05', '21724350-5', NULL, NULL, 'SAN ENRIQUE CON SERENA', '', '', NULL, 'Ninguna', NULL, 8, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 273),
(387, 'PEDRO ANTONIO', 'VILLARROEL RETAMAL', '1968-05-31', '10348925-3', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 8, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 274),
(388, 'PRISCILLA MARÍA', 'PAILLACÁN CARTE', '1986-09-15', '16500150-8', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 8, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 275),
(389, 'MARCO ANTONIO', 'BASAEZ DONOSO', '1990-10-17', '24442970-K', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 8, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 276),
(390, 'JASSON PATRICK', 'SCHMIDT JIMÉNEZ', '2004-05-31', '21585656-9', NULL, NULL, 'PJE. LONCOCHE', 'JSCHMIDT@LITECVA.CL', '2929005', NULL, 'Ninguna', NULL, 8, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 277),
(391, 'JOCELYN ALEJANDRA', 'DÍAZ GUTIÉRREZ', '1984-12-19', '15763765-7', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 8, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 278),
(392, 'NATALY ELVIRA', 'FERNÁNDEZ PEÑA', '1985-04-19', '16033947-0', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 8, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 279),
(393, 'BRITANY ZUELY', 'TRONCOSO FERNÁNDEZ', '2004-03-09', '21529124-3', NULL, NULL, 'DINAMARCA CON SAN JOSE , BLOCK 19, DEP-103', 'JOSE.TRON35@GMAIL.COM', '', NULL, 'Ninguna', NULL, 8, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 280),
(394, 'ESTEBAN RAFAEL', 'ARAVENA VILLARROEL', '1996-12-11', '19602512-K', NULL, NULL, 'DINAMARCA 1385, BLOCK 15, DPTO. 103', '', '', NULL, 'Ninguna', NULL, 8, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 281),
(395, 'DAINA FRANCHESCA', 'LABRADOR TOLOSA', '1993-08-13', '18552952-5', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 8, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 282),
(396, 'NICOLÁS ANDRÉS', 'TAPIA BÓRQUEZ', '1998-09-13', '19664838-0', NULL, NULL, 'DINAMARCA   1250   B-35  D-302', '', '', NULL, 'Ninguna', NULL, 8, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 283),
(397, 'CRISTINA JEISSY', 'ÁLVAREZ URRUTIA', '1989-08-18', '17143866-7', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 8, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 284),
(398, 'MAURA ALEXANDRA', 'PUEBLA COLLADO', '2005-05-28', '21856306-6', NULL, NULL, 'PATRICIO LYNMCH', '', '0322534981', NULL, 'Ninguna', NULL, 8, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 285),
(399, 'ALDEBARAN LEE', 'CANALES DÍAZ', '2006-06-20', '22146454-0', NULL, NULL, 'MARIA ISABEL BELLOTO', 'ALDEBARAN.CANALES@LATINAIGO.CMVA.CL', '322950764', NULL, 'Ninguna', NULL, 8, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 286),
(400, 'FELIPE IGNACIO', 'GÓMEZ OLIVARES', '2006-11-03', '22253000-8', NULL, NULL, '', 'MACA.OLIVARESM@GMAIL.COM', '986834060', NULL, 'Ninguna', NULL, 8, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 287),
(401, 'ANDRIU FABIÁN', 'CISTERNA LABARCA', '2004-08-04', '21635428-1', NULL, NULL, 'SANTA SARA', 'ACISTERNA@LITECVA.CL', '964986842', NULL, 'Ninguna', NULL, 8, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 288),
(402, 'ANTONIA SOFÍA', 'ARREDONDO SOTOMAYOR', '2007-03-06', '22344863-1', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 8, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 289),
(403, 'JENNIFFER DANIELA', 'QUIROZ VALDENEGRO', '1992-03-25', '18049318-2', NULL, NULL, 'OZMAN PÉREZ 0775', '', '', NULL, 'Ninguna', NULL, 8, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 290),
(404, 'VINCHENNZO ANÍBAL', 'SORIAGALVARRO NÚÑEZ', '2006-04-23', '22120440-9', NULL, NULL, 'FINLANDIA 2598', '', '', NULL, 'Ninguna', NULL, 9, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 291),
(405, 'FRANCESCA NICOL', 'JARA RUBIO', '1996-05-31', '19193379-6', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 9, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 292),
(406, 'LORENA ANDREA', 'GUAJARDO BENAVIDES', '1994-11-15', '18916444-0', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 9, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 293),
(407, 'ISIDORA ANTONIA', 'NÚÑEZ ARANCIBIA', '2006-12-02', '22274610-8', NULL, NULL, 'CALLE LAS ORQUIDEAS N°6 VILLA MONTE', '', '', NULL, 'Ninguna', NULL, 9, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 294),
(408, 'PEDRO MARCELO', 'FIGUEROA BRAVO', '2003-04-17', '21280440-1', NULL, NULL, 'HIPODROMO 350 POBL PRAT', '', '', NULL, 'Ninguna', NULL, 9, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 295),
(409, 'EMERSON BRAYAN SEBASTIÁN', 'BERNAL GONZÁLEZ', '2004-05-10', '21572043-8', NULL, NULL, 'FUNDO EL BOSQUE CASA 37 LAS CABRAS', '', '', NULL, 'Ninguna', NULL, 9, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 296),
(410, 'MILA JANA', 'ROJAS PINTO', '2005-05-29', '21872224-5', NULL, NULL, 'LOS DAMASCOS N° 1664', 'RJASMILA7@GMAIL.COM', '', NULL, 'Ninguna', NULL, 9, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 297),
(411, 'ANTONIA FERNANDA', 'ROJAS VALENZUELA', '2000-07-16', '20272272-5', NULL, NULL, 'PJE. LAS BOIRA TRONCOS VIEJOS', '', '2119298', NULL, 'Ninguna', NULL, 9, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 298),
(412, 'SOFÍA SCARLET PAZ', 'ARAYA OPAZO', '2006-07-20', '22166093-5', NULL, NULL, 'AVENIDA FRANCIA', '', '', NULL, 'Ninguna', NULL, 9, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 299),
(413, 'JEANETTE DE LA CRUZ', 'CABELLO BAEZA', '1985-03-05', '16033721-4', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 9, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 300),
(414, 'OSVALDO SALVADOR', 'CÁCERES ALTAMIRANO', '2001-11-29', '20851095-9', NULL, NULL, 'GABRIEL DAZZAROLA  1166  POB. GUMERCINDO', '', '', NULL, 'Ninguna', NULL, 9, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 301),
(415, 'ANIA MILENNE', 'ESTAY GUZMÁN', '2002-04-05', '21027817-6', NULL, NULL, 'FUNDO EL BOSQUE 121, SANTA ROSA MANZANA K-2, LOTE 16', 'CHARLESDARWINCOLEGIO2020@GMAIL.COM', '', NULL, 'Ninguna', NULL, 9, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 302),
(416, 'ANDREA PAOLA', 'MARCONI', '1979-06-09', '22260416-8', NULL, NULL, 'RAMIREZ', '', '', NULL, 'Ninguna', NULL, 9, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 303),
(417, 'ANGÉLICA MARÍA', 'MORALES GÓMEZ', '1985-09-25', '16058867-5', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 9, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 304),
(418, 'SANDRA DEL ROSARIO', 'SERENA VILLARROEL', '1972-12-24', '12450554-2', NULL, NULL, 'REGIDOR SADY JOVI POB. DON ESTEBAN', '', '', NULL, 'Ninguna', NULL, 9, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 305),
(419, 'CARLOS ALEJANDRO', 'MICHEAS MONDACA', '2008-05-06', '22719656-4', NULL, NULL, 'JOSE MIGUEL CARRERA', 'CARLOS.MICHEAS.M@EDUCAQUILPUE.CL', '', NULL, 'Ninguna', NULL, 9, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 306),
(420, 'PAMELA LUZMIRA', 'ROMERO OYARCE', '1976-08-21', '14316136-6', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 9, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 307),
(421, 'OSCAR NICOLAS', 'DEL CANTO MATAMALA', '2006-07-01', '22150376-7', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 9, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 308),
(422, 'GENESIS PALOMA', 'MELO HERRERA', '2006-08-30', '22201387-9', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 9, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 309),
(423, 'HENRY ESTEBAN', 'MUÑOZ BRITO', '1980-01-16', '13877408-2', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 9, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 310),
(424, 'MARCELA BELÉN', 'CÁRDENAS RÍOS', '1997-02-18', '19470651-0', NULL, NULL, 'POB. EX STA. ELENA PJE. BARROS ARANA 10B', '', '', NULL, 'Ninguna', NULL, 9, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 311),
(425, 'MAURO ARTURO', 'DEL CANTO MATAMALA', '2008-04-09', '22691669-5', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 9, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 312),
(426, 'BELEN NOEMI', 'MELO HERRERA', '2006-08-30', '22201394-1', NULL, NULL, 'SANTA ROSA 225', '', '', NULL, 'Ninguna', NULL, 9, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 313),
(427, 'LUCAS ISMAEL', 'RECABARREN JILIBERTO', '2004-08-17', '21641292-3', NULL, NULL, 'DINAMARCA', 'M.CONSTANZA.JILIBERTO@GMAIL.COM', '', NULL, 'Ninguna', NULL, 9, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 314),
(428, 'LISETT CATHERINE', 'CRONORO MARCOLETA', '1969-09-17', '10749318-2', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 9, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 315),
(429, 'JACQUELINE VALESKA DEL CARMEN', 'CASTRO TELLO', '1975-03-29', '12822006-2', NULL, NULL, '', '', '', NULL, 'Ninguna', NULL, 9, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 316),
(430, 'NICOLÁS MAURICIO', 'REYES MIRANDA', '2007-07-08', '22437922-6', NULL, NULL, 'TOCONAO N°1643, POMPEYA NORTE', 'NICOLAS.REYES@LATINAIGO.CMVA.CL', '2561284', NULL, 'Ninguna', NULL, 9, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 317),
(431, 'SCARLETT ALEXANDRA', 'MENA LOFF', '2007-08-26', '22487734-K', NULL, NULL, 'DINAMARCA 1350 BLOCK 39 DEPTO 103', '', '', NULL, 'Ninguna', NULL, 9, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 318),
(432, 'KEVIN DANELL ALEJANDRO', 'CAMPOS DÍAZ', '2005-09-08', '21931670-4', NULL, NULL, 'HUANHUALI BLOCK 1549 DEPTO 42', 'GRISSEL.DIAZ@GMAIL.COM', '', NULL, 'Ninguna', NULL, 9, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 319),
(433, 'JUAN IGNACIO', 'RAMÍREZ ESPINOZA', '2005-06-20', '21863790-6', NULL, NULL, 'RANCHILLO BAJO', 'ESCUELA.RANCHILLOS@GMAIL.COM', '', NULL, 'Ninguna', NULL, 9, NULL, NULL, NULL, NULL, NULL, NULL, 'Ninguna', '2026-01-31 03:10:23', 'Activa', 320);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_evaluacion`
--

CREATE TABLE `tipo_evaluacion` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_evaluacion`
--

INSERT INTO `tipo_evaluacion` (`id`, `nombre`, `descripcion`, `created_at`) VALUES
(1, 'Prueba', NULL, '2026-02-15 20:29:43'),
(2, 'Trabajo', NULL, '2026-02-15 20:29:43'),
(3, 'Disertación', NULL, '2026-02-15 20:29:43'),
(4, 'Proyecto', NULL, '2026-02-15 20:29:43'),
(5, 'Guía', NULL, '2026-02-15 20:29:43');

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
  `asignatura` enum('Lenguaje','Matemáticas','Estudios Sociales','Inglés','Inglés Comunicativo','TIC','Filosofía','Instrumental','Ciencias') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `correo`, `contrasena`, `fecha_registro`, `rol`, `asignatura`) VALUES
(1, 'Adrián Maturana Muñoz', 'maturana.or.adrian@gmail.com', '$2y$10$IPgMPJZGdI31iCSFkuQgGuG5mx2eipa9BXnaJDUfeI3QHsc/6LXJ.', '2025-06-11 04:31:42', 'editor', 'Estudios Sociales'),
(3, 'Director', 'director@gmail.com', '$2y$10$G0X0FLMq9Kxnza/VgGue8eEOQLmzE0uw2ec//c7XnnLB95tIH.9ou', '2025-06-11 05:55:39', 'admin', NULL),
(6, 'Noemi', 'adrian.buscando123@gmail.com', '$2y$10$6UyE41deZkLLkzCTgFF5MuXUiXy1RSt/VgriqprhRvVg9k9tkGY12', '2025-06-13 00:09:28', 'editor', 'Inglés'),
(7, 'Orlando', 'dsadas@gmail.com', '$2y$10$lm4l/9ojULtLywXL04zeQue50b8fHzWuhgx1n9K9KvTVVtAqNgAZ2', '2025-06-13 02:40:07', 'editor', 'Matemáticas'),
(13, 'profesor 1', 'profesor1@gmail.com', '$2y$10$3YGEoZOTEa0wfN2ju0MrSe6xHhbtY9TCNtUdefQOv4nFu2BX8Rj9K', '2025-11-25 09:13:55', 'editor', 'Inglés Comunicativo'),
(15, 'profesor2', 'profesor2@gmail.com', '$2y$10$dcODeHL6H7D8NuUWEuUAbeCBwXgG5ORbIyz0CR2qJRyRVuMB0L7Yy', '2025-11-25 09:20:36', 'editor', 'Ciencias'),
(16, 'profesor3', 'profesor3@gmail.com', '$2y$10$0oE.uk.GnYCf2mTwFN7KiOlAVvN0vk6ggPPKVc/HVO.NmThxEdFYS', '2025-11-25 09:21:19', 'editor', 'Lenguaje'),
(18, 'profesor4', 'profesor4@gmail.com', '$2y$10$38sRNMrXtlJ8TMxMZUX4hehZfttFR0VJJN/wPf0CNMRcDQQJHjVjm', '2025-11-25 09:22:46', 'editor', 'TIC'),
(19, 'MIGUEL ARAYA BARRERA', 'centroestudiossanignacio@vtr.net', '$2y$10$JUsu1y7g4mNqbILkd./.s.xSDB936O.uuyE9a/I32aYRSpTj28bGO', '2025-12-04 18:04:00', 'admin', NULL);

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
-- Indices de la tabla `bloques_horarios`
--
ALTER TABLE `bloques_horarios`
  ADD PRIMARY KEY (`id`);

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
-- Indices de la tabla `evaluaciones`
--
ALTER TABLE `evaluaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `curso_profesor_id` (`curso_profesor_id`),
  ADD KEY `tipo_id` (`tipo_id`);

--
-- Indices de la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `curso_id` (`curso_id`,`dia`,`bloque_id`),
  ADD KEY `bloque_id` (`bloque_id`),
  ADD KEY `fk_horarios_asignatura` (`asignatura_id`);

--
-- Indices de la tabla `matriculas`
--
ALTER TABLE `matriculas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_curso_preferido` (`curso_preferido`),
  ADD KEY `fk_matriculas_estudiante` (`estudiante_id`);

--
-- Indices de la tabla `tipo_evaluacion`
--
ALTER TABLE `tipo_evaluacion`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT de la tabla `bloques_horarios`
--
ALTER TABLE `bloques_horarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `curso_asignatura`
--
ALTER TABLE `curso_asignatura`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT de la tabla `curso_profesor`
--
ALTER TABLE `curso_profesor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=321;

--
-- AUTO_INCREMENT de la tabla `evaluaciones`
--
ALTER TABLE `evaluaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `horarios`
--
ALTER TABLE `horarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de la tabla `matriculas`
--
ALTER TABLE `matriculas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=434;

--
-- AUTO_INCREMENT de la tabla `tipo_evaluacion`
--
ALTER TABLE `tipo_evaluacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

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
-- Filtros para la tabla `evaluaciones`
--
ALTER TABLE `evaluaciones`
  ADD CONSTRAINT `evaluaciones_ibfk_1` FOREIGN KEY (`curso_profesor_id`) REFERENCES `curso_profesor` (`id`),
  ADD CONSTRAINT `evaluaciones_ibfk_2` FOREIGN KEY (`tipo_id`) REFERENCES `tipo_evaluacion` (`id`);

--
-- Filtros para la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD CONSTRAINT `fk_horarios_asignatura` FOREIGN KEY (`asignatura_id`) REFERENCES `asignaturas` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `horarios_ibfk_1` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`),
  ADD CONSTRAINT `horarios_ibfk_2` FOREIGN KEY (`bloque_id`) REFERENCES `bloques_horarios` (`id`);

--
-- Filtros para la tabla `matriculas`
--
ALTER TABLE `matriculas`
  ADD CONSTRAINT `fk_curso_preferido` FOREIGN KEY (`curso_preferido`) REFERENCES `cursos` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_matriculas_estudiante` FOREIGN KEY (`estudiante_id`) REFERENCES `estudiantes` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
