-- LAST UPDATE: 2020-09-19 07:24

-- Adminer 4.6.3 MySQL dump
DROP DATABASE IF EXISTS `db_osca`;
CREATE DATABASE `db_osca`;
USE `db_osca`;

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DELIMITER ;;

DROP PROCEDURE IF EXISTS `activate_admin_account`;;
CREATE PROCEDURE `activate_admin_account`(IN `user_name_` varchar(60), OUT `msg` int(10))
BEGIN
	IF( (SELECT count(*) FROM `admin` WHERE `user_name` = `user_name_`) = 1) 
    THEN
		UPDATE `admin` SET
		is_enabled = 1,
		log_attempts = 0
		WHERE `user_name` = `user_name_`;
		SET `msg` = "1";
	ELSE 
		SET `msg` = "0";
	END IF;
END;;

DROP PROCEDURE IF EXISTS `add_address`;;
CREATE PROCEDURE `add_address`(IN `add1_` varchar(120), IN `add2_` varchar(120), IN `city_` varchar(120), IN `province_` varchar(120), IN `is_active_` varchar(11), IN `member_id_` varchar(20), OUT `msg` varchar(1))
BEGIN
	IF ((SELECT COUNT(*) FROM `member` WHERE `id` = member_id_) = 1)
	THEN
		START TRANSACTION;
        
			IF (`is_active_` = 1) -- if is_active flag is set, clear other address of this entity
			THEN
				UPDATE `address` a
				INNER JOIN `address_jt` ajt ON ajt.`address_id` = a.id
				SET a.`is_active` = 0
				WHERE ajt.`member_id` = (SELECT `id` FROM `member` WHERE `id` = `member_id_`);
			ELSE
				SET msg = 0; -- do nothing, 
			END IF;
            
			INSERT	INTO `address` (`address1`, `address2`, `city`, `province`, `is_active`, `last_update`)
			VALUES (`add1_`, `add2_`, `city_`, `province_`, `is_active_`, now());
			
			SET @last_inserted_id = LAST_INSERT_ID();

			INSERT INTO address_jt (`address_id`, `member_id`) VALUES (@last_inserted_id, (SELECT `id` FROM `member` WHERE `id` = `member_id_`));
			SET msg = 1;  -- company exists
		COMMIT;
	ELSE 
		SET msg = 0; -- company doesnt exist
	END IF;
END;;

DROP PROCEDURE IF EXISTS `add_admin`;;
CREATE PROCEDURE `add_admin`(IN `username_` varchar(20), IN `password_` varchar(20), IN `firstname_` varchar(60), IN `middlename_` varchar(60), IN `lastname_` varchar(60), IN `birthdate_` date, IN `sex_` varchar(10), IN `contact_number_` varchar(20), IN `email_` varchar(120), IN `position_` varchar(60), IN `isEnabled_` tinyint, IN `answer1_` varchar(20), IN `answer2_` varchar(20), OUT `msg` varchar(60))
BEGIN

    IF
        ((SELECT COUNT(*) FROM `admin` where `user_name` = `username_`) = 0)
    THEN
        INSERT INTO `admin` (`user_name`, `password`, `first_name`, `middle_name`, `last_name`, `birth_date`, `sex`, `contact_number`, `email`, `position`, `is_enabled`, `log_attempts`, `answer1`, `answer2`, `temporary_password`, `avatar`)
                    VALUES (`username_`, MD5(`password_`), `firstname_`, `middlename_`, `lastname_`, `birthdate_`, `sex_`, `contact_number_`, `email_`, `position_`, `isEnabled_`, 0, `answer1_`, `answer2_`, `password_`, 'null');
        SET msg = "1";
    ELSE
        SET msg = "0";
    END IF;

END;;

DROP PROCEDURE IF EXISTS `add_company`;;
CREATE PROCEDURE `add_company`(IN `company_tin_` varchar(20), IN `company_name_` varchar(250), IN `branch_` varchar(120), IN `business_type_` varchar(120),
                                IN `address1_` varchar(120), IN `address2_` varchar(120), IN `city_` varchar(120), IN `province_` varchar(120))
BEGIN
	START TRANSACTION;
	INSERT INTO `company` (`company_tin`, `company_name`, `branch`, `business_type`)
		VALUES	(`company_tin_`, `company_name_`, `branch_`, `business_type_`);
	SET @company_inserted_id = LAST_INSERT_ID();

	INSERT INTO `address` (`address1`, `address2`, `city`, `province`, `is_active`, `last_update`)
		VALUES	(`address1_`, `address2_`, `city_`, `province_`, 1, now());

	SET @address_inserted_id = LAST_INSERT_ID();
	INSERT INTO address_jt (`address_id`, `company_id`) VALUES (@address_inserted_id, @company_inserted_id);
	COMMIT;
END;;

DROP PROCEDURE IF EXISTS `add_company_address`;;
CREATE PROCEDURE `add_company_address`(IN `add1_` varchar(120), IN `add2_` varchar(120), IN `city_` varchar(120), IN `province_` varchar(120), IN `is_active_` varchar(11), IN `selected_id_` varchar(20), OUT `msg` varchar(1))
BEGIN
	IF ((SELECT COUNT(*) FROM `company` WHERE `id` = `selected_id_`) = 1)
	THEN
		START TRANSACTION;
        
			IF (`is_active_` = 1) -- if is_active flag is set, clear other address of this entity
			THEN
				UPDATE `address` a
				INNER JOIN `address_jt` ajt ON ajt.`address_id` = a.id
				SET a.`is_active` = 0
				WHERE ajt.`company_id` = (SELECT `id` FROM `company` WHERE `id` = `selected_id_`);
			ELSE
				SET msg = 0; -- do nothing, 
			END IF;
			
			INSERT	INTO `address` (`address1`, `address2`, `city`, `province`, `is_active`, `last_update`)
			VALUES (`add1_`, `add2_`, `city_`, `province_`, `is_active_`, now());
			SET @last_inserted_id = LAST_INSERT_ID();
			INSERT INTO address_jt (`address_id`, `company_id`) VALUES (@last_inserted_id, (SELECT `id` FROM `company` WHERE `id` = `selected_id_`));
            
			SET msg = 1;  -- company exists
		COMMIT;
	ELSE 
		SET msg = 0; -- company doesnt exist
	END IF;
END;;

DROP PROCEDURE IF EXISTS `add_complaint_report`;;
CREATE PROCEDURE `add_complaint_report`(IN `company_name_` VARCHAR(250), IN `branch_` VARCHAR(120), IN `osca_id_` VARCHAR(20), IN `desc_` VARCHAR(300), IN `report_date_` TIMESTAMP, OUT `msg` INT(1))
BEGIN
  IF ((SELECT COUNT(*)
    FROM company AS c
    INNER JOIN `member` AS m
    WHERE c.company_name = company_name_
    AND c.branch = branch_
    AND m.osca_id = osca_id_) = 1)
  THEN
    INSERT INTO complaint_report (company_id, member_id)
    SELECT c.id, m.id
    FROM company AS c
    INNER JOIN `member` AS m
    WHERE c.company_name = company_name_
    AND c.branch = branch_
    AND m.osca_id = osca_id_;
  
    SET @last_inserted_id = LAST_INSERT_ID();
    
    UPDATE complaint_report set
    `desc` = desc_,
    report_date = report_date_
    WHERE id = @last_inserted_id;
    
    SET msg = 1;
  ELSE 
    SET msg = 0;
  END IF;
END;;

DROP PROCEDURE IF EXISTS `add_drug`;;
CREATE PROCEDURE `add_drug`(IN `generic_name_` varchar(120), IN `brand_` varchar(120), IN `dose_` int(20), IN `unit_` varchar(120), IN `is_otc_` int(10), IN `max_monthly_` int(20), IN `max_weekly_` int(20))
BEGIN
    INSERT INTO `drug` (`generic_name`, `brand`, `dose`, `unit`, `is_otc`, `max_monthly`, `max_weekly`)
                VALUES (`generic_name_`, `brand_`, `dose_`, `unit_`, `is_otc_`, `max_monthly_`, `max_weekly_`);
END;;

DROP PROCEDURE IF EXISTS `add_guardian`;;
CREATE PROCEDURE `add_guardian`(IN `first_name_` varchar(120), IN `middle_name_` varchar(120), IN `last_name_` varchar(120), IN `sex_` varchar(10), IN `relationship_` varchar(120), IN `contact_number_` varchar(20), IN `email_` varchar(120), IN `member_id_` varchar(20), OUT `msg` int(1))
BEGIN
	IF ((SELECT COUNT(*) FROM `member` WHERE `id` = member_id_) = 1)
	THEN
		START TRANSACTION;
			
			INSERT	INTO `guardian` (`first_name`,	`middle_name`,	`last_name`,	`sex`,	`relationship`,	`contact_number`,	`email`,	`member_id`)
				VALUES (`first_name_`,	`middle_name_`,	`last_name_`,	`sex_`,	`relationship_`,	`contact_number_`,	`email_`,	`member_id_`);

			SET msg= 1; -- member exists
		COMMIT;
	ELSE 
		SET msg= 0; -- member does not exist
	END IF;
END;;

DROP PROCEDURE IF EXISTS `add_lost_report`;;
CREATE PROCEDURE `add_lost_report`(IN `osca_id_` VARCHAR(20), IN `report_date_` TIMESTAMP, OUT `msg` INT(1))
BEGIN
  IF ((SELECT COUNT(*)
    FROM `member` AS m
    WHERE m.osca_id = osca_id_) = 1)
  THEN
    INSERT INTO lost_report (member_id)
    SELECT m.id
    FROM `member` AS m
    WHERE m.osca_id = osca_id_;
  
    SET @last_inserted_id = LAST_INSERT_ID();
    
    UPDATE lost_report set
    report_date = report_date_
    WHERE id = @last_inserted_id;
    
    SET msg = 1;
  ELSE 
    SET msg = 0;
  END IF;
END;;

DROP PROCEDURE IF EXISTS `add_member`;;
CREATE PROCEDURE `add_member`(IN `fname` varchar(60), IN `mname` varchar(60), IN `lname` varchar(60), IN `bday` date, IN `sex_` varchar(10), 
								IN `contact_no` varchar(20), IN `email_` varchar(120), IN `memship_date_` datetime, 
                                IN `add1` varchar(120), IN `add2` varchar(120), IN `city_` varchar(120), IN `province_` varchar(120), 
                                IN `nfc_serial_` varchar(45), IN `osca_id_` varchar(20), IN `pword` varchar(120), 
                                IN `g_fname` varchar(120), IN `g_mname` varchar(120), IN `g_lname` varchar(120), 
                                IN `g_contact_no` varchar(120), IN `g_sex_` varchar(10), IN `g_relationship` varchar(120), IN `g_email_` varchar(120))
