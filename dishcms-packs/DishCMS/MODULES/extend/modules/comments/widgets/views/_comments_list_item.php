<?php
/** @var \crud\models\ar\extend\modules\comments\Comment $data */
?>
<div class="extend__comments-item">
    <div class="extend__comments_item-header">
        <h3 class="extend__comments_item-name"><?= $data->name; ?></h3>
        <div class="extend__comments_item-date"><?= \Yii::app()->dateFormatter->format('dd.MM.yyyy', $data->create_time); ?></div>
    </div>
    <div class="extend__comments_item-rating" data-rating="<?= $data->rating; ?>"><?= $data->rating; ?></div>
    <div class="extend__comments_item-comment"><?= $data->comment; ?></div>
</div>