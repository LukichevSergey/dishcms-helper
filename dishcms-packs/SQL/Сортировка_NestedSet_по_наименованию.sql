// только для фиксированного уровня вложенности. Пример для 3-х уровней вложенности
SET @title := "";
SET @chtitle1 := "";
SET @chtitle2 := "";
SELECT * FROM (SELECT *, 
  (@title:=IF(root=id,title,@title)) as root_title, 
  (@chtitle1:=IF(level=2,title,@chtitle1)) as child_lvl1_title,
  (@chtitle2:=IF(level=3,title,@chtitle2)) as child_lvl2_title
  FROM`category` WHERE `level` < 4 ORDER BY `root`, `lft`) AS t 
ORDER BY root_title, IF(level=1,0,root), root, child_lvl1_title, IF(level=2,0,lft), child_lvl2_title, lft
