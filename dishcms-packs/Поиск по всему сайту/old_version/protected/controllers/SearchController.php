<?php

class SearchController extends Controller
{
	public function actionIndex()
	{
		$query = Yii::app()->request->getQuery('q');
		
		if (mb_strlen($query, 'UTF-8') < 3) {
			$this->prepareSeo('Слишком короткий запрос');
			$this->render('index_empty');
			return;
		}
		
		// поиск по акциям (новостям)
		$criteria = new CDbCriteria();
		$criteria->addSearchCondition('title', $query, true, 'OR');
		$criteria->addSearchCondition('text', $query, true, 'OR');
		$criteria->addSearchCondition('publish', '1', false);
		
		$pagination = new CPagination();
		$pagination->pageSize = 3;
		$eventsDataProvider = new CActiveDataProvider('Event', array(
			'criteria'=>$criteria,
			'pagination' => $pagination
		));
		
		// поиск по страницам
		$criteria = new CDbCriteria();
		$criteria->addSearchCondition('title', $query, true, 'OR');
		$criteria->addSearchCondition('intro', $query, true, 'OR');
		$criteria->addSearchCondition('text', $query, true, 'OR');
		
		$pagination = new CPagination();
		$pagination->pageSize = 3;
		$pagesDataProvider = new CActiveDataProvider('Page', array(
			'criteria'=>$criteria,
			'pagination' => $pagination 
		));
		
		// поиск по продукции
		$criteria = new CDbCriteria();
		$criteria->addSearchCondition('title', $query, true, 'OR');
		$criteria->addSearchCondition('description', $query, true, 'OR');
		$criteria->addSearchCondition('code', $query, true, 'OR');
			
		$dataProvider = new CActiveDataProvider('Product', array(
			'criteria'=>$criteria
		));
	
		$this->prepareSeo('Результаты поиска');
		$this->render('index', compact('dataProvider', 'eventsDataProvider', 'pagesDataProvider'));
	}
}