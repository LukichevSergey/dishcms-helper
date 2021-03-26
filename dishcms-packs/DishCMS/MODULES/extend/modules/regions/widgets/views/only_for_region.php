<?php
/** @var \extend\modules\regions\widgets\OnlyForRegion $this */
use extend\modules\regions\components\helpers\HRegion;

echo \CHtml::tag(
	$this->tag, 
	$this->tagOptions, 
	\Yii::t($this->messageCategory, $this->messageText, ['{title}'=>HRegion::i()->getTitle($this->regionId)])
);
?>