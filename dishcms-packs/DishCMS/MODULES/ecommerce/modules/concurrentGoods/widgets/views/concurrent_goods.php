<?php
use common\components\helpers\HRequest;
/** @var \ecommerce\modules\concurrentGoods\widgets\ConcurrentGoods $this */

if($this->getData()) {
	echo $this->openTag();
	echo \CHtml::checkBoxList($this->name, HRequest::requestGet($this->name), $this->getData(), $this->htmlOptions);
	echo $this->closeTag();
}
?>