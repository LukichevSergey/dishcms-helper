<?
/**
 * Контроллер
 *
 */
namespace pecom\controllers;

use common\components\helpers\HArray as A;
use common\components\helpers\HAjax;
use settings\components\helpers\HSettings;
use pecom\components\PecomApi;
use pecom\components\helpers\HPecom;

class CalcController extends \Controller
{
    /**
	 * (non-PHPdoc)
	 * @see \CController::filters()
	 */
	public function filters()
	{
		return A::m(parent::filters(), [
            'ajaxOnly +index'
		]);
	}
    
    public function actionIndex()
    {
        $ajax=HAjax::start();
        
        if($errors=$this->getCalcErrors()) {
            $ajax->errors=$errors;
            $ajax->data['html']=$this->renderPartial('pecom.views.calc._errors', compact('errors'), true);
            $ajax->end();            
        }
        
        $result=HPecom::calc(A::rget($_POST, 'deliver.town'), $isOversized, $params);
		
		if(!$result || isset($result['errors']) || !(isset($result['auto']) || isset($result['avia']))) {
            $ajax->errors=$result['error'];
            $ajax->data['html']=$this->renderPartial('pecom.views.calc._errors', compact('result'), true);
        }
        else {
            $ajax->data['html']=$this->renderPartial('pecom.views.calc._result', compact('result', 'isOversized'), true);
            $ajax->success=true;
        }
        
        $ajax->end();
    }
    
    protected function getCalcErrors()
    {
        $errors=false;
        
         // проверяем, что в корзине есть товары
        if(\Yii::app()->cart->isEmpty()) $errors[]='Корзина пуста';
        
        // проверяем город-отправитель
        if(!HPecom::settings()->pecom_take_town) $errors[]='Не задан город-отправитель';
        
        // проверяем город доставки
        if(!A::rget($_POST, 'deliver.town')) $errors[]='Не задан город доставки';
        
        return $errors;
    }
}