BEGIN
	START TRANSACTION;
	INSERT INTO `member` (`first_name`, `middle_name`, `last_name`, `birth_date`, `sex`, `contact_number`, `email`, `membership_date`, `nfc_serial`, `osca_id`, `password`)
		VALUES	(fname, mname, lname, bday, sex_, contact_no, email_, memship_date_, nfc_serial_, osca_id_, MD5(pword));
	SET @member_inserted_id = LAST_INSERT_ID();

	INSERT INTO `guardian` (`first_name`, `middle_name`, `last_name`, `sex`, `relationship`, `contact_number`, `email`, `member_id`)
		VALUES	(g_fname, g_mname, g_lname, g_sex_, g_relationship, g_contact_no, g_email_, @member_inserted_id);


	INSERT INTO `address` (`address1`, `address2`, `city`, `province`, `is_active`, `last_update`)
		VALUES	(add1, add2, city_, province_, 1, now());

	SET @address_inserted_id = LAST_INSERT_ID();
	INSERT INTO address_jt (`address_id`, `member_id`) VALUES (@address_inserted_id, @member_inserted_id);
	COMMIT;
END;;

DROP PROCEDURE IF EXISTS `add_qr_request`;;
CREATE PROCEDURE `add_qr_request`(IN `osca_id_` VARCHAR(120), IN `desc_` VARCHAR(120), IN `token_` VARCHAR(120), OUT `msg` VARCHAR(1))
BEGIN
  DECLARE member_id_ VARCHAR(120);
  IF ((SELECT * FROM `view_members_with_guardian` WHERE `osca_id` = `osca_id_` LIMIT 1) = 1)
  THEN
  SET `member_id_` = (SELECT `member_id` FROM `view_members_with_guardian` WHERE `osca_id` = `osca_id_` LIMIT 1);
  INSERT INTO `qr_request`(`member_id`, `desc`, `token`) VALUES
    (`member_id_`, `desc_`, `token_`);
  SET msg = 1;
  ELSE
  SET msg = 0;
  END IF;
END;;

DROP PROCEDURE IF EXISTS `add_transaction`;;
CREATE PROCEDURE `add_transaction`(IN `trans_date_` timestamp, IN `company_tin_` varchar(120), IN `osca_id_` varchar(120), IN `clerk_` varchar(120), OUT `msg` varchar(120))
BEGIN
  DECLARE company_id_ INT(20);
  DECLARE member_id_ VARCHAR(120);
    SET `company_id_` = (SELECT `c_id` FROM `view_companies` WHERE `company_tin` = `company_tin_`);
    SET `member_id_` = (SELECT `member_id` FROM `view_members_with_guardian` WHERE `osca_id` = `osca_id_` LIMIT 1);
    INSERT INTO `transaction`(`trans_date`, `company_id`, `member_id`, `clerk`) VALUES
      (`trans_date_`, `company_id_`, `member_id_`, `clerk_`);
    SET msg = LAST_INSERT_ID();
END;;

DROP PROCEDURE IF EXISTS `add_transaction_food`;;
CREATE PROCEDURE `add_transaction_food`(IN `trans_type` varchar(120), IN `transaction_id_` int(20), IN `company_tin_` varchar(120), IN `desc_` varchar(120), IN `vat_exempt_price_` decimal(13,2), IN `discount_price_` decimal(13,2), IN `payable_price_` decimal(13,2), OUT `msg` varchar(120))
BEGIN
  IF (`trans_type` = 'food' AND (SELECT COUNT(*) FROM `view_companies` WHERE `company_tin` = `company_tin_`) = 1)
  THEN
    INSERT INTO `food` (`transaction_id`, `desc`, `vat_exempt_price`, `discount_price`, `payable_price`) VALUES
      (`transaction_id_`, `desc_`, `vat_exempt_price_`, `discount_price_`, `payable_price_`);
    SET msg = "1";
  ELSE 
    SET msg = "0";
  END IF;
END;;

DROP PROCEDURE IF EXISTS `add_transaction_pharmacy_drug`;;
CREATE PROCEDURE `add_transaction_pharmacy_drug`(IN `trans_type` varchar(120), IN `transaction_id_` int(20), IN `company_tin_` varchar(120), IN `drug_id_` int(20), IN `quantity_` int(20), IN `unit_price_` decimal(13,2), IN `vat_exempt_price_` decimal(13,2), IN `discount_price_` decimal(13,2), IN `payable_price_` decimal(13,2), OUT `msg` varchar(120))
BEGIN
  IF (`trans_type` = 'pharmacy' AND (SELECT COUNT(*) FROM `view_companies` WHERE `company_tin` = `company_tin_`) = 1)
  THEN
    INSERT INTO `pharmacy` (`transaction_id`, `drug_id`, `quantity`, `unit_price`, `vat_exempt_price`, `discount_price`, `payable_price`) VALUES
      (`transaction_id_`, `drug_id_`, `quantity_`, `unit_price_`, `vat_exempt_price_`, `discount_price_`, `payable_price_`);
    SET msg = "1";
  ELSE 
    SET msg = "0";
  END IF;
END;;

DROP PROCEDURE IF EXISTS `add_transaction_pharmacy_nondrug`;;
CREATE PROCEDURE `add_transaction_pharmacy_nondrug`(IN `trans_type` varchar(120), IN `transaction_id_` int(20), IN `company_tin_` varchar(120), IN `desc_` varchar(120), IN `vat_exempt_price_` decimal(13,2), IN `discount_price_` decimal(13,2), IN `payable_price_` decimal(13,2), OUT `msg` varchar(120))
BEGIN
  IF (`trans_type` = 'pharmacy' AND (SELECT COUNT(*) FROM `view_companies` WHERE `company_tin` = `company_tin_`) = 1)
  THEN
		INSERT INTO `pharmacy` (`transaction_id`, `desc_nondrug`, `vat_exempt_price`, `discount_price`, `payable_price`) VALUES
      (`transaction_id_`, `desc_`, `vat_exempt_price_`, `discount_price_`, `payable_price_`);
    SET msg = "1";
  ELSE 
    SET msg = "0";
  END IF;
END;;

DROP PROCEDURE IF EXISTS `add_transaction_transportation`;;
CREATE PROCEDURE `add_transaction_transportation`(IN `trans_type` varchar(120), IN `transaction_id_` int(20), IN `company_tin_` varchar(120), IN `desc_` varchar(120), IN `vat_exempt_price_` decimal(13,2), IN `discount_price_` decimal(13,2), IN `payable_price_` decimal(13,2), OUT `msg` varchar(120))
BEGIN
  IF (`trans_type` = 'transportation' AND (SELECT COUNT(*) FROM `view_companies` WHERE `company_tin` = `company_tin_`) = 1)
  THEN
		INSERT INTO `transportation` (`transaction_id`, `desc`, `vat_exempt_price`, `discount_price`, `payable_price`) VALUES
      (`transaction_id_`, `desc_`, `vat_exempt_price_`, `discount_price_`, `payable_price_`);
    SET msg = "1";
  ELSE 
    SET msg = "0";
  END IF;
END;;

DROP PROCEDURE IF EXISTS `deactivate_admin_account`;;
CREATE PROCEDURE `deactivate_admin_account`(IN `user_name_` varchar(60), OUT `msg` int(10))
BEGIN
	IF( (SELECT count(*) FROM `admin` WHERE `user_name` = `user_name_`) = 1) 
    THEN
		UPDATE `admin` SET
		is_enabled = 0,
		log_attempts = 0
		WHERE `user_name` = `user_name_`;
		SET `msg` = "0";
	ELSE 
		SET `msg` = "0";
	END IF;
END;;

DROP PROCEDURE IF EXISTS `delete_company_address`;;
CREATE PROCEDURE `delete_company_address`(IN `company_id_` int(20), IN `company_name_` varchar(250), IN `branch_` varchar(120), OUT `msg` varchar(20))
BEGIN
	
	IF(( SELECT count(*) FROM `company` c
			WHERE c.`id` = `company_id_` 
			AND c.`company_name` = `company_name_`
			AND c.`branch` = `branch_`) = 1)
	THEN
		DELETE FROM `company`
			WHERE `id` = `company_id_`;
		IF(( SELECT count(*) FROM `address` a 
				INNER JOIN `address_jt` ajt ON ajt.`address_id` = a.`id`
				WHERE ajt.`company_id` = `company_id_`) > 0)
		THEN
			DELETE FROM `address_jt`
				WHERE `company_id` = `company_id_`;
			SET msg = 2; -- member and address exists
		ELSE
			SET msg = 1; -- address doesnt exist
		END IF;
	ELSE
		SET msg = 0; -- the company doesnt exist
	END IF;
END;;

DROP PROCEDURE IF EXISTS `delete_guardian`;;
CREATE PROCEDURE `delete_guardian`(IN `member_osca_id_` varchar(20), IN `id_` int(20), OUT `msg` int(1))
BEGIN
	IF((
	SELECT count(*) FROM `guardian` g INNER JOIN `member` m on g.`member_id` = m.`id` WHERE g.`id` = `id_` AND m.`osca_id` = `member_osca_id_`) = 1)
	THEN
		
		DELETE FROM `guardian`
		WHERE `id` = `id_`;

		IF(( SELECT count(*) FROM address a INNER JOIN address_jt ajt ON ajt.address_id = a.id WHERE ajt.`guardian_id` = `id_`) = 1)
		THEN            
			DELETE FROM `address_jt`
			WHERE `guardian_id` = `id_`;
			
			SET msg = 2; -- address for this guardian exists, guardian deleted
		ELSE

			SET msg = 1; -- address for this guardian does not exist, guardian deleted
		END IF;
	ELSE

		SET msg = 0; -- Guardian does not exist
	END IF;
END;;

