<?php
return [
    'class'=>'\crud\models\ar\amocrm\models\Token',
    'config'=>[
        'tablename'=>'amocrm_token',
        'definitions'=>[
            'column.pk',
            'column.create_time',
            'client_id',
            'access_token'=>['type'=>'TEXT NOT NULL', 'label'=>'Токен авторизации'],
            'refresh_token'=>['type'=>'TEXT NOT NULL', 'label'=>'Токен обновления'],
            'expire'=>['type'=>'BIGINT NOT NULL', 'label'=>'Срок  действия'],
        ],
        'methods'=>[
            function() { return <<<'EOL'
private static $token=null;
public function afterSave(){parent::afterSave();static::$token=null;return true;}
public static function getToken($clientId) { 
    if(static::$token === null) {
        if(!(static::$token=static::model()->wcolumns(['client_id'=>$clientId])->find(["order"=>"create_time DESC"]))) {
            static::$token=false;
        }
    }
    return static::$token;
}
public static function getAccessToken($clientId) { if(static::getToken($clientId)) { return static::getToken($clientId)->access_token; } else { return null; } }
public static function getRefreshToken($clientId) { if(static::getToken($clientId)) { return static::getToken($clientId)->refresh_token; } else { return null; } }
public static function getExpire($clientId) { if(static::getToken($clientId)) { return (int)static::getToken($clientId)->expire; } else { return null; } }
public static function isExpire($clientId) { $expire=static::getExpire($clientId); return $expire ? (time() > $expire) : null; }
EOL;
            }
        ]
    ],
    'buttons'=>[
        'create'=>['label'=>''],
    ],
    'crud'=>[
        'onBeforeLoad'=>function() {throw new \CHttpException(404);}
    ]
];
