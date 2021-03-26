<?php
/** @var DynamicAttributesWidget $this */  
use \AttributeHelper as A;
?>
<style>
.daw-inpt {
	width: 85% !important;
	margin: 0 !important;
	padding: 2px 5px !important;
	font-size: 12px !important;
	height: 20px !important;
}
</style>

<div class="daw-wrapper" data-item="<?php echo $this->attribute; ?>">
	<table class="daw-template" style="display: none !important;"><tbody><?php echo $this->generateRow(null); ?></tbody></table>
	<table class="daw-table" border=0 cellpadding="0" cellspacing="0">
		<thead>
			<tr>
				<th class="daw-thead-visible" title="Отображать на сайте">Акт.</th>
				<?php foreach($this->header as $title) echo "<th>{$title}</th>"; ?>
				<th class="daw-thead-remove">Удалить</th>
			</tr>
		</thead>
		<?php if(!$this->hideAddButton): ?>
		<tfoot>
			<tr><td colspan="<?=count($this->header)+2?>"><button class="default-button daw-btn-add" data-attribute="<?php echo $this->attribute; ?>">Добавить</button></td></tr>
		</tfoot>
		<?php endif;?>
		<tbody>
			<?php  
			foreach(($this->behavior->get() ?: $this->default) as $index=>$data)
				echo $this->generateRow($index, $data);
			?>
		</tbody>
	</table>
</div>