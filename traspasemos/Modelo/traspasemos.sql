-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-10-2025 a las 21:39:25
-- Versión del servidor: 10.4.17-MariaDB
-- Versión de PHP: 8.0.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `traspasemo`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aspacadem`
--

CREATE TABLE `aspacadem` (
  `IDAspectoAcad` int(11) NOT NULL,
  `NombreAsp` int(70) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aspectocomport`
--

CREATE TABLE `aspectocomport` (
  `IdAspectoCompo` int(11) NOT NULL,
  `NombreAsp` varchar(70) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aspectosremision`
--

CREATE TABLE `aspectosremision` (
  `IdAspectosRemi` int(11) NOT NULL,
  `IdRemision` int(11) NOT NULL,
  `IdAspectoAcad` int(11) NOT NULL,
  `Aplica` varchar(50) NOT NULL,
  `Observacion` varchar(250) NOT NULL,
  `IdAspectCompo` int(11) NOT NULL,
  `Cumple` varchar(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ciudad`
--

CREATE TABLE `ciudad` (
  `IdCiudad` int(11) NOT NULL,
  `Descripcion` varchar(70) NOT NULL,
  `IdDepto` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `datosestudiante`
--

CREATE TABLE `datosestudiante` (
  `IdDatosEst` int(11) NOT NULL,
  `IdUsuario` int(11) NOT NULL,
  `FechaNacimiento` date NOT NULL,
  `IdGrupo` int(11) NOT NULL,
  `Direccion` varchar(50) NOT NULL,
  `Barrio` varchar(50) NOT NULL,
  `IdCiudad` int(11) NOT NULL,
  `IdDepto` int(11) NOT NULL,
  `EPS` varchar(50) NOT NULL,
  `NombreAcudiente` varchar(70) NOT NULL,
  `CelularAcudiente` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departamento`
--

CREATE TABLE `departamento` (
  `IdeDepartamente` int(11) NOT NULL,
  `Descripcion` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `discapacidad`
--

CREATE TABLE `discapacidad` (
  `IdDiscapacidad` int(11) NOT NULL,
  `Nombre` varchar(50) NOT NULL,
  `IdTipoDiscacidad` int(11) NOT NULL,
  `Observaciones` varchar(205) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `diversidadfuncional`
--

CREATE TABLE `diversidadfuncional` (
  `IdDiversidad` int(11) NOT NULL,
  `Nombre` varchar(70) NOT NULL,
  `Observaciones` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `diversidadfuncionalest`
--

CREATE TABLE `diversidadfuncionalest` (
  `IdDiversFuncEstud` int(11) NOT NULL,
  `IdEstudiante` int(11) NOT NULL,
  `IdDiversidad` int(11) NOT NULL,
  `IdDiscapacidad` int(11) NOT NULL,
  `FechaReporte` date NOT NULL,
  `Recomendaciones` blob NOT NULL,
  `Observacion` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grado`
--

CREATE TABLE `grado` (
  `Idgrado` int(11) NOT NULL,
  `Descripcion` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupo`
--

CREATE TABLE `grupo` (
  `Idgrupo` int(11) NOT NULL,
  `Descripcion` varchar(30) NOT NULL,
  `Sede` varchar(50) NOT NULL,
  `Jornada` varchar(30) NOT NULL,
  `DirectorGrupo` varchar(70) NOT NULL,
  `IdGrado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `remision`
--

CREATE TABLE `remision` (
  `idRemision` int(11) NOT NULL,
  `IdEstudiante` int(11) NOT NULL,
  `Motivo` text NOT NULL,
  `OtrosCompor` varchar(250) NOT NULL,
  `ComportamentalProb` varchar(250) NOT NULL,
  `Estrategias` text NOT NULL,
  `Observaciones` text NOT NULL,
  `Docente` varchar(70) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipodiscapacidad`
--

CREATE TABLE `tipodiscapacidad` (
  `IdTipoDisc` int(11) NOT NULL,
  `Descripcion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipodoc`
--

CREATE TABLE `tipodoc` (
  `IdTipoDoc` int(11) NOT NULL,
  `Descripcion` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipousuario`
--

CREATE TABLE `tipousuario` (
  `IdTipoUsuario` int(11) NOT NULL,
  `Descripcion` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `IdUsuarios` int(11) NOT NULL,
  `IdTipoDoc` int(11) NOT NULL,
  `IdTipoUsuario` int(11) NOT NULL,
  `NombreUsua` varchar(50) NOT NULL,
  `ApellidosUsua` varchar(50) NOT NULL,
  `Celular` varchar(15) NOT NULL,
  `Correo` varchar(50) NOT NULL,
  `Contraseña` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `aspacadem`
--
ALTER TABLE `aspacadem`
  ADD PRIMARY KEY (`IDAspectoAcad`);

--
-- Indices de la tabla `aspectocomport`
--
ALTER TABLE `aspectocomport`
  ADD PRIMARY KEY (`IdAspectoCompo`);

--
-- Indices de la tabla `aspectosremision`
--
ALTER TABLE `aspectosremision`
  ADD PRIMARY KEY (`IdAspectosRemi`),
  ADD KEY `IdRemision` (`IdRemision`,`IdAspectoAcad`,`IdAspectCompo`);

--
-- Indices de la tabla `ciudad`
--
ALTER TABLE `ciudad`
  ADD PRIMARY KEY (`IdCiudad`),
  ADD KEY `IdDepto` (`IdDepto`);

--
-- Indices de la tabla `datosestudiante`
--
ALTER TABLE `datosestudiante`
  ADD PRIMARY KEY (`IdDatosEst`),
  ADD KEY `IdCiudad` (`IdCiudad`,`IdDepto`),
  ADD KEY `IdUsuario` (`IdUsuario`),
  ADD KEY `IdDepto` (`IdDepto`),
  ADD KEY `IdGrupo` (`IdGrupo`);

--
-- Indices de la tabla `departamento`
--
ALTER TABLE `departamento`
  ADD PRIMARY KEY (`IdeDepartamente`);

--
-- Indices de la tabla `discapacidad`
--
ALTER TABLE `discapacidad`
  ADD PRIMARY KEY (`IdDiscapacidad`),
  ADD KEY `IdTipoDiscacidad` (`IdTipoDiscacidad`);

--
-- Indices de la tabla `diversidadfuncional`
--
ALTER TABLE `diversidadfuncional`
  ADD PRIMARY KEY (`IdDiversidad`);

--
-- Indices de la tabla `diversidadfuncionalest`
--
ALTER TABLE `diversidadfuncionalest`
  ADD PRIMARY KEY (`IdDiversFuncEstud`),
  ADD KEY `IdEstudiante` (`IdEstudiante`,`IdDiversidad`,`IdDiscapacidad`),
  ADD KEY `IdDiversidad` (`IdDiversidad`),
  ADD KEY `IdDiscapacidad` (`IdDiscapacidad`);

--
-- Indices de la tabla `grado`
--
ALTER TABLE `grado`
  ADD PRIMARY KEY (`Idgrado`);

--
-- Indices de la tabla `grupo`
--
ALTER TABLE `grupo`
  ADD PRIMARY KEY (`Idgrupo`),
  ADD KEY `IdGrado` (`IdGrado`);

--
-- Indices de la tabla `remision`
--
ALTER TABLE `remision`
  ADD PRIMARY KEY (`idRemision`),
  ADD KEY `IdEstudiante` (`IdEstudiante`);

--
-- Indices de la tabla `tipodiscapacidad`
--
ALTER TABLE `tipodiscapacidad`
  ADD PRIMARY KEY (`IdTipoDisc`);

--
-- Indices de la tabla `tipodoc`
--
ALTER TABLE `tipodoc`
  ADD PRIMARY KEY (`IdTipoDoc`);

--
-- Indices de la tabla `tipousuario`
--
ALTER TABLE `tipousuario`
  ADD PRIMARY KEY (`IdTipoUsuario`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`IdUsuarios`),
  ADD KEY `IdTipoDoc` (`IdTipoDoc`,`IdTipoUsuario`),
  ADD KEY `IdTipoUsuario` (`IdTipoUsuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `aspacadem`
--
ALTER TABLE `aspacadem`
  MODIFY `IDAspectoAcad` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `aspectocomport`
--
ALTER TABLE `aspectocomport`
  MODIFY `IdAspectoCompo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `aspectosremision`
--
ALTER TABLE `aspectosremision`
  MODIFY `IdAspectosRemi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ciudad`
--
ALTER TABLE `ciudad`
  MODIFY `IdCiudad` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `datosestudiante`
--
ALTER TABLE `datosestudiante`
  MODIFY `IdDatosEst` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `departamento`
--
ALTER TABLE `departamento`
  MODIFY `IdeDepartamente` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `discapacidad`
--
ALTER TABLE `discapacidad`
  MODIFY `IdDiscapacidad` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `diversidadfuncional`
--
ALTER TABLE `diversidadfuncional`
  MODIFY `IdDiversidad` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `diversidadfuncionalest`
--
ALTER TABLE `diversidadfuncionalest`
  MODIFY `IdDiversFuncEstud` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `grado`
--
ALTER TABLE `grado`
  MODIFY `Idgrado` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `grupo`
--
ALTER TABLE `grupo`
  MODIFY `Idgrupo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `remision`
--
ALTER TABLE `remision`
  MODIFY `idRemision` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tipodiscapacidad`
--
ALTER TABLE `tipodiscapacidad`
  MODIFY `IdTipoDisc` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tipodoc`
--
ALTER TABLE `tipodoc`
  MODIFY `IdTipoDoc` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tipousuario`
--
ALTER TABLE `tipousuario`
  MODIFY `IdTipoUsuario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `IdUsuarios` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `ciudad`
--
ALTER TABLE `ciudad`
  ADD CONSTRAINT `ciudad_ibfk_1` FOREIGN KEY (`IdDepto`) REFERENCES `departamento` (`IdeDepartamente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `datosestudiante`
--
ALTER TABLE `datosestudiante`
  ADD CONSTRAINT `datosestudiante_ibfk_1` FOREIGN KEY (`IdCiudad`) REFERENCES `ciudad` (`IdCiudad`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `datosestudiante_ibfk_2` FOREIGN KEY (`IdDepto`) REFERENCES `departamento` (`IdeDepartamente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `diversidadfuncionalest`
--
ALTER TABLE `diversidadfuncionalest`
  ADD CONSTRAINT `diversidadfuncionalest_ibfk_1` FOREIGN KEY (`IdEstudiante`) REFERENCES `usuarios` (`IdUsuarios`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `diversidadfuncionalest_ibfk_2` FOREIGN KEY (`IdDiversidad`) REFERENCES `diversidadfuncional` (`IdDiversidad`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `diversidadfuncionalest_ibfk_3` FOREIGN KEY (`IdDiscapacidad`) REFERENCES `discapacidad` (`IdDiscapacidad`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `grupo`
--
ALTER TABLE `grupo`
  ADD CONSTRAINT `grupo_ibfk_1` FOREIGN KEY (`IdGrado`) REFERENCES `grado` (`Idgrado`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `remision`
--
ALTER TABLE `remision`
  ADD CONSTRAINT `remision_ibfk_1` FOREIGN KEY (`IdEstudiante`) REFERENCES `usuarios` (`IdUsuarios`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`IdTipoUsuario`) REFERENCES `tipousuario` (`IdTipoUsuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `usuarios_ibfk_2` FOREIGN KEY (`IdTipoDoc`) REFERENCES `tipodoc` (`IdTipoDoc`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
