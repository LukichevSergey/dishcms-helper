<?php
/** @var \banks\controllers\DefaultController $this */
use common\components\helpers\HYii as Y;
use settings\components\helpers\HSettings;

$t=Y::ct('\extend\modules\banks\BanksModule.common', 'banks');
$settings=HSettings::getById('banks');
?>
<h1><?= $settings->meta_h1?:$this->getHomeTitle(); ?></h1>
<div class="banks__description"><?=$settings->main_text?></div>

<? $this->widget('\crud\widgets\ListWidget', [
	'cid'=>'bank', 
	'options'=>['criteria'=>['scopes'=>'actived'], 'pagination'=>['pageSize'=>99]],
	'view'=>'extend.modules.banks.views.default.list',
	'itemView'=>'extend.modules.banks.views.default._list_item'
]) ?>