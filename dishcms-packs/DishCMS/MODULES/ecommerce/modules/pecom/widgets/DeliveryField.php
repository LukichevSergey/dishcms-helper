<?php
/**
 * Виджет поля доставки ПЭК
 * 
 */
namespace pecom\widgets;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HDb;
use common\components\helpers\HModel;
use pecom\components\PecomApi;

class DeliveryField extends \common\components\widgets\form\BaseField
{
    public $attribute='delivery_city';
    
    public $view='delivery_field';
    
    public function init()
    {
        parent::init();

        $this->publish();        
    }
    
    public function run()
    {
        $options=[
            'model_name'=>'DOrder_models_CustomerForm',
            'attribute'=>$this->attribute,
        ];

        Y::js(false, ';window.pecom_widgets_DeliveryField.init('.\CJavaScript::encode($options).');', \CClientScript::POS_READY);

        $this->render($this->view, $this->params);

    }
    
    public function getCityData()
    {
        $data=[''=>'Не выбрано'];
        return A::m($data, PecomApi::i()->towns());
    }
}
