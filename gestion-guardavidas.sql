-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 05-11-2025 a las 16:23:14
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
-- Base de datos: `gestion-guardavidas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencias`
--

CREATE TABLE `asistencias` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `longitud` decimal(10,7) NOT NULL,
  `latitud` decimal(10,7) NOT NULL,
  `precision` double NOT NULL,
  `puesto_id` bigint(20) UNSIGNED NOT NULL,
  `guardavidas_id` bigint(20) UNSIGNED NOT NULL,
  `fecha_hora` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `banderas`
--

CREATE TABLE `banderas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `turno` enum('M','T') NOT NULL,
  `bandera_id` bigint(20) UNSIGNED NOT NULL,
  `viento_direccion` varchar(255) DEFAULT NULL,
  `viento_intensidad` varchar(255) DEFAULT NULL,
  `temperatura` varchar(255) DEFAULT NULL,
  `playa_id` bigint(20) UNSIGNED NOT NULL,
  `puesto_id` bigint(20) UNSIGNED DEFAULT NULL,
  `detalles` longtext DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bandera_tipos`
--

CREATE TABLE `bandera_tipos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `codigo` varchar(255) NOT NULL,
  `color` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `bandera_tipos`
--

INSERT INTO `bandera_tipos` (`id`, `codigo`, `color`, `created_at`, `updated_at`) VALUES
(1, 'Mar bueno', 'bandera-bueno', NULL, NULL),
(2, 'Dudoso', 'bandera-dudoso', NULL, NULL),
(3, 'Peligroso', 'bandera-peligroso', NULL, NULL),
(4, 'Rayos', 'bandera-rayos', NULL, NULL),
(5, 'Prohibido bañarse', 'bandera-prohibido', NULL, NULL),
(6, 'Niño perdido', 'bandera-perdido', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('gestion-guardavidas-cache-spatie.permission.cache', 'a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:38:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";s:12:\"ver usuarios\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:14:\"crear usuarios\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:15:\"editar usuarios\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:17:\"eliminar usuarios\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:11:\"ver puestos\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:13:\"crear puestos\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:14:\"editar puestos\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:16:\"eliminar puestos\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:8;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:15:\"agregar_bandera\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:9;a:4:{s:1:\"a\";i:10;s:1:\"b\";s:14:\"editar_bandera\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:10;a:4:{s:1:\"a\";i:11;s:1:\"b\";s:16:\"eliminar_bandera\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:11;a:4:{s:1:\"a\";i:12;s:1:\"b\";s:11:\"ver_bandera\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:12;a:4:{s:1:\"a\";i:13;s:1:\"b\";s:18:\"agregar_guardavida\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:13;a:4:{s:1:\"a\";i:14;s:1:\"b\";s:17:\"editar_guardavida\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:14;a:4:{s:1:\"a\";i:15;s:1:\"b\";s:19:\"eliminar_guardavida\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:15;a:4:{s:1:\"a\";i:16;s:1:\"b\";s:14:\"ver_guardavida\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:16;a:4:{s:1:\"a\";i:17;s:1:\"b\";s:20:\"agregar_intervencion\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:17;a:4:{s:1:\"a\";i:18;s:1:\"b\";s:19:\"editar_intervencion\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:18;a:4:{s:1:\"a\";i:19;s:1:\"b\";s:21:\"eliminar_intervencion\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:19;a:4:{s:1:\"a\";i:20;s:1:\"b\";s:16:\"ver_intervencion\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:20;a:4:{s:1:\"a\";i:21;s:1:\"b\";s:24:\"agregar_novedad_material\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:21;a:4:{s:1:\"a\";i:22;s:1:\"b\";s:23:\"editar_novedad_material\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:22;a:4:{s:1:\"a\";i:23;s:1:\"b\";s:25:\"eliminar_novedad_material\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:23;a:4:{s:1:\"a\";i:24;s:1:\"b\";s:20:\"ver_novedad_material\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:24;a:4:{s:1:\"a\";i:25;s:1:\"b\";s:20:\"agregar_cambio_turno\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:25;a:4:{s:1:\"a\";i:26;s:1:\"b\";s:19:\"editar_cambio_turno\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:26;a:4:{s:1:\"a\";i:27;s:1:\"b\";s:21:\"eliminar_cambio_turno\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:27;a:4:{s:1:\"a\";i:28;s:1:\"b\";s:16:\"ver_cambio_turno\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:28;a:4:{s:1:\"a\";i:29;s:1:\"b\";s:16:\"agregar_licencia\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:29;a:4:{s:1:\"a\";i:30;s:1:\"b\";s:15:\"editar_licencia\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:30;a:4:{s:1:\"a\";i:31;s:1:\"b\";s:17:\"eliminar_licencia\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:31;a:4:{s:1:\"a\";i:32;s:1:\"b\";s:12:\"ver_licencia\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:32;a:4:{s:1:\"a\";i:33;s:1:\"b\";s:18:\"agregar_asistencia\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:33;a:4:{s:1:\"a\";i:34;s:1:\"b\";s:17:\"editar_asistencia\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:34;a:4:{s:1:\"a\";i:35;s:1:\"b\";s:19:\"eliminar_asistencia\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:35;a:4:{s:1:\"a\";i:36;s:1:\"b\";s:14:\"ver_asistencia\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:36;a:4:{s:1:\"a\";i:37;s:1:\"b\";s:21:\"ver_asistencia_propia\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:37;a:4:{s:1:\"a\";i:38;s:1:\"b\";s:20:\"abm_roles_y_permisos\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}}s:5:\"roles\";a:3:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:5:\"admin\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:9:\"encargado\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:10:\"guardavida\";s:1:\"c\";s:3:\"web\";}}}', 1762442418);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cambio_de_turnos`
--

CREATE TABLE `cambio_de_turnos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `turno_nuevo` enum('M','T') NOT NULL,
  `guardavida_id` bigint(20) UNSIGNED DEFAULT NULL,
  `playa_id` bigint(20) UNSIGNED NOT NULL,
  `puesto_id` bigint(20) UNSIGNED NOT NULL,
  `funcion` varchar(255) NOT NULL,
  `detalles` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fuerzas`
--

CREATE TABLE `fuerzas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `fuerzas`
--

INSERT INTO `fuerzas` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
(1, 'Ambulancia', NULL, NULL),
(2, 'Bomberos', NULL, NULL),
(3, 'Policía', NULL, NULL),
(4, 'Inspección municipal', NULL, NULL),
(5, 'Prefectura', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fuerzas_intervenciones`
--

