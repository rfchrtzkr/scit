CREATE DATABASE IF NOT EXISTS `scit`;
USE `scit`;

DROP VIEW IF EXISTS `view_companies`;
CREATE VIEW `view_companies` AS
(
SELECT * FROM `db_osca`.`view_companies`
);


DROP VIEW IF EXISTS `view_pharma_transactions`;
CREATE VIEW `view_pharma_transactions` AS 
(
SELECT  * FROM `db_osca`.`view_pharma_transactions`
);

DROP VIEW IF EXISTS `view_pharma_transactions_nondrug`;
CREATE VIEW `view_pharma_transactions_nondrug` AS 
(
SELECT  * FROM `db_osca`.`view_pharma_transactions_nondrug`
);

DROP VIEW IF EXISTS `view_pharma_transactions_all`;
CREATE VIEW `view_pharma_transactions_all` AS 
(
SELECT  * FROM `db_osca`.`view_pharma_transactions_all`
);

DROP VIEW IF EXISTS `view_food_transactions`;
CREATE VIEW `view_food_transactions` AS 
(
SELECT  * FROM `db_osca`.`view_food_transactions`
);

DROP VIEW IF EXISTS `view_transportation_transactions`;
CREATE VIEW `view_transportation_transactions` AS 
(
SELECT  * FROM `db_osca`.`view_transportation_transactions`
);

DROP VIEW IF EXISTS `view_all_transactions`;
CREATE VIEW `view_all_transactions` AS 
(
SELECT  * FROM `db_osca`.`view_all_transactions`
);

DROP VIEW IF EXISTS `view_members_with_guardian`;
CREATE VIEW `view_members_with_guardian` AS 
(
SELECT  * FROM `db_osca`.`view_members_with_guardian`
);

DROP VIEW IF EXISTS `view_drugs`;
CREATE VIEW `view_drugs` AS 
SELECT * FROM `db_osca`.`view_drugs`;

DROP VIEW IF EXISTS `view_qr_request`;
CREATE VIEW `view_qr_request` AS 
SELECT * FROM `db_osca`.`view_qr_request`;