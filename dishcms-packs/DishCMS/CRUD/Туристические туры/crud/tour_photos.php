<?
/**
 * Фотографии тура
 */
use common\components\helpers\HRequest as R;

return [
    'class'=>'\crud\models\ar\TourPhoto',
    'access'=>[
        ['allow', 'users'=>['@'], 'roles'=>['admin', 'sadmin', 'crud_tours_manager']],
        ['deny', 'users'=>['*']],
    ],
    'config'=>[
        'tablename'=>'crud_tour_photos',
        'definitions'=>[
            'column.pk',
        ],
    ],
    'crud'=>[
    	'onBeforeLoad'=>function() { R::e404(); }
    ]
];
