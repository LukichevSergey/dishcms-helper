<?php
/** @var \DListBoxAttribute\widgets\admin\ListBoxWidget $this */
/** @var \CActiveForm $form */ 
/** @var \DListBoxAttribute $model */
use DListBoxAttribute\models\DListBoxAttributeRelation;

// @var string $hash хэш-строка
$hash = \HashHelper::generateHash(null, 8);
?>

<?php if(!$this->cssClass): $this->cssClass = 'listBox'; ?>
<style>
.listBox {
	width: auto;
	min-width: 200px;
	max-width: 630px;
	max-height: 150px;
	overflow-y: auto;
	border: 1px solid #ccc;
	padding: 5px;
}
.listBox .check-box-list-row {
	padding: 2px 0 2px 0;
}
.listBox .check-box-list-row input, label {
	display: inline !important;
}
.listBox .check-box-list-row-odd {
	background: #f4f4f4;
}
.listBox .check-box-list-row-all {
	border: 1px solid #999;
	background: #f0f0f0;
	padding: 5px 5px 5px 5px;
}
</style>
<?php endif; ?>

<?php echo $form->labelEx($model, $this->attributeOwner); ?>
<?php echo $form->error($model, $this->attributeOwner); ?>
<div class="<?php echo $this->cssClass; ?> check-box-list-row-<?php echo $hash; ?>">
<?php $relationClassName = DListBoxAttributeRelation::getClassName($this->model, $this->attribute); ?>
<?php echo CHtml::hiddenField(get_class($model) . "[{$relationClassName}-admin]"); ?>
<?php echo CHtml::checkBoxList(get_class($model) . "[{$relationClassName}][]", $this->getSelect(), $this->getItems(), array(
	'template' => '<div class="check-box-list-row">{input} {label}</div>',
	'separator' => '',
	'checkAll' => 'Выбрать все',
	'checkAllLast' => true,
	//'class' => $this->cssClass
)); ?>
<?php /*echo $form->checkBoxList($model, $this->attributeOwner, $this->getItems(), array(
	//'options' => $this->getHtmlOptions('checked', 'checked'),
	'template' => '<div class="check-box-list-row">{input} {label}</div>',
	'separator' => '',
	'checkAll' => 'Выбрать все',
	'checkAllLast' => true,
	//'class' => $this->cssClass
)); */?>
</div>

<script>
	$(function() {
		$(".<?php echo $this->cssClass; ?> .check-box-list-row:odd").addClass("check-box-list-row-odd");
		$(".<?php echo $this->cssClass; ?> input[name$='_all']").parents(".check-box-list-row").addClass("check-box-list-row-all");
		$(".check-box-list-row-<?php echo $hash; ?> input[name$='_all']").live("click", function() {
			var $checked = Boolean($(this).filter(":checked").length); 
			$(".check-box-list-row-<?php echo $hash; ?> input:checkbox:not([name$='_all'])").each(function() {
				$(this).attr("checked", $checked); 
			});
		});
	});
</script>