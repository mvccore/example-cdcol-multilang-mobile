DROP DATABASE IF EXISTS `cdcol`;

CREATE DATABASE IF NOT EXISTS `cdcol` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;
USE `cdcol`;

DROP TABLE IF EXISTS `cds`;
CREATE TABLE `cds` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `interpret` varchar(200) NOT NULL,
  `year` int(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `title` (`title`),
  KEY `interpret` (`interpret`),
  KEY `year` (`year`)
) CHARSET=utf8;

INSERT INTO `cds` (`id`, `title`, `interpret`, `year`) VALUES
(1,	'Jump',	'Van Halen',	1984),
(2,	'Hey Boy Hey Girl',	'The Chemical Brothers',	1999),
(3,	'Black Light',	'Groove Armada',	2010),
(4,	'Hotel',	'Moby',	2005),
(5, 'Berlin Calling', 'Paul Kalkbrenner', 2008);

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`active` TINYINT(1) NOT NULL DEFAULT 1,
	`admin` TINYINT(1) NOT NULL DEFAULT 0,
	`user_name` VARCHAR(50) NOT NULL,
	`full_name` VARCHAR(100) NOT NULL,
	`password_hash` VARCHAR(60) NOT NULL,
	`permissions` VARCHAR(1000) NULL DEFAULT NULL,
	`roles` VARCHAR(1000) NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `user_name` (`user_name`),
	INDEX `active` (`active`),
	INDEX `admin` (`admin`)
) CHARSET=utf8;

INSERT INTO `users` (`id`, `active`, `admin`, `user_name`, `full_name`, `password_hash`) VALUES
(1,	1,	1,	'admin',	'Administrator',	'$2y$10$s9E56/QH6.a69sJML9aS6enCczRCZcEPrbFh7BYTSrnrn4H9QMF6u'); -- password is "demo"