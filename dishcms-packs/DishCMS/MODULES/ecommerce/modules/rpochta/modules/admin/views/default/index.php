<?php
/** @var \cdek\modules\admin\controllers\DefaultController $this */
use common\components\helpers\HYii as Y;

$t=Y::ct('\rpochta\modules\admin\AdminModule.controllers/default', 'cdek');

echo \CHtml::tag('h1', [], $t('page.title'));

$this->renderPartial('rpochta.modules.admin.views.default._requests');
?>
