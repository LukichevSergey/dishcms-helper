<?
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "File.php");
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "Autoload.php");

use Bitrix\Kontur\Core\File as File; 

\Bitrix\Kontur\Core\Autoload::moduleAutoloadClasses("kontur.core", array(
	"\Bitrix\Kontur\Core\IBlock\IBlock" => File::getPath(array('lib','iblock','IBlock.php'))
));
?>