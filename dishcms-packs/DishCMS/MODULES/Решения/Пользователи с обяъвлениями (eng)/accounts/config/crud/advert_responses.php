<?php
use common\components\helpers\HYii as Y;
use common\components\helpers\HRequest as R;
use crud\models\ar\accounts\models\Advert;

$advert=null;
if(R::rget('accounts_adverts') && class_exists('\crud\models\ar\accounts\models\Advert')) {
    $advert=Advert::model()->findByPk((int)R::rget('accounts_adverts'));
}

return [
    'class'=>'\crud\models\ar\accounts\models\AdvertResponse',
    /*
    'relations'=>[
        'accounts_adverts'=>[
            'type'=>'belongs_to',
            'attribute'=>'advert_id',
            'titleAttribute'=>'id',
        ],
        'accounts'=>[
            'type'=>'belongs_to',
            'attribute'=>'account_id',
            'titleAttribute'=>'name',
        ]
    ],
    /**/
    'config'=>[
        'tablename'=>'accounts_advert_responses',
        'definitions'=>[
            'column.pk',
            'column.create_time',
            'foreign.account_id'=>['label'=>'Отправитель'],
            'foreign.advert_id'=>['label'=>'Объявление'],
            'column.published'=>['label'=>'Обработан'],
        ],
        'relations'=>[
            'advert'=>[\CActiveRecord::BELONGS_TO, '\crud\models\ar\accounts\models\Advert', 'advert_id'],
            'account'=>[\CActiveRecord::BELONGS_TO, '\crud\models\ar\accounts\models\Account', 'account_id'],
        ]
    ],
    'buttons'=>[
        'create'=>['label'=>''],
    ],
    'crud'=>[
        'index'=>[
            'url'=>'/cp/crud/index',
            'title'=>$advert ? ('Отклики на объявление #' . $advert->id) : 'Отклики на объявления',
            'breadcrumbs'=>$advert ? [
                'Объявления'=>['/cp/crud/index?cid=accounts_adverts']
            ] : [],
            'gridView'=>[
                'dataProvider'=>call_user_func(function() use ($advert) {
                    $criteria=new \CDbCriteria();
                    
                    if($advert) {
                        $criteria->addColumnCondition(['advert_id'=>$advert->id]);
                    }
                    
                    return [
                        'criteria'=>$criteria,
                        'sort'=>['defaultOrder'=>'`published`, `create_time` DESC']
                    ];
                }),
                'summaryText'=>'Отклики на объявления {start} - {end} из {count}',
                'emptyText'=>'Откликов нет',
                'columns'=>[
                    'column.id',
                    [
                        'name'=>'advert_id',
                        'header'=>'Объявление',
                        'type'=>'raw',
                        'headerHtmlOptions'=>['style'=>'width:30%;text-align:center;white-space:nowrap;'],
                        'htmlOptions'=>['style'=>'width:30%;'],
                        'value'=>function($data) {
                            if($advert=$data->getRelated('advert')) {
                                $html=\CHtml::link($advert->getAdvertTitle(), '/cp/crud/index?cid=accounts_adverts&crud_models_ar_accounts_models_Advert[id]=' . $advert->id);
                                $html.='<small>';
                                $html.='<br/><b>Тип:</b> ' . (($advert->type == $advert::TYPE_SALE) ? 'Продажа' : 'Покупка');
                                $html.='<br/><b>Part Number:</b> ' . $advert->part_number;
                                $html.='<br/><b>Type of part:</b> ' . $advert->part_type;
                                $html.='<br/><b>Quantity:</b> ' . $advert->quantity;
                                $html.='<br/><b>Condition / Capability Code:</b> ' . $advert->code;
                                $html.='<br/><b>'.$advert->getDetailTypeLabel().':</b> ' . $advert->detail_type_value;
                                $html.='<br/><b>Category:</b> ' . $advert->category;
                                $html.='</small>';
                            }
                            else {
                                $html='Не найдено';
                            }
                            
                            return $html;
                        }
                    ],
                    [
                        'name'=>'account_id',
                        'header'=>'Отправитель',
                        'type'=>'raw',
                        'headerHtmlOptions'=>['style'=>'width:30%;text-align:center;white-space:nowrap;'],
                        'htmlOptions'=>['style'=>'width:30%;'],
                        'value'=>function($data) {
                            if($account=$data->getRelated('account')) {
                                $html=\CHtml::link($account->name, '/cp/crud/index?cid=accounts&crud_models_ar_accounts_models_Account[id]=' . $account->id);
                                $html.='<small>';
                                $html.='<br/><b>Категория:</b> ' . $account->getCategoryLabel();
                                if($country=$account->getRelated('country')) {
                                    if($region=$country->getRelated('region')) {
                                        $html.='<br/><b>Регион:</b> ' .$region->title;
                                    }
                                    $html.='<br/><b>Страна:</b> ' . $country->title;
                                }
                                $html.='<br/><b>E-Mail:</b> ' . $account->email;
                                $html.='<br/><b>Телефон:</b> ' . $account->formatPhone();
                                $html.='</small>';
                            }
                            else {
                                $html='Не найден';
                            }
                            
                            return $html;
                        }
                    ],
                    [
                        'name'=>'create_time',
                        'header'=>'Дата',
                        'type'=>'raw',
                        'headerHtmlOptions'=>['style'=>'width:10%;text-align:center;white-space:nowrap;'],
                        'htmlOptions'=>['style'=>'width:10%;text-align:center;'],
                        'value'=>function($data) {
                            return Y::formatDate($data->create_time);
                        }
                    ],
                    [
                        'name'=>'published',
                        'header'=>'Обработан',
                        'type'=>'common.ext.published',
                        'headerHtmlOptions'=>['style'=>'width:10%;text-align:center;white-space:nowrap;']
                    ],
                ]
            ]
        ],
        'create'=>[
            'onBeforeLoad'=>function(){R::e404();},
            'url'=>'/cp/crud/create',
        ],
        'update'=>[
            'onBeforeLoad'=>function(){R::e404();},
            'url'=>'/cp/crud/update',
        ],
        'delete'=>[
            'url'=>'/cp/crud/delete',
        ],
    ],
];