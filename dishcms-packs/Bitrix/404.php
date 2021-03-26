<?php
добавить после
CHTTP::SetStatus("404 Not Found");
@define("ERROR_404","Y");
------------------------------------------
if(preg_match('/\.(jpg|jpeg|png|gif|tiff|bmp|svg|pdf|doc|docx|js|css)$/i', $_SERVER['REQUEST_URI'])) {
	$APPLICATION->RestartBuffer();
    exit;
}