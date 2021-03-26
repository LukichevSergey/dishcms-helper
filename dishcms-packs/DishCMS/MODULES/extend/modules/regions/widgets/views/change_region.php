<?php
/** @var \extend\modules\regions\widgets\ChangeRegion $this */
use common\components\helpers\HYii as Y;
use extend\modules\regions\components\helpers\HRegion;

$data = $this->getData();
if(empty($data)) {
	return;
}

$t = Y::ct('\extend\modules\regions\RegionsModule.widgets/changeRegion', 'extend.regions');

echo \CHtml::openTag($this->tag, $this->tagOptions);
echo \CHtml::tag('span', ['class'=>'region__changebox-title label label-primary'], $t('title'));

$current=isset($_COOKIE[$this->cookieName]) ? $_COOKIE[$this->cookieName] : HRegion::i()->getId();
echo \CHtml::dropDownList($this->name, $current, $data, ['class'=>'form-control js-region__changebox']);

echo \CHtml::closeTag($this->tag);
?>