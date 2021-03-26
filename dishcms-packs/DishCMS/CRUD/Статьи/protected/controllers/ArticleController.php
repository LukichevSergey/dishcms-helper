<?php
/**
 * Контроллер модуля Статьи
 */
class ArticleController extends Controller
{
	/**
	 * (non-PHPdoc)
	 * @see AdminController::filters()
	 */
	public function filters()
	{
		return CMap::mergeArray(parent::filters(), [
		]);
	}
	
	/**
	 * Просмотр акции
	 * @param integer $id id модели.
	 */
	public function actionView($id)
	{
		$model=$this->loadModel('Article', $id, true, Article::model()->actived()->getDbCriteria());
		
		$this->prepareSeo($model->title);
		$this->seoTags($model);
		ContentDecorator::decorate($model);
		
		$this->breadcrumbs->add($this->getHomeTitle(), '/stati');
		$this->breadcrumbs->add($model->title);
		
		$this->render('view', compact('model'));
	}
	
	/**
	 * Список акций
	 */
	public function actionList()
	{
		$this->seoTags(array(
			'meta_h1'=>D::cms('articles_meta_h1', $this->getHomeTitle()),
			'meta_title'=>D::cms('articles_meta_title', $this->getHomeTitle()),
			'meta_key'=>D::cms('articles_meta_key'),
			'meta_desc'=>D::cms('articles_meta_desc')
		));
		$this->breadcrumbs->add($this->getHomeTitle());
		
		$dataProvider=\Article::model()
			->previewColumns()
			->actived()
			->getDataProvider(['order' => '`t`.`create_time` DESC'], array('pageVar'=>'p'));
		
		$this->render('list', compact('dataProvider'));	
	}
	
	/**
	 * Получить основной заголовок
	 * @return string
	 */
	public function getHomeTitle()
	{
		return 'Статьи';
	}
}
