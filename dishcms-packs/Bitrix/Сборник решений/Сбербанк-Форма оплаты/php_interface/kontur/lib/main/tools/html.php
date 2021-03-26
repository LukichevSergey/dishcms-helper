<?
/**
 * Tools. Html.
 *
 * Пример использования:
 * require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/kontur.core/lib/main/tools/html.php');
 * use \Kontur\Core\Main\Tools\Html;
 * $isAjax=Html::beginAjaxPage("Обратная связь");
 * контент страницы
 * Html::endAjaxPage($isAjax);
 */
namespace Kontur\Core\Main\Tools;

class Html
{
    /**
     * @return boolean выполняется AJAX-запрос (TRUE) или нет (FALSE).
     */
    public static function beginAjaxPage($title, $onlyAjax=false)
    {
        global $APPLICATION;
        
        $isAjax=false;
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) || ($_REQUEST['AJAX_CALL']=='Y')) {
            require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
        	$isAjax=true;
        } 
        else {
            if($onlyAjax) {
            	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
		        \CHTTP::SetStatus("404 Not Found");
		        $APPLICATION->RestartBuffer();
		        exit;
            }
            else {
                require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
                $APPLICATION->SetTitle($title);
            }
        }
        
        return $isAjax;
    }
    
    public static function endAjaxPage($isAjax)
    {
        if(!$isAjax) {
            require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
        }
    }
}