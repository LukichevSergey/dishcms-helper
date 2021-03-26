<?php
/** @var \CActiveForm $form */
/** @var \accounts\models\AccountEmailSettings $model */
/** @var string $id идетификатор почтового шаблона */
use common\components\helpers\HArray as A;
use accounts\components\helpers\HAccountEmail;

if($config=HAccountEmail::getTemplateConfig($id)):
    $attributes=A::get($config, 'attributes', []);
    foreach($attributes as $attribute=>$label) {
        echo HAccountEmail::getField($id, $attribute, $form, $model);
    }

    $shortcodes=HAccountEmail::getShortCodeLabels($id);
    if(!empty($shortcodes)):
    ?>
    <div class="alert alert-info">
    	<div style="margin-bottom:10px">
    		<strong>Доступны следующие шорткоды:</strong>
    	</div>
    	<ul style="list-style:none">
    	<?php foreach($shortcodes as $code=>$label): ?>
    		<li><code><?= $code; ?></code><? if($label) { echo " - {$label}"; } ?></li>
        <?php endforeach; ?>
		</ul>
	</div>
	<?php 
    endif;
endif;