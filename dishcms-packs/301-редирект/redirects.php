<?php
// вставить во входной файл include dirname(__FILE__) . '/redirects.php';
// обратите внимение, что по умолчанию стоит протокол HTTPS
$redirects=[
	'/старая-ссылка'=>'/новая-ссылка',
];
// Редиректы по регулярному выражению, вида array(регулярное_выражение_для_входного_url, выражение_замены_редиректа_для_preg_replace)
$mredirects=[ 
];
$k=preg_replace('/^(.*?)(\?.*)$/', '$1', $_SERVER['REQUEST_URI']);
if(isset($redirects[$k])) {
    if($k != $redirects[$k]) {
        header("HTTP/1.1 301 Moved Permanently");
        header('Location: https://'.$_SERVER['SERVER_NAME'].'/'.ltrim($redirects[$k], '/'));
        exit();
    }
}
else {
    foreach($mredirects as $inPattern=>$outPattern) {
        if(preg_match($inPattern, $k)) {
            header("HTTP/1.1 301 Moved Permanently");
            header('Location: https://'.$_SERVER['SERVER_NAME'].'/'.ltrim(preg_replace($inPattern, $outPattern, $k), '/'));
            exit();
        }
    }
}

