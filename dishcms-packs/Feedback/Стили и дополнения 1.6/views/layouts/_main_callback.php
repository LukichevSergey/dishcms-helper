<?php CmsHtml::js($this->template . '/js/magnific-popup.js'); ?>
<div class="white-popup mfp-hide" id="form-callback">
	<div class="popup-info">
		<div id="callback" class="">

		<?php $this->widget('\feedback\widgets\FeedbackWidget', array('id'=>'callback')); ?>
		</div>
	</div>
</div>

<div class="header">
	<div class="phone right">
    	<p class="callback"><a href="#form-callback" class="dozvon open-popup-link">Заказать звонок</a></p>
    </div>
</div>
