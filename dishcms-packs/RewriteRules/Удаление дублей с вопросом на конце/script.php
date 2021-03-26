<?php
if(strrpos($_SERVER['REQUEST_URI'], '?') === (strlen($_SERVER['REQUEST_URI'])-1)) {
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: http://".$_SERVER['SERVER_NAME']. substr($_SERVER['REQUEST_URI'], 0, -1));
    exit;
}
?>