DROP PROCEDURE IF EXISTS `delete_member_address`;;
CREATE PROCEDURE `delete_member_address`(IN `member_id_` int(20), IN `id_` int(20), OUT `msg` varchar(120))
BEGIN
	IF(( SELECT count(*) FROM `member` m
			INNER JOIN `address_jt` ajt ON ajt.`member_id` = m.`id`
			WHERE ajt.`member_id` = `member_id_`) > 0)
	THEN
		IF(( SELECT count(*) FROM `address` a 
				INNER JOIN `address_jt` ajt ON ajt.`address_id` = a.`id`
                WHERE ajt.`address_id` = `address_id_`) > 0)
		THEN
			DELETE FROM `address_jt`
				WHERE `member_id` = `member_id_` AND `address_id` = `address_id_`;
			DELETE FROM `address`
				WHERE `id` = `address_id_`;
			SET msg = 2; -- member and address exists
        ELSE
			SET msg = 1; -- member exists but not the address
        END IF;
	ELSE
		SET msg = 0; -- the member doesnt exist
	END IF;
END;;

DROP PROCEDURE IF EXISTS `edit_address_company`;;
CREATE PROCEDURE `edit_address_company`(IN `add1_` varchar(120), IN `add2_` varchar(120), IN `city_` varchar(120), IN `province_` varchar(120), IN `id_` int(11), IN `company_id_` varchar(120), OUT `msg` varchar(120))
BEGIN
	IF( (SELECT count(*) FROM `company` c
                    WHERE c.`id` = `company_id_`) = 1)
    THEN
		IF( (SELECT count(*) FROM `address` a
			INNER JOIN `address_jt` ajt ON ajt.`address_id` = a.id
			WHERE ajt.`company_id` = `company_id_` AND ajt.`address_id` = `id_`) = 1 )
		THEN
			UPDATE `address` a
			INNER JOIN `address_jt` ajt ON ajt.`address_id` = a.id
			SET `address1` = `add1_`,
				`address2` = `add2_`,
				`city` = `city_`,
				`province` = `province_`,
				`is_active` = `1`,
				`last_update` = now()
			WHERE a.`id` = `id_`
			AND  ajt.`company_id` = `company_id_`;
			SET msg = "2";  -- company exist and address exist
		ELSE
			SET msg = "1"; -- company exist but address doesnt
		END IF;
	ELSE 
		SET msg = "0"; -- company doesnt exist
	END IF;
END;;

DROP PROCEDURE IF EXISTS `edit_admin_no_pw`;;
CREATE PROCEDURE `edit_admin_no_pw`(IN `uname` varchar(120), IN `fname` varchar(120), IN `mname` varchar(120), IN `lname` varchar(120), IN `bday` date, IN `sex_` varchar(10), IN `contact_number_` varchar(20), IN `email_` varchar(120), IN `pos` varchar(120), IN `ans1` varchar(100), IN `ans2` varchar(100), IN `uid` varchar(120))
UPDATE `admin` SET 
user_name = uname,
first_name = fname,
middle_name = mname,
last_name = lname,
birth_date = bday,
sex = sex_,
contact_number = contact_number_,
email = email_,
position = pos,
answer1 = ans1,
answer2 = ans2
WHERE id = uid;;

DROP PROCEDURE IF EXISTS `edit_admin_picture`;;
CREATE PROCEDURE `edit_admin_picture`(IN `user_name_` varchar(60), IN `avatar_` varchar(60), OUT `msg` int(10))
BEGIN
	IF( (SELECT count(*) FROM `admin` WHERE `user_name` = `user_name_`) = 1) 
    THEN
		UPDATE `admin` SET
		`avatar` = `avatar_`
		WHERE `user_name` = `user_name_`;
		SET `msg` = "1";
	ELSE 
		SET `msg` = "0";
	END IF;
END;;

DROP PROCEDURE IF EXISTS `edit_admin_with_pw`;;
CREATE PROCEDURE `edit_admin_with_pw`(IN `uname` varchar(120), IN `pword` varchar(120), IN `fname` varchar(120), IN `mname` varchar(120), IN `lname` varchar(120), IN `bday` date, IN `sex_` varchar(10), IN `contact_number_` varchar(20), IN `email_` varchar(120), IN `pos` varchar(120), IN `ans1` varchar(100), IN `ans2` varchar(100), IN `tempopw` varchar(120), IN `uid` varchar(120))
UPDATE `admin` SET 
user_name = uname,
password = MD5(pword),
first_name = fname,
middle_name = mname,
last_name = lname,
birth_date = bday,
sex = sex_,
contact_number = contact_number_,
email = email_,
position = pos,
answer1 = ans1,
answer2 = ans2,
temporary_password = pword
WHERE id = uid;;

DROP PROCEDURE IF EXISTS `edit_company`;;
CREATE PROCEDURE `edit_company`(IN `company_tin_` varchar(20), IN `company_name_` varchar(250), IN `branch_` varchar(120), IN `business_type_` varchar(120), IN `company_id_` int(20), OUT `msg` varchar(60))
BEGIN
	IF( (SELECT count(*) FROM `company` c WHERE c.`id` = `company_id_`) = 1)
	THEN
		UPDATE `company`
			SET 
			`company_tin` = `company_tin_`,
			`company_name` = `company_name_`,
			`branch` = `branch_`,
			`business_type` = `business_type_`
			WHERE `id` = `company_id_`;
		SET msg = "1"; -- company exists
	ELSE 
		SET msg = "0"; -- company exists
	END IF;
END;;

DROP PROCEDURE IF EXISTS `edit_company_address`;;
CREATE PROCEDURE `edit_company_address`(IN `add1_` varchar(120), IN `add2_` varchar(120), IN `city_` varchar(120), IN `province_` varchar(120), IN `is_active_` varchar(120), IN `id_` int(11), IN `company_id_` varchar(120), OUT `msg` varchar(120))
BEGIN
	IF( (SELECT count(*) FROM `company` c
                    WHERE c.`id` = `company_id_`) = 1)
    THEN
		IF( (SELECT count(*) FROM `address` a
			INNER JOIN `address_jt` ajt ON ajt.`address_id` = a.id
			WHERE ajt.`company_id` = `company_id_` AND ajt.`address_id` = `id_`) = 1 )
		THEN
			UPDATE `address` a
			INNER JOIN `address_jt` ajt ON ajt.`address_id` = a.id
			SET `address1` = `add1_`,
				`address2` = `add2_`,
				`city` = `city_`,
				`province` = `province_`,
				`is_active` = '1',
				`last_update` = now()
			WHERE a.`id` = `id_`
			AND  ajt.`company_id` = `company_id_`;
			SET msg = "2";  -- company exist and address exist
		ELSE
			SET msg = "1"; -- company exist but address doesnt
		END IF;
	ELSE 
		SET msg = "0"; -- company doesnt exist
	END IF;
END;;

DROP PROCEDURE IF EXISTS `edit_company_logo`;;
CREATE PROCEDURE `edit_company_logo`(IN `company_tin_` varchar(60), IN `logo_` varchar(60), OUT `msg` int(10))
BEGIN
	IF( (SELECT count(*) FROM `company` WHERE `company_tin` = `company_tin_`) = 1) 
    THEN
		UPDATE `company` SET
		`logo` = `logo_`
		WHERE `company_tin` = `company_tin_`;
		SET `msg` = "1";
	ELSE 
		SET `msg` = "0";
	END IF;
END;;

DROP PROCEDURE IF EXISTS `edit_guardian`;;
CREATE PROCEDURE `edit_guardian`(IN `g_id_` int(20), IN `osca_id_` varchar(20), IN `first_name_` varchar(120), IN `middle_name_` varchar(120), IN `last_name_` varchar(120), IN `sex_` varchar(120), IN `relationship_` varchar(120), IN `contact_number_` varchar(120), IN `email_` varchar(120), OUT `msg` varchar(120))
BEGIN
	IF(
	(SELECT count(*) FROM `member` m
                    INNER JOIN `guardian` g ON g.member_id = m.id
                    WHERE m.`osca_id` = `osca_id_` AND g.`id` = `g_id_`) 
	= 1) THEN
			UPDATE `guardian` g
			INNER JOIN `member` m ON g.`member_id` = m.id
			SET 
			g.`first_name` = `first_name_`,
			g.`middle_name` = `middle_name_`,
			g.`last_name` = `last_name_`,
			g.`sex` = `sex_`,
			g.`relationship` = `relationship_`,
			g.`contact_number` = `contact_number_`,
			g.`email` = `email_`
			WHERE g.`id` = `g_id_`;
			SET msg = "1";
		ELSE 
			SET msg = "0";
		END IF;
END;;

DROP PROCEDURE IF EXISTS `edit_member_address`;;
CREATE PROCEDURE `edit_member_address`(IN `add1_` varchar(120), IN `add2_` varchar(120), IN `city_` varchar(120), IN `province_` varchar(120), IN `is_active_` varchar(120), IN `id_` int(11), IN `member_id_` varchar(120), OUT `msg` varchar(120))
BEGIN
	IF( (SELECT count(*) FROM `member` m
                    WHERE `id` = `member_id_`) = 1)
	THEN 
		IF(( SELECT count(*) FROM `address` a
			INNER JOIN `address_jt` ajt ON ajt.`address_id` = a.id
			WHERE ajt.`member_id` = `member_id_` AND ajt.`address_id` = `id_`) = 1)
		THEN
			IF (`is_active_` = 1)
			THEN
				UPDATE `address` a
				INNER JOIN `address_jt` ajt ON ajt.`address_id` = a.id
				SET `is_active` = 0
				WHERE ajt.`member_id` = `member_id_`;
			ELSE 
				SET msg = "1";
			END IF;
			UPDATE `address` a
				INNER JOIN `address_jt` ajt ON ajt.`address_id` = a.id
				SET 
				`address1` = `add1_`,
				`address2` = `add2_`,
				`city` = `city_`,
				`province` = `province_`,
				`is_active` = `is_active_`,
				`last_update` = now()
				WHERE a.`id` = `id_`
				AND  ajt.`member_id` = `member_id_`;
				SET msg = "2"; -- Address and User exists. Successfully updated
		ELSE
			SET msg = "1"; -- Address for user does not exist
		END IF;
	ELSE
		SET msg = "0"; -- user des not exist
	END IF;
END;;

