<?php
use common\components\helpers\HArray as A;

$this->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), ['attribute'=>'enable_debug_mode']));

echo \CHtml::tag('div', ['class'=>'alert alert-danger'], 'Не забудьте отключить режим отладки!')

?>