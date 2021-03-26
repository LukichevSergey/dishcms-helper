<?
/**
 * Переадресация 301
 * 1) ЧПУ
 * 2) с www на без www
 * 3) /index на /
 * 4) слэш на конце или без.
 */
function kontur_sef_301($file=null) 
{
    $serverName='http://'.$_SERVER['SERVER_NAME']; // заменить на домен

	// установить в false, если нужно добавлять слэш на конце.
	$nonTailSlash=false;

    $fRedirect=function($url) use ($serverName, $qs) {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: {$serverName}{$url}{$qs}"); 
        exit;
    };

    $uri=preg_replace('/^([^?]+).*?$/', '$1', $_SERVER['REQUEST_URI']);
    if(!$uri || ($uri == '/')) {
    	if(strpos($_SERVER['HTTP_HOST'], 'www.') === 0) {
    		$fRedirect('/');
    	}
    	return;
    }

    $qs=preg_replace('/^([^?]+)(.*)?$/', '$2', $_SERVER['REQUEST_URI']);

    if($nonTailSlash) {
    	$doRedirect=(substr($uri, -1, 1) == '/');
    }
    else {
    	$doRedirect=(substr($uri, -1, 1) != '/');
    	if($doRedirect) $uri.='/';
   	}
    $doRedirect |= (strpos($_SERVER['HTTP_HOST'], 'www.') === 0);
    $_SERVER['REQUEST_URI']=$uri;
    
    // [ссылка входная, ссылка для системы, чпу]
    $sefConfig=[
        ['/about/contacts.php', '/about/contacts.php', '/contacts/']
    ];
    
    if(strpos($uri, '/index') === 0) {
        $fRedirect('/');
    }

    foreach($sefConfig as $cfg) {
        if($uri == $cfg[0]) $fRedirect($cfg[2]);
        elseif($uri == $cfg[2]) {
            $_SERVER['REQUEST_URI']=$cfg[1];
            $_REQUEST['q']=trim($cfg[1],'/');
            if($doRedirect) $fRedirect($cfg[2]);
        }    
    }
    
    if($doRedirect) {
        $fRedirect($_SERVER['REQUEST_URI']);
    }
}
kontur_sef_301();