DROP PROCEDURE IF EXISTS `edit_member_no_pw`;;
CREATE PROCEDURE `edit_member_no_pw`(IN `oid` varchar(20), IN `nserial` varchar(45), IN `fname` varchar(120), IN `mname` varchar(120), IN `lname` varchar(120), IN `bday` date, IN `cnumber` varchar(20), IN `email_` varchar(120), IN `sex_` varchar(10), IN `mdate` timestamp, IN `uid` varchar(120))
UPDATE `member` SET 
osca_id= oid,
nfc_serial= nserial,
first_name = fname,
middle_name = mname,
last_name = lname,
birth_date = bday,
sex = sex_,
contact_number = cnumber,
email = email_,
membership_date = mdate
WHERE id = uid;;

DROP PROCEDURE IF EXISTS `edit_member_picture`;;
CREATE PROCEDURE `edit_member_picture`(IN `osca_id_` varchar(60), IN `picture_` varchar(60), OUT `msg` int(10))
BEGIN
	IF( (SELECT count(*) FROM `member` WHERE `osca_id` = `osca_id_`) = 1) 
    THEN
		UPDATE `member` SET
		`picture` = `picture_`
		WHERE `osca_id` = `osca_id_`;
		SET `msg` = "1";
	ELSE 
		SET `msg` = "0";
	END IF;
END;;

DROP PROCEDURE IF EXISTS `edit_member_with_pw`;;
CREATE PROCEDURE `edit_member_with_pw`(IN `oid` varchar(20), IN `nserial` varchar(45), IN `pword` varchar(60), IN `fname` varchar(120), IN `mname` varchar(120), IN `lname` varchar(120), IN `bday` date, IN `cnumber` varchar(20), IN `email_` varchar(120), IN `sex_` varchar(10), IN `mdate` timestamp, IN `uid` varchar(120))
UPDATE `member` SET 
osca_id= oid,
nfc_serial= nserial,
password=MD5(MD5(MD5(pword))),
first_name = fname,
middle_name = mname,
last_name = lname,
birth_date = bday,
sex = sex_,
contact_number = cnumber,
email = email_,
membership_date = mdate
WHERE id = uid;;

DROP PROCEDURE IF EXISTS `fetch_food_transactions`;;
CREATE PROCEDURE `fetch_food_transactions`(IN `osca_id` INT)
BEGIN
  select
      `f`.trans_date,
      `f`.company_name,
      `f`.branch,
      `f`.business_type,
      `f`.`desc`,
      `f`.vat_exempt_price,
      `f`.discount_price,
      `f`.payable_price
    from
      `view_food_transactions` `f`
    where
      `f`.osca_id = osca_id;
END;;

DROP PROCEDURE IF EXISTS `fetch_password`;;
CREATE PROCEDURE `fetch_password`(IN `osca_id` INT)
BEGIN
  select
    `mg`.osca_id,
    `mg`.`password`,
    `mg`.contact_number
  from
    `view_members_with_guardian` `mg`
  where
    `mg`.osca_id = osca_id
    AND `mg`.a_is_active = 1;
END;;

DROP PROCEDURE IF EXISTS `fetch_pharma_transactions`;;
CREATE PROCEDURE `fetch_pharma_transactions`(IN `osca_id` INT)
BEGIN
  select
    `p`.trans_date,
    `p`.company_name,
    `p`.branch,
    `p`.business_type,
    concat(`p`.generic_name, '\n' , `p`.brand, '\n',
      `p`.dose, '\n', `p`.unit, '\n',
      `p`.quantity, '\n', `p`.unit_price) AS `desc`,
    `p`.vat_exempt_price,
    `p`.discount_price,
    `p`.payable_price
  from
    `view_pharma_transactions` `p`
  where
    `p`.osca_id = osca_id;
END;;

DROP PROCEDURE IF EXISTS `fetch_transportation_transactions`;;
CREATE PROCEDURE `fetch_transportation_transactions`(IN `osca_id` INT)
BEGIN
  select
    `t`.trans_date,
    `t`.company_name,
    `t`.branch,
    `t`.business_type,
    `t`.`desc`,
    `t`.vat_exempt_price,
    `t`.discount_price,
    `t`.payable_price
  from
    `view_transportation_transactions` `t`
  where
    `t`.osca_id = osca_id;
END;;

DROP PROCEDURE IF EXISTS `forgot_pw_admin`;;
CREATE DEFINER=`dbosca`@`localhost` PROCEDURE `forgot_pw_admin`(IN `uname` varchar(120), IN `ans1` varchar(100), IN `ans2` varchar(100), OUT `tempopw` varchar(6), OUT `msg` int(10))
IF ((SELECT EXISTS(SELECT * FROM `admin` WHERE (user_name = uname AND answer1 = ans1) OR (user_name = uname AND answer2=ans2))) = 1)
THEN
  SET `tempopw`= (SELECT substring(MD5(RAND()), -6));
  UPDATE `admin` SET `password`=MD5(`tempopw`), `temporary_password`=(`tempopw`), is_enabled = 1, log_attempts=0 WHERE `user_name`=`uname`;
  SET msg = 1;
ELSE
  SET msg = 0;
END IF;;

DROP PROCEDURE IF EXISTS `invalid_login`;;
CREATE DEFINER=`dbosca`@`localhost` PROCEDURE `invalid_login`(IN `uname` varchar(20))
BEGIN
  DECLARE selected_id INT(8);
  SET selected_id = (select id from `admin` where `user_name`=uname);
  UPDATE `admin` SET log_attempts = log_attempts + 1 WHERE `id` = selected_id;
  IF (select log_attempts from admin where `user_name`=uname) > 2
  THEN UPDATE admin SET is_enabled = 0 WHERE id = selected_id;
  END IF;
END;;

DROP PROCEDURE IF EXISTS `login_member`;;
CREATE PROCEDURE `login_member`(IN `osca_id` INT, `password` VARCHAR(120))
BEGIN
  select
    `mg`.osca_id,
    `mg`.`password`,
    `mg`.picture,
    CONCAT(`mg`.first_name, ' ', `mg`.last_name) AS full_name,
    `mg`.bdate,
    `mg`.sex,
    `mg`.memship_date,
    `mg`.contact_number,
    CONCAT(`mg`.address_1, ' ', `mg`.address_2, ' ', `mg`.city, ' ', `mg`.province) AS address
  from
    `view_members_with_guardian` `mg`
  where
    `mg`.osca_id = osca_id
    AND `mg`.`password` = password
    AND `mg`.a_is_active = 1;
END;;

DROP PROCEDURE IF EXISTS `validate_login`;;
CREATE DEFINER=`dbosca`@`localhost` PROCEDURE `validate_login`(IN `osca_id` INT, IN `password` VARCHAR(120))
BEGIN
	select user.osca_id, user.password, concat(first_name, " ", middle_name, " ", last_name) as full_name, user.birth_date, user.sex, user.membership_date, user.avatar, concat(address1, " ", address2, ", " , city, ", ", province) as address
	from user
	right join address
	on user.address_id = address.id
	where user.osca_id = osca_id and user.password = password;

END;;

DELIMITER ;

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `address`;
CREATE TABLE `address` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `address1` varchar(120) COLLATE utf8mb4_bin NOT NULL,
  `address2` varchar(120) COLLATE utf8mb4_bin DEFAULT NULL,
  `city` varchar(120) COLLATE utf8mb4_bin NOT NULL,
  `province` varchar(120) COLLATE utf8mb4_bin NOT NULL,
  `is_active` int(11) NOT NULL,
  `last_update` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

INSERT INTO `address` (`id`, `address1`, `address2`, `city`, `province`, `is_active`, `last_update`) VALUES
(1,	'2129 Culdesac Rd Edison St',	'Brgy. Sun Valley',	'Paranaque City',	'Metro Manila',	1,	'2020-08-21 09:20:25'),
(2,	'L23 Villa Antonina Subd',	'Brgy. San Nicolas 2',	'Bacoor 1',	'Cavite',	1,	'2020-09-01 21:28:27'),
(3,	'Blk25 lot41 Milkwort St Ph3 Villa de Primarosa',	'Brgy. Mambog 3',	'Bacoor',	'Cavite',	1,	'2020-08-21 09:20:30'),
(4,	'3009, Ipil st.',	'Brgy Banaba',	'Silang',	'Cavite',	0,	'2020-09-01 21:01:08'),
(5,	'0235 Rafael St., Villa Modena',	'Villagio Ignatius Subd., Brgy. Buenavista III',	'General Trias',	'Cavite',	0,	'2020-08-22 20:37:57'),
(6,	'2099 Culdesac Rd Edison St',	'Brgy. Sun Valley',	'Paranaque City',	'Metro Manila',	1,	'2020-03-30 01:53:27'),
(7,	'5636 Rafael St.',	'Brgy. Manggahan',	'General Trias',	'Cavite',	1,	'2020-09-11 22:21:34'),
(8,	'1001 Sant St.',	'Brgy Maybuhay',	'Manila',	'NCR',	1,	'2020-04-01 00:18:55'),
(9,	'0925 Remedios St.',	'Malate',	'Manila City',	'NCR',	1,	'2020-04-01 00:40:00'),
(10,	'1235 Phase 5 Pili St.',	'Brgy. Anahaw',	'Silang',	'Cavite',	1,	'2020-09-11 22:23:51'),
(11,	'Land of',	'Dawn',	'Nasugbu',	'Batangas',	1,	'2020-09-11 22:39:33'),
(12,	'9287 Riverdale St.',	'Riverdale Subdivision, Brgy. Kasulukan',	'Paniqui',	'Tarlac',	1,	'2020-09-01 20:47:30'),
(13,	'0003 Grove St',	'Brgy. Los Santos',	'Batangas',	'Batangas',	0,	'2020-09-11 22:42:45'),
(14,	'Glass Manor',	'Brgy. Ibabaw Del Sur',	'Paete',	'Laguna',	1,	'2020-09-01 20:33:04'),
(15,	'2548, Nakpil St.',	'Brgy. Reezal, Tamagochi Village',	'Marilao',	'Bulacan',	1,	'2020-09-03 15:53:52'),
(16,	'3180 Zobel St.',	'San Andres Bukid',	'Manila',	'Metro Manila',	1,	'2020-08-24 13:56:38'),
(17,	'0028 Merger St.',	'Louiseville',	'Batangas',	'Batangas',	1,	'2020-08-24 11:32:03'),
(19,	'asdgasdf',	'sadgasdg',	'sadfsda',	'dgasdgasdf',	0,	'2020-08-28 07:47:25'),
(20,	'Walter Mart',	'Mc Arthur Highway ',	'Guiguinto',	'Bulacan',	1,	'2020-09-04 09:32:20'),
(23,	'4F Right Wing',	'Farmers Plaza Cubao',	'Quezon City',	'Ncr, Second District',	1,	'2020-09-11 22:03:35'),
(24,	'dsasdg',	'asdgasdg',	'asdgsadg',	'asdgasdgasdg',	1,	'2020-09-02 11:07:11'),
(25,	'Brgy aosdklj',	'Brgy aosdklj',	'Brgy aosdklj',	'Brgy aosdklj',	0,	'2020-09-02 11:08:48'),
(26,	'Portal Mall GF',	'Brgy. San Gabriel II',	'General Mariano Alvarez',	'Cavite',	0,	'2020-09-02 18:31:38'),
(27,	'Taft Ave. cor. Quirino St',	'Brgy 6969',	'Malate',	'Ncr, City Of Manila, First District',	0,	'2020-09-11 21:42:18');

