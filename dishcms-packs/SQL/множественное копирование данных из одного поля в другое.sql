create table if not exists `tmp` (id int(11), txt TEXT);
truncate tmp;
insert into tmp (id,txt) select id,description from product;
update product as t set description_kemerovo=(select txt from tmp where tmp.id=t.id), description_novokuznetsk=(select txt from tmp where tmp.id=t.id);


<?
if(isset($_GET['sql'])) {
$data=array(
	array('category','description'),
	array('product','description'),
	array('page','text'),
	array('metadata','meta_title'),
	array('metadata','meta_key'),
	array('metadata','meta_desc'),
	array('metadata','h1_title')
);
$sql='';
foreach(DRegion::getPostfixs() as $postfix) {
	foreach($data as $d) {
		$sql.="
create table if not exists `tmp` (id int(11), txt TEXT);
truncate tmp;
insert into tmp (id,txt) select id,{$d[1]} from {$d[0]};
update {$d[0]} as t set {$d[1]}{$postfix}=(select txt from tmp where tmp.id=t.id);";
	}
}
echo $sql;
}
?>