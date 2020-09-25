USE db_osca;

DROP PROCEDURE IF EXISTS `add_transaction`;
DELIMITER ;;
CREATE PROCEDURE `add_transaction` (IN `trans_date_` TIMESTAMP, IN `company_tin_` varchar(120), IN `osca_id_` varchar(120), IN `clerk_` varchar(120), OUT `msg` varchar(120))
BEGIN
  DECLARE company_id_ INT(20);
  DECLARE member_id_ VARCHAR(120);
    SET `company_id_` = (SELECT `c_id` FROM `view_companies` WHERE `company_tin` = `company_tin_`);
    SET `member_id_` = (SELECT `member_id` FROM `view_members_with_guardian` WHERE `osca_id` = `osca_id_` LIMIT 1);
    INSERT INTO `transaction`(`trans_date`, `company_id`, `member_id`, `clerk`) VALUES
      (`trans_date_`, `company_id_`, `member_id_`, `clerk_`);
    SET msg = LAST_INSERT_ID();
END;;
DELIMITER ;

DROP PROCEDURE IF EXISTS `add_transaction_food`;
DELIMITER ;;
CREATE PROCEDURE `add_transaction_food` (IN `trans_type` varchar(120), IN `transaction_id_` int(20), IN `company_tin_` varchar(120), IN `desc_` varchar(120), IN `vat_exempt_price_` decimal(13,2), IN `discount_price_` decimal(13,2), IN `payable_price_` decimal(13,2), OUT `msg` varchar(120))
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
DELIMITER ;

DROP PROCEDURE IF EXISTS `add_transaction_pharmacy_drug`;
DELIMITER ;;
CREATE PROCEDURE `add_transaction_pharmacy_drug` (IN `trans_type` varchar(120), IN `transaction_id_` int(20), IN `company_tin_` varchar(120), IN `drug_id_` int(20), IN `quantity_` int(20), IN `unit_price_` decimal(13,2), IN `vat_exempt_price_` decimal(13,2), IN `discount_price_` decimal(13,2), IN `payable_price_` decimal(13,2), OUT `msg` varchar(120))
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
DELIMITER ;

DROP PROCEDURE IF EXISTS `add_transaction_pharmacy_nondrug`;
DELIMITER ;;
CREATE PROCEDURE `add_transaction_pharmacy_nondrug` (IN `trans_type` varchar(120), IN `transaction_id_` int(20), IN `company_tin_` varchar(120), IN `desc_` varchar(120), IN `vat_exempt_price_` decimal(13,2), IN `discount_price_` decimal(13,2), IN `payable_price_` decimal(13,2), OUT `msg` varchar(120))
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
DELIMITER ;

DROP PROCEDURE IF EXISTS `add_transaction_transportation`;
DELIMITER ;;
CREATE PROCEDURE `add_transaction_transportation` (IN `trans_type` varchar(120), IN `transaction_id_` int(20), IN `company_tin_` varchar(120), IN `desc_` varchar(120), IN `vat_exempt_price_` decimal(13,2), IN `discount_price_` decimal(13,2), IN `payable_price_` decimal(13,2), OUT `msg` varchar(120))
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
DELIMITER ;


DROP PROCEDURE IF EXISTS `add_qr_request`;
DELIMITER ;;
CREATE PROCEDURE `add_qr_request` (IN `osca_id_` varchar(120), IN `desc_` varchar(120), IN `token_` varchar(120), OUT `msg` varchar(120))
BEGIN
  DECLARE member_id_ VARCHAR(120);
    IF ((SELECT count(*) FROM `view_members_with_guardian` WHERE `osca_id` = `osca_id_`) = 1)
    THEN
      SET `member_id_` = (SELECT `member_id` FROM `view_members_with_guardian` WHERE `osca_id` = `osca_id_` LIMIT 1);
      INSERT INTO `qr_request`(`member_id`, `desc`, `token`) VALUES
        (`member_id_`, `desc_`, `token_`);
      SET msg = 1;
    ELSE 
      SET msg = 0;
    END IF;
END;;
DELIMITER ;
/*

btnAuthorize_click()
{
  Open Fillup Form. Input Authorized meds to buy
}

btnSubmit_button_from_fillup_form(){
  generate random string as `token`. Example in php below
  // $token = substr(MD5(rand(100000,999999)),0,16);

  call stored proceduure: `add_qr_request`('$osca_id', 'desc', '$token', @`msg`);
}





