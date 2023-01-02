-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 02, 2023 at 04:05 PM
-- Server version: 5.7.40
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jupitertouchlab_volantworld2`
--

-- --------------------------------------------------------

--
-- Table structure for table `activations`
--

CREATE TABLE `activations` (
  `id` int(10) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `completed` tinyint(1) NOT NULL DEFAULT '0',
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activations`
--

INSERT INTO `activations` (`id`, `user_id`, `code`, `completed`, `completed_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'BmFTUQA57zF832HyXbiv30VexUQ1bAG8', 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `banner`
--

CREATE TABLE `banner` (
  `id` int(11) NOT NULL,
  `Image` varchar(500) DEFAULT NULL,
  `position` varchar(500) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `title` varchar(250) DEFAULT NULL,
  `subtitle` varchar(250) DEFAULT NULL,
  `subcategory` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `banner`
--

INSERT INTO `banner` (`id`, `Image`, `position`, `created_at`, `updated_at`, `title`, `subtitle`, `subcategory`) VALUES
(1, 'demo.jpg', '1', '2019-10-09 12:41:24', '2020-04-19 06:19:55', 'SHOP', 'SHOP', 0),
(2, 'demo1.jpg', '2', '2019-10-09 12:41:24', '2020-04-19 06:19:55', 'SHOP', 'SHOP', 0),
(3, 'demo1.jpg', '3', '2019-10-09 12:41:24', '2020-04-19 06:19:55', 'SHOP', 'SHOP', 0);

-- --------------------------------------------------------

--
-- Table structure for table `brand`
--

CREATE TABLE `brand` (
  `id` int(11) NOT NULL,
  `brand_name` varchar(250) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `is_active` enum('0','1') NOT NULL DEFAULT '1' COMMENT '1=>is active,0=>not active',
  `is_delete` enum('0','1') NOT NULL DEFAULT '0' COMMENT '1=>is delete,0=>not delete',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `brand`
--

INSERT INTO `brand` (`id`, `brand_name`, `category_id`, `is_active`, `is_delete`, `created_at`, `updated_at`) VALUES
(1, 'brand-test', 2, '1', '0', '2022-12-30 12:21:00', '2022-12-30 12:21:00'),
(2, 'brand-test2', 3, '1', '0', '2022-12-30 12:21:36', '2022-12-30 12:21:36');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `name` varchar(250) DEFAULT NULL,
  `image` varchar(100) DEFAULT NULL,
  `parent_category` int(11) NOT NULL DEFAULT '0',
  `is_active` enum('0','1') NOT NULL DEFAULT '1' COMMENT '0=>not active,1=>active',
  `is_delete` enum('0','1') NOT NULL DEFAULT '0' COMMENT '0=>not delete,1=>delete',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `name`, `image`, `parent_category`, `is_active`, `is_delete`, `created_at`, `updated_at`) VALUES
(1, 'test', 'aetY83p7ap1672402632.jpeg', 0, '1', '0', '2022-12-30 12:17:12', '2022-12-30 12:17:12'),
(2, 'test1', NULL, 1, '1', '0', '2022-12-30 12:17:38', '2022-12-30 12:17:38'),
(3, 'test2', NULL, 1, '1', '0', '2022-12-30 12:17:50', '2022-12-30 12:17:50');

-- --------------------------------------------------------

--
-- Table structure for table `complain`
--

CREATE TABLE `complain` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `email` varchar(250) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `report_error` varchar(250) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `contact_us`
--

CREATE TABLE `contact_us` (
  `id` int(11) NOT NULL,
  `name` varchar(150) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(100) DEFAULT NULL,
  `subject` varchar(500) DEFAULT NULL,
  `message` varchar(5000) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

CREATE TABLE `country` (
  `id` int(11) NOT NULL,
  `iso` char(2) NOT NULL,
  `name` varchar(80) NOT NULL,
  `nicename` varchar(80) NOT NULL,
  `iso3` char(3) DEFAULT NULL,
  `numcode` smallint(6) DEFAULT NULL,
  `phonecode` int(5) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `country`
--

INSERT INTO `country` (`id`, `iso`, `name`, `nicename`, `iso3`, `numcode`, `phonecode`) VALUES
(1, 'AF', 'AFGHANISTAN', 'Afghanistan', 'AFG', 4, 93),
(2, 'AL', 'ALBANIA', 'Albania', 'ALB', 8, 355),
(3, 'DZ', 'ALGERIA', 'Algeria', 'DZA', 12, 213),
(4, 'AS', 'AMERICAN SAMOA', 'American Samoa', 'ASM', 16, 1684),
(5, 'AD', 'ANDORRA', 'Andorra', 'AND', 20, 376),
(6, 'AO', 'ANGOLA', 'Angola', 'AGO', 24, 244),
(7, 'AI', 'ANGUILLA', 'Anguilla', 'AIA', 660, 1264),
(8, 'AQ', 'ANTARCTICA', 'Antarctica', NULL, NULL, 0),
(9, 'AG', 'ANTIGUA AND BARBUDA', 'Antigua and Barbuda', 'ATG', 28, 1268),
(10, 'AR', 'ARGENTINA', 'Argentina', 'ARG', 32, 54),
(11, 'AM', 'ARMENIA', 'Armenia', 'ARM', 51, 374),
(12, 'AW', 'ARUBA', 'Aruba', 'ABW', 533, 297),
(13, 'AU', 'AUSTRALIA', 'Australia', 'AUS', 36, 61),
(14, 'AT', 'AUSTRIA', 'Austria', 'AUT', 40, 43),
(15, 'AZ', 'AZERBAIJAN', 'Azerbaijan', 'AZE', 31, 994),
(16, 'BS', 'BAHAMAS', 'Bahamas', 'BHS', 44, 1242),
(17, 'BH', 'BAHRAIN', 'Bahrain', 'BHR', 48, 973),
(18, 'BD', 'BANGLADESH', 'Bangladesh', 'BGD', 50, 880),
(19, 'BB', 'BARBADOS', 'Barbados', 'BRB', 52, 1246),
(20, 'BY', 'BELARUS', 'Belarus', 'BLR', 112, 375),
(21, 'BE', 'BELGIUM', 'Belgium', 'BEL', 56, 32),
(22, 'BZ', 'BELIZE', 'Belize', 'BLZ', 84, 501),
(23, 'BJ', 'BENIN', 'Benin', 'BEN', 204, 229),
(24, 'BM', 'BERMUDA', 'Bermuda', 'BMU', 60, 1441),
(25, 'BT', 'BHUTAN', 'Bhutan', 'BTN', 64, 975),
(26, 'BO', 'BOLIVIA', 'Bolivia', 'BOL', 68, 591),
(27, 'BA', 'BOSNIA AND HERZEGOVINA', 'Bosnia and Herzegovina', 'BIH', 70, 387),
(28, 'BW', 'BOTSWANA', 'Botswana', 'BWA', 72, 267),
(29, 'BV', 'BOUVET ISLAND', 'Bouvet Island', NULL, NULL, 0),
(30, 'BR', 'BRAZIL', 'Brazil', 'BRA', 76, 55),
(31, 'IO', 'BRITISH INDIAN OCEAN TERRITORY', 'British Indian Ocean Territory', NULL, NULL, 246),
(32, 'BN', 'BRUNEI DARUSSALAM', 'Brunei Darussalam', 'BRN', 96, 673),
(33, 'BG', 'BULGARIA', 'Bulgaria', 'BGR', 100, 359),
(34, 'BF', 'BURKINA FASO', 'Burkina Faso', 'BFA', 854, 226),
(35, 'BI', 'BURUNDI', 'Burundi', 'BDI', 108, 257),
(36, 'KH', 'CAMBODIA', 'Cambodia', 'KHM', 116, 855),
(37, 'CM', 'CAMEROON', 'Cameroon', 'CMR', 120, 237),
(38, 'CA', 'CANADA', 'Canada', 'CAN', 124, 1),
(39, 'CV', 'CAPE VERDE', 'Cape Verde', 'CPV', 132, 238),
(40, 'KY', 'CAYMAN ISLANDS', 'Cayman Islands', 'CYM', 136, 1345),
(41, 'CF', 'CENTRAL AFRICAN REPUBLIC', 'Central African Republic', 'CAF', 140, 236),
(42, 'TD', 'CHAD', 'Chad', 'TCD', 148, 235),
(43, 'CL', 'CHILE', 'Chile', 'CHL', 152, 56),
(44, 'CN', 'CHINA', 'China', 'CHN', 156, 86),
(45, 'CX', 'CHRISTMAS ISLAND', 'Christmas Island', NULL, NULL, 61),
(46, 'CC', 'COCOS (KEELING) ISLANDS', 'Cocos (Keeling) Islands', NULL, NULL, 672),
(47, 'CO', 'COLOMBIA', 'Colombia', 'COL', 170, 57),
(48, 'KM', 'COMOROS', 'Comoros', 'COM', 174, 269),
(49, 'CG', 'CONGO', 'Congo', 'COG', 178, 242),
(50, 'CD', 'CONGO, THE DEMOCRATIC REPUBLIC OF THE', 'Congo, the Democratic Republic of the', 'COD', 180, 242),
(51, 'CK', 'COOK ISLANDS', 'Cook Islands', 'COK', 184, 682),
(52, 'CR', 'COSTA RICA', 'Costa Rica', 'CRI', 188, 506),
(53, 'CI', 'COTE D\'IVOIRE', 'Cote D\'Ivoire', 'CIV', 384, 225),
(54, 'HR', 'CROATIA', 'Croatia', 'HRV', 191, 385),
(55, 'CU', 'CUBA', 'Cuba', 'CUB', 192, 53),
(56, 'CY', 'CYPRUS', 'Cyprus', 'CYP', 196, 357),
(57, 'CZ', 'CZECH REPUBLIC', 'Czech Republic', 'CZE', 203, 420),
(58, 'DK', 'DENMARK', 'Denmark', 'DNK', 208, 45),
(59, 'DJ', 'DJIBOUTI', 'Djibouti', 'DJI', 262, 253),
(60, 'DM', 'DOMINICA', 'Dominica', 'DMA', 212, 1767),
(61, 'DO', 'DOMINICAN REPUBLIC', 'Dominican Republic', 'DOM', 214, 1809),
(62, 'EC', 'ECUADOR', 'Ecuador', 'ECU', 218, 593),
(63, 'EG', 'EGYPT', 'Egypt', 'EGY', 818, 20),
(64, 'SV', 'EL SALVADOR', 'El Salvador', 'SLV', 222, 503),
(65, 'GQ', 'EQUATORIAL GUINEA', 'Equatorial Guinea', 'GNQ', 226, 240),
(66, 'ER', 'ERITREA', 'Eritrea', 'ERI', 232, 291),
(67, 'EE', 'ESTONIA', 'Estonia', 'EST', 233, 372),
(68, 'ET', 'ETHIOPIA', 'Ethiopia', 'ETH', 231, 251),
(69, 'FK', 'FALKLAND ISLANDS (MALVINAS)', 'Falkland Islands (Malvinas)', 'FLK', 238, 500),
(70, 'FO', 'FAROE ISLANDS', 'Faroe Islands', 'FRO', 234, 298),
(71, 'FJ', 'FIJI', 'Fiji', 'FJI', 242, 679),
(72, 'FI', 'FINLAND', 'Finland', 'FIN', 246, 358),
(73, 'FR', 'FRANCE', 'France', 'FRA', 250, 33),
(74, 'GF', 'FRENCH GUIANA', 'French Guiana', 'GUF', 254, 594),
(75, 'PF', 'FRENCH POLYNESIA', 'French Polynesia', 'PYF', 258, 689),
(76, 'TF', 'FRENCH SOUTHERN TERRITORIES', 'French Southern Territories', NULL, NULL, 0),
(77, 'GA', 'GABON', 'Gabon', 'GAB', 266, 241),
(78, 'GM', 'GAMBIA', 'Gambia', 'GMB', 270, 220),
(79, 'GE', 'GEORGIA', 'Georgia', 'GEO', 268, 995),
(80, 'DE', 'GERMANY', 'Germany', 'DEU', 276, 49),
(81, 'GH', 'GHANA', 'Ghana', 'GHA', 288, 233),
(82, 'GI', 'GIBRALTAR', 'Gibraltar', 'GIB', 292, 350),
(83, 'GR', 'GREECE', 'Greece', 'GRC', 300, 30),
(84, 'GL', 'GREENLAND', 'Greenland', 'GRL', 304, 299),
(85, 'GD', 'GRENADA', 'Grenada', 'GRD', 308, 1473),
(86, 'GP', 'GUADELOUPE', 'Guadeloupe', 'GLP', 312, 590),
(87, 'GU', 'GUAM', 'Guam', 'GUM', 316, 1671),
(88, 'GT', 'GUATEMALA', 'Guatemala', 'GTM', 320, 502),
(89, 'GN', 'GUINEA', 'Guinea', 'GIN', 324, 224),
(90, 'GW', 'GUINEA-BISSAU', 'Guinea-Bissau', 'GNB', 624, 245),
(91, 'GY', 'GUYANA', 'Guyana', 'GUY', 328, 592),
(92, 'HT', 'HAITI', 'Haiti', 'HTI', 332, 509),
(93, 'HM', 'HEARD ISLAND AND MCDONALD ISLANDS', 'Heard Island and Mcdonald Islands', NULL, NULL, 0),
(94, 'VA', 'HOLY SEE (VATICAN CITY STATE)', 'Holy See (Vatican City State)', 'VAT', 336, 39),
(95, 'HN', 'HONDURAS', 'Honduras', 'HND', 340, 504),
(96, 'HK', 'HONG KONG', 'Hong Kong', 'HKG', 344, 852),
(97, 'HU', 'HUNGARY', 'Hungary', 'HUN', 348, 36),
(98, 'IS', 'ICELAND', 'Iceland', 'ISL', 352, 354),
(99, 'IN', 'INDIA', 'India', 'IND', 356, 91),
(100, 'ID', 'INDONESIA', 'Indonesia', 'IDN', 360, 62),
(101, 'IR', 'IRAN, ISLAMIC REPUBLIC OF', 'Iran, Islamic Republic of', 'IRN', 364, 98),
(102, 'IQ', 'IRAQ', 'Iraq', 'IRQ', 368, 964),
(103, 'IE', 'IRELAND', 'Ireland', 'IRL', 372, 353),
(104, 'IL', 'ISRAEL', 'Israel', 'ISR', 376, 972),
(105, 'IT', 'ITALY', 'Italy', 'ITA', 380, 39),
(106, 'JM', 'JAMAICA', 'Jamaica', 'JAM', 388, 1876),
(107, 'JP', 'JAPAN', 'Japan', 'JPN', 392, 81),
(108, 'JO', 'JORDAN', 'Jordan', 'JOR', 400, 962),
(109, 'KZ', 'KAZAKHSTAN', 'Kazakhstan', 'KAZ', 398, 7),
(110, 'KE', 'KENYA', 'Kenya', 'KEN', 404, 254),
(111, 'KI', 'KIRIBATI', 'Kiribati', 'KIR', 296, 686),
(112, 'KP', 'KOREA, DEMOCRATIC PEOPLE\'S REPUBLIC OF', 'Korea, Democratic People\'s Republic of', 'PRK', 408, 850),
(113, 'KR', 'KOREA, REPUBLIC OF', 'Korea, Republic of', 'KOR', 410, 82),
(114, 'KW', 'KUWAIT', 'Kuwait', 'KWT', 414, 965),
(115, 'KG', 'KYRGYZSTAN', 'Kyrgyzstan', 'KGZ', 417, 996),
(116, 'LA', 'LAO PEOPLE\'S DEMOCRATIC REPUBLIC', 'Lao People\'s Democratic Republic', 'LAO', 418, 856),
(117, 'LV', 'LATVIA', 'Latvia', 'LVA', 428, 371),
(118, 'LB', 'LEBANON', 'Lebanon', 'LBN', 422, 961),
(119, 'LS', 'LESOTHO', 'Lesotho', 'LSO', 426, 266),
(120, 'LR', 'LIBERIA', 'Liberia', 'LBR', 430, 231),
(121, 'LY', 'LIBYAN ARAB JAMAHIRIYA', 'Libyan Arab Jamahiriya', 'LBY', 434, 218),
(122, 'LI', 'LIECHTENSTEIN', 'Liechtenstein', 'LIE', 438, 423),
(123, 'LT', 'LITHUANIA', 'Lithuania', 'LTU', 440, 370),
(124, 'LU', 'LUXEMBOURG', 'Luxembourg', 'LUX', 442, 352),
(125, 'MO', 'MACAO', 'Macao', 'MAC', 446, 853),
(126, 'MK', 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF', 'Macedonia, the Former Yugoslav Republic of', 'MKD', 807, 389),
(127, 'MG', 'MADAGASCAR', 'Madagascar', 'MDG', 450, 261),
(128, 'MW', 'MALAWI', 'Malawi', 'MWI', 454, 265),
(129, 'MY', 'MALAYSIA', 'Malaysia', 'MYS', 458, 60),
(130, 'MV', 'MALDIVES', 'Maldives', 'MDV', 462, 960),
(131, 'ML', 'MALI', 'Mali', 'MLI', 466, 223),
(132, 'MT', 'MALTA', 'Malta', 'MLT', 470, 356),
(133, 'MH', 'MARSHALL ISLANDS', 'Marshall Islands', 'MHL', 584, 692),
(134, 'MQ', 'MARTINIQUE', 'Martinique', 'MTQ', 474, 596),
(135, 'MR', 'MAURITANIA', 'Mauritania', 'MRT', 478, 222),
(136, 'MU', 'MAURITIUS', 'Mauritius', 'MUS', 480, 230),
(137, 'YT', 'MAYOTTE', 'Mayotte', NULL, NULL, 269),
(138, 'MX', 'MEXICO', 'Mexico', 'MEX', 484, 52),
(139, 'FM', 'MICRONESIA, FEDERATED STATES OF', 'Micronesia, Federated States of', 'FSM', 583, 691),
(140, 'MD', 'MOLDOVA, REPUBLIC OF', 'Moldova, Republic of', 'MDA', 498, 373),
(141, 'MC', 'MONACO', 'Monaco', 'MCO', 492, 377),
(142, 'MN', 'MONGOLIA', 'Mongolia', 'MNG', 496, 976),
(143, 'MS', 'MONTSERRAT', 'Montserrat', 'MSR', 500, 1664),
(144, 'MA', 'MOROCCO', 'Morocco', 'MAR', 504, 212),
(145, 'MZ', 'MOZAMBIQUE', 'Mozambique', 'MOZ', 508, 258),
(146, 'MM', 'MYANMAR', 'Myanmar', 'MMR', 104, 95),
(147, 'NA', 'NAMIBIA', 'Namibia', 'NAM', 516, 264),
(148, 'NR', 'NAURU', 'Nauru', 'NRU', 520, 674),
(149, 'NP', 'NEPAL', 'Nepal', 'NPL', 524, 977),
(150, 'NL', 'NETHERLANDS', 'Netherlands', 'NLD', 528, 31),
(151, 'AN', 'NETHERLANDS ANTILLES', 'Netherlands Antilles', 'ANT', 530, 599),
(152, 'NC', 'NEW CALEDONIA', 'New Caledonia', 'NCL', 540, 687),
(153, 'NZ', 'NEW ZEALAND', 'New Zealand', 'NZL', 554, 64),
(154, 'NI', 'NICARAGUA', 'Nicaragua', 'NIC', 558, 505),
(155, 'NE', 'NIGER', 'Niger', 'NER', 562, 227),
(156, 'NG', 'NIGERIA', 'Nigeria', 'NGA', 566, 234),
(157, 'NU', 'NIUE', 'Niue', 'NIU', 570, 683),
(158, 'NF', 'NORFOLK ISLAND', 'Norfolk Island', 'NFK', 574, 672),
(159, 'MP', 'NORTHERN MARIANA ISLANDS', 'Northern Mariana Islands', 'MNP', 580, 1670),
(160, 'NO', 'NORWAY', 'Norway', 'NOR', 578, 47),
(161, 'OM', 'OMAN', 'Oman', 'OMN', 512, 968),
(162, 'PK', 'PAKISTAN', 'Pakistan', 'PAK', 586, 92),
(163, 'PW', 'PALAU', 'Palau', 'PLW', 585, 680),
(164, 'PS', 'PALESTINIAN TERRITORY, OCCUPIED', 'Palestinian Territory, Occupied', NULL, NULL, 970),
(165, 'PA', 'PANAMA', 'Panama', 'PAN', 591, 507),
(166, 'PG', 'PAPUA NEW GUINEA', 'Papua New Guinea', 'PNG', 598, 675),
(167, 'PY', 'PARAGUAY', 'Paraguay', 'PRY', 600, 595),
(168, 'PE', 'PERU', 'Peru', 'PER', 604, 51),
(169, 'PH', 'PHILIPPINES', 'Philippines', 'PHL', 608, 63),
(170, 'PN', 'PITCAIRN', 'Pitcairn', 'PCN', 612, 0),
(171, 'PL', 'POLAND', 'Poland', 'POL', 616, 48),
(172, 'PT', 'PORTUGAL', 'Portugal', 'PRT', 620, 351),
(173, 'PR', 'PUERTO RICO', 'Puerto Rico', 'PRI', 630, 1787),
(174, 'QA', 'QATAR', 'Qatar', 'QAT', 634, 974),
(175, 'RE', 'REUNION', 'Reunion', 'REU', 638, 262),
(176, 'RO', 'ROMANIA', 'Romania', 'ROM', 642, 40),
(177, 'RU', 'RUSSIAN FEDERATION', 'Russian Federation', 'RUS', 643, 70),
(178, 'RW', 'RWANDA', 'Rwanda', 'RWA', 646, 250),
(179, 'SH', 'SAINT HELENA', 'Saint Helena', 'SHN', 654, 290),
(180, 'KN', 'SAINT KITTS AND NEVIS', 'Saint Kitts and Nevis', 'KNA', 659, 1869),
(181, 'LC', 'SAINT LUCIA', 'Saint Lucia', 'LCA', 662, 1758),
(182, 'PM', 'SAINT PIERRE AND MIQUELON', 'Saint Pierre and Miquelon', 'SPM', 666, 508),
(183, 'VC', 'SAINT VINCENT AND THE GRENADINES', 'Saint Vincent and the Grenadines', 'VCT', 670, 1784),
(184, 'WS', 'SAMOA', 'Samoa', 'WSM', 882, 684),
(185, 'SM', 'SAN MARINO', 'San Marino', 'SMR', 674, 378),
(186, 'ST', 'SAO TOME AND PRINCIPE', 'Sao Tome and Principe', 'STP', 678, 239),
(187, 'SA', 'SAUDI ARABIA', 'Saudi Arabia', 'SAU', 682, 966),
(188, 'SN', 'SENEGAL', 'Senegal', 'SEN', 686, 221),
(189, 'CS', 'SERBIA AND MONTENEGRO', 'Serbia and Montenegro', NULL, NULL, 381),
(190, 'SC', 'SEYCHELLES', 'Seychelles', 'SYC', 690, 248),
(191, 'SL', 'SIERRA LEONE', 'Sierra Leone', 'SLE', 694, 232),
(192, 'SG', 'SINGAPORE', 'Singapore', 'SGP', 702, 65),
(193, 'SK', 'SLOVAKIA', 'Slovakia', 'SVK', 703, 421),
(194, 'SI', 'SLOVENIA', 'Slovenia', 'SVN', 705, 386),
(195, 'SB', 'SOLOMON ISLANDS', 'Solomon Islands', 'SLB', 90, 677),
(196, 'SO', 'SOMALIA', 'Somalia', 'SOM', 706, 252),
(197, 'ZA', 'SOUTH AFRICA', 'South Africa', 'ZAF', 710, 27),
(198, 'GS', 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS', 'South Georgia and the South Sandwich Islands', NULL, NULL, 0),
(199, 'ES', 'SPAIN', 'Spain', 'ESP', 724, 34),
(200, 'LK', 'SRI LANKA', 'Sri Lanka', 'LKA', 144, 94),
(201, 'SD', 'SUDAN', 'Sudan', 'SDN', 736, 249),
(202, 'SR', 'SURINAME', 'Suriname', 'SUR', 740, 597),
(203, 'SJ', 'SVALBARD AND JAN MAYEN', 'Svalbard and Jan Mayen', 'SJM', 744, 47),
(204, 'SZ', 'SWAZILAND', 'Swaziland', 'SWZ', 748, 268),
(205, 'SE', 'SWEDEN', 'Sweden', 'SWE', 752, 46),
(206, 'CH', 'SWITZERLAND', 'Switzerland', 'CHE', 756, 41),
(207, 'SY', 'SYRIAN ARAB REPUBLIC', 'Syrian Arab Republic', 'SYR', 760, 963),
(208, 'TW', 'TAIWAN, PROVINCE OF CHINA', 'Taiwan, Province of China', 'TWN', 158, 886),
(209, 'TJ', 'TAJIKISTAN', 'Tajikistan', 'TJK', 762, 992),
(210, 'TZ', 'TANZANIA, UNITED REPUBLIC OF', 'Tanzania, United Republic of', 'TZA', 834, 255),
(211, 'TH', 'THAILAND', 'Thailand', 'THA', 764, 66),
(212, 'TL', 'TIMOR-LESTE', 'Timor-Leste', NULL, NULL, 670),
(213, 'TG', 'TOGO', 'Togo', 'TGO', 768, 228),
(214, 'TK', 'TOKELAU', 'Tokelau', 'TKL', 772, 690),
(215, 'TO', 'TONGA', 'Tonga', 'TON', 776, 676),
(216, 'TT', 'TRINIDAD AND TOBAGO', 'Trinidad and Tobago', 'TTO', 780, 1868),
(217, 'TN', 'TUNISIA', 'Tunisia', 'TUN', 788, 216),
(218, 'TR', 'TURKEY', 'Turkey', 'TUR', 792, 90),
(219, 'TM', 'TURKMENISTAN', 'Turkmenistan', 'TKM', 795, 7370),
(220, 'TC', 'TURKS AND CAICOS ISLANDS', 'Turks and Caicos Islands', 'TCA', 796, 1649),
(221, 'TV', 'TUVALU', 'Tuvalu', 'TUV', 798, 688),
(222, 'UG', 'UGANDA', 'Uganda', 'UGA', 800, 256),
(223, 'UA', 'UKRAINE', 'Ukraine', 'UKR', 804, 380),
(224, 'AE', 'UNITED ARAB EMIRATES', 'United Arab Emirates', 'ARE', 784, 971),
(225, 'GB', 'UNITED KINGDOM', 'United Kingdom', 'GBR', 826, 44),
(226, 'US', 'UNITED STATES', 'United States', 'USA', 840, 1),
(227, 'UM', 'UNITED STATES MINOR OUTLYING ISLANDS', 'United States Minor Outlying Islands', NULL, NULL, 1),
(228, 'UY', 'URUGUAY', 'Uruguay', 'URY', 858, 598),
(229, 'UZ', 'UZBEKISTAN', 'Uzbekistan', 'UZB', 860, 998),
(230, 'VU', 'VANUATU', 'Vanuatu', 'VUT', 548, 678),
(231, 'VE', 'VENEZUELA', 'Venezuela', 'VEN', 862, 58),
(232, 'VN', 'VIET NAM', 'Viet Nam', 'VNM', 704, 84),
(233, 'VG', 'VIRGIN ISLANDS, BRITISH', 'Virgin Islands, British', 'VGB', 92, 1284),
(234, 'VI', 'VIRGIN ISLANDS, U.S.', 'Virgin Islands, U.s.', 'VIR', 850, 1340),
(235, 'WF', 'WALLIS AND FUTUNA', 'Wallis and Futuna', 'WLF', 876, 681),
(236, 'EH', 'WESTERN SAHARA', 'Western Sahara', 'ESH', 732, 212),
(237, 'YE', 'YEMEN', 'Yemen', 'YEM', 887, 967),
(238, 'ZM', 'ZAMBIA', 'Zambia', 'ZMB', 894, 260),
(239, 'ZW', 'ZIMBABWE', 'Zimbabwe', 'ZWE', 716, 263);

-- --------------------------------------------------------

--
-- Table structure for table `coupon`
--

CREATE TABLE `coupon` (
  `id` int(11) NOT NULL,
  `name` varchar(250) DEFAULT NULL,
  `code` varchar(250) DEFAULT NULL,
  `discount_type` enum('0','1') NOT NULL DEFAULT '0' COMMENT '0=>fixed,1=>percentage',
  `value` varchar(250) DEFAULT NULL,
  `free_shipping` enum('0','1') NOT NULL DEFAULT '0' COMMENT '0=>no,1=>yes',
  `start_date` varchar(250) DEFAULT NULL,
  `end_date` varchar(250) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '0' COMMENT '0=>disable,1=>enable',
  `coupon_on` enum('0','1') NOT NULL DEFAULT '0' COMMENT '0=>product,1=>category',
  `minmum_spend` varchar(250) DEFAULT NULL,
  `maximum_spend` varchar(250) DEFAULT NULL,
  `product` varchar(250) DEFAULT NULL,
  `categories` varchar(250) DEFAULT NULL,
  `usage_limit_per_coupon` int(11) DEFAULT '0',
  `usage_limit_per_customer` int(11) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` enum('0','1') NOT NULL DEFAULT '0' COMMENT '0=>not deleted,1=>deleted'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `coupon`
--

INSERT INTO `coupon` (`id`, `name`, `code`, `discount_type`, `value`, `free_shipping`, `start_date`, `end_date`, `status`, `coupon_on`, `minmum_spend`, `maximum_spend`, `product`, `categories`, `usage_limit_per_coupon`, `usage_limit_per_customer`, `created_at`, `updated_at`, `is_deleted`) VALUES
(1, 'test', '1212121', '1', '12', '1', 'December 29,2022', 'December 31,2022', '1', '0', '121', '111111', NULL, NULL, 11, 1, '2022-12-30 12:25:27', '2022-12-30 12:25:41', '0');

-- --------------------------------------------------------

--
-- Table structure for table `deals`
--

CREATE TABLE `deals` (
  `id` int(11) NOT NULL,
  `offer_id` int(11) NOT NULL,
  `offer_type` enum('1','2') NOT NULL DEFAULT '2' COMMENT '1=>big_deals,2=>normal',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_active` enum('0','1') NOT NULL DEFAULT '0' COMMENT '0=>not active,1=>active'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `deals`
--

INSERT INTO `deals` (`id`, `offer_id`, `offer_type`, `created_at`, `updated_at`, `is_active`) VALUES
(4, 0, '2', '2019-10-15 17:21:41', '2019-10-15 17:21:41', '0'),
(5, 0, '2', '2019-10-15 17:21:41', '2020-01-01 04:04:27', '0'),
(6, 0, '2', '2019-12-19 17:52:17', '2020-03-18 09:30:46', '0');

-- --------------------------------------------------------

--
-- Table structure for table `feature_product`
--

CREATE TABLE `feature_product` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `feature_product`
--

INSERT INTO `feature_product` (`id`, `product_id`, `created_at`, `updated_at`) VALUES
(1, 1, '2022-12-30 12:43:29', '2022-12-30 12:43:29'),
(2, 1, '2022-12-30 12:43:39', '2022-12-30 12:43:39');

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE `languages` (
  `id` int(10) NOT NULL,
  `name` char(49) CHARACTER SET utf8 DEFAULT NULL,
  `iso_639-1` char(2) CHARACTER SET utf8 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`id`, `name`, `iso_639-1`) VALUES
(1, 'English', 'en'),
(2, 'Afar', 'aa'),
(3, 'Abkhazian', 'ab'),
(4, 'Afrikaans', 'af'),
(5, 'Amharic', 'am'),
(6, 'Arabic', 'ar'),
(7, 'Assamese', 'as'),
(8, 'Aymara', 'ay'),
(9, 'Azerbaijani', 'az'),
(10, 'Bashkir', 'ba'),
(11, 'Belarusian', 'be'),
(12, 'Bulgarian', 'bg'),
(13, 'Bihari', 'bh'),
(14, 'Bislama', 'bi'),
(15, 'Bengali/Bangla', 'bn'),
(16, 'Tibetan', 'bo'),
(17, 'Breton', 'br'),
(18, 'Catalan', 'ca'),
(19, 'Corsican', 'co'),
(20, 'Czech', 'cs'),
(21, 'Welsh', 'cy'),
(22, 'Danish', 'da'),
(23, 'German', 'de'),
(24, 'Bhutani', 'dz'),
(25, 'Greek', 'el'),
(26, 'Esperanto', 'eo'),
(27, 'Spanish', 'es'),
(28, 'Estonian', 'et'),
(29, 'Basque', 'eu'),
(30, 'Persian', 'fa'),
(31, 'Finnish', 'fi'),
(32, 'Fiji', 'fj'),
(33, 'Faeroese', 'fo'),
(34, 'French', 'fr'),
(35, 'Frisian', 'fy'),
(36, 'Irish', 'ga'),
(37, 'Scots/Gaelic', 'gd'),
(38, 'Galician', 'gl'),
(39, 'Guarani', 'gn'),
(40, 'Gujarati', 'gu'),
(41, 'Hausa', 'ha'),
(42, 'Hindi', 'hi'),
(43, 'Croatian', 'hr'),
(44, 'Hungarian', 'hu'),
(45, 'Armenian', 'hy'),
(46, 'Interlingua', 'ia'),
(47, 'Interlingue', 'ie'),
(48, 'Inupiak', 'ik'),
(49, 'Indonesian', 'in'),
(50, 'Icelandic', 'is'),
(51, 'Italian', 'it'),
(52, 'Hebrew', 'iw'),
(53, 'Japanese', 'ja'),
(54, 'Yiddish', 'ji'),
(55, 'Javanese', 'jw'),
(56, 'Georgian', 'ka'),
(57, 'Kazakh', 'kk'),
(58, 'Greenlandic', 'kl'),
(59, 'Cambodian', 'km'),
(60, 'Kannada', 'kn'),
(61, 'Korean', 'ko'),
(62, 'Kashmiri', 'ks'),
(63, 'Kurdish', 'ku'),
(64, 'Kirghiz', 'ky'),
(65, 'Latin', 'la'),
(66, 'Lingala', 'ln'),
(67, 'Laothian', 'lo'),
(68, 'Lithuanian', 'lt'),
(69, 'Latvian/Lettish', 'lv'),
(70, 'Malagasy', 'mg'),
(71, 'Maori', 'mi'),
(72, 'Macedonian', 'mk'),
(73, 'Malayalam', 'ml'),
(74, 'Mongolian', 'mn'),
(75, 'Moldavian', 'mo'),
(76, 'Marathi', 'mr'),
(77, 'Malay', 'ms'),
(78, 'Maltese', 'mt'),
(79, 'Burmese', 'my'),
(80, 'Nauru', 'na'),
(81, 'Nepali', 'ne'),
(82, 'Dutch', 'nl'),
(83, 'Norwegian', 'no'),
(84, 'Occitan', 'oc'),
(85, '(Afan)/Oromoor/Oriya', 'om'),
(86, 'Punjabi', 'pa'),
(87, 'Polish', 'pl'),
(88, 'Pashto/Pushto', 'ps'),
(89, 'Portuguese', 'pt'),
(90, 'Quechua', 'qu'),
(91, 'Rhaeto-Romance', 'rm'),
(92, 'Kirundi', 'rn'),
(93, 'Romanian', 'ro'),
(94, 'Russian', 'ru'),
(95, 'Kinyarwanda', 'rw'),
(96, 'Sanskrit', 'sa'),
(97, 'Sindhi', 'sd'),
(98, 'Sangro', 'sg'),
(99, 'Serbo-Croatian', 'sh'),
(100, 'Singhalese', 'si'),
(101, 'Slovak', 'sk'),
(102, 'Slovenian', 'sl'),
(103, 'Samoan', 'sm'),
(104, 'Shona', 'sn'),
(105, 'Somali', 'so'),
(106, 'Albanian', 'sq'),
(107, 'Serbian', 'sr'),
(108, 'Siswati', 'ss'),
(109, 'Sesotho', 'st'),
(110, 'Sundanese', 'su'),
(111, 'Swedish', 'sv'),
(112, 'Swahili', 'sw'),
(113, 'Tamil', 'ta'),
(114, 'Telugu', 'te'),
(115, 'Tajik', 'tg'),
(116, 'Thai', 'th'),
(117, 'Tigrinya', 'ti'),
(118, 'Turkmen', 'tk'),
(119, 'Tagalog', 'tl'),
(120, 'Setswana', 'tn'),
(121, 'Tonga', 'to'),
(122, 'Turkish', 'tr'),
(123, 'Tsonga', 'ts'),
(124, 'Tatar', 'tt'),
(125, 'Twi', 'tw'),
(126, 'Ukrainian', 'uk'),
(127, 'Urdu', 'ur'),
(128, 'Uzbek', 'uz'),
(129, 'Vietnamese', 'vi'),
(130, 'Volapuk', 'vo'),
(131, 'Wolof', 'wo'),
(132, 'Xhosa', 'xh'),
(133, 'Yoruba', 'yo'),
(134, 'Chinese', 'zh'),
(135, 'Zulu', 'zu');

-- --------------------------------------------------------

--
-- Table structure for table `ltm_translations`
--

CREATE TABLE `ltm_translations` (
  `id` bigint(20) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `locale` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `key` text COLLATE utf8mb4_bin NOT NULL,
  `value` text COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `ltm_translations`
--

INSERT INTO `ltm_translations` (`id`, `status`, `locale`, `group`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 0, 'en', 'auth', 'failed', 'These credentials do not match our.', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(2, 0, 'en', 'auth', 'throttle', 'Too many login attempts. Please try again in :seconds seconds.', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(3, 0, 'en', 'messages', 'site_name', 'Shop', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(4, 0, 'en', 'messages', 'site_color', '#f07f13', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(5, 0, 'en', 'messages', 'colorid', '1', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(6, 0, 'en', 'messages', 'short_code', 'SH', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(7, 0, 'en', 'messages', 'metadescweb', 'Online Selling product', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(8, 0, 'en', 'messages', 'metakeyboard', 'Online Shopping', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(9, 0, 'en', 'messages', 'dashboard', 'Dashboard', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(10, 0, 'en', 'messages', 'product', 'Products', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(11, 0, 'en', 'messages', 'category', 'Categories', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(12, 0, 'en', 'messages', 'catalog', 'Product', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(13, 0, 'en', 'messages', 'option', 'Options', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(14, 0, 'en', 'messages', 'attributeset', 'Attribute Sets', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(15, 0, 'en', 'messages', 'attribute', 'Attributes', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(16, 0, 'en', 'messages', 'review', 'Reviews', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(17, 0, 'en', 'messages', 'sales', 'Sales', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(18, 0, 'en', 'messages', 'orders', 'Orders', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(19, 0, 'en', 'messages', 'transaction', 'Transactions', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(20, 0, 'en', 'messages', 'offers', 'Offers', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(21, 0, 'en', 'messages', 'big_offer', 'Big Offer', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(22, 0, 'en', 'messages', 'normal_offer', 'Normal Offer', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(23, 0, 'en', 'messages', 'sensonal_offer', 'Sensonal Offer', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(24, 0, 'en', 'messages', 'current_offer', 'Current Offer', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(25, 0, 'en', 'messages', 'special_category', 'Special Category', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(26, 0, 'en', 'messages', 'coupon', 'Coupon', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(27, 0, 'en', 'messages', 'users', 'Users', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(28, 0, 'en', 'messages', 'admin', 'Admin', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(29, 0, 'en', 'messages', 'role', 'Role', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(30, 0, 'en', 'messages', 'site_setting', 'Site Setting', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(31, 0, 'en', 'messages', 'setting', 'Setting', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(32, 0, 'en', 'messages', 'page', 'Pages', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(33, 0, 'en', 'messages', 'Banner', 'Banner', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(34, 0, 'en', 'messages', 'localization', 'Localization', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(35, 0, 'en', 'messages', 'translation', 'Translations', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(36, 0, 'en', 'messages', 'taxes', 'Taxes', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(37, 0, 'en', 'messages', 'report', 'Report', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(38, 0, 'en', 'messages', 'my_profile', 'My Profile', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(39, 0, 'en', 'messages', 'change_pwd', 'Change Password', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(40, 0, 'en', 'messages', 'logout', 'Logout', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(41, 0, 'en', 'messages', 'banner', 'Banner', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(42, 0, 'en', 'messages', 'email', 'Email', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(43, 0, 'en', 'messages', 'password', 'Password', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(44, 0, 'en', 'messages', 'sign_in', 'Sign in', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(45, 0, 'en', 'messages', 'total_sale', 'Total Sales', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(46, 0, 'en', 'messages', 'total_order', 'Total Orders', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(47, 0, 'en', 'messages', 'total_product', 'Total Products', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(48, 0, 'en', 'messages', 'total_customers', 'Total Customers', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(49, 0, 'en', 'messages', 'order_id', 'Order ID', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(50, 0, 'en', 'messages', 'customer', 'Customer', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(51, 0, 'en', 'messages', 'status', 'Status', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(52, 0, 'en', 'messages', 'total', 'Total', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(53, 0, 'en', 'messages', 'latest_order', 'Lastest Orders', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(54, 0, 'en', 'messages', 'latest_review', 'Lastest Reviews', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(55, 0, 'en', 'messages', 'ratting', 'Ratting', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(56, 0, 'en', 'messages', 'ent_current_pwd', 'Enter Current Password', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(57, 0, 'en', 'messages', 'ent_new_pwd', 'Enter New Password', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(58, 0, 'en', 'messages', 're_enter_pwd_en', 'Re-enter New Password', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(59, 0, 'en', 'messages', 'update', 'Update', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(60, 0, 'en', 'messages', 'edit_profile', 'Edit Profile', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(61, 0, 'en', 'messages', 'first_name', 'First Name', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(62, 0, 'en', 'messages', 'last_name', 'Last Name', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(63, 0, 'en', 'messages', 'profile_picture', 'Profile Picture', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(64, 0, 'en', 'messages', 'ban_upload_sec', 'Banner Upload Section', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(65, 0, 'en', 'messages', 'save', 'Save', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(66, 0, 'en', 'messages', 'cancel', 'Cancel', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(67, 0, 'en', 'messages', 'brands', 'Brands', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(68, 0, 'en', 'messages', 'add_brands', 'Add Brand', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(69, 0, 'en', 'messages', 'id', 'ID', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(70, 0, 'en', 'messages', 'name', 'Name', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(71, 0, 'en', 'messages', 'action', 'Action', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(72, 0, 'en', 'messages', 'brand_name', 'Brand Name', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(73, 0, 'en', 'messages', 'submit', 'Submit', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(74, 0, 'en', 'messages', 'edit_brand', 'Edit Brand', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(75, 0, 'en', 'messages', 'add_parent_category', 'Add Parent Category', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(76, 0, 'en', 'messages', 'add_category', 'Add Category', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(77, 0, 'en', 'messages', 'category_name', 'Category Name', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(78, 0, 'en', 'messages', 'edit_category', 'Edit Category', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(79, 0, 'en', 'messages', 'sub_category', 'Sub Categories', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(80, 0, 'en', 'messages', 'add_sub_category', 'Add Sub Category', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(81, 0, 'en', 'messages', 'sub_category_name', 'Sub Category Name', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(82, 0, 'en', 'messages', 'edit_sub_category', 'Edit Sub Category', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(83, 0, 'en', 'messages', 'add_coupon', 'Add Coupon', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(84, 0, 'en', 'messages', 'general', 'General', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(85, 0, 'en', 'messages', 'usage_res', 'Usage Restrictions', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(86, 0, 'en', 'messages', 'usage_limit', 'Usage Limits', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(87, 0, 'en', 'messages', 'discount_type', 'Discount Type', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(88, 0, 'en', 'messages', 'Fixed', 'Fixed', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(89, 0, 'en', 'messages', 'percentage', 'Percentage', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(90, 0, 'en', 'messages', 'value', 'Value', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(91, 0, 'en', 'messages', 'start_date', 'Start Date', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(92, 0, 'en', 'messages', 'end_date', 'End Date', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(93, 0, 'en', 'messages', 'code', 'Code', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(94, 0, 'en', 'messages', 'enable_the_coupon', 'Enable the Coupon', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(95, 0, 'en', 'messages', 'allow_free_shipping', 'Allow free shipping', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(96, 0, 'en', 'messages', 'next', 'Next', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(97, 0, 'en', 'messages', 'minmum_spend', 'Minimum Spend', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(98, 0, 'en', 'messages', 'maximum_spend', 'Maximum Spend', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(99, 0, 'en', 'messages', 'exclude_product', 'Exclude Product', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(100, 0, 'en', 'messages', 'exclude_category', 'Exclude Categories', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(101, 0, 'en', 'messages', 'usage_limit_per_coupon', 'Usage Limit Per Coupon', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(102, 0, 'en', 'messages', 'usage_limit_per_customer', 'Usage Limit Per Customer', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(103, 0, 'en', 'messages', 'finish', 'Finish', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(104, 0, 'en', 'messages', 'date', 'Date', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(105, 0, 'en', 'messages', 'add_tax', 'Add Tax', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(106, 0, 'en', 'messages', 'tax_name', 'Tax Name', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(107, 0, 'en', 'messages', 'based_on', 'Based On', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(108, 0, 'en', 'messages', 'billing_address', 'Billing Address', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(109, 0, 'en', 'messages', 'shipping_address', 'Shipping Address', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(110, 0, 'en', 'messages', 'rate', 'Rate', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(111, 0, 'en', 'messages', 'EditTax', 'Edit Tax', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(112, 0, 'en', 'messages', 'key', 'Key', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(113, 0, 'en', 'messages', 'seasonal_offer', 'Seasonal Offers', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(114, 0, 'en', 'messages', 'title', 'Title', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(115, 0, 'en', 'messages', 'cate_gory', 'Category', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(116, 0, 'en', 'messages', 'select_category', 'Select Category', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(117, 0, 'en', 'messages', 'fixed_from', 'Fixed From', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(118, 0, 'en', 'messages', 'fixed_to', ' Fixed To', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(119, 0, 'en', 'messages', 'add_sen', 'Add Sensonal Offer', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(120, 0, 'en', 'messages', 'current_deals', 'Current Deals', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(121, 0, 'en', 'messages', 'deals', 'Deals', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(122, 0, 'en', 'messages', 'add_big_offer', 'Add Big Offer', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(123, 0, 'en', 'messages', 'offer_on', 'Offer On', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(124, 0, 'en', 'messages', 'offer_price', 'Offer Price', '2022-12-30 06:56:57', '2022-12-30 07:13:49'),
(125, 0, 'en', 'messages', 'edit_deals', 'Edit Deals', '2022-12-30 06:56:58', '2022-12-30 07:13:49'),
(126, 0, 'en', 'messages', 'edit', 'Edit', '2022-12-30 06:56:58', '2022-12-30 07:13:49'),
(127, 0, 'en', 'messages', 'fixed_up_to', 'Fixed Up to', '2022-12-30 06:56:58', '2022-12-30 07:13:49'),
(128, 0, 'en', 'messages', 'select', 'Select', '2022-12-30 06:56:58', '2022-12-30 07:13:49'),
(129, 0, 'en', 'messages', 'MRP', 'MRP', '2022-12-30 06:56:58', '2022-12-30 07:13:49'),
(130, 0, 'en', 'messages', 'selling_price', 'Selling Price', '2022-12-30 06:56:58', '2022-12-30 07:13:49'),
(131, 0, 'en', 'messages', 'add', 'Add', '2022-12-30 06:56:58', '2022-12-30 07:13:49'),
(132, 0, 'en', 'messages', 'select_product', 'Select Product', '2022-12-30 06:56:58', '2022-12-30 07:13:49'),
(133, 0, 'en', 'messages', 'payment_method', 'Payment Method', '2022-12-30 06:56:58', '2022-12-30 07:13:49'),
(134, 0, 'en', 'messages', 'shipping_method', 'Shipping Method', '2022-12-30 06:56:58', '2022-12-30 07:13:49'),
(135, 0, 'en', 'messages', 'view_order', 'View Order', '2022-12-30 06:56:58', '2022-12-30 07:13:49'),
(136, 0, 'en', 'messages', 'account_info', 'Account Information', '2022-12-30 06:56:58', '2022-12-30 07:13:49'),
(137, 0, 'en', 'messages', 'order_info', 'Order Information', '2022-12-30 06:56:58', '2022-12-30 07:13:49'),
(138, 0, 'en', 'messages', 'order_date', 'Order date', '2022-12-30 06:56:58', '2022-12-30 07:13:49'),
(139, 0, 'en', 'messages', 'order_status', 'Order Status', '2022-12-30 06:56:58', '2022-12-30 07:13:49'),
(140, 0, 'en', 'messages', 'canceled', 'Canceled', '2022-12-30 06:56:58', '2022-12-30 07:13:49'),
(141, 0, 'en', 'messages', 'completed', 'Completed', '2022-12-30 06:56:58', '2022-12-30 07:13:49'),
(142, 0, 'en', 'messages', 'on_hold', 'On Hold', '2022-12-30 06:56:58', '2022-12-30 07:13:49'),
(143, 0, 'en', 'messages', 'pending', 'Pending', '2022-12-30 06:56:58', '2022-12-30 07:13:49'),
(144, 0, 'en', 'messages', 'processing', 'Processing', '2022-12-30 06:56:58', '2022-12-30 07:13:49'),
(145, 0, 'en', 'messages', 'refunded', 'Refunded', '2022-12-30 06:56:58', '2022-12-30 07:13:49'),
(146, 0, 'en', 'messages', 'phone', 'Phone', '2022-12-30 06:56:58', '2022-12-30 07:13:49'),
(147, 0, 'en', 'messages', 'address_info', 'Address Information', '2022-12-30 06:56:58', '2022-12-30 07:13:49'),
(148, 0, 'en', 'messages', 'item_ordered', 'Items Ordered', '2022-12-30 06:56:58', '2022-12-30 07:13:49'),
(149, 0, 'en', 'messages', 'unit_price', 'Unit Price', '2022-12-30 06:56:58', '2022-12-30 07:13:49'),
(150, 0, 'en', 'messages', 'qty', 'Quantity', '2022-12-30 06:56:58', '2022-12-30 07:13:49'),
(151, 0, 'en', 'messages', 'line_total', 'Line Total', '2022-12-30 06:56:58', '2022-12-30 07:13:49'),
(152, 0, 'en', 'messages', 'subtotal', 'Subtotal', '2022-12-30 06:56:58', '2022-12-30 07:13:49'),
(153, 0, 'en', 'messages', 'shipping', 'Shipping', '2022-12-30 06:56:58', '2022-12-30 07:13:49'),
(154, 0, 'en', 'messages', 'add_sepical_category', 'Add Sepical Category', '2022-12-30 06:56:58', '2022-12-30 07:13:49'),
(155, 0, 'en', 'messages', 'description', 'Description', '2022-12-30 06:56:58', '2022-12-30 07:13:49'),
(156, 0, 'en', 'messages', 'image', 'Image', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(157, 0, 'en', 'messages', 'edit_page', 'Edit Page', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(158, 0, 'en', 'messages', 'page_name', 'Page Name', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(159, 0, 'en', 'messages', 'general_sec', 'General Section', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(160, 0, 'en', 'messages', 'soical_sec', 'Soical Login Section', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(161, 0, 'en', 'messages', 'section', 'Section', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(162, 0, 'en', 'messages', 'address', 'Address', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(163, 0, 'en', 'messages', 'default_country', 'Default  Country', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(164, 0, 'en', 'messages', 'select_country', 'Select Country', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(165, 0, 'en', 'messages', 'default_locales', 'Default Locales', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(166, 0, 'en', 'messages', 'select_locale', 'Select Locale', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(167, 0, 'en', 'messages', 'default_timezone', 'Default TimeZone', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(168, 0, 'en', 'messages', 'select_timezone', 'Select TimeZone', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(169, 0, 'en', 'messages', 'default_currency', 'Default Currency', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(170, 0, 'en', 'messages', 'facebook_api_id', 'Facebook API ID', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(171, 0, 'en', 'messages', 'facebook_secret', 'Facebook Secret', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(172, 0, 'en', 'messages', 'google_api_id', 'Google API ID', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(173, 0, 'en', 'messages', 'google_secret', 'Google Secret', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(174, 0, 'en', 'messages', 'enable_facebook_login', 'Enable the Facebook Login', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(175, 0, 'en', 'messages', 'enable_google_login', 'Enable the Google Login', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(176, 0, 'en', 'messages', 'label', 'label', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(177, 0, 'en', 'messages', 'cost', 'Cost', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(178, 0, 'en', 'messages', 'paypal', 'PayPal', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(179, 0, 'en', 'messages', 'stripe', 'Stripe', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(180, 0, 'en', 'messages', 'case_on_delivery', 'Cash on delivery', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(181, 0, 'en', 'messages', 'enable', 'Enable', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(182, 0, 'en', 'messages', 'sandbox', 'Sandbox', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(183, 0, 'en', 'messages', 'use_sandbox', 'Use sandbox for test payments', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(184, 0, 'en', 'messages', 'api_key', 'API Key', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(185, 0, 'en', 'messages', 'secret', 'Secret Key', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(186, 0, 'en', 'messages', 'add_admin', 'Add Admin', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(187, 0, 'en', 'messages', 'confirm_password', 'Confirm Password', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(188, 0, 'en', 'messages', 'edit_admin', 'Edit Admin', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(189, 0, 'en', 'messages', 'values', 'Values', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(190, 0, 'en', 'messages', 'filterable', 'Filterable', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(191, 0, 'en', 'messages', 'filter_checkbox', 'Use this attribute for filtering products', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(192, 0, 'en', 'messages', 'add_new_row', 'Add New Row', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(193, 0, 'en', 'messages', 'type', 'Type', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(194, 0, 'en', 'messages', 'required', 'Required', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(195, 0, 'en', 'messages', 'req_option_msg', 'This option is required', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(196, 0, 'en', 'messages', 'dropdown', 'Dropdown', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(197, 0, 'en', 'messages', 'checkbox', 'Checkbox', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(198, 0, 'en', 'messages', 'radiobutton', 'Radio Button', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(199, 0, 'en', 'messages', 'multiple_select', 'Multiple Select', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(200, 0, 'en', 'messages', 'price', 'Price', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(201, 0, 'en', 'messages', 'price_type', 'Price Type', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(202, 0, 'en', 'messages', 'thumbnail', 'Thumbnail', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(203, 0, 'en', 'messages', 'product_name', 'Product name', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(204, 0, 'en', 'messages', 'review_name', 'Review Name', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(205, 0, 'en', 'messages', 'basic_info', 'Basic Information', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(206, 0, 'en', 'messages', 'add_info', 'Additional Information', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(207, 0, 'en', 'messages', 'inventory', 'Inventory', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(208, 0, 'en', 'messages', 'images', 'Images', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(209, 0, 'en', 'messages', 'sub_cat', 'Sub-Category', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(210, 0, 'en', 'messages', 'pro_status', 'Enable the product', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(211, 0, 'en', 'messages', 'spe_price', 'Special Price', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(212, 0, 'en', 'messages', 'start', 'Start', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(213, 0, 'en', 'messages', 'to', 'To', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(214, 0, 'en', 'messages', 'from', 'From', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(215, 0, 'en', 'messages', 'SKU', 'SKU', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(216, 0, 'en', 'messages', 'inventory_mang', 'Inventory Management', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(217, 0, 'en', 'messages', 'donot_track_inven', 'Don\'t Track Inventory', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(218, 0, 'en', 'messages', 'track_inven', 'Track Inventory', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(219, 0, 'en', 'messages', 'stock_avilable', 'Stock Availability', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(220, 0, 'en', 'messages', 'in_stock', 'In Stock', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(221, 0, 'en', 'messages', 'outstock', 'Out Of Stock', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(222, 0, 'en', 'messages', 'basic_img', 'Basic Image', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(223, 0, 'en', 'messages', 'add_img', 'Additional Images', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(224, 0, 'en', 'messages', 'SEO', 'SEO', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(225, 0, 'en', 'messages', 'Url', 'Url', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(226, 0, 'en', 'messages', 'meta_title', 'Meta Title', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(227, 0, 'en', 'messages', 'meta_keyword', 'Meta Keywords', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(228, 0, 'en', 'messages', 'meta_desc', 'Meta Description', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(229, 0, 'en', 'messages', 'realted_product', 'Related Products', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(230, 0, 'en', 'messages', 'up_sell', 'Up-Sells', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(231, 0, 'en', 'messages', 'cross_sell', 'Cross-sell', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(232, 0, 'en', 'messages', 'additional', 'Additionals', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(233, 0, 'en', 'messages', 'short_desc', 'Short Description', '2022-12-30 06:56:58', '2022-12-30 07:13:50'),
(234, 0, 'en', 'messages', 'product_new_from', 'Product New From', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(235, 0, 'en', 'messages', 'product_new_to', 'Product New To', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(236, 0, 'en', 'messages', 'upload_crop_img', 'Upload & Crop Image', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(237, 0, 'en', 'messages', 'upload_img', 'Upload Image', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(238, 0, 'en', 'messages', 'new_option', 'New Option', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(239, 0, 'en', 'messages', 'add_new_option', 'Add New Option', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(240, 0, 'en', 'messages', 'add_global_option', 'Add Global Option', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(241, 0, 'en', 'messages', 'coupon_report', 'Coupons Report', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(242, 0, 'en', 'messages', 'customer_order_report', 'Customers Order Report', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(243, 0, 'en', 'messages', 'pro_pur_report', 'Products Purchase Report', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(244, 0, 'en', 'messages', 'pro_stock_report', 'Products Stock Report', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(245, 0, 'en', 'messages', 'sales_report', 'Sales Report', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(246, 0, 'en', 'messages', 'shipping_report', 'Shipping Report', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(247, 0, 'en', 'messages', 'tax_report', 'Tax Report', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(248, 0, 'en', 'messages', 'add_product_report', 'Add Product Report', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(249, 0, 'en', 'messages', 'top_seller_report', 'Top Sellers Report', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(250, 0, 'en', 'messages', 'add_customer_report', 'Add Customer Report', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(251, 0, 'en', 'messages', 'add_coupon_report', 'Add Coupon Report', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(252, 0, 'en', 'messages', 'filter', 'Filter', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(253, 0, 'en', 'messages', 'cus_name', 'Customer Name', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(254, 0, 'en', 'messages', 'cus_email', 'Customer Email', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(255, 0, 'en', 'messages', 'home_delivery', 'Home Delivery', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(256, 0, 'en', 'messages', 'local_pickup', 'Local Pickup', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(257, 0, 'en', 'messages', 'customer_order_confirm_email', 'Send Customer email after order status is changed', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(258, 0, 'en', 'messages', 'is_email_confirm', 'Enable Customer Register Email Confirmation', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(259, 0, 'en', 'messages', 'is_admin_send_mail', 'Send new order notification to the admin', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(260, 0, 'en', 'messages', 'fileexport', 'Export', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(261, 0, 'en', 'messages', 'feature_product', 'Feature Product', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(262, 0, 'en', 'messages', 'main_title', 'Main Title', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(263, 0, 'en', 'messages', 'add_fetureproduct', 'Add Feature Product', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(264, 0, 'en', 'messages', 'working_day', 'Working Day', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(265, 0, 'en', 'messages', 'working_placeholder', 'Mon - Sun / 9:00AM - 8:00PM', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(266, 0, 'en', 'messages', 'helpline', 'HelpLine No', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(267, 0, 'en', 'messages', 'main_feature', 'Main Features', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(268, 0, 'en', 'messages', 'newsletter', 'NewsLetter', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(269, 0, 'en', 'messages', 'helpsupport', 'Help & Support', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(270, 0, 'en', 'messages', 'termscon', 'Terms & Condition', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(271, 0, 'en', 'messages', 'topic', 'Topic', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(272, 0, 'en', 'messages', 'topicname', 'Topic Name', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(273, 0, 'en', 'messages', 'quesans', 'Question Answer List', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(274, 0, 'en', 'messages', 'ques', 'Question', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(275, 0, 'en', 'messages', 'ans', 'Answer', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(276, 0, 'en', 'messages', 'contact_details', 'Contact Detail', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(277, 0, 'en', 'messages', 'subject', 'Subject', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(278, 0, 'en', 'messages', 'message', 'Message', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(279, 0, 'en', 'messages', 'out_of_delivery', 'Out for delivery', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(280, 0, 'en', 'messages', 'sub_title', 'Sub Title', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(281, 0, 'en', 'messages', 'subcategory', 'Sub Category', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(282, 0, 'en', 'messages', 'Banner_1', 'Banner 1', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(283, 0, 'en', 'messages', 'Banner_2', 'Banner 2', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(284, 0, 'en', 'messages', 'Banner_3', 'Banner 3', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(285, 0, 'en', 'messages', 'color_name', 'Color Name', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(286, 0, 'en', 'messages', 'product_color', 'Product Color', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(287, 0, 'en', 'messages', 'check_price', 'Check Price', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(288, 0, 'en', 'messages', 'special_price_check', 'Check Speical Price', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(289, 0, 'en', 'messages', 'all_category', 'All Categories', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(290, 0, 'en', 'messages', 'Hello', 'Hello', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(291, 0, 'en', 'messages', 'confirm_email_address', 'Confirm Email Address', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(292, 0, 'en', 'messages', 'resetpassord', 'Reset Password', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(293, 0, 'en', 'messages', 'new_order', 'New Order', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(294, 0, 'en', 'messages', 'order_amount', 'Order Amount', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(295, 0, 'en', 'messages', 'my_account', 'My Account', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(296, 0, 'en', 'messages', 'my_order', 'My Order', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(297, 0, 'en', 'messages', 'personal_detail', 'Personal detail', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(298, 0, 'en', 'messages', 'view', 'View', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(299, 0, 'en', 'messages', 'no_order', 'No Orders yet.', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(300, 0, 'en', 'messages', 'home', 'Home', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(301, 0, 'en', 'messages', 'order_detail', 'Order-detail', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(302, 0, 'en', 'messages', 'order_thank_msg', 'Thank you. Your order has been received.', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(303, 0, 'en', 'messages', 'Delivered', 'Delivered', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(304, 0, 'en', 'messages', 'Quanitity', 'Quanitity', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(305, 0, 'en', 'messages', 'same_ship', 'Same as a Shipping Address.', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(306, 0, 'en', 'messages', 'cart_total', 'Cart Totals', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(307, 0, 'en', 'messages', 'sub_total', 'Sub Total', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(308, 0, 'en', 'messages', 'free_delivery', 'Free Delivery', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(309, 0, 'en', 'messages', 'My_WishList', 'My WishList', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(310, 0, 'en', 'messages', 'del', 'Delete', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(311, 0, 'en', 'messages', 'stock_status', 'Stock Status', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(312, 0, 'en', 'messages', 'add_to_cart', 'Add to cart', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(313, 0, 'en', 'messages', 'continue_shopping', 'Continue Shoping', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(314, 0, 'en', 'messages', 'Checkout', 'Checkout', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(315, 0, 'en', 'messages', 'My_Cart', 'My Cart', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(316, 0, 'en', 'messages', 'return_cus', 'Returning Customer??', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(317, 0, 'en', 'messages', 'note1', 'If you have shopped with us before, please enter your details in the boxes below. If you are a new customer,', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(318, 0, 'en', 'messages', 'note2', 'please proceed to the Billing & Shipping section.', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(319, 0, 'en', 'messages', 'sign_up', 'Sign up', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(320, 0, 'en', 'messages', 'rem_me', 'Remember me', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(321, 0, 'en', 'messages', 'billing_detail', 'Billing Detail', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(322, 0, 'en', 'messages', 'Town_City', 'Town / City ', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(323, 0, 'en', 'messages', 'Postcode_Zip', 'Postcode / Zip', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(324, 0, 'en', 'messages', 'ship_different', 'Ship To a Different Address?', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(325, 0, 'en', 'messages', 'notes_op', 'Order Notes ( Optional )', '2022-12-30 06:56:59', '2022-12-30 07:13:50'),
(326, 0, 'en', 'messages', 'your_order', 'Your Order', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(327, 0, 'en', 'messages', 'Detail', 'Detail', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(328, 0, 'en', 'messages', 'place_order', 'Place Order', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(329, 0, 'en', 'messages', 'cart_details', 'Cart Details', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(330, 0, 'en', 'messages', 'coupon_note', 'Enter your cupon code if you have one.', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(331, 0, 'en', 'messages', 'apply_coupon', 'Apply coupon', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(332, 0, 'en', 'messages', 'offer_on_bill', 'Offer applied on the bill.', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(333, 0, 'en', 'messages', 'proceed_to_checkout', 'Proceed To Checkout', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(334, 0, 'en', 'messages', 'product_ls', 'Product List', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(335, 0, 'en', 'messages', 'color', 'Colors', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(336, 0, 'en', 'messages', 'Size', 'Size', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(337, 0, 'en', 'messages', 'show_note1', 'Showing  1-', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(338, 0, 'en', 'messages', 'pro_of', 'products of', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(339, 0, 'en', 'messages', 'sort_by', 'Sort By', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(340, 0, 'en', 'messages', 'Popalarity', 'Popalarity', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(341, 0, 'en', 'messages', 'PLTH', 'Price - Low to High', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(342, 0, 'en', 'messages', 'PHTL', 'Price - High to Low', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(343, 0, 'en', 'messages', 'newest', 'Newest First', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(344, 0, 'en', 'messages', 'shop_now', 'Shop Now', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(345, 0, 'en', 'messages', 'view_product_detail', 'View Product Details', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(346, 0, 'en', 'messages', 'add_to_wishlist', 'Add To Wishlist', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(347, 0, 'en', 'messages', 'Tags', 'Tags', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(348, 0, 'en', 'messages', 'Specifications', 'Specifications', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(349, 0, 'en', 'messages', 'by', 'By', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(350, 0, 'en', 'messages', 'on', 'On', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(351, 0, 'en', 'messages', 'review_note', 'BE THE FIRST TO REVIEW \'WHITE BAG\'', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(352, 0, 'en', 'messages', 'your_ratting', 'Your Rating', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(353, 0, 'en', 'messages', 'related_product', 'REALTED PRODUCTS', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(354, 0, 'en', 'messages', 'contact_us', 'Contact Us', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(355, 0, 'en', 'messages', 'Subject', 'Subject', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(356, 0, 'en', 'messages', 'helpnote', 'How can we help you?', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(357, 0, 'en', 'messages', 'help_topic', 'HELP TOPICS', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(358, 0, 'en', 'messages', 'home_note_1', 'Save Up To', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(359, 0, 'en', 'messages', 'home_note_2', 'Off', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(360, 0, 'en', 'messages', 'home_note_3', 'New Price', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(361, 0, 'en', 'messages', 'home_note_4', 'Order over 60', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(362, 0, 'en', 'messages', 'home_note_5', '99% Customer', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(363, 0, 'en', 'messages', 'Feedbacks', 'Feedbacks', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(364, 0, 'en', 'messages', 'Payment', 'Payment', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(365, 0, 'en', 'messages', 'secured_sys', 'Secured system', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(366, 0, 'en', 'messages', 'support', '24/7 Support', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(367, 0, 'en', 'messages', 'best_selling', 'Best Selling', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(368, 0, 'en', 'messages', 'up_to', 'Up to', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(369, 0, 'en', 'messages', 'logout_msg', 'Are you sure you want to log out?', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(370, 0, 'en', 'messages', 'forgot_note', 'Reset Your Password', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(371, 0, 'en', 'messages', 'back_to_login', 'Back To Login', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(372, 0, 'en', 'messages', 'forgot_pwd', 'Forgot Password', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(373, 0, 'en', 'messages', 'login', 'LOGIN', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(374, 0, 'en', 'messages', 'register', 'REGISTER', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(375, 0, 'en', 'messages', 'lost_pwd', 'Lost Your Password?', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(376, 0, 'en', 'messages', 'view_cart', 'View Cart', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(377, 0, 'en', 'messages', 'cart_empty', 'Cart is Empty.', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(378, 0, 'en', 'messages', 'choose_color', 'Choose Color For', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(379, 0, 'en', 'messages', 'search', 'Search', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(380, 0, 'en', 'messages', 'about', 'About Us', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(381, 0, 'en', 'messages', 'order_history', 'Order History', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(382, 0, 'en', 'messages', 'newsletternote', 'Be The First To Know', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(383, 0, 'en', 'messages', 'go', 'Go', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(384, 0, 'en', 'messages', 'footer_note', '© Copyright All Rights Reserved.', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(385, 0, 'en', 'messages', 'best_offer', 'Best Offers', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(386, 0, 'en', 'messages', 'big_deal', 'Big Deal', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(387, 0, 'en', 'messages', 'normal_deal', 'Normal Deal', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(388, 0, 'en', 'messages', 'No', 'No', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(389, 0, 'en', 'messages', 'yes', 'Yes', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(390, 0, 'en', 'messages', 'complain', 'Complain', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(391, 0, 'en', 'messages', 'reason_error', 'complain_type', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(392, 0, 'en', 'messages', 'orders_pending', 'You have not any pending orders', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(393, 0, 'en', 'messages', 'you_have', 'You have', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(394, 0, 'en', 'messages', 'all', 'All', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(395, 0, 'en', 'messages', 'rtl', '0', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(396, 0, 'en', 'messages', 'No Result Found', 'No Result Found', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(397, 0, 'en', 'messages', 'cus', 'Customers', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(398, 0, 'en', 'messages', 'a1', 'We love restaurants as much as you do. That’s why we’ve been helping\n         them fill tables since 1999. Welcome to elixir restaurant', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(399, 0, 'en', 'messages', 'this_history', 'The History', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(400, 0, 'en', 'messages', 'the', 'The', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(401, 0, 'en', 'messages', 'contact_success', 'Thanks For You Contact', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(402, 0, 'en', 'messages', 'history_of_web', 'History of Website', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(403, 0, 'en', 'messages', 'a2', 'and Cooks gives further intimation on Mr Boulanger usual menu, stating confidently that \"Boulanger served salted poultry and fresh eggs, all presented without a tablecloth on small marble tables\". Numerous commentators have also referred to the supposed restaurant owners eccentric habit of touting for custom outside his establishment, dressed in aristocratic fashion and brandishing a sword', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(404, 0, 'en', 'messages', 'a3', 'According to Miss Spang, there is not a shred of evidence for any of it. She said: These legends just get passed on by hearsay and then spiral out of control. Her interest in dates back to a history of food seminar in Paris in the mid-1990s', '2022-12-30 06:56:59', '2022-12-30 07:13:51'),
(405, 0, 'en', 'messages', 'notification', 'Notification', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(406, 0, 'en', 'messages', 'msg', 'Message', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(407, 0, 'en', 'messages', 'add_notification', 'Add Notification', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(408, 0, 'en', 'messages', 'error_notification', 'Notification Not Send Successfully', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(409, 0, 'en', 'messages', 'success_notification', 'Notification  Send Successfully', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(410, 0, 'en', 'messages', 'Android Server Key', 'Android Server Key', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(411, 0, 'en', 'messages', 'Iphone Server Key', 'Iphone Server Key', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(412, 0, 'en', 'messages', 'Key Update Successfully', 'Key Update Successfully', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(413, 0, 'en', 'messages', 'Code Expired', 'Code Expired', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(414, 0, 'en', 'messages', 'Your transaction description', 'Your transaction description', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(415, 0, 'en', 'messages', 'All', 'All', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(416, 0, 'en', 'messages', 'Please Login to Your Account', 'Please Login to Your Account', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(417, 0, 'en', 'messages', 'Invaild Coupon', 'Invaild Coupon', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(418, 0, 'en', 'messages', 'Please Enter Coupon Code', 'Please Enter Coupon Code', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(419, 0, 'en', 'messages', 'No Product', 'No Product', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(420, 0, 'en', 'messages', 'is Required', 'is Required', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(421, 0, 'en', 'messages', 'Email Send Successfully', 'Email Send Successfully', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(422, 0, 'en', 'messages', 'Your Entered Email Not Exist', 'Your Entered Email Not Exist', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(423, 0, 'en', 'messages', 'Entered Your Email', 'Entered Your Email', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(424, 0, 'en', 'messages', 'Your wishlist is currently empty!', 'Your wishlist is currently empty!', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(425, 0, 'en', 'messages', 'offer_deal_lang', 'Please Select Offer', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(426, 0, 'en', 'messages', 'Read More', 'Read More', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(427, 0, 'en', 'messages', 'Show All', 'Show All', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(428, 0, 'en', 'messages', 'Quick View', 'Quick View', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(429, 0, 'en', 'messages', 'No Any Product In Your Compare List', 'There are no product in compare list.', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(430, 0, 'en', 'messages', 'Compare', 'Compare', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(431, 0, 'en', 'messages', 'Remove', 'Remove', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(432, 0, 'en', 'messages', 'Product Name', 'Product Name', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(433, 0, 'en', 'messages', 'Product Image', 'Product Image', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(434, 0, 'en', 'messages', 'On Sale', 'On Sale', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(435, 0, 'en', 'messages', 'Product Sku', 'Product Sku', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(436, 0, 'en', 'messages', 'Availability', 'Availability', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(437, 0, 'en', 'messages', 'Availabel In stock', 'Availabel In stock', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(438, 0, 'en', 'messages', 'Availabel Out Of stock', 'Availabel Out Of stock', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(439, 0, 'en', 'messages', 'for', 'for', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(440, 0, 'en', 'messages', 'item', 'item', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(441, 0, 'en', 'messages', 'The product has been added to your compare list', 'The product has been added to your compare list.', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(442, 0, 'en', 'messages', 'mobile_banner', 'Mobile Banner', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(443, 0, 'en', 'messages', 'setupemail', 'You need to setup email then email functionality  is work', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(444, 0, 'en', 'messages', 'user_register', 'User Register Successfully', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(445, 0, 'en', 'messages', 'coupon_vaild_max', 'Please Check amount', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(446, 0, 'en', 'messages', 'you_stripe_error', 'Stripe account not setup', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(447, 0, 'en', 'messages', 'you_paypal_error', 'PayPal account not setup', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(448, 0, 'en', 'messages', 'error_in_paypal', 'Error Paypal Account Setup', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(449, 0, 'en', 'messages', 'no_realted', 'No any product avilable for selection', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(450, 0, 'en', 'messages', 'New Attributes', 'New Attributes', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(451, 0, 'en', 'messages', 'company_name', 'Company Name', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(452, 0, 'en', 'messages', 'document', 'How to manage products?', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(453, 0, 'en', 'messages', 'soical_note', 'Note', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(454, 0, 'en', 'messages', 'facebook_redirect_url', 'Facebook Redirect Url', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(455, 0, 'en', 'messages', 'google_redirect_url', 'Google Redirect Url', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(456, 0, 'en', 'messages', 'com_logo', 'Company Logo', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(457, 0, 'en', 'messages', 'img_invaild', 'Image Size Invaild', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(458, 0, 'en', 'messages', 'site_rtl', 'Site RTL', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(459, 0, 'en', 'messages', 'site_color_setting', 'Color Setting Show', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(460, 0, 'en', 'messages', 'img_tr', 'image should be transparent', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(461, 0, 'en', 'messages', 'news', 'News', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(462, 0, 'en', 'messages_error_success', 'delete_alert', 'Are you sure to want to delete?', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(463, 0, 'en', 'messages_error_success', 'coupon_code_use', 'Coupon Code Already Use', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(464, 0, 'en', 'messages_error_success', 'required_field', 'Please Enter Required Fields', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(465, 0, 'en', 'messages_error_success', 'general_form_msg', 'First, Fill Up General Information Then Proceed Next', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(466, 0, 'en', 'messages_error_success', 'data_save_success', 'Data Save Successfully', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(467, 0, 'en', 'messages_error_success', 'note_sensonal', 'Note:- Only One Sensonal Offer Active At Time', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(468, 0, 'en', 'messages_error_success', 'confirm_alert', 'Are you sure to want?', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(469, 0, 'en', 'messages_error_success', 'per_bet', 'Percentage Between 0 To 100', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(470, 0, 'en', 'messages_error_success', 'check_price', 'Please Check Offer Price', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(471, 0, 'en', 'messages_error_success', 'offer_price_error', 'Please Select Product Then Add Offer Price', '2022-12-30 06:57:00', '2022-12-30 07:13:51');
INSERT INTO `ltm_translations` (`id`, `status`, `locale`, `group`, `key`, `value`, `created_at`, `updated_at`) VALUES
(472, 0, 'en', 'messages_error_success', 'sepical_price_vaildate', 'You Add Sepical Product Price So You must Be Add Price Sepical Start Date and End Date', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(473, 0, 'en', 'messages_error_success', 'selling_mrp_vaildate', 'MRP Price Always Greater Then Selling Price', '2022-12-30 06:57:00', '2022-12-30 07:13:51'),
(474, 0, 'en', 'messages_error_success', 'sku_already', 'Product Sku Already Existe', '2022-12-30 06:57:00', '2022-12-30 07:13:52'),
(475, 0, 'en', 'messages_error_success', 'report_not_select', 'Please Select Report Type', '2022-12-30 06:57:00', '2022-12-30 07:13:52'),
(476, 0, 'en', 'messages_error_success', 'login_error', 'Your Login Credentials Are Wrong', '2022-12-30 06:57:00', '2022-12-30 07:13:52'),
(477, 0, 'en', 'messages_error_success', 'profile_sucess_update', 'Profile Update Successfully', '2022-12-30 06:57:00', '2022-12-30 07:13:52'),
(478, 0, 'en', 'messages_error_success', 'password_update_success', 'Password Update Successfully', '2022-12-30 06:57:00', '2022-12-30 07:13:52'),
(479, 0, 'en', 'messages_error_success', 'error_code', 'Please Try Again...', '2022-12-30 06:57:00', '2022-12-30 07:13:52'),
(480, 0, 'en', 'messages_error_success', 'category_add_success', 'Category Add Successfully', '2022-12-30 06:57:00', '2022-12-30 07:13:52'),
(481, 0, 'en', 'messages_error_success', 'sepcategory_add_success', 'Sepical Category Add Successfully', '2022-12-30 06:57:00', '2022-12-30 07:13:52'),
(482, 0, 'en', 'messages_error_success', 'category_update_success', 'Category Update Successfully', '2022-12-30 06:57:00', '2022-12-30 07:13:52'),
(483, 0, 'en', 'messages_error_success', 'subcat_add_success', 'Sub Category Add Successfully', '2022-12-30 06:57:00', '2022-12-30 07:13:52'),
(484, 0, 'en', 'messages_error_success', 'brand_add_success', 'Brand Add Successfully', '2022-12-30 06:57:00', '2022-12-30 07:13:52'),
(485, 0, 'en', 'messages_error_success', 'brand_update_success', 'Brand Update Successfully', '2022-12-30 06:57:00', '2022-12-30 07:13:52'),
(486, 0, 'en', 'messages_error_success', 'sepcategory_update_success', 'Sepical Category Update Successfully', '2022-12-30 06:57:00', '2022-12-30 07:13:52'),
(487, 0, 'en', 'messages_error_success', 'sepcategory_change_success', 'Sepical Category Changes Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(488, 0, 'en', 'messages_error_success', 'category_del', 'Category Deleted Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(489, 0, 'en', 'messages_error_success', 'brand_del', 'Brand Deleted Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(490, 0, 'en', 'messages_error_success', 'sensonal_offer_add_success', 'Sensonal Offer Add Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(491, 0, 'en', 'messages_error_success', 'sensonal_offer_change_success', 'Sensonal Offer Changes Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(492, 0, 'en', 'messages_error_success', 'Offer_add_success', 'Offer Add Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(493, 0, 'en', 'messages_error_success', 'offer_update_success', 'Offer Update Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(494, 0, 'en', 'messages_error_success', 'offer_delete', 'Offer Deleted Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(495, 0, 'en', 'messages_error_success', 'mail_send_success', 'Mail Send Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(496, 0, 'en', 'messages_error_success', 'order_status_change', 'Order Status Change Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(497, 0, 'en', 'messages_error_success', 'order_hold_msg', 'Order Is Hold On', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(498, 0, 'en', 'messages_error_success', 'order_pending_msg', 'Order Is Pending', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(499, 0, 'en', 'messages_error_success', 'order_complete_msg', 'Order Is Completed', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(500, 0, 'en', 'messages_error_success', 'order_cancel_msg', 'Order Is Cancel', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(501, 0, 'en', 'messages_error_success', 'order_refund_msg', 'Order Is Refund', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(502, 0, 'en', 'messages_error_success', 'page_update_success', 'Page Update Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(503, 0, 'en', 'messages_error_success', 'status_change_success', 'Status Change Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(504, 0, 'en', 'messages_error_success', 'tax_add_success', 'Tax Add Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(505, 0, 'en', 'messages_error_success', 'tax_update_sucess', 'Tax Update Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(506, 0, 'en', 'messages_error_success', 'create_success', 'Create Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(507, 0, 'en', 'messages_error_success', 'email_already_error', 'Email Already Exists', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(508, 0, 'en', 'messages_error_success', 'user_active_msg', 'User Is Activated', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(509, 0, 'en', 'messages_error_success', 'user_deactive_msg', 'User Is Deactivated', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(510, 0, 'en', 'messages_error_success', 'user_update_success', 'User Update Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(511, 0, 'en', 'messages_error_success', 'email_verified', 'Email Verified Successfully Now You Can Login', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(512, 0, 'en', 'messages_error_success', 'catalog_del', 'Product Deleted Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(513, 0, 'en', 'messages_error_success', 'attributeset_success', 'AttributeSet Add Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(514, 0, 'en', 'messages_error_success', 'attributeset_update_success', 'AttributeSet Update Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(515, 0, 'en', 'messages_error_success', 'option_add_success', 'Option Add Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(516, 0, 'en', 'messages_error_success', 'option_update_success', 'Option Update Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(517, 0, 'en', 'messages_error_success', 'attribute_add_success', 'Attribute Add Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(518, 0, 'en', 'messages_error_success', 'attribute_update_success', 'Attribute Update Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(519, 0, 'en', 'messages_error_success', 'review_status_change', 'Review Status Change Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(520, 0, 'en', 'messages_error_success', 'option_delete', 'Option Delete Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(521, 0, 'en', 'messages_error_success', 'attributeset_del', 'Attribute Set Delete Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(522, 0, 'en', 'messages_error_success', 'attribute_del', 'Attribute Delete Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(523, 0, 'en', 'messages_error_success', 'review_del_success', 'Review Delete Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(524, 0, 'en', 'messages_error_success', 'topic_add_success', 'Topic Add Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(525, 0, 'en', 'messages_error_success', 'ques_add_success', 'Question Add Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(526, 0, 'en', 'messages_error_success', 'topic_update_success', 'Topic Update Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(527, 0, 'en', 'messages_error_success', 'topic_del_success', 'Topic Delete Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(528, 0, 'en', 'messages_error_success', 'topic_del_danger', 'Topic Delete Unsuccessfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(529, 0, 'en', 'messages_error_success', 'ques_update_success', 'Question Update Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(530, 0, 'en', 'messages_error_success', 'ques_del_success', 'Question Delete Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(531, 0, 'en', 'messages_error_success', 'contact_del', 'Contact Delete Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(532, 0, 'en', 'messages_error_success', 'error_coupon_limit', 'Usage  limit per customer is less then usage limit per coupon', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(533, 0, 'en', 'messages_error_success', 'order_out_of_delivery_msg', 'Order Is Out For Delivery', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(534, 0, 'en', 'messages_error_success', 'ple_sel_option', 'Please Select Option', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(535, 0, 'en', 'messages_error_success', 'product_add_success', 'Product Add Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(536, 0, 'en', 'messages_error_success', 'Coupon_Delete', 'Coupon Delete', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(537, 0, 'en', 'messages_error_success', 'feature_product_del', 'Feature Product Delete Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(538, 0, 'en', 'messages_error_success', 'feature_product_add', 'Feature Product Add Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(539, 0, 'en', 'messages_error_success', 'newsletter_note', 'Get all the latest information on Events,Sales and Offers in Your Email', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(540, 0, 'en', 'messages_error_success', 'pwd_reset', 'Password Reset Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(541, 0, 'en', 'messages_error_success', 'order_place_success', 'Order Place Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(542, 0, 'en', 'messages_error_success', 'connection_timeout', 'Connection timeout', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(543, 0, 'en', 'messages_error_success', 'paypal_error_1', 'Some error occur, sorry for inconvenient', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(544, 0, 'en', 'messages_error_success', 'unknow_error', 'Unknown error occurred', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(545, 0, 'en', 'messages_error_success', 'payment_fail', 'Payment failed', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(546, 0, 'en', 'messages_error_success', 'product_status_update', 'Product Status Update Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(547, 0, 'en', 'messages_error_success', 'attribute_set_existe', 'AttributeSet Name Already Existe', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(548, 0, 'en', 'messages_error_success', 'user_del', 'User Delete Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(549, 0, 'en', 'messages_error_success', 'review_success', 'Review Add Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(550, 0, 'en', 'messages_error_success', 'verified_email', 'Please Verified Your Email', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(551, 0, 'en', 'messages_error_success', 'order_process_msg', 'Order Is Process asdasd asd', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(552, 0, 'en', 'messages_error_success', 'user_not_exist', 'User Not Exist in system', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(553, 0, 'en', 'messages_error_success', 'pro_add', 'Product Add Successfully', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(554, 0, 'en', 'pagination', 'previous', '&laquo; Previous', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(555, 0, 'en', 'pagination', 'next', 'Next &raquo;', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(556, 0, 'en', 'passwords', 'password', 'Password must be at least eight characters and match the confirmation.', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(557, 0, 'en', 'passwords', 'reset', 'Your password has been reset!', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(558, 0, 'en', 'passwords', 'sent', 'We have e-mailed your password reset link!', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(559, 0, 'en', 'passwords', 'token', 'This password reset token is invalid.', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(560, 0, 'en', 'passwords', 'user', 'We can\'t find a user with that e-mail address.', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(561, 0, 'en', 'passwords', 'pwd_same', 'New Password And Re-enter Password Must Be Same', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(562, 0, 'en', 'passwords', 'error_cur_pwd', 'Please Enter Correct Password', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(563, 0, 'en', 'passwords', 'pass_mus', 'Password And Confirm Password Must Be Same', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(564, 0, 'ar', 'vendor/cookieConsent', 'message', 'سيتم تحسين تجربتنا على هذا الموقع من خلال السماح بملفات تعريف الارتباط', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(565, 0, 'ar', 'vendor/cookieConsent', 'agree', 'السماح', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(566, 0, 'cs', 'vendor/cookieConsent', 'message', 'Tato stránka používá cookies na vylepšení vašeho uživatelského zážitku.', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(567, 0, 'cs', 'vendor/cookieConsent', 'agree', 'Souhlasím', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(568, 0, 'da', 'vendor/cookieConsent', 'message', 'Din oplevelse på dette websted vil blive forbedret ved at tillade cookies.', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(569, 0, 'da', 'vendor/cookieConsent', 'agree', 'Tillad cookies', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(570, 0, 'de', 'vendor/cookieConsent', 'message', 'Diese Seite verwendet Cookies um das Nutzererlebnis zu steigern.', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(571, 0, 'de', 'vendor/cookieConsent', 'agree', 'Akzeptieren', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(572, 0, 'el', 'vendor/cookieConsent', 'message', 'Η ιστοσελίδα μας χρησιμοποιεί cookies για να βελτιώσει την εμπειρία περιήγησής σας.', '2022-12-30 06:57:01', '2022-12-30 07:13:52'),
(573, 0, 'el', 'vendor/cookieConsent', 'agree', 'Αποδοχή', '2022-12-30 06:57:02', '2022-12-30 07:13:52'),
(574, 0, 'en', 'vendor/cookieConsent', 'message', 'Your experience on this site will be improved by allowing cookies.', '2022-12-30 06:57:02', '2022-12-30 07:13:52'),
(575, 0, 'en', 'vendor/cookieConsent', 'agree', 'Allow cookies', '2022-12-30 06:57:02', '2022-12-30 07:13:52'),
(576, 0, 'eo', 'vendor/cookieConsent', 'message', 'Tiu ĉi retejo uzas kuketojn por plibonigi vian sperton.', '2022-12-30 06:57:02', '2022-12-30 07:13:52'),
(577, 0, 'eo', 'vendor/cookieConsent', 'agree', 'Akcepti kuketojn', '2022-12-30 06:57:02', '2022-12-30 07:13:52'),
(578, 0, 'es', 'vendor/cookieConsent', 'message', 'Su experiencia en este sitio será mejorada con el uso de cookies.', '2022-12-30 06:57:02', '2022-12-30 07:13:52'),
(579, 0, 'es', 'vendor/cookieConsent', 'agree', 'Aceptar', '2022-12-30 06:57:02', '2022-12-30 07:13:52'),
(580, 0, 'et', 'vendor/cookieConsent', 'message', 'Sellel veebilehel on kasutusel cookies-failid teie kasutajaliidese parandamiseks.', '2022-12-30 06:57:02', '2022-12-30 07:13:52'),
(581, 0, 'et', 'vendor/cookieConsent', 'agree', 'Sain aru', '2022-12-30 06:57:02', '2022-12-30 07:13:52'),
(582, 0, 'fr', 'vendor/cookieConsent', 'message', 'Ce site nécessite l\'autorisation de cookies pour fonctionner correctement.', '2022-12-30 06:57:02', '2022-12-30 07:13:52'),
(583, 0, 'fr', 'vendor/cookieConsent', 'agree', 'Accepter', '2022-12-30 06:57:02', '2022-12-30 07:13:52'),
(584, 0, 'hu', 'vendor/cookieConsent', 'message', 'A megfelelő élmény biztosításához sütikre van szükség.', '2022-12-30 06:57:02', '2022-12-30 07:13:52'),
(585, 0, 'hu', 'vendor/cookieConsent', 'agree', 'Sütik engedélyezése', '2022-12-30 06:57:02', '2022-12-30 07:13:52'),
(586, 0, 'id', 'vendor/cookieConsent', 'message', 'Pengalaman anda pada situs ini akan meningkat dengan cara mengizinkan cookies.', '2022-12-30 06:57:02', '2022-12-30 07:13:52'),
(587, 0, 'id', 'vendor/cookieConsent', 'agree', 'Izikan Cookies', '2022-12-30 06:57:02', '2022-12-30 07:13:52'),
(588, 0, 'it', 'vendor/cookieConsent', 'message', 'Questo sito utilizza i cookies per offrire un\'esperienza migliore all\'utente.', '2022-12-30 06:57:02', '2022-12-30 07:13:52'),
(589, 0, 'it', 'vendor/cookieConsent', 'agree', 'Consenti i cookies', '2022-12-30 06:57:02', '2022-12-30 07:13:52'),
(590, 0, 'kr', 'vendor/cookieConsent', 'message', '보다 나은 사용자 경험을 위해 쿠키를 허용해 주십시요.', '2022-12-30 06:57:02', '2022-12-30 07:13:52'),
(591, 0, 'kr', 'vendor/cookieConsent', 'agree', '쿠키 허용', '2022-12-30 06:57:02', '2022-12-30 07:13:52'),
(592, 0, 'nb', 'vendor/cookieConsent', 'message', 'Vi bruker informasjonskapsler (cookies) som lagres i nettleseren din når du besøker nettsiden vår. Ved å fortsette å besøke nettsiden vår eller bruke tjenestene våre, godtar du at vi bruker informasjonskapsler.', '2022-12-30 06:57:02', '2022-12-30 07:13:52'),
(593, 0, 'nb', 'vendor/cookieConsent', 'agree', 'Jeg forstår', '2022-12-30 06:57:02', '2022-12-30 07:13:52'),
(594, 0, 'nl', 'vendor/cookieConsent', 'message', 'Uw ervaring op deze site wordt verbeterd door het gebruik van cookies.', '2022-12-30 06:57:02', '2022-12-30 07:13:52'),
(595, 0, 'nl', 'vendor/cookieConsent', 'agree', 'Sta cookies toe', '2022-12-30 06:57:02', '2022-12-30 07:13:52'),
(596, 0, 'pl', 'vendor/cookieConsent', 'message', 'Twoje doświadczenia na tej witrynie będą lepsze dzięki cookies.', '2022-12-30 06:57:02', '2022-12-30 07:13:52'),
(597, 0, 'pl', 'vendor/cookieConsent', 'agree', 'Zezwalaj na cookie', '2022-12-30 06:57:02', '2022-12-30 07:13:52'),
(598, 0, 'pt', 'vendor/cookieConsent', 'message', 'Este site utiliza cookies. Ao navegar no site estará a consentir a sua utilização.', '2022-12-30 06:57:02', '2022-12-30 07:13:52'),
(599, 0, 'pt', 'vendor/cookieConsent', 'agree', 'Aceitar', '2022-12-30 06:57:02', '2022-12-30 07:13:52'),
(600, 0, 'ro', 'vendor/cookieConsent', 'message', 'Experiența ta pe acest site va fi îmbunătățită dacă acceptați folosirea de cookie-uri.', '2022-12-30 06:57:02', '2022-12-30 07:13:52'),
(601, 0, 'ro', 'vendor/cookieConsent', 'agree', 'Acceptă cookie', '2022-12-30 06:57:02', '2022-12-30 07:13:52'),
(602, 0, 'ru', 'vendor/cookieConsent', 'message', 'На этом сайте используются файлы cookies для улучшения вашего пользовательского интерфейса.', '2022-12-30 06:57:02', '2022-12-30 07:13:52'),
(603, 0, 'ru', 'vendor/cookieConsent', 'agree', 'Разрешить', '2022-12-30 06:57:02', '2022-12-30 07:13:52'),
(604, 0, 'sk', 'vendor/cookieConsent', 'message', 'Táto stránka používa cookies na vylepšenie vášho užívateľského zážitku.', '2022-12-30 06:57:02', '2022-12-30 07:13:52'),
(605, 0, 'sk', 'vendor/cookieConsent', 'agree', 'Súhlasím', '2022-12-30 06:57:02', '2022-12-30 07:13:52'),
(606, 0, 'sv', 'vendor/cookieConsent', 'message', 'Vi använder kakor (cookies) för att webbplatsen ska fungera på ett bra sätt för dig. Genom att surfa vidare godkänner du att vi använder kakor.', '2022-12-30 06:57:02', '2022-12-30 07:13:53'),
(607, 0, 'sv', 'vendor/cookieConsent', 'agree', 'Jag förstår', '2022-12-30 06:57:02', '2022-12-30 07:13:53'),
(608, 0, 'tr', 'vendor/cookieConsent', 'message', 'Bu sitedeki deneyiminizi çerezlere izin vererek geliştirebilirsiniz.', '2022-12-30 06:57:02', '2022-12-30 07:13:53'),
(609, 0, 'tr', 'vendor/cookieConsent', 'agree', 'Çerezlere izin ver', '2022-12-30 06:57:02', '2022-12-30 07:13:53');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_07_02_230147_migration_cartalyst_sentinel', 1),
(2, '2014_04_02_193005_create_translations_table', 2);

-- --------------------------------------------------------

--
-- Table structure for table `news_letter`
--

CREATE TABLE `news_letter` (
  `id` int(11) NOT NULL,
  `email` varchar(250) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `offer`
--

CREATE TABLE `offer` (
  `id` int(11) NOT NULL,
  `offer_type` enum('1','2') NOT NULL DEFAULT '2' COMMENT '1=>category/subcategory,2=>product,3=>coupon',
  `title` varchar(250) DEFAULT NULL,
  `main_title` varchar(250) DEFAULT NULL,
  `banner` varchar(500) DEFAULT NULL,
  `start_date` varchar(250) DEFAULT NULL,
  `end_date` varchar(250) DEFAULT NULL,
  `fixed` varchar(250) DEFAULT NULL,
  `is_product` enum('1','2','3') NOT NULL DEFAULT '1' COMMENT '1=>category,2=>product,3=>coupon_offer',
  `product_id` int(11) DEFAULT '0',
  `category_id` int(11) DEFAULT '0',
  `coupon_id` int(11) DEFAULT NULL,
  `new_price` varchar(250) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_active` enum('0','1') NOT NULL DEFAULT '0',
  `mobile_banner` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `offer`
--

INSERT INTO `offer` (`id`, `offer_type`, `title`, `main_title`, `banner`, `start_date`, `end_date`, `fixed`, `is_product`, `product_id`, `category_id`, `coupon_id`, `new_price`, `created_at`, `updated_at`, `is_active`, `mobile_banner`) VALUES
(1, '1', 'test', 'test', 'pKltx6gmRe1672403056.jpeg', 'December 08,2022', 'December 23,2022', '12', '1', NULL, 1, NULL, NULL, '2022-12-30 12:24:16', '2022-12-30 12:24:16', '0', 'EaYnWi5CrF1672403056.jpeg'),
(2, '2', 'title', 'main', 'cidsBUToUN1672404331.jpeg', 'December 30,2022', 'December 31,2022', NULL, '2', 1, NULL, NULL, '7', '2022-12-30 12:45:31', '2022-12-30 12:45:31', '0', '');

-- --------------------------------------------------------

--
-- Table structure for table `options`
--

CREATE TABLE `options` (
  `id` int(11) NOT NULL,
  `name` varchar(250) DEFAULT NULL,
  `type` enum('1','2','3','4') NOT NULL DEFAULT '1' COMMENT '1=>dropdown 2=>checkbox,3=>radio button,4=>multiple select',
  `is_required` enum('0','1') NOT NULL DEFAULT '0' COMMENT '0=>not required,1=>required',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` enum('0','1') NOT NULL COMMENT '0=>not deleted,1=>deleted'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `options`
--

INSERT INTO `options` (`id`, `name`, `type`, `is_required`, `created_at`, `updated_at`, `is_deleted`) VALUES
(1, 'option', '1', '1', '2022-12-30 12:18:54', '2022-12-30 12:18:54', '0'),
(2, 'option2', '3', '0', '2022-12-30 12:34:17', '2022-12-30 12:34:17', '0');

-- --------------------------------------------------------

--
-- Table structure for table `option_values`
--

CREATE TABLE `option_values` (
  `id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `label` varchar(250) NOT NULL,
  `price` varchar(250) DEFAULT NULL,
  `price_type` enum('1','2') NOT NULL DEFAULT '1' COMMENT '1=>fixed,2=>percentage',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `option_values`
--

INSERT INTO `option_values` (`id`, `option_id`, `label`, `price`, `price_type`, `created_at`, `updated_at`) VALUES
(1, 1, '12', '11', '1', '2022-12-30 12:18:55', '2022-12-30 12:18:55'),
(2, 2, '12', '1111', '1', '2022-12-30 12:34:17', '2022-12-30 12:34:17');

-- --------------------------------------------------------

--
-- Table structure for table `order_data`
--

CREATE TABLE `order_data` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` varchar(250) DEFAULT NULL,
  `total_amount` varchar(500) DEFAULT NULL,
  `tax_name` varchar(250) DEFAULT NULL,
  `tax_charges` varchar(250) DEFAULT NULL,
  `option_name` varchar(5000) DEFAULT NULL,
  `label` varchar(5000) DEFAULT NULL,
  `option_price` varchar(5000) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `order_record`
--

CREATE TABLE `order_record` (
  `id` int(11) NOT NULL,
  `orderdate` varchar(250) DEFAULT NULL,
  `notify` enum('1','0') NOT NULL DEFAULT '1',
  `pending_datetime` varchar(250) DEFAULT NULL,
  `onhold_datetime` varchar(250) DEFAULT NULL,
  `outfordelivery_datetime` varchar(250) DEFAULT NULL,
  `completed_datetime` varchar(250) DEFAULT NULL,
  `cancel_datetime` varchar(250) DEFAULT NULL,
  `refund_datetime` varchar(250) DEFAULT NULL,
  `shipping_method` varchar(250) DEFAULT NULL,
  `payment_method` varchar(250) DEFAULT NULL COMMENT '1=>paypal,2=>stripe,3=>case on delivery',
  `user_id` int(11) NOT NULL,
  `billing_first_name` varchar(250) DEFAULT NULL,
  `billing_last_name` varchar(250) DEFAULT NULL,
  `billing_address` varchar(250) DEFAULT NULL,
  `billing_city` varchar(250) DEFAULT NULL,
  `billing_pincode` varchar(50) DEFAULT NULL,
  `phone` varchar(250) DEFAULT NULL,
  `email` varchar(250) DEFAULT NULL,
  `to_ship` enum('0','1') DEFAULT '0' COMMENT '0=>same address,1=>different address',
  `notes` varchar(500) DEFAULT NULL,
  `shipping_city` varchar(250) DEFAULT NULL,
  `shipping_pincode` varchar(250) DEFAULT NULL,
  `shipping_first_name` varchar(250) DEFAULT NULL,
  `shipping_last_name` varchar(250) DEFAULT NULL,
  `shipping_address` varchar(250) DEFAULT NULL,
  `subtotal` varchar(250) DEFAULT NULL,
  `shipping_charge` varchar(250) DEFAULT NULL,
  `is_freeshipping` enum('0','1') NOT NULL DEFAULT '0' COMMENT '0=>no free shipping,1=>yes free shipping',
  `taxes_charge` varchar(250) DEFAULT NULL,
  `total` varchar(250) DEFAULT NULL,
  `coupon_code` varchar(250) DEFAULT NULL,
  `coupon_price` varchar(250) DEFAULT NULL,
  `order_status` enum('1','2','3','5','6','7','4') NOT NULL DEFAULT '1' COMMENT 'processing=>1,on_hold=>2,pending=>3,out_of_deliverd=>4,completed=>5,canceled=>6,refuned=>7',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `charges_id` varchar(250) DEFAULT NULL,
  `paypal_payment_Id` varchar(250) DEFAULT NULL,
  `paypal_token` varchar(250) DEFAULT NULL,
  `paypal_payer_ID` varchar(250) DEFAULT NULL,
  `processing_datetime` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `order_response`
--

CREATE TABLE `order_response` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `desc` varchar(5000) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `page_name` varchar(500) DEFAULT NULL,
  `description` varchar(5000) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `page_name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'About Us', '<p style=\"text-align:center\"><span style=\"font-size:20px\"><strong>The History</strong></span></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>The&nbsp;History of Website&nbsp;and Cooks gives further intimation on Mr. Boulanger&#39;s usual menu, stating confidently that &quot;Boulanger served salted poultry and fresh eggs, all presented without a tablecloth on small marble tables&quot;. Numerous commentators have also referred to the supposed restaurant owner&#39;s eccentric habit of touting for custom outside his establishment, dressed in aristocratic fashion and brandishing a sword</p>\r\n\r\n<p style=\"text-align:justify\">According to Miss Spang, there is not a shred of evidence for any of it. She sai<img alt=\"\" src=\"http://192.168.1.118/project/ecommerce_new/Ecommerce/images/bag-about.png\" style=\"float:right; height:202px; width:200px\" />d: These legends just get passed on by hearsay and then spiral out of control. Her interest in dates back to a history of food seminar in Paris in the mid-1990s</p>', '2019-10-17 17:14:11', '2020-01-01 03:52:55'),
(2, 'Terms Of Condition', NULL, '2019-10-17 17:14:12', '2019-10-17 17:14:12'),
(3, 'Help & Support', NULL, '2019-10-17 17:14:12', '2019-10-17 17:14:12'),
(4, 'Contact Us', NULL, '2019-10-17 17:14:12', '2019-10-17 17:14:12');

-- --------------------------------------------------------

--
-- Table structure for table `payment_method`
--

CREATE TABLE `payment_method` (
  `id` int(11) NOT NULL,
  `status` enum('1','0') DEFAULT NULL,
  `label` varchar(250) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `payment_key` varchar(250) DEFAULT NULL,
  `payment_secret` varchar(250) DEFAULT NULL,
  `payment_mode` enum('2','1','0') DEFAULT '0' COMMENT '1=>sandbox,2=>live',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `payment_method`
--

INSERT INTO `payment_method` (`id`, `status`, `label`, `description`, `payment_key`, `payment_secret`, `payment_mode`, `created_at`, `updated_at`) VALUES
(1, '0', 'Paypal', 'Pay via your PayPal account.', 'AaT793pjARjOWkXpWaOd45lGARUMRN9pr8seE5c-AJpQBSJS1H6Z44rUPSEWYPpO7J7iF1Hu0N-MqnPx', 'ECRNnl-2t-Rli34RbdQiMHOHkyzwomvbo8mdj3kGrTL8N5lvlfPjSq7DfuArz4zksW0T9TxB5ifjY4HC', '2', '2019-10-25 10:12:03', '2020-04-22 20:44:18'),
(2, '0', 'Stripe', 'Pay via credit or debit card.', 'pk_test_yFUNiYsEESF7QBY0jcZoYK9j00yHumvXho', 'sk_test_H4cvZ6S2eX8vFFDdZCk4oNvt00RMnplVS4', '0', '2019-10-25 10:12:03', '2020-04-22 20:44:22'),
(3, '1', 'Cash On Delivery', 'Pay with cash upon delivery.', NULL, NULL, '0', '2019-10-25 10:12:03', '2020-03-22 16:57:36');

-- --------------------------------------------------------

--
-- Table structure for table `persistences`
--

CREATE TABLE `persistences` (
  `id` int(10) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `persistences`
--

INSERT INTO `persistences` (`id`, `user_id`, `code`, `created_at`, `updated_at`) VALUES
(1, 1, 'nQ70YAWPfPKHcqHACa78cjN2tjWAa333', '2022-12-30 03:36:23', '2022-12-30 03:36:23'),
(2, 1, 'vFWp1SUNTe6Vjqj92at1wMDJgwAgvBid', '2022-12-30 04:22:37', '2022-12-30 04:22:37'),
(4, 1, 'Xo0o2YkDnyItBCfxbLhUCKUBqEK8I1ql', '2023-01-02 02:23:26', '2023-01-02 02:23:26');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(250) DEFAULT NULL,
  `color_name` varchar(250) DEFAULT NULL,
  `product_color` varchar(250) DEFAULT NULL,
  `description` varchar(5000) DEFAULT NULL,
  `category` int(11) DEFAULT NULL,
  `subcategory` int(11) DEFAULT NULL,
  `brand` int(11) DEFAULT NULL,
  `tax_class` enum('0','1') NOT NULL DEFAULT '0' COMMENT '0=>no text,1=>text',
  `status` enum('0','1') NOT NULL DEFAULT '1' COMMENT '0=>not enable,1=>enable',
  `MRP` text,
  `price` text,
  `special_price` text,
  `special_price_start` varchar(50) DEFAULT NULL,
  `special_price_to` varchar(50) DEFAULT NULL,
  `sku` varchar(50) DEFAULT NULL,
  `inventory` enum('0','1') NOT NULL DEFAULT '0' COMMENT '0=>not trackable,1=>trackable',
  `stock` enum('0','1') NOT NULL DEFAULT '1' COMMENT '0=>not stock,1=>stock',
  `basic_image` varchar(250) DEFAULT NULL,
  `additional_image` varchar(1500) DEFAULT NULL,
  `url` varchar(100) DEFAULT NULL,
  `meta_title` varchar(100) DEFAULT NULL,
  `meta_keyword` varchar(250) DEFAULT NULL,
  `meta_description` varchar(5000) DEFAULT NULL,
  `related_product` varchar(500) DEFAULT NULL,
  `up_sells` varchar(500) DEFAULT NULL,
  `cross_sells` varchar(500) DEFAULT NULL,
  `short_description` varchar(2500) DEFAULT NULL,
  `product_new_from` varchar(100) DEFAULT NULL,
  `product_new_to` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` enum('0','1') NOT NULL DEFAULT '0' COMMENT '0=>not deleted,1=>deleted',
  `discount` text,
  `selling_price` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `color_name`, `product_color`, `description`, `category`, `subcategory`, `brand`, `tax_class`, `status`, `MRP`, `price`, `special_price`, `special_price_start`, `special_price_to`, `sku`, `inventory`, `stock`, `basic_image`, `additional_image`, `url`, `meta_title`, `meta_keyword`, `meta_description`, `related_product`, `up_sells`, `cross_sells`, `short_description`, `product_new_from`, `product_new_to`, `created_at`, `updated_at`, `is_deleted`, `discount`, `selling_price`) VALUES
(1, 'product-test', 'red', '#ff4013', '<p>Lorem Ipsum</p>', 1, 2, 1, '1', '1', '10.00', '10.00', '9.00', 'Dec 29,2022', 'Jan 05,2023', '121', '1', '1', '63aedcb763f01.png', '63aedcb764096.png', NULL, NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-12-30 12:40:57', '2022-12-30 12:42:31', '0', '10', '9.00');

-- --------------------------------------------------------

--
-- Table structure for table `product_attributes`
--

CREATE TABLE `product_attributes` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `attributeset` varchar(5000) DEFAULT NULL,
  `attribute` varchar(5000) DEFAULT NULL,
  `value` varchar(5000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `product_attributes`
--

INSERT INTO `product_attributes` (`id`, `product_id`, `created_at`, `updated_at`, `attributeset`, `attribute`, `value`) VALUES
(1, 1, '2022-12-30 12:42:46', '2022-12-30 12:42:46', 'att', 'att', '121');

-- --------------------------------------------------------

--
-- Table structure for table `product_options`
--

CREATE TABLE `product_options` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `type` varchar(250) DEFAULT NULL,
  `is_required` varchar(250) DEFAULT NULL,
  `label` varchar(1000) DEFAULT NULL,
  `price` varchar(500) DEFAULT NULL,
  `price_type` varchar(250) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `query_ans_question`
--

CREATE TABLE `query_ans_question` (
  `id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `question` varchar(1000) DEFAULT NULL,
  `answer` varchar(5000) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `question_query_topic`
--

CREATE TABLE `question_query_topic` (
  `id` int(11) NOT NULL,
  `topic` varchar(250) DEFAULT NULL,
  `page_id` enum('1','2') NOT NULL DEFAULT '1' COMMENT '1=>help&support,2=>terms&condition',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `reminders`
--

CREATE TABLE `reminders` (
  `id` int(10) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `completed` tinyint(1) NOT NULL DEFAULT '0',
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reset_password`
--

CREATE TABLE `reset_password` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `code` varchar(250) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `review` varchar(5000) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ratting` varchar(250) DEFAULT NULL,
  `is_approved` enum('0','1') NOT NULL DEFAULT '1' COMMENT '0=>not approved,1=>approved',
  `is_deleted` enum('0','1') NOT NULL DEFAULT '0' COMMENT '0=>not deleted,1=>deleted',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `permissions` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `slug`, `name`, `permissions`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin', NULL, NULL, NULL),
(3, 'User', 'user', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `role_users`
--

CREATE TABLE `role_users` (
  `user_id` int(10) NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `seasonal_offer`
--

CREATE TABLE `seasonal_offer` (
  `id` int(11) NOT NULL,
  `title` varchar(250) DEFAULT NULL,
  `fixed_form` int(11) DEFAULT '0',
  `fixed_to` int(11) DEFAULT '0',
  `category` int(11) NOT NULL DEFAULT '0',
  `banner` varchar(500) DEFAULT NULL,
  `start_date` varchar(500) DEFAULT NULL,
  `end_date` varchar(500) DEFAULT NULL,
  `sub_category` int(11) DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_active` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `seasonal_offer`
--

INSERT INTO `seasonal_offer` (`id`, `title`, `fixed_form`, `fixed_to`, `category`, `banner`, `start_date`, `end_date`, `sub_category`, `created_at`, `updated_at`, `is_active`) VALUES
(1, 'title', 12, 10, 0, 'ZFUhb9goEF1672404364.jpeg', NULL, NULL, 0, '2022-12-30 12:46:04', '2022-12-30 12:46:04', '0');

-- --------------------------------------------------------

--
-- Table structure for table `send_notification`
--

CREATE TABLE `send_notification` (
  `id` int(11) NOT NULL,
  `msg` varchar(5000) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `sepical_category`
--

CREATE TABLE `sepical_category` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(250) NOT NULL,
  `description` varchar(5000) NOT NULL,
  `image` varchar(500) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_active` enum('0','1') NOT NULL DEFAULT '0' COMMENT '0=>not active,1=>active'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `setting`
--

CREATE TABLE `setting` (
  `id` int(11) NOT NULL,
  `is_demo` enum('0','1') NOT NULL DEFAULT '1' COMMENT '0=>live,1=>demo',
  `email` varchar(250) NOT NULL,
  `working_day` varchar(250) DEFAULT NULL,
  `helpline` varchar(250) DEFAULT NULL,
  `company_name` varchar(250) DEFAULT NULL,
  `main_feature` varchar(5000) DEFAULT NULL,
  `newsletter` varchar(250) DEFAULT NULL,
  `address` varchar(250) NOT NULL,
  `phone` varchar(250) NOT NULL,
  `default_country` int(11) DEFAULT NULL,
  `default_locale` int(250) DEFAULT NULL,
  `default_timezone` int(11) DEFAULT NULL,
  `default_currency` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `facebook_id` varchar(250) DEFAULT NULL,
  `facebook_secret` varchar(250) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `facebook_active` enum('0','1') NOT NULL DEFAULT '0',
  `google_active` enum('0','1') NOT NULL DEFAULT '0',
  `google_id` varchar(250) DEFAULT NULL,
  `google_secret` varchar(250) DEFAULT NULL,
  `customer_reg_email` enum('0','1') NOT NULL DEFAULT '0' COMMENT '1=>is active,0=>not active',
  `customer_order_status` enum('0','1') NOT NULL DEFAULT '0' COMMENT '0=>not active,1=>active',
  `admin_order_mail` enum('0','1') NOT NULL DEFAULT '0' COMMENT '1=>is active,0=>not active',
  `android_api_key` varchar(500) DEFAULT NULL,
  `iphone_api_key` varchar(500) DEFAULT NULL,
  `is_web` enum('1','2') NOT NULL DEFAULT '1' COMMENT '1=>web,2=>admin',
  `logo` varchar(500) DEFAULT NULL,
  `is_rtl` enum('0','1') NOT NULL DEFAULT '0' COMMENT '0=>ltl,1=>rtl',
  `set_show` enum('0','1') DEFAULT '0' COMMENT '0=>show,1=>hide'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `setting`
--

INSERT INTO `setting` (`id`, `is_demo`, `email`, `working_day`, `helpline`, `company_name`, `main_feature`, `newsletter`, `address`, `phone`, `default_country`, `default_locale`, `default_timezone`, `default_currency`, `facebook_id`, `facebook_secret`, `created_at`, `updated_at`, `facebook_active`, `google_active`, `google_id`, `google_secret`, `customer_reg_email`, `customer_order_status`, `admin_order_mail`, `android_api_key`, `iphone_api_key`, `is_web`, `logo`, `is_rtl`, `set_show`) VALUES
(1, '0', 'inquiry@shopapp.com', 'Mon-Sun, 9:00 AM to 5:00 PM', '121', 'Freaktemplate', 'Super Fast Mangeto Theme \n1st Fully Working Ajax Theme 20 \nUniq Home page Layouts\n Power admin Panel Mobile\n & Retina Optimized', 'Get all the latest information on Events,Sales and Offers. Sign up for newsletter today.', '343 Business Place, Suite 314, Seattle, WA 98112', '(+555) 555-1234', 18, 15, 67, 'BBD - $', '319647552010214', '319647552010214', '2019-10-19 12:05:08', '2020-05-13 10:34:45', '0', '0', '6856565656', '154545485468', '0', '1', '1', 'AAAAJtKdMDs:APA91bEOuNmwIgWhrc1URrrZHcKrN3i1RYOR7HkE0fYn7AILxLx-Pwzi5pSPowgZ9o0cup783l7VBQMkwyONSCLFxg1_1BzKmhrwaEjaDYmUOlkJ2d83P9BPN08l-g5nWsqj0Luj6lbU', 'egdfgfgfgf', '2', 'shop.png', '0', '0');

-- --------------------------------------------------------

--
-- Table structure for table `shipping`
--

CREATE TABLE `shipping` (
  `id` int(11) NOT NULL,
  `label` varchar(250) NOT NULL,
  `cost` varchar(250) NOT NULL,
  `is_enable` enum('1','0') NOT NULL COMMENT '1=>enable,0=>not enable',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `shipping`
--

INSERT INTO `shipping` (`id`, `label`, `cost`, `is_enable`, `created_at`, `updated_at`) VALUES
(1, 'Home Delivery', '10', '1', '2019-10-19 11:18:28', '2020-05-08 05:13:28'),
(2, 'Local Pickup', '0.00', '1', '2019-10-19 11:18:28', '2020-05-31 23:09:44');

-- --------------------------------------------------------

--
-- Table structure for table `taxes_list`
--

CREATE TABLE `taxes_list` (
  `id` int(11) NOT NULL,
  `tax_name` varchar(250) DEFAULT NULL,
  `base_on` enum('1','2') NOT NULL DEFAULT '1' COMMENT '1=>billing,2=>shipping',
  `rate` varchar(250) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `taxes_list`
--

INSERT INTO `taxes_list` (`id`, `tax_name`, `base_on`, `rate`, `created_at`, `updated_at`) VALUES
(1, 'GST', '1', '21', '2022-12-30 12:40:21', '2022-12-30 12:40:21');

-- --------------------------------------------------------

--
-- Table structure for table `tbcart`
--

CREATE TABLE `tbcart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `option` varchar(500) DEFAULT NULL,
  `label` varchar(500) DEFAULT NULL,
  `price_product` varchar(250) DEFAULT NULL,
  `qty` varchar(250) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `product_id` int(11) NOT NULL,
  `tax` varchar(250) DEFAULT NULL,
  `tax_name` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `throttle`
--

CREATE TABLE `throttle` (
  `id` int(10) NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `throttle`
--

INSERT INTO `throttle` (`id`, `user_id`, `type`, `ip`, `created_at`, `updated_at`) VALUES
(1, NULL, 'global', NULL, '2022-12-30 03:36:17', '2022-12-30 03:36:17'),
(2, NULL, 'ip', '132.154.52.204', '2022-12-30 03:36:17', '2022-12-30 03:36:17'),
(3, 1, 'user', NULL, '2022-12-30 03:36:17', '2022-12-30 03:36:17'),
(4, NULL, 'global', NULL, '2022-12-30 04:22:31', '2022-12-30 04:22:31'),
(5, NULL, 'ip', '103.19.199.190', '2022-12-30 04:22:31', '2022-12-30 04:22:31'),
(6, 1, 'user', NULL, '2022-12-30 04:22:31', '2022-12-30 04:22:31');

-- --------------------------------------------------------

--
-- Table structure for table `token_data`
--

CREATE TABLE `token_data` (
  `id` int(11) NOT NULL,
  `token` varchar(250) DEFAULT NULL,
  `type` enum('1','2') NOT NULL COMMENT '1=>android,2=>ios',
  `user_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `update_cron`
--

CREATE TABLE `update_cron` (
  `id` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `update_cron`
--

INSERT INTO `update_cron` (`id`, `date`, `created_at`, `updated_at`) VALUES
(1, '2023-01-02', '2019-12-18 14:29:31', '2023-01-02 07:23:10');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) NOT NULL,
  `soical_id` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `login_type` enum('1','2','3') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1' COMMENT '1=>email,2=>facebook,3=>google',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `billing_address` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_address` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_pic` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `permissions` text COLLATE utf8mb4_unicode_ci,
  `last_login` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_type` enum('1','2') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1' COMMENT '1=>user,2=>admin',
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1' COMMENT '0=>not active,1=>active',
  `is_email_verified` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `soical_id`, `login_type`, `email`, `billing_address`, `shipping_address`, `profile_pic`, `password`, `permissions`, `last_login`, `first_name`, `address`, `phone`, `user_type`, `last_name`, `is_active`, `is_email_verified`, `created_at`, `updated_at`, `remember_token`) VALUES
(1, NULL, '1', 'admin@gmail.com', NULL, NULL, 'admin.jpg', '$2y$10$SqJ0wo4r1h3nCsYQW7kYOer5AhBESy3IvUOEP97g3Ga8DV6S.1XUS', NULL, '2023-01-02 07:53:26', 'redixbit', NULL, NULL, '2', 'Admin', '1', '1', NULL, '2023-01-02 02:23:26', NULL),
(2, NULL, '1', 'test@gmail.com', NULL, NULL, NULL, '1111', NULL, NULL, 'test', 'test', '1212', '1', NULL, '1', '1', '2022-12-30 06:56:34', '2022-12-30 06:56:34', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activations`
--
ALTER TABLE `activations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `banner`
--
ALTER TABLE `banner`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `brand`
--
ALTER TABLE `brand`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `complain`
--
ALTER TABLE `complain`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_us`
--
ALTER TABLE `contact_us`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `country`
--
ALTER TABLE `country`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `coupon`
--
ALTER TABLE `coupon`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `deals`
--
ALTER TABLE `deals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feature_product`
--
ALTER TABLE `feature_product`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ltm_translations`
--
ALTER TABLE `ltm_translations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news_letter`
--
ALTER TABLE `news_letter`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `offer`
--
ALTER TABLE `offer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `option_values`
--
ALTER TABLE `option_values`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_data`
--
ALTER TABLE `order_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_record`
--
ALTER TABLE `order_record`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_response`
--
ALTER TABLE `order_response`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_method`
--
ALTER TABLE `payment_method`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `persistences`
--
ALTER TABLE `persistences`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_attributes`
--
ALTER TABLE `product_attributes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_options`
--
ALTER TABLE `product_options`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `query_ans_question`
--
ALTER TABLE `query_ans_question`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `question_query_topic`
--
ALTER TABLE `question_query_topic`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reminders`
--
ALTER TABLE `reminders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reset_password`
--
ALTER TABLE `reset_password`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role_users`
--
ALTER TABLE `role_users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `seasonal_offer`
--
ALTER TABLE `seasonal_offer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `send_notification`
--
ALTER TABLE `send_notification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sepical_category`
--
ALTER TABLE `sepical_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting`
--
ALTER TABLE `setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shipping`
--
ALTER TABLE `shipping`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `taxes_list`
--
ALTER TABLE `taxes_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbcart`
--
ALTER TABLE `tbcart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `throttle`
--
ALTER TABLE `throttle`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `token_data`
--
ALTER TABLE `token_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `update_cron`
--
ALTER TABLE `update_cron`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activations`
--
ALTER TABLE `activations`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `banner`
--
ALTER TABLE `banner`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `brand`
--
ALTER TABLE `brand`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `complain`
--
ALTER TABLE `complain`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contact_us`
--
ALTER TABLE `contact_us`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `country`
--
ALTER TABLE `country`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=240;

--
-- AUTO_INCREMENT for table `coupon`
--
ALTER TABLE `coupon`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `deals`
--
ALTER TABLE `deals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `feature_product`
--
ALTER TABLE `feature_product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `languages`
--
ALTER TABLE `languages`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=136;

--
-- AUTO_INCREMENT for table `ltm_translations`
--
ALTER TABLE `ltm_translations`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=610;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `news_letter`
--
ALTER TABLE `news_letter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `offer`
--
ALTER TABLE `offer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `options`
--
ALTER TABLE `options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `option_values`
--
ALTER TABLE `option_values`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `order_data`
--
ALTER TABLE `order_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_record`
--
ALTER TABLE `order_record`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_response`
--
ALTER TABLE `order_response`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payment_method`
--
ALTER TABLE `payment_method`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `persistences`
--
ALTER TABLE `persistences`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `product_attributes`
--
ALTER TABLE `product_attributes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `product_options`
--
ALTER TABLE `product_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `query_ans_question`
--
ALTER TABLE `query_ans_question`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `question_query_topic`
--
ALTER TABLE `question_query_topic`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reminders`
--
ALTER TABLE `reminders`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reset_password`
--
ALTER TABLE `reset_password`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `role_users`
--
ALTER TABLE `role_users`
  MODIFY `user_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `seasonal_offer`
--
ALTER TABLE `seasonal_offer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `send_notification`
--
ALTER TABLE `send_notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sepical_category`
--
ALTER TABLE `sepical_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting`
--
ALTER TABLE `setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `shipping`
--
ALTER TABLE `shipping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `taxes_list`
--
ALTER TABLE `taxes_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbcart`
--
ALTER TABLE `tbcart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `throttle`
--
ALTER TABLE `throttle`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `token_data`
--
ALTER TABLE `token_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `update_cron`
--
ALTER TABLE `update_cron`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
