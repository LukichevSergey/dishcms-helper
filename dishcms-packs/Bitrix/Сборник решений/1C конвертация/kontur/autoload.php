<?php
/**
 * require_once \Bitrix\Main\Application::getDocumentRoot() . getLocalPath('php_interface/kontur/autoload.php');
 * 
 */
\Bitrix\Main\Loader::registerAutoLoadClasses(null, array(
    '\kontur\bx1c\Convert' => getLocalPath( 'php_interface/kontur/bx1c/convert.php' )
));