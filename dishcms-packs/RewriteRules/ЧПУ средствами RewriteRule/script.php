<?php 
/**
 * Скрипт генерации правил редиректов
 */
// --------------
!!! Вставить в entrypoint-файл (index.php)
!!! if(!empty($_SERVER['REDIRECT_rsef'])) $_SERVER['REQUEST_URI']=$_SERVER['REDIRECT_rsef'];
!!! elseif(!empty($_SERVER['REDIRECT_REDIRECT_rsef'])) $_SERVER['REQUEST_URI']=$_SERVER['REDIRECT_REDIRECT_rsef'];
// --------------------------

$urls=[
    // '/catalog/category/1'=> '/my-category-sef-example',
    ''=> '',
];

echo '<pre>';
foreach($urls as $o=>$n) {
	echo 'RewriteCond %{REQUEST_URI} ^('.$o.')(.*|$) [NC]
RewriteRule . - [E=r301_2:yes,E=rsef_tail:%2,E=rsef:'.$n.']
RewriteCond %{REQUEST_URI} ^('.$n.')(.*|$) [NC]
RewriteRule . - [E=rsef_from:%1,E=rsef_tail:%2,E=rsef:'.$o.']

';
}
?>