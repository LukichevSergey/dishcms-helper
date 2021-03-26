<?php
/** @var \buildings\modules\admin\controllers\DefaultController $this */
use common\components\helpers\HYii as Y;

Y::jsFile('/js/fancybox/jquery.fancybox.min.js');
Y::cssFile('/js/fancybox/jquery.fancybox.min.css');

$t=Y::ct('\extend\modules\buildings\modules\admin\AdminModule.controllers/default', 'buildings');
?>
<h1><?=$t('page.title')?></h1>

<?php $this->renderPartial('extend.modules.buildings.modules.admin.views.default._porches', compact('porch')); ?>