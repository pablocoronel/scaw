-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 01-07-2015 a las 19:47:52
-- Versión del servidor: 5.5.8
-- Versión de PHP: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `scaw`
--

CREATE DATABASE `scaw` DEFAULT CHARACTER SET utf8 COLLATE utf8_spanish_ci;
USE `scaw`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `archivo`
--

CREATE TABLE IF NOT EXISTS `archivo` (
  `idArchivo` int(10) NOT NULL AUTO_INCREMENT,
  `nombreArchivo` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `tipoCompartido` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `versionActual` int(10) NOT NULL,
  `fkUsuarioCreador` int(10) NOT NULL,
  `codigoArchivo` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`idArchivo`),
  KEY `arc_usu_fk` (`fkUsuarioCreador`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `archivo`
--

INSERT INTO `archivo` (`idArchivo`, `nombreArchivo`, `tipoCompartido`, `versionActual`, `fkUsuarioCreador`, `codigoArchivo`) VALUES
(1, 'Archivo de prueba.txt', 'privado', 1, 2, 'z66v1irr'),
(2, 'Archivo de prueba.txt', 'publico', 2, 1, '1cq900e3');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `archivo_version`
--

CREATE TABLE IF NOT EXISTS `archivo_version` (
  `idArchivoVersion` int(10) NOT NULL AUTO_INCREMENT,
  `fkArchivo` int(10) NOT NULL,
  `version` int(10) NOT NULL,
  `ruta` varchar(200) COLLATE utf8_spanish_ci NOT NULL,
  `peso` int(50) NOT NULL,
  PRIMARY KEY (`idArchivoVersion`),
  KEY `avs_arc_fk` (`fkArchivo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `archivo_version`
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
  `idComentario` int(10) NOT NULL AUTO_INCREMENT,
  `fkArchivo` int(10) NOT NULL,
  `fkUsuario` int(10) NOT NULL,
  `comentario` varchar(500) COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`idComentario`),
  KEY `com_arc_fk` (`fkArchivo`),
  KEY `com_usu_fk` (`fkUsuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `comentario`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE IF NOT EXISTS `usuario` (
  `idUsuario` int(10) NOT NULL AUTO_INCREMENT,
  `nivel` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `usuario` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `clave` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `espacioAsignado` int(50) NOT NULL COMMENT '1 MB=1048576 B (10 MB por defecto)',
  `estadoRegistro` int(10) NOT NULL COMMENT '0:NO hablilitado / 1:Habilitado / 2:Eliminado',
  PRIMARY KEY (`idUsuario`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=2 ;

--
-- Volcar la base de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`idUsuario`, `nivel`, `usuario`, `clave`, `espacioAsignado`, `estadoRegistro`) VALUES
(1, 'administrador', 'pablo', '2a52adc7b1da6a4e0a7a14e4c8db1b11', 52428800, 1),
(2, 'usuario', 'vanesa', 'c383d524d15b9e3812e6c696af2067ce', 10485760, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_editor`
--

CREATE TABLE IF NOT EXISTS `usuario_editor` (
  `idUsuarioEditor` int(10) NOT NULL AUTO_INCREMENT,
  `fkArchivo` int(10) NOT NULL,
  `fkUsuario` int(10) NOT NULL,
  PRIMARY KEY (`idUsuarioEditor`),
  KEY `ued_arc_fk` (`fkArchivo`),
  KEY `ued_usu_fk` (`fkUsuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `usuario_editor`
--

INSERT INTO `usuario_editor` (`idUsuarioEditor`, `fkArchivo`, `fkUsuario`) VALUES
(1, 1, 2),
(2, 2, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_lector`
--

CREATE TABLE IF NOT EXISTS `usuario_lector` (
  `idUsuarioLector` int(10) NOT NULL AUTO_INCREMENT,
  `fkArchivo` int(10) NOT NULL,
  `fkUsuario` int(10) NOT NULL,
  PRIMARY KEY (`idUsuarioLector`),
  KEY `ule_arc_fk` (`fkArchivo`),
  KEY `ule_usu_fk` (`fkUsuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `usuario_lector`
--

INSERT INTO `usuario_lector` (`idUsuarioLector`, `fkArchivo`, `fkUsuario`) VALUES
(1, 1, 2),
(2, 1, 1);

--
-- Filtros para las tablas descargadas (dump)
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
