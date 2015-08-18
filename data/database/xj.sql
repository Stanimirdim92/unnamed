-- phpMyAdmin SQL Dump
-- version 4.0.4.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Aug 18, 2015 at 09:28 PM
-- Server version: 5.5.32-log
-- PHP Version: 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `xj`
--
CREATE DATABASE IF NOT EXISTS `xj` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `xj`;

-- --------------------------------------------------------

--
-- Table structure for table `administrator`
--

CREATE TABLE IF NOT EXISTS `administrator` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `administrator`
--

INSERT INTO `administrator` (`id`, `user`) VALUES
(1, 19),
(2, 3);

-- --------------------------------------------------------

--
-- Table structure for table `adminmenu`
--

CREATE TABLE IF NOT EXISTS `adminmenu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `caption` varchar(50) NOT NULL,
  `menuOrder` int(11) NOT NULL,
  `description` varchar(150) NOT NULL,
  `parent` int(11) NOT NULL DEFAULT '0',
  `controller` varchar(50) NOT NULL,
  `action` varchar(50) NOT NULL,
  `class` varchar(50) NOT NULL,
  `advanced` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=71 ;

--
-- Dumping data for table `adminmenu`
--

INSERT INTO `adminmenu` (`id`, `caption`, `menuOrder`, `description`, `parent`, `controller`, `action`, `class`, `advanced`) VALUES
(69, 'test', 0, 'desc', 0, 'term', 'index', 'fa-file-o', 0),
(70, 'subtest', 1, 'add admin menu', 69, 'adminmenu', 'add', 'fa-plus', 0);

-- --------------------------------------------------------

--
-- Table structure for table `content`
--

CREATE TABLE IF NOT EXISTS `content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `preview` varchar(100) DEFAULT NULL,
  `text` text,
  `menuOrder` int(11) NOT NULL DEFAULT '0',
  `type` smallint(6) NOT NULL DEFAULT '0',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `menu` int(11) NOT NULL DEFAULT '0',
  `language` smallint(6) NOT NULL DEFAULT '1',
  `titleLink` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

--
-- Dumping data for table `content`
--

INSERT INTO `content` (`id`, `title`, `preview`, `text`, `menuOrder`, `type`, `date`, `menu`, `language`, `titleLink`) VALUES
(1, 'Yahoo &amp; Bing', 'yahoo-bing.png', 'text', 1, 0, '2014-10-23 10:33:39', 20, 1, NULL),
(2, 'Google', 'google.png', '', 2, 0, '2014-10-23 10:43:36', 21, 1, NULL),
(5, 'Best local position', '', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 1, 0, '2014-10-23 17:12:46', 9, 1, 'best-local-position'),
(6, 'Create impressive reports', '', '', 1, 0, '2014-10-24 09:30:35', 7, 1, NULL),
(7, 'Everything you need', '', '', 1, 0, '2014-10-24 09:37:12', 4, 1, NULL),
(8, 'Get high search ranking', '', '', 1, 0, '2014-10-24 09:49:03', 5, 1, NULL),
(9, 'Innovative technology', '', 'sfgdfhdfhddghdghdhdgh', 1, 0, '2014-10-24 09:49:57', 8, 1, 'innovative-technology'),
(11, 'Track of your positions', '', 'pak test', 1, 0, '2014-10-24 09:51:50', 6, 1, 'track-of-your-positions'),
(13, 'Why use test', '', 'test4e2333333333333333333333333', 16, 0, '0000-00-00 00:00:00', 11, 1, 'why-use-test'),
(16, 'lorem news', '', '', 1, 1, '2014-10-24 10:45:27', 0, 1, 'lorem-ipsum'),
(17, 'trerwt', '', '', 1, 1, '2014-10-24 11:13:41', 0, 1, 'trerwt'),
(18, 'sfgsg', '', '', 1, 1, '2014-10-24 11:53:43', 0, 1, 'sfgsg'),
(19, 'wregdth', '', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 1, 1, '2014-10-24 12:50:10', 0, 1, 'wregdth'),
(20, 'Video 2013-10-02 09:38:08', '', 'aaaaaaaaaaaaaas  d d d d d', 1, 0, '2014-08-07 00:00:00', 17, 1, 'video-20131002-093808');

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE IF NOT EXISTS `countries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_code` varchar(2) NOT NULL DEFAULT '',
  `country_name` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=243 ;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `country_code`, `country_name`) VALUES
