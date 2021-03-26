<?php
use common\components\helpers\HArray as A;

?>
<div class="panel panel-default">
    <div class="panel-heading">Настройки для сервиса MailChimp</div>
    <div class="panel-body"><?
    
    $this->owner->widget('\common\widgets\form\TextField', [
        'form'=>$this->form, 
        'model'=>$this->model,
        'attribute'=>'mailchimp_key', 
        'htmlOptions'=>['class'=>'w50 inline form-control']
    ]);
    
    $this->owner->widget('\common\widgets\form\TextField', [
        'form'=>$this->form, 
        'model'=>$this->model,
        'attribute'=>'mailchimp_default_list_id', 
        'htmlOptions'=>['class'=>'w25 inline form-control']
    ]);
    
    ?>
    </div>
</div>
