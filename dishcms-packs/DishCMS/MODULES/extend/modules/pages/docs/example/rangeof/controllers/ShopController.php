<?php
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HModel;
use common\components\helpers\HCache;
use common\components\helpers\HRequest;
use settings\components\helpers\HSettings;
use extend\modules\seo\components\helpers\HSeo;

class ShopController extends Controller
{
    /**
     * Страница фильтра товаров.
     */
    public function actionFilter()
    {
    	$params=Product::model()->getFilterRequestParams('filter');
    	$dataProvider=Product::model()->visibled()->filter('filter', A::m([
    		['Product', 'filterDefaultHandler'],
    	], Product::model()->rangeofBehavior->getFilterHandler()))->eav()->getDataProvider([
    		'pagination'=>[
                'pageSize' => Yii::app()->request->getQuery('size', 12),
                'pageVar'=>'p',
    			'params'=>$params
            ],
            'sort'=>[
                'sortVar'=>'s', 
                'descTag'=>'d',
            	'params'=>Product::model()->getDefaultOrder()//$params
            ]
    	]);
    	
    	if(\Yii::app()->request->isAjaxRequest) {
    		$this->renderPartial('filter', compact('dataProvider'), false, true);
    	}
    	else {
    		$this->breadcrumbs->add($this->getHomeTitle(), '/shop');    		
    		// Распродажа
    		if(isset($_POST['filter']['marker'])) {
    			$title=\Yii::t('shop', 'filter.marker.page.title');    			
    			$this->breadcrumbs->add($title);
    			$this->prepareSeo(\Yii::t('shop', 'filter.marker.page.meta_title'));
    			$this->render('sale', compact('dataProvider', 'title'));
    		}
    		// Область применения
    		elseif(isset($_POST['filter']['rangeof'][0])) {
    			$this->renderRangeofPage($_POST['filter']['rangeof'][0], $dataProvider);    			
    		}
    		else {
    			HRequest::e404();
    		}    		
    	}
    }
    
    /**
     * Страница "области применения"
     * @param string $sef ЧПУ страницы области применения.
     * @param \CActiveDataProvider $dataProvider товары.
     */
    protected function renderRangeofPage($sef, $dataProvider)
    {
    	$model=\Rangeof::model()->sef($sef)->utcache(HCache::YEAR)->find();
    	 
    	if(!$model) HRequest::e404();
    	
    	ContentDecorator::decorate($model, 'detail_text');
    	 
    	HSeo::seo($model);
    	 
    	$this->breadcrumbs->add(\Yii::t('shop', 'filter.rangeof.page.title'), ['site/page', 'id'=>13]);
    	$this->breadcrumbs->add($model->title);
    	
    	$this->render('rangeof', compact('model', 'dataProvider'));
    }
    
    /**
     * Получить заголовок основной страницы каталога.
     * @return string
     */
    public function getHomeTitle()
    {
    	return D::cms('shop_title',Yii::t('shop','shop_title'));
    }
}
