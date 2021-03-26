/** 
 * GOST 7.79-2000
 * @author aMacedonian
 * @link http://www.sql.ru/forum/1090122/translit-funkciey
 *
 * Можно обновить как update category set alias=_fs_transliterate_ru(title);
 */
DELIMITER $$

DROP FUNCTION IF EXISTS `_fs_transliterate_ru` $$
CREATE DEFINER=`root`@`localhost`
  FUNCTION `_fs_transliterate_ru`(str TEXT)
  RETURNS text CHARSET utf8
DETERMINISTIC SQL SECURITY INVOKER
BEGIN
  DECLARE strlow TEXT;
  DECLARE sub VARCHAR(3);
  DECLARE res TEXT;
  DECLARE len INT(11);
  DECLARE i INT(11);
  DECLARE pos INT(11);
  DECLARE alphabeth CHAR(44);

  SET i = 0;
  SET res = '';
  SET strlow = LOWER(str);
  SET len = CHAR_LENGTH(str); 
  SET alphabeth = ' абвгдеёжзийклмнопрстуфхцчшщъыьэюя0123456789';

  /* идем циклом по символам строки */

  WHILE i <= len DO

  SET i = i + 1;
  SET pos = INSTR(alphabeth, SUBSTR(strlow,i,1));

  /*выполняем преобразование припомощи ф-ии ELT */

  SET sub = elt(pos, '-',
  'a','b','v','g', 'd', 'e', 'yo','zh', 'z',
  'i','j','k','l', 'm', 'n', 'o', 'p', 'r',
  's','t','u','f', 'x', 'c','ch','sh','shh',
  '', 'y', '','e','yu','ya','0','1','2','3','4','5','6','7','8','9');

  IF sub IS NOT NULL THEN
    SET res = CONCAT(res, sub);
  ELSE
    SET res = CONCAT(res, SUBSTR(strlow,i,1));
  END IF;

  END WHILE;

  RETURN TRIM(TRAILING '-' FROM res);
END $$

DELIMITER ;
