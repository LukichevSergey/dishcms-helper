<?php
/** @var \ykassa\widgets\PaymentTypeField $this */
use common\components\helpers\HArray as A;

echo \CHtml::hiddenField('paymentType');
?><div class="ykassa__paymenttypes"><?
foreach($this->getActiveTypes() as $type=>$data):
?><div class="ykassa__paymenttypes-type">
	<? $title=A::get($data, 'title'); $default=A::get($data, 'default'); ?>
	<? if($title): 
		?><div class="ykassa__paymenttypes-header">
			<?=$default ? CHtml::link($title, 'javascript:;', ['data-payment-type'=>$default]) : $title?>
		</div><?
	endif; 
	?>
	<div class="ykassa__paymenttypes-items"><?
		foreach(A::get($data, 'types', []) as $code=>$config): 
		?><div class="ykassa__paymenttypes-item">
			<?=\CHtml::image($this->getIconSrc(A::get($config, 'icon')), A::get($config, 'title'), [
				'data-payment-type'=>A::get($config, 'code'), 
				'class'=>'js-ykassa-paymenttype',
				'title'=>A::get($config, 'title')
			]);?>
		</div><?
		endforeach;
	?></div>
</div><?
endforeach;
?></div>