<?
/**
 * https://looklikecat.ru
  0 => string 'id' (length=2)
  1 => string 'NAME' (length=4)
  2 => string 'IBLOCK_SECTION_ID' (length=17)
  3 => string 'IBLOCK_SECTION_ID' (length=17)
  4 => string 'NAME' (length=4)
**/
error_reporting(E_ALL);
ini_set('display_errors', 1);

$words=[
  73 =>  ['Летнее', 1],
  72 =>  ['Вечернее', 1],
  91 =>  ['Длинная', 1],
  74 =>  ['Коктейльное', 1],
  75 =>  ['Трикотажное', 1],
  95 =>  ['Офисное', 1],
  93 =>  ['Кожаная', 1],
  90 =>  ['Джинсовая', 1],
  100 => ['Расклешенная', 1],
  92 =>  ['Юбка-карандаш', 0],
  76 =>  ['Платье-футляр', 0],
];
if(isset($_POST['submit'])) {
    $ords=[];
    $i=1;
    foreach($words as $sid=>$word) {
        $ords[$sid]=$i++;
    }
    
    if(($h = fopen($_FILES['csv']['tmp_name'], "r")) !== FALSE){
        fgetcsv($h);
        $cols=[];
        $sections=[];
        while($row=fgetcsv($h)) {
            $id=(int)$row[0];
            $sid=(int)$row[2];
            $rsid=(int)$row[3];
            if(!isset($cols[$id])) {
                $cols[$id]=['s'=>[], 'n'=>$row[1]];
            }
            $cols[$id]['s'][$ords[$rsid]]=$rsid;
            if(!isset($sections[$rsid])) {
                $sections[$rsid]=$row[4];
            }
        }
        fclose($h);
        var_dump($sections);
        
        $dbgnames=[];
        $names=[];
        foreach($cols as $id=>$data) {
            $name='';
            ksort($data['s'], SORT_NATURAL);
            $idx=0;
            foreach($data['s'] as $sid) {
                if(!isset($words[$sid])) { var_dump('not_found'); continue; }
                if($idx++) $t=mb_strtolower($words[$sid][0]);
                else $t=$words[$sid][0];
                $name.=$t.' ' . ($words[$sid][1]?'@@@':'');
            }
            $name=preg_replace('/@@@$/', '\\\\1', trim($name));
            $name=preg_replace('/@@@/', '', $name);
            $rname='/('.preg_replace('/\s+/', '|', trim(mb_strtolower(str_replace('\1', '', $name)))).')/i';
            $oname=trim(preg_replace($rname, '', mb_strtolower($data['n'])));
            if(preg_match('/(платье|юбка)-/', $oname)) {
                $dbgnames[$id]=[$rname, $name, $data['n'], $oname, preg_replace('/(платье[^\s]+|юбка[^\s]+)/i', $name, $oname)];
                $names[$id]=preg_replace('/(платье[^\s]+|юбка[^\s]+)/i', $name, $oname);
            }
            elseif(preg_match('/(платье|юбка)/', $oname)) {
                $dbgnames[$id]=[$rname, $name, $data['n'], $oname, preg_replace('/(платье|юбка)/i', $name, $oname)];
                $names[$id]=preg_replace('/(платье|юбка)/i', $name, $oname);
            }
        }
        var_dump($names);
        
        $query=[];
        foreach($names as $id=>$name) {
            $query[]="UPDATE `b_iblock_element` SET `NAME`='{$name}' WHERE `ID`={$id}";
        }
        var_dump(implode(";\n", $query));
    }
}
?>
<form method="post" enctype="multipart/form-data">
    <input type="file" name="csv">
    <input type="submit" name="submit" value="run" />
</form>
