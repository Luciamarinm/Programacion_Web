-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-07-2025 a las 01:35:16
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `vetpet_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mascotas`
--

CREATE TABLE `mascotas` (
  `idMascota` int(11) NOT NULL,
  `nombreMascota` varchar(100) NOT NULL,
  `tipoAnimal` varchar(50) NOT NULL,
  `raza` varchar(50) NOT NULL,
  `fechaNacimiento` date NOT NULL,
  `Historial` varchar(500) NOT NULL,
  `idUsuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `mascotas`
--

INSERT INTO `mascotas` (`idMascota`, `nombreMascota`, `tipoAnimal`, `raza`, `fechaNacimiento`, `Historial`, `idUsuario`) VALUES
(1, 'Rudy ', 'Perro', 'Border Collie', '2024-08-01', '', 1),
(25, 'Ejemplo', 'Ejemplo', 'Ejemplo3', '2025-07-01', '', 11),
(26, 'Ejemplo2', 'Ejemplo2', 'Ejemplo2', '2025-07-02', '', 13),
(27, 'Ejemplo3', 'Ejemplo3', 'Ejemplo3', '2025-07-02', '', 14),
(30, 'Rudy', 'Perro', 'Border collie', '2025-07-02', '', 2),
(31, 'asdfasdf', 'asdf', 'asdf', '2025-07-02', '', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas`
--

CREATE TABLE `reservas` (
  `idReserva` int(11) NOT NULL,
  `idUsuario` int(11) NOT NULL,
  `idMascota` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefono` int(9) NOT NULL,
  `servicio` varchar(20) NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `mensaje` varchar(500) NOT NULL,
  `diagnostico` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `reservas`
--

INSERT INTO `reservas` (`idReserva`, `idUsuario`, `idMascota`, `email`, `telefono`, `servicio`, `fecha`, `hora`, `mensaje`, `diagnostico`) VALUES
(34, 11, 25, 'Ejemplo@gmail.com', 123, 'Animales exóticos', '2025-07-03', '10:00:00', '', ''),
(35, 13, 26, 'Ejemplo2@gmai.com', 13123, 'Veterinario', '2025-07-03', '10:00:00', '', ''),
(36, 14, 27, 'Ejemplo3@gmail.com', 2233421, 'Veterinario', '2025-07-03', '10:30:00', '', ''),
(39, 11, 25, 'Ejemplo@gmail.com', 123, 'Veterinario', '2025-07-03', '11:00:00', '', ''),
(40, 2, 31, 'joseantonio.salmeron@murciaeduca.es', 1593, 'Veterinario', '2025-07-04', '10:00:00', '', ''),
(41, 2, 30, 'joseantonio.salmeron@murciaeduca.es', 1593, 'Veterinario', '2025-07-04', '11:30:00', 'Cuidadle bien', ''),
(42, 1, 1, 'luciamarinmartinez94@gmail.com', 695306952, 'Animales exóticos', '2025-07-23', '10:30:00', '', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `idUsuario` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `pass` varchar(100) NOT NULL,
  `nombreUsuario` varchar(100) NOT NULL,
  `apellidosUsuario` varchar(100) NOT NULL,
  `telefono` int(9) NOT NULL,
  `DNI` varchar(9) NOT NULL,
  `rol` varchar(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`idUsuario`, `email`, `pass`, `nombreUsuario`, `apellidosUsuario`, `telefono`, `DNI`, `rol`) VALUES
(1, 'luciamarinmartinez94@gmail.com', '123', 'Lucía', 'Marín Martínez', 695306952, '48723469Q', 'Admin'),
(2, 'joseantonio.salmeron@murciaeduca.es', '123', 'José', 'Salmerón Marín', 1593, '1456', 'Cliente'),
(11, 'Ejemplo@gmail.com', '123', 'Ejemplo', 'Ejemplo', 123, 'Ejemplo', 'Admin'),
(13, 'Ejemplo2@gmai.com', '123', 'Ejemplo2', 'Ejemplo2', 13123, 'Ejemplo', 'Admin'),
(14, 'Ejemplo3@gmail.com', '123', 'Ejemplo3', 'Ejemplo3', 2233421, 'Ejemplo3', 'Cliente'),
(15, 'Ejemplo4@gmail.com', '1232', 'Ejemplo4', 'Ejemplo4', 234234, 'Ejemplo4', 'Cliente'),
(16, 'Ejemplo5@gmail.co', '123', 'Ejemplo5', 'Ejemplo', 14234, 'Ejempl', 'Cliente'),
(17, 'asdfgasf@gmai.com', '123', 'Ejemplo6', 'Ejemplo6', 24124, '141241', 'Cliente');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `mascotas`
--
ALTER TABLE `mascotas`
  ADD PRIMARY KEY (`idMascota`),
  ADD KEY `idUsuario` (`idUsuario`);

--
-- Indices de la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`idReserva`),
  ADD KEY `idUsuario` (`idUsuario`),
  ADD KEY `idMascota` (`idMascota`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`idUsuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `mascotas`
--
ALTER TABLE `mascotas`
  MODIFY `idMascota` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de la tabla `reservas`
--
ALTER TABLE `reservas`
  MODIFY `idReserva` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `idUsuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `mascotas`
--
ALTER TABLE `mascotas`
  ADD CONSTRAINT `mascotas_ibfk_1` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `reservas_ibfk_1` FOREIGN KEY (`idMascota`) REFERENCES `mascotas` (`idMascota`) ON UPDATE CASCADE,
  ADD CONSTRAINT `reservas_ibfk_2` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
