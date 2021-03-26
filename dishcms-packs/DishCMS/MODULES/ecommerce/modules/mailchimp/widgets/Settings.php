<?php
/**
 * Виджет полей настроек MailChimp
 */
namespace mailchimp\widgets;

use common\components\helpers\HYii as Y;

class Settings extends \common\components\widgets\form\BaseField
{
    public $view='settings';
    
    public function init()
    {
        parent::init();
        
        if(!$this->model->mailchimp_key) {
            $this->model->mailchimp_key=Y::param('mailchimp.key');
        }
    }
}
