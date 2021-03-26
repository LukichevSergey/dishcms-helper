<?
/**
 * Контроллер ОПС
 *
 * @FIXME использует города модуля СДЭК
 */
namespace rpochta\controllers;

use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use common\components\helpers\HAjax;
use common\components\helpers\HDb;
use rpochta\components\RPochtaApi;
use cdek\models\City;

class OpsController extends \Controller
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
     * @param integer $_REQUEST[postcode] почтовый индекс для которого получается список ОПС
     * @param integer $_REQUEST[address] адрес, для которого получается список ОПС
     */
    public function actionList()
    {
        $ajax=HAjax::start();

        $latitude=R::rget('latitude');
        $longitude=R::rget('longitude');
        if($latitude || $longitude) {
            if($latitude && $longitude) {
                $ajax->data['ops']=RPochtaApi::i()->opsNearby([
                    'latitude'=>$latitude,
                    'longitude'=>$longitude,
                    'top'=>50,
                ]);
                $ajax->success=true;
            }
        }
        else {
            $valided=false;
            $criteria=HDb::criteria();
            if($address=R::rget('address')) {
                $criteria->addSearchCondition('fullname', $address);
                $valided=true;
            }
            if($postcode=R::rget('postcode')) {
                $criteria->addColumnCondition(['postcode'=>$postcode]);
                $valided=true;
            }
            
            if($valided) {
                if($city=City::model()->find($criteria)) {
                    $city->updateGeoCode();
                    if($city->ym_point_x) {
                        $ajax->data['ops']=RPochtaApi::i()->opsNearby([
                            'latitude'=>$city->ym_point_y,
                            'longitude'=>$city->ym_point_x,
                            'top'=>50,
                        ]);
                        $ajax->success=true;
                    }
                }
            }
        }
        
        $ajax->end();
    }
}
