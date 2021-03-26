<?php
/**
 * Хлебные крошки для магазина
 */
Yii::import('widget.breadcrumbs.BaseBreadcrumbsWidget');

class BreadcrumbsShopWidget extends BaseBreadcrumbsWidget
{
 	/**
 	 * @var boolean не отображать в хлебных крошках главную страницу магазина.
 	 */
	public $hideShop=false;

	/**
	 * @var string заголовок главной страницы магазина.
	 * Если не задано будет взято из \Yii::t('BreadcrumbsArrayWidget.main', 'shopTitle');
	 */
	public $shopTitle=null;

	/**
	 * @var \Category|NULL category model. Если не задано, будут отображены крошки для основной страницы магазина.
	 * Если модель не задана, но задана модель self::$product будут отбражены крошки для категории self::$product->category.
	 */
	public $category=null;

	/**
	 * @var \Product|NULL product model. Если не задано, будут отображены крошки для категории.
	 */
	public $product=null;

	/**
	 * (non-PHPDoc)
	 * @see \CWidget::init()
	 */
	public function init()
	{
		if($this->shopTitle===null) {
			$this->shopTitle=\Yii::t('BreadcrumbsArrayWidget.main', 'shopTitle');
		}

		parent::init();
	}

	/**
	 * (non-PHPdoc)
	 * @see \CWidget::run()
	 */
	public function run()
	{
		if(!$this->hideShop) {
			$this->breadcrumbs[]=array('url'=>$this->controller->createUrl('shop/index'), 'title'=>$this->shopTitle);
		}

		if($this->category !== null) 
			$category=$this->category;
		elseif($this->product !== null) 
			$category=$this->product->category;
		else $category=null;
		
		if($category!==null) {
    		$ancestors=$category->ancestors()->findAll(array('select'=>'id,title,lft,rgt,root,level'));
    		if($ancestors) {
	        	foreach($ancestors as $ancestor) {
    	        	$this->breadcrumbs[]=array('url'=>$this->controller->createUrl('shop/category', array('id'=>$ancestor->id)), 'title'=>$ancestor->title);
	    	    }
	    	}
		    $this->breadcrumbs[]=array('url'=>$this->controller->createUrl('shop/category', array('id'=>$category->id)), 'title'=>$category->title);
    	}

    	if($this->product !== null) {
			$this->breadcrumbs[]=array('url'=>$this->controller->createUrl('shop/product', array('id'=>$this->product->id)), 'title'=>$this->product->title);
		}

		parent::run();
	}
}
