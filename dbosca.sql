-- Adminer 4.6.3 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

CREATE DATABASE `db_osca` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin */;
USE `db_osca`;

DELIMITER ;;

DROP PROCEDURE IF EXISTS `activate_admin_account`;;
CREATE PROCEDURE `activate_admin_account`(IN `uid` int(20))
UPDATE `admin` SET
is_enabled = 1,
log_attempts = 0
WHERE id = uid;;

DROP PROCEDURE IF EXISTS `add_admin`;;
CREATE PROCEDURE `add_admin`(IN username_ VARCHAR(20), IN password_ VARCHAR(20), IN firstname_ VARCHAR(60), IN middlename_ VARCHAR(60), IN lastname_ VARCHAR(60), IN birthdate_ DATE, IN sex_ VARCHAR(10), IN position_ VARCHAR(60), IN isEnabled_ TINYINT, IN answer1_  VARCHAR(20), IN answer2_ VARCHAR(20), IN avatar_ VARCHAR(60), OUT msg  VARCHAR(60))
BEGIN

		IF
			((SELECT COUNT(*) as user_count FROM admin where user_name = username_) = 0)
		THEN
			INSERT INTO `admin` (`user_name`, `password`, `first_name`, `middle_name`, `last_name`, `birth_date`, `sex`, `position`, `is_enabled`, `log_attempts`, `answer1`, `answer2`, `temporary_password`, `avatar`)
						VALUES	(username_, MD5(password_), firstname_, middlename_, lastname_, birthdate_, sex_, position_, isEnabled_, 0, answer1_, answer2_, password_, avatar_);
			SET msg = "true";
        ELSE
			SET msg = "false";
		END IF;

END;;

DROP PROCEDURE IF EXISTS `add_member`;;
CREATE PROCEDURE `add_member`(IN `fname` varchar(60), IN `mname` varchar(60), IN `lname` varchar(60), IN `bday` date, IN `sex_` varchar(10), IN `contact_no` varchar(20), IN `member_date` timestamp, IN `pic` varchar(60), IN `pword` varchar(20), IN `oscaid` varchar(20), IN `nfcs` varchar(45), IN `add1` varchar(120), IN `add2` varchar(120), IN `city_` varchar(120), IN `province_` varchar(120))
BEGIN

	/*IF
		((SELECT COUNT(*) as user_count
			FROM `member` 
			WHERE `osca_id` = oscaid
			) = 0 ) -- OR (lower(first_name) like '%fname%' AND lower(last_name) like '%lname%')
		THEN*/
			INSERT INTO `member` (`osca_id`, `nfc_serial`, `password`, `first_name`, `middle_name`, `last_name`, `birth_date`, `sex`, `contact_number`, `membership_date`, `picture`)
						VALUES	(oscaid, nfcs, MD5(pword), fname, mname, lname, bday, sex_, contact_no, member_date, pic);
			INSERT INTO `address` (`address1`, `address2`, `city`, `province`, `is_active`, `last_update`, `member_id`)
						VALUES	(add1, add2, city_, province_, 1, now(), (SELECT id FROM `member` WHERE `osca_id` = oscaid));
/*			SET msg = "true";
	      ELSE
			SET msg = "false";
		END IF;*/

END;;

DROP PROCEDURE IF EXISTS `deactivate_admin_account`;;
CREATE PROCEDURE `deactivate_admin_account`(IN `uid` int(20))
UPDATE `admin` SET
is_enabled = 0,
log_attempts = 0
WHERE id = uid;;

DROP PROCEDURE IF EXISTS `edit_admin_no_pw`;;
CREATE PROCEDURE `edit_admin_no_pw`(IN `uname` varchar(120), IN `fname` varchar(120), IN `mname` varchar(120), IN `lname` varchar(120), IN `bday` date, IN `sex_` varchar(10), IN `pos` varchar(120), IN `ans1` varchar(100), IN `ans2` varchar(100), IN `uid` varchar(120))
UPDATE `admin` SET 
user_name = uname,
first_name = fname,
middle_name = mname,
last_name = lname,
birth_date = bday,
sex = sex_,
position = pos,
answer1 = ans1,
answer2 = ans2
WHERE id = uid;;

