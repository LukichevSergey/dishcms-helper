<?php
use common\components\helpers\HArray as A;

$this->owner->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), ['attribute'=>'sync_url']));
$this->owner->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), ['attribute'=>'sync_token']));
$this->owner->widget('\common\widgets\form\NumberField', A::m(compact('form', 'model'), ['attribute'=>'sync_limit', 'htmlOptions'=>['class'=>'form-control w10']]));
$this->owner->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), ['attribute'=>'sync_reload_files']));