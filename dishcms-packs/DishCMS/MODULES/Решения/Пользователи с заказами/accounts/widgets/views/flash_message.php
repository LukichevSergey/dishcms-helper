<?php
/** @var \accounts\widgets\FlashMessage $this */
use common\components\helpers\HYii as Y;

Y::css('flash_message-success', '.accounts__flash-success{width:100%;border:1px solid #c9e0c9;border-radius:3px;padding:5px 10px;text-align:center;color:#3c693c;background:#dff8df;}');
Y::css('flash_message-fail', '.accounts__flash-fail{width:100%;border:1px solid #e68b84;border-radius:3px;padding:5px 10px;text-align:center;color:#ca3e34;background:#f8dfdf;}');

if(Y::hasFlash($this->failFlashId)) {
    echo \CHtml::tag('div', $this->failOptions, Y::getFlash($this->failFlashId));
}

if(Y::hasFlash($this->successFlashId)) {
    echo \CHtml::tag('div', $this->successOptions, Y::getFlash($this->successFlashId));
}

