-- phpMyAdmin SQL Dump
-- version 4.4.14
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 01-01-2006 a las 04:12:11
-- Versión del servidor: 5.6.26
-- Versión de PHP: 5.6.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `scaw`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `archivo`
--

CREATE TABLE IF NOT EXISTS `archivo` (
  `idArchivo` int(10) NOT NULL,
  `nombreArchivo` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `tipoCompartido` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `versionActual` int(10) NOT NULL,
  `fkUsuarioCreador` int(10) NOT NULL,
  `codigoArchivo` varchar(50) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `archivo`
--

INSERT INTO `archivo` (`idArchivo`, `nombreArchivo`, `tipoCompartido`, `versionActual`, `fkUsuarioCreador`, `codigoArchivo`) VALUES
(1, 'Archivo de prueba.txt', 'privado', 1, 2, 'z66v1irr'),
(2, 'Archivo de prueba.txt', 'publico', 2, 1, '1cq900e3');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `archivo_version`
--

CREATE TABLE IF NOT EXISTS `archivo_version` (
  `idArchivoVersion` int(10) NOT NULL,
  `fkArchivo` int(10) NOT NULL,
  `version` int(10) NOT NULL,
  `ruta` varchar(200) COLLATE utf8_spanish_ci NOT NULL,
  `peso` int(50) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `archivo_version`
--

INSERT INTO `archivo_version` (`idArchivoVersion`, `fkArchivo`, `version`, `ruta`, `peso`) VALUES
(1, 1, 1, '../files/archivo_1/version_1/Archivo de prueba.txt', 17),
(2, 2, 1, '../files/archivo_2/version_1/Archivo de prueba.txt', 17),
(3, 2, 2, '../files/archivo_2/version_2/Archivo de prueba.txt', 46);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentario`
--

CREATE TABLE IF NOT EXISTS `comentario` (
  `idComentario` int(10) NOT NULL,
  `fkArchivo` int(10) NOT NULL,
  `fkUsuario` int(10) NOT NULL,
  `comentario` varchar(500) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE IF NOT EXISTS `usuario` (
  `idUsuario` int(10) NOT NULL,
  `nivel` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `usuario` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `clave` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `espacioAsignado` int(50) NOT NULL COMMENT '1 MB=1048576 B (10 MB por defecto)',
  `estadoRegistro` int(10) NOT NULL COMMENT '0:NO hablilitado / 1:Habilitado / 2:Eliminado'
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`idUsuario`, `nivel`, `usuario`, `clave`, `espacioAsignado`, `estadoRegistro`) VALUES
(1, 'administrador', 'pablo', '2a52adc7b1da6a4e0a7a14e4c8db1b11', 52428800, 1),
(2, 'usuario', 'vanesa', 'c383d524d15b9e3812e6c696af2067ce', 10485760, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_editor`
--

CREATE TABLE IF NOT EXISTS `usuario_editor` (
  `idUsuarioEditor` int(10) NOT NULL,
  `fkArchivo` int(10) NOT NULL,
  `fkUsuario` int(10) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `usuario_editor`
--

INSERT INTO `usuario_editor` (`idUsuarioEditor`, `fkArchivo`, `fkUsuario`) VALUES
(1, 1, 2),
(2, 2, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_lector`
--

CREATE TABLE IF NOT EXISTS `usuario_lector` (
  `idUsuarioLector` int(10) NOT NULL,
  `fkArchivo` int(10) NOT NULL,
  `fkUsuario` int(10) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `usuario_lector`
--

INSERT INTO `usuario_lector` (`idUsuarioLector`, `fkArchivo`, `fkUsuario`) VALUES
(1, 1, 2),
(2, 1, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `archivo`
--
ALTER TABLE `archivo`
  ADD PRIMARY KEY (`idArchivo`),
  ADD KEY `arc_usu_fk` (`fkUsuarioCreador`);

--
-- Indices de la tabla `archivo_version`
--
ALTER TABLE `archivo_version`
  ADD PRIMARY KEY (`idArchivoVersion`),
  ADD KEY `avs_arc_fk` (`fkArchivo`);

--
-- Indices de la tabla `comentario`
--
ALTER TABLE `comentario`
  ADD PRIMARY KEY (`idComentario`),
  ADD KEY `com_arc_fk` (`fkArchivo`),
  ADD KEY `com_usu_fk` (`fkUsuario`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`idUsuario`);

--
-- Indices de la tabla `usuario_editor`
--
ALTER TABLE `usuario_editor`
  ADD PRIMARY KEY (`idUsuarioEditor`),
  ADD KEY `ued_arc_fk` (`fkArchivo`),
  ADD KEY `ued_usu_fk` (`fkUsuario`);

--
-- Indices de la tabla `usuario_lector`
--
ALTER TABLE `usuario_lector`
  ADD PRIMARY KEY (`idUsuarioLector`),
  ADD KEY `ule_arc_fk` (`fkArchivo`),
  ADD KEY `ule_usu_fk` (`fkUsuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `archivo`
--
ALTER TABLE `archivo`
  MODIFY `idArchivo` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `archivo_version`
--
ALTER TABLE `archivo_version`
  MODIFY `idArchivoVersion` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `comentario`
--
ALTER TABLE `comentario`
  MODIFY `idComentario` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `idUsuario` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `usuario_editor`
--
ALTER TABLE `usuario_editor`
  MODIFY `idUsuarioEditor` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `usuario_lector`
--
ALTER TABLE `usuario_lector`
  MODIFY `idUsuarioLector` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `archivo`
--
ALTER TABLE `archivo`
  ADD CONSTRAINT `arc_usu_fk` FOREIGN KEY (`fkUsuarioCreador`) REFERENCES `usuario` (`idUsuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `archivo_version`
--
ALTER TABLE `archivo_version`
  ADD CONSTRAINT `avs_arc_fk` FOREIGN KEY (`fkArchivo`) REFERENCES `archivo` (`idArchivo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `comentario`
--
ALTER TABLE `comentario`
  ADD CONSTRAINT `com_arc_fk` FOREIGN KEY (`fkArchivo`) REFERENCES `archivo` (`idArchivo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `com_usu_fk` FOREIGN KEY (`fkUsuario`) REFERENCES `usuario` (`idUsuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario_editor`
--
ALTER TABLE `usuario_editor`
  ADD CONSTRAINT `ued_arc_fk` FOREIGN KEY (`fkArchivo`) REFERENCES `archivo` (`idArchivo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ued_usu_fk` FOREIGN KEY (`fkUsuario`) REFERENCES `usuario` (`idUsuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario_lector`
--
ALTER TABLE `usuario_lector`
  ADD CONSTRAINT `ule_usu_fk` FOREIGN KEY (`fkArchivo`) REFERENCES `archivo` (`idArchivo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `usuario_lector_ibfk_1` FOREIGN KEY (`fkUsuario`) REFERENCES `usuario` (`idUsuario`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
