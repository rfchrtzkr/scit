USE `db_osca`;

DROP VIEW IF EXISTS `view_companies`;
CREATE VIEW `view_companies` AS
(
SELECT c.id c_id, company_tin, company_name, branch, business_type, logo,
    ca.id ca_id, user_name, password, is_enabled, log_attempts
FROM company c
INNER JOIN company_accounts ca ON ca.company_id = c.id
);

DROP VIEW IF EXISTS `view_pharma_transactions`;
CREATE VIEW `view_pharma_transactions` AS 
(
SELECT  m.id `member_id`, m.osca_id, m.first_name, m.last_name, t.id `trans_number`, t.trans_date, t.clerk, c.id company_id, c.company_tin, c.company_name, c.branch, c.business_type,
    -- query pharmacy
    d.generic_name, d.brand, d.dose, d.unit, d.is_otc,  d.max_monthly, d.max_weekly, p.quantity,  p.unit_price, p.vat_exempt_price, p.discount_price, p.payable_price    
FROM transaction t
LEFT JOIN pharmacy p ON p.transaction_id = t.id
LEFT JOIN member m ON t.member_id = m.id
LEFT JOIN company c ON t.company_id = c.id 
LEFT JOIN drug d ON p.drug_id = d.id 
WHERE p.transaction_id = t.id and (p.`desc_nondrug` IS NULL OR p.`desc_nondrug` = "")
);

DROP VIEW IF EXISTS `view_pharma_transactions_nondrug`;
CREATE VIEW `view_pharma_transactions_nondrug` AS 
(
SELECT  m.id `member_id`, m.osca_id, m.first_name, m.last_name, t.id `trans_number`, t.trans_date, t.clerk, c.id company_id, c.company_tin, c.company_name, c.branch, c.business_type,
    -- query pharmacy
    p.`desc_nondrug`, p.vat_exempt_price, p.discount_price, p.payable_price
FROM transaction t
LEFT JOIN pharmacy p ON p.transaction_id = t.id
LEFT JOIN member m ON t.member_id = m.id
LEFT JOIN company c ON t.company_id = c.id
WHERE p.transaction_id = t.id AND p.id NOT IN (SELECT p2.id FROM pharmacy p2 WHERE (p.`desc_nondrug` IS NULL OR p.`desc_nondrug` = ""))
);

DROP VIEW IF EXISTS `view_pharma_transactions_all`;
CREATE VIEW `view_pharma_transactions_all` AS 
SELECT  m.id `member_id`, m.osca_id, m.first_name, m.last_name, t.id `trans_number`, t.trans_date, t.clerk, c.id company_id, c.company_tin, c.company_name, c.branch, c.business_type,
    -- query pharmacy
    p.`desc_nondrug`, d.generic_name, d.brand, d.dose, d.unit, d.is_otc,  d.max_monthly, d.max_weekly, p.quantity,  p.unit_price, p.vat_exempt_price, p.discount_price, p.payable_price    
FROM transaction t
LEFT JOIN pharmacy p ON p.transaction_id = t.id
LEFT JOIN member m ON t.member_id = m.id
LEFT JOIN company c ON t.company_id = c.id 
LEFT JOIN drug d ON p.drug_id = d.id 
WHERE p.transaction_id = t.id
;

DROP VIEW IF EXISTS `view_food_transactions`;
CREATE VIEW `view_food_transactions` AS 
(
SELECT  m.id `member_id`, m.osca_id, m.first_name, m.last_name, t.id `trans_number`, t.trans_date, t.clerk, c.id company_id, c.company_tin, c.company_name, c.branch, c.business_type,
    -- query food
    f.`desc`, f.vat_exempt_price, f.discount_price, f.payable_price
FROM transaction t
LEFT JOIN food f ON f.transaction_id = t.id
LEFT JOIN member m ON t.member_id = m.id
LEFT JOIN company c ON t.company_id = c.id 
WHERE f.transaction_id = t.id
);

DROP VIEW IF EXISTS view_transportation_transactions;
CREATE VIEW view_transportation_transactions AS 
(
SELECT  m.id `member_id`, m.osca_id, m.first_name, m.last_name, t.id `trans_number`, t.trans_date, t.clerk, c.id company_id, c.company_tin, c.company_name, c.branch, c.business_type,
    -- query pharmacy
    t1.`desc`,t1.vat_exempt_price, t1.discount_price, t1.payable_price
FROM transaction t
LEFT JOIN transportation t1 ON t1.transaction_id = t.id
LEFT JOIN member m ON t.member_id = m.id
LEFT JOIN company c ON t.company_id = c.id 
WHERE t1.transaction_id = t.id
);

