-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 31-07-2018 a las 22:32:35
-- Versión del servidor: 10.1.34-MariaDB
-- Versión de PHP: 7.2.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `app_porras`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migration_versions`
--

CREATE TABLE `migration_versions` (
  `version` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `migration_versions`
--

INSERT INTO `migration_versions` (`version`) VALUES
('20180718154109');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `room`
--

CREATE TABLE `room` (
  `id_room` int(5) NOT NULL,
  `password` varchar(30) NOT NULL,
  `type` int(1) NOT NULL,
  `name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `room`
--

INSERT INTO `room` (`id_room`, `password`, `type`, `name`) VALUES
(1, 'asdfasdf', 1, 'olala');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `teams`
--

CREATE TABLE `teams` (
  `id_team` int(2) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `pixeles` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `teams`
--

INSERT INTO `teams` (`id_team`, `nombre`, `pixeles`) VALUES
(1, 'Athletic Club', '0 -200px'),
(2, 'AtlÃ©tico de Madrid', '0 -240px'),
(3, 'CD LeganÃ©s', '0 -920px'),
(4, 'D. AlavÃ©s', '0 -40px'),
(5, 'FC Barcelona', '0 -280px'),
(6, 'Getafe CF', '0 -680px'),
(7, 'Girona FC', '0 -760px'),
(8, 'Levante UD', '0 -960px'),
(9, 'R. Valladolid CF', '0 -1600px'),
(10, 'Rayo Vallecano', '0 -1240px'),
(11, 'RC Celta', '0 -400px'),
(12, 'RCD Espanyol', '0 -600px'),
(13, 'Real Betis', '0 -320px'),
(14, 'Real Madrid', '0 -1320px'),
(15, 'Real Sociedad', '0 -1360px'),
(16, 'SD Eibar', '0 -520px'),
(17, 'SD Huesca', '0 -840px'),
(18, 'Sevilla FC', '0 -1440px'),
(19, 'Valencia CF', '0 -1560px'),
(20, 'Villarreal CF', '0 -1640px');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user`
--

CREATE TABLE `user` (
  `id_user` int(5) NOT NULL,
  `email` varchar(50) NOT NULL,
  `nickname` varchar(30) NOT NULL,
  `password` varchar(100) NOT NULL,
  `is_active` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `user`
--

INSERT INTO `user` (`id_user`, `email`, `nickname`, `password`, `is_active`) VALUES
(32, 'a@gmail.com', 'asdf', '$2y$15$nC7Ar3uVNKQrdMdc1HIDkegCvy0WpL/wiAK8CGf1UdT24SoosCh0K', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_room`
--

CREATE TABLE `user_room` (
  `user_id` int(5) NOT NULL,
  `room_id` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `migration_versions`
--
ALTER TABLE `migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Indices de la tabla `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`id_room`),
  ADD KEY `id_room` (`id_room`);

--
-- Indices de la tabla `teams`
--
ALTER TABLE `teams`
  ADD PRIMARY KEY (`id_team`);

--
-- Indices de la tabla `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD KEY `id_user` (`id_user`);

--
-- Indices de la tabla `user_room`
--
ALTER TABLE `user_room`
  ADD PRIMARY KEY (`user_id`,`room_id`),
  ADD KEY `IDX_81E1D5254177093` (`room_id`) USING BTREE;

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `room`
--
ALTER TABLE `room`
  MODIFY `id_room` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `teams`
--
ALTER TABLE `teams`
  MODIFY `id_team` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `user_room`
--
ALTER TABLE `user_room`
  ADD CONSTRAINT `FK_81E1D5254177093` FOREIGN KEY (`room_id`) REFERENCES `room` (`id_room`),
  ADD CONSTRAINT `FK_81E1D52A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id_user`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
