<?php
if(preg_match('/^([^?]+)\?(.*)$/', $_SERVER['REQUEST_URI'], $REQUEST_URI_M)) {
	$REQUEST_URI=$REQUEST_URI_M[1];
	$QUERY_STRING=$REQUEST_URI_M[2];
}
else {
	$REQUEST_URI=$_SERVER['REQUEST_URI'];
	$QUERY_STRING=false;
}
if ($REQUEST_URI !== strtolower($REQUEST_URI)) {
	$https = isset($_SERVER['HTTPS']) && (strcasecmp($_SERVER['HTTPS'],'on')===0 || $_SERVER['HTTPS']==1)
        || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'],'https')===0;
    $http = $https ? 'https' : 'http';
	$url = $http . '://' . $_SERVER['HTTP_HOST'] . strtolower($REQUEST_URI) .($QUERY_STRING ? '?'.$QUERY_STRING : '');
	header("HTTP/1.1 301 Moved Permanently"); 
	header("Location: " . $url); 
	exit(); 
}
