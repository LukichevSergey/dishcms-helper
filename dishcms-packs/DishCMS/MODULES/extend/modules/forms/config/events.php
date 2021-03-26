<?php
use common\components\helpers\HArray as A;
use extend\modules\forms\components\helpers\HForm;

return [
    /**
     * Событие модификации меню уведомлений в разделе администрирования
     */
    'onAdminMenuItemsNotifications'=>function(&$event) {
        if($configs=HForm::configs(true, ['scopes'=>'isSaveResults', 'order'=>'title'])) {
            $new=A::get($event->params, 'new', false);
            foreach($configs as $config) {
                $form=HForm::form($config->code);
                $count= $form->unpublished()->count();
                $new=$new || ($count > 0);
                $event->params['items'][]=[                    
                    'label'=>\CHtml::tag('i', ['class'=>'glyphicon glyphicon-earphone'], '', true) 
                        . ' ' . $config->title
                        . \CHtml::tag('span', ['class'=>'notification_new_count '.HForm::getCrudConfigId($config->code).'-count-in-title'], $count),
                    'encodeLabel'=>false,
                    'url'=>HForm::getFormCrudIndexUrl($config->code)
                ];
            }
            $event->params['new']=$new;
        }
    },
    
    /**
     * Событие модификации CRUD конфигурации модели формы
     * В свойстве $event->params передаются параметры
     * "cid"=>$crudConfigId, - идентификатор конфигурации
     * "crud"=>&$crud - массив конфигурации
     */
    'onExtendFormsFormFactoryAfterCrud'=>function(&$event) {
        
    }
];