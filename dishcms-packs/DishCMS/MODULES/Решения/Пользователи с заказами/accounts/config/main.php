<?php
/**
 * Конфигурация модуля "Пользователи"
 * 
 **/
return [
    'controllerMap'=>[
        'account'=>'\accounts\controllers\AccountController',
        'auth'=>'\accounts\controllers\AuthController',
        'reg'=>'\accounts\controllers\RegController',
        'advert'=>'\accounts\controllers\AdvertController',
    ],
    'modules'=>[
        'admin'=>['class'=>'\accounts\modules\admin\AdminModule']
    ]
];