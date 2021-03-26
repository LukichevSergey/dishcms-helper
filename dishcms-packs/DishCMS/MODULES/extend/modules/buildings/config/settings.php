<?php
return [
    'buildings'=>[
        'class'=>'\extend\modules\buildings\models\Settings',
        'title'=>'Настройки планировок',
        'menuItemLabel'=>'Настройки планировок',
        'breadcrumbs'=>['Планировки'=>['/cp/buildings/index']],
        'viewForm'=>'extend.modules.buildings.modules.admin.views.settings._buildings_form'
    ],
];