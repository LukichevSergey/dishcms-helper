SET @i:=0; SET @a:=''; SET @alias:=''; 
SELECT IF(@alias:=LOWER(CONCAT(_fs_sef_ru(`name_ru-RU`), '-', `product_ean`)),
IF(@a=@alias,CONCAT(@alias,'-',@i:=@i+1),IF(@i:=0,0,@a:=@alias)),@alias) AS a 
from d4lwz_jshopping_products order by `name_ru-RU`
