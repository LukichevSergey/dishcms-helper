/** 
 * использует функцию _fs_sef_ru (ЧПУ транслитерация ГОСТ 7.79-2000.sql)
 */

INSERT INTO `oc_url_alias` (`query`, `keyword`) 
SELECT CONCAT('category_id=', `category_id`), _fs_sef_ru(`name`) 
FROM `oc_category_description` AS `c` LEFT JOIN `oc_url_alias` AS `a` ON (`a`.`query`=CONCAT('category_id=',`c`.`category_id`)) 
WHERE `a`.`query` IS NULL;

# проверка

SELECT a.keyword, c.category_id, _fs_sef_ru(c.name), c.name FROM `oc_category_description` as c 
LEFT JOIN  oc_url_alias as a ON(a.query=CONCAT('category_id=',c.category_id)) WHERE `a`.`query` IS NULL;
