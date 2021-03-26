<?
// замена текста в файлах
exec('find ./ -name "*.php" -type f -exec grep -il "<строка>" {} +', $files);
$r=array();
$not=array();
//$p='/\/\/###=i=###(.*?)\/\/###=i=###/sm';
$p='/<что заменять>/sm';
$pr='<на что заменять>';
if(!empty($files)) {
    foreach($files as $file) {
        if($file == './freplace.php') continue;
        $c=file_get_contents($_SERVER['DOCUMENT_ROOT'].'/'.$file);
        preg_match_all($p, $c, $m);
        $c2=preg_replace($p, $pr, $c);
        if(strlen($c2) != strlen($c)) {
            $r[$file]=$m;
/* uncomment for write */ // file_put_contents($_SERVER['DOCUMENT_ROOT'].'/'.$file, $c2);
        }
        else $not[$file]=$m;
    }
}
echo '<pre>';
echo '<b>Replaced:</b><br/>';
print_r($r);
echo '<b>Not replaced:</b><br/>';
print_r($not);
