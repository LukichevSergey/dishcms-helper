<?php
/** @var $this refers to the owner of this list view widget. For example, if the widget is in the view of a controller, then $this refers to the controller. */
/** @var $data refers to the data item currently being rendered. */
/** @var $index refers to the zero-based index of the data item currently being rendered. */
/** @var $widget refers to this list view widget instance. */
?>
<li>
	<div class="clearfix">
		<div class="bank-item-left left">
			<div class="bank__image"><?= $data->img(200,200); ?></div>
			<div class="bank__link">
				<a href="#consult-form" class="consult-form open-popup-link">Записаться на консультацию</a>
			</div>
		</div>
		<div class="bank-item-right right">
			<div class="clearfix">
				<div class="bank__title left"><?= $data->title; ?></div>
				<div class="bank__rate right">
					Ставка: от <?=$data->bank_rate; ?>% 
					<span class="bank__rate_decrease"><?= $data->decrease; ?></span>
				</div>
			</div>
			<div class="bank__preview_text"><?= $data->preview_text; ?></div>
		</div>
	</div>
</li>
