<?php
/** @var \rpochta\widgets\OpsField $this */

if($this->jOpsButton == 'default-rpochta-ops-button') {
    echo \CHtml::tag('span', ['data-js'=>'rpochta-default-ops-button']);
}

echo $this->form->error($this->model, $this->attribute); 
echo $this->form->error($this->model, $this->attributeIndex); 

?>
<div data-js="rpochta-ops" style="display:none;">
    <?= $this->form->hiddenField($this->model, $this->attributeIndex); ?>
    <?= $this->form->hiddenField($this->model, $this->attributeLongitude); ?>
    <?= $this->form->hiddenField($this->model, $this->attributeLatitude); ?>
    <?= $this->form->hiddenField($this->model, $this->attribute); ?>
    <div id="js-rpochta-ops-map" style="width:100%;height:300px"></div>
</div>
<style>
#js-rpochta-ops-map {
    border: 1px solid #ccc;
    margin: 5px 0;
    position: relative;
}
#js-rpochta-ops-content {
    width: 100%;
    height: 300px;
    position: relative;
}
#js-rpochta-ops-map ymaps .rpochta-ops-btn {
    float: left;
    margin-right: 10px;
    background: #e28c3d;
    color: #fff;
    text-decoration: none;
    padding: 0 15px;
    border-radius: 3px;
}
#js-rpochta-ops-map ymaps .rpochta-ops-btn:hover {
    opacity: 0.7;
}
#js-rpochta-ops-map ymaps .rpochta-ops-btn-active {
    float: left;
    margin-right: 10px;
    background: #3c763d;
    color: #fff;
    text-decoration: none;
    padding: 0 15px;
    border-radius: 3px;
}
</style>
