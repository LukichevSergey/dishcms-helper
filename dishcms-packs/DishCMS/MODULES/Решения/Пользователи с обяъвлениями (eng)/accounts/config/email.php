<?php
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use accounts\components\helpers\HAccount;
use accounts\components\helpers\HAccountEmail;
use crud\models\ar\accounts\models\Advert;

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
        
        // advert_published
        'advert_published'=>[
            'title'=>'Объявление опубликовано',
            'enable'=>'email_enable',
            'subject'=>'email_subject',
            'body'=>'email_body',
            'attributes'=>[
                'email_enable'=>'Отправлять письмо уведомления при публикации объявления',
                'email_subject'=>'Заголовок письма уведомления при публикации объявления',
                'email_body'=>'Шаблон письма уведомления при публикации объявления',
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
                    'email_subject'=>$t('default.advert_published_email_subject'),
                    'email_body'=>$t('default.advert_published_email_body'),
                ];
            },
            'shortcodes'=>[
                '#ID#'=>[
                    'title'=>'Идентификатор объявления в системе',
                    'value'=>function($params) {
                        if($advert=A::get($params, 'advert')) {
                            return $advert->id;
                        }
                    }
                ],                
                '#ACCOUNT_NAME#'=>[
                    'title'=>'Имя пользователя',
                    'value'=>function($params) {
                        if($account=A::get($params, 'account')) {
                            return $account->name;
                        }
                    }
                ],
                '#ADVERT_TYPE#'=>[
                    'title'=>'Тип объявления',
                    'value'=>function($params) {
                        if($advert=A::get($params, 'advert')) {
                            return ($advert->type == Advert::TYPE_SALE) ? 'Продажа' : 'Покупка';
                        }
                    }
                ],
                '#MY_ADVERTS_URL#'=>[
                    'title'=>'Ссылка на список объявлений в личном кабинете',
                    'value'=>function($params) {
                        return Y::createAbsoluteUrl('/accounts/account/adverts');
                    }
                ],
                '#SITENAME#'=>[
                    'title'=>'имя сайта заданное в настройках',
                    'value'=>\D::cms('sitename')
                ]
            ]
        ],
        
        // advert_unpublished
        'advert_unpublished'=>[
            'title'=>'Объявление снято с публикации',
            'enable'=>'email_enable',
            'subject'=>'email_subject',
            'body'=>'email_body',
            'attributes'=>[
                'email_enable'=>'Отправлять письмо уведомления при снятии объявления с публикации',
                'email_subject'=>'Заголовок письма уведомления при снятии объявления с публикации',
                'email_body'=>'Шаблон письма уведомления при снятии объявления с публикации',
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
                    'email_subject'=>$t('default.advert_unpublished_email_subject'),
                    'email_body'=>$t('default.advert_unpublished_email_body'),
                ];
            },
            'shortcodes'=>[
                '#ID#'=>[
                    'title'=>'Идентификатор объявления в системе',
                    'value'=>function($params) {
                        if($advert=A::get($params, 'advert')) {
                            return $advert->id;
                        }
                    }
                ],                
                '#ACCOUNT_NAME#'=>[
                    'title'=>'Имя пользователя',
                    'value'=>function($params) {
                        if($account=A::get($params, 'account')) {
                            return $account->name;
                        }
                    }
                ],
                '#ADVERT_TYPE#'=>[
                    'title'=>'Тип объявления',
                    'value'=>function($params) {
                        if($advert=A::get($params, 'advert')) {
                            return ($advert->type == Advert::TYPE_SALE) ? 'Продажа' : 'Покупка';
                        }
                    }
                ],
                '#MY_ADVERTS_URL#'=>[
                    'title'=>'Ссылка на список объявлений в личном кабинете',
                    'value'=>function($params) {
                        return Y::createAbsoluteUrl('/accounts/account/adverts');
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
                '#COMPANY#'=>[
                    'title'=>'Имя компании',
                    'value'=>function($params) {
                        if($account=A::get($params, 'account')) {
                            return $account->company;
                        }
                    }
                ],
                '#CATEGORY#'=>[
                    'title'=>'Категория',
                    'value'=>function($params) {
                        if($account=A::get($params, 'account')) {
                            return $account->getCategoryLabel();
                        }
                    }
                ],
                '#COUNTRY#'=>[
                    'title'=>'Страна',
                    'value'=>function($params) {
                        if($account=A::get($params, 'account')) {
                            return $account->country->title;
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
                '#SITENAME#'=>[
                    'title'=>'имя сайта заданное в настройках',
                    'value'=>\D::cms('sitename')
                ]
            ]
        ],
        
        // admin_new_advert
        'admin_new_advert'=>[
            'title'=>'(Админ) Новое объявление',
            'enable'=>'email_enable',
            'subject'=>'email_subject',
            'body'=>'email_body',
            'attributes'=>[
                'email_enable'=>'Отправлять письмо уведомления при добавлении нового объявления',
                'email_subject'=>'Заголовок письма уведомления при добавлении нового объявления',
                'email_body'=>'Шаблон письма уведомления при добавлении нового объявления',
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
                    'email_subject'=>$t('default.admin_new_advert_email_subject'),
                    'email_body'=>$t('default.admin_new_advert_email_body'),
                ];
            },
            'shortcodes'=>[
                '#ID#'=>[
                    'title'=>'Идентификатор объявления в системе',
                    'value'=>function($params) {
                        if($advert=A::get($params, 'advert')) {
                            return $advert->id;
                        }
                    }
                ],
                '#ACCOUNT_ID#'=>[
                    'title'=>'Идентификатор пользователя в системе',
                    'value'=>function($params) {
                        if($account=A::get($params, 'account')) {
                            return $account->id;
                        }
                    }
                ],
                '#ACCOUNT_COMPANY#'=>[
                    'title'=>'Имя компании пользователя',
                    'value'=>function($params) {
                        if($account=A::get($params, 'account')) {
                            return $account->company;
                        }
                    }
                ],
                '#ACCOUNT_CATEGORY#'=>[
                    'title'=>'Категория пользователя',
                    'value'=>function($params) {
                        if($account=A::get($params, 'account')) {
                            return $account->getCategoryLabel();
                        }
                    }
                ],
                '#ACCOUNT_COUNTRY#'=>[
                    'title'=>'Страна пользователя',
                    'value'=>function($params) {
                        if($account=A::get($params, 'account')) {
                            return $account->country->title;
                        }
                    }
                ],
                '#ACCOUNT_NAME#'=>[
                    'title'=>'Имя пользователя',
                    'value'=>function($params) {
                        if($account=A::get($params, 'account')) {
                            return $account->name;
                        }
                    }
                ],
                '#ACCOUNT_EMAIL#'=>[
                    'title'=>'E-Mail пользователя',
                    'value'=>function($params) {
                        if($account=A::get($params, 'account')) {
                            return $account->email;
                        }
                    }
                ],
                '#ACCOUNT_PHONE#'=>[
                    'title'=>'Телефон пользователя',
                    'value'=>function($params) {
                        if($account=A::get($params, 'account')) {
                            return $account->formatPhone();
                        }
                    }
                ],
                '#ADVERT_TYPE#'=>[
                    'title'=>'Тип объявления',
                    'value'=>function($params) {
                        if($advert=A::get($params, 'advert')) {
                            return ($advert->type == Advert::TYPE_SALE) ? 'Продажа' : 'Покупка';
                        }
                    }
                ],
                '#PART_NUMBER#'=>[
                    'title'=>'Part Number',
                    'value'=>function($params) {
                        if($advert=A::get($params, 'advert')) {
                            return $advert->part_number;
                        }
                    }
                ],
                '#TYPE_OF_PART#'=>[
                    'title'=>'Type of part',
                    'value'=>function($params) {
                        if($advert=A::get($params, 'advert')) {
                            return $advert->part_type;
                        }
                    }
                ],
                '#QUANTITY#'=>[
                    'title'=>'Quantity',
                    'value'=>function($params) {
                        if($advert=A::get($params, 'advert')) {
                            return $advert->quantity;
                        }
                    }
                ],
                '#CODE#'=>[
                    'title'=>'Condition / Capability Code',
                    'value'=>function($params) {
                        if($advert=A::get($params, 'advert')) {
                            return $advert->code;
                        }
                    }
                ],
                '#OBJECT_TYPE#'=>[
                    'title'=>'Object Type',
                    'value'=>function($params) {
                        if($advert=A::get($params, 'advert')) {
                            return $advert->getDetailTypeLabel();
                        }
                    }
                ],
                '#OBJECT_TYPE_VALUE#'=>[
                    'title'=>'Object Type Value',
                    'value'=>function($params) {
                        if($advert=A::get($params, 'advert')) {
                            return $advert->detail_type_value;
                        }
                    }
                ],
                '#CATEGORY#'=>[
                    'title'=>'Category',
                    'value'=>function($params) {
                        if($advert=A::get($params, 'advert')) {
                            return $advert->category;
                        }
                    }
                ],
                '#DOCUMENT#'=>[
                    'title'=>'Document',
                    'value'=>function($params) {
                        if($advert=A::get($params, 'advert')) {
                            if($advert->fileBehavior->exists()) {
                                return $advert->fileBehavior->downloadLink(['target'=>'_blank'], true);
                            }
                        }
                    }
                ],
                '#SITENAME#'=>[
                    'title'=>'имя сайта заданное в настройках',
                    'value'=>\D::cms('sitename')
                ]
            ]
        ],
        
        // admin_advert_edited
        'admin_advert_edited'=>[
            'title'=>'(Админ) Изменено объявление',
            'enable'=>'email_enable',
            'subject'=>'email_subject',
            'body'=>'email_body',
            'attributes'=>[
                'email_enable'=>'Отправлять письмо уведомления об изменении объявления',
                'email_subject'=>'Заголовок письма уведомления об изменении объявления',
                'email_body'=>'Шаблон письма уведомления при изменении объявления',
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
                    'email_subject'=>$t('default.admin_advert_edited_email_subject'),
                    'email_body'=>$t('default.admin_advert_edited_email_body'),
                ];
            },
            'shortcodes'=>[
                '#ID#'=>[
                    'title'=>'Идентификатор объявления в системе',
                    'value'=>function($params) {
                        if($advert=A::get($params, 'advert')) {
                            return $advert->id;
                        }
                    }
                ],
                '#ACCOUNT_ID#'=>[
                    'title'=>'Идентификатор пользователя в системе',
                    'value'=>function($params) {
                        if($account=A::get($params, 'account')) {
                            return $account->id;
                        }
                    }
                ],
                '#ACCOUNT_COMPANY#'=>[
                    'title'=>'Имя компании пользователя',
                    'value'=>function($params) {
                        if($account=A::get($params, 'account')) {
                            return $account->company;
                        }
                    }
                ],
                '#ACCOUNT_CATEGORY#'=>[
                    'title'=>'Категория пользователя',
                    'value'=>function($params) {
                        if($account=A::get($params, 'account')) {
                            return $account->getCategoryLabel();
                        }
                    }
                ],
                '#ACCOUNT_COUNTRY#'=>[
                    'title'=>'Страна пользователя',
                    'value'=>function($params) {
                        if($account=A::get($params, 'account')) {
                            return $account->country->title;
                        }
                    }
                ],
                '#ACCOUNT_NAME#'=>[
                    'title'=>'Имя пользователя',
                    'value'=>function($params) {
                        if($account=A::get($params, 'account')) {
                            return $account->name;
                        }
                    }
                ],
                '#ACCOUNT_EMAIL#'=>[
                    'title'=>'E-Mail пользователя',
                    'value'=>function($params) {
                        if($account=A::get($params, 'account')) {
                            return $account->email;
                        }
                    }
                ],
                '#ACCOUNT_PHONE#'=>[
                    'title'=>'Телефон пользователя',
                    'value'=>function($params) {
                        if($account=A::get($params, 'account')) {
                            return $account->formatPhone();
                        }
                    }
                ],
                '#ADVERT_TYPE#'=>[
                    'title'=>'Тип объявления',
                    'value'=>function($params) {
                        if($advert=A::get($params, 'advert')) {
                            return ($advert->type == Advert::TYPE_SALE) ? 'Продажа' : 'Покупка';
                        }
                    }
                ],
                '#PART_NUMBER#'=>[
                    'title'=>'Part Number',
                    'value'=>function($params) {
                        if($advert=A::get($params, 'advert')) {
                            return $advert->part_number;
                        }
                    }
                ],
                '#TYPE_OF_PART#'=>[
                    'title'=>'Type of part',
                    'value'=>function($params) {
                        if($advert=A::get($params, 'advert')) {
                            return $advert->part_type;
                        }
                    }
                ],
                '#QUANTITY#'=>[
                    'title'=>'Quantity',
                    'value'=>function($params) {
                        if($advert=A::get($params, 'advert')) {
                            return $advert->quantity;
                        }
                    }
                ],
                '#CODE#'=>[
                    'title'=>'Condition / Capability Code',
                    'value'=>function($params) {
                        if($advert=A::get($params, 'advert')) {
                            return $advert->code;
                        }
                    }
                ],
                '#OBJECT_TYPE#'=>[
                    'title'=>'Object Type',
                    'value'=>function($params) {
                        if($advert=A::get($params, 'advert')) {
                            return $advert->getDetailTypeLabel();
                        }
                    }
                ],
                '#OBJECT_TYPE_VALUE#'=>[
                    'title'=>'Object Type Value',
                    'value'=>function($params) {
                        if($advert=A::get($params, 'advert')) {
                            return $advert->detail_type_value;
                        }
                    }
                ],
                '#CATEGORY#'=>[
                    'title'=>'Category',
                    'value'=>function($params) {
                        if($advert=A::get($params, 'advert')) {
                            return $advert->category;
                        }
                    }
                ],
                '#DOCUMENT#'=>[
                    'title'=>'Document',
                    'value'=>function($params) {
                        if($advert=A::get($params, 'advert')) {
                            if($advert->fileBehavior->exists()) {
                                return $advert->fileBehavior->downloadLink(['target'=>'_blank'], true);
                            }
                        }
                    }
                ],
                '#SITENAME#'=>[
                    'title'=>'имя сайта заданное в настройках',
                    'value'=>\D::cms('sitename')
                ]
            ]
        ],
        
        // admin_advert_response
        'admin_advert_response'=>[
            'title'=>'(Админ) Отклик на объявление',
            'enable'=>'email_enable',
            'subject'=>'email_subject',
            'body'=>'email_body',
            'attributes'=>[
                'email_enable'=>'Отправлять письмо уведомления при отклике на объявление',
                'notify_responded'=>'Сообщение об успешном отклике на объявление',
                'notify_not_responded'=>'Сообщение при возникновении ошибки при отклике на объявление',                
                'email_subject'=>'Заголовок письма уведомления отклика на объявление',
                'email_body'=>'Шаблон письма уведомления отклика на объявление',
            ],
            'types'=>[
                'email_enable'=>'checkbox',
                'notify_responded'=>'tinyMceLite',
                'notify_not_responded'=>'tinyMceLite',                
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
                    'notify_responded'=>$t('default.admin_advert_response_notify_responded'),
                    'notify_not_responded'=>$t('default.admin_advert_response_notify_not_responded'),
                    'email_subject'=>$t('default.admin_advert_response_email_subject'),
                    'email_body'=>$t('default.admin_advert_response_email_body'),
                ];
            },
            'shortcodes'=>[
                '#ID#'=>[
                    'title'=>'Идентификатор объявления в системе',
                    'value'=>function($params) {
                        if($advert=A::get($params, 'advert')) {
                            return $advert->id;
                        }
                    }
                ],
                '#ACCOUNT_ID#'=>[
                    'title'=>'Идентификатор пользователя в системе',
                    'value'=>function($params) {
                        if($account=A::get($params, 'account')) {
                            return $account->id;
                        }
                    }
                ],
                '#ACCOUNT_COMPANY#'=>[
                    'title'=>'Имя компании пользователя',
                    'value'=>function($params) {
                        if($account=A::get($params, 'account')) {
                            return $account->company;
                        }
                    }
                ],
                '#ACCOUNT_CATEGORY#'=>[
                    'title'=>'Категория пользователя',
                    'value'=>function($params) {
                        if($account=A::get($params, 'account')) {
                            return $account->getCategoryLabel();
                        }
                    }
                ],
                '#ACCOUNT_COUNTRY#'=>[
                    'title'=>'Страна пользователя',
                    'value'=>function($params) {
                        if($account=A::get($params, 'account')) {
                            return $account->country->title;
                        }
                    }
                ],
                '#ACCOUNT_NAME#'=>[
                    'title'=>'Имя пользователя',
                    'value'=>function($params) {
                        if($account=A::get($params, 'account')) {
                            return $account->name;
                        }
                    }
                ],
                '#ACCOUNT_EMAIL#'=>[
                    'title'=>'E-Mail пользователя',
                    'value'=>function($params) {
                        if($account=A::get($params, 'account')) {
                            return $account->email;
                        }
                    }
                ],
                '#ACCOUNT_PHONE#'=>[
                    'title'=>'Телефон пользователя',
                    'value'=>function($params) {
                        if($account=A::get($params, 'account')) {
                            return $account->formatPhone();
                        }
                    }
                ],
                '#ADVERT_ACCOUNT_ID#'=>[
                    'title'=>'Идентификатор владельца объявления в системе',
                    'value'=>function($params) {
                        if($advert=A::get($params, 'advert')) {
                            if($account=$advert->account) {
                                return $account->id;
                            }
                        }
                    }
                ],
                '#ADVERT_ACCOUNT_COMPANY#'=>[
                    'title'=>'Имя компании владельца объявления',
                    'value'=>function($params) {
                        if($advert=A::get($params, 'advert')) {
                            if($account=$advert->account) {
                                return $account->company;
                            }
                        }
                    }
                ],
                '#ADVERT_ACCOUNT_CATEGORY#'=>[
                    'title'=>'Категория владельца объявления',
                    'value'=>function($params) {
                        if($advert=A::get($params, 'advert')) {
                            if($account=$advert->account) {
                                return $account->getCategoryLabel();
                            }
                        }
                    }
                ],
                '#ADVERT_ACCOUNT_COUNTRY#'=>[
                    'title'=>'Страна владельца объявления',
                    'value'=>function($params) {
                        if($advert=A::get($params, 'advert')) {
                            if($account=A::get($params, 'account')) {
                                return $account->country->title;
                            }
                        }
                    }
                ],
                '#ADVERT_ACCOUNT_NAME#'=>[
                    'title'=>'Имя владельца объявления',
                    'value'=>function($params) {
                        if($advert=A::get($params, 'advert')) {
                            if($account=$advert->account) {
                                return $account->name;
                            }
                        }
                    }
                ],
                '#ADVERT_ACCOUNT_EMAIL#'=>[
                    'title'=>'E-Mail владельца объявления',
                    'value'=>function($params) {
                        if($advert=A::get($params, 'advert')) {
                            if($account=$advert->account) {
                                return $account->email;
                            }
                        }
                    }
                ],
                '#ADVERT_ACCOUNT_PHONE#'=>[
                    'title'=>'Телефон владельца объявления',
                    'value'=>function($params) {
                        if($advert=A::get($params, 'advert')) {
                            if($account=$advert->account) {
                                return $account->formatPhone();
                            }
                        }
                    }
                ],
                '#ADVERT_TYPE#'=>[
                    'title'=>'Тип объявления',
                    'value'=>function($params) {
                        if($advert=A::get($params, 'advert')) {
                            return ($advert->type == Advert::TYPE_SALE) ? 'Продажа' : 'Покупка';
                        }
                    }
                ],
                '#PART_NUMBER#'=>[
                    'title'=>'Part Number',
                    'value'=>function($params) {
                        if($advert=A::get($params, 'advert')) {
                            return $advert->part_number;
                        }
                    }
                ],
                '#TYPE_OF_PART#'=>[
                    'title'=>'Type of part',
                    'value'=>function($params) {
                        if($advert=A::get($params, 'advert')) {
                            return $advert->part_type;
                        }
                    }
                ],
                '#QUANTITY#'=>[
                    'title'=>'Quantity',
                    'value'=>function($params) {
                        if($advert=A::get($params, 'advert')) {
                            return $advert->quantity;
                        }
                    }
                ],
                '#CODE#'=>[
                    'title'=>'Condition / Capability Code',
                    'value'=>function($params) {
                        if($advert=A::get($params, 'advert')) {
                            return $advert->code;
                        }
                    }
                ],
                '#OBJECT_TYPE#'=>[
                    'title'=>'Object Type',
                    'value'=>function($params) {
                        if($advert=A::get($params, 'advert')) {
                            return $advert->getDetailTypeLabel();
                        }
                    }
                ],
                '#OBJECT_TYPE_VALUE#'=>[
                    'title'=>'Object Type Value',
                    'value'=>function($params) {
                        if($advert=A::get($params, 'advert')) {
                            return $advert->detail_type_value;
                        }
                    }
                ],
                '#CATEGORY#'=>[
                    'title'=>'Category',
                    'value'=>function($params) {
                        if($advert=A::get($params, 'advert')) {
                            return $advert->category;
                        }
                    }
                ],
                '#DOCUMENT#'=>[
                    'title'=>'Document',
                    'value'=>function($params) {
                        if($advert=A::get($params, 'advert')) {
                            if($advert->fileBehavior->exists()) {
                                return $advert->fileBehavior->downloadLink(['target'=>'_blank'], true);
                            }
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