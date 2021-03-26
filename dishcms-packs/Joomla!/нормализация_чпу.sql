SELECT _fs_normalize_alias_ru(product_id, `alias_ru-RU`, `name_ru-RU`, `product_ean`) FROM d4lwz_jshopping_products LIMIT 5;

SELECT _fs_normalize_alias_ru(product_id, `alias_ru-RU`, `name_ru-RU`, `product_ean`) AS alias 
FROM d4lwz_jshopping_products HAVING alias REGEXP '\-[0-9]+$' LIMIT 5000;

UPDATE d4lwz_jshopping_products SET `alias_ru-RU`=_fs_normalize_alias_ru(product_id, `alias_ru-RU`, `name_ru-RU`, `product_ean`);
# ------------------------------ FUNCTION ------------------------------------------------------------------------------------

DELIMITER $$

DROP FUNCTION IF EXISTS `_fs_normalize_alias_ru` $$
CREATE DEFINER=`root`@`localhost`
  FUNCTION `_fs_normalize_alias_ru`(productId INT, productAlias TEXT, productName TEXT, productEan TEXT)
  RETURNS text CHARSET utf8
DETERMINISTIC SQL SECURITY INVOKER
BEGIN
  DECLARE n INT DEFAULT 1;
  DECLARE normalizeProductAlias TEXT;
  DECLARE loopProductId INT;
  DECLARE loopProductAlias TEXT;
  DECLARE curProducts CURSOR FOR SELECT `product_id`, LOWER(CONCAT(_fs_sef_ru(`name_ru-RU`), '-', `product_ean`)) FROM `d4lwz_jshopping_products` WHERE `alias_ru-RU`=productAlias ORDER BY `product_id`;
  
  SET normalizeProductAlias = LOWER(CONCAT(_fs_sef_ru(productName), '-', productEan));
  
  OPEN curProducts;
  read_loop: LOOP
  	FETCH curProducts INTO loopProductId, loopProductAlias;
	IF ((loopProductId <> productId) AND (STRCMP(loopProductAlias, productAlias) = 0)) THEN SET n = n + 1;
	ELSEIF (loopProductId = productId) THEN LEAVE read_loop;
	END IF;	
  END LOOP;
  CLOSE curProducts;

  IF (n > 1) THEN RETURN CONCAT(normalizeProductAlias, '-', n);
  ELSE RETURN normalizeProductAlias; END IF;
END $$

DELIMITER ; 
