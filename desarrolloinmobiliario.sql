-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-11-2024 a las 13:46:34
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
-- Base de datos: `desarrolloinmobiliario`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vendedores`
--

CREATE TABLE `vendedores` (
  `id_vendedor` int(11) NOT NULL,
  `Nombre` varchar(50) NOT NULL,
  `Apellido` varchar(50) NOT NULL,
  `Telefono` varchar(70) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `usuario` varchar(50) DEFAULT NULL,
  `password` char(150) DEFAULT NULL,
  `rol` enum('admin','vendedor') NOT NULL DEFAULT 'vendedor'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `vendedores`
--

INSERT INTO `vendedores` (`id_vendedor`, `Nombre`, `Apellido`, `Telefono`, `Email`, `usuario`, `password`, `rol`) VALUES
(1, 'Agustín Leonel', 'Castro', '2494678637', 'agustinC@gmail.com', 'webadmin', '$2a$12$mvhk0vIlA2p3LU.cQw/OxOrWxQFOk71l0Eq8I94pvcQTF5Z32icBu', 'admin'),
(2, 'Pamela Andrea', 'Sosa', '2494582311', 'pamsosa@gmail.com', '', '', 'vendedor'),
(3, 'Atilio', 'Arce', '2494985635', 'ja@gmail.com', '', '', 'vendedor'),
(48, 'Nora Alicia', 'Pascal', '2494302987', 'pascal@gmail.com', '', '', 'vendedor'),


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta`
--

CREATE TABLE `venta` (
  `id_venta` int(11) NOT NULL,
  `inmueble` varchar(300) NOT NULL,
  `fecha_venta` date NOT NULL,
  `precio` int(11) NOT NULL,
  `id_vendedor` int(11) NOT NULL,
  `foto_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `venta`
--

INSERT INTO `venta` (`id_venta`, `inmueble`, `fecha_venta`, `precio`, `id_vendedor`, `foto_url`) VALUES
(35, 'Casa importante cerca del lago de Tandil.', '2024-07-07', 485000, 48, 'https://cdn.pixabay.com/photo/2013/09/24/12/08/apartment-185779_1280.jpg'),
(36, ' Excepcional cabaña en frente lago de Tandil, en plena sierra', '2024-10-08', 287000, 3, 'https://cdn.pixabay.com/photo/2016/09/23/10/20/cottage-1689224_1280.jpg'),
(71, 'Espectacular casa en las sierras de Tandil con vista inmejorable', '2024-09-04', 455000, 1, 'https://cdn.pixabay.com/photo/2017/04/10/22/28/residence-2219972_1280.jpg'),
(76, 'Casona de campo muy importante ', '2024-08-07', 467900, 2, 'https://cdn.pixabay.com/photo/2023/08/19/08/26/house-8200038_960_720.jpg'),
(80, 'Casa excepcional en Tandil', '2024-07-04', 229900, 48, 'https://cdn.pixabay.com/photo/2021/07/23/11/38/town-6487197_1280.jpg'),
(83, 'Departamento en pleno centro Tandil', '2024-09-04', 450000, 1, 'https://cdn.pixabay.com/photo/2016/11/22/23/38/apartment-1851201_1280.jpg'),
(84, 'Casa de corte antiguo centro Tandil', '2024-10-04', 358000, 48, 'https://cdn.pixabay.com/photo/2015/10/20/18/57/furniture-998265_1280.jpg');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `vendedores`
--
ALTER TABLE `vendedores`
  ADD PRIMARY KEY (`id_vendedor`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indices de la tabla `venta`
--
ALTER TABLE `venta`
  ADD UNIQUE KEY `id_venta` (`id_venta`),
  ADD KEY `Id_vendedor` (`id_vendedor`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `vendedores`
--
ALTER TABLE `vendedores`
  MODIFY `id_vendedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT de la tabla `venta`
--
ALTER TABLE `venta`
  MODIFY `id_venta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `venta`
--
ALTER TABLE `venta`
  ADD CONSTRAINT `venta_ibfk_1` FOREIGN KEY (`Id_vendedor`) REFERENCES `vendedores` (`Id_vendedor`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
