<?php
if (!class_exists("CFileCacheCleaner")) {
   	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/cache_files_cleaner.php");
}

// очистка кэша
$obCacheCleaner = new CFileCacheCleaner("all");
$obCacheCleaner->Start();
while ($file = $obCacheCleaner->GetNextFile()) {
    if (is_string($file)) {
        unlink($file);
    }
}