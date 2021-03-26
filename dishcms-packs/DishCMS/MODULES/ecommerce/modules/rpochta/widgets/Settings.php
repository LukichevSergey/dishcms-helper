<?php
/**
 * Виджет полей настроек Почта.России
 */
namespace rpochta\widgets;

use common\components\helpers\HYii as Y;

class Settings extends \common\components\widgets\form\BaseField
{
    public $view='settings';
    
    public function init()
    {
        parent::init();
        
        if(!$this->model->rpochta_index_from) {
            $this->model->rpochta_index_from=Y::param('rpochta.index_from');
        }
        
        if(!$this->model->rpochta_index_from_name) {
            $this->model->rpochta_index_from_name=Y::param('rpochta.index_from_name');
        }
    }
}
