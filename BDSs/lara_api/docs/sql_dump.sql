SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


INSERT INTO `catalog_categories` (`id`, `title`, `created_at`, `updated_at`) VALUES
(1, 'cars', NULL, '2019-04-06 19:27:15'),
(2, 'clothes', NULL, NULL),
(3, 'trucks', NULL, NULL);

INSERT INTO `catalog_category_product` (`id`, `category_id`, `product_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, NULL),
(2, 1, 3, NULL, NULL),
(3, 2, 2, NULL, NULL),
(4, 2, 4, NULL, NULL),
(5, 1, 5, NULL, NULL),
(6, 1, 6, NULL, NULL),
(7, 3, 3, NULL, NULL),
(8, 3, 5, NULL, NULL),
(27, 3, 10, NULL, NULL),
(28, 3, 14, NULL, NULL);

INSERT INTO `catalog_products` (`id`, `name`, `price`, `created_at`, `updated_at`) VALUES
(1, 'bmwwww', 44444.44, NULL, '2019-04-07 15:09:59'),
(2, 'hat', 2.00, NULL, NULL),
(3, 'mercedes', 5000.00, NULL, NULL),
(4, 'dress', 10.00, NULL, NULL),
(5, 'volvo', 2000.00, NULL, NULL),
(6, 'audi', 5000.00, NULL, NULL),
(10, '13', 13.00, NULL, NULL),
(11, 'super name', 44.66, '2019-04-07 14:25:27', '2019-04-07 14:25:27'),
(12, 'super name', 44.66, '2019-04-07 14:25:31', '2019-04-07 14:25:31'),
(13, 'super name', 44.66, '2019-04-07 14:30:45', '2019-04-07 14:30:45'),
(14, 'attached name', 55.77, '2019-04-07 14:32:22', '2019-04-07 14:32:22');

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `api_token`, `created_at`, `updated_at`) VALUES
(1, 'jack', 'seugenev@gmail.com', '2019-04-04 00:00:00', '$2y$10$AlmKGZ7Oh/liatULV3WoTONsSBlqDeojsBRo0Zxmfum/Hx592HLgC', NULL, 'ed44cb534e9662b51876b44d43a2078b', NULL, NULL),
(2, 'jack1', 'sdfds@sdfsd.com', NULL, '$2y$10$zErpPgwQ2ihasO1wtE1UXuIGI25TNMabKZm3k8to1SLt64cwOlg8O', NULL, '5fbe7d734806af062a77c29216829671', '2019-04-07 15:57:46', '2019-04-07 15:57:46'),
(3, 'Vasia Ivanov', 'sdfds1@sdfsd.com', NULL, '$2y$10$2N/aBSUbtexWVfSTD1KvdO6fuI.TsdZsHaYl8dO9zwFeX3b21HrkK', NULL, '11eed3e5b52a966f9a3ff22548639d4a', '2019-04-07 19:11:32', '2019-04-07 19:11:32');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
