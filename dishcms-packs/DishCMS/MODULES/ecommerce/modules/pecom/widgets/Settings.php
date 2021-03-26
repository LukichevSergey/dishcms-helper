<?php
/**
 * Виджет полей настроек ПЭК
 */
namespace pecom\widgets;

use common\components\helpers\HYii as Y;
use pecom\components\PecomApi;

class Settings extends \common\components\widgets\form\BaseField
{
    public $view='settings';
    
    public function init()
    {
        parent::init();
        
        if(!$this->model->pecom_take_town) {
            $this->model->pecom_take_town=Y::param('pecom.take.town');
        }
    }
    
    public function getCityData()
    {
        return PecomApi::i()->towns();
    }
}
