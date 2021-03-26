<?php
/** @var \crud\models\ar\extend\modules\comments\Comment $data */
use common\components\helpers\HArray as A;

$ratingLabels = [
    1 => 'Ужасно!',
    2 => 'Неудовлетворительно!',
    3 => 'Удовлетворительно!',
    4 => 'Хорошо!',
    5 => 'Отлично!',
];
?>
<li class="reviews-item reviews__item">
    <div class="reviews-item__header">
        <h3 class="reviews-item__name"><?= $data->name; ?></h3>
        <div class="reviews-item__date"><?= \Yii::app()->dateFormatter->format('dd.MM.yyyy', $data->create_time); ?></div>
    </div>
    <div class="reviews-item__rating" data-rating="<?= $data->rating; ?>">
        <div class="reviews-item__rating-circles">
            <div class="reviews-item__rating-circle"></div>
            <div class="reviews-item__rating-circle"></div>
            <div class="reviews-item__rating-circle"></div>
            <div class="reviews-item__rating-circle"></div>
            <div class="reviews-item__rating-circle"></div>
        </div>
        <div class="reviews-item__rating-word"><?= A::get($ratingLabels, $data->rating); ?></div>
    </div>
    <div class="reviews-item__text"><?= $data->comment; ?></div>
</li>