<?php
/** @var \ecommerce\modules\concurrentGoods\widgets\admin\ConcurrentGoodsField $this */

echo $this->openTag();

echo $this->labelTag();
if($this->getData()) {
	echo $this->form->checkBoxList($this->model, $this->attribute, $this->getData(), $this->htmlOptions);
	echo $this->errorTag();
}
else {
	echo CHtml::tag('p', [], 'Нет сопутствующих товаров.');
	echo CHtml::link('Добавить сопутствующий товар', ['/cp/crud/index', 'cid'=>$this->cid], ['class'=>'btn btn-default']);
}

echo $this->closeTag();
?>