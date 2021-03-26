<?
/**
 * Дополнительные фотографии номеров отеля
 */
use common\components\helpers\HRequest as R;

return [
    'class'=>'\crud\models\ar\HotelNumberPhoto',
    'access'=>[
        ['allow', 'users'=>['@'], 'roles'=>['admin', 'sadmin']],
        ['deny', 'users'=>['*']],
    ],
    'config'=>[
        'tablename'=>'crud_hotel_numbers_photos',
        'definitions'=>[
            'column.pk',
        ],
    ],
    'crud'=>[
    	'onBeforeLoad'=>function() { R::e404(); }
    ]
];
