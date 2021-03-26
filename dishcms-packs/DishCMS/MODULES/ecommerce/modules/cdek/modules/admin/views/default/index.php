<?php
/** @var \cdek\modules\admin\controllers\DefaultController $this */
use common\components\helpers\HYii as Y;

$t=Y::ct('\cdek\modules\admin\AdminModule.controllers/default', 'cdek');

echo \CHtml::tag('h1', [], $t('page.title'));

$this->renderPartial('cdek.modules.admin.views.default._cdek_city_ym_geocodes');

$this->widget('zii.widgets.jui.CJuiTabs', [
    'tabs'=>[
        'Накладные'=>['content'=>$this->renderPartial('cdek.modules.admin.views.default._tab_dispatches', [], true), 'id'=>'tab-dispatches'],
        'Города'=>['content'=>$this->renderPartial('cdek.modules.admin.views.default._tab_cities', compact('cdekCityImportFormModel', 'cityDataProvider'), true), 'id'=>'tab-cities'],
        //'Настройки'=>['content'=>$this->renderPartial('cdek.modules.admin.views.default._tab_settings', [], true), 'id'=>'tab-settings'],
    ],
    'options'=>[]
]); 
?>
