
DROP VIEW IF EXISTS pharma_transactions;
CREATE VIEW pharma_transactions AS 
(
SELECT  m.id `member_id`, m.first_name, m.last_name, t.id `trans_number`, t.trans_date, c.company_name, c.branch, c.business_type,
     -- query pharmacy
     d.generic_name, d.brand, d.dose, d.unit, p.quantity,  p.unit_price, p.vat_exempt_price, p.discount_price, p.payable_price	
FROM transaction t
LEFT JOIN pharmacy p on p.transaction_id = t.id
LEFT JOIN member m on t.member_id = m.id
LEFT JOIN company c on t.company_id = c.id 
LEFT JOIN drug d on p.drug_id = d.id 
WHERE p.transaction_id = t.id
);

DROP VIEW IF EXISTS food_transactions;
CREATE VIEW food_transactions AS 
(
SELECT  m.id `member_id`, m.first_name, m.last_name, t.id `trans_number`, t.trans_date, c.company_name, c.branch, c.business_type,
     -- query food
     f.`desc`, f.vat_exempt_price, f.discount_price, f.payable_price
FROM transaction t
LEFT JOIN food f on f.transaction_id = t.id
LEFT JOIN member m on t.member_id = m.id
LEFT JOIN company c on t.company_id = c.id 
WHERE f.transaction_id = t.id
);

DROP VIEW IF EXISTS transportation_transactions;
CREATE VIEW transportation_transactions AS 
(
SELECT  m.id `member_id`, m.first_name, m.last_name, t.id `trans_number`, t.trans_date, c.company_name, c.branch, c.business_type,
     -- query pharmacy
     t1.`desc`,t1.vat_exempt_price, t1.discount_price, t1.payable_price
FROM transaction t
LEFT JOIN transportation t1 on t1.transaction_id = t.id
LEFT JOIN member m on t.member_id = m.id
LEFT JOIN company c on t.company_id = c.id 
WHERE t1.transaction_id = t.id
);

DROP VIEW IF EXISTS view_all_transactions;
CREATE VIEW view_all_transactions AS 

SELECT * FROM
	(SELECT  m.id `member_id`, m.first_name, m.last_name, t.id `trans_number`, t.trans_date, c.company_name, c.branch, c.business_type,
		 -- query pharmacy
         
		 concat(UCASE(LEFT(d.generic_name, 1)), LCASE(SUBSTRING(d.generic_name, 2)), ", ", UCASE(LEFT(d.brand, 1)), LCASE(SUBSTRING(d.brand, 2)),  ", ", d.dose, d.unit,  ", ", p.quantity,  "pcs, P ",  p.unit_price, "/pc") as `desc`, p.vat_exempt_price, p.discount_price, p.payable_price	
	FROM transaction t
	LEFT JOIN pharmacy p on p.transaction_id = t.id
	LEFT JOIN member m on t.member_id = m.id
	LEFT JOIN company c on t.company_id = c.id 
	LEFT JOIN drug d on p.drug_id = d.id 
	WHERE p.transaction_id = t.id) AS T1
UNION
SELECT * FROM
	(SELECT  m.id `member_id`, m.first_name, m.last_name, t.id `trans_number`, t.trans_date, c.company_name, c.branch, c.business_type,
		 -- query pharmacy
		 t1.`desc`,t1.vat_exempt_price, t1.discount_price, t1.payable_price
	FROM transaction t
	LEFT JOIN transportation t1 on t1.transaction_id = t.id
	LEFT JOIN member m on t.member_id = m.id
	LEFT JOIN company c on t.company_id = c.id 
	WHERE t1.transaction_id = t.id) AS T2
UNION 
SELECT * FROM
	(SELECT  m.id `member_id`, m.first_name, m.last_name, t.id `trans_number`, t.trans_date, c.company_name, c.branch, c.business_type,
		 -- query pharmacy
		 t1.`desc`,t1.vat_exempt_price, t1.discount_price, t1.payable_price
	FROM transaction t
	LEFT JOIN transportation t1 on t1.transaction_id = t.id
	LEFT JOIN member m on t.member_id = m.id
	LEFT JOIN company c on t.company_id = c.id 
	WHERE t1.transaction_id = t.id) AS T3
;

SELECT * from pharma_transactions
WHERE `member_id` = 2
ORDER BY trans_date;
SELECT * from food_transactions
WHERE member_id = 2
ORDER BY trans_date;
SELECT * from transportation_transactions
WHERE member_id = 2
ORDER BY trans_date;
SELECT *from view_all_transactions
WHERE member_id = 2
ORDER BY trans_date;