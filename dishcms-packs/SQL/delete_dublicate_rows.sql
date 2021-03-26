Пример удаления дубликатов записей, с сохранением первой уникальной записи.

SET @pid:=0;SET @v:='';
DELETE FROM `eav_value` WHERE `id` IN (
SELECT * FROM (
    SELECT delid FROM (SELECT @pid,@v,id_product,value,IF(@pid=id_product AND @v=`value`,`id`,NULL) as delid, @pid:=id_product, @v:=`value`
    FROM `eav_value`
    WHERE `id_attrs` = '16'
    ORDER BY `id_attrs`, `id_product`, `value`
) as t3 WHERE delid>0) as t4
) AND `id_attrs`=16
