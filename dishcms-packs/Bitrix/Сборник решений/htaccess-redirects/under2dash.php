<?php
$requestUri=preg_replace('/^([^?]+)\??.*$/', '$1', $_SERVER['REQUEST_URI']);
if(strpos($requestUri, '_') !== false) {
	header("HTTP/1.1 301 Moved Permanently");
	$qs=$_SERVER['QUERY_STRING'];
	header("Location: " . str_replace('_', '-', $requestUri . ($qs ? '?'.$qs : '')));
	exit();
}
?>
