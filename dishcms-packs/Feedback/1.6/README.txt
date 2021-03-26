================================
| Для дишмана версии ранее 1.7 |
================================

I. Установка и настройка модуля Feedback
-------------------------------------
1. /protected/config/defaults.php
1.1. 
	'aliases'=>array(
		...
		'feedback'=>'application.modules.feedback',
	),

1.2.
	'import'=>array(
		...
		'application.components.helpers.*',
		'application.components.models.*',
		'application.components.behaviors.*',

1.3.
	'modules'=>array(
		...
		'feedback'

1.4. 
	Закомментарить:
	'components'=>array(
		...
		'urlManager'=>array(
			'urlFormat'=>'path',
            'showScriptName'=>false,
			//'rules'=>include(dirname(__FILE__).DS.'urls.php') <--- вот эту строку
		),
		
	Добавить:
	'onBeginRequest'=> array('UrlManagerHelper', 'onBeginRequest'),
	'params'=>array(
		'UrlManagerHelper' => array(
			'modules' => array('feedback'),
			'defaultRules' => include(dirname(__FILE__).DS.'urls.php')
		),


-------------------------------
2. /protected/data/routes.php
-------------------------------
    Добавить в раздел "admin":
    	'feedback' => array(
    		'combine' => 'feedback',
        ),

---------------------------------------------	        
3. /modules/admin/views/layouts/column2.php
---------------------------------------------
	Вставить код:
	<script type="text/javascript">
      	$(function() {
	      	// @hook of feedback module for admin menu.
	       	<?php foreach(\feedback\components\FeedbackFactory::getFeedbackIds() as $id): ?>
	       		<?php $count = \feedback\components\FeedbackFactory::factory($id)->getModelFactory()->getModel()->uncompleted()->count(); ?>
       			$("#site-menu a[href$='/feedback/<?php echo $id; ?>']").after('<span class="notify notifybutton feedback-<?php echo $id; ?>-count-in-title"><?php echo $count; ?></span>');
       		<?php endforeach; ?>
       	});
    </script>

----------------------
4. Поправить стили
------------------------
/protected/modules/admin/views/layouts/main.php ("notify notifybutton" => заменить на => "notify notifybutton notifyorders")
protected/modules/admin/assets/css/style.css 
.notifyorders {
 left: 67px !important;
 top: -4px !important;
}

Также, иногда приходится править (пример)
.notifybutton {
    position: relative;
    top: -30px;
    left: 127px;
    padding: 5px 10px;
}

-------------------------
5.  Добавить стиль, если надо для подсветки элементов формы при ошибке
-------------------------
.inpt-error {
  	border: 1px solid #f00 !important;
}

--------------------
II. Использование
--------------------
<?php $this->widget('\feedback\widgets\FeedbackWidget', array('id'=>'<ID обратной связи>')); ?>
