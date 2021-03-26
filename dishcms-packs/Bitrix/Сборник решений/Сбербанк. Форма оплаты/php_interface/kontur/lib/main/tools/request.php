<?
/**
 * Tools. Request.
 */
namespace Kontur\Core\Main\Tools;

class Request
{
    public static function isAjax()
    {
    	return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) || ($_REQUEST['AJAX_CALL']=='Y'));
    }

    public static function http404()
    {
        global $APPLICATION;
        
        \CHTTP::SetStatus("404 Not Found");
        $APPLICATION->RestartBuffer();
        exit;
    }

    public static function endAjax($result=null, $json=true, $forcyOutputResult=true)
    {
    	global $APPLICATION;
    	$APPLICATION->RestartBuffer();

        if($forcyOutputResult || ($result !== null)) {
            if($json) {
                echo json_encode($result);
            }
            else {
                echo $result;
            }
        }

       	die;
    }
    
    public static function end($message=null)
    {
    	global $APPLICATION;
    	$APPLICATION->RestartBuffer();
        
        if($message !== null) {
            echo $message;
        }

       	die;
    }
}