-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-01-2025 a las 12:37:13
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
-- Base de datos: `harasdb`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alimentos`
--

CREATE TABLE `alimentos` (
  `idAlimento` int(11) NOT NULL,
  `idUsuario` int(11) NOT NULL,
  `nombreAlimento` varchar(100) NOT NULL,
  `idTipoAlimento` int(11) NOT NULL,
  `stockActual` decimal(10,2) NOT NULL,
  `stockMinimo` decimal(10,2) DEFAULT 0.00,
  `estado` enum('Disponible','Por agotarse','Agotado','Vencido') DEFAULT 'Disponible',
  `idUnidadMedida` int(11) NOT NULL,
  `costo` decimal(10,2) NOT NULL,
  `idLote` int(11) NOT NULL,
  `idEquino` int(11) DEFAULT NULL,
  `compra` decimal(10,2) NOT NULL,
  `fechaMovimiento` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alimentos`
--

INSERT INTO `alimentos` (`idAlimento`, `idUsuario`, `nombreAlimento`, `idTipoAlimento`, `stockActual`, `stockMinimo`, `estado`, `idUnidadMedida`, `costo`, `idLote`, `idEquino`, `compra`, `fechaMovimiento`) VALUES
(1, 3, 'Afrecho', 2, 412.50, 100.00, 'Disponible', 1, 65.00, 1, 5, 32500.00, '2025-01-28 22:03:33'),
(2, 3, 'Afrecho', 2, 10000.00, 1000.00, 'Disponible', 1, 70.00, 2, NULL, 700000.00, '2025-01-28 21:50:39'),
(3, 3, 'Cebada', 2, 8000.00, 1000.00, 'Disponible', 1, 65.00, 2, NULL, 520000.00, '2025-01-28 21:51:50'),
(4, 3, 'Afrecho', 2, 10000.00, 1000.00, 'Disponible', 1, 69.00, 3, NULL, 690000.00, '2025-01-28 21:52:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistenciapersonal`
--

CREATE TABLE `asistenciapersonal` (
  `idAsistencia` int(11) NOT NULL,
  `idPersonal` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `horaEntrada` time NOT NULL,
  `horaSalida` time NOT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bostas`
--

CREATE TABLE `bostas` (
  `idbosta` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `cantidadsacos` int(11) NOT NULL,
  `pesoaprox` decimal(4,2) NOT NULL,
  `peso_diario` decimal(7,2) DEFAULT NULL,
  `peso_semanal` decimal(9,2) DEFAULT NULL,
  `peso_mensual` decimal(12,2) DEFAULT NULL,
  `numero_semana` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `bostas`
--

INSERT INTO `bostas` (`idbosta`, `fecha`, `cantidadsacos`, `pesoaprox`, `peso_diario`, `peso_semanal`, `peso_mensual`, `numero_semana`) VALUES
(1, '2024-12-31', 34, 27.00, 918.00, 918.00, 918.00, 53),
(2, '2025-01-10', 36, 25.00, 900.00, 900.00, 900.00, 2),
(3, '2025-01-20', 40, 24.00, 960.00, 960.00, 1860.00, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `campos`
--

CREATE TABLE `campos` (
  `idCampo` int(11) NOT NULL,
  `numeroCampo` int(11) NOT NULL,
  `tamanoCampo` decimal(10,2) NOT NULL,
  `idTipoSuelo` int(11) NOT NULL,
  `estado` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `campos`
--

INSERT INTO `campos` (`idCampo`, `numeroCampo`, `tamanoCampo`, `idTipoSuelo`, `estado`) VALUES
(1, 1, 998.83, 1, 'Activo'),
(2, 2, 1023.89, 1, 'Activo'),
(3, 3, 1015.33, 1, 'Activo'),
(4, 4, 1008.52, 1, 'Activo'),
(5, 5, 1004.52, 2, 'Activo'),
(6, 6, 999.65, 2, 'Activo'),
(7, 7, 997.77, 2, 'Activo'),
(8, 8, 989.45, 2, 'Activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `combinacionesmedicamentos`
--

CREATE TABLE `combinacionesmedicamentos` (
  `idCombinacion` int(11) NOT NULL,
  `idTipo` int(11) NOT NULL,
  `idPresentacion` int(11) NOT NULL,
  `dosis` decimal(10,2) NOT NULL,
  `idUnidad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `combinacionesmedicamentos`
--

INSERT INTO `combinacionesmedicamentos` (`idCombinacion`, `idTipo`, `idPresentacion`, `dosis`, `idUnidad`) VALUES
(1, 1, 1, 500.00, 1),
(4, 1, 4, 1.00, 3),
(20, 1, 4, 5.00, 3),
(16, 1, 15, 250.00, 1),
(5, 2, 1, 50.00, 1),
(2, 2, 2, 10.00, 2),
(3, 3, 3, 200.00, 1),
(6, 3, 5, 5.00, 1),
(15, 3, 14, 15.00, 1),
(17, 3, 16, 100.00, 4),
(21, 3, 18, 10.00, 1),
(22, 3, 18, 50.00, 1),
(7, 4, 6, 300.00, 1),
(8, 5, 7, 100.00, 3),
(14, 5, 13, 50.00, 1),
(18, 6, 1, 10.00, 5),
(9, 6, 8, 1.00, 2),
(10, 7, 9, 0.50, 2),
(11, 8, 10, 20.00, 1),
(12, 9, 11, 5.00, 1),
(13, 10, 12, 1.00, 3),
(19, 11, 4, 200.00, 3),
(24, 11, 19, 10.00, 1),
(23, 11, 19, 10.00, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detallemedicamentos`
--

CREATE TABLE `detallemedicamentos` (
  `idDetalleMed` int(11) NOT NULL,
  `idMedicamento` int(11) NOT NULL,
  `idEquino` int(11) NOT NULL,
  `dosis` varchar(50) NOT NULL,
  `frecuenciaAdministracion` varchar(50) NOT NULL,
  `idViaAdministracion` int(11) NOT NULL,
  `fechaInicio` date NOT NULL,
  `fechaFin` date NOT NULL,
  `observaciones` text DEFAULT NULL,
  `reaccionesAdversas` text DEFAULT NULL,
  `idUsuario` int(11) NOT NULL,
  `tipoTratamiento` enum('Primario','Complementario') DEFAULT 'Primario',
  `estadoTratamiento` enum('Activo','Finalizado','En pausa') DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detallemedicamentos`
--

INSERT INTO `detallemedicamentos` (`idDetalleMed`, `idMedicamento`, `idEquino`, `dosis`, `frecuenciaAdministracion`, `idViaAdministracion`, `fechaInicio`, `fechaFin`, `observaciones`, `reaccionesAdversas`, `idUsuario`, `tipoTratamiento`, `estadoTratamiento`) VALUES
(1, 2, 1, 'mg', '8h', 1, '2025-01-16', '2025-01-31', 'PRUEBA', '', 3, 'Primario', 'Activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entrenamientos`
--

CREATE TABLE `entrenamientos` (
  `idEntrenamiento` int(11) NOT NULL,
  `idEquino` int(11) NOT NULL,
  `fecha` datetime DEFAULT NULL,
  `tipoEntrenamiento` varchar(100) NOT NULL,
  `duracion` decimal(5,2) NOT NULL,
  `intensidad` enum('baja','media','alta') NOT NULL,
  `comentarios` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equinos`
--

CREATE TABLE `equinos` (
  `idEquino` int(11) NOT NULL,
  `nombreEquino` varchar(100) NOT NULL,
  `fechaNacimiento` date DEFAULT NULL,
  `sexo` enum('Macho','Hembra') NOT NULL,
  `idTipoEquino` int(11) NOT NULL,
  `detalles` text DEFAULT NULL,
  `idEstadoMonta` int(11) DEFAULT NULL,
  `idNacionalidad` int(11) DEFAULT NULL,
  `idPropietario` int(11) DEFAULT NULL,
  `pesokg` decimal(5,1) DEFAULT NULL,
  `fotografia` varchar(255) DEFAULT NULL,
  `estado` bit(1) NOT NULL,
  `fechaentrada` date DEFAULT NULL,
  `fechasalida` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `equinos`
--

INSERT INTO `equinos` (`idEquino`, `nombreEquino`, `fechaNacimiento`, `sexo`, `idTipoEquino`, `detalles`, `idEstadoMonta`, `idNacionalidad`, `idPropietario`, `pesokg`, `fotografia`, `estado`, `fechaentrada`, `fechasalida`, `created_at`, `updated_at`) VALUES
(1, 'Southdale', '2006-04-30', 'Macho', 2, NULL, 1, 35, NULL, 750.0, 'wamevh4drqeq6014yabu', b'1', NULL, NULL, '2025-01-28 04:04:36', '2025-01-28 04:57:13'),
(2, 'Umbridled Command', '2009-03-22', 'Macho', 2, NULL, 1, 57, NULL, 730.0, 'fdox90by0ixfpqmqnwml', b'1', NULL, NULL, '2025-01-28 04:07:28', '2025-01-29 05:25:19'),
(3, 'Floriform', '2018-04-16', 'Macho', 2, NULL, 1, 57, NULL, 700.0, 'mbutbvtf7srkrulyrqwv', b'1', NULL, NULL, '2025-01-28 04:09:48', '2025-01-29 05:25:30'),
(4, 'La Lomada', '2012-08-05', 'Hembra', 1, NULL, 5, 8, NULL, 620.0, 'agzlw5eefgmhfyw8he5s', b'1', NULL, NULL, '2025-01-28 04:38:13', '2025-01-29 05:25:19'),
(5, 'Gong Zhu', '2013-01-24', 'Hembra', 1, NULL, 5, 57, NULL, 630.0, 'siyzv6nrrxb1zecq4lu4', b'1', NULL, NULL, '2025-01-28 04:39:03', NULL),
(6, 'Rocio de Lima', '2011-05-13', 'Hembra', 1, NULL, 5, 57, NULL, 650.0, 'snrw85wx03ygxhouhz7q', b'1', NULL, NULL, '2025-01-28 04:39:56', NULL),
(7, 'Alena', '2016-03-03', 'Hembra', 1, NULL, 5, 137, NULL, 680.0, 'z88s36zxckkryce0pteb', b'1', NULL, NULL, '2025-01-28 04:40:43', NULL),
(8, 'La Elegida', '2006-07-08', 'Hembra', 1, NULL, 5, 137, NULL, 640.0, 'g5e4cujkuzryiajuj0a3', b'1', NULL, NULL, '2025-01-28 04:41:26', '2025-01-29 05:25:30'),
(9, 'Nairobi', '2008-06-02', 'Hembra', 1, NULL, 5, 137, NULL, 600.0, 'amq0nqrlbcr9cbe9kesa', b'1', NULL, NULL, '2025-01-28 04:42:09', NULL),
(10, 'Galaxia', '2016-05-05', 'Hembra', 1, NULL, 5, 137, NULL, 630.0, 'pcyx90wsaoq4fgrag58u', b'1', NULL, NULL, '2025-01-28 04:43:59', NULL),
(11, 'Gwendoline', '2008-04-25', 'Hembra', 1, NULL, 5, 137, NULL, 620.0, 'qzuldgsu0vovijsynoti', b'1', NULL, NULL, '2025-01-28 04:44:49', NULL),
(12, 'Moon Pass', '2009-03-24', 'Hembra', 1, NULL, 5, 57, NULL, 610.0, 'qziseur0xemjkbwanbgn', b'1', NULL, NULL, '2025-01-28 04:45:52', NULL),
(13, 'Mosquetera', '2018-05-06', 'Hembra', 1, NULL, 4, 57, NULL, 620.0, 'qnznzzd93imue7ct2w15', b'1', NULL, NULL, '2025-01-28 04:46:29', '2025-01-29 05:25:30'),
(14, 'Q\'Orianka', '2017-04-16', 'Hembra', 1, NULL, 5, 8, NULL, 630.0, 'x97hi5dbkvs9kdvwpbbo', b'1', NULL, NULL, '2025-01-28 04:47:31', NULL),
(15, 'Hechicero', '2023-10-11', 'Macho', 4, NULL, NULL, 137, NULL, 280.0, 'eplldnef8xjozjw2gttz', b'1', NULL, NULL, '2025-01-28 04:50:09', NULL),
(16, 'Via Regina', '2022-09-17', 'Hembra', 3, NULL, 5, 137, NULL, 350.0, 'zakh9ur42g5mrijkh1et', b'1', NULL, NULL, '2025-01-28 04:51:21', '2025-01-28 04:57:13'),
(17, 'Curare', '2023-10-06', 'Macho', 4, NULL, NULL, 137, NULL, 290.0, 'yncuf9afn7ysqecuyrwr', b'1', NULL, NULL, '2025-01-28 04:53:05', NULL),
(18, 'La Candy', NULL, 'Hembra', 1, NULL, 4, 57, 1, NULL, '', b'1', NULL, NULL, '2025-01-28 04:54:02', '2025-01-29 05:40:50'),
(19, 'La Negra', NULL, 'Hembra', 1, NULL, 5, 115, 2, NULL, '', b'1', '2025-01-27', '2025-01-31', '2025-01-28 04:54:52', '2025-01-29 05:25:19'),
(20, 'Poética', NULL, 'Hembra', 1, NULL, 5, 137, 1, NULL, '', b'1', NULL, NULL, '2025-01-28 04:56:16', NULL),
(21, 'Rayo Veloz', '2025-01-15', 'Macho', 5, NULL, NULL, 137, NULL, 40.0, 'ie2fgreqsrhkrpjsk5op', b'1', NULL, NULL, '2025-01-29 02:58:51', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estadomonta`
--

CREATE TABLE `estadomonta` (
  `idEstadoMonta` int(11) NOT NULL,
  `genero` enum('Macho','Hembra') NOT NULL,
  `nombreEstado` enum('S/S','Servida','Por Servir','Preñada','Vacia','Con Cria','Activo','Inactivo') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estadomonta`
--

INSERT INTO `estadomonta` (`idEstadoMonta`, `genero`, `nombreEstado`) VALUES
(1, 'Macho', 'Activo'),
(2, 'Macho', 'Inactivo'),
(3, 'Hembra', 'Preñada'),
(4, 'Hembra', 'Servida'),
(5, 'Hembra', 'S/S'),
(6, 'Hembra', 'Por Servir'),
(7, 'Hembra', 'Vacia'),
(8, 'Hembra', 'Con Cria');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fotografiaequinos`
--

CREATE TABLE `fotografiaequinos` (
  `idfotografia` int(11) NOT NULL,
  `idEquino` int(11) NOT NULL,
  `public_id` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `herramientas`
--

CREATE TABLE `herramientas` (
  `idHerramienta` int(11) NOT NULL,
  `nombreHerramienta` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `herramientas`
--

INSERT INTO `herramientas` (`idHerramienta`, `nombreHerramienta`) VALUES
(2, 'Corta-casco'),
(3, 'Cortador de herraduras'),
(4, 'Cuñas y espuelas'),
(1, 'Tenazas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `herramientasusadashistorial`
--

CREATE TABLE `herramientasusadashistorial` (
  `idHerramientasUsadas` int(11) NOT NULL,
  `idHistorialHerrero` int(11) NOT NULL,
  `idHerramienta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `herramientasusadashistorial`
--

INSERT INTO `herramientasusadashistorial` (`idHerramientasUsadas`, `idHistorialHerrero`, `idHerramienta`) VALUES
(1, 1, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historialdosisaplicadas`
--

CREATE TABLE `historialdosisaplicadas` (
  `idDosis` int(11) NOT NULL,
  `idMedicamento` int(11) NOT NULL,
  `idEquino` int(11) NOT NULL,
  `cantidadAplicada` decimal(10,2) NOT NULL,
  `cantidadRestante` decimal(10,2) DEFAULT NULL,
  `fechaAplicacion` date NOT NULL,
  `idUsuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historialequinos`
--

CREATE TABLE `historialequinos` (
  `idHistorial` int(11) NOT NULL,
  `idEquino` int(11) NOT NULL,
  `descripcion` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `historialequinos`
--

INSERT INTO `historialequinos` (`idHistorial`, `idEquino`, `descripcion`) VALUES
(1, 1, '<p>Linajudo zaino hijo de Street Cry con compaña en Canada donde logró ganar 4 carreras en 9 presentaciones incluso Eclipse S.<strong>(G3).</strong></p>');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historialherrero`
--

CREATE TABLE `historialherrero` (
  `idHistorialHerrero` int(11) NOT NULL,
  `idEquino` int(11) NOT NULL,
  `idUsuario` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `idTrabajo` int(11) NOT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `historialherrero`
--

INSERT INTO `historialherrero` (`idHistorialHerrero`, `idEquino`, `idUsuario`, `fecha`, `idTrabajo`, `observaciones`) VALUES
(1, 1, 3, '2025-01-31', 2, 'Se requiere realizar el recorte de los cascos del equino Southdale para poder salvaguardar el estado y salubridad del mismo.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historialimplemento`
--

CREATE TABLE `historialimplemento` (
  `idHistorial` int(11) NOT NULL,
  `idInventario` int(11) NOT NULL,
  `idTipoinventario` int(11) NOT NULL,
  `idTipomovimiento` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precioUnitario` decimal(10,2) DEFAULT NULL,
  `precioTotal` decimal(10,2) DEFAULT NULL,
  `descripcion` varchar(100) DEFAULT NULL,
  `fechaMovimiento` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `historialimplemento`
--

INSERT INTO `historialimplemento` (`idHistorial`, `idInventario`, `idTipoinventario`, `idTipomovimiento`, `cantidad`, `precioUnitario`, `precioTotal`, `descripcion`, `fechaMovimiento`) VALUES
(1, 6, 2, 2, 1, NULL, NULL, 'Mal estado', '2025-01-29 05:39:42');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historialmovimientos`
--

CREATE TABLE `historialmovimientos` (
  `idMovimiento` int(11) NOT NULL,
  `idAlimento` int(11) NOT NULL,
  `tipoMovimiento` varchar(50) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `idEquino` int(11) DEFAULT NULL,
  `idUsuario` int(11) NOT NULL,
  `unidadMedida` varchar(50) NOT NULL,
  `fechaMovimiento` date DEFAULT current_timestamp(),
  `merma` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `historialmovimientos`
--

INSERT INTO `historialmovimientos` (`idMovimiento`, `idAlimento`, `tipoMovimiento`, `cantidad`, `idEquino`, `idUsuario`, `unidadMedida`, `fechaMovimiento`, `merma`) VALUES
(1, 1, 'Entrada', 200.00, NULL, 3, '1', '2025-01-28', NULL),
(2, 1, 'Salida', 98.00, 15, 3, 'kg', '2025-01-28', 2.00),
(3, 1, 'Salida', 7.00, 1, 3, 'kg', '2025-01-28', 0.00),
(4, 1, 'Salida', 12.00, 2, 3, 'kg', '2025-01-28', 0.00),
(5, 1, 'Salida', 5.00, 16, 3, 'kg', '2025-01-28', 0.00),
(6, 1, 'Salida', 10.00, 15, 3, 'kg', '2025-01-28', 0.50),
(7, 1, 'Salida', 72.00, 5, 3, 'kg', '2025-01-28', 1.00),
(8, 1, 'Salida', 75.00, 5, 3, 'kg', '2025-01-28', 5.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historialmovimientosmedicamentos`
--

CREATE TABLE `historialmovimientosmedicamentos` (
  `idMovimiento` int(11) NOT NULL,
  `idMedicamento` int(11) NOT NULL,
  `tipoMovimiento` varchar(50) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `motivo` text NOT NULL,
  `idEquino` int(11) DEFAULT NULL,
  `idUsuario` int(11) NOT NULL,
  `fechaMovimiento` date DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `historialmovimientosmedicamentos`
--

INSERT INTO `historialmovimientosmedicamentos` (`idMovimiento`, `idMedicamento`, `tipoMovimiento`, `cantidad`, `motivo`, `idEquino`, `idUsuario`, `fechaMovimiento`) VALUES
(1, 1, 'Entrada', 20, '', NULL, 3, '2025-01-28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `implementos`
--

CREATE TABLE `implementos` (
  `idInventario` int(11) NOT NULL,
  `idTipoinventario` int(11) NOT NULL,
  `nombreProducto` varchar(100) NOT NULL,
  `descripcion` text NOT NULL,
  `precioUnitario` decimal(10,2) DEFAULT NULL,
  `precioTotal` decimal(10,2) DEFAULT NULL,
  `idTipomovimiento` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `stockFinal` int(11) NOT NULL,
  `estado` bit(1) NOT NULL,
  `create_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `implementos`
--

INSERT INTO `implementos` (`idInventario`, `idTipoinventario`, `nombreProducto`, `descripcion`, `precioUnitario`, `precioTotal`, `idTipomovimiento`, `cantidad`, `stockFinal`, `estado`, `create_at`) VALUES
(1, 1, 'Soga', 'Soga de 3.5 metros de largo.', 12.00, 240.00, 1, 20, 20, b'1', '2025-01-28 23:56:18'),
(2, 1, 'Jáquima', 'Jáquima para diferenciar equinos', 100.00, 1500.00, 1, 15, 15, b'1', '2025-01-28 23:57:35'),
(3, 1, 'Cepillo de cuerpo', 'Cepillo de cerdas suaves, de cerdas duras.', 12.00, 60.00, 1, 5, 5, b'1', '2025-01-28 23:58:49'),
(4, 1, 'Cepillo de crin', 'Mantener las crines y colas desenredadas.', 8.00, 16.00, 1, 2, 2, b'1', '2025-01-28 23:59:56'),
(5, 1, 'Refuerzo para casco', 'Mantener la salud del pie del caballo.', 150.00, 450.00, 1, 3, 3, b'1', '2025-01-29 00:00:33'),
(6, 2, 'Lampa', 'Beneficiar campos', 26.00, 156.00, 1, 6, 5, b'1', '2025-01-29 00:05:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lotesalimento`
--

CREATE TABLE `lotesalimento` (
  `idLote` int(11) NOT NULL,
  `lote` varchar(50) NOT NULL,
  `fechaCaducidad` date DEFAULT NULL,
  `fechaIngreso` datetime DEFAULT current_timestamp(),
  `estadoLote` enum('No Vencido','Vencido','Agotado') DEFAULT 'No Vencido'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `lotesalimento`
--

INSERT INTO `lotesalimento` (`idLote`, `lote`, `fechaCaducidad`, `fechaIngreso`, `estadoLote`) VALUES
(1, '0101', '2025-03-18', '2025-01-28 00:02:50', 'No Vencido'),
(2, '1114', '2025-03-20', '2025-01-28 21:50:39', 'No Vencido'),
(3, '1142', '2025-05-07', '2025-01-28 21:52:45', 'No Vencido');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lotesmedicamento`
--

CREATE TABLE `lotesmedicamento` (
  `idLoteMedicamento` int(11) NOT NULL,
  `lote` varchar(100) NOT NULL,
  `fechaCaducidad` date NOT NULL,
  `fechaIngreso` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `lotesmedicamento`
--

INSERT INTO `lotesmedicamento` (`idLoteMedicamento`, `lote`, `fechaCaducidad`, `fechaIngreso`) VALUES
(1, '0101', '2025-06-27', '2025-01-28'),
(2, '0552', '2027-05-31', '2025-01-28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `medicamentos`
--

CREATE TABLE `medicamentos` (
  `idMedicamento` int(11) NOT NULL,
  `idUsuario` int(11) NOT NULL,
  `nombreMedicamento` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `idCombinacion` int(11) NOT NULL,
  `cantidad_stock` int(11) NOT NULL,
  `stockMinimo` int(11) DEFAULT 0,
  `estado` enum('Disponible','Por agotarse','Agotado') DEFAULT 'Disponible',
  `idEquino` int(11) DEFAULT NULL,
  `idLoteMedicamento` int(11) NOT NULL,
  `precioUnitario` decimal(10,2) NOT NULL,
  `motivo` text DEFAULT NULL,
  `fecha_registro` date NOT NULL,
  `ultima_modificacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `medicamentos`
--

INSERT INTO `medicamentos` (`idMedicamento`, `idUsuario`, `nombreMedicamento`, `descripcion`, `idCombinacion`, `cantidad_stock`, `stockMinimo`, `estado`, `idEquino`, `idLoteMedicamento`, `precioUnitario`, `motivo`, `fecha_registro`, `ultima_modificacion`) VALUES
(1, 3, 'Flunixin Meglumine', 'Antiinflamatorio no esteroideo (AINE).', 22, 30, 3, 'Disponible', NULL, 1, 110.00, NULL, '2025-01-28', '2025-01-29 04:51:34'),
(2, 3, 'Ivermectina', '', 24, 15, 5, 'Disponible', NULL, 2, 35.00, NULL, '2025-01-28', '2025-01-29 04:50:41');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mermasalimento`
--

CREATE TABLE `mermasalimento` (
  `idMerma` int(11) NOT NULL,
  `idAlimento` int(11) NOT NULL,
  `cantidadMerma` decimal(10,2) NOT NULL,
  `fechaMerma` datetime DEFAULT current_timestamp(),
  `motivo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mermasalimento`
--

INSERT INTO `mermasalimento` (`idMerma`, `idAlimento`, `cantidadMerma`, `fechaMerma`, `motivo`) VALUES
(1, 1, 2.00, '2025-01-28 00:04:55', 'Merma registrada en salida de inventario'),
(2, 1, 0.50, '2025-01-28 22:02:05', 'Merma registrada en salida de inventario'),
(3, 1, 1.00, '2025-01-28 22:02:35', 'Merma registrada en salida de inventario'),
(4, 1, 5.00, '2025-01-28 22:03:33', 'Merma registrada en salida de inventario');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulos`
--

CREATE TABLE `modulos` (
  `idmodulo` int(11) NOT NULL,
  `modulo` varchar(30) NOT NULL,
  `create_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `modulos`
--

INSERT INTO `modulos` (`idmodulo`, `modulo`, `create_at`) VALUES
(1, 'campos', '2025-01-27 22:51:30'),
(2, 'equinos', '2025-01-27 22:51:30'),
(3, 'historialMedico', '2025-01-27 22:51:30'),
(4, 'inventarios', '2025-01-27 22:51:30'),
(5, 'reportes', '2025-01-27 22:51:30'),
(6, 'servicios', '2025-01-27 22:51:30'),
(7, 'usuarios', '2025-01-27 22:51:30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nacionalidades`
--

CREATE TABLE `nacionalidades` (
  `idNacionalidad` int(11) NOT NULL,
  `nacionalidad` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `nacionalidades`
--

INSERT INTO `nacionalidades` (`idNacionalidad`, `nacionalidad`) VALUES
(1, 'Afgana'),
(2, 'Alemana'),
(3, 'Andorrana'),
(4, 'Angoleña'),
(5, 'Antiguana'),
(6, 'Árabe'),
(7, 'Argelina'),
(8, 'Argentina'),
(9, 'Armenia'),
(10, 'Arubeña'),
(11, 'Australiana'),
(12, 'Austriaca'),
(13, 'Azerbaiyana'),
(14, 'Bahameña'),
(15, 'Bahreiní'),
(16, 'Bangladesí'),
(17, 'Barbadense'),
(18, 'Belga'),
(19, 'Beliceña'),
(20, 'Beninesa'),
(21, 'Bermudeña'),
(22, 'Bielorrusa'),
(23, 'Boliviana'),
(24, 'Bosnia'),
(25, 'Botsuana'),
(26, 'Brasileña'),
(27, 'Bruneana'),
(28, 'Búlgara'),
(29, 'Burkinesa'),
(30, 'Burundesa'),
(31, 'Butanesa'),
(32, 'Caboverdiana'),
(33, 'Camboyana'),
(34, 'Camerunesa'),
(35, 'Canadiense'),
(36, 'Centroafricana'),
(37, 'Chadiana'),
(38, 'Checa'),
(39, 'Chilena'),
(40, 'China'),
(41, 'Chipriota'),
(42, 'Colombiana'),
(43, 'Comorense'),
(44, 'Congoleña'),
(45, 'Costarricense'),
(46, 'Croata'),
(47, 'Cubana'),
(48, 'Danesa'),
(49, 'Dominicana'),
(50, 'Ecuatoriana'),
(51, 'Egipcia'),
(52, 'Emiratí'),
(53, 'Eritrea'),
(54, 'Eslovaca'),
(55, 'Eslovena'),
(56, 'Española'),
(57, 'Estadounidense'),
(58, 'Estonia'),
(59, 'Etíope'),
(60, 'Filipina'),
(61, 'Finlandesa'),
(62, 'Fiyiana'),
(63, 'Francesa'),
(64, 'Gabonesa'),
(65, 'Galesa'),
(66, 'Gambiana'),
(67, 'Georgiana'),
(68, 'Ghanesa'),
(69, 'Granadina'),
(70, 'Griega'),
(71, 'Guatemalteca'),
(72, 'Guineana'),
(73, 'Guyanesa'),
(74, 'Haitiana'),
(75, 'Hondureña'),
(76, 'Húngara'),
(77, 'India'),
(78, 'Indonesia'),
(79, 'Iraquí'),
(80, 'Iraní'),
(81, 'Irlandesa'),
(82, 'Islandesa'),
(83, 'Israelí'),
(84, 'Italiana'),
(85, 'Jamaiquina'),
(86, 'Japonesa'),
(87, 'Jordana'),
(88, 'Kazaja'),
(89, 'Keniana'),
(90, 'Kirguisa'),
(91, 'Kiribatiana'),
(92, 'Kosovar'),
(93, 'Kuwaití'),
(94, 'Laosiana'),
(95, 'Lesotense'),
(96, 'Letona'),
(97, 'Libanesa'),
(98, 'Liberiana'),
(99, 'Libia'),
(100, 'Liechtensteiniana'),
(101, 'Lituana'),
(102, 'Luxemburguesa'),
(103, 'Macedonia'),
(104, 'Malaya'),
(105, 'Malauí'),
(106, 'Maldiva'),
(107, 'Malgache'),
(108, 'Maliense'),
(109, 'Maltesa'),
(110, 'Marfileña'),
(111, 'Marroquí'),
(112, 'Marshallina'),
(113, 'Mauritana'),
(114, 'Mauriciana'),
(115, 'Mexicana'),
(116, 'Micronesia'),
(117, 'Moldava'),
(118, 'Monegasca'),
(119, 'Mongola'),
(120, 'Montenegrina'),
(121, 'Mozambiqueña'),
(122, 'Namibia'),
(123, 'Neerlandesa'),
(124, 'Nepalí'),
(125, 'Nicaragüense'),
(126, 'Nigeriana'),
(127, 'Nigerina'),
(128, 'Norcoreana'),
(129, 'Noruega'),
(130, 'Nueva Zelandesa'),
(131, 'Omaní'),
(132, 'Pakistaní'),
(133, 'Palauana'),
(134, 'Panameña'),
(135, 'Papú'),
(136, 'Paraguaya'),
(137, 'Peruana'),
(138, 'Polaca'),
(139, 'Portuguesa'),
(140, 'Puertorriqueña'),
(141, 'Qatarí'),
(142, 'Reino Unido'),
(143, 'Rumana'),
(144, 'Rusa'),
(145, 'Ruandesa'),
(146, 'Salvadoreña'),
(147, 'Samoana'),
(148, 'Sanmarinense'),
(149, 'Santa Lucía'),
(150, 'Saudí'),
(151, 'Senegalesa'),
(152, 'Serbia'),
(153, 'Seychellense'),
(154, 'Sierraleonesa'),
(155, 'Singapurense'),
(156, 'Somalí'),
(157, 'Sri Lanka'),
(158, 'Sudafricana'),
(159, 'Sudanesa'),
(160, 'Sueca'),
(161, 'Suiza'),
(162, 'Surcoreana'),
(163, 'Surinamesa'),
(164, 'Tailandesa'),
(165, 'Tanzana'),
(166, 'Togolesa'),
(167, 'Tongana'),
(168, 'Trinitaria'),
(169, 'Tunecina'),
(170, 'Turca'),
(171, 'Tuvaluana'),
(172, 'Ucraniana'),
(173, 'Ugandesa'),
(174, 'Uruguaya'),
(175, 'Uzbeca'),
(176, 'Vanuatuense'),
(177, 'Venezolana'),
(178, 'Vietnamita'),
(179, 'Yemení'),
(180, 'Yibutiana'),
(181, 'Zambiana'),
(182, 'Zimbabuense');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `idpermiso` int(11) NOT NULL,
  `idRol` int(11) NOT NULL,
  `idvista` int(11) NOT NULL,
  `create_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`idpermiso`, `idRol`, `idvista`, `create_at`) VALUES
(1, 1, 1, '2025-01-27 22:51:30'),
(2, 1, 5, '2025-01-27 22:51:30'),
(3, 1, 6, '2025-01-27 22:51:30'),
(4, 1, 7, '2025-01-27 22:51:30'),
(5, 1, 10, '2025-01-27 22:51:30'),
(6, 1, 12, '2025-01-27 22:51:30'),
(7, 1, 15, '2025-01-27 22:51:30'),
(8, 1, 19, '2025-01-27 22:51:30'),
(9, 1, 20, '2025-01-27 22:51:30'),
(10, 1, 21, '2025-01-27 22:51:30'),
(11, 1, 22, '2025-01-27 22:51:30'),
(12, 1, 23, '2025-01-27 22:51:30'),
(13, 1, 30, '2025-01-27 22:51:30'),
(14, 1, 31, '2025-01-27 22:51:30'),
(15, 1, 32, '2025-01-27 22:51:30'),
(16, 1, 36, '2025-01-27 22:51:30'),
(17, 2, 1, '2025-01-27 22:51:30'),
(18, 2, 5, '2025-01-27 22:51:30'),
(19, 2, 6, '2025-01-27 22:51:30'),
(20, 2, 7, '2025-01-27 22:51:30'),
(21, 2, 10, '2025-01-27 22:51:30'),
(22, 2, 12, '2025-01-27 22:51:30'),
(23, 2, 15, '2025-01-27 22:51:30'),
(24, 2, 19, '2025-01-27 22:51:30'),
(25, 2, 20, '2025-01-27 22:51:30'),
(26, 2, 21, '2025-01-27 22:51:30'),
(27, 2, 22, '2025-01-27 22:51:30'),
(28, 2, 23, '2025-01-27 22:51:30'),
(29, 2, 30, '2025-01-27 22:51:30'),
(30, 2, 31, '2025-01-27 22:51:30'),
(31, 2, 32, '2025-01-27 22:51:30'),
(32, 2, 35, '2025-01-27 22:51:30'),
(33, 2, 36, '2025-01-27 22:51:30'),
(34, 3, 1, '2025-01-27 22:51:30'),
(35, 3, 4, '2025-01-27 22:51:30'),
(36, 3, 6, '2025-01-27 22:51:30'),
(37, 3, 7, '2025-01-27 22:51:30'),
(38, 3, 9, '2025-01-27 22:51:30'),
(39, 3, 10, '2025-01-27 22:51:30'),
(40, 3, 11, '2025-01-27 22:51:30'),
(41, 3, 12, '2025-01-27 22:51:30'),
(42, 3, 13, '2025-01-27 22:51:30'),
(43, 3, 14, '2025-01-27 22:51:30'),
(44, 3, 15, '2025-01-27 22:51:30'),
(45, 3, 16, '2025-01-27 22:51:30'),
(46, 3, 17, '2025-01-27 22:51:30'),
(47, 3, 18, '2025-01-27 22:51:30'),
(48, 3, 19, '2025-01-27 22:51:30'),
(49, 3, 21, '2025-01-27 22:51:30'),
(50, 3, 23, '2025-01-27 22:51:30'),
(51, 3, 24, '2025-01-27 22:51:30'),
(52, 3, 26, '2025-01-27 22:51:30'),
(53, 3, 27, '2025-01-27 22:51:30'),
(54, 3, 28, '2025-01-27 22:51:30'),
(55, 3, 31, '2025-01-27 22:51:30'),
(56, 3, 32, '2025-01-27 22:51:30'),
(57, 3, 33, '2025-01-27 22:51:30'),
(58, 3, 34, '2025-01-27 22:51:30'),
(59, 3, 36, '2025-01-27 22:51:30'),
(60, 4, 1, '2025-01-27 22:51:30'),
(61, 4, 2, '2025-01-27 22:51:30'),
(62, 4, 3, '2025-01-27 22:51:30'),
(63, 4, 5, '2025-01-27 22:51:30'),
(64, 4, 8, '2025-01-27 22:51:30'),
(65, 4, 22, '2025-01-27 22:51:30'),
(66, 4, 25, '2025-01-27 22:51:30'),
(67, 4, 29, '2025-01-27 22:51:30'),
(68, 4, 36, '2025-01-27 22:51:30'),
(69, 5, 1, '2025-01-27 22:51:30'),
(70, 5, 6, '2025-01-27 22:51:30'),
(71, 5, 10, '2025-01-27 22:51:30'),
(72, 5, 11, '2025-01-27 22:51:30'),
(73, 5, 12, '2025-01-27 22:51:30'),
(74, 5, 13, '2025-01-27 22:51:30'),
(75, 5, 14, '2025-01-27 22:51:30'),
(76, 5, 15, '2025-01-27 22:51:30'),
(77, 5, 36, '2025-01-27 22:51:30'),
(78, 6, 1, '2025-01-27 22:51:30'),
(79, 6, 13, '2025-01-27 22:51:30'),
(80, 6, 17, '2025-01-27 22:51:30'),
(81, 6, 19, '2025-01-27 22:51:30'),
(82, 6, 36, '2025-01-27 22:51:30'),
(83, 3, 20, '2025-01-28 00:03:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal`
--

CREATE TABLE `personal` (
  `idPersonal` int(11) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `tipodoc` varchar(20) NOT NULL,
  `nrodocumento` varchar(50) NOT NULL,
  `fechaIngreso` date NOT NULL,
  `fechaSalida` date DEFAULT NULL,
  `tipoContrato` enum('Parcial','Completo','Por Prácticas','Otro') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `personal`
--

INSERT INTO `personal` (`idPersonal`, `nombres`, `apellidos`, `direccion`, `tipodoc`, `nrodocumento`, `fechaIngreso`, `fechaSalida`, `tipoContrato`) VALUES
(1, 'Gerente', 'Mateo', 'San Agustin ', 'DNI', '11111111', '2024-08-27', NULL, 'Completo'),
(2, 'Administrador', 'Marcos', 'Calle Fatima', 'DNI', '22222222', '2024-08-27', NULL, 'Completo'),
(3, 'SupervisorE', 'Gereda', 'AV. Los Angeles', 'DNI', '33333333', '2024-08-27', NULL, 'Completo'),
(4, 'SupervisorC', 'Mamani', 'Calle Fatima', 'DNI', '44444444', '2024-08-27', NULL, 'Completo'),
(5, 'Medico', 'Paullac', 'Calle Fatima', 'DNI', '55555555', '2024-08-27', NULL, 'Completo'),
(6, 'Herrero', 'Nuñez', 'Calle Fatima', 'DNI', '66666666', '2024-08-27', NULL, 'Parcial'),
(9, 'José Aurelio', 'Quispe Yupanqui', 'Av. Brasil', 'DNI', '77777777', '2024-12-30', NULL, 'Parcial');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presentacionesmedicamentos`
--

CREATE TABLE `presentacionesmedicamentos` (
  `idPresentacion` int(11) NOT NULL,
  `presentacion` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `presentacionesmedicamentos`
--

INSERT INTO `presentacionesmedicamentos` (`idPresentacion`, `presentacion`) VALUES
(16, 'aerosoles'),
(8, 'ampollas'),
(3, 'cápsulas'),
(9, 'colirios'),
(12, 'comprimidos'),
(13, 'enemas'),
(10, 'gotas nasales'),
(14, 'goteros'),
(6, 'grageas'),
(4, 'inyectable'),
(18, 'inyectables'),
(2, 'jarabes'),
(11, 'píldoras'),
(15, 'polvos medicinales'),
(7, 'pomadas'),
(19, 'solución oral'),
(17, 'spray'),
(5, 'suspensión'),
(1, 'tabletas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `propietarios`
--

CREATE TABLE `propietarios` (
  `idPropietario` int(11) NOT NULL,
  `nombreHaras` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `propietarios`
--

INSERT INTO `propietarios` (`idPropietario`, `nombreHaras`) VALUES
(1, 'Los Eucaliptos'),
(2, 'Haras Hasmide');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `revisionequinos`
--

CREATE TABLE `revisionequinos` (
  `idRevision` int(11) NOT NULL,
  `idEquino` int(11) NOT NULL,
  `idPropietario` int(11) DEFAULT NULL,
  `tiporevision` enum('Ecografía','Examen ginecológico','Citología','Cultivo bacteriológico','Biopsia endometrial') DEFAULT NULL,
  `fecharevision` date NOT NULL,
  `observaciones` text NOT NULL,
  `costorevision` decimal(10,2) DEFAULT NULL,
  `create_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `revisionequinos`
--

INSERT INTO `revisionequinos` (`idRevision`, `idEquino`, `idPropietario`, `tiporevision`, `fecharevision`, `observaciones`, `costorevision`, `create_at`) VALUES
(1, 18, 1, 'Ecografía', '2025-01-06', 'Abortó', NULL, '2025-01-28 22:34:03'),
(7, 18, 1, 'Biopsia endometrial', '2025-01-23', 'Ninguna, solo rutinaria', 1100.00, '2025-01-28 23:37:52');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `idRol` int(11) NOT NULL,
  `nombreRol` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`idRol`, `nombreRol`) VALUES
(1, 'Gerente'),
(2, 'Administrador'),
(3, 'Supervisor Equino'),
(4, 'Supervisor Campo'),
(5, 'Médico'),
(6, 'Herrero');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rotacioncampos`
--

CREATE TABLE `rotacioncampos` (
  `idRotacion` int(11) NOT NULL,
  `idCampo` int(11) NOT NULL,
  `idTipoRotacion` int(11) NOT NULL,
  `fechaRotacion` date DEFAULT NULL,
  `estadoRotacion` varchar(50) NOT NULL,
  `detalleRotacion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rotacioncampos`
--

INSERT INTO `rotacioncampos` (`idRotacion`, `idCampo`, `idTipoRotacion`, `fechaRotacion`, `estadoRotacion`, `detalleRotacion`) VALUES
(1, 8, 3, '2025-01-01', '', ''),
(2, 3, 2, '2025-01-10', '', ''),
(3, 8, 12, '2025-01-04', '', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `idServicio` int(11) NOT NULL,
  `idEquinoMacho` int(11) DEFAULT NULL,
  `idEquinoHembra` int(11) DEFAULT NULL,
  `idEquinoExterno` int(11) DEFAULT NULL,
  `fechaServicio` date NOT NULL,
  `tipoServicio` enum('Propio','Mixto') NOT NULL,
  `detalles` text DEFAULT NULL,
  `idMedicamento` int(11) DEFAULT NULL,
  `horaEntrada` time DEFAULT NULL,
  `horaSalida` time DEFAULT NULL,
  `idPropietario` int(11) DEFAULT NULL,
  `idEstadoMonta` int(11) DEFAULT NULL,
  `costoServicio` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicios`
--

INSERT INTO `servicios` (`idServicio`, `idEquinoMacho`, `idEquinoHembra`, `idEquinoExterno`, `fechaServicio`, `tipoServicio`, `detalles`, `idMedicamento`, `horaEntrada`, `horaSalida`, `idPropietario`, `idEstadoMonta`, `costoServicio`) VALUES
(1, 1, 4, NULL, '2025-01-27', 'Propio', '', NULL, NULL, NULL, NULL, NULL, NULL),
(2, 1, NULL, 18, '2025-01-01', 'Mixto', '', NULL, '06:00:00', '06:15:00', 1, NULL, 3500.00),
(3, 1, NULL, 19, '2025-01-01', 'Mixto', '', NULL, '10:44:00', '10:49:00', 2, NULL, 2000.00),
(4, 1, 4, NULL, '2025-01-01', 'Propio', '', NULL, NULL, NULL, NULL, NULL, NULL),
(5, 2, 8, NULL, '2024-12-29', 'Propio', '', NULL, NULL, NULL, NULL, NULL, NULL),
(6, 3, 13, NULL, '2025-01-16', 'Propio', '', NULL, NULL, NULL, NULL, NULL, NULL),
(7, 1, NULL, 18, '2025-01-17', 'Mixto', '', NULL, '12:40:00', '12:50:00', 1, NULL, 2500.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipoalimentos`
--

CREATE TABLE `tipoalimentos` (
  `idTipoAlimento` int(11) NOT NULL,
  `tipoAlimento` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipoalimentos`
--

INSERT INTO `tipoalimentos` (`idTipoAlimento`, `tipoAlimento`) VALUES
(10, 'Alimentos Especializados para Caballos Deportivos'),
(8, 'Complementos Nutricionales'),
(7, 'Fibras'),
(1, 'Forrajes'),
(2, 'Granos y Cereales'),
(6, 'Heno y Pasto Preservado'),
(9, 'Hierbas Medicinales'),
(5, 'Proteínas y Energéticos'),
(4, 'Subproductos de la Agricultura'),
(3, 'Suplementos y Concentrados');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipoalimento_unidadmedida`
--

CREATE TABLE `tipoalimento_unidadmedida` (
  `idTipoAlimento` int(11) NOT NULL,
  `idUnidadMedida` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipoalimento_unidadmedida`
--

INSERT INTO `tipoalimento_unidadmedida` (`idTipoAlimento`, `idUnidadMedida`) VALUES
(1, 1),
(1, 3),
(1, 6),
(1, 8),
(2, 1),
(2, 2),
(2, 3),
(2, 9),
(3, 1),
(3, 2),
(3, 10),
(3, 13),
(4, 1),
(4, 2),
(4, 9),
(5, 1),
(5, 4),
(5, 5),
(6, 1),
(6, 6),
(6, 8),
(7, 1),
(7, 2),
(7, 8),
(8, 1),
(8, 2),
(8, 15),
(9, 2),
(9, 11),
(10, 1),
(10, 4),
(10, 14),
(10, 16);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipoequinos`
--

CREATE TABLE `tipoequinos` (
  `idTipoEquino` int(11) NOT NULL,
  `tipoEquino` enum('Yegua','Padrillo','Potranca','Potrillo','Recién nacido','Destete') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipoequinos`
--

INSERT INTO `tipoequinos` (`idTipoEquino`, `tipoEquino`) VALUES
(1, 'Yegua'),
(2, 'Padrillo'),
(3, 'Potranca'),
(4, 'Potrillo'),
(5, 'Recién nacido');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipoinventarios`
--

CREATE TABLE `tipoinventarios` (
  `idTipoinventario` int(11) NOT NULL,
  `nombreInventario` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipoinventarios`
--

INSERT INTO `tipoinventarios` (`idTipoinventario`, `nombreInventario`) VALUES
(1, 'Implementos Equinos'),
(2, 'Implementos Campos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipomovimientos`
--

CREATE TABLE `tipomovimientos` (
  `idTipomovimiento` int(11) NOT NULL,
  `movimiento` enum('Entrada','Salida') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipomovimientos`
--

INSERT INTO `tipomovimientos` (`idTipomovimiento`, `movimiento`) VALUES
(1, 'Entrada'),
(2, 'Salida');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tiporotaciones`
--

CREATE TABLE `tiporotaciones` (
  `idTipoRotacion` int(11) NOT NULL,
  `nombreRotacion` varchar(100) NOT NULL,
  `detalles` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tiporotaciones`
--

INSERT INTO `tiporotaciones` (`idTipoRotacion`, `nombreRotacion`, `detalles`) VALUES
(1, 'Riego', NULL),
(2, 'Deshierve', NULL),
(3, 'Arado', NULL),
(4, 'Gradeado', NULL),
(5, 'Rufiado', NULL),
(6, 'Potrillo', NULL),
(7, 'Potranca', NULL),
(8, 'Yeguas Preñadas', NULL),
(9, 'Yeguas con Crías', NULL),
(10, 'Yeguas Vacías', NULL),
(11, 'Destetados', NULL),
(12, 'Sembrío', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tiposmedicamentos`
--

CREATE TABLE `tiposmedicamentos` (
  `idTipo` int(11) NOT NULL,
  `tipo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tiposmedicamentos`
--

INSERT INTO `tiposmedicamentos` (`idTipo`, `tipo`) VALUES
(2, 'Analgésico'),
(1, 'Antibiótico'),
(8, 'Antifúngico'),
(3, 'Antiinflamatorio'),
(11, 'Antiparasitario'),
(7, 'Broncodilatador'),
(5, 'Desparasitante'),
(4, 'Gastroprotector'),
(9, 'Sedante'),
(6, 'Suplemento'),
(10, 'Vacuna'),
(12, 'Vitaminas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipostrabajos`
--

CREATE TABLE `tipostrabajos` (
  `idTipoTrabajo` int(11) NOT NULL,
  `nombreTrabajo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipostrabajos`
--

INSERT INTO `tipostrabajos` (`idTipoTrabajo`, `nombreTrabajo`) VALUES
(1, 'Colocación de herraduras'),
(2, 'Recorte de los cascos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tiposuelo`
--

CREATE TABLE `tiposuelo` (
  `idTipoSuelo` int(11) NOT NULL,
  `nombreTipoSuelo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tiposuelo`
--

INSERT INTO `tiposuelo` (`idTipoSuelo`, `nombreTipoSuelo`) VALUES
(1, 'Arcilloso'),
(2, 'Arenoso'),
(3, 'Calizo'),
(4, 'Humiferos'),
(5, 'Mixto'),
(6, 'Pedregoso'),
(7, 'Salino'),
(8, 'Urbano');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidadesmedida`
--

CREATE TABLE `unidadesmedida` (
  `idUnidad` int(11) NOT NULL,
  `unidad` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `unidadesmedida`
--

INSERT INTO `unidadesmedida` (`idUnidad`, `unidad`) VALUES
(6, 'dL'),
(5, 'fL'),
(3, 'g'),
(7, 'L'),
(4, 'mcg'),
(8, 'mcl'),
(9, 'mcmol'),
(10, 'mEq'),
(1, 'mg'),
(2, 'ml'),
(11, 'mm'),
(12, 'mm Hg'),
(13, 'mmol'),
(14, 'mOsm'),
(16, 'mU'),
(15, 'mUI'),
(17, 'ng'),
(18, 'nmol'),
(19, 'pg'),
(20, 'pmol'),
(22, 'U'),
(21, 'UI');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidadesmedidaalimento`
--

CREATE TABLE `unidadesmedidaalimento` (
  `idUnidadMedida` int(11) NOT NULL,
  `nombreUnidad` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `unidadesmedidaalimento`
--

INSERT INTO `unidadesmedidaalimento` (`idUnidadMedida`, `nombreUnidad`) VALUES
(10, 'bloque'),
(14, 'cápsula'),
(12, 'cc'),
(7, 'cubeta'),
(16, 'dosificado'),
(8, 'fardo'),
(2, 'g'),
(1, 'kg'),
(4, 'L'),
(11, 'mg'),
(5, 'ml'),
(6, 'paca'),
(15, 'ración'),
(9, 'sacos'),
(3, 't'),
(13, 'tableta');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `idUsuario` int(11) NOT NULL,
  `idPersonal` int(11) NOT NULL,
  `correo` varchar(50) NOT NULL,
  `clave` varchar(100) NOT NULL,
  `idRol` int(11) DEFAULT NULL,
  `estado` bit(1) NOT NULL DEFAULT b'1',
  `create_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `inactive_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`idUsuario`, `idPersonal`, `correo`, `clave`, `idRol`, `estado`, `create_at`, `inactive_at`) VALUES
(1, 1, 'gerente', '$2y$10$RaoPTBz9oVETRVocodEaWuwxQPjshzARRmDnGZcWcDY43YxNF/sIa', 1, b'1', '2025-01-28 03:51:30', NULL),
(2, 2, 'admin', '$2y$10$RaoPTBz9oVETRVocodEaWuwxQPjshzARRmDnGZcWcDY43YxNF/sIa', 2, b'1', '2025-01-28 03:51:30', NULL),
(3, 3, 'superE', '$2y$10$RaoPTBz9oVETRVocodEaWuwxQPjshzARRmDnGZcWcDY43YxNF/sIa', 3, b'1', '2025-01-28 03:51:30', NULL),
(4, 4, 'superC', '$2y$10$RaoPTBz9oVETRVocodEaWuwxQPjshzARRmDnGZcWcDY43YxNF/sIa', 4, b'1', '2025-01-28 03:51:30', NULL),
(5, 5, 'medico', '$2y$10$RaoPTBz9oVETRVocodEaWuwxQPjshzARRmDnGZcWcDY43YxNF/sIa', 5, b'1', '2025-01-28 03:51:30', NULL),
(6, 6, 'herrero', '$2y$10$RaoPTBz9oVETRVocodEaWuwxQPjshzARRmDnGZcWcDY43YxNF/sIa', 6, b'1', '2025-01-28 03:51:30', NULL),
(7, 9, 'jose', '$2y$10$8UuHdbYIkL036ecao7Y69.yCV9GHZhMbMDulc95h/dSDTbR6shCU.', 2, b'0', '2025-01-29 05:47:51', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `viasadministracion`
--

CREATE TABLE `viasadministracion` (
  `idViaAdministracion` int(11) NOT NULL,
  `nombreVia` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `viasadministracion`
--

INSERT INTO `viasadministracion` (`idViaAdministracion`, `nombreVia`, `descripcion`) VALUES
(1, 'Oral', 'Por la boca.'),
(2, 'Intravenosa', 'En una vena.'),
(3, 'Intramuscular', 'En un músculo.'),
(4, 'Sublingual', 'Bajo la lengua.'),
(5, 'Tópica', 'Sobre la piel.'),
(6, 'Rectal', 'Por el recto.'),
(7, 'Inhalatoria', 'Por las vías respiratorias.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vistas`
--

CREATE TABLE `vistas` (
  `idvista` int(11) NOT NULL,
  `idmodulo` int(11) DEFAULT NULL,
  `ruta` varchar(50) NOT NULL,
  `sidebaroption` char(1) NOT NULL,
  `texto` varchar(40) DEFAULT NULL,
  `icono` varchar(35) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `vistas`
--

INSERT INTO `vistas` (`idvista`, `idmodulo`, `ruta`, `sidebaroption`, `texto`, `icono`) VALUES
(1, NULL, 'home', 'S', 'Inicio', 'fas fa-home'),
(2, 1, 'rotar-campo', 'S', 'Campos', 'fa-solid fa-group-arrows-rotate'),
(3, 1, 'programar-rotacion', 'S', 'Rotacion Campos', 'fa-solid fa-calendar-days'),
(4, 2, 'historial-equino', 'S', 'Historial Equinos', 'fas fa-history'),
(5, 2, 'listar-bostas', 'S', 'Listado Bostas', 'fa-solid fa-list'),
(6, 2, 'listar-equino', 'S', 'Listado Equinos', 'fa-solid fa-list'),
(7, 2, 'mostrar-foto', 'S', 'Colección de Fotos', 'fa-solid fa-image'),
(8, 2, 'registrar-bostas', 'S', 'Registro Bostas', 'fas fa-poop'),
(9, 2, 'registrar-equino', 'S', 'Registro Equinos', 'fa-solid fa-horse'),
(10, 2, 'listar-equino-externo', 'S', 'Listado Equinos Ajenos', 'fas fa-file-alt'),
(11, 3, 'diagnosticar-equino', 'N', NULL, NULL),
(12, 3, 'listar-diagnostico-avanzado', 'N', NULL, NULL),
(13, 3, 'revisar-equino', 'N', NULL, NULL),
(14, 3, 'seleccionar-diagnostico', 'S', 'Diagnóstico', 'fa-solid fa-notes-medical'),
(15, 3, 'listar-diagnostico-basico', 'N', NULL, NULL),
(16, 4, 'administrar-alimento', 'S', 'Alimentos', 'fas fa-apple-alt'),
(17, 4, 'administrar-herramienta', 'S', 'Herrero', 'fas fa-wrench'),
(18, 4, 'administrar-medicamento', 'S', 'Medicamentos', 'fas fa-pills'),
(19, 4, 'listar-accion-herrero', 'N', NULL, NULL),
(20, 4, 'listar-alimento', 'N', NULL, NULL),
(21, 4, 'listar-implemento-caballo', 'N', NULL, NULL),
(22, 4, 'listar-implemento-campo', 'N', NULL, NULL),
(23, 4, 'listar-medicamento', 'N', NULL, NULL),
(24, 4, 'registrar-implemento-caballo', 'S', 'Implementos Caballos', 'fa-solid fa-scissors'),
(25, 4, 'registrar-implemento-campo', 'S', 'Implementos Campos', 'fa-solid fa-wrench'),
(26, 4, 'listar-historial-medicamento', 'N', NULL, NULL),
(27, 4, 'listar-historial-alimento', 'N', NULL, NULL),
(28, 4, 'listar-historial-I-caballo', 'N', NULL, NULL),
(29, 4, 'listar-historial-I-campo', 'N', NULL, NULL),
(30, 5, 'presionar-boton-reporte', 'S', 'Reportes', 'fa-solid fa-file-circle-plus'),
(31, 6, 'listar-medicamento-usado', 'N', NULL, NULL),
(32, 6, 'listar-servicio', 'S', 'Listado Servicios', 'fa-solid fa-list'),
(33, 6, 'servir-mixto', 'S', 'Servicio Mixto', 'fas fa-exchange-alt'),
(34, 6, 'servir-propio', 'S', 'Servicio Propio', 'fas fa-tools'),
(35, 7, 'registrar-personal', 'S', 'Registrar Personal', 'fa-solid fa-wallet'),
(36, 7, 'actualizar-contrasenia', 'S', 'Actualizar Contraseña', 'fas fa-key');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alimentos`
--
ALTER TABLE `alimentos`
  ADD PRIMARY KEY (`idAlimento`),
  ADD KEY `fk_alimento_usuario` (`idUsuario`),
  ADD KEY `fk_alimento_tipoalimento` (`idTipoAlimento`),
  ADD KEY `fk_alimento_unidadmedida` (`idUnidadMedida`),
  ADD KEY `fk_alimento_lote` (`idLote`),
  ADD KEY `fk_alimento_equino` (`idEquino`);

--
-- Indices de la tabla `asistenciapersonal`
--
ALTER TABLE `asistenciapersonal`
  ADD PRIMARY KEY (`idAsistencia`),
  ADD KEY `fk_asistencia_personal` (`idPersonal`);

--
-- Indices de la tabla `bostas`
--
ALTER TABLE `bostas`
  ADD PRIMARY KEY (`idbosta`),
  ADD UNIQUE KEY `fecha` (`fecha`);

--
-- Indices de la tabla `campos`
--
ALTER TABLE `campos`
  ADD PRIMARY KEY (`idCampo`);

--
-- Indices de la tabla `combinacionesmedicamentos`
--
ALTER TABLE `combinacionesmedicamentos`
  ADD PRIMARY KEY (`idCombinacion`),
  ADD UNIQUE KEY `idTipo` (`idTipo`,`idPresentacion`,`dosis`,`idUnidad`),
  ADD KEY `idPresentacion` (`idPresentacion`),
  ADD KEY `idUnidad` (`idUnidad`);

--
-- Indices de la tabla `detallemedicamentos`
--
ALTER TABLE `detallemedicamentos`
  ADD PRIMARY KEY (`idDetalleMed`),
  ADD KEY `fk_detallemed_medicamento` (`idMedicamento`),
  ADD KEY `fk_detallemed_equino` (`idEquino`),
  ADD KEY `fk_detallemed_usuario` (`idUsuario`),
  ADD KEY `fk_detallemed_via` (`idViaAdministracion`);

--
-- Indices de la tabla `entrenamientos`
--
ALTER TABLE `entrenamientos`
  ADD PRIMARY KEY (`idEntrenamiento`),
  ADD KEY `fk_entrenamiento_equino` (`idEquino`);

--
-- Indices de la tabla `equinos`
--
ALTER TABLE `equinos`
  ADD PRIMARY KEY (`idEquino`),
  ADD KEY `fk_equino_tipoequino` (`idTipoEquino`),
  ADD KEY `fk_equino_propietario` (`idPropietario`),
  ADD KEY `fk_equino_estado_monta` (`idEstadoMonta`),
  ADD KEY `fk_equino_nacionalidad` (`idNacionalidad`);

--
-- Indices de la tabla `estadomonta`
--
ALTER TABLE `estadomonta`
  ADD PRIMARY KEY (`idEstadoMonta`);

--
-- Indices de la tabla `fotografiaequinos`
--
ALTER TABLE `fotografiaequinos`
  ADD PRIMARY KEY (`idfotografia`),
  ADD KEY `fk_public_id_ft` (`idEquino`);

--
-- Indices de la tabla `herramientas`
--
ALTER TABLE `herramientas`
  ADD PRIMARY KEY (`idHerramienta`),
  ADD UNIQUE KEY `nombreHerramienta` (`nombreHerramienta`);

--
-- Indices de la tabla `herramientasusadashistorial`
--
ALTER TABLE `herramientasusadashistorial`
  ADD PRIMARY KEY (`idHerramientasUsadas`),
  ADD KEY `fk_herramienta_historial` (`idHistorialHerrero`),
  ADD KEY `fk_herramienta` (`idHerramienta`);

--
-- Indices de la tabla `historialdosisaplicadas`
--
ALTER TABLE `historialdosisaplicadas`
  ADD PRIMARY KEY (`idDosis`),
  ADD KEY `fk_idMedicamento` (`idMedicamento`),
  ADD KEY `fk_idEquino_dosis` (`idEquino`),
  ADD KEY `fk_idUsuario_dosis` (`idUsuario`);

--
-- Indices de la tabla `historialequinos`
--
ALTER TABLE `historialequinos`
  ADD PRIMARY KEY (`idHistorial`),
  ADD KEY `fk_idEquino_historial` (`idEquino`);

--
-- Indices de la tabla `historialherrero`
--
ALTER TABLE `historialherrero`
  ADD PRIMARY KEY (`idHistorialHerrero`),
  ADD KEY `fk_historialherrero_equino` (`idEquino`),
  ADD KEY `fk_historialherrero_usuario` (`idUsuario`),
  ADD KEY `fk_historialherrero_trabajo` (`idTrabajo`);

--
-- Indices de la tabla `historialimplemento`
--
ALTER TABLE `historialimplemento`
  ADD PRIMARY KEY (`idHistorial`),
  ADD KEY `fk_historial_inventario` (`idInventario`),
  ADD KEY `fk_historial_tipoinventario` (`idTipoinventario`);

--
-- Indices de la tabla `historialmovimientos`
--
ALTER TABLE `historialmovimientos`
  ADD PRIMARY KEY (`idMovimiento`),
  ADD KEY `idAlimento` (`idAlimento`),
  ADD KEY `idEquino` (`idEquino`),
  ADD KEY `idUsuario` (`idUsuario`);

--
-- Indices de la tabla `historialmovimientosmedicamentos`
--
ALTER TABLE `historialmovimientosmedicamentos`
  ADD PRIMARY KEY (`idMovimiento`),
  ADD KEY `idMedicamento` (`idMedicamento`),
  ADD KEY `fk_historialmedicamentos_equino` (`idEquino`),
  ADD KEY `idUsuario` (`idUsuario`);

--
-- Indices de la tabla `implementos`
--
ALTER TABLE `implementos`
  ADD PRIMARY KEY (`idInventario`),
  ADD UNIQUE KEY `fk_implemento_nombreProducto` (`nombreProducto`),
  ADD KEY `fk_implemento_inventario` (`idTipoinventario`),
  ADD KEY `fk_implemento_movimiento` (`idTipomovimiento`);

--
-- Indices de la tabla `lotesalimento`
--
ALTER TABLE `lotesalimento`
  ADD PRIMARY KEY (`idLote`);

--
-- Indices de la tabla `lotesmedicamento`
--
ALTER TABLE `lotesmedicamento`
  ADD PRIMARY KEY (`idLoteMedicamento`),
  ADD UNIQUE KEY `UQ_lote_medicamento` (`lote`);

--
-- Indices de la tabla `medicamentos`
--
ALTER TABLE `medicamentos`
  ADD PRIMARY KEY (`idMedicamento`),
  ADD KEY `fk_medicamento_usuario` (`idUsuario`),
  ADD KEY `fk_medicamento_combinacion` (`idCombinacion`),
  ADD KEY `fk_medicamento_lote` (`idLoteMedicamento`),
  ADD KEY `fk_medicamento_equino` (`idEquino`);

--
-- Indices de la tabla `mermasalimento`
--
ALTER TABLE `mermasalimento`
  ADD PRIMARY KEY (`idMerma`),
  ADD KEY `fk_merma_alimento` (`idAlimento`);

--
-- Indices de la tabla `modulos`
--
ALTER TABLE `modulos`
  ADD PRIMARY KEY (`idmodulo`),
  ADD UNIQUE KEY `uk_modulo_mod` (`modulo`);

--
-- Indices de la tabla `nacionalidades`
--
ALTER TABLE `nacionalidades`
  ADD PRIMARY KEY (`idNacionalidad`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`idpermiso`),
  ADD UNIQUE KEY `uk_vista_per` (`idRol`,`idvista`),
  ADD KEY `fk_idvisita_per` (`idvista`);

--
-- Indices de la tabla `personal`
--
ALTER TABLE `personal`
  ADD PRIMARY KEY (`idPersonal`),
  ADD UNIQUE KEY `nrodocumento` (`nrodocumento`);

--
-- Indices de la tabla `presentacionesmedicamentos`
--
ALTER TABLE `presentacionesmedicamentos`
  ADD PRIMARY KEY (`idPresentacion`),
  ADD UNIQUE KEY `presentacion` (`presentacion`);

--
-- Indices de la tabla `propietarios`
--
ALTER TABLE `propietarios`
  ADD PRIMARY KEY (`idPropietario`);

--
-- Indices de la tabla `revisionequinos`
--
ALTER TABLE `revisionequinos`
  ADD PRIMARY KEY (`idRevision`),
  ADD KEY `fk_idEquino_revision` (`idEquino`),
  ADD KEY `fk_idPropietario_revision` (`idPropietario`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`idRol`);

--
-- Indices de la tabla `rotacioncampos`
--
ALTER TABLE `rotacioncampos`
  ADD PRIMARY KEY (`idRotacion`),
  ADD KEY `fk_rotacioncampo_campo` (`idCampo`),
  ADD KEY `fk_rotacioncampo_tiporotacion` (`idTipoRotacion`);

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD PRIMARY KEY (`idServicio`),
  ADD KEY `fk_servicio_equino_macho` (`idEquinoMacho`),
  ADD KEY `fk_servicio_equino_hembra` (`idEquinoHembra`),
  ADD KEY `fk_servicio_equino_externo` (`idEquinoExterno`),
  ADD KEY `fk_servicio_medicamento` (`idMedicamento`),
  ADD KEY `fk_servicio_propietario` (`idPropietario`);

--
-- Indices de la tabla `tipoalimentos`
--
ALTER TABLE `tipoalimentos`
  ADD PRIMARY KEY (`idTipoAlimento`),
  ADD UNIQUE KEY `tipoAlimento` (`tipoAlimento`);

--
-- Indices de la tabla `tipoalimento_unidadmedida`
--
ALTER TABLE `tipoalimento_unidadmedida`
  ADD PRIMARY KEY (`idTipoAlimento`,`idUnidadMedida`),
  ADD UNIQUE KEY `uq_tipo_unidad` (`idTipoAlimento`,`idUnidadMedida`),
  ADD KEY `fk_unidadmedida` (`idUnidadMedida`);

--
-- Indices de la tabla `tipoequinos`
--
ALTER TABLE `tipoequinos`
  ADD PRIMARY KEY (`idTipoEquino`);

--
-- Indices de la tabla `tipoinventarios`
--
ALTER TABLE `tipoinventarios`
  ADD PRIMARY KEY (`idTipoinventario`);

--
-- Indices de la tabla `tipomovimientos`
--
ALTER TABLE `tipomovimientos`
  ADD PRIMARY KEY (`idTipomovimiento`);

--
-- Indices de la tabla `tiporotaciones`
--
ALTER TABLE `tiporotaciones`
  ADD PRIMARY KEY (`idTipoRotacion`);

--
-- Indices de la tabla `tiposmedicamentos`
--
ALTER TABLE `tiposmedicamentos`
  ADD PRIMARY KEY (`idTipo`),
  ADD UNIQUE KEY `tipo` (`tipo`);

--
-- Indices de la tabla `tipostrabajos`
--
ALTER TABLE `tipostrabajos`
  ADD PRIMARY KEY (`idTipoTrabajo`),
  ADD UNIQUE KEY `nombreTrabajo` (`nombreTrabajo`);

--
-- Indices de la tabla `tiposuelo`
--
ALTER TABLE `tiposuelo`
  ADD PRIMARY KEY (`idTipoSuelo`),
  ADD UNIQUE KEY `nombreTipoSuelo` (`nombreTipoSuelo`);

--
-- Indices de la tabla `unidadesmedida`
--
ALTER TABLE `unidadesmedida`
  ADD PRIMARY KEY (`idUnidad`),
  ADD UNIQUE KEY `unidad` (`unidad`);

--
-- Indices de la tabla `unidadesmedidaalimento`
--
ALTER TABLE `unidadesmedidaalimento`
  ADD PRIMARY KEY (`idUnidadMedida`),
  ADD UNIQUE KEY `nombreUnidad` (`nombreUnidad`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`idUsuario`),
  ADD UNIQUE KEY `uk_correo` (`correo`),
  ADD KEY `fk_usuario_personal` (`idPersonal`),
  ADD KEY `fk_usuario_rol` (`idRol`);

--
-- Indices de la tabla `viasadministracion`
--
ALTER TABLE `viasadministracion`
  ADD PRIMARY KEY (`idViaAdministracion`),
  ADD UNIQUE KEY `nombreVia` (`nombreVia`);

--
-- Indices de la tabla `vistas`
--
ALTER TABLE `vistas`
  ADD PRIMARY KEY (`idvista`),
  ADD UNIQUE KEY `uk_ruta_vis` (`ruta`),
  ADD KEY `fk_idmodulo_vis` (`idmodulo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alimentos`
--
ALTER TABLE `alimentos`
  MODIFY `idAlimento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `asistenciapersonal`
--
ALTER TABLE `asistenciapersonal`
  MODIFY `idAsistencia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `bostas`
--
ALTER TABLE `bostas`
  MODIFY `idbosta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `campos`
--
ALTER TABLE `campos`
  MODIFY `idCampo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `combinacionesmedicamentos`
--
ALTER TABLE `combinacionesmedicamentos`
  MODIFY `idCombinacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `detallemedicamentos`
--
ALTER TABLE `detallemedicamentos`
  MODIFY `idDetalleMed` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `entrenamientos`
--
ALTER TABLE `entrenamientos`
  MODIFY `idEntrenamiento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `equinos`
--
ALTER TABLE `equinos`
  MODIFY `idEquino` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `estadomonta`
--
ALTER TABLE `estadomonta`
  MODIFY `idEstadoMonta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `fotografiaequinos`
--
ALTER TABLE `fotografiaequinos`
  MODIFY `idfotografia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `herramientas`
--
ALTER TABLE `herramientas`
  MODIFY `idHerramienta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `herramientasusadashistorial`
--
ALTER TABLE `herramientasusadashistorial`
  MODIFY `idHerramientasUsadas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `historialdosisaplicadas`
--
ALTER TABLE `historialdosisaplicadas`
  MODIFY `idDosis` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `historialequinos`
--
ALTER TABLE `historialequinos`
  MODIFY `idHistorial` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `historialherrero`
--
ALTER TABLE `historialherrero`
  MODIFY `idHistorialHerrero` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `historialimplemento`
--
ALTER TABLE `historialimplemento`
  MODIFY `idHistorial` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `historialmovimientos`
--
ALTER TABLE `historialmovimientos`
  MODIFY `idMovimiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `historialmovimientosmedicamentos`
--
ALTER TABLE `historialmovimientosmedicamentos`
  MODIFY `idMovimiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `implementos`
--
ALTER TABLE `implementos`
  MODIFY `idInventario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `lotesalimento`
--
ALTER TABLE `lotesalimento`
  MODIFY `idLote` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `lotesmedicamento`
--
ALTER TABLE `lotesmedicamento`
  MODIFY `idLoteMedicamento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `medicamentos`
--
ALTER TABLE `medicamentos`
  MODIFY `idMedicamento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `mermasalimento`
--
ALTER TABLE `mermasalimento`
  MODIFY `idMerma` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
  MODIFY `idmodulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `nacionalidades`
--
ALTER TABLE `nacionalidades`
  MODIFY `idNacionalidad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=183;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `idpermiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT de la tabla `personal`
--
ALTER TABLE `personal`
  MODIFY `idPersonal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `presentacionesmedicamentos`
--
ALTER TABLE `presentacionesmedicamentos`
  MODIFY `idPresentacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `propietarios`
--
ALTER TABLE `propietarios`
  MODIFY `idPropietario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `revisionequinos`
--
ALTER TABLE `revisionequinos`
  MODIFY `idRevision` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `idRol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `rotacioncampos`
--
ALTER TABLE `rotacioncampos`
  MODIFY `idRotacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `idServicio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `tipoalimentos`
--
ALTER TABLE `tipoalimentos`
  MODIFY `idTipoAlimento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `tipoequinos`
--
ALTER TABLE `tipoequinos`
  MODIFY `idTipoEquino` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tipoinventarios`
--
ALTER TABLE `tipoinventarios`
  MODIFY `idTipoinventario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tipomovimientos`
--
ALTER TABLE `tipomovimientos`
  MODIFY `idTipomovimiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tiporotaciones`
--
ALTER TABLE `tiporotaciones`
  MODIFY `idTipoRotacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `tiposmedicamentos`
--
ALTER TABLE `tiposmedicamentos`
  MODIFY `idTipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `tipostrabajos`
--
ALTER TABLE `tipostrabajos`
  MODIFY `idTipoTrabajo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tiposuelo`
--
ALTER TABLE `tiposuelo`
  MODIFY `idTipoSuelo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `unidadesmedida`
--
ALTER TABLE `unidadesmedida`
  MODIFY `idUnidad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `unidadesmedidaalimento`
--
ALTER TABLE `unidadesmedidaalimento`
  MODIFY `idUnidadMedida` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `idUsuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `viasadministracion`
--
ALTER TABLE `viasadministracion`
  MODIFY `idViaAdministracion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `vistas`
--
ALTER TABLE `vistas`
  MODIFY `idvista` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alimentos`
--
ALTER TABLE `alimentos`
  ADD CONSTRAINT `fk_alimento_equino` FOREIGN KEY (`idEquino`) REFERENCES `equinos` (`idEquino`),
  ADD CONSTRAINT `fk_alimento_lote` FOREIGN KEY (`idLote`) REFERENCES `lotesalimento` (`idLote`),
  ADD CONSTRAINT `fk_alimento_tipoalimento` FOREIGN KEY (`idTipoAlimento`) REFERENCES `tipoalimentos` (`idTipoAlimento`),
  ADD CONSTRAINT `fk_alimento_unidadmedida` FOREIGN KEY (`idUnidadMedida`) REFERENCES `unidadesmedidaalimento` (`idUnidadMedida`),
  ADD CONSTRAINT `fk_alimento_usuario` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`);

--
-- Filtros para la tabla `asistenciapersonal`
--
ALTER TABLE `asistenciapersonal`
  ADD CONSTRAINT `fk_asistencia_personal` FOREIGN KEY (`idPersonal`) REFERENCES `personal` (`idPersonal`);

--
-- Filtros para la tabla `combinacionesmedicamentos`
--
ALTER TABLE `combinacionesmedicamentos`
  ADD CONSTRAINT `combinacionesmedicamentos_ibfk_1` FOREIGN KEY (`idTipo`) REFERENCES `tiposmedicamentos` (`idTipo`),
  ADD CONSTRAINT `combinacionesmedicamentos_ibfk_2` FOREIGN KEY (`idPresentacion`) REFERENCES `presentacionesmedicamentos` (`idPresentacion`),
  ADD CONSTRAINT `combinacionesmedicamentos_ibfk_3` FOREIGN KEY (`idUnidad`) REFERENCES `unidadesmedida` (`idUnidad`);

--
-- Filtros para la tabla `detallemedicamentos`
--
ALTER TABLE `detallemedicamentos`
  ADD CONSTRAINT `fk_detallemed_equino` FOREIGN KEY (`idEquino`) REFERENCES `equinos` (`idEquino`),
  ADD CONSTRAINT `fk_detallemed_medicamento` FOREIGN KEY (`idMedicamento`) REFERENCES `medicamentos` (`idMedicamento`),
  ADD CONSTRAINT `fk_detallemed_usuario` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`),
  ADD CONSTRAINT `fk_detallemed_via` FOREIGN KEY (`idViaAdministracion`) REFERENCES `viasadministracion` (`idViaAdministracion`);

--
-- Filtros para la tabla `entrenamientos`
--
ALTER TABLE `entrenamientos`
  ADD CONSTRAINT `fk_entrenamiento_equino` FOREIGN KEY (`idEquino`) REFERENCES `equinos` (`idEquino`);

--
-- Filtros para la tabla `equinos`
--
ALTER TABLE `equinos`
  ADD CONSTRAINT `fk_equino_estado_monta` FOREIGN KEY (`idEstadoMonta`) REFERENCES `estadomonta` (`idEstadoMonta`),
  ADD CONSTRAINT `fk_equino_nacionalidad` FOREIGN KEY (`idNacionalidad`) REFERENCES `nacionalidades` (`idNacionalidad`),
  ADD CONSTRAINT `fk_equino_propietario` FOREIGN KEY (`idPropietario`) REFERENCES `propietarios` (`idPropietario`),
  ADD CONSTRAINT `fk_equino_tipoequino` FOREIGN KEY (`idTipoEquino`) REFERENCES `tipoequinos` (`idTipoEquino`);

--
-- Filtros para la tabla `fotografiaequinos`
--
ALTER TABLE `fotografiaequinos`
  ADD CONSTRAINT `fk_public_id_ft` FOREIGN KEY (`idEquino`) REFERENCES `equinos` (`idEquino`) ON DELETE CASCADE;

--
-- Filtros para la tabla `herramientasusadashistorial`
--
ALTER TABLE `herramientasusadashistorial`
  ADD CONSTRAINT `fk_herramienta` FOREIGN KEY (`idHerramienta`) REFERENCES `herramientas` (`idHerramienta`),
  ADD CONSTRAINT `fk_herramienta_historial` FOREIGN KEY (`idHistorialHerrero`) REFERENCES `historialherrero` (`idHistorialHerrero`);

--
-- Filtros para la tabla `historialdosisaplicadas`
--
ALTER TABLE `historialdosisaplicadas`
  ADD CONSTRAINT `fk_idEquino_dosis` FOREIGN KEY (`idEquino`) REFERENCES `equinos` (`idEquino`),
  ADD CONSTRAINT `fk_idMedicamento` FOREIGN KEY (`idMedicamento`) REFERENCES `medicamentos` (`idMedicamento`),
  ADD CONSTRAINT `fk_idUsuario_dosis` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`);

--
-- Filtros para la tabla `historialequinos`
--
ALTER TABLE `historialequinos`
  ADD CONSTRAINT `fk_idEquino_historial` FOREIGN KEY (`idEquino`) REFERENCES `equinos` (`idEquino`);

--
-- Filtros para la tabla `historialherrero`
--
ALTER TABLE `historialherrero`
  ADD CONSTRAINT `fk_historialherrero_equino` FOREIGN KEY (`idEquino`) REFERENCES `equinos` (`idEquino`),
  ADD CONSTRAINT `fk_historialherrero_trabajo` FOREIGN KEY (`idTrabajo`) REFERENCES `tipostrabajos` (`idTipoTrabajo`),
  ADD CONSTRAINT `fk_historialherrero_usuario` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`);

--
-- Filtros para la tabla `historialimplemento`
--
ALTER TABLE `historialimplemento`
  ADD CONSTRAINT `fk_historial_inventario` FOREIGN KEY (`idInventario`) REFERENCES `implementos` (`idInventario`),
  ADD CONSTRAINT `fk_historial_tipoinventario` FOREIGN KEY (`idTipoinventario`) REFERENCES `tipoinventarios` (`idTipoinventario`);

--
-- Filtros para la tabla `historialmovimientos`
--
ALTER TABLE `historialmovimientos`
  ADD CONSTRAINT `historialmovimientos_ibfk_1` FOREIGN KEY (`idAlimento`) REFERENCES `alimentos` (`idAlimento`),
  ADD CONSTRAINT `historialmovimientos_ibfk_2` FOREIGN KEY (`idEquino`) REFERENCES `equinos` (`idEquino`),
  ADD CONSTRAINT `historialmovimientos_ibfk_3` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`);

--
-- Filtros para la tabla `historialmovimientosmedicamentos`
--
ALTER TABLE `historialmovimientosmedicamentos`
  ADD CONSTRAINT `fk_historialmedicamentos_equino` FOREIGN KEY (`idEquino`) REFERENCES `equinos` (`idEquino`),
  ADD CONSTRAINT `historialmovimientosmedicamentos_ibfk_1` FOREIGN KEY (`idMedicamento`) REFERENCES `medicamentos` (`idMedicamento`),
  ADD CONSTRAINT `historialmovimientosmedicamentos_ibfk_2` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`);

--
-- Filtros para la tabla `implementos`
--
ALTER TABLE `implementos`
  ADD CONSTRAINT `fk_implemento_inventario` FOREIGN KEY (`idTipoinventario`) REFERENCES `tipoinventarios` (`idTipoinventario`),
  ADD CONSTRAINT `fk_implemento_movimiento` FOREIGN KEY (`idTipomovimiento`) REFERENCES `tipomovimientos` (`idTipomovimiento`);

--
-- Filtros para la tabla `medicamentos`
--
ALTER TABLE `medicamentos`
  ADD CONSTRAINT `fk_medicamento_combinacion` FOREIGN KEY (`idCombinacion`) REFERENCES `combinacionesmedicamentos` (`idCombinacion`),
  ADD CONSTRAINT `fk_medicamento_equino` FOREIGN KEY (`idEquino`) REFERENCES `equinos` (`idEquino`),
  ADD CONSTRAINT `fk_medicamento_lote` FOREIGN KEY (`idLoteMedicamento`) REFERENCES `lotesmedicamento` (`idLoteMedicamento`),
  ADD CONSTRAINT `fk_medicamento_usuario` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`);

--
-- Filtros para la tabla `mermasalimento`
--
ALTER TABLE `mermasalimento`
  ADD CONSTRAINT `fk_merma_alimento` FOREIGN KEY (`idAlimento`) REFERENCES `alimentos` (`idAlimento`) ON DELETE CASCADE;

--
-- Filtros para la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD CONSTRAINT `fk_idRol_per` FOREIGN KEY (`idRol`) REFERENCES `roles` (`idRol`),
  ADD CONSTRAINT `fk_idvisita_per` FOREIGN KEY (`idvista`) REFERENCES `vistas` (`idvista`);

--
-- Filtros para la tabla `revisionequinos`
--
ALTER TABLE `revisionequinos`
  ADD CONSTRAINT `fk_idEquino_revision` FOREIGN KEY (`idEquino`) REFERENCES `equinos` (`idEquino`),
  ADD CONSTRAINT `fk_idPropietario_revision` FOREIGN KEY (`idPropietario`) REFERENCES `propietarios` (`idPropietario`);

--
-- Filtros para la tabla `rotacioncampos`
--
ALTER TABLE `rotacioncampos`
  ADD CONSTRAINT `fk_rotacioncampo_campo` FOREIGN KEY (`idCampo`) REFERENCES `campos` (`idCampo`),
  ADD CONSTRAINT `fk_rotacioncampo_tiporotacion` FOREIGN KEY (`idTipoRotacion`) REFERENCES `tiporotaciones` (`idTipoRotacion`);

--
-- Filtros para la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD CONSTRAINT `fk_servicio_equino_externo` FOREIGN KEY (`idEquinoExterno`) REFERENCES `equinos` (`idEquino`),
  ADD CONSTRAINT `fk_servicio_equino_hembra` FOREIGN KEY (`idEquinoHembra`) REFERENCES `equinos` (`idEquino`),
  ADD CONSTRAINT `fk_servicio_equino_macho` FOREIGN KEY (`idEquinoMacho`) REFERENCES `equinos` (`idEquino`),
  ADD CONSTRAINT `fk_servicio_medicamento` FOREIGN KEY (`idMedicamento`) REFERENCES `medicamentos` (`idMedicamento`),
  ADD CONSTRAINT `fk_servicio_propietario` FOREIGN KEY (`idPropietario`) REFERENCES `propietarios` (`idPropietario`);

--
-- Filtros para la tabla `tipoalimento_unidadmedida`
--
ALTER TABLE `tipoalimento_unidadmedida`
  ADD CONSTRAINT `fk_tipoalimento` FOREIGN KEY (`idTipoAlimento`) REFERENCES `tipoalimentos` (`idTipoAlimento`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_unidadmedida` FOREIGN KEY (`idUnidadMedida`) REFERENCES `unidadesmedidaalimento` (`idUnidadMedida`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuario_personal` FOREIGN KEY (`idPersonal`) REFERENCES `personal` (`idPersonal`),
  ADD CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`idRol`) REFERENCES `roles` (`idRol`);

--
-- Filtros para la tabla `vistas`
--
ALTER TABLE `vistas`
  ADD CONSTRAINT `fk_idmodulo_vis` FOREIGN KEY (`idmodulo`) REFERENCES `modulos` (`idmodulo`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
