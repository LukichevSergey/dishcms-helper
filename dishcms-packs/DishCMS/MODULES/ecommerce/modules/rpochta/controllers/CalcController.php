<?
/**
 * Контроллер
 *
 */
namespace rpochta\controllers;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use common\components\helpers\HAjax;
use common\components\helpers\HHtml;
use common\components\helpers\HTools;
use settings\components\helpers\HSettings;
use rpochta\components\helpers\HRPochta;
use rpochta\components\RPochtaApi;

class CalcController extends \Controller
{
    /**
	 * (non-PHPdoc)
	 * @see \CController::filters()
	 */
	public function filters()
	{
		return A::m(parent::filters(), [
            //'ajaxOnly +index'
		]);
	}
    
    public function actionIndex()
    {
        $ajax=HAjax::start();
        
        if($errors=$this->getCalcErrors()) {
            $ajax->errors=$errors;
            $ajax->end();            
        }
        
        $result=(array)RPochtaApi::i()->tariff();
        
        if(isset($result['errors'])) {
            $ajax->errors=$result['errors'];
            $ajax->data['html']=$this->renderPartial('rpochta.views.calc._errors', compact('result'), true);
        }
        else {
            $ajax->data['html']=$this->renderPartial('rpochta.views.calc._result', compact('result'), true);
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
        if(!HRPochta::settings()->rpochta_index_from) $errors[]='Не задан город-отправитель';
        
        // проверяем город-получатель
        if(!R::rget('index_to')) $errors[]='Не задан город-получатель';
        
        return $errors;
    }
}
