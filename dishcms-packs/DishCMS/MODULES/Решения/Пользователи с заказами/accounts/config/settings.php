<?php
return [
    'accounts'=>[
        'class'=>'\accounts\models\AccountSettings',
        'breadcrumbs'=>['Пользователи'=>'/cp/crud/index?cid=accounts'],
        'title'=>'Настройки',
        'viewForm'=>'accounts.modules.admin.views.settings._account_settings'
    ],
    'accounts_email'=>[
        'class'=>'\accounts\models\AccountEmailSettings',
        'breadcrumbs'=>['Пользователи'=>'/cp/crud/index?cid=accounts'],
        'title'=>'Почтовые шаблоны',
        'viewForm'=>'accounts.modules.admin.views.settingsEmail._form'
    ]
];