DROP PROCEDURE IF EXISTS `edit_admin_with_pw`;;
CREATE PROCEDURE `edit_admin_with_pw`(IN `uname` varchar(120), IN `pword` varchar(120), IN `fname` varchar(120), IN `mname` varchar(120), IN `lname` varchar(120), IN `bday` date, IN `sex_` varchar(10), IN `pos` varchar(120), IN `ans1` varchar(100), IN `ans2` varchar(100), IN `tempopw` varchar(120), IN `uid` varchar(120))
UPDATE `admin` SET 
user_name = uname,
password = MD5(pword),
first_name = fname,
middle_name = mname,
last_name = lname,
birth_date = bday,
sex = sex_,
position = pos,
answer1 = ans1,
answer2 = ans2,
temporary_password = pword
WHERE id = uid;;

DROP PROCEDURE IF EXISTS `edit_member_no_pw`;;
CREATE PROCEDURE `edit_member_no_pw`(IN oid int(20), IN nserial varchar(45), IN `fname` varchar(120), IN `mname` varchar(120), IN `lname` varchar(120), IN `bday` date, IN cnumber varchar(20), IN `sex_` varchar(10), IN mdate timestamp, IN `uid` varchar(120))
UPDATE `member` SET 
osca_id= oid,
nfc_serial= nserial,
first_name = fname,
middle_name = mname,
last_name = lname,
birth_date = bday,
contact_number= cnumber,
sex = sex_,
membership_date = mdate
WHERE id = uid;;

DROP PROCEDURE IF EXISTS `edit_member_with_pw`;;
CREATE PROCEDURE `edit_member_with_pw`(IN oid int(20), IN nserial varchar(45), IN pword varchar(60), IN `fname` varchar(120), IN `mname` varchar(120), IN `lname` varchar(120), IN `bday` date, IN cnumber varchar(20), IN `sex_` varchar(10), IN mdate timestamp, IN `uid` varchar(120))
UPDATE `member` SET 
osca_id= oid,
nfc_serial= nserial,
password=MD5(MD5(MD5(pword))),
first_name = fname,
middle_name = mname,
last_name = lname,
birth_date = bday,
contact_number= cnumber,
sex = sex_,
membership_date = mdate
WHERE id = uid;;

DROP PROCEDURE IF EXISTS `forgot_pw_admin`;;
CREATE PROCEDURE `forgot_pw_admin`(IN `uname` varchar(120), IN `ans1` varchar(100), IN `ans2` varchar(100), OUT `tempopw` varchar(6), OUT `msg` int(10))
IF ((SELECT EXISTS(SELECT * FROM `admin` WHERE (user_name = uname AND answer1 = ans1) OR (user_name = uname AND answer2=ans2))) = 1)
THEN
  SET `tempopw`= (SELECT substring(MD5(RAND()), -6));
  UPDATE `admin` SET `password`=MD5(`tempopw`), `temporary_password`=(`tempopw`), is_enabled = 1, log_attempts=0 WHERE `user_name`=`uname`;
  SET msg = 1;
ELSE
  SET msg = 0;
END IF;;

DROP PROCEDURE IF EXISTS `invalid_login`;;
CREATE PROCEDURE `invalid_login`(IN `uname` varchar(20))
BEGIN
  DECLARE selected_id INT(8);
  SET selected_id = (select id from `admin` where `user_name`=uname);
  UPDATE `admin` SET log_attempts = log_attempts + 1 WHERE `id` = selected_id;
  IF (select log_attempts from admin where `user_name`=uname) > 2
  THEN UPDATE admin SET is_enabled = 0 WHERE id = selected_id;
  END IF;
END;;

DROP PROCEDURE IF EXISTS `validate_login`;;
CREATE PROCEDURE `validate_login`(IN `osca_id` INT, IN `password` VARCHAR(120))
BEGIN
	select user.osca_id, user.password, concat(first_name, " ", middle_name, " ", last_name) as full_name, user.birth_date, user.sex, user.membership_date, user.avatar, concat(address1, " ", address2, ", " , city, ", ", province) as address
	from user
	right join address
	on user.address_id = address.id
	where user.osca_id = osca_id and user.password = password;

