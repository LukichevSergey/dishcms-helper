/** 
 * использует функцию _fs_sef_ru (ЧПУ транслитерация ГОСТ 7.79-2000.sql)
 */

INSERT INTO `oc_url_alias` (`query`, `keyword`) 
SELECT CONCAT('product_id=', `product_id`), _fs_sef_ru(`name`) 
FROM `oc_product_description` AS `p` LEFT JOIN `oc_url_alias` AS `a` ON (`a`.`query`=CONCAT('product_id=',`p`.`product_id`)) 
WHERE `a`.`query` IS NULL;

# проверка

SELECT a.keyword, p.product_id, _fs_sef_ru(p.name), p.name FROM `oc_product_description` as p 
LEFT JOIN  oc_url_alias as a ON(a.query=CONCAT('product_id=',p.product_id)) WHERE `a`.`query` IS NULL;
