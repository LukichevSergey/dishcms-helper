<?php

class ServicesController extends BaseSectionsController
{
	public $itemsPageSize=99999;
	public $itemsDefaultOrder='title DESC';
	public $itemsDataProviderOptions=[];
	
	public $sectionsHeader='';
	
	public $indexView='application.views.base.sections.index';
	public $sectionView='application.views.base.sections.section';
	public $viewView='application.views.base.sections.view';
	
	public $_sectionsView='application.views.base.sections._sections';
	public $_itemsView='application.views.base.sections._items';
	
	protected $baseUrl='/services';
	protected $sectionClass='\crud\models\ar\ServiceSection';
	protected $itemClass='\crud\models\ar\Service';
	protected $homeTitle='Услуги нашего салона';
	protected $viewPath='application.views.services';	
}
