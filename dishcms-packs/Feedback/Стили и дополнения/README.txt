В шаблон themes/<template>/views/layouts/main.php

------------
1. В <head>:
------------

<?php CmsHtml::js('/js/magnific-popup.js'); ?>
<script type="text/javascript">
$(function() {
    $('.open-popup-link').magnificPopup({
      type:'inline',
      midClick: true 
    });
});
</script>

------------
2. В <body>:
------------

	<div class="white-popup mfp-hide" id="callback">
	    <div class="popup-info">
			<?php $this->widget('\feedback\widgets\FeedbackWidget', array('id'=>'callback' , 'title' => 'Обратный звонок')); ?>
    	</div>
	</div>

------------
3. Кнопка:
------------

<a href="#callback"  class="callback left open-popup-link">Заказать звонок</a>