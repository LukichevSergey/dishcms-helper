<?php

class SearchController extends Controller
{



public function actionAutoComplete() {
     

    if (isset($_GET['q'])) {

    	$query = Yii::app()->request->getQuery('q');
		$criteria = new CDbCriteria();
		$criteria->addSearchCondition('title', $query, true, 'OR');
		$criteria->addSearchCondition('description', $query, true, 'OR');
		$criteria->limit = 10;
         
        $products = Product::model()->findAll($criteria);
         
        $resStr = '';
        foreach ($products as $product) {
            $resStr .= $product->title."\n";
        }
        echo $resStr;
    }
}
	public function actionIndex()
	{
		$query = Yii::app()->request->getQuery('q');
		
		if (mb_strlen($query, 'UTF-8') < 3) {
			$this->prepareSeo('Слишком короткий запрос');
			$this->render('index_empty');
			return;
		}
		
		if(\Yii::app()->request->isAjaxRequest) {
			switch(strtolower($_GET['ajax'])) {
				case 'eventlistview': 
					$dataProvider=$this->getEventsDataProvider($query);
					$view='_events_view'; 
					break;
				case 'productlistview':
					$dataProvider=$this->getProductsDataProvider($query);
					$view='_products_view';
					break;
				case 'pagelistview':
					$dataProvider=$this->getPageDataProvider($query);
					$view='_page_view';
					break;
				case 'categorylistview':
					$dataProvider=$this->getCategoryDataProvider($query);
					$view='_category_view';
					break;
				default: throw new \CHttpException(400);
			}
			$this->renderPartial($view, compact('dataProvider'), false, true);
		}
		else {
			$this->prepareSeo('Результаты поиска');
			$this->render('index', array(
				'eventsDataProvider'=>$this->getEventsDataProvider($query), 
				'pagesDataProvider'=>$this->getPageDataProvider($query), 
				'data_p'=>$this->getProductsDataProvider($query), 
				'categoryDataProvider'=>$this->getCategoryDataProvider($query)
			));
		}
	}
	
	protected function getEventsDataProvider($query)
	{
		// поиск по новостям
		$criteria = new CDbCriteria();
		$criteria->addSearchCondition('title', $query, true, 'OR');
		$criteria->addSearchCondition('text', $query, true, 'OR');
		$criteria->addSearchCondition('publish', '1', false);
		$criteria->order='created DESC';
		
		$pagination = new CPagination();
		$pagination->pageSize = 5;
		
		return new CActiveDataProvider('Event', array(
			'criteria'=>$criteria,
			'pagination' => $pagination
		));
	}
	
	protected function getCategoryDataProvider($query)
	{
		// поиск по категориям
		$criteria = new CDbCriteria();
		$criteria->addSearchCondition('title', $query, true, 'OR');
		$criteria->addSearchCondition('description', $query, true, 'OR');
		
		$pagination = new CPagination();
		$pagination->pageSize = 5;
		
		return new CActiveDataProvider('Category', array(
			'criteria'=>$criteria,
			'pagination' => $pagination
		));
	}
	
	protected function getPageDataProvider($query)
	{
		// поиск по страницам
		$criteria = new CDbCriteria();
		$criteria->addSearchCondition('title', $query, true, 'OR');
		$criteria->addSearchCondition('intro', $query, true, 'OR');
		$criteria->addSearchCondition('text', $query, true, 'OR');
		
		$pagination = new CPagination();
		$pagination->pageSize = 5;
		
		return new CActiveDataProvider('Page', array(
			'criteria'=>$criteria,
			'pagination' => $pagination
		));
	}
	
	protected function getProductsDataProvider($query)
	{
		// поиск по продукции
		$criteria = new CDbCriteria();
		$criteria->addSearchCondition('title', $query, true, 'OR');
		$criteria->addSearchCondition('description', $query, true, 'OR');
		
		return new CActiveDataProvider('Product', array(
			'sort'=>array(
				'defaultOrder'=>'ordering ASC , id DESC',
			),
			'pagination'=>array(
				'pageSize' => 15,
			),
			'criteria'=>$criteria,
		));
	}
}