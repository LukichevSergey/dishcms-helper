<?
/**
 * Контроллер
 *
 */
namespace cdek\controllers;

use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use cdek\models\City;

class CityController extends \Controller
{
    protected $en2ru=['q'=>'й', 'w'=>'ц', 'e'=>'у', 'r'=>'к', 
't'=>'е', 'y'=>'н', 'u'=>'г', 'i'=>'ш', 'o'=>'щ', 
'p'=>'з', '['=>'х', ']'=>'ъ', 'a'=>'ф', 's'=>'ы', 
'd'=>'в', 'f'=>'а', 'g'=>'п', 'h'=>'р', 'j'=>'о', 
'k'=>'л', 'l'=>'д', ';'=>'ж', '\''=>'э', 'z'=>'я', 
'x'=>'ч', 'c'=>'с', 'v'=>'м', 'b'=>'и', 'n'=>'т', 
'm'=>'ь', ','=>'б', '.'=>'ю'];

    /**
	 * (non-PHPdoc)
	 * @see \CController::filters()
	 */
	public function filters()
	{
		return A::m(parent::filters(), [
            //'ajaxOnly +autocomplete'
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CController::actions()
	 */
	public function actions()
	{
		return A::m(parent::actions(), [
		]);
	}
    
    public function actionAutocomplete()
    {
         if(isset($_GET['cityname'])) {
            $query=R::rget('cityname');
            $returnJSON=(R::rget('json') == 'on');
            $phrases=$this->getPhrases($query);

            $criteria = new \CDbCriteria();
            $this->addSearchInCondition($criteria, 'fullname', $phrases);
            $criteria->select='id, cdek_id, fullname, postcode';
            $criteria->order='cityname';
            $criteria->limit = 1000;
             
            $cities = City::model()->findAll($criteria);
            
            if(!$cities) {
            	$cities=City::model()->findAll(['condition'=>'center=1', 'order'=>'cityname']);
            }
            
            if($returnJSON) {
                $keyIsPostcode=(R::rget('postcode') == 'on');
                
                $result=[];
                foreach ($cities as $city) {
                    if($keyIsPostcode) $key=$city->postcode;
                    else $key=$city->cdek_id;
                    $result[$key]=$city->fullname;
                    
                    // получение геокоординат
                    //$city->updateGeoCode();
                }
                
                if(R::rget('html') == 'on') {
                    if(empty($result)) {
                        $html='Ни одного города не найдено';
                    }
                    else {
                        $html='';
                        foreach($result as $key=>$fullname) {
                            $html.=\CHtml::tag('option', ['value'=>$key], $fullname);
                        }
                        $html.='';
                    }
                    $result=[];
                    $result['html']=$html;
                }
                
                echo json_encode($result);
            }
            else {
                $result = '';
                foreach ($cities as $city) {
                    $result .= $city->fullname."\n";
                }
                echo trim($result);
            }
        }
        exit;
    }
    
    protected function getPhrases($q) 
    {
        $q=preg_replace('/ +/', ' ', $q);
        return array_filter(explode(' ', $q), function($v) { return (strlen($v) > 2); });
    }
    
    protected function addSearchInCondition(&$criteria, $attribute, $phrases, $operator='OR') {
        $c=new \CDbCriteria();
        if(!empty($phrases)) {
            foreach($phrases as $p) {
                $c->addSearchCondition($attribute, $p, true, 'AND');
            }
        }
        $criteria->mergeWith($c, $operator);
    }
}
