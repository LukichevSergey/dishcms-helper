<?php
// Вставить в начало файла 404.php
if(preg_match('/\.(jpg|png|jpeg|css|js)$/', preg_replace('/^([^?]+)\??.*$/', '$1', $_SERVER['REQUEST_URI']))) {
	header("HTTP/1.1 404 Not Found");
	exit;
}