(1, 'US', 'United States'),
(2, 'CA', 'Canada'),
(3, 'AF', 'Afghanistan'),
(4, 'AL', 'Albania'),
(5, 'DZ', 'Algeria'),
(6, 'DS', 'American Samoa'),
(7, 'AD', 'Andorra'),
(8, 'AO', 'Angola'),
(9, 'AI', 'Anguilla'),
(10, 'AQ', 'Antarctica'),
(11, 'AG', 'Antigua and/or Barbuda'),
(12, 'AR', 'Argentina'),
(13, 'AM', 'Armenia'),
(14, 'AW', 'Aruba'),
(15, 'AU', 'Australia'),
(16, 'AT', 'Austria'),
(17, 'AZ', 'Azerbaijan'),
(18, 'BS', 'Bahamas'),
(19, 'BH', 'Bahrain'),
(20, 'BD', 'Bangladesh'),
(21, 'BB', 'Barbados'),
(22, 'BY', 'Belarus'),
(23, 'BE', 'Belgium'),
(24, 'BZ', 'Belize'),
(25, 'BJ', 'Benin'),
(26, 'BM', 'Bermuda'),
(27, 'BT', 'Bhutan'),
(28, 'BO', 'Bolivia'),
(29, 'BA', 'Bosnia and Herzegovina'),
(30, 'BW', 'Botswana'),
(31, 'BV', 'Bouvet Island'),
(32, 'BR', 'Brazil'),
(33, 'IO', 'British lndian Ocean Territory'),
(34, 'BN', 'Brunei Darussalam'),
(35, 'BG', 'Bulgaria'),
(36, 'BF', 'Burkina Faso'),
(37, 'BI', 'Burundi'),
(38, 'KH', 'Cambodia'),
(39, 'CM', 'Cameroon'),
(40, 'CV', 'Cape Verde'),
(41, 'KY', 'Cayman Islands'),
(42, 'CF', 'Central African Republic'),
(43, 'TD', 'Chad'),
(44, 'CL', 'Chile'),
(45, 'CN', 'China'),
(46, 'CX', 'Christmas Island'),
(47, 'CC', 'Cocos (Keeling) Islands'),
(48, 'CO', 'Colombia'),
(49, 'KM', 'Comoros'),
(50, 'CG', 'Congo'),
(51, 'CK', 'Cook Islands'),
(52, 'CR', 'Costa Rica'),
(53, 'HR', 'Croatia (Hrvatska)'),
(54, 'CU', 'Cuba'),
(55, 'CY', 'Cyprus'),
(56, 'CZ', 'Czech Republic'),
(57, 'DK', 'Denmark'),
(58, 'DJ', 'Djibouti'),
(59, 'DM', 'Dominica'),
(60, 'DO', 'Dominican Republic'),
(61, 'TP', 'East Timor'),
(62, 'EC', 'Ecuador'),
(63, 'EG', 'Egypt'),
(64, 'SV', 'El Salvador'),
(65, 'GQ', 'Equatorial Guinea'),
(66, 'ER', 'Eritrea'),
(67, 'EE', 'Estonia'),
(68, 'ET', 'Ethiopia'),
(69, 'FK', 'Falkland Islands (Malvinas)'),
(70, 'FO', 'Faroe Islands'),
(71, 'FJ', 'Fiji'),
(72, 'FI', 'Finland'),
(73, 'FR', 'France'),
(74, 'FX', 'France, Metropolitan'),
(75, 'GF', 'French Guiana'),
(76, 'PF', 'French Polynesia'),
(77, 'TF', 'French Southern Territories'),
(78, 'GA', 'Gabon'),
(79, 'GM', 'Gambia'),
(80, 'GE', 'Georgia'),
(81, 'DE', 'Germany'),
(82, 'GH', 'Ghana'),
(83, 'GI', 'Gibraltar'),
(84, 'GR', 'Greece'),
(85, 'GL', 'Greenland'),
(86, 'GD', 'Grenada'),
(87, 'GP', 'Guadeloupe'),
(88, 'GU', 'Guam'),
(89, 'GT', 'Guatemala'),
(90, 'GN', 'Guinea'),
(91, 'GW', 'Guinea-Bissau'),
(92, 'GY', 'Guyana'),
(93, 'HT', 'Haiti'),
(94, 'HM', 'Heard and Mc Donald Islands'),
(95, 'HN', 'Honduras'),
(96, 'HK', 'Hong Kong'),
(97, 'HU', 'Hungary'),
(98, 'IS', 'Iceland'),
(99, 'IN', 'India'),
(100, 'ID', 'Indonesia'),
(101, 'IR', 'Iran (Islamic Republic of)'),
(102, 'IQ', 'Iraq'),
(103, 'IE', 'Ireland'),
(104, 'IL', 'Israel'),
(105, 'IT', 'Italy'),
(106, 'CI', 'Ivory Coast'),
(107, 'JM', 'Jamaica'),
(108, 'JP', 'Japan'),
(109, 'JO', 'Jordan'),
(110, 'KZ', 'Kazakhstan'),
(111, 'KE', 'Kenya'),
(112, 'KI', 'Kiribati'),
(113, 'KP', 'Korea, Democratic People''s Republic of'),
(114, 'KR', 'Korea, Republic of'),
(115, 'XK', 'Kosovo'),
(116, 'KW', 'Kuwait'),
(117, 'KG', 'Kyrgyzstan'),
(118, 'LA', 'Lao People''s Democratic Republic'),
(119, 'LV', 'Latvia'),
(120, 'LB', 'Lebanon'),
(121, 'LS', 'Lesotho'),
(122, 'LR', 'Liberia'),
(123, 'LY', 'Libyan Arab Jamahiriya'),
(124, 'LI', 'Liechtenstein'),
(125, 'LT', 'Lithuania'),
(126, 'LU', 'Luxembourg'),
(127, 'MO', 'Macau'),
(128, 'MK', 'Macedonia'),
(129, 'MG', 'Madagascar'),
(130, 'MW', 'Malawi'),
(131, 'MY', 'Malaysia'),
(132, 'MV', 'Maldives'),
(133, 'ML', 'Mali'),
(134, 'MT', 'Malta'),
(135, 'MH', 'Marshall Islands'),
(136, 'MQ', 'Martinique'),
(137, 'MR', 'Mauritania'),
(138, 'MU', 'Mauritius'),
(139, 'TY', 'Mayotte'),
(140, 'MX', 'Mexico'),
(141, 'FM', 'Micronesia, Federated States of'),
(142, 'MD', 'Moldova, Republic of'),
(143, 'MC', 'Monaco'),
(144, 'MN', 'Mongolia'),
(145, 'ME', 'Montenegro'),
(146, 'MS', 'Montserrat'),
(147, 'MA', 'Morocco'),
(148, 'MZ', 'Mozambique'),
(149, 'MM', 'Myanmar'),
(150, 'NA', 'Namibia'),
(151, 'NR', 'Nauru'),
(152, 'NP', 'Nepal'),
(153, 'NL', 'Netherlands'),
(154, 'AN', 'Netherlands Antilles'),
(155, 'NC', 'New Caledonia'),
(156, 'NZ', 'New Zealand'),
(157, 'NI', 'Nicaragua'),
(158, 'NE', 'Niger'),
(159, 'NG', 'Nigeria'),
(160, 'NU', 'Niue'),
(161, 'NF', 'Norfork Island'),
(162, 'MP', 'Northern Mariana Islands'),
(163, 'NO', 'Norway'),
(164, 'OM', 'Oman'),
(165, 'PK', 'Pakistan'),
(166, 'PW', 'Palau'),
(167, 'PA', 'Panama'),
(168, 'PG', 'Papua New Guinea'),
(169, 'PY', 'Paraguay'),
(170, 'PE', 'Peru'),
(171, 'PH', 'Philippines'),
(172, 'PN', 'Pitcairn'),
(173, 'PL', 'Poland'),
(174, 'PT', 'Portugal'),
(175, 'PR', 'Puerto Rico'),
(176, 'QA', 'Qatar'),
(177, 'RE', 'Reunion'),
(178, 'RO', 'Romania'),
(179, 'RU', 'Russian Federation'),
(180, 'RW', 'Rwanda'),
(181, 'KN', 'Saint Kitts and Nevis'),
(182, 'LC', 'Saint Lucia'),
(183, 'VC', 'Saint Vincent and the Grenadines'),
(184, 'WS', 'Samoa'),
(185, 'SM', 'San Marino'),
(186, 'ST', 'Sao Tome and Principe'),
(187, 'SA', 'Saudi Arabia'),
(188, 'SN', 'Senegal'),
(189, 'RS', 'Serbia'),
(190, 'SC', 'Seychelles'),
(191, 'SL', 'Sierra Leone'),
(192, 'SG', 'Singapore'),
(193, 'SK', 'Slovakia'),
(194, 'SI', 'Slovenia'),
(195, 'SB', 'Solomon Islands'),
(196, 'SO', 'Somalia'),
(197, 'ZA', 'South Africa'),
(198, 'GS', 'South Georgia South Sandwich Islands'),
(199, 'ES', 'Spain'),
(200, 'LK', 'Sri Lanka'),
(201, 'SH', 'St. Helena'),
(202, 'PM', 'St. Pierre and Miquelon'),
(203, 'SD', 'Sudan'),
(204, 'SR', 'Suriname'),
(205, 'SJ', 'Svalbarn and Jan Mayen Islands'),
(206, 'SZ', 'Swaziland'),
(207, 'SE', 'Sweden'),
(208, 'CH', 'Switzerland'),
(209, 'SY', 'Syrian Arab Republic'),
(210, 'TW', 'Taiwan'),
(211, 'TJ', 'Tajikistan'),
(212, 'TZ', 'Tanzania, United Republic of'),
(213, 'TH', 'Thailand'),
(214, 'TG', 'Togo'),
(215, 'TK', 'Tokelau'),
(216, 'TO', 'Tonga'),
(217, 'TT', 'Trinidad and Tobago'),
(218, 'TN', 'Tunisia'),
(219, 'TR', 'Turkey'),
(220, 'TM', 'Turkmenistan'),
(221, 'TC', 'Turks and Caicos Islands'),
(222, 'TV', 'Tuvalu'),
(223, 'UG', 'Uganda'),
(224, 'UA', 'Ukraine'),
(225, 'AE', 'United Arab Emirates'),
(226, 'GB', 'United Kingdom'),
(227, 'UM', 'United States minor outlying islands'),
(228, 'UY', 'Uruguay'),
(229, 'UZ', 'Uzbekistan'),
(230, 'VU', 'Vanuatu'),
(231, 'VA', 'Vatican City State'),
(232, 'VE', 'Venezuela'),
(233, 'VN', 'Vietnam'),
(234, 'VG', 'Virgin Islands (British)'),
(235, 'VI', 'Virgin Islands (U.S.)'),
(236, 'WF', 'Wallis and Futuna Islands'),
(237, 'EH', 'Western Sahara'),
(238, 'YE', 'Yemen'),
(239, 'YU', 'Yugoslavia'),
(240, 'ZR', 'Zaire'),
(241, 'ZM', 'Zambia'),
(242, 'ZW', 'Zimbabwe');

