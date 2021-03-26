<div class="search-wrapper">
   	<form action="<?php echo $this->createUrl('search/index'); ?>">
		<input type="text" name="q" placeholder="Поиск по сайту" value="<?php echo Yii::app()->request->getQuery('q') ?>">
		<input type="submit" value="">	
	</form>                
</div>