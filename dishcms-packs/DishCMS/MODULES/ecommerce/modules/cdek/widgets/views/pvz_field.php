<?php
/** @var \cdek\widgets\PvzField $this */

if($this->jPvzButton == 'default-cdek-pvz-button') {
    echo \CHtml::tag('span', ['data-js'=>'cdek-default-pvz-button']);
}

echo $this->form->error($this->model, $this->attribute); 
?>
<div data-js="cdek-pvz" style="display:none;">
    <?= $this->form->hiddenField($this->model, $this->attribute); ?>
    <div class="cdek__pvz-map" id="js-cdek-pvz-map" style="width:100%;height:320px"></div>
</div>
