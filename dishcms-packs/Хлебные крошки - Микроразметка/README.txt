Использование

Страницы page.php
<?$this->widget('widget.breadcrumbs.BreadcrumbsPageWidget', array('model'=>$page))?>

Новости events.php, 
<?$this->widget('widget.breadcrumbs.BreadcrumbsEventWidget')?>
event.php
<?$this->widget('widget.breadcrumbs.BreadcrumbsEventWidget', array('model'=>$event))?>

Магазин shop.php, 
<?$this->widget('widget.breadcrumbs.BreadcrumbsShopWidget')?>

category.php 
<?$this->widget('widget.breadcrumbs.BreadcrumbsShopWidget', compact('category'))?>

product.php
<?$this->widget('widget.breadcrumbs.BreadcrumbsShopWidget', compact('product'))?>

Отзывы
<?$this->widget('widget.breadcrumbs.BreadcrumbsArrayWidget', array('breadcrumbs'=>array(array('title'=>'Отзывы', 'url'=>'question/index'))))?>