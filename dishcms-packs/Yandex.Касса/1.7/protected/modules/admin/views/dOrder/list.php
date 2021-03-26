<?php
/** @var DOrderController $this */

$this->widget('\DOrder\widgets\admin\actions\ListWidget', array(
	'paymentType'=>\DOrder\models\DOrder::TYPE_YANDEX
));
?>