-- --------------------------------------------------------

--
-- Table structure for table `language`
--

CREATE TABLE IF NOT EXISTS `language` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `language`
--

INSERT INTO `language` (`id`, `name`, `active`) VALUES
(1, 'en', 1),
(2, 'bg', 1),
(3, 'cn', 1),
(5, 'de', 1);

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE IF NOT EXISTS `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `caption` varchar(100) DEFAULT NULL,
  `menuOrder` int(11) NOT NULL DEFAULT '0',
  `keywords` varchar(100) DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `language` smallint(6) NOT NULL DEFAULT '1',
  `parent` int(11) NOT NULL DEFAULT '0',
  `menutype` smallint(5) NOT NULL DEFAULT '0',
  `footercolumn` int(11) NOT NULL DEFAULT '0',
  `menulink` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `parent` (`parent`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id`, `caption`, `menuOrder`, `keywords`, `description`, `language`, `parent`, `menutype`, `footercolumn`, `menulink`) VALUES
(1, 'specialno test4e', 1, 'test csrfffff', 'sssssss', 1, 0, 0, 1, 'specialno-test4e'),
(3, 'About', 1, '', '', 1, 0, 0, 1, 'about'),
(4, 'Everything you need', 1, 'test', 'test', 1, 3, 1, 1, 'everything you need'),
(5, 'Get high search ranking', 1, '', '', 1, 0, 1, 1, NULL),
(6, 'Track of your positions', 1, '', '', 1, 0, 1, 1, NULL),
(7, 'Create impressive reports', 1, '', '', 1, 0, 1, 1, NULL),
(8, 'Innovative technology', 1, '', '', 1, 3, 0, 1, 'innovative-technology'),
(9, 'Best local position', 1, '', '', 1, 0, 1, 1, 'best-local-position'),
(10, 'More traffic and clients', 1, '', '', 1, 0, 1, 1, NULL),
(11, 'Why use seo street optimizer', 1, '', '', 1, 0, 1, 1, NULL),
(12, 'Privacy Policy', 1, '', '', 1, 0, 3, 1, NULL),
(13, 'Terms &amp; Conditions', 1, '', '', 1, 0, 3, 1, NULL),
(14, 'Contact', 1, '', '', 1, 0, 3, 2, NULL),
(15, 'Distributors', 1, '', '', 1, 0, 3, 2, NULL),
(16, 'Reseller Club', 1, '', '', 1, 3, 0, 2, 'reseller club'),
(17, 'Brands', 1, '', '', 1, 0, 3, 3, NULL),
(19, 'Specials', 1, '', '', 1, 0, 0, 3, 'specials'),
(20, 'Yahoo and Bing', 1, '', '', 1, 0, 2, 1, NULL),
(23, 'teest caption', 1, 'dfg', 'sgsfgfsg', 1, 0, 0, 1, 'teest-caption'),
(24, 'wgg g eg eg', 1, 'egeg', '', 1, 0, 0, 1, 'wgg-g-eg-eg'),
(26, 'fgsfgg', 1, 'sgsgs', 'gdg', 2, 0, 0, 1, 'fgsfg'),
(27, 'aaaaaaaa', 1, 'aaaaaaaaaaaaaaaaaaa', 'aaaaaaaaaaaaaaaaaa', 1, 0, 0, 1, 'sdsga');

