<?require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/kontur.core/lib/main/tools/html.php');
use \Kontur\Core\Main\Tools\Html;
$isAjax=Html::beginAjaxPage("Обратная связь");
?><?$APPLICATION->IncludeComponent(
	"kontur:feedback.form", 
	"", 
	array(),
	false
);?><?
Html::endAjaxPage($isAjax);
?>
