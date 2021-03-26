<?php
abstract class BaseSectionsController extends Controller
{
	public $itemsPageSize=36;
	public $itemsDefaultOrder='title DESC';
	public $itemsDataProviderOptions=[];
	
	public $sectionsHeader='';
	
	public $indexView='application.views.base.sections.index';
	public $sectionView='application.views.base.sections.section';
	public $viewView='application.views.base.sections.view';
	
	public $_sectionsView='application.views.base.sections._sections';
	public $_itemsView='application.views.base.sections._items';
	
	protected $baseUrl='/';
	protected $sectionClass='';
	protected $itemClass='';
	protected $homeTitle='';
	
	public function getHomeTitle() 
	{
		return $this->homeTitle;
	}
	
	public function actionIndex()
	{
		$sectionClassName=$this->sectionClass;
		$sections = $sectionClassName::model()->roots()->published()->findAll();
		
		$this->prepareSeo($this->getHomeTitle());
		$this->breadcrumbs->add($this->getHomeTitle());
		
		$this->render($this->indexView, compact('sections'));
	}

	public function actionSection($id)
	{
		$section = $this->loadModel($this->sectionClass, $id);

		$this->prepareSeo($section->title);
        $this->seoTags([
			'meta_h1'=>$section->getSeoH1(),
            'meta_title'=>$section->getSeoMetaTitle(),
			'meta_key'=>$section->getSeoMetaKeywords(),
			'meta_desc'=>$section->getSeoMetaDesc()
		]);

        ContentDecorator::decorate($section, 'text');

        $this->breadcrumbs->add($this->getHomeTitle(), $this->baseUrl);
        $this->breadcrumbs->addByNestedSet($section, $this->baseUrl . '/section');
        $this->breadcrumbs->add($section->title);

		$this->render($this->sectionView, compact('section'));
	}
	
	public function actionView($id)
	{
		$model = $this->loadModel($this->itemClass, $id);

		$this->prepareSeo($model->title);
        $this->seoTags([
			'meta_h1'=>$model->getSeoH1(),
            'meta_title'=>$model->getSeoMetaTitle(),
			'meta_key'=>$model->getSeoMetaKeywords(),
			'meta_desc'=>$model->getSeoMetaDesc()
		]);

        ContentDecorator::decorate($model, 'text');

        $this->breadcrumbs->add($this->getHomeTitle(), $this->baseUrl);
        $this->breadcrumbs->addByNestedSet($model->section, $this->baseUrl . '/section');
        $this->breadcrumbs->add($model->section->title, [$this->baseUrl . '/section', 'id'=>$model->section->id]);
        $this->breadcrumbs->add($model->title);

		$this->render($this->viewView, compact('model'));
	}
}