END;;

DELIMITER ;

DROP TABLE IF EXISTS `address`;
CREATE TABLE `address` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `address1` varchar(120) COLLATE utf8mb4_bin NOT NULL,
  `address2` varchar(120) COLLATE utf8mb4_bin DEFAULT NULL,
  `city` varchar(120) COLLATE utf8mb4_bin NOT NULL,
  `province` varchar(120) COLLATE utf8mb4_bin NOT NULL,
  `is_active` int(11) NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `member_id` int(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  CONSTRAINT `address_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

INSERT INTO `address` (`id`, `address1`, `address2`, `city`, `province`, `is_active`, `last_update`, `member_id`) VALUES
(1,	'2099 Culdesac Rd Edison St',	'Brgy. Sun Valley',	'Paranaque City',	'Metro Manila',	1,	'2020-03-23 16:00:00',	1),
(2,	'L49 Villa Antonina Subd',	'Brgy. San Nicolas 2',	'Bacoor',	'Cavite',	1,	'2020-03-23 16:00:00',	3),
(3,	'Blk31 lot4 Milkwort St Ph3 Villa de Primarosa',	'Brgy. Mambog 3',	'Bacoor',	'Cavite',	1,	'2020-03-23 16:00:00',	4),
(4,	'3009 Ipil St.',	'Brgy. Banaba',	'Silang',	'Cavite',	1,	'2020-03-23 16:00:00',	2),
(5,	'5636 Rafael St., Villagio Ignatius Subd.',	'Buenavista III',	'General Trias',	'Cavite',	0,	'2020-03-23 16:00:00',	2),
(6,	'2099 Culdesac Rd Edison St',	'Brgy. Sun Valley',	'Paranaque City',	'Metro Manila',	1,	'2020-03-30 09:53:27',	5),
(7,	'5636 Rafxx',	'Brgy Manggahan',	'Gen. Tri',	'Cavite',	1,	'2020-04-01 08:18:55',	7),
(8,	'1001 Sant St.',	'Brgy Maybuhay',	'Manila',	'NCR',	1,	'2020-04-01 08:18:55',	8),
(9,	'0925 Remedios St.',	'Malate',	'Manila City',	'NCR',	1,	'2020-04-01 08:40:00',	6);

DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(120) COLLATE utf8mb4_bin NOT NULL,
  `password` varchar(120) COLLATE utf8mb4_bin NOT NULL,
  `first_name` varchar(120) COLLATE utf8mb4_bin NOT NULL,
  `middle_name` varchar(120) COLLATE utf8mb4_bin NOT NULL,
  `last_name` varchar(120) COLLATE utf8mb4_bin NOT NULL,
  `birth_date` date NOT NULL,
  `sex` varchar(10) COLLATE utf8mb4_bin NOT NULL,
  `position` varchar(120) COLLATE utf8mb4_bin NOT NULL,
  `is_enabled` int(10) NOT NULL,
  `log_attempts` int(10) NOT NULL,
  `answer1` varchar(100) COLLATE utf8mb4_bin NOT NULL,
  `answer2` varchar(100) COLLATE utf8mb4_bin NOT NULL,
  `temporary_password` varchar(120) COLLATE utf8mb4_bin NOT NULL,
  `avatar` varchar(250) COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

INSERT INTO `admin` (`id`, `user_name`, `password`, `first_name`, `middle_name`, `last_name`, `birth_date`, `sex`, `position`, `is_enabled`, `log_attempts`, `answer1`, `answer2`, `temporary_password`, `avatar`) VALUES
(1,	'ralf',	'3cca634013591eb51173fb6207572e37',	'Ralph Christian',	'Arbiol',	'Ortiz',	'1998-06-14',	'm',	'User',	0,	4,	'ralp',	'orti',	'ralf',	'499772.jpg'),
(2,	'hstn',	'fc29f6ea32a347d55bd690c5d11ed8e3',	'Justine',	'Ildefonso',	'Laserna',	'1996-01-25',	'm',	'admin',	1,	2,	'hustino',	'hustino',	'hstn',	'190001.jpg'),
(3,	'matt',	'ce86d7d02a229acfaca4b63f01a1171b',	'Matthew Franz',	'Castro',	'Vasquez',	'1998-06-15',	'm',	'admin',	1,	0,	'matt',	'vasq',	'matt',	'409801.jpg'),
(4,	'fred',	'570a90bfbf8c7eab5dc5d4e26832d5b1',	'Frederick Allain',	'Gaco',	'Dela Cruz',	'1996-07-17',	'm',	'admin',	1,	0,	'fred',	'dela',	'fred',	'828040.jpg'),
(5,	'cyrel',	'6230471bd10839658f414438bc33c88a',	'Cyrel',	'Odette',	'Lalikan',	'1997-06-03',	'm',	'admin',	1,	0,	'swan',	'song',	'',	'142885.jpg'),
(6,	'shang',	'8379c86250c50c0537999a6576e18aa7',	'shang',	'shang',	'shang',	'1998-05-24',	'm',	'User',	1,	1,	'shang',	'shang',	'shang',	'168438.jpg'),
(7,	'chasiminx',	'61be5d0aa51d14a32d5d7a2b04adaae7',	'chasimin',	'chasimin',	'chasimin',	'1998-11-21',	'm',	'user',	1,	0,	'chasimin',	'chasimin',	'chasiminx',	'538636.jpg'),
(8,	'chewel',	'025ab5c6f4c0ef5d56c525e678a22702',	'chewel',	'chewel',	'chewel',	'2000-12-01',	'f',	'user',	1,	0,	'chewel',	'chewel',	'chewel',	'700969.jpg'),
(9,	'tito_boi',	'2ed031dd651a2975ce00993c09bb80c9',	'tito',	'boi',	'abunda',	'1950-01-01',	'm',	'admin',	1,	0,	'tito_boi',	'tito_boi',	'tito_boi',	'140652.jpg');

DROP TABLE IF EXISTS `company`;
CREATE TABLE `company` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `company_tin` int(20) NOT NULL,
  `company_name` varchar(250) COLLATE utf8mb4_bin NOT NULL,
  `branch` varchar(120) COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `company_tin_UNIQUE` (`company_tin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;


DROP TABLE IF EXISTS `complaint_report`;
CREATE TABLE `complaint_report` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(250) COLLATE utf8mb4_bin NOT NULL,
  `branch` varchar(120) COLLATE utf8mb4_bin NOT NULL,
  `concern` text COLLATE utf8mb4_bin NOT NULL,
  `report_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `member_id` int(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  CONSTRAINT `complaint_report_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;


DROP TABLE IF EXISTS `food_transaction`;
CREATE TABLE `food_transaction` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `transaction_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `total_amount` decimal(13,2) NOT NULL,
  `total_discount` decimal(13,2) NOT NULL,
  `description` text COLLATE utf8mb4_bin NOT NULL,
  `member_id` int(20) NOT NULL,
  `company_id` int(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  KEY `company_id` (`company_id`),
  CONSTRAINT `food_transaction_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`),
  CONSTRAINT `food_transaction_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;


DROP TABLE IF EXISTS `loss_report`;
CREATE TABLE `loss_report` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `report_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `member_id` int(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  CONSTRAINT `loss_report_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;


DROP TABLE IF EXISTS `member`;
CREATE TABLE `member` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `osca_id` varchar(20) COLLATE utf8mb4_bin NOT NULL,
  `nfc_serial` varchar(45) COLLATE utf8mb4_bin NOT NULL,
  `password` varchar(120) COLLATE utf8mb4_bin NOT NULL,
  `first_name` varchar(120) COLLATE utf8mb4_bin NOT NULL,
  `middle_name` varchar(120) COLLATE utf8mb4_bin DEFAULT NULL,
  `last_name` varchar(120) COLLATE utf8mb4_bin NOT NULL,
  `birth_date` date NOT NULL,
  `sex` varchar(10) COLLATE utf8mb4_bin NOT NULL,
  `contact_number` varchar(20) COLLATE utf8mb4_bin DEFAULT NULL,
  `membership_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `picture` varchar(250) COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `osca_id_UNIQUE` (`osca_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

INSERT INTO `member` (`id`, `osca_id`, `nfc_serial`, `password`, `first_name`, `middle_name`, `last_name`, `birth_date`, `sex`, `contact_number`, `membership_date`, `picture`) VALUES
(1,	'20200000',	'WBVQRVY4DU5JALZI',	'621b0c9bbd565b7e39b9e7397cbec287',	'Lai',	'Arbiol',	'Girardi',	'1998-06-14',	'M',	'0912-456-7890',	'2020-04-01 08:30:30',	'avatar.png'),
(2,	'20200001',	'441C5D9C99080400',	'17e3fe30f46ca4df7c67db07b174475d',	'Ruby',	'Ildefonso',	'Gross',	'1996-01-25',	'M',	'046-538-5233',	'2020-04-01 04:50:08',	'avatar.png'),
(3,	'20200002',	'3U9K4TIVRUK7XO6G',	'62ea98c79d3e69f8cce7850cd0a3d4f0',	'Cordell',	'Castro',	'Broxton',	'1998-06-15',	'M',	'046-201-2011',	'2020-04-01 08:30:36',	'avatar.png'),
(4,	'20200003',	'DAF5093412880400',	'ba241200ba9c49dcde1b7be816522125',	'Stephine',	'Gaco',	'Lamagna',	'1996-07-17',	'M',	'0917-325-5200',	'2020-04-01 04:50:08',	'avatar.png'),
(5,	'20200006',	'FBAD739105CBAE1D',	'e2b4ea1bfc499f085c0537209992ea84',	'Olimpia',	'',	'Ollis',	'1998-01-01',	'M',	NULL,	'2020-04-01 04:50:08',	'pic.png'),
(6,	'20201010',	'9JIFHJVAHKE0G9AI',	'a48ada5f1fed03ae84a537eebb16f29b',	'Harriette',	'Flavell',	'Milbourn',	'1945-01-25',	'f',	'09-253-1028',	'2020-04-01 08:30:46',	'761308329.png'),
(7,	'19386729',	'H0B277JSE6SMGPY0',	'fec239bd7298a04b7436fc8c5e01abef',	'Elise',	'Trump',	'Benjamin',	'1950-03-06',	'm',	'092819285719',	'2020-04-01 08:30:46',	'694019664.jpg'),
(8,	'12341234',	'1234123443214321',	'9ad4504474c4563c50a4c8bda304d3f7',	'hermine',	'bridgman',	'poirer',	'1990-01-01',	'm',	'0909-123-4567',	'2020-04-01 08:18:02',	'229263585.jpg');

DROP TABLE IF EXISTS `pharmacy_transaction`;
CREATE TABLE `pharmacy_transaction` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `transaction_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `total_amount` decimal(13,2) NOT NULL,
  `total_discount` decimal(13,2) NOT NULL,
  `description` text COLLATE utf8mb4_bin NOT NULL,
  `member_id` int(20) NOT NULL,
  `company_id` int(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_pharmacy_transaction_user1_idx` (`member_id`),
  KEY `fk_pharmacy_transaction_company1_idx` (`company_id`),
  CONSTRAINT `fk_pharmacy_transaction_company1` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`),
  CONSTRAINT `pharmacy_transaction_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;


DROP TABLE IF EXISTS `transport_transaction`;
CREATE TABLE `transport_transaction` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `transaction_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `total_amount` decimal(13,2) NOT NULL,
  `total_discount` decimal(13,2) NOT NULL,
  `description` text COLLATE utf8mb4_bin NOT NULL,
  `member_id` int(20) NOT NULL,
  `company_id` int(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_transport_transaction_user1_idx` (`member_id`),
  KEY `fk_transport_transaction_company1_idx` (`company_id`),
  CONSTRAINT `fk_transport_transaction_company1` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`),
  CONSTRAINT `transport_transaction_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;


-- 2020-07-01 13:16:16
