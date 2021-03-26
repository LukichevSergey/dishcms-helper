<?php
/** @var \components\base\BaseSectionsController $this */
/** @var array[\common\components\base\ActiveRecord] $sections */
/** @var string $header */
use common\components\helpers\HArray as A;

if(!empty($sections)): ?>
<div class="body__block main-services">
  <div class="main-services__in">
    <div class="main-services__h"><?= $header; ?></div>
    <div class="main-services__content msc">
      <div class="row">
        <?php foreach ($sections as $section): ?>
          <div class="msc__item col-12 col-sm-6 col-lg-3">
            <div class="msci">
			  <?= CHtml::link($section->img(260, 170, true, ['class'=>'msc__img']), $section->getUrl(), ['class'=>'msc__a', 'title'=>$section->title]); ?>
			  <?= CHtml::link($section->title, $section->getUrl(), ['class'=>'msc__h']); ?>
			  <?php if($subsections=$section->getChildrenSections(['select'=>'id, title'])): ?>
				<div class="msc__p msc__p-list"><?
					echo \CHtml::openTag('p');
					// echo \CHtml::openTag('ul');					
					echo array_reduce($subsections, function($html, $subsection) {
						// return $html . \CHtml::tag('li', [], \CHtml::link($subsection->title, $subsection->getUrl(), ['title'=>$subsection->title]));
						return $html . ($html ? ' &bull; ' : '') . \CHtml::link($subsection->title, $subsection->getUrl(), ['title'=>$subsection->title]);
					}, ''); 					
					// echo \CHtml::closeTag('ul');
					echo \CHtml::closeTag('p');
				?></div>
			  <?php endif; ?>
              <div class="msc__p"><?= $section->preview_text; ?></div>
              <div class="rasp"></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>
