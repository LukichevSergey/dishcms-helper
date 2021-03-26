<?php
/** @var DOrderController $this */

$this->widget('\DOrder\widgets\actions\OrderWidget', array(
	'mailAttributes' => array('categoryTitle', 'code', 'price', 'count'),
	'adminMailAttributes' => array('categoryTitle', 'code', 'price', 'count')
));
?>