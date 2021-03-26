<?php
use common\components\helpers\HArray as A;
?>
<?php $this->widget('\common\widgets\form\TinyMceField', A::m(compact('form', 'model'), ['attribute'=>'text'])); ?>