-- --------------------------------------------------------

--
-- Table structure for table `resetpassword`
--

CREATE TABLE IF NOT EXISTS `resetpassword` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) DEFAULT NULL,
  `token` varchar(100) DEFAULT NULL,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ip` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=32 ;

--
-- Dumping data for table `resetpassword`
--

INSERT INTO `resetpassword` (`id`, `user`, `token`, `date`, `ip`) VALUES
(1, 8, 'a7d67b91dc34906804c51da7719e46302aa89cfb92874acb1ec5a7225f92d198', '2014-10-23 12:57:19', '87.120.12.121'),
(2, 2, '', '2014-12-07 18:23:34', '127.0.0.1'),
(3, 2, ')j-๯', '2014-12-07 18:30:04', '127.0.0.1'),
(4, 2, '4', '2014-12-07 18:31:23', '127.0.0.1'),
(5, 2, 'q', '2014-12-07 18:38:13', '127.0.0.1'),
(6, 2, '', '2014-12-07 18:38:38', '127.0.0.1'),
(7, 2, 'RB', '2014-12-07 18:39:09', '127.0.0.1'),
(8, 2, '}#', '2014-12-07 18:39:25', '127.0.0.1'),
(9, 2, '', '2014-12-07 18:40:24', '127.0.0.1'),
(10, 2, '', '2014-12-07 18:43:22', '127.0.0.1'),
(11, 2, 'iEAi1opgIoi1RbydVpbhhC079mHCwoqsLXrGZKxaEGqy42CBcgvlEx0X6rIbaa9t', '2014-12-09 16:09:45', '127.0.0.1'),
(12, 2, '/PPBkpPiz/zVKrQpoymKiT0zplLoYkln+E6nMSBBynl3D16OGOp10btu5FlGFCs4', '2014-12-09 16:09:52', '127.0.0.1'),
(13, 2, '+RlXfIW2Jz0xZT0InkqlTUvnZg+uiTVGI4lpoHHq7Hl3MvrctPCEpIMk6ObIe+fj', '2014-12-10 12:57:34', '127.0.0.1'),
(14, 2, 'tTLMB3uiFj1Po4e5DmL8LBUqQjapP8jdW/I71TWlecoUZV6Q3T1ff0ULlpVaEvUn', '2015-03-03 10:25:16', '127.0.0.1'),
(15, 2, 'sHrjedYowCzXIZd70jy+SiWpx1uiPFxfH0HwGVNHEdS13Ocr3z89SS97KNtXIlcK', '2015-06-07 19:53:08', '::1'),
(16, 18, 'sgfshdfhsgfshdfhsgfshdfhsgfshdfhsgfshdfhsgfshdfhsgfshdfhsgfshdfs', '2015-06-07 19:53:55', '::1'),
(17, 18, 'lOmJqd3EM5gMWjR755Ix2ccfyb4xw4eeRU4khzKhV8xan9CKytxA92kw3BoWMvW0', '2015-08-05 11:43:48', '::1'),
(18, 18, 'V65uqIRVdo+5QApYd7N4qq7P4g7UOh7ncjrcCenTE3UpYOoeMtHzwSTpeCHb6PMk', '2015-08-05 11:44:43', '::1'),
(19, 18, 'JmVFSFT7fL8Eh836Xh2/mJl901Qlc11bmasJIHqOrRhJTXM0JGkS0Y36veqvDmyq', '2015-08-05 12:15:08', '::1'),
(20, 21, 'X5uEs+83ob5XuVkzyzwuzZKeoJ8iD/QYdTz800/fKu1YOaduX9iQktcfUy9Funn2', '2015-08-05 12:49:46', '::1'),
(21, 21, 'gK/DFTJCvv0x8Qd3JRboBqp9QO4jZXZfkX+rBRAIKDm5XWjOic5HYcBSFFFa786A', '2015-08-08 16:30:36', '::1'),
(22, 21, 'CAbuHOYVFDJQHRu8VZZjGoD4yX5CkVr9qyO3K4/iiqiYHXnVJb/T1ecrhq7k0gh5', '2015-08-08 16:34:23', '::1'),
(23, 21, '+yr75m6VkOtoYq8Vkvz1tNiCkAp6Z1omDdLgYg0Sfz1e0f7jxQGc60tMDEOcznhp', '2015-08-08 16:39:25', '::1'),
(24, 21, 'diLlmC++FBqsfia9z/VlnwldqcUnfYPieN66NlEAHb4R3rxC8wUiK/EDuAK8M5Zs', '2015-08-08 16:40:14', '::1'),
(25, 21, 'LB/0jBYd5TK/zG7TC44xfZ3yUM8f2OursXTM50niz3IlTCNMRq9zElc0/4QJrFMs', '2015-08-08 16:41:16', '::1'),
(26, 21, 'f5vEnV/ouBlJwaGzxcGNksPp41dyMphv/OuENbaZR9o7qEBqKHojnij0egdsWsr/', '2015-08-08 16:42:22', '::1'),
(27, 21, 'pNpeNoPyRphyazC5qa0DLGXhPOB+HRzNQyPhqHqtzF79Pl926eeSHRM+DYK8GIoI', '2015-08-08 16:43:02', '::1'),
(28, 1, 'Zmft+YOwwX8Sds54+DQ8O8oXTtSIgf0H1/fYzx4rXAfIUraa/3HfRWMrc7xd8SHQ', '2015-08-08 16:44:32', '::1'),
(29, 21, 'Ay8jwcTVa6wJmbDt1tJAVj6CMwq5wXhfF2PPKVP8Y+P6PT/vco/Lfgjcd3LKowM6', '2015-08-08 16:46:28', '::1'),
(30, 21, 'WkJTwlwfyhbwqCeMC4J1+9virAv7BdB8OcGnxT7d/pXneZCtFnTkdAq4ImvYkSuk', '2015-08-08 16:47:11', '::1'),
(31, 21, 'VTrHhVgPWI5XXyMTUImjDebtBH7Wrw8U3uhintQUlpNah36rmUB1gPRRC4CapqsQ', '2015-08-08 16:47:44', '::1');

-- --------------------------------------------------------

--
-- Table structure for table `term`
--

CREATE TABLE IF NOT EXISTS `term` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) DEFAULT NULL,
  `termcategory` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=69 ;

--
-- Dumping data for table `term`
--

INSERT INTO `term` (`id`, `name`, `termcategory`) VALUES
(1, 'REGISTRATION2', 1),
(2, 'NO_ID_SET', 1),
(3, 'TERM_NOT_FOUND', 1),
(4, 'USER_NOT_FOUND', 1),
(5, 'LANGUAGE_CLASS_NOT_FOUND', 1),
(6, 'LANGUAGE_NOT_FOUND', 1),
(7, 'ROW_NOT_FOUND', 1),
(8, 'TERMCATEGORY_NOT_FOUND', 1),
(9, 'TERMTRANSLATION_NOT_FOUND', 1),
(10, 'WRONG_USER_AND_PW', 1),
(11, 'USERNAME_EXIST', 1),
(12, 'EMAIL_EXIST', 1),
(13, 'ALREADY_EXIST', 1),
(14, 'REGISTRATION_SUCCESS', 1),
(15, 'SIGN_OUT', 1),
(16, 'SIGN_UP', 1),
(17, 'SIGN_IN', 1),
(18, 'ERROR_AUTHORIZATION', 1),
(19, 'USERCLASS_NOT_FOUND', 1),
(20, 'USERCLASS_EXIST', 1),
(21, 'BACK', 1),
(22, 'DELETE_CONFIRM_TEXT', 1),
(23, 'DETAILS', 1),
(24, 'CANCEL', 1),
(25, 'DELETE', 1),
(26, 'MODIFY', 1),
(27, 'NO', 1),
(28, 'YES', 1),
(29, 'NAME', 1),
(30, 'ACTIVE', 1),
(31, 'ADD_NEW', 1),
(32, 'ADD', 1),
(33, 'SAVE_SUCCESS', 1),
(34, 'DELETE_SUCCESS', 1),
(35, 'CLONED_SUCCESS', 1),
(36, 'HOME', 1),
(37, 'SEARCH', 1),
(38, 'EXCEL_EXPORT', 1),
(39, 'USERNAME', 1),
(40, 'EMAIL', 1),
(41, 'LAST_LOGIN', 1),
(42, 'DISABLED', 1),
(43, 'REGISTERED', 1),
(44, 'USER_CLASSES', 1),
(45, 'EXCEL_IMPORT_SUCCESS', 1),
(46, 'ADMINMENU_NOT_FOUND', 1),
(47, 'TERMTRANSLATION', 1),
(48, 'LANGUAGE', 1),
(49, 'EXCEL_IMPORT', 1),
(50, 'CAPTION', 1),
(51, 'MENU_ORDER', 1),
(52, 'ADVANCED', 1),
(53, 'CONTROLLER', 1),
(54, 'ACTION', 1),
(55, 'CLASS', 1),
(56, 'DESCRIPTION', 1),
(57, 'PARENT', 1),
(58, 'ERROR_STRING', 1),
(59, 'TERMCATEGORY', 1),
(60, 'TERM', 1),
(61, 'CONTAINS_DUPLICATES', 1),
(62, 'ADMIN_MENU', 1),
(63, 'USER', 1),
(64, 'BAN', 1),
(65, 'ADMINISTRATOR_NOT_FOUND', 1),
(66, 'ADMINISTRATOR', 1),
(67, 'DETAILS2', 1),
(68, 'JOB', 1);

-- --------------------------------------------------------

--
-- Table structure for table `termcategory`
--

CREATE TABLE IF NOT EXISTS `termcategory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `termcategory`
--

