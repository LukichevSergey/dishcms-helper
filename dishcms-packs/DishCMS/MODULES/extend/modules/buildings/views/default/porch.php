<?php
/** @var \extend\modules\buildings\models\Porch $model */
?>
<h1><?= $model->getNumberTitle(true); ?></h1>
<?php
$floors = $model->getRelated('floors', true, [
    'select'=>'id, title, number', 
    'scopes'=>['published', 'scopeSort'=>['building_floors', $model->id, false, 'floors']]]
);

foreach($floors as $floor) {
    echo \CHtml::link($floor->getNumberTitle(), ['/buildings/floor', 'id'=>$floor->id], ['style'=>'margin-bottom: 20px;display:block;']);
}
?>