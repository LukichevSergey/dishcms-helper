<?php
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use accounts\components\helpers\HAccount;
use accounts\components\helpers\HAccountEmail;
use crud\models\ar\accounts\models\Account;

return [
    'templates'=>[
        // успешная регистрация
        'reg_successed'=>[
            'title'=>'Регистрация',
            'enable'=>'email_enable',
            'subject'=>'email_subject',
            'body'=>'email_body',
            'attributes'=>[
                'email_enable'=>'Отправлять письмо об успешной регистрации',
                'email_subject'=>'Заголовок письма об успешной регистрации',
                'email_body'=>'Шаблон письма об успешной регистрации',
            ],
            'types'=>[
                'email_enable'=>'checkbox',
                'email_subject',
                'email_body'=>[
                    'type'=>'tinyMce',
                    'enableClassicFull'=>true,
                    'showAccordion'=>false,
                    'uploadFiles'=>false,
                    'uploadImages'=>false,
                ],
            ],
            'defaults'=>function() {
                $t=Y::ct('\AccountsModule.models/accountEmailSettings', 'accounts');
                return [
                    'email_enable'=>1,
                    'email_subject'=>$t('default.reg_successed_email_subject'),
                    'email_body'=>$t('default.reg_successed_email_body'),
                ];
            },
            'shortcodes'=>[
                '#URL_AUTH#'=>[
                    'title'=>'ссылка на страницу авторизации',
                    'value'=>\CHtml::link(Y::createAbsoluteUrl('/signin'), Y::createAbsoluteUrl('/signin'), ['target'=>'_blank'])
                ],
                '#EMAIL#'=>[
                    'title'=>'E-Mail пользователя',
                    'value'=>function($params) {
                        if($account=A::get($params, 'account')) {
                            return $account->email;
                        }
                    }
                ],
                '#SITENAME#'=>[
                    'title'=>'имя сайта заданное в настройках',
                    'value'=>\D::cms('sitename')
                ]
            ]
        ],
        
        // успешная регистрация
        'reg_successed_wholesale'=>[
            'title'=>'Регистрация оптового покупателя',
            'enable'=>'email_enable',
            'subject'=>'email_subject',
            'body'=>'email_body',
            'attributes'=>[
                'email_enable'=>'Отправлять письмо об успешной регистрации',
                'email_subject'=>'Заголовок письма об успешной регистрации',
                'email_body'=>'Шаблон письма об успешной регистрации',
            ],
            'types'=>[
                'email_enable'=>'checkbox',
                'email_subject',
                'email_body'=>[
                    'type'=>'tinyMce',
                    'enableClassicFull'=>true,
                    'showAccordion'=>false,
                    'uploadFiles'=>false,
                    'uploadImages'=>false,
                ],
            ],
            'defaults'=>function() {
                $t=Y::ct('\AccountsModule.models/accountEmailSettings', 'accounts');
                return [
                    'email_enable'=>1,
                    'email_subject'=>$t('default.reg_successed_wholesale_email_subject'),
                    'email_body'=>$t('default.reg_successed_wholesale_email_body'),
                ];
            },
            'shortcodes'=>[
                '#URL_AUTH#'=>[
                    'title'=>'ссылка на страницу авторизации',
                    'value'=>\CHtml::link(Y::createAbsoluteUrl('/signin'), Y::createAbsoluteUrl('/signin'), ['target'=>'_blank'])
                ],
                '#EMAIL#'=>[
                    'title'=>'E-Mail пользователя',
                    'value'=>function($params) {
                        if($account=A::get($params, 'account')) {
                            return $account->email;
                        }
                    }
                ],
                '#SITENAME#'=>[
                    'title'=>'имя сайта заданное в настройках',
                    'value'=>\D::cms('sitename')
                ]
            ]
        ],
    
        // аккаунт успешно активирован
        'reg_activated'=>[
            'title'=>'Модерация',
            'enable'=>'email_enable',
            'subject'=>'email_subject',
            'body'=>'email_body',
            'attributes'=>[
                'email_enable'=>'Отправлять письмо об успешной активации доступа',
                'email_subject'=>'Заголовок письма об успешной активации доступа',
                'email_body'=>'Шаблон письма успешной об активации доступа',
            ],
            'types'=>[
                'email_enable'=>'checkbox',
                'email_subject',
                'email_body'=>[
                    'type'=>'tinyMce',
                    'enableClassicFull'=>true,
                    'showAccordion'=>false,
                    'uploadFiles'=>false,
                    'uploadImages'=>false,
                ],
            ],
            'defaults'=>function() {
                $t=Y::ct('\AccountsModule.models/accountEmailSettings', 'accounts');
                return [
                    'email_enable'=>1,
                    'email_subject'=>$t('default.reg_activated_email_subject'),
                    'email_body'=>$t('default.reg_activated_email_body'),
                ];
            },
            'shortcodes'=>[
                '#URL_AUTH#'=>[
                    'title'=>'ссылка на страницу авторизации',
                    'value'=>\CHtml::link(Y::createAbsoluteUrl('/signin'), Y::createAbsoluteUrl('/signin'), ['target'=>'_blank'])
                ],
                '#EMAIL#'=>[
                    'title'=>'E-Mail пользователя / Логин',
                    'value'=>function($params) {
                        if($account=A::get($params, 'account')) {
                            return $account->email;
                        }
                    }
                ],
                '#PASSWORD#'=>[
                    'title'=>'Пароль пользователя',
                    'value'=>function($params) {
                        if($account=A::get($params, 'account')) {
                            return $account->plain_password;
                        }
                    }
                ],
                '#SITENAME#'=>[
                    'title'=>'имя сайта заданное в настройках',
                    'value'=>\D::cms('sitename')
                ]
            ]
        ],
        
        // аккаунт успешно подтвержден
        'reg_confirm'=>[
            'active'=>function() {
                return false;
                // return HAccount::settings()->isRegConfirmMode();
            },
            'title'=>'Подтверждение',
            'subject'=>'email_subject',
            'body'=>'email_body',
            'attributes'=>[
                'email_subject'=>'Заголовок письма об успешном подтверждении доступа',
                'email_body'=>'Шаблон письма успешного подтверждения доступа',
            ],
            'types'=>[
                'email_subject',
                'email_body'=>[
                    'type'=>'tinyMce',
                    'enableClassicFull'=>true,
                    'showAccordion'=>false,
                    'uploadFiles'=>false,
                    'uploadImages'=>false,
                ],
            ],
            'defaults'=>function() {
                $t=Y::ct('\AccountsModule.models/accountEmailSettings', 'accounts');
                return [
                    'email_subject'=>$t('default.reg_confirm_email_subject'),
                    'email_body'=>$t('default.reg_confirm_email_body'),
                ];
            },
            'shortcodes'=>[
                '#URL_AUTH#'=>[
                    'title'=>'ссылка на страницу авторизации',
                    'value'=>\CHtml::link(Y::createAbsoluteUrl('/signin'), Y::createAbsoluteUrl('/signin'), ['target'=>'_blank'])
                ],
                '#EMAIL#'=>[
                    'title'=>'E-Mail пользователя',
                    'value'=>function($params) {
                        if($account=A::get($params, 'account')) {
                            return $account->email;
                        }
                    }
                ],
                '#SITENAME#'=>[
                    'title'=>'имя сайта заданное в настройках',
                    'value'=>\D::cms('sitename')
                ]
            ]
        ],
        
        // восстановление пароля
        'restore_password'=>[
            'title'=>'Восстановление пароля',
            'subject'=>'email_subject',
            'body'=>'email_body',
            'attributes'=>[
                'email_subject'=>'Заголовок письма восстановления пароля',
                'email_body'=>'Шаблон письма восстановления пароля',
            ],
            'types'=>[
                'email_subject',
                'email_body'=>[
                    'type'=>'tinyMce',
                    'enableClassicFull'=>true,
                    'showAccordion'=>false,
                    'uploadFiles'=>false,
                    'uploadImages'=>false,
                ],
            ],
            'defaults'=>function() {
                $t=Y::ct('\AccountsModule.models/accountEmailSettings', 'accounts');
                return [
                    'email_subject'=>$t('default.restore_password_email_subject'),
                    'email_body'=>$t('default.restore_password_email_body'),
                ];
            },
            'shortcodes'=>[
                '#URL_RESTORE_PASSWORD#'=>[
                    'title'=>'ссылка на страницу восстановления пароля',
                    'value'=>function($params) {
                        if($account=A::get($params, 'account')) {
                            $url=Y::createAbsoluteUrl('/accounts/auth/restoreChange', ['code'=>$account->confirm_code], Y::param('httpschema', 'http'));
                            return \CHtml::link($url, $url, ['target'=>'_blank']);
                        }
                    }
                ],
                '#SITENAME#'=>[
                    'title'=>'имя сайта заданное в настройках',
                    'value'=>\D::cms('sitename')
                ]
            ]
        ],
        
        // новая регистрация (для администратора)
        'admin_reg_successed'=>[
            'title'=>'(Админ) Новый пользователь',
            'enable'=>'email_enable',
            'subject'=>'email_subject',
            'body'=>'email_body',
            'attributes'=>[
                'email_enable'=>'Отправлять письмо о новой регистрации',
                'email_subject'=>'Заголовок письма о новой регистрации',
                'email_body'=>'Шаблон письма новой регистрации',
            ],
            'types'=>[
                'email_enable'=>'checkbox',
                'email_subject',
                'email_body'=>[
                    'type'=>'tinyMce',
                    'enableClassicFull'=>true,
                    'showAccordion'=>false,
                    'uploadFiles'=>false,
                    'uploadImages'=>false,
                ],
            ],
            'defaults'=>function() {
                $t=Y::ct('\AccountsModule.models/accountEmailSettings', 'accounts');
                return [
                    'email_enable'=>1,
                    'email_subject'=>$t('default.admin_reg_successed_email_subject'),
                    'email_body'=>$t('default.admin_reg_successed_email_body'),
                ];
            },
            'shortcodes'=>[
                '#ID#'=>[
                    'title'=>'Идентификатор пользователя в системе',
                    'value'=>function($params) {
                        if($account=A::get($params, 'account')) {
                            return $account->id;
                        }
                    }
                ],
                '#NAME#'=>[
                    'title'=>'Имя',
                    'value'=>function($params) {
                        if($account=A::get($params, 'account')) {
                            return $account->name;
                        }
                    }
                ],
                '#LASTNAME#'=>[
                    'title'=>'Фамилия',
                    'value'=>function($params) {
                        if($account=A::get($params, 'account')) {
                            return $account->lastname;
                        }
                    }
                ],
                '#EMAIL#'=>[
                    'title'=>'E-Mail пользователя',
                    'value'=>function($params) {
                        if($account=A::get($params, 'account')) {
                            return $account->email;
                        }
                    }
                ],
                '#PHONE#'=>[
                    'title'=>'Телефон пользователя',
                    'value'=>function($params) {
                        if($account=A::get($params, 'account')) {
                            return $account->formatPhone();
                        }
                    }
                ],
                '#AWAITING_MODERATOR_REVIEW#'=>[
                    'title'=>'Текст "Ожидает проверку модератором". Будет отображен только для оптового покупателя',
                    'value'=>function($params) {
                        if($account=A::get($params, 'account')) {
                            return HAccount::isWholesaleBuyer($account) ? '<p><i style="color:red">Ожидает проверку модератором!</i></p><br/>' : '';
                        }
                    }
                ],
                '#IS_WHOLESALE_BUYER#'=>[
                    'title'=>'Является оптовым покупателем',
                    'value'=>function($params) {
                        if($account=A::get($params, 'account')) {
                            return HAccount::isWholesaleBuyer($account) ? 'Да' : 'Нет';
                        }
                    }
                ],
                '#SITENAME#'=>[
                    'title'=>'имя сайта заданное в настройках',
                    'value'=>\D::cms('sitename')
                ]
            ]
        ],        
        
    ]
];