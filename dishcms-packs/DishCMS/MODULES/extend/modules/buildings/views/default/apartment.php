<?php
/** @var \extend\modules\buildings\models\Apartment $model */

use common\components\helpers\HArray as A;
use common\components\helpers\HHtml;

$h1 = $this->pageTitle; //'Квартира: ' . $model->title;

$props = [];
if((int)$model->rooms > 0) {
    $props['Кол-во комнат'] = (int)$model->rooms;
}
if((float)$model->area > 0) {
    $props['Общая площадь'] = (float)$model->area . ' <sup>2</sup>';
}
if((float)$model->price > 0) {
    $props['Цена'] = HHtml::price($model->price) . ' руб.';
}
if((float)$model->sale_price > 0) {
    $props['Цена по акции'] = CHtml::tag('span', ['class'=>'apartment__props-saleprice'], HHtml::price($model->sale_price) . ' руб.');
}
$props=A::m($props, $model->getPropsList());
?>
<div class="apartment__page">
	<div class="apartment__left">
		<div class="apartment__image">
		<?php 
		if($model->imageBehavior->exists()): 
			$title = $model->imageBehavior->getAlt();
			if(empty($title)) {
			    $title = $h1;
			}
			echo \CHtml::link($model->img(250, 250), $model->getSrc(), ['rel'=>'images-gallery', 'class'=>'image-full', 'title'=>$title]);
		else: 
		    echo \CHtml::image(HHtml::phSrc(['w'=>250, 'h'=>250, 't'=>'Нет фото']));
		endif; 
		?>
		</div>
	</div>
	<div class="apartment__right">
		<h1><?= $h1; ?></h1>
		<div class="apartment__status <?= $model->sold ? 'apartment__status-sold' : 'apartment__status-available'?>">
			<?= $model->sold ? 'продана' : 'свободна'; ?>
		</div>
		<?php if(!empty($props)): ?>
		<div class="apartment__props">
			<ul class="apartment__props-list">
				<?php foreach($props as $title=>$value): if(!empty($title) && !empty($value)): ?>
				<li><span class="apartment__props-title"><?= $title; ?></span>: <span class="apartment__props-value"><?= $value; ?></span></li>
				<?php endif; endforeach; ?>
			</ul>
		</div><?php 
		endif; 
		?>
		<?php if(!$model->sold): ?>
			<a href="#form-callback" class="btn btn-callback open-popup-link">Оставить заявку</a>
		<?php endif; ?>
	</div>
	<div class="apartment__description">
		<?= $model->text; ?>
	</div>
</div>
<?php if(!$model->sold): ?>
<div style="display: none;">
    <div class="white-popup mfp-hide" id="form-callback">
        <div class="popup-info">
            <?php $this->widget('\feedback\widgets\FeedbackWidget', [
                'id' => 'callback', 
                'view'=>'callback', 
                'title'=>$h1,
                'skip'=>['apartment_id', 'apartment'],
                'params'=>['apartment_id'=>$model->id, 'apartment_title'=>$h1]
            ]); ?>
        </div>
    </div>
</div>
<?php endif; ?>