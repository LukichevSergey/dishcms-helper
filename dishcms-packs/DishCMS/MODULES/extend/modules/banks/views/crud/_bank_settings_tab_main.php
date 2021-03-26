<?
use common\components\helpers\HArray as A;

// $this->widget('\common\widgets\form\DropDownListField', A::m(compact('form', 'model'), [
// 	'attribute'=>'cropTop',
// 	'data'=>['top'=>'Верх', 'center'=>'Центр', 0=>'Нет'],
// ]));

$this->widget('\common\widgets\form\TinyMceField', A::m(compact('form', 'model'), ['attribute'=>'main_text']));
?>