DROP VIEW IF EXISTS `view_all_transactions`;
CREATE VIEW `view_all_transactions` AS 
SELECT m.id `member_id`, m.osca_id, m.first_name, m.last_name, t.id `trans_number`, t.trans_date, t.clerk, c.id company_id, c.company_tin, c.company_name, c.branch, c.business_type,
        -- query pharmacy
         concat("[", UCASE(LEFT(generic_name, 1)), LCASE(SUBSTRING(generic_name, 2)), "], ", UCASE(LEFT(brand, 1)), LCASE(SUBSTRING(brand, 2)),  ", ", dose, unit,  ", ", quantity,  "pcs, P ",  unit_price, "/pc") AS `desc`, p.vat_exempt_price, p.discount_price, p.payable_price    
    FROM transaction t
    LEFT JOIN pharmacy p ON p.transaction_id = t.id
    LEFT JOIN member m ON t.member_id = m.id
    LEFT JOIN company c ON t.company_id = c.id 
    LEFT JOIN drug d ON p.drug_id = d.id 
    WHERE p.transaction_id = t.id AND (p.`desc_nondrug` IS NULL OR p.`desc_nondrug` = "")
UNION
SELECT m.id `member_id`, m.osca_id, m.first_name, m.last_name, t.id `trans_number`, t.trans_date, t.clerk, c.id company_id, c.company_tin, c.company_name, c.branch, c.business_type,
        -- query pharmacy
         p.`desc_nondrug` AS `desc`, p.vat_exempt_price, p.discount_price, p.payable_price    
    FROM transaction t
    LEFT JOIN pharmacy p ON p.transaction_id = t.id
    LEFT JOIN member m ON t.member_id = m.id
    LEFT JOIN company c ON t.company_id = c.id 
    LEFT JOIN drug d ON p.drug_id = d.id 
    WHERE p.transaction_id = t.id AND p.id NOT IN (SELECT p2.id FROM pharmacy p2 WHERE (p2.`desc_nondrug` IS NULL OR p2.`desc_nondrug` = ""))
UNION
SELECT m.id `member_id`, m.osca_id, m.first_name, m.last_name, t.id `trans_number`, t.trans_date, t.clerk, c.id company_id, c.company_tin, c.company_name, c.branch, c.business_type,
        -- query transpo
         t1.`desc`,t1.vat_exempt_price, t1.discount_price, t1.payable_price
    FROM transaction t
    LEFT JOIN transportation t1 ON t1.transaction_id = t.id
    LEFT JOIN member m ON t.member_id = m.id
    LEFT JOIN company c ON t.company_id = c.id 
    WHERE t1.transaction_id = t.id
UNION 
SELECT m.id `member_id`, m.osca_id, m.first_name, m.last_name, t.id `trans_number`, t.trans_date, t.clerk, c.id company_id, c.company_tin, c.company_name, c.branch, c.business_type,
        -- query food
         f.`desc`,f.vat_exempt_price, f.discount_price, f.payable_price
    FROM transaction t
    LEFT JOIN food f ON f.transaction_id = t.id
    LEFT JOIN member m ON t.member_id = m.id
    LEFT JOIN company c ON t.company_id = c.id 
    WHERE f.transaction_id = t.id
;

DROP VIEW IF EXISTS `view_members_with_guardian`;
CREATE VIEW `view_members_with_guardian` AS 
(
SELECT m.`id` member_id, m.`osca_id`, m.`nfc_serial`, m.`password`, m.`first_name`, m.`middle_name`, m.`last_name`, m.`sex`, 
    concat(day(`birth_date`), ' ', monthname(`birth_date`), ' ', year(`birth_date`)) `bdate`, 
    YEAR(CURDATE()) - YEAR(birth_date) - IF(STR_TO_DATE(CONCAT(YEAR(CURDATE()), '-', MONTH(birth_date), '-', DAY(birth_date)) ,'%Y-%c-%e') > CURDATE(), 1, 0) age,
    concat(day(`membership_date`), ' ', monthname(`membership_date`), ' ', year(`membership_date`)) `memship_date`, 
    m.`contact_number`, m.`email`, m.`picture` `picture`, 
    g.id `g_id`, g.`first_name` `g_first_name`, g.`middle_name` `g_middle_name`, g.`last_name` `g_last_name`, g.`sex` `g_sex`, 
    g.`contact_number` `g_contact_number`, g.`email` `g_email`, g.`relationship` `g_relationship`,
    a.`address1` `address_1`, a.`address2` `address_2`, a.`city` `city`, a.`province` `province`, a.`is_active` `a_is_active`, a.`last_update` `a_last_update`
FROM member m
INNER JOIN guardian g ON g.`member_id` = m.id
INNER JOIN address_jt ajt on ajt.`member_id` = m.id
INNER JOIN address a on ajt.`address_id` = a.id
);

DROP VIEW IF EXISTS `view_complaints`;
CREATE VIEW `view_complaints` AS 
(
SELECT `desc`, `report_date`, `company_id`, `member_id`,
    c.company_tin, c.company_name, c.branch, c.business_type,
    m.osca_id, m.first_name, m.last_name
FROM `complaint_report` cr
LEFT JOIN member m ON cr.member_id = m.id
LEFT JOIN company c ON cr.company_id = c.id
);

-- view_drugs
DROP VIEW IF EXISTS `view_drugs`;
CREATE VIEW `view_drugs` AS 
SELECT * FROM `db_osca`.`drug`;

-- view_qr_request
DROP VIEW IF EXISTS `view_qr_request`;
CREATE VIEW `view_qr_request` AS 
SELECT * FROM `db_osca`.`qr_request`;