INSERT INTO `termcategory` (`id`, `name`) VALUES
(1, 'translationsssssssssss');

-- --------------------------------------------------------

--
-- Table structure for table `termtranslation`
--

CREATE TABLE IF NOT EXISTS `termtranslation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language` tinyint(3) NOT NULL,
  `translation` varchar(200) NOT NULL,
  `term` tinyint(3) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `language` (`language`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=71 ;

--
-- Dumping data for table `termtranslation`
--

INSERT INTO `termtranslation` (`id`, `language`, `translation`, `term`) VALUES
(1, 1, 'Create new account', 1),
(2, 1, 'No id was set', 2),
(3, 1, 'Term not found', 3),
(4, 1, 'User not found', 4),
(5, 1, 'Language class not found', 5),
(6, 1, 'Language not found', 6),
(7, 1, 'Row not found', 7),
(8, 1, 'TermCategory not found', 8),
(9, 1, 'TermTranslation not found', 9),
(10, 1, 'Wrong username or password', 10),
(11, 1, 'Username', 11),
(12, 1, 'Email', 12),
(13, 1, 'already exist', 13),
(14, 1, 'Registration was successful', 14),
(15, 1, 'Sign out', 15),
(16, 1, 'Sign up', 16),
(17, 1, 'Sign in', 17),
(18, 1, 'Access denied', 18),
(19, 1, 'User class not found', 19),
(20, 1, 'Userclass Exist', 20),
(21, 1, 'Back', 21),
(22, 1, 'Are you sure you want to delete', 22),
(23, 1, 'Details', 23),
(24, 1, 'Cancel', 24),
(25, 1, 'Delete', 25),
(26, 1, 'Modify', 26),
(27, 1, 'No', 27),
(28, 1, 'Yes', 28),
(29, 1, 'Name', 29),
(30, 1, 'Active', 30),
(31, 1, 'Add new', 31),
(32, 1, 'Add', 32),
(33, 1, 'was successfully saved', 33),
(34, 1, 'was successfully deleted', 34),
(35, 1, 'was successfully cloned', 35),
(36, 1, 'Home', 36),
(37, 1, 'Search', 37),
(38, 1, 'Export to excel', 38),
(39, 1, 'Username', 39),
(40, 1, 'Email', 40),
(41, 1, 'Lastlogin', 41),
(42, 1, 'Disabled', 42),
(43, 1, 'Registered', 43),
(44, 1, 'User class', 44),
(45, 1, 'The excel file was correctly imported and processed', 45),
(46, 1, 'Menu not found', 46),
(47, 1, 'Term translation', 47),
(48, 1, 'Language', 48),
(49, 1, 'Import excel file', 49),
(50, 1, 'Caption', 50),
(51, 1, 'Menu order', 51),
(52, 1, 'Advanced', 52),
(53, 1, 'Controller', 53),
(54, 1, 'Action', 54),
(55, 1, 'Class', 55),
(56, 1, 'Description', 56),
(57, 1, 'Parent', 57),
(58, 1, 'Error!', 58),
(59, 1, 'Term category', 59),
(60, 1, 'Term', 60),
(61, 1, 'contains duplicates that have been deleted', 61),
(62, 1, '', 62),
(63, 1, 'Admin menu', 63),
(64, 1, 'Admin menu', 64),
(65, 1, 'User class', 65),
(66, 1, 'User', 66),
(67, 1, 'Ban', 67),
(68, 1, 'Administrator not found', 68),
(69, 1, 'Administrator', 69),
(70, 1, 'details', 70);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `surname` varchar(100) DEFAULT NULL,
  `password` char(65) DEFAULT NULL COMMENT 'this is the most correct way',
  `email` varchar(100) DEFAULT NULL,
  `birthDate` date NOT NULL DEFAULT '0000-00-00',
  `lastLogin` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `image` varchar(100) DEFAULT NULL,
  `registered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `hideEmail` tinyint(1) NOT NULL DEFAULT '0',
  `ip` varchar(30) DEFAULT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `language` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `surname`, `password`, `email`, `birthDate`, `lastLogin`, `deleted`, `image`, `registered`, `hideEmail`, `ip`, `admin`, `language`) VALUES
(1, 'Stanimir', 'Dimitrov', '$2y$12$Mko1YW9UYURWSjdPWWoyWeVSaGr1Dj4OA6X7rpea39ad0ZIXm/ke6', 'psyxopat@gmail.com', '0000-00-00', '2014-10-07 19:26:35', 0, '', '2014-09-23 21:08:31', 0, '127.0.0.1', 0, 1),
(3, 'Stanimir', '', '$2y$13$dGN1QWhZdFpTMlA4VHBGSeohj/4Lr38FnkVSbObs4fXOMJKuEF.OG', 'arasfgsgm@boza.be', '0000-00-00', '2014-12-07 09:05:52', 0, '', '2014-12-06 22:14:33', 0, '127.0.0.1', 1, 1),
(4, 'ivan', '', '$2y$13$SGxSTWM1R2xlVGVxMFFPUOWpiRnlUtiCsDQ4kK92I9YY0dek4tUBW', 'pek@gmail.com', '0000-00-00', '2014-12-07 11:06:17', 0, '', '2014-12-07 11:05:48', 0, '127.0.0.1', 0, 1),
(5, 'ivancho', '', '$2y$13$ck1RZG5Ga0NWdFJKclZHde6QexNCWoAWeRY6OyxsZ3jQGvPhZeRTW', 'ppp@gmail.com', '0000-00-00', '2014-12-07 11:13:34', 0, '', '2014-12-07 11:13:11', 0, '127.0.0.1', 0, 1),
(6, 'Stanimir', '', '$2y$13$aXYwTE9ZQ29YZXBuWXhpMun1/KZf.On/r47yQoCgxB2D6QrFWSUKK', 'aaa@gmail.com', '0000-00-00', '2014-12-07 12:00:16', 0, '', '2014-12-07 11:59:53', 0, '127.0.0.1', 0, 1),
(7, 'Stanimir', '', '$2y$13$WXBVVXhsMFY2T0RQaVo0NeF7/9COOsHStAiTtln7Hq/VwxjNaa6dO', 'bbb@gmail.com', '0000-00-00', '2014-12-07 12:05:14', 0, '', '2014-12-07 12:04:35', 0, '127.0.0.1', 0, 1),
(18, 'StanimirCrypt', '', '$2y$13$0BALuGf4iK3WzpYo57naOOb1p/VPJBVYjSZ5IJhEf1CkLuWFV55bi', 'stanimirdim@gmail.com', '0000-00-00', '2015-06-08 11:33:23', 0, '', '2015-03-12 21:34:42', 0, '::1', 0, 1),
(19, 'Stanimir2', 'dimitrov', '', 'standim92@gmail.com', '0000-00-00', '0000-00-00 00:00:00', 0, '', '0000-00-00 00:00:00', 0, '', 0, 0),
(20, 'alert(String.fromCharCode(88,83,83))//";alert(String.fromCharCode(88,83,83))//-- "''alert(String.from', '', '$2y$13$OJ.Juvg5KSZnwO0PCEYyc./qYMR4hcoW7XZq9.RZJHocZqvVwMun.', 'sgsfgsfxh@gmail.com', '0000-00-00', '0000-00-00 00:00:00', 0, '', '2015-04-13 18:19:00', 0, '127.0.0.1', 0, 1),
(21, 'stanNew', '', '$2y$13$00RYCbrYUp5UGDFBPk/Yye7G6YJ8QuJAMtfa05l0qYu032qpd6YvG', 'stanimirdim92@gmail.com', '0000-00-00', '2015-08-18 09:49:11', 0, '', '2015-06-27 17:31:27', 0, '::1', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `userclass`
--

CREATE TABLE IF NOT EXISTS `userclass` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT NULL,
  `active` tinyint(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `userclass`
--

INSERT INTO `userclass` (`id`, `name`, `active`) VALUES
(1, 'edno', 1),
(2, 'dve', 1),
(3, 'tri', 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
