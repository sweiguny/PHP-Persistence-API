-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server Version:               5.5.27 - MySQL Community Server (GPL)
-- Server Betriebssystem:        Win32
-- HeidiSQL Version:             8.1.0.4545
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Exportiere Struktur von Tabelle ppa.order
CREATE TABLE IF NOT EXISTS `order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `customer` varchar(50) NOT NULL,
  `crdate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle ppa.order: ~3 rows (ungefähr)
/*!40000 ALTER TABLE `order` DISABLE KEYS */;
INSERT INTO `order` (`id`, `customer`, `crdate`) VALUES
	(1, 'david gueatta', '2014-01-20 08:15:00'),
	(2, 'Jerry Lewis', '1961-05-12 09:59:30'),
	(3, 'Dean Martin', '1975-12-24 18:20:00');
/*!40000 ALTER TABLE `order` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle ppa.orderpos
CREATE TABLE IF NOT EXISTS `orderpos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL DEFAULT '0',
  `article` varchar(50) NOT NULL,
  `price` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `FK_orderpos_order` FOREIGN KEY (`order_id`) REFERENCES `order` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle ppa.orderpos: ~9 rows (ungefähr)
/*!40000 ALTER TABLE `orderpos` DISABLE KEYS */;
INSERT INTO `orderpos` (`id`, `order_id`, `article`, `price`) VALUES
	(1, 1, 'knife', '300'),
	(2, 1, 'spoon', '2500'),
	(3, 1, 'fork', '35'),
	(4, 2, 'hut', '55.1'),
	(5, 2, 'bath', '0.5'),
	(6, 3, 'pignose', '0.5'),
	(7, 3, 'baseballball', '650'),
	(8, 3, 'baseballcap', '651'),
	(9, 3, 'basballbat', '652');
/*!40000 ALTER TABLE `orderpos` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle ppa.right
CREATE TABLE IF NOT EXISTS `right` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle ppa.right: ~5 rows (ungefähr)
/*!40000 ALTER TABLE `right` DISABLE KEYS */;
INSERT INTO `right` (`id`, `name`) VALUES
	(3, 'ch-pw'),
	(5, 'create_order'),
	(4, 'delete_order'),
	(1, 'login'),
	(2, 'logout');
/*!40000 ALTER TABLE `right` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle ppa.role
CREATE TABLE IF NOT EXISTS `role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle ppa.role: ~3 rows (ungefähr)
/*!40000 ALTER TABLE `role` DISABLE KEYS */;
INSERT INTO `role` (`id`, `name`) VALUES
	(1, 'admin'),
	(2, 'user');
/*!40000 ALTER TABLE `role` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle ppa.role2right
CREATE TABLE IF NOT EXISTS `role2right` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned NOT NULL DEFAULT '0',
  `right_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `role_id` (`role_id`),
  KEY `right_id` (`right_id`),
  CONSTRAINT `FK_role2right_right` FOREIGN KEY (`right_id`) REFERENCES `right` (`id`),
  CONSTRAINT `FK_role2right_role` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle ppa.role2right: ~0 rows (ungefähr)
/*!40000 ALTER TABLE `role2right` DISABLE KEYS */;
INSERT INTO `role2right` (`id`, `role_id`, `right_id`) VALUES
	(4, 1, 3),
	(5, 1, 1),
	(7, 1, 2),
	(8, 1, 5),
	(9, 1, 4),
	(11, 2, 1),
	(12, 2, 5),
	(14, 2, 2);
/*!40000 ALTER TABLE `role2right` ENABLE KEYS */;


-- Exportiere Struktur von Tabelle ppa.user
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL DEFAULT '0',
  `password` varchar(50) NOT NULL DEFAULT '0',
  `role_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `role` (`role_id`),
  CONSTRAINT `FK_user_role` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle ppa.user: ~3 rows (ungefähr)
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`id`, `username`, `password`, `role_id`) VALUES
	(1, 'root', 'ba018160fc26e0cc2e929b8e071f052d', 1),
	(2, 'newby', 'ba018160fc26e0cc2e929b8e071f052d', 2),
	(4, 'adam', 'ba018160fc26e0cc2e929b8e071f052d', 2);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