DROP TABLE IF EXISTS `address_jt`;
CREATE TABLE `address_jt` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `address_id` int(20) NOT NULL,
  `member_id` int(20) DEFAULT NULL,
  `company_id` int(20) DEFAULT NULL,
  `guardian_id` int(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `address_id` (`address_id`),
  KEY `member_id` (`member_id`),
  KEY `company_id` (`company_id`),
  KEY `guardian_id` (`guardian_id`),
  CONSTRAINT `address_jt_ibfk_1` FOREIGN KEY (`address_id`) REFERENCES `address` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_address_jt_company` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_address_jt_guardian` FOREIGN KEY (`guardian_id`) REFERENCES `guardian` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_address_jt_member` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

INSERT INTO `address_jt` (`id`, `address_id`, `member_id`, `company_id`, `guardian_id`) VALUES
(1,	1,	1,	NULL,	NULL),
(2,	2,	3,	NULL,	NULL),
(3,	3,	4,	NULL,	NULL),
(4,	4,	NULL,	NULL,	NULL),
(5,	5,	NULL,	NULL,	NULL),
(6,	6,	5,	NULL,	NULL),
(7,	7,	7,	NULL,	NULL),
(8,	8,	8,	NULL,	NULL),
(9,	9,	6,	NULL,	NULL),
(10,	10,	2,	NULL,	NULL),
(11,	11,	9,	NULL,	NULL),
(12,	12,	10,	NULL,	NULL),
(13,	13,	9,	NULL,	NULL),
(14,	14,	11,	NULL,	NULL),
(15,	15,	13,	NULL,	NULL),
(16,	16,	12,	NULL,	NULL),
(17,	17,	14,	NULL,	NULL),
(19,	19,	14,	NULL,	NULL),
(20,	20,	NULL,	31,	NULL),
(21,	23,	NULL,	5,	NULL),
(22,	24,	NULL,	9,	NULL),
(23,	25,	NULL,	1,	NULL),
(24,	26,	NULL,	14,	NULL),
(25,	27,	NULL,	2,	NULL);

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
  `contact_number` varchar(20) COLLATE utf8mb4_bin DEFAULT NULL,
  `email` varchar(120) COLLATE utf8mb4_bin DEFAULT NULL,
  `is_enabled` int(10) NOT NULL,
  `log_attempts` int(10) NOT NULL,
  `answer1` varchar(100) COLLATE utf8mb4_bin NOT NULL,
  `answer2` varchar(100) COLLATE utf8mb4_bin NOT NULL,
  `temporary_password` varchar(120) COLLATE utf8mb4_bin NOT NULL,
  `avatar` varchar(250) COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

INSERT INTO `admin` (`id`, `user_name`, `password`, `first_name`, `middle_name`, `last_name`, `birth_date`, `sex`, `position`, `contact_number`, `email`, `is_enabled`, `log_attempts`, `answer1`, `answer2`, `temporary_password`, `avatar`) VALUES
(1,	'ralf',	'3cca634013591eb51173fb6207572e37',	'Ralph Christian',	'Arbiol',	'Ortiz',	'1990-01-14',	'1',	'admin',	'07283754',	'ralph.ortiz@ymeal.com',	1,	1,	'ralp',	'orti',	'ralfralf',	'inuho1wjbk.png'),
(2,	'hstn',	'fc29f6ea32a347d55bd690c5d11ed8e3',	'Justine',	'Ildefonso',	'Laserna',	'1990-01-25',	'1',	'admin',	'86554553',	'justine.laserna@ymeal.com',	1,	1,	'hustino',	'hustino',	'b18340',	'c4ef6d230c396efc.png'),
(3,	'matt',	'ce86d7d02a229acfaca4b63f01a1171b',	'Matthew Franz',	'Castro',	'Vasquez',	'1990-01-15',	'1',	'admin',	'32101107',	'matthew.vasquez@ymeal.com',	1,	0,	'matt',	'vasq',	'matt',	'1dngb3owoz.png'),
(4,	'fred',	'2697359d57024a8f41301b0332a8ba39',	'Frederick Allain',	'',	'Dela Cruz',	'1990-01-01',	'1',	'admin',	'09123456789',	'frederick.dela.cruz@ymeal.com',	1,	0,	'fred',	'lain',	'fredfred',	'izkue0sbn0.png'),
(5,	'alycheese',	'6230471bd10839658f414438bc33c88a',	'Aly',	'x',	'Cheese',	'1990-11-11',	'2',	'user',	'09654123789',	'cyrel.lalikan@ymeal.com',	1,	0,	'swan',	'song',	'',	'88d0f2663ebfacb8.jpg'),
(6,	'shang',	'8379c86250c50c0537999a6576e18aa7',	'Jess',	'',	'Monty',	'1990-01-24',	'1',	'user',	'76567752',	'jess.monty@ymeal.com',	1,	2,	'shang',	'shang',	'4347da',	'py1c2qjcpq.png'),
(7,	'synth',	'4b418ed51830f54c3f9af6262b2201d2',	'synth',	'synth',	'synth',	'1980-08-19',	'2',	'user',	'96493119',	'synth.synth@ymeal.com',	0,	0,	'synth',	'synth',	'synthsynth',	'8ea533f799ce6fc3.jpg'),
(8,	'dsfadgasdg',	'd15fd399edbb0b84811b7d18378692a3',	'asdgasdgasdg',	'asdgasdg',	'asdgasdgasdg',	'2019-08-26',	'2',	'admin',	'09123987456',	'6541234@asd.zxc',	1,	0,	'sdfgsdfgsdf',	'sdfgsdfg',	'dsfadgasdg',	'45a3f40a62f2cfef.png');

DROP TABLE IF EXISTS `company`;
CREATE TABLE `company` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `company_tin` varchar(20) COLLATE utf8mb4_bin NOT NULL,
  `company_name` varchar(250) COLLATE utf8mb4_bin NOT NULL,
  `branch` varchar(120) COLLATE utf8mb4_bin NOT NULL,
  `business_type` varchar(120) COLLATE utf8mb4_bin NOT NULL,
  `logo` varchar(120) COLLATE utf8mb4_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `company_tin_UNIQUE` (`company_tin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

INSERT INTO `company` (`id`, `company_tin`, `company_name`, `branch`, `business_type`, `logo`) VALUES
(1,	'654132150',	'Jollibee',	'Gen. Luna, Ermita',	'food',	'jollibee.png'),
(2,	'123654789',	'Diligence Cafe',	'Malate, Manila',	'food',	'diligence.jpg'),
(3,	'5514917468044',	'Jollibee',	'Tandang Sora, Commonwealth',	'food',	'jollibee.png'),
(4,	'7752487534265',	'Dunkin Donuts',	'Sangandaan, Quezon City',	'food',	'dunkin.png'),
(5,	'1239874563',	'Greenwich',	'Farmer\'s Plaza',	'food',	'greenwich.png'),
(6,	'1550792273984',	'Mang Inasal',	'Alphaland Southgate Towers',	'food',	'mang inasal.png'),
(7,	'6070323661079',	'Kenny Rogers Roasters',	'T3 - NAIA',	'food',	'kennyrogers.png'),
(8,	'5200487589140',	'Chowking',	'Hi-Top Supermarket, Aurora Blvd.',	'food',	'chowking.png'),
(9,	'123498742',	'KFC',	'Mall of Asia',	'food',	'kfc.png'),
(10,	'3411927270293',	'Aristocrat',	'Malate, Manila',	'food',	'7f0s8h7d0s0c.png'),
(11,	'6171762815599',	'Mercury Drug',	'GMA, Cavite',	'pharmacy',	'mercury.png'),
(12,	'9777439585523',	'Mercury Drug',	'Hidalgo St., Quiapo',	'pharmacy',	'mercury.png'),
(13,	'3591867572189',	'Mercury Drug',	'BGC Market Market',	'pharmacy',	'mercury.png'),
(14,	'9871234562',	'Watsons',	'Portal, GMA, Cavite',	'pharmacy',	'watsons.png'),
(15,	'8323848273530',	'Watsons',	'Montillano St., Alabang',	'pharmacy',	'watsons.png'),
(16,	'7965766163274',	'Watsons',	'Waltermart, Gen. Trias',	'pharmacy',	'watsons.png'),
(17,	'5821645777418',	'Watsons',	'SM Manila',	'pharmacy',	'watsons.png'),
(18,	'8690961165847',	'The Generics Pharmacy',	'Arnaiz Ave., Makati',	'pharmacy',	'tgp.png'),
(19,	'3983278667746',	'The Generics Pharmacy',	'Quirino Ave, Paranaque',	'pharmacy',	'tgp.png'),
(20,	'6344364475462',	'The Generics Pharmacy',	'Boni Ave., Mandaluyong',	'pharmacy',	'tgp.png'),
(21,	'8016618868870',	'LRT',	'Central Station',	'transportation',	'lrta.png'),
(22,	'9690147404702',	'LRT',	'Doroteo Jose Station',	'transportation',	'lrta.png'),
(23,	'2035775056071',	'LRT',	'United Nations Station',	'transportation',	'lrta.png'),
(24,	'8063147891117',	'LRT',	'Gil Puyat Station',	'transportation',	'lrta.png'),
(25,	'2019924979512',	'LRT',	'Pedro Gil Station',	'transportation',	'lrta.png'),
(26,	'2949532037078',	'MRT',	'Taft Station',	'transportation',	'lrta.png'),
(27,	'5100616960228',	'MRT',	'Guadalupe Station',	'transportation',	'lrta.png'),
(28,	'7282645530357',	'MRT',	'Magallanes Station',	'transportation',	'lrta.png'),
(29,	'3115335081083',	'MRT',	'Araneta - Cubao Station',	'transportation',	'lrta.png'),
(30,	'9271004158049',	'MRT',	'Ayala Station',	'transportation',	'lrta.png'),
(31,	'6545671280',	'Mercury Drug',	'Guiguinto, Bulacan',	'pharmacy',	'mercury.png');

DROP TABLE IF EXISTS `company_accounts`;
CREATE TABLE `company_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(120) COLLATE utf8mb4_bin NOT NULL,
  `password` varchar(120) COLLATE utf8mb4_bin NOT NULL,
  `company_id` int(20) NOT NULL,
  `is_enabled` int(10) NOT NULL,
  `log_attempts` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `company_id` (`company_id`),
  CONSTRAINT `company_accounts_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

INSERT INTO `company_accounts` (`id`, `user_name`, `password`, `company_id`, `is_enabled`, `log_attempts`) VALUES
(1,	'jb_genluna',	'497be5f0198c1013006e9d996989025f',	1,	0,	7),
(2,	'diligence_malate',	'f9aa9714846b01a8e53e9e1fbf48b645',	2,	1,	0),
(3,	'jb_tsora',	'c5447f2456099ffe25b72d33a4306b98',	3,	1,	0),
(4,	'dd_sangandaan',	'd6cc7d23565cb3a613845f3aa5494785',	4,	1,	0),
(5,	'gw_farmers',	'57cd02849719667bcda9907cf7d06367',	5,	1,	0),
(6,	'mi_alphaland',	'85c1a1370fbd5445bd04a7b9baeb3142',	6,	1,	0),
(7,	'krr_naiat3',	'88bdd553895ad4e8cc9f423bf5ce6d30',	7,	1,	0),
(8,	'ck_hitop',	'e8ac986e7fc025944fd255d79e11cbd9',	8,	1,	0),
(9,	'kfc_moa1',	'11f0dc647cc8d1dc45be423e76bf57a1',	9,	1,	0),
(10,	'ars_malate',	'd09e2d16db0d34ea17b22b6ea797a81c',	10,	1,	0),
(11,	'md_gmacav',	'7dea03553e42d350f4d495b471e1e93c',	11,	1,	0),
(12,	'md_hidalgo',	'dbfcae8c1e9d8c11b4e10c7cd6b017dc',	12,	1,	0),
(13,	'md_bgcmarket',	'325686ffdbf76c771a7442acc32de0ed',	13,	1,	0),
(14,	'wats_portal',	'dc6f58dd01e606eff0879d3a4b49105b',	14,	1,	0),
(15,	'wats_montillano',	'820d92d47034ef55e5ec73cfae112071',	15,	1,	0),
(16,	'wats_wmgentri',	'2bcdd43285f1e6918ed141f94c211a5b',	16,	1,	0),
(17,	'wats_smmanila',	'c4aca91ed91cdb8f20c455f358ef82e7',	17,	1,	0),
(18,	'tgp_arnaiz',	'7b8ad0271d4c27fbb644cbb8bf38af84',	18,	1,	0),
(19,	'tgp_quirino',	'6ebef3a0dc58f31f29fc9fb62e1a9787',	19,	1,	0),
(20,	'tgp_boni',	'883a5d99edd0d7ff659f408425f6f35f',	20,	1,	0),
(21,	'lrt_central',	'213a685e9492411de7648789f3388e0a',	21,	1,	0),
(22,	'lrt_djose',	'e290b31b851111f7ff96908d309c172b',	22,	1,	0),
(23,	'lrt_un',	'803aa5a49b417aab8543eb610ca09c8e',	23,	1,	0),
(24,	'lrt_gp',	'844bad0f042113287b99138680cd2f48',	24,	1,	0),
(25,	'lrt_pg',	'be9fcad3a6db5f5fc323838867d3d286',	25,	1,	0),
(26,	'mrt_taft',	'295eb00c7e0bd88685dd5be2b59fda97',	26,	1,	0),
(27,	'mrt_guada',	'10760d0affc120b7fa4b7390ace0d9cc',	27,	1,	0),
(28,	'mrt_maga',	'f1031948b5f83cc72411cf5d5e5ea534',	28,	1,	0),
(29,	'mrt_araneta',	'35603a4ee8e6fa6b59f8062c961322f7',	29,	1,	0),
(30,	'mrt_ayala',	'6226dd56a733a525c6aaa76b7e7a6702',	30,	1,	0),
(31,	'md_wmguiguinto',	'85f0dcc21eb3ca8173867aae85baee97',	31,	1,	0);

DROP TABLE IF EXISTS `complaint_report`;
CREATE TABLE `complaint_report` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `desc` varchar(300) COLLATE utf8mb4_bin DEFAULT NULL,
  `report_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `company_id` int(20) DEFAULT NULL,
  `member_id` int(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  KEY `company_id` (`company_id`),
  CONSTRAINT `complaint_report_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`),
  CONSTRAINT `complaint_report_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

INSERT INTO `complaint_report` (`id`, `desc`, `report_date`, `company_id`, `member_id`) VALUES
(2,	'Epal nung bantay',	'2020-08-27 06:46:30',	1,	2),
(3,	'No discount given',	'2020-08-27 06:46:30',	11,	3);

DROP TABLE IF EXISTS `drug`;
CREATE TABLE `drug` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `generic_name` varchar(120) COLLATE utf8mb4_bin NOT NULL,
  `brand` varchar(120) COLLATE utf8mb4_bin NOT NULL,
  `dose` int(20) NOT NULL,
  `unit` varchar(120) COLLATE utf8mb4_bin NOT NULL,
  `is_otc` int(10) NOT NULL,
  `max_monthly` int(20) DEFAULT NULL,
  `max_weekly` int(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

INSERT INTO `drug` (`id`, `generic_name`, `brand`, `dose`, `unit`, `is_otc`, `max_monthly`, `max_weekly`) VALUES
(1,	'paracetamol',	'biogesic',	500,	'mg',	1,	45000,	12000),
(2,	'paracetamol',	'bioflu',	500,	'mg',	1,	45000,	13500),
(3,	'ibuprofen,paracetamol',	'alaxan',	500,	'mg',	1,	45000,	14000),
(4,	'diphenhydramine',	'benadryl',	500,	'mg',	1,	45000,	21000),
(5,	'loratidine',	'claritin ',	500,	'mg',	1,	45000,	21000),
(6,	'calcium carbonate,famotidine,magnesium hydroxide',	'kremil-s advance',	500,	'mg',	0,	45000,	21000),
(7,	'cetirizine',	'watsons',	10,	'mg',	1,	70,	300),
(8,	'cetirizine',	'virlix',	10,	'mg',	1,	70,	300),
(9,	'carbocisteine,zinc',	'solmux',	500,	'mg',	1,	7000,	30000),
(10,	'sodium ascorbate,zinc',	'immunpro',	500,	'mg',	1,	7000,	30000),
(11,	'aa,sodium ascorbate',	'immunpro',	500,	'mg',	1,	30000,	7000),
(12,	'sodium ascorbate,zincx',	'immunpro',	500,	'mg',	1,	7000,	30000);

DROP TABLE IF EXISTS `food`;
CREATE TABLE `food` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `transaction_id` int(20) NOT NULL,
  `desc` varchar(120) COLLATE utf8mb4_bin DEFAULT NULL,
  `vat_exempt_price` decimal(13,2) NOT NULL,
  `discount_price` decimal(13,2) NOT NULL,
  `payable_price` decimal(13,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `transaction_id` (`transaction_id`),
  CONSTRAINT `food_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transaction` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

INSERT INTO `food` (`id`, `transaction_id`, `desc`, `vat_exempt_price`, `discount_price`, `payable_price`) VALUES
(1,	7,	'meals for 2',	100.00,	20.00,	80.00),
(2,	8,	'meals',	100.00,	20.00,	80.00),
(3,	9,	'meals for 2',	100.00,	20.00,	80.00),
(4,	10,	'meals',	100.00,	20.00,	80.00),
(5,	11,	'meals',	100.00,	20.00,	80.00),
(6,	12,	'meals',	100.00,	20.00,	80.00),
(7,	38,	'Deiri melk 3s',	200.00,	40.00,	160.00),
(8,	38,	'Gardenko 6s',	1000.00,	200.00,	800.00),
(9,	38,	'Neskopi 11-in-1',	500.00,	100.00,	400.00),
(10,	66,	'Dine in meals for 2',	1227.68,	245.54,	982.14),
(11,	73,	'Meals for 3',	892.86,	178.57,	714.29),
(12,	78,	'1 King Lasagna 2 Pizza',	799.11,	159.82,	639.29);

DROP TABLE IF EXISTS `guardian`;
CREATE TABLE `guardian` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(120) COLLATE utf8mb4_bin NOT NULL,
  `middle_name` varchar(120) COLLATE utf8mb4_bin NOT NULL,
  `last_name` varchar(120) COLLATE utf8mb4_bin NOT NULL,
  `sex` varchar(10) COLLATE utf8mb4_bin NOT NULL DEFAULT '0',
  `relationship` varchar(120) COLLATE utf8mb4_bin NOT NULL,
  `contact_number` varchar(20) COLLATE utf8mb4_bin NOT NULL,
  `email` varchar(120) COLLATE utf8mb4_bin DEFAULT NULL,
  `member_id` int(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  CONSTRAINT `guardian_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

INSERT INTO `guardian` (`id`, `first_name`, `middle_name`, `last_name`, `sex`, `relationship`, `contact_number`, `email`, `member_id`) VALUES
(1,	'Gary',	'Jenelle',	'Winton',	'1',	'Grandfather',	'0256890796',	'garywinton9@gmeal.com',	1),
(2,	'Nonie',	'Asx',	'Irene',	'2',	'Grandmother',	'0289965829',	'nonieirene20@gmeal.com',	2),
(3,	'Kelsey',	'Eulalia',	'Diamond',	'1',	'Grandfather',	'0238471073',	'kelseydiamond9@gmeal.com',	3),
(4,	'Galen',	'Avah',	'Kirby',	'2',	'Grandmother',	'0211549317',	'galenkirby7@gmeal.com',	4),
(5,	'Kristine',	'Marcy',	'Charissa',	'1',	'Grandfather',	'0228048410',	'kristinecharissa17@gmeal.com',	5),
(6,	'Cheyanne',	'Paulette',	'Jaylee',	'1',	'Grandfather',	'0266301227',	'cheyannejaylee9@gmeal.com',	6),
(7,	'Avalon',	'Brynlee',	'Aspen',	'1',	'Grandfather',	'09123654654',	'avalonaspen19@gmeal.com',	7),
(8,	'Vianne',	'Kassidy',	'Ursula',	'2',	'Grandmother',	'0214578421',	'vianneursula9@gmeal.com',	8),
(9,	'Rayzer',	'Kentt',	'Carran',	'2',	'Grandmother',	'0274763308',	'raycarran7@gmeal.com',	9),
(10,	'Pamella',	'Russel',	'Corey',	'2',	'Grandmother',	'0288296230',	'pamellacorey1@gmeal.com',	10),
(11,	'Lacy',	'Bekki',	'Marcy',	'1',	'Grandfather',	'0277137953',	'lacymarcy18@gmeal.com',	11),
(12,	'Love',	'Demelzaxx',	'Paulette',	'1',	'Grandfather',	'0288759308',	'lovepaulette0@gmeal.com',	12),
(13,	'Goldie',	'Marilynn',	'Vianne',	'1',	'Grandfather',	'0261176212',	'goldievianne13@gmeal.com',	13),
(14,	'Kimson',	'',	'Kingston',	'1',	'Grandfather',	'029384723',	'sdlkfj@meal.ccc',	14),
(17,	'Billy',	'Boi',	'Zoey',	'2',	'Father',	'09123123123',	'xcvcxvxc@a.c',	14);

DROP TABLE IF EXISTS `lost_report`;
CREATE TABLE `lost_report` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `report_date` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  `member_id` int(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  CONSTRAINT `lost_report_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;


DROP TABLE IF EXISTS `member`;
CREATE TABLE `member` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `osca_id` varchar(20) COLLATE utf8mb4_bin DEFAULT NULL,
  `nfc_serial` varchar(45) COLLATE utf8mb4_bin DEFAULT NULL,
  `password` varchar(120) COLLATE utf8mb4_bin DEFAULT NULL,
  `first_name` varchar(120) COLLATE utf8mb4_bin NOT NULL,
  `middle_name` varchar(120) COLLATE utf8mb4_bin DEFAULT NULL,
  `last_name` varchar(120) COLLATE utf8mb4_bin NOT NULL,
  `birth_date` date NOT NULL,
  `sex` varchar(10) COLLATE utf8mb4_bin NOT NULL,
  `contact_number` varchar(20) COLLATE utf8mb4_bin DEFAULT NULL,
  `email` varchar(120) COLLATE utf8mb4_bin DEFAULT NULL,
  `membership_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `picture` varchar(250) COLLATE utf8mb4_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `osca_id_UNIQUE` (`osca_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

INSERT INTO `member` (`id`, `osca_id`, `nfc_serial`, `password`, `first_name`, `middle_name`, `last_name`, `birth_date`, `sex`, `contact_number`, `email`, `membership_date`, `picture`) VALUES
(1,	'20200000',	'WBVQRVY4DU5JALZI',	'3b33d36e03d1e0510d0bc88a269ad342',	'Lai',	'Arbiol',	'Girardi',	'1953-06-17',	'2',	'0912-456-7890',	'lai.girardi@ymeal.com',	'2020-09-24 16:58:12',	'ci1g9s0y35.png'),
(2,	'20200001',	'441C5D9C99080123',	'3e4752ed713a565b662a2db13a7a62ba',	'Ruby',	'Ildefonso',	'Glass',	'1960-01-25',	'2',	'046-538-5233',	'ruby.glass@ymeal.com',	'2020-09-24 16:58:12',	'ci1g9s0y35.png'),
(3,	'20200009',	'3U9K4TIVRUK7XO6G',	'c70368723fb1780003b3c4986223a232',	'Cordell',	'Castro',	'Broxton',	'1940-06-15',	'1',	'09654123789',	'cordell.broxton@ymeal.com',	'2020-09-24 16:58:12',	'j6bo6kqm07.png'),
(4,	'20200003',	'DAF5093412880400',	'47a3a5537a5fdba16042736763c71bca',	'Stephine',	'Gaco',	'Lamagna',	'1932-07-17',	'2',	'0917-325-5200',	'stephine.lamagna@ymeal.com',	'2020-09-24 16:58:12',	'ci1g9s0y35.png'),
(5,	'20200006',	'FBAD739105CBAE1D',	'd9334221988275625af60e69ce83d33e',	'Olimpia',	'',	'Ollis',	'1998-01-01',	'9',	NULL,	'olimpia.ollis@ymeal.com',	'2020-09-24 16:58:12',	'j6bo6kqm07.png'),
(6,	'20201010',	'9JIFHJVAHKE0G9AI',	'e24f1540f4d0491092bc11b929254947',	'Harriette',	'Flavell',	'Milbourn',	'1945-01-25',	'2',	'09-253-1028',	'harriette.milbourn@ymeal.com',	'2020-09-24 16:58:12',	'ci1g9s0y35.png'),
(7,	'19386329',	'H0B277JSE6SM0PY0',	'092010029bb7de6ee43ecbf56a941abe',	'Elise',	'Trump',	'Benjamin',	'1960-02-22',	'2',	'09123456987',	'elise.benjamin@ymeal.com',	'2020-09-24 16:58:12',	'a488ceea8a1f5aed.jpg'),
(8,	'12341234',	'1234123443214321',	'ed2b1f468c5f915f3f1cf75d7068baae',	'Hermine',	'Bridgman',	'Poirer',	'1990-01-01',	'1',	'0909-123-4567',	'hermine.poirer@ymeal.com',	'2020-09-24 16:58:12',	'j6bo6kqm07.png'),
(9,	'43214321',	'1234123412341234',	'0271bc781a4db4328a5f0af1ca7d2669',	'Khaleed',	'',	'Dawson',	'1900-01-01',	'2',	'12341234',	'khaleed.dawson@ymeal.com',	'2020-09-24 16:58:12',	'ci1g9s0y35.png'),
(10,	'56785678',	'5678567856785678',	'c18b3eff03996f3a203f63733be03d15',	'Ernestine',	'Kyle',	'Ayers',	'1960-08-11',	'2',	'56785678',	'ernestine.ayers@ymeal.com',	'2020-08-21 19:56:47',	'ci1g9s0y35.png'),
(11,	'43217890',	'12341234asdfasdf',	'b1dd603315de7d03d3c9be460e141033',	'Noburu',	'Danya',	'Lea',	'1940-08-29',	'2',	'12341234',	'noburu.lea@ymeal.com',	'2020-09-24 16:58:12',	'ci1g9s0y35.png'),
(12,	'24192651',	'2A6B9CE2A46B1DE9',	'617445834661adc756417af92adc683d',	'Vasanti',	'Elpidio',	'Hippolyte',	'1800-12-25',	'2',	'0279281684',	'vasanti.hippolyte@ymeal.com',	'2020-09-24 16:58:12',	''),
(13,	'24292651',	'2A6B9CE2A4DB4DE9',	'24c2558e01bf9ebece44c54fb8e8ca73',	'McKenzie ',	'Houston',	'Jessye',	'1948-12-08',	'2',	'0279281684',	'mckenzie.jessye@ymeal.com',	'2020-09-24 16:58:12',	''),
(14,	'7890acde',	'7890acde7890acde',	'911be9ad950a83ae1641c4b890e8b38a',	'Christian',	'',	'Murphy',	'1958-10-25',	'1',	'0948654123',	'asdsa@aa.afx',	'2017-12-31 16:00:00',	NULL),
(15,	'12346542',	'1231234654246542',	'7d3f640f0b7470da98c679e68bbb0a38',	'Senior',	'',	'No Address',	'1920-05-20',	'1',	'09234654265',	'asdf@asdc.zxc',	'2020-09-24 16:58:12',	NULL);

DROP TABLE IF EXISTS `pharmacy`;
CREATE TABLE `pharmacy` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `transaction_id` int(20) NOT NULL,
  `desc_nondrug` varchar(120) COLLATE utf8mb4_bin DEFAULT NULL,
  `drug_id` int(20) DEFAULT NULL,
  `quantity` int(20) DEFAULT NULL,
  `unit_price` decimal(13,2) DEFAULT NULL,
  `vat_exempt_price` decimal(13,2) NOT NULL,
  `discount_price` decimal(13,2) NOT NULL,
  `payable_price` decimal(13,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `drug_id` (`drug_id`),
  KEY `transaction_id` (`transaction_id`),
  CONSTRAINT `pharmacy_ibfk_10` FOREIGN KEY (`transaction_id`) REFERENCES `transaction` (`id`),
  CONSTRAINT `pharmacy_ibfk_12` FOREIGN KEY (`drug_id`) REFERENCES `drug` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

INSERT INTO `pharmacy` (`id`, `transaction_id`, `desc_nondrug`, `drug_id`, `quantity`, `unit_price`, `vat_exempt_price`, `discount_price`, `payable_price`) VALUES
(1,	1,	'',	2,	8,	1120.00,	1000.00,	200.00,	800.00),
(2,	2,	'',	6,	10,	112.00,	100.00,	20.00,	80.00),
(3,	3,	'',	3,	4,	896.00,	800.00,	160.00,	640.00),
(4,	4,	'',	4,	3,	448.00,	400.00,	80.00,	320.00),
(5,	5,	'',	3,	14,	1500.00,	1339.29,	267.86,	1071.43),
(6,	6,	'',	1,	18,	2000.00,	1785.71,	357.14,	1428.57),
(7,	19,	'',	3,	10,	5.00,	50.00,	10.00,	40.00),
(8,	21,	'',	1,	14,	100.00,	1250.00,	250.00,	1000.00),
(9,	23,	'',	1,	18,	100.00,	1250.00,	250.00,	1000.00),
(10,	24,	'',	1,	14,	100.00,	1250.00,	250.00,	1000.00),
(17,	23,	'',	2,	10,	201.60,	1800.00,	360.00,	1440.00),
(18,	29,	'',	6,	10,	4.00,	35.71,	7.14,	28.56),
(19,	33,	'Dairy Milk',	NULL,	NULL,	NULL,	100.00,	20.00,	80.00),
(20,	41,	'Deiri melk',	NULL,	NULL,	NULL,	200.00,	40.00,	160.00),
(21,	42,	'Kopinya 10s',	NULL,	NULL,	NULL,	100.00,	20.00,	80.00),
(22,	42,	'Nice Kopi 11in1 - 10s',	NULL,	NULL,	NULL,	200.00,	40.00,	160.00),
(23,	44,	'Kopinya 10s',	NULL,	NULL,	NULL,	100.00,	20.00,	80.00),
(24,	45,	'Grate Caste White 30s',	NULL,	NULL,	NULL,	150.00,	30.00,	120.00),
(25,	46,	'Quai-ker Oathmill',	NULL,	NULL,	NULL,	100.00,	20.00,	80.00),
(26,	46,	'Starbox Prop-puchino 330ml',	NULL,	NULL,	NULL,	97.32,	19.46,	77.86),
(27,	48,	'Quai-ker Oathmill',	NULL,	NULL,	NULL,	100.00,	20.00,	80.00),
(28,	48,	'Starbox Prop-puchino 330ml',	NULL,	NULL,	NULL,	97.32,	19.46,	77.86),
(29,	50,	'Quai-ker Oathmill',	NULL,	NULL,	NULL,	100.00,	20.00,	80.00),
(30,	50,	'Pudgey Vars 12s',	NULL,	NULL,	NULL,	97.32,	19.46,	77.86),
(31,	50,	NULL,	3,	10,	5.00,	100.00,	20.00,	80.00),
(32,	50,	'Quai-ker Oathmill',	NULL,	NULL,	NULL,	100.00,	20.00,	80.00),
(33,	50,	'Pudgey Vars 12s',	NULL,	NULL,	NULL,	97.32,	19.46,	77.86),
(34,	55,	NULL,	6,	10,	5.00,	100.00,	20.00,	80.00),
(35,	55,	'Quai-ker Oathmill',	NULL,	NULL,	NULL,	100.00,	20.00,	80.00),
(36,	55,	'Pudgey Vars 12s',	NULL,	NULL,	NULL,	97.32,	19.46,	77.86),
(37,	58,	NULL,	6,	10,	5.00,	100.00,	20.00,	80.00),
(38,	58,	'Quai-ker Oathmill',	NULL,	NULL,	NULL,	100.00,	20.00,	80.00),
(39,	58,	'Pudgey Vars 12s',	NULL,	NULL,	NULL,	97.32,	19.46,	77.86),
(40,	61,	NULL,	6,	10,	5.00,	100.00,	20.00,	80.00),
(41,	61,	'Quai-ker Oathmill',	NULL,	NULL,	NULL,	100.00,	20.00,	80.00),
(42,	61,	'Pudgey Vars 12s',	NULL,	NULL,	NULL,	97.32,	19.46,	77.86),
(43,	67,	NULL,	6,	14,	8.15,	101.88,	20.38,	81.50),
(44,	68,	NULL,	8,	7,	6.25,	39.06,	7.81,	31.25),
(45,	68,	NULL,	9,	7,	8.00,	50.00,	10.00,	40.00),
(46,	68,	NULL,	1,	1,	5.20,	65.00,	13.00,	52.00),
(47,	68,	NULL,	10,	1,	5.20,	65.00,	13.00,	52.00),
(48,	72,	NULL,	9,	7,	8.00,	50.00,	10.00,	40.00),
(49,	76,	'Meals for 3',	NULL,	NULL,	NULL,	892.86,	178.57,	714.29),
(50,	79,	NULL,	7,	7,	6.25,	39.06,	7.81,	31.25),
(51,	79,	NULL,	9,	7,	8.00,	50.00,	10.00,	40.00),
(52,	79,	NULL,	1,	1,	5.20,	65.00,	13.00,	52.00),
(53,	79,	NULL,	10,	1,	5.20,	65.00,	13.00,	52.00),
(54,	80,	'Frozen Siomai Pack 25s Pack',	NULL,	NULL,	NULL,	249.75,	44.60,	205.15),
(57,	83,	'Frozen Siomai Pack 25s Pack',	NULL,	NULL,	NULL,	249.75,	44.60,	205.15);

DROP TABLE IF EXISTS `qr_request`;
CREATE TABLE `qr_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(20) NOT NULL,
  `desc` varchar(120) COLLATE utf8mb4_bin NOT NULL,
  `token` varchar(120) COLLATE utf8mb4_bin NOT NULL,
  `trans_date` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  CONSTRAINT `fk_qr_request_member` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

INSERT INTO `qr_request` (`id`, `member_id`, `desc`, `token`, `trans_date`) VALUES
(1,	2,	'Product: Biogesic Quantity: 7 Notes: kahit ano',	'9jifhjvahke0g9ai',	'2020-09-24 14:16:44');

DROP TABLE IF EXISTS `transaction`;
CREATE TABLE `transaction` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `trans_date` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  `company_id` int(20) NOT NULL,
  `member_id` int(20) NOT NULL,
  `clerk` varchar(120) COLLATE utf8mb4_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `company_id` (`company_id`),
  KEY `member_id` (`member_id`),
  CONSTRAINT `fk_transaction_company` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_transaction_member` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

INSERT INTO `transaction` (`id`, `trans_date`, `company_id`, `member_id`, `clerk`) VALUES
(1,	'2020-09-06 13:12:45',	14,	4,	'M Reyes'),
(2,	'2020-07-27 20:45:34',	13,	2,	''),
(3,	'2020-09-07 15:15:25',	14,	3,	'Cy'),
(4,	'2020-07-24 21:39:00',	15,	3,	''),
(5,	'2020-08-11 17:36:53',	16,	4,	''),
(6,	'2020-07-04 17:36:53',	17,	2,	''),
(7,	'2020-09-12 09:27:18',	4,	4,	''),
(8,	'2020-08-15 17:36:55',	4,	4,	''),
(9,	'2020-06-17 17:36:55',	6,	5,	''),
(10,	'2020-01-11 17:36:55',	5,	2,	''),
(11,	'2020-02-18 17:36:55',	4,	3,	''),
(12,	'2020-08-11 17:36:55',	9,	4,	''),
(13,	'2020-03-12 17:36:55',	21,	2,	''),
(14,	'2020-07-11 17:36:55',	28,	4,	''),
(15,	'2020-04-13 17:36:55',	24,	3,	''),
(16,	'2020-05-15 17:36:55',	27,	3,	''),
(17,	'2020-02-11 17:36:55',	27,	1,	''),
(18,	'2020-03-11 17:36:55',	26,	2,	''),
(19,	'2020-08-15 23:15:28',	14,	2,	NULL),
(21,	'2020-08-16 00:57:09',	18,	2,	''),
(23,	'2020-09-02 06:10:45',	14,	2,	''),
(24,	'2020-09-02 06:07:17',	14,	4,	''),
(28,	'2020-09-02 06:07:17',	14,	2,	''),
(29,	'2020-09-08 16:03:50',	11,	2,	'Cy'),
(33,	'2020-08-28 05:25:34',	13,	2,	'AB Dela Rosa'),
(38,	'2020-09-10 13:12:45',	4,	2,	'AB Garcia'),
(41,	'2020-09-10 13:12:45',	14,	2,	'AB Garcia'),
(42,	'2020-09-11 05:11:11',	14,	2,	'CD Efren'),
(43,	'2020-09-11 05:10:18',	14,	2,	'CD Efren'),
(44,	'2020-09-11 08:43:49',	14,	2,	'CD Efren'),
(45,	'2020-09-13 01:25:34',	14,	2,	'CD Efren'),
(46,	'2020-09-14 04:18:10',	14,	2,	'CD Efren'),
(48,	'2020-09-14 04:35:48',	14,	2,	'CD Efren'),
(50,	'2020-09-14 07:48:09',	14,	2,	'GH Igol'),
(55,	'2020-09-15 03:14:31',	14,	2,	'GH Igol'),
(58,	'2020-09-13 07:48:09',	14,	3,	'GH Igol'),
(61,	'2020-09-16 01:56:37',	14,	2,	'JB Meneses'),
(65,	'2020-09-18 11:30:13',	26,	2,	'jeppie boi'),
(66,	'2020-09-11 12:38:08',	10,	2,	'AR Magnayo'),
(67,	'2020-09-14 10:45:37',	19,	3,	'AR Magnayo'),
(68,	'2020-09-17 10:40:11',	19,	3,	'AL Manalon'),
(72,	'2020-09-17 13:11:11',	14,	2,	'JK MARLU'),
(73,	'2020-09-15 03:11:11',	5,	2,	'XY Zinger'),
(74,	'2020-09-16 03:11:11',	14,	2,	'XY Zinger'),
(75,	'2020-09-17 15:11:11',	14,	2,	'XY Zinger'),
(76,	'2020-09-17 15:11:11',	14,	2,	'XY Zinger'),
(77,	'2020-09-16 03:11:11',	24,	3,	'AL Wanwan'),
(78,	'2020-09-16 03:11:11',	5,	1,	'AL Wanwan'),
(79,	'2020-09-17 13:11:11',	11,	6,	'AL Manalon'),
(80,	'2020-09-17 15:23:23',	13,	2,	'Ling Maker'),
(83,	'2020-09-21 22:59:59',	13,	6,	'Baxia Master');

DROP TABLE IF EXISTS `transportation`;
CREATE TABLE `transportation` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `transaction_id` int(20) NOT NULL,
  `desc` varchar(120) COLLATE utf8mb4_bin DEFAULT NULL,
  `vat_exempt_price` decimal(13,2) NOT NULL,
  `discount_price` decimal(13,2) NOT NULL,
  `payable_price` decimal(13,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `transaction_id` (`transaction_id`),
  CONSTRAINT `transportation_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transaction` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

INSERT INTO `transportation` (`id`, `transaction_id`, `desc`, `vat_exempt_price`, `discount_price`, `payable_price`) VALUES
(1,	13,	'Bound to Pasay',	100.00,	20.00,	80.00),
(2,	14,	'Bound to Pasay',	100.00,	20.00,	80.00),
(3,	15,	'Bound to DJose',	100.00,	20.00,	80.00),
(4,	16,	'Bound to Pasay',	100.00,	20.00,	80.00),
(5,	17,	'Bound to Cubao',	100.00,	20.00,	80.00),
(6,	18,	'Bound to EDSA',	100.00,	20.00,	80.00),
(7,	65,	'Pasay to Guadalupe | Senior - SJT',	26.79,	5.36,	21.43),
(8,	77,	'LRT Gil Puyat to LRT United Nations',	267.86,	53.57,	214.29);

-- 2020-09-24 16:53:59
