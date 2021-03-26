<?php
$handle = fopen("http://corp.kontur-lite.ru".$_SERVER['REQUEST_URI'], "r");
while (!feof($handle)) {
    $buffer = fgets($handle, 4096);
    echo $buffer;
}
fclose($handle);