<?php
/**
 * Виджет поля доставки СДЭК
 * 
 */
namespace cdek\widgets;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HDb;
use common\components\helpers\HModel;
use cdek\components\helpers\HCdek;
use cdek\models\Tariff;

class DeliveryField extends \common\components\widgets\form\BaseField
{
    public $tariffGroup=false;
    public $tariffModes=false;
    
    /**
     * @var integer|false режим доставки по умолчанию
     */
    public $tariffDefaultMode=false;
    
    public $view='delivery_field';
    
    public function init()
    {
        parent::init();
        
        $this->publish();        
    }
    
    public function run()
    {   
        $this->model=HModel::massiveAssignment('\cdek\models\Order', true);
        
        if(!$this->model->delivery_mode) {
            $this->model->delivery_mode=$this->getDeliveryDefaultMode();
        }
        
        if(isset($_POST['cdek_models_Order'])) {
            $this->model->setScenario($this->model->getScenarioByMode());
            $this->model->validate();
            $this->model->setScenario('insert');
        }
                
        if(!$this->model->send_city_id) {
            $this->model->send_city_id=HCdek::settings()->cdek_send_city_id;
        }
        
        $options=[
            'model_name'=>'cdek_models_Order',
            'mode_types'=>[]
        ];
        foreach($this->getDeliveryModes() as $mode=>$label) {
            $options['mode_types'][$mode]=$this->model->getScenarioByMode($mode);
        }        
        $options['default_mode_type']=$this->model->getScenarioByMode();
        
        Y::js(false, ';window.cdek_widgets_DeliveryField.init('.\CJavaScript::encode($options).');', \CClientScript::POS_READY);
        
        $this->render($this->view, $this->params);
    }
    
    /**
     * Получить варанты доставки
     */
    public function getDeliveryModes()
    {
        $codes=Tariff::i()->tariffCodes($this->tariffGroup, $this->tariffModes);
        
        $data=[];
        foreach($codes as $group=>$modes) {
            foreach($modes as $mode=>$tariffs) {               
                $data[$mode]=Tariff::i()->modePublicLabels($mode);
                if($this->model->getScenarioByMode($mode) == 'pvz') {
                    $data[$mode].='<span data-js="cdek-pvz-btn"></span>';
                }
            }
        }
        
        return $data;
    }
    
    public function getDeliveryDefaultMode()
    {
        if(isset($_COOKIE['cdek_delivery_mode'])) {
            return $_COOKIE['cdek_delivery_mode'];
        }
        
        if($this->tariffDefaultMode) {
            return $this->tariffDefaultMode;
        }
        
        foreach($this->getDeliveryModes() as $mode=>$label) {
            return $mode;
        }
    }
    
    public function getCityData()
    {
        $criteria=HDb::criteria(['order'=>'cityname', 'select'=>'*']);
        if($this->model->rec_city_id) {
            $criteria->addColumnCondition(['cdek_id'=>$this->model->rec_city_id]);
        }
        else {
            $criteria->addCondition('center=1');
        }
        
        return \cdek\models\City::model()->listData('fullname', $criteria, null, 'cdek_id');
        
        /*return \cdek\models\City::model()->listData('fullname', $criteria, null, ['cdek_id'=>function($model, $attribute) {
            $model->updateGeoCode();
        }]);*/
    }
}
