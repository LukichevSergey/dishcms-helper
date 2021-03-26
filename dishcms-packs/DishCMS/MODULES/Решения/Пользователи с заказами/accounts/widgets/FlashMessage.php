<?php
namespace accounts\widgets;

use accounts\components\helpers\HAccount;

/**
 * Отображает flash сообщение для модуля аккаунта
 *
 */
class FlashMessage extends \common\components\base\Widget
{
    public $successFlashId=HAccount::FLASH_SUCCESS;
    
    public $successOptions=['class'=>'accounts__flash-success'];
    
    public $failFlashId=HAccount::FLASH_FAIL;
    
    public $failOptions=['class'=>'accounts__flash-fail'];
    
    public $view='flash_message';
}