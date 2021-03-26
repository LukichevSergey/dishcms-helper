<?
if(!empty($_GET['f'])) {
	$_GET['f']=preg_replace('/^(..\/)+/', '/', urldecode($_GET['f']));
}
if(!empty($_GET['f']) && (strpos($_GET['f'], '/files/') === 0) && is_file($_SERVER['DOCUMENT_ROOT'].$_GET['f'])) {
	$filename=preg_replace('#^.*?/([^/]+)$#', '\\1', $_GET['f']);
	Header("HTTP/1.1 200 OK");
	Header("Connection: close");
	Header("Content-Type: application/octet-stream");
	Header("Accept-Ranges: bytes");
	Header("Content-Disposition: Attachment; filename={$filename}");
	Header("Content-Length: ".filesize($_SERVER['DOCUMENT_ROOT'].$_GET['f']));
 
	readfile($_SERVER['DOCUMENT_ROOT'].$_GET['f']);
}
else {
	header("HTTP/1.0 404 Not Found");
	exit;
}
?>