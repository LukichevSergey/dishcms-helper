// использует функцию базы данных _fs_sef_ru (_fs_sef_ru.sql)
if(isset($_GET['run_rename_images'])) {
        global $DB;
        $sql="SELECT `f`.`ID`, `f`.`SUBDIR`, `f`.`FILE_NAME`,"
        . "CONCAT(_fs_sef_ru(`e`.`NAME`), '.', SUBSTRING_INDEX(`f`.`FILE_NAME`, '.', -1)) AS `NEW_FILE_NAME`"
        . "FROM `b_iblock_element` `e` INNER JOIN `b_file` `f` ON (`e`.`PREVIEW_PICTURE`=`f`.`ID` OR `e`.`DETAIL_PICTURE`=`f`.`ID`)";
        if($res=$DB->Query($sql)) {
                $queries=array();
                while ($row=$res->Fetch()) {
                        $OLD_FILE_NAME=$_SERVER["DOCUMENT_ROOT"] . "/upload/" . $row["SUBDIR"] . "/" . $row["FILE_NAME"];
                        $NEW_FILE_NAME=$_SERVER["DOCUMENT_ROOT"] . "/upload/" . $row["SUBDIR"] . "/" . $row["NEW_FILE_NAME"];
                        if(is_file($OLD_FILE_NAME)) {
                                if(!is_file($NEW_FILE_NAME) && copy($OLD_FILE_NAME, $NEW_FILE_NAME)) {
                                }
                                $queries[]="UPDATE `b_file` SET `FILE_NAME`='" . $row["NEW_FILE_NAME"] . "' WHERE `ID`=" . (int)$row["ID"];
                        }
                }
                if(!empty($queries)) {
                        echo implode(';', $queries);
//                      $DB->Query(implode(';', $queries));
                }
        }
        exit;
}