CREATE TABLE `fuerzas_intervenciones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fuerza_id` bigint(20) UNSIGNED NOT NULL,
  `intervencion_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `guardavidas`
--

CREATE TABLE `guardavidas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `funcion` enum('Guardavida','Timonel','Encargado','Jefe_de_playa') NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `apellido` varchar(255) NOT NULL,
  `dni` varchar(255) NOT NULL,
  `telefono` varchar(255) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `numero` varchar(255) NOT NULL,
  `piso_dpto` varchar(255) DEFAULT NULL,
  `playa_id` bigint(20) UNSIGNED NOT NULL,
  `puesto_id` bigint(20) UNSIGNED NOT NULL,
  `turno` enum('M','T') DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `guardavidas`
--

INSERT INTO `guardavidas` (`id`, `funcion`, `nombre`, `apellido`, `dni`, `telefono`, `direccion`, `numero`, `piso_dpto`, `playa_id`, `puesto_id`, `turno`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'Encargado', 'María', 'Gómez', '30222333', '2983456789', 'Calle 12', '456', '2B', 1, 1, NULL, 2, '2025-11-05 15:20:05', '2025-11-05 15:20:05'),
(2, 'Guardavida', 'Carla', 'Fernández', '30444555', '2983678901', 'Av. Libertad', '321', '1A', 2, 4, NULL, 3, '2025-11-05 15:20:05', '2025-11-05 15:20:05'),
(3, 'Timonel', 'Juan', 'Pérez', '30111222', '2983123456', 'Av. Costanera', '123', NULL, 1, 1, NULL, 4, '2025-11-05 15:20:05', '2025-11-05 15:20:05'),
(4, 'Timonel', 'Valentina', 'Cast', '44993777', '2983123453', 'Av. Costanera', '123', NULL, 4, 11, NULL, 5, '2025-11-05 15:20:05', '2025-11-05 15:20:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `guardavidas_intervenciones`
--

CREATE TABLE `guardavidas_intervenciones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `guardavida_id` bigint(20) UNSIGNED NOT NULL,
  `intervencion_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `intervencions`
--

CREATE TABLE `intervencions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `tipo_intervencion` varchar(255) NOT NULL,
  `victimas` int(11) NOT NULL,
  `codigo` int(11) NOT NULL,
  `bandera_id` bigint(20) UNSIGNED DEFAULT NULL,
  `traslado` tinyint(1) NOT NULL,
  `playa_id` bigint(20) UNSIGNED NOT NULL,
  `puesto_id` bigint(20) UNSIGNED NOT NULL,
  `detalles` longtext DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `licencias`
--

CREATE TABLE `licencias` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `tipo_licencia` enum('Capacitación','Enfermedad','Evento deportivo','Exámen','Fallecimiento familiar','Feriado trabajado compensado','Lesión','Licencia médica','Permiso especial','Otro') NOT NULL,
  `en_tiempo` tinyint(1) NOT NULL,
  `turno` enum('M','T') NOT NULL,
  `guardavida_id` bigint(20) UNSIGNED DEFAULT NULL,
  `playa_id` bigint(20) UNSIGNED NOT NULL,
  `puesto_id` bigint(20) UNSIGNED DEFAULT NULL,
  `detalle` longtext DEFAULT NULL,
  `archivo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `licencia_tipos`
--

CREATE TABLE `licencia_tipos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `licencia_tipos`
--

INSERT INTO `licencia_tipos` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
(1, 'Enfermedad', NULL, NULL),
(2, 'Lesión', NULL, NULL),
(3, 'Licencia médica', NULL, NULL),
(4, 'Examen', NULL, NULL),
(5, 'Evento deportivo', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materiales`
--

CREATE TABLE `materiales` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `detalle` varchar(255) DEFAULT NULL,
  `playa_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_09_11_141356_create_playas_table', 1),
(5, '2025_09_11_150743_create_banderas_table', 1),
(6, '2025_09_11_154937_create_fuerzas_table', 1),
(7, '2025_09_11_155021_create_licencia_tipos_table', 1),
(8, '2025_09_12_122002_create_permission_tables', 1),
(9, '2025_09_18_130921_create_puestos_table', 1),
(10, '2025_09_18_133113_create_guardavidas_table', 1),
(11, '2025_09_18_135928_create_categorias_table', 1),
(12, '2025_09_22_144421_create_banderas_table', 1),
(13, '2025_09_23_111419_create_intervencions_table', 1),
(14, '2025_09_23_113218_create_guardavidas_intervenciones_table', 1),
(15, '2025_09_23_113241_create_fuerzas_intervenciones_table', 1),
(16, '2025_10_09_133152_asistencias', 1),
(17, '2025_10_16_160022_add_habilitado_to_users_table', 1),
(18, '2025_10_17_153025_create_materiales_table', 1),
(19, '2025_10_17_161019_create_novedad_materials_table', 1),
(20, '2025_10_23_153542_create_licencias_table', 1),
(21, '2025_10_24_114502_create_personal_access_tokens_table', 1),
(22, '2025_10_27_124224_add_turno_to_guardavidas_table', 1),
(23, '2025_10_29_155518_create_cambio_de_turnos_table', 1),
(24, '2025_10_31_090757_create_novedads_table', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 3),
(2, 'App\\Models\\User', 4),
(2, 'App\\Models\\User', 5),
(3, 'App\\Models\\User', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `novedades`
--

CREATE TABLE `novedades` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `tipo` varchar(255) DEFAULT NULL,
  `titulo` varchar(255) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `playa_id` bigint(20) UNSIGNED DEFAULT NULL,
  `referencia_modelo` varchar(255) DEFAULT NULL,
  `icono` longtext DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `referencia_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `novedad_materials`
--

CREATE TABLE `novedad_materials` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tipo_novedad` enum('Daño','Faltante','Falla','Pérdida','Rotura') NOT NULL,
  `material_id` bigint(20) UNSIGNED NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `playa_id` bigint(20) UNSIGNED NOT NULL,
  `detalles` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'ver usuarios', 'web', '2025-11-05 15:20:02', '2025-11-05 15:20:02'),
(2, 'crear usuarios', 'web', '2025-11-05 15:20:02', '2025-11-05 15:20:02'),
(3, 'editar usuarios', 'web', '2025-11-05 15:20:02', '2025-11-05 15:20:02'),
(4, 'eliminar usuarios', 'web', '2025-11-05 15:20:02', '2025-11-05 15:20:02'),
(5, 'ver puestos', 'web', '2025-11-05 15:20:02', '2025-11-05 15:20:02'),
(6, 'crear puestos', 'web', '2025-11-05 15:20:02', '2025-11-05 15:20:02'),
(7, 'editar puestos', 'web', '2025-11-05 15:20:02', '2025-11-05 15:20:02'),
(8, 'eliminar puestos', 'web', '2025-11-05 15:20:02', '2025-11-05 15:20:02'),
(9, 'agregar_bandera', 'web', '2025-11-05 15:20:05', '2025-11-05 15:20:05'),
(10, 'editar_bandera', 'web', '2025-11-05 15:20:05', '2025-11-05 15:20:05'),
(11, 'eliminar_bandera', 'web', '2025-11-05 15:20:05', '2025-11-05 15:20:05'),
(12, 'ver_bandera', 'web', '2025-11-05 15:20:05', '2025-11-05 15:20:05'),
(13, 'agregar_guardavida', 'web', '2025-11-05 15:20:05', '2025-11-05 15:20:05'),
(14, 'editar_guardavida', 'web', '2025-11-05 15:20:05', '2025-11-05 15:20:05'),
(15, 'eliminar_guardavida', 'web', '2025-11-05 15:20:05', '2025-11-05 15:20:05'),
(16, 'ver_guardavida', 'web', '2025-11-05 15:20:05', '2025-11-05 15:20:05'),
(17, 'agregar_intervencion', 'web', '2025-11-05 15:20:05', '2025-11-05 15:20:05'),
(18, 'editar_intervencion', 'web', '2025-11-05 15:20:05', '2025-11-05 15:20:05'),
(19, 'eliminar_intervencion', 'web', '2025-11-05 15:20:05', '2025-11-05 15:20:05'),
(20, 'ver_intervencion', 'web', '2025-11-05 15:20:05', '2025-11-05 15:20:05'),
(21, 'agregar_novedad_material', 'web', '2025-11-05 15:20:05', '2025-11-05 15:20:05'),
(22, 'editar_novedad_material', 'web', '2025-11-05 15:20:05', '2025-11-05 15:20:05'),
(23, 'eliminar_novedad_material', 'web', '2025-11-05 15:20:05', '2025-11-05 15:20:05'),
(24, 'ver_novedad_material', 'web', '2025-11-05 15:20:05', '2025-11-05 15:20:05'),
(25, 'agregar_cambio_turno', 'web', '2025-11-05 15:20:05', '2025-11-05 15:20:05'),
(26, 'editar_cambio_turno', 'web', '2025-11-05 15:20:05', '2025-11-05 15:20:05'),
(27, 'eliminar_cambio_turno', 'web', '2025-11-05 15:20:05', '2025-11-05 15:20:05'),
(28, 'ver_cambio_turno', 'web', '2025-11-05 15:20:05', '2025-11-05 15:20:05'),
(29, 'agregar_licencia', 'web', '2025-11-05 15:20:05', '2025-11-05 15:20:05'),
(30, 'editar_licencia', 'web', '2025-11-05 15:20:05', '2025-11-05 15:20:05'),
(31, 'eliminar_licencia', 'web', '2025-11-05 15:20:05', '2025-11-05 15:20:05'),
(32, 'ver_licencia', 'web', '2025-11-05 15:20:05', '2025-11-05 15:20:05'),
(33, 'agregar_asistencia', 'web', '2025-11-05 15:20:05', '2025-11-05 15:20:05'),
(34, 'editar_asistencia', 'web', '2025-11-05 15:20:05', '2025-11-05 15:20:05'),
(35, 'eliminar_asistencia', 'web', '2025-11-05 15:20:05', '2025-11-05 15:20:05'),
(36, 'ver_asistencia', 'web', '2025-11-05 15:20:05', '2025-11-05 15:20:05'),
(37, 'ver_asistencia_propia', 'web', '2025-11-05 15:20:05', '2025-11-05 15:20:05'),
(38, 'abm_roles_y_permisos', 'web', '2025-11-05 15:20:05', '2025-11-05 15:20:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 2, 'sw-token', 'd8aa18af67f7a7c4b483ee370d2425ae545ebf2be75f4d9d35090de4446072dc', '[\"*\"]', NULL, NULL, '2025-11-05 15:20:18', '2025-11-05 15:20:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `playas`
--

CREATE TABLE `playas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `lat` decimal(10,6) NOT NULL,
  `lon` decimal(10,6) NOT NULL,
  `color` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `playas`
--

INSERT INTO `playas` (`id`, `nombre`, `lat`, `lon`, `color`, `created_at`, `updated_at`) VALUES
(1, 'Claromecó', -38.250000, -60.149900, 'cyan', NULL, NULL),
(2, 'Dunamar', -38.200000, -60.183300, 'fuchsia', NULL, NULL),
(3, 'Orense', -38.044400, -60.224400, 'purple', NULL, NULL),
(4, 'Reta', -38.110000, -60.288900, 'teal', NULL, NULL),
(5, 'Tres Arroyos Muni', -38.110000, -60.288900, 'teal', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `puestos`
--

CREATE TABLE `puestos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `latitud` decimal(10,6) NOT NULL,
  `longitud` decimal(10,6) NOT NULL,
  `qr_encriptado` text DEFAULT NULL,
  `playa_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `puestos`
--

INSERT INTO `puestos` (`id`, `nombre`, `latitud`, `longitud`, `qr_encriptado`, `playa_id`, `created_at`, `updated_at`) VALUES
(1, 'Samoa', -38.860599, -60.075412, 'eyJpdiI6InFOYkF2eG84TW9oNjlrUG1UUVh3cmc9PSIsInZhbHVlIjoiOGFCT1F2bW5WM21OL2NNVEgxMGpqcTJ1R3pTLzZMVXFlL2p6Z3M0YjBEc2trR2JYRm9BbnducG9jdUgzUjAwYUJNbitzbzVpK2JVdTBTUEtmWm0zUmJsZ1FYZFVnNFRJM08vVHE1bG0wQ1puaU5TQU9Hd01LV2pGZlVrL2V3cUNpU2s0cnNPZHREcjQzYlVCZkM5c21nPT0iLCJtYWMiOiJiZjgwMTkzNDIxYWQ2ZDIyZTc1ZmQzNGJjMmZhZTU2OWFkYTZlNTY4YmY1NWVkNzAxZDEzMWY2NmQ2ODdhNmY2IiwidGFnIjoiIn0%3D', 1, '2025-11-05 15:20:03', '2025-11-05 15:20:03'),
(2, 'Anexo', -38.860206, -60.074211, 'eyJpdiI6Ilh4S05YWW9xWFlJZHoyeExmUzQ4RkE9PSIsInZhbHVlIjoidWZDOFFmb2RpZWhHMTMrMkwrTWRMN0ZTQ1RZTWU5M0IzNWtjbnVLZzlDdVBUNVpISGpzK1NidXJsQ25UbjBZZ2lPUUd0d2VNbFBBWlZ6TFV2eUw5L0thMjVybTdIYjQzZFJYOWdyZ0pUbzJzd3JKSmluOXB5dUxYZ0tzaDl5Q1BsczlVK2F5M0t4azZTZ29WMjQzTFJnPT0iLCJtYWMiOiI5YTI3M2RhMTQyYmU0MDllZGY3ZjMyNDJiMDdlZTY3MzY1YzFjODlhZmQyZWE2ZmNhOTgzMWE3ZTkyNWEzOWRlIiwidGFnIjoiIn0%3D', 1, '2025-11-05 15:20:03', '2025-11-05 15:20:03'),
(3, 'Nahuel Epu', -38.859867, -60.071315, 'eyJpdiI6IlZCN0I3ZDFQYUsyS0wwWFBmUmRndWc9PSIsInZhbHVlIjoiRjNuNXRTZUNiRGYwWnFWRHQ3NUt3VkZQVzdJMVFVMmovNk5jZ3hrMVRIa1N1RFZpODNYZEdMdW1KWk0zV1VwK25TUkxvSmlEWWxZcHpWVDFqNS9Mc1hXUFVmbVlWNnFDRys4T1pvc2twZmQ0eGUrZG5lV1ZrL3pCcS85cHNSdlpIM1BmMmNMb3gwUk1iRlBNQm52NEVnPT0iLCJtYWMiOiI0N2UxMWM4MDJjZDkzNGE5MDgwNjVlZDI3OGRkZDQ1YTYwMmQ1ZGM1MGZiNjM1N2M4MjM4NDRmYzY3MmYwZWRlIiwidGFnIjoiIn0%3D', 1, '2025-11-05 15:20:03', '2025-11-05 15:20:03'),
(4, '37', -38.859512, -60.067039, 'eyJpdiI6Ing3N2NiNEkvSDRXY2tDbUxua05rSWc9PSIsInZhbHVlIjoiQWZYdTBNN1J4anlqRFBEejVtcXV6TjBqdkswTE00bXJRRXlSdC9wL1laOFNleWxwYVJzNmxZVTcra0FNallRWGhhU3Jya3RMRzZ1YlJoODV2NEVZODV6YVZIM3o4dTdYT2FIZ2lOTXJYVlVKc0ZDSTlkaG1nNWdNMkV4MFJrRllMa1pFWlFQakRjcTdWbWdlcVNERGpnPT0iLCJtYWMiOiJmNWE5YmRlN2E3ZTEyOGNiZmFkNTI4MTYwODJhMWEyZmQ1NmU4ZTk5NTljZmQ0NGY5YmQxOWJjOTk0NDI3YmMwIiwidGFnIjoiIn0%3D', 1, '2025-11-05 15:20:03', '2025-11-05 15:20:03'),
(5, 'Cazadores', -38.859794, -60.065235, 'eyJpdiI6IjYvdURpalhrYjk2NHUxK1hYS3JoTFE9PSIsInZhbHVlIjoiSEkyeW5nT0xvczZJMFZUeWhOU04yVys4Q2hHazNKVm1lUlJhcjZNYnF0Z3JaYmtFL2g5Mm9Ud1dUUXFrWW55WlBBdmhWdW05ZWlpTGpGdlZscDVTenJXRjJQcVVteDVkR1ZCMHZmVWtXSmM0WjRkaDZ2Z0VoMnJhY3RpRG4ybURaekdETGJGYzZkdXN3eU44VTAyeDR3PT0iLCJtYWMiOiI2OGRiOWVjZGI0MWFkYTgzNTI4YmQxZDQyOWZjOGZlOTlhOGUzNzgyYzE1Y2QwNzJkNmY0NDJjNDk1NDFkNmFkIiwidGFnIjoiIn0%3D', 1, '2025-11-05 15:20:03', '2025-11-05 15:20:03'),
(6, 'Plumas Verdes', -38.859539, -60.061256, 'eyJpdiI6Ind0SWM4TENDalIvdFpYb2k0N2VFa2c9PSIsInZhbHVlIjoiTGJwaEwzM0F5ZkNZR3hnc21aZExOSW9Oc1kvNlpncTh0RXpLUGxZS3dneU42OTg2QndFSGJFNXRla09YcmtkYmp3cnQ5T2V2dEpwbnNFRVJLRVVCL0ovK091WnFrbnMzb3hRbkVzYzlZMThKOVJuVlRxSXFxVG5GL2IrZ25idzMwS3BhWkRkbG9udWliYkhkNlhtSnVRPT0iLCJtYWMiOiJmNGY4NzA4YTUzYjg0ZTQ2MjEwYzhiMWFiNDg0NDU0YzczZTAxYjI0NTZjYzU2Yzg0Y2Y3ZmZmMTRmMWM2MjVkIiwidGFnIjoiIn0%3D', 1, '2025-11-05 15:20:03', '2025-11-05 15:20:03'),
(7, 'Fuera zona de baño', -38.867000, -60.056500, 'eyJpdiI6InAzaDBXZDFzL2FkOXkrSS9ScHhoNVE9PSIsInZhbHVlIjoiSFk5a0VtRGpaRm5VMkFNbW9YUndVT09kSytWTkNPTXkyeHpaaVdJeEh2a2Y0MGc1T0F5RUwyZ1lsVVh5eERUNVVZSmtYRm9zWUxqd2RDZkIzTURsUkRrZVNkbDcvTHdTbUkrYngzdHR2bzIyUFpXVC91VmJVTzNhVHYrcUhBbTFjTkNrT3pCcHpmc2xtTGgvU2hjSGpWT2dsTHBHd3NvUHZka0FZSmxXN1I0PSIsIm1hYyI6IjRmOWIzZTFkMTE3MGY2ZWQwMTJhYTMzNDk1MjUxOWVkOTg1MDY3YjZmNTgwNzdkMDViMWYwM2UyNGE4YmMyZGMiLCJ0YWciOiIifQ%3D%3D', 1, '2025-11-05 15:20:03', '2025-11-05 15:20:03'),
(8, 'Kuyem', -38.861782, -60.086318, 'eyJpdiI6IkpoSjljY2lpMThWS1hFb0JBMC9XR1E9PSIsInZhbHVlIjoiZnpoSHdGM2RSTVdBbzdnQWFWYVpjSFowZjF0UWpsYjI1L2hHUWU5UGpBOWppN2dEM2RONmVnRDdxb21mSEZVYVlsRUFDanpLRWlQb1hiU21WNGI4M0xXZGZ0T25GNVdtNG9pNDNQa1B0TXBHMms3Mjgxamh6cUJJRHlyejlRSnllWXhmZ0xXRnEzVFpCL1h0RHZrd0RnPT0iLCJtYWMiOiJhNjA4MzU1N2NmNjkxNTQzODZjMTZiNzU1M2E3Yzg3Mzk5YmJjZmU4YWE0NGE3MjU2OTIxYWEwODZmNjY0NWRmIiwidGFnIjoiIn0%3D', 2, '2025-11-05 15:20:04', '2025-11-05 15:20:04'),
(9, 'Fuera zona de baño', -38.860000, -60.090000, 'eyJpdiI6IkpSTGQra0h6WDMzZlV6VWFmSXpKVXc9PSIsInZhbHVlIjoiU1RoS09kTE4yN050YUZ0cHh4Q014K25KWms2bGVOMTFUc1N2L29LQUJCaXJVNGdpZlphZ05ESGdMTGU5NEJNMTdDR21CMFUyOGRkbC9rUUNGcmVGTlpEUGE0QXh0c0kxS0lIN0tHWmR2d0pJajhyWE9oNXpWOUtrZHU4aEpTRkYrSGcya1JzSGJMRCt1b2JiVFJPR2xBPT0iLCJtYWMiOiJiYzQwZDU5NjU2NDM4ZWJmMjI3ZDE0YWMwNDNhYTU0M2U3ZDM1ZjdmMjg0MzgxYTg0YjA1MDMyM2JjZWQ5ZTQ0IiwidGFnIjoiIn0%3D', 2, '2025-11-05 15:20:04', '2025-11-05 15:20:04'),
(10, 'Califa', -38.806690, -59.729438, 'eyJpdiI6IkxRT2pSSVBnSk5RaTF1UEVkY0RRUEE9PSIsInZhbHVlIjoiUnNWNG1kb1lYTUltL3FxcytaVG1rY3BoOHBDRlQ2YkNhcmErR1VnaXAzODVESzZNYmdkTWNCcHpNVGcvd0NyRloyYTV5SFh5Uk1ZSXZkZGMyR2Vqblpta2tESk1vaWpWcnU4REdMR0xBNXVHd0NqamwwSFkxNkIwMVFpR2dYYWRoT01QR0dkZDlIMkJPR21tZ3RpWDBBPT0iLCJtYWMiOiIyMzU3ZTQ3MzFhMDBiY2M3ZGE5ZjhmYjBjYThlYTA2YzM4Mjk1MTFkODZmYzU0ZjllMTEyNWRjZDMxZmUwMzk5IiwidGFnIjoiIn0%3D', 3, '2025-11-05 15:20:04', '2025-11-05 15:20:04'),
(11, 'Refugio', -38.807643, -59.731575, 'eyJpdiI6IlI1S20xaFFSWVRmWkt5b2NiTGFCanc9PSIsInZhbHVlIjoiaE9BcFRMMTVZajNXNlNzWWlvYXhwSDVMSVZ5UVRRUm9BaFJta3NKb1orYXE1L1NLNnZyaWFiM0EzQitwSFZDTGFocXBwdTJ4cDMrU0NnQTBZT2E3THNXSmNGdlRMWXIwTHdOT05VUEVkMlNhdDNGRUcramVkZW1oSXZTUW1hUHA5NnRLRmJvWTJKc1lySEwyb2lFUGVnPT0iLCJtYWMiOiI1ZjE5MjEyOTU4YmU2OWE3NTE4NTkzZDljMTY1NWIwMGY2MTFiZTIxMGYzOGFiMjQ3ZjVhOTAwYmM3MTZjNWQyIiwidGFnIjoiIn0%3D', 3, '2025-11-05 15:20:04', '2025-11-05 15:20:04'),
(12, 'Virazon', -38.808155, -59.732990, 'eyJpdiI6InJIZFNlZjhCQVc1SFFCMXA0bVFjWmc9PSIsInZhbHVlIjoiREVlMXpVMFMzbVJ4L1czZ0J6Q0xYZE4vYWQzUENUWlBrSExzWEZHb1A3UzJOdkRNeGUweW44VkI4VE5wbEJ1aHRaa3hjYXB6UXh1N1k5MzdZSTVnZXNDRFMvcmFoNzhsR2lRVGdSWU9ld0gzQXB3K3h1TlZxem9ZNnVVaHY4VjNjWnd3bFAwR1NHdlNOK2lCZEZMLzh3PT0iLCJtYWMiOiI4MGU4ZmU2YTZlYzgwMjUyMmYzMWU4OWI0MDFmOTI4MzU4N2VmNzE1M2NkYmEwMzU4MmNjY2FiMmYyZjk0ZDYzIiwidGFnIjoiIn0%3D', 3, '2025-11-05 15:20:04', '2025-11-05 15:20:04'),
(13, 'Fuera zona de baño', -38.834500, -59.949500, 'eyJpdiI6Ilhhc29mb21sakRNamQ5RE0rSHBWV3c9PSIsInZhbHVlIjoiRXRQSTNJUXFLaUNjZ2RzbWc1YnRhbklNMTgyaFl5VTNDT2VpY1E1M0JtZTN4aWx2QUJyMHlTT3FxbnlnejI3S1k5SlpiWVZSR2pYWnJ3Z29BZzFSeTg4cmlGSTVSbmE3bWRhR3RCUjlBOWVpYWVDQ0VZcFh4QU53L2NkYWp2eWJMSXlTajd0dWt3ZmsvVG1qSUdQNXNUVWx2YVFjWmQ2V0VQOUl0WDEva2w4PSIsIm1hYyI6ImZlNGVkZTBmYTRhMGJkZjIxNzJjN2JkMDBhYzQ4ZDAzMTFmNWE1YTQ0OTA5MDdkOTI5M2E5ZGIxMDY0ZGY4MTYiLCJ0YWciOiIifQ%3D%3D', 3, '2025-11-05 15:20:04', '2025-11-05 15:20:04'),
(14, 'Tequila', -38.900833, -60.334167, 'eyJpdiI6ImdTeXhhNHZudmd0ZEJITGd2aWx3U3c9PSIsInZhbHVlIjoiSjVDNFk2ekpiMDFXZ2FEMVlkQUpoY0xuV2xMYnFaeUszdWptQzg2SXRZT2ZtcmhpdmdGQkllS2d0TkFjc0JiUHpSM21nRTFURS9zZDNyV2h6dzBGaFUrNVpmK0xoUms3VjNuWUZYOSs4aXdLTUxYZUViV05sVEhqem5lUGh6bVM5SXJuOW0wTDMxaWxRcjBXV25jMjBnPT0iLCJtYWMiOiJiMTkxMWYxMTE0YjAwMDlkOGRlYjYxYmM2ZDU4ZWMzOTliZDJiZjljZDdjMTczM2ZjYWY5MGRhNDk3MjEwYmU1IiwidGFnIjoiIn0%3D', 4, '2025-11-05 15:20:04', '2025-11-05 15:20:04'),
(15, 'La pausa', -38.901389, -60.336389, 'eyJpdiI6IlNGd1k0a2pXT0ZNUTNyYUxYTWlMcmc9PSIsInZhbHVlIjoibzAwZzlTK25EU2dhbytWWlZxV0U3Z0RZdTRWVUx0amNwSnFFd2tLM0NpYmdHUnkzazJWVnVZR0owOHlhS0hVckhVY1Rvcmk5czRuazErbzRtTXo0S0J5d09FZ1h3OXJ2alU2cnRnUndBMUJTa3RlZlMvcGk5UFluWTRVWUprTVdiUGdCalorSU9PSXpyWnl4cVBLQTNnPT0iLCJtYWMiOiI2MjlmYWMzNDk2OTEzMTBjZDliZDUyNmJlYTZkOTgyMDM4OTk0NGI1YzEyZDM5MDYyZWIzMjNiZjIwMWM0ZjM0IiwidGFnIjoiIn0%3D', 4, '2025-11-05 15:20:04', '2025-11-05 15:20:04'),
(16, 'Walters', -38.902222, -60.339722, 'eyJpdiI6IjhIUHEzdWhWaHV5bjFLcWRzWS9YQXc9PSIsInZhbHVlIjoiZzQvMWpqeHJTeDFXa2tTQlN6aEZoMGVyV1NXNjVBZ1RJb2JSTG1TSTQ1RzMrQURNWkd6cy9BYWFiZ1JwUWZ3QllURDhTd1FLRHFxc2szMGp4NURRWmVURFFxVDJQYmFKWDMzRElIaWxJVTJFMWVpb3pNYnh1SkdxTTZLVUcxREI0WUpYMlNjNTdDQnJjYWErNzl0TW9RPT0iLCJtYWMiOiIyNDM4ZDliODU4MzUyYWYwNWRiYWIwNDVlZDIxNzk1MjA4ZjUxNGFhYmQ2MTUxMmQwOTFlYjJlNTJhNmE3YWUzIiwidGFnIjoiIn0%3D', 4, '2025-11-05 15:20:04', '2025-11-05 15:20:04'),
(17, 'Moto', -38.902222, -60.340278, 'eyJpdiI6IkpqQUtKR1ZnN3Y1ekVObmxxVGZsSXc9PSIsInZhbHVlIjoiZitjUHplbW5RckRjZlNEUHYxanFzaW5ESlZ2L3RNb0RmOXpNRDdLQm10UGVXaDdlNlE1cXMvczNiamF1MGpsY3RSOWlVeUdNanIxVE94UE9pWHhxa1lZVmY2R0pzeXRiUlhMRWwyTVFwM3Nhc3BDVlpLVDN3SklxMTN1Rit0YWtoOVE5aHZCYmQ3cGFpQ1B2bmhqN1NRPT0iLCJtYWMiOiIxYjBiZDY4OTZiM2FjMjViMzExZjRkMGEzN2RmMWY1MmEzZWViYjhiNzcxZWUzYjMxZTUwY2E0MzRlNTczYmI4IiwidGFnIjoiIn0%3D', 4, '2025-11-05 15:20:04', '2025-11-05 15:20:04'),
(18, 'Rakos', -38.902778, -60.344167, 'eyJpdiI6Ik03ZXNtcU1CK3QrYXo3VWFCQnMybHc9PSIsInZhbHVlIjoieTN0ZkVtdlprLzlSRVMvdktocHVoTElaRkFKNjFnMFlVakV4Vm1OQnhuMWlwcTFkMXlkZ2hsa3ZVL1g1ZkJRM212VTc2b2NPTzlhanpGc1h5aFNOaXhXcW51ZHFPcHVOTlcvT1RJWlFHMkhCbnU3OVdPSVZRY09JMlp4UjJ5YXozS3FJa0dqM1VkMHVwcTNUejVIZ0RnPT0iLCJtYWMiOiI0NjJkODYzOWNiZTc3NjhkYjI3NTc4MjkwYTU4Yzg1NTA2NjhkNjI2YTgyNjc5ODFmNzZhN2ZlY2M5MzlkYzhmIiwidGFnIjoiIn0%3D', 4, '2025-11-05 15:20:05', '2025-11-05 15:20:05'),
(19, 'Cayasta', -38.900000, -60.330278, 'eyJpdiI6IjhoZnB2d2FTNlFYamc2cWZtTXlKbFE9PSIsInZhbHVlIjoicmVZdzRhUnV1ajZ6SUVTakZMNlFGcHFjeDNNZVJ1U2lTZUU0MTFRa0hGaldFTVZUOERVSlJycE4yTk03eTdObHpzZVJTN3BhazhUY3VhcVNMWG9VYmQ1Y3RHRUI1eVdLd0RIalJNNjRXU3luZzhUU09QSUxNdlBSSDFWNDRhbWovUU51THlBNmYxRExwRWNuZk5xbER3PT0iLCJtYWMiOiIxY2UxZTBkZmJiODc3MDY2NDRkNjE5NDBjNDc5NGQyNGIwZDA1MWUwOWM4YzY3MjEzOWQ2MDc2ZmM4ODNhYjRjIiwidGFnIjoiIn0%3D', 4, '2025-11-05 15:20:05', '2025-11-05 15:20:05'),
(20, 'Fuera zona de baño', -38.902500, -60.340500, 'eyJpdiI6ImlPYWhqYlZCSWM0eHhPU000OXBtQmc9PSIsInZhbHVlIjoiMnBwdmV6SG1FeWI2bFgxeEF5dmdRbUZZQ21zb3MrNFZ5bVFIVmpNUWpIK0JhLzY0QlJtTWhYMHRXdE9uK0E0S1ZPNEx4UWJLWVhiM1lLcHBjRUNDWTBidVY4Z0VkTHVub2xyN1hLYUNRdDVIaWdyaXAzRVVOM2VUZUE4SEtIUWZ6NFljYzZCT0RYa3d3WXdhM3VsRCswdHpHRmM0U01KMEhvdlc4WDlTMXpJPSIsIm1hYyI6ImMyZGI3MGQ5OTRjNjY0ZWY1Njk5ODQ2ZDdmMjkxNTA4ZTI1N2Q3ZjkzMzA4MzdmZTJhNDlmNzRkYWMzNDNmMWMiLCJ0YWciOiIifQ%3D%3D', 4, '2025-11-05 15:20:05', '2025-11-05 15:20:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'web', '2025-11-05 15:20:02', '2025-11-05 15:20:02'),
(2, 'guardavida', 'web', '2025-11-05 15:20:03', '2025-11-05 15:20:03'),
(3, 'encargado', 'web', '2025-11-05 15:20:03', '2025-11-05 15:20:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(1, 3),
(2, 1),
(2, 3),
(3, 1),
(3, 3),
(4, 1),
(4, 3),
(5, 1),
(5, 2),
(5, 3),
(6, 1),
(6, 3),
(7, 1),
(7, 3),
(8, 1),
(8, 3),
(9, 1),
(9, 2),
(9, 3),
(10, 1),
(10, 2),
(10, 3),
(11, 1),
(11, 2),
(11, 3),
(12, 1),
(12, 2),
(12, 3),
(13, 1),
(13, 3),
(14, 1),
(14, 3),
(15, 1),
(15, 3),
(16, 1),
(16, 3),
(17, 1),
(17, 3),
(18, 1),
(18, 3),
(19, 1),
(19, 3),
(20, 1),
(20, 3),
(21, 1),
(21, 3),
(22, 1),
(22, 3),
(23, 1),
(23, 3),
(24, 1),
(24, 3),
(25, 1),
(25, 3),
(26, 1),
(26, 3),
(27, 1),
(27, 3),
(28, 1),
(28, 3),
(29, 1),
(29, 3),
(30, 1),
(30, 3),
(31, 1),
(31, 3),
(32, 1),
(32, 3),
(33, 1),
(34, 1),
(35, 1),
(36, 1),
(36, 3),
(37, 1),
(37, 2),
(37, 3),
(38, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('g10hmfBGyHo2saygaB4dfR55blDu5mXQ9jZXUUYt', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiRHRvaFAycHJiaHk0SzNjcWwxeDhCZEsydUFCWUw5RVNJcjg3cThmYSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyOToiaHR0cDovL2d1YXJkYXZpZGFzLmxvY2FsL2hvbWUiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozNzoiaHR0cDovL2d1YXJkYXZpZGFzLmxvY2FsL2ludGVydmVuY2lvbiI7czo1OiJyb3V0ZSI7czoxODoiaW50ZXJ2ZW5jaW9uLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', 1762356180);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `lastname`, `email`, `enabled`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin', 'admin@admin.com', 1, '2025-11-05 15:20:02', '$2y$12$rBv85CrWbFtP8CcH2pIRdew345jD8f3NjVgRNfUamuVDPeqDDzNFq', NULL, '2025-11-05 15:20:02', '2025-11-05 15:20:02'),
(2, 'María', 'Gómez', 'guardavida@gmail.com', 1, '2025-11-05 15:20:02', '$2y$12$uH94ZIeKfr5jazXuemQab.yFjcGhs1HG7cqbrVAjEkwRsK0j2116m', NULL, '2025-11-05 15:20:02', '2025-11-05 15:20:02'),
(3, 'Carla Fernández', 'Carla Fernández', 'guardavida2@gmail.com', 1, '2025-11-05 15:20:02', '$2y$12$O0cAyjTxxMEyKVVYYCEJdOUGhSRhWXwSYne4kG/XEunw6.RKPa6W6', NULL, '2025-11-05 15:20:02', '2025-11-05 15:20:02'),
(4, 'Juan Pérez', 'Juan Pérez', 'guardavida3@gmail.com', 1, '2025-11-05 15:20:03', '$2y$12$zkhvz59mrCcgAtr6HqzHL.kqp/umwW9naYtkL2hqH7VuoQ4dXQiCy', NULL, '2025-11-05 15:20:03', '2025-11-05 15:20:03'),
(5, 'Valentina', 'Pérez', 'guardavidaTresArroyos@gmail.com', 1, '2025-11-05 15:20:03', '$2y$12$wCnUf8GHfRNO0F/gS6UHmuYVm7jPJoE4Dl9yeLcmRAFQFqvcmcI1e', NULL, '2025-11-05 15:20:03', '2025-11-05 15:20:03');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `asistencias`
--
ALTER TABLE `asistencias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asistencias_puesto_id_foreign` (`puesto_id`),
  ADD KEY `asistencias_guardavidas_id_foreign` (`guardavidas_id`);

--
-- Indices de la tabla `banderas`
--
ALTER TABLE `banderas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `banderas_bandera_id_foreign` (`bandera_id`),
  ADD KEY `banderas_playa_id_foreign` (`playa_id`),
  ADD KEY `banderas_puesto_id_foreign` (`puesto_id`),
  ADD KEY `banderas_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `bandera_tipos`
--
ALTER TABLE `bandera_tipos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `cambio_de_turnos`
--
ALTER TABLE `cambio_de_turnos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cambio_de_turnos_guardavida_id_foreign` (`guardavida_id`),
  ADD KEY `cambio_de_turnos_playa_id_foreign` (`playa_id`),
  ADD KEY `cambio_de_turnos_puesto_id_foreign` (`puesto_id`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `fuerzas`
--
ALTER TABLE `fuerzas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `fuerzas_intervenciones`
--
ALTER TABLE `fuerzas_intervenciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fuerzas_intervenciones_fuerza_id_foreign` (`fuerza_id`),
  ADD KEY `fuerzas_intervenciones_intervencion_id_foreign` (`intervencion_id`);

--
-- Indices de la tabla `guardavidas`
--
ALTER TABLE `guardavidas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guardavidas_playa_id_foreign` (`playa_id`),
  ADD KEY `guardavidas_puesto_id_foreign` (`puesto_id`),
  ADD KEY `guardavidas_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `guardavidas_intervenciones`
--
ALTER TABLE `guardavidas_intervenciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guardavidas_intervenciones_guardavida_id_foreign` (`guardavida_id`),
  ADD KEY `guardavidas_intervenciones_intervencion_id_foreign` (`intervencion_id`);

--
-- Indices de la tabla `intervencions`
--
ALTER TABLE `intervencions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `intervencions_bandera_id_foreign` (`bandera_id`),
  ADD KEY `intervencions_playa_id_foreign` (`playa_id`),
  ADD KEY `intervencions_puesto_id_foreign` (`puesto_id`),
  ADD KEY `intervencions_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indices de la tabla `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `licencias`
--
ALTER TABLE `licencias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `licencias_guardavida_id_foreign` (`guardavida_id`),
  ADD KEY `licencias_playa_id_foreign` (`playa_id`),
  ADD KEY `licencias_puesto_id_foreign` (`puesto_id`);

--
-- Indices de la tabla `licencia_tipos`
--
ALTER TABLE `licencia_tipos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `materiales`
--
ALTER TABLE `materiales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `materiales_playa_id_foreign` (`playa_id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indices de la tabla `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indices de la tabla `novedades`
--
ALTER TABLE `novedades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `novedades_playa_id_foreign` (`playa_id`);

--
-- Indices de la tabla `novedad_materials`
--
ALTER TABLE `novedad_materials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `novedad_materials_material_id_foreign` (`material_id`),
  ADD KEY `novedad_materials_playa_id_foreign` (`playa_id`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indices de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indices de la tabla `playas`
--
ALTER TABLE `playas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `puestos`
--
ALTER TABLE `puestos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `puestos_playa_id_foreign` (`playa_id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indices de la tabla `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `asistencias`
--
ALTER TABLE `asistencias`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `banderas`
--
ALTER TABLE `banderas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `bandera_tipos`
--
ALTER TABLE `bandera_tipos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `cambio_de_turnos`
--
ALTER TABLE `cambio_de_turnos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `fuerzas`
--
ALTER TABLE `fuerzas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `fuerzas_intervenciones`
--
ALTER TABLE `fuerzas_intervenciones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `guardavidas`
--
ALTER TABLE `guardavidas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `guardavidas_intervenciones`
--
ALTER TABLE `guardavidas_intervenciones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `intervencions`
--
ALTER TABLE `intervencions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `licencias`
--
ALTER TABLE `licencias`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `licencia_tipos`
--
ALTER TABLE `licencia_tipos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `materiales`
--
ALTER TABLE `materiales`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `novedades`
--
ALTER TABLE `novedades`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `novedad_materials`
--
ALTER TABLE `novedad_materials`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `playas`
--
ALTER TABLE `playas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `puestos`
--
ALTER TABLE `puestos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asistencias`
--
ALTER TABLE `asistencias`
  ADD CONSTRAINT `asistencias_guardavidas_id_foreign` FOREIGN KEY (`guardavidas_id`) REFERENCES `guardavidas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `asistencias_puesto_id_foreign` FOREIGN KEY (`puesto_id`) REFERENCES `puestos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `banderas`
--
ALTER TABLE `banderas`
  ADD CONSTRAINT `banderas_bandera_id_foreign` FOREIGN KEY (`bandera_id`) REFERENCES `bandera_tipos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `banderas_playa_id_foreign` FOREIGN KEY (`playa_id`) REFERENCES `playas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `banderas_puesto_id_foreign` FOREIGN KEY (`puesto_id`) REFERENCES `puestos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `banderas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cambio_de_turnos`
--
ALTER TABLE `cambio_de_turnos`
  ADD CONSTRAINT `cambio_de_turnos_guardavida_id_foreign` FOREIGN KEY (`guardavida_id`) REFERENCES `guardavidas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cambio_de_turnos_playa_id_foreign` FOREIGN KEY (`playa_id`) REFERENCES `playas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cambio_de_turnos_puesto_id_foreign` FOREIGN KEY (`puesto_id`) REFERENCES `puestos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `fuerzas_intervenciones`
--
ALTER TABLE `fuerzas_intervenciones`
  ADD CONSTRAINT `fuerzas_intervenciones_fuerza_id_foreign` FOREIGN KEY (`fuerza_id`) REFERENCES `fuerzas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fuerzas_intervenciones_intervencion_id_foreign` FOREIGN KEY (`intervencion_id`) REFERENCES `intervencions` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `guardavidas`
--
ALTER TABLE `guardavidas`
  ADD CONSTRAINT `guardavidas_playa_id_foreign` FOREIGN KEY (`playa_id`) REFERENCES `playas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `guardavidas_puesto_id_foreign` FOREIGN KEY (`puesto_id`) REFERENCES `puestos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `guardavidas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `guardavidas_intervenciones`
--
ALTER TABLE `guardavidas_intervenciones`
  ADD CONSTRAINT `guardavidas_intervenciones_guardavida_id_foreign` FOREIGN KEY (`guardavida_id`) REFERENCES `guardavidas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `guardavidas_intervenciones_intervencion_id_foreign` FOREIGN KEY (`intervencion_id`) REFERENCES `intervencions` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `intervencions`
--
ALTER TABLE `intervencions`
  ADD CONSTRAINT `intervencions_bandera_id_foreign` FOREIGN KEY (`bandera_id`) REFERENCES `banderas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `intervencions_playa_id_foreign` FOREIGN KEY (`playa_id`) REFERENCES `playas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `intervencions_puesto_id_foreign` FOREIGN KEY (`puesto_id`) REFERENCES `puestos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `intervencions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `licencias`
--
ALTER TABLE `licencias`
  ADD CONSTRAINT `licencias_guardavida_id_foreign` FOREIGN KEY (`guardavida_id`) REFERENCES `guardavidas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `licencias_playa_id_foreign` FOREIGN KEY (`playa_id`) REFERENCES `playas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `licencias_puesto_id_foreign` FOREIGN KEY (`puesto_id`) REFERENCES `puestos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `materiales`
--
ALTER TABLE `materiales`
  ADD CONSTRAINT `materiales_playa_id_foreign` FOREIGN KEY (`playa_id`) REFERENCES `playas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `novedades`
--
ALTER TABLE `novedades`
  ADD CONSTRAINT `novedades_playa_id_foreign` FOREIGN KEY (`playa_id`) REFERENCES `playas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `novedad_materials`
--
ALTER TABLE `novedad_materials`
  ADD CONSTRAINT `novedad_materials_material_id_foreign` FOREIGN KEY (`material_id`) REFERENCES `materiales` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `novedad_materials_playa_id_foreign` FOREIGN KEY (`playa_id`) REFERENCES `playas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `puestos`
--
ALTER TABLE `puestos`
  ADD CONSTRAINT `puestos_playa_id_foreign` FOREIGN KEY (`playa_id`) REFERENCES `playas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
