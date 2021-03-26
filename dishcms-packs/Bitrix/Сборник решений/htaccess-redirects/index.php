<?php
if(strpos($_SERVER['REQUEST_URI'], '/index.php') === 0) {
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: /");
	exit();
}
....
