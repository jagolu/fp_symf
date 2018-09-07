-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-09-2018 a las 16:27:22
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
CREATE DATABASE IF NOT EXISTS `app_porras` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `app_porras`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `player`
--

CREATE TABLE `player` (
  `id_player` int(4) NOT NULL,
  `id_team` int(2) NOT NULL,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `fullName` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `position` int(1) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `goals` int(3) NOT NULL,
  `shots` int(4) NOT NULL,
  `passes` int(4) NOT NULL,
  `assits` int(3) NOT NULL,
  `recoveries` int(4) NOT NULL,
  `goals_conceded` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `room`
--

CREATE TABLE `room` (
  `id_room` int(10) NOT NULL,
  `type` int(1) NOT NULL,
  `date_begin` date NOT NULL,
  `name` varchar(50) NOT NULL,
  `password` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `seasonbet`
--

CREATE TABLE `seasonbet` (
  `id_user` int(10) NOT NULL,
  `id_room` int(10) NOT NULL,
  `type` int(1) NOT NULL,
  `goals` int(4) DEFAULT NULL,
  `shots` int(4) DEFAULT NULL,
  `passes` int(4) DEFAULT NULL,
  `assits` int(4) DEFAULT NULL,
  `recoveries` int(4) DEFAULT NULL,
  `goals_conceded` int(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `team`
--

CREATE TABLE `team` (
  `id_team` int(2) NOT NULL,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `pix` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `position` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user`
--

CREATE TABLE `user` (
  `id_user` int(10) NOT NULL,
  `email` varchar(50) NOT NULL,
  `nickname` varchar(50) NOT NULL,
  `password` varchar(500) NOT NULL,
  `is_active` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_room`
--

CREATE TABLE `user_room` (
  `id_user` int(10) NOT NULL,
  `id_room` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `player`
--
ALTER TABLE `player`
  ADD PRIMARY KEY (`id_player`),
  ADD KEY `id_team_fk` (`id_team`) USING BTREE;

--
-- Indices de la tabla `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`id_room`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indices de la tabla `seasonbet`
--
ALTER TABLE `seasonbet`
  ADD PRIMARY KEY (`id_user`,`id_room`,`type`),
  ADD KEY `goals_fk` (`goals`),
  ADD KEY `shots_fk` (`shots`),
  ADD KEY `passes_fk` (`passes`),
  ADD KEY `recoveries_fk` (`recoveries`),
  ADD KEY `goals_conceded_fk` (`goals_conceded`),
  ADD KEY `assits_fk` (`assits`),
  ADD KEY `room_fk` (`id_room`);

--
-- Indices de la tabla `team`
--
ALTER TABLE `team`
  ADD PRIMARY KEY (`id_team`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indices de la tabla `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `user_room`
--
ALTER TABLE `user_room`
  ADD PRIMARY KEY (`id_user`,`id_room`),
  ADD KEY `id_room_fk` (`id_room`) USING BTREE;

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `room`
--
ALTER TABLE `room`
  MODIFY `id_room` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(10) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `player`
--
ALTER TABLE `player`
  ADD CONSTRAINT `id_team_fk` FOREIGN KEY (`id_team`) REFERENCES `team` (`id_team`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `seasonbet`
--
ALTER TABLE `seasonbet`
  ADD CONSTRAINT `assits_fk` FOREIGN KEY (`assits`) REFERENCES `player` (`id_player`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `goals_conceded_fk` FOREIGN KEY (`goals_conceded`) REFERENCES `player` (`id_player`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `goals_fk` FOREIGN KEY (`goals`) REFERENCES `player` (`id_player`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `passes_fk` FOREIGN KEY (`passes`) REFERENCES `player` (`id_player`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `recoveries_fk` FOREIGN KEY (`recoveries`) REFERENCES `player` (`id_player`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `room_fk` FOREIGN KEY (`id_room`) REFERENCES `room` (`id_room`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `shots_fk` FOREIGN KEY (`shots`) REFERENCES `player` (`id_player`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_fk` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `user_room`
--
ALTER TABLE `user_room`
  ADD CONSTRAINT `id_room_fk` FOREIGN KEY (`id_room`) REFERENCES `room` (`id_room`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_user_fk` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
