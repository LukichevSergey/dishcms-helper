<?php
use common\components\helpers\HArray as A;

$this->owner->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), ['attribute'=>'sync_token']));
// $this->owner->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), ['attribute'=>'sync_url']));