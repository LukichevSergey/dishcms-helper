<?php
/**
 * $_GET['file'] filename
 */
if(!isset($_GET['file'])) {
// @link https://phpclub.ru/talk/threads/header-status-404-not-found-rewritecond-errordocument-%D0%BD%D0%B5-%D0%BC%D0%BE%D0%B3%D1%83-%D1%80%D0%B0%D0%B7%D0%BE%D0%B1%D1%80%D0%B0%D1%82%D1%8C%D1%81%D1%8F.69197/
    header('HTTP/1.0 404 Not Found');
    header('HTTP/1.1 404 Not Found');
    header('Status: 404 Not Found');
    exit;
}
 
// @link http://stackoverflow.com/questions/14661637/allowing-caching-of-image-php-until-source-has-been-changed 
header("Cache-Control: private, max-age=10800, pre-check=10800");
header("Pragma: private");
header("Expires: " . date(DATE_RFC822,strtotime(" 2 day")));

/**
 * @link http://webcodingeasy.com/Site-optimization/Cache-images-from-PHP-output
 */
// return the browser request header 
// use built in apache ftn when PHP built as module, 
// or query $_SERVER when cgi 
function getRequestHeaders() 
{ 
    if (function_exists("apache_request_headers")) 
    { 
        if($headers = apache_request_headers()) 
        { 
            return $headers; 
        } 
    } 

    $headers = array(); 
    // Grab the IF_MODIFIED_SINCE header 
    if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) 
    { 
        $headers['If-Modified-Since'] = $_SERVER['HTTP_IF_MODIFIED_SINCE']; 
    } 
    return $headers; 
}

// Return the requested graphic file to the browser 
// or a 304 code to use the cached browser copy 
function displayGraphicFile ($graphicFileName, $fileType='jpeg') 
{ 
    $fileModTime = filemtime($graphicFileName); 
    // Getting headers sent by the client. 
    $headers = getRequestHeaders(); 
    // Checking if the client is validating his cache and if it is current. 
    if (isset($headers['If-Modified-Since']) && 
        (strtotime($headers['If-Modified-Since']) == $fileModTime)) 
    { 
        // Client's cache IS current, so we just respond '304 Not Modified'. 
        header('Last-Modified: '.gmdate('D, d M Y H:i:s', $fileModTime).
                ' GMT', true, 304); 
    } 
    else 
    { 
        // Image not cached or cache outdated, we respond '200 OK' and output the image. 
        header('Last-Modified: '.gmdate('D, d M Y H:i:s', $fileModTime).
                ' GMT', true, 200); 
        header('Content-type: image/'.$fileType); 
        header('Content-transfer-encoding: binary'); 
        header('Content-length: '.filesize($graphicFileName)); 
        readfile($graphicFileName); 
    } 
} 

//example usage
displayGraphicFile(dirname(__FILE__).$_GET['file']);

?>