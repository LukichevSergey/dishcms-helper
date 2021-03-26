<?php $this->pageTitle = 'Экспорт товаров YML - '. $this->appName; ?>
<div class="form">
	<h1>Экспорт товаров YML</h1>

	<form action="" method="post">
		<input class="button default-button settings-b" type="submit" name="yml" value="Перегенерировать YML файл для Yandex Market">
		<div class="clr"></div>
		<br>
		<br>
		<p><a href="<?php echo Yii::app()->createUrl('/admin/yml/download');?>">Скачать текущий YML</a></p>
		<br>
		<p>Ссылка: <?php echo Yii::app()->request->getBaseUrl(true);?>/yml/export.yml</p>
		<br>
		<div class="row">
			<label>Выберите категории</label>
			<?php
				$this->widget('ext.chosen.Chosen',array(
				   'name' => 'categories',
				   'multiple' => true,
				   'placeholderMultiple' => 'Все категории',
				   'data' => CHtml::listData(Category::model()->findAll(), 'id', 'title'),
				));
			?>
		</div>
		<div class="row">
			<label>Исключить категории</label>
			<?php
				$this->widget('ext.chosen.Chosen',array(
				   'name' => 'notinclude',
				   'multiple' => true,
				   'placeholderMultiple' => 'Все категории',
				   'data' => CHtml::listData(Category::model()->findAll(), 'id', 'title'),
				));
			?>
		</div>
		<div class="row">
	        <input name="notexist" id="notexist" value="1" type="checkbox">
	        <label class="inline" for="notexist">Исключить товары, которых нет в наличии</label>
		</div>
	</form>
</div>
