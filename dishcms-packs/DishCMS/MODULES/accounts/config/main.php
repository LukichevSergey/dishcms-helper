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
    ],
    'modules'=>[
        'admin'=>'\accounts\modules\admin\AdminModule'
    ]
];