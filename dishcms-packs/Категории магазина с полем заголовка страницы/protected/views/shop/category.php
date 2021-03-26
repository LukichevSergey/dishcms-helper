<h1><?php echo $category->page_title?:$category->title; ?></h1> 

<?php if (!isset(Yii::app()->params['hide_shop_categories'])): ?>
<div id="category-list-module">
    <?php $this->renderPartial('/shop/_categories', array('categories'=>$categories, 'category_id'=>$category->id)); ?>
</div>
<?php endif; ?>

<?php if ($category->description): ?>
<div id="category-description" class="category-description">
    <?php echo $category->description; ?>
</div>
<?php endif; ?>

<div id="product-list-module">
    <?php $this->renderPartial('_products', compact('products', 'pages')); ?>
</div>

<?php $this->renderPartial('_category_js'); ?>
