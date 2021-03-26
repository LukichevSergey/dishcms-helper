<?php
/** @var \menu\controllers\Admin $this */
/** @var string $list @see \menu\components\helpers\HtmlHelper::getList() */
use \menu\components\helpers\MenuHtmlHelper;
?>

<?php echo MenuHtmlHelper::getList($menu, null, null, array('class'=>'sortable'), null, null); ?>

<script type="text/javascript">
	$(document).ready(function(){
		$('.sortable').nestedSortable({
			handle: 'div',
			items: 'li',
			toleranceElement: '> div'
		});
	});
</script>
