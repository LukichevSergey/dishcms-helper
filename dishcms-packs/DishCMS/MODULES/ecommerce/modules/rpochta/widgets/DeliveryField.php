<?php
/**
 * Виджет поля доставки Почта.России
 * 
 * @FIXME список городов использует функционал модуля СДЭК.
 */
namespace rpochta\widgets;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HDb;
use common\components\helpers\HModel;
use rpochta\components\helpers\HRPochta;
use rpochta\components\RPochtaConst;

class DeliveryField extends \common\components\widgets\form\BaseField
{
//    public $tariffGroup=false;
//    public $tariffModes=false;
    
    /**
     * @var boolean режим доставки до пункта ОПС
     */
    public $ops=true;
    
    public $view='delivery_field';
    
    public function init()
    {
        parent::init();
        
        $this->publish();        
    }
    
    public function run()
    {   
        $this->model=HModel::massiveAssignment('\rpochta\models\Order', true);
        
        if(!$this->ops) {
            $this->model->delivery_mode=RPochtaConst::MODE_ADDRESS;
        }
        
        if(!$this->model->delivery_mode) {
            $this->model->delivery_mode=$this->getDeliveryDefaultMode();
        }
        
        if(isset($_POST['cdek_models_Order'])) {
            $this->model->setScenario($this->model->getScenarioByMode());
            $this->model->validate();
            $this->model->setScenario('insert');
        }
        
        if(!$this->model->index_from) {
            $this->model->index_from=HRPochta::settings()->rpochta_index_from;
        }
        
        $options=[
            'model_name'=>'rpochta_models_Order',
            'mode_types'=>[]
        ];
        foreach($this->getDeliveryModes() as $mode=>$label) {
            $options['mode_types'][$mode]=$this->model->getScenarioByMode($mode);
        }        
        $options['default_mode_type']=$this->model->getScenarioByMode();
        
        Y::js(false, ';window.rpochta_widgets_DeliveryField.init('.\CJavaScript::encode($options).');', \CClientScript::POS_READY);
        
        $this->render($this->view, $this->params);
    }
    
    /**
     * Получить варанты доставки
     */
    public function getDeliveryModes()
    {
        $data=RPochtaConst::i()->modeLabels();
        $data[RPochtaConst::MODE_OPS].='<span data-js="rpochta-ops-btn"></span>';
        
        return $data;
    }
    
    public function getDeliveryDefaultMode()
    {
        if(isset($_COOKIE['rpochta_delivery_mode'])) {
            return $_COOKIE['rpochta_delivery_mode'];
        }
        
        foreach($this->getDeliveryModes() as $mode=>$label) {
            return $mode;
        }
    }
    
    public function getCityData()
    {
        $criteria=HDb::criteria(['order'=>'cityname']);
        if($this->model->index_to) {
            $criteria->addColumnCondition(['postcode'=>$this->model->index_to]);
        }
        else {
            $criteria->addCondition('center=1');
        }
        
        return \cdek\models\City::model()->listData('fullname', $criteria, null, 'postcode');
        /*return \cdek\models\City::model()->listData('fullname', $criteria, null, ['postcode'=>function($model, $attribute) {
            $model->updateGeoCode();
        }]);*/
    }
}
