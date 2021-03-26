<?
/**
 * Контроллер ПВЗ
 *
 */
namespace cdek\controllers;

use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use common\components\helpers\HAjax;
use cdek\models\City;
use cdek\components\CdekApi;

class PvzController extends \Controller
{
    /**
     * {@inheritDoc}
     * @see \CController::filters()
     */
    public function filters()
    {
        return A::m(parent::filters(), [
            'ajaxOnly +list'
        ]);
    }
    
    /**
     * Получить список ПВЗ
     * @param integer $_REQUEST[cdek_id] СДЭК-идентификатор города, для которого получается список ПВЗ
     */
    public function actionList()
    {
        $ajax=HAjax::start();
        
        $cityId=R::rget('cdek_id');
        if(is_numeric($cityId) && (int)$cityId) {
            $ajax->data['pvz']=CdekApi::i()->getPvzList($cityId, true);
            $ajax->success=true;
        }
        
        $ajax->end();
    }
}
