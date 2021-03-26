<?
/**
 * Контроллер
 *
 */
namespace cdek\controllers;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use common\components\helpers\HAjax;
use common\components\helpers\HHtml;
use common\components\helpers\HTools;
use cdek\components\CdekApi;
use cdek\components\helpers\HCdek;

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
        
        $result=CdekApi::calc([
            'rec_city_id'=>(int)R::rget('rec'),
            'mode'=>(int)R::rget('mode')
        ]);
        
        if(isset($result['errors'])) {
            $ajax->data['errors']=$result['errors'];
            $ajax->data['html']=$this->renderPartial('cdek.views.calc._errors', compact('result'), true);
        } else {
            $ajax->data['result']=$result;
            if(isset($result['result'])) {
                $ajax->data['html']=$this->renderPartial('cdek.views.calc._result', compact('result'), true);
            }
            else {
                $ajax->data['html']='';
            }
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
        if(!HCdek::settings()->cdek_send_city_id) $errors[]='Не задан город-отправитель';
        
        // проверяем город-получатель
        if(!R::rget('rec')) $errors[]='Не задан город-получатель';
        
        // проверяем группу тарифов
        if(!HCdek::settings()->cdek_tariff_group) $errors[]='Не задана группа тарифов';
        
        // проверяем режим доставки
        if(!R::rget('mode')) $errors[]='Не задан режим доставки';
        
        return $errors;
    }
}
