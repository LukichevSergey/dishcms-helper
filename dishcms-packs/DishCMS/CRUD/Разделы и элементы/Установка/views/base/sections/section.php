<?php
/** @var \components\base\BaseSectionsController $this */
/** @var \common\components\base\ActiveRecord $section */
use common\components\helpers\HArray as A;
?>
<h1><?= $section->getSeoH1(); ?></h1>
<? $this->renderPartial($this->_sectionsView, ['sections'=>$section->getChildrenSections(), 'header'=>$this->sectionsHeader]); ?>
<? $this->renderPartial($this->_itemsView, [
	'itemsDataProvider'=>$section->getItemsDataProvider(A::m([
		'criteria'=>[
			'scopes'=>'published',
		],
		'pagination'=>[
			'pageVar'=>'p',
			'pageSize'=>$this->itemsPageSize
		],
		'sort'=>[
			'sortVar'=>'s',
			'defaultOrder'=>$this->itemsDefaultOrder
		]
	], $this->itemsDataProviderOptions))
]); ?>

<div class="base__sections_section-text"><?= $section->text; ?></div>
