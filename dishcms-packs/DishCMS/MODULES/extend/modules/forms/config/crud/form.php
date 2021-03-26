<?php
use common\components\helpers\HRequest as R;
use extend\modules\forms\components\helpers\HForm;

// @hook подключение динамической конфигурации формы
if(($cid=R::get('cid')) && (strpos($cid, 'form__')===0)) {
    return [
        'events'=>[
            'onCrudAfterConfigPrepared'=>function($event) use ($cid) {
                if(!defined('FORMS_CRUD_FORM_LOADED')) {
                    define('FORMS_CRUD_FORM_LOADED', true);
                    HForm::getFormByCrudConfigId($cid);
                }
            }
        ]
    ];
}

return null;