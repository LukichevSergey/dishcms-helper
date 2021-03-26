<?php
use extend\modules\buildings\models\Porch;

$porches = Porch::model()->findAll(['order'=>'number, title']/*['scopes'=>['scopeSort'=>'buildings_porches']]*/);
?>
<div class="row">
	<div class="col-md-12">
		<?= \CHtml::link('Подъезды', ['/cp/crud/index', 'cid'=>'buildings_porches'], ['class'=>'btn btn-info', 'style'=>'margin-bottom:20px']); ?>
		<?= \CHtml::link('Добавить подъезд', ['/cp/crud/create', 'cid'=>'buildings_porches'], ['class'=>'btn btn-primary', 'style'=>'margin-bottom:20px']); ?>
		<?= CHtml::link('<i class="glyphicon glyphicon-cog"></i>Настройки', '/cp/settings/buildings', ['class'=>'btn btn-warning pull-right']); ?>
		<div class="panel-group" id="panel__porches">
        	<?php foreach($porches as $porchIdx=>$porch): ?>
      			<div class="panel panel-default" style="margin-bottom:20px">
        			<div class="panel-heading" style="min-height:40px">
          				<h4 class="panel-title pull-left">
                  			<a data-toggle="collapse" ddata-parent="#panel__porches" href="#porch__<?= $porch->id; ?>"><?php
                  			   echo $porch->getNumberTitle();
                  			   if($porch->title) {
                  			       echo " ({$porch->title})";
                  			   }
                  			?></a>
                		</h4>
        				<?= \CHtml::tag(
        				    'span', 
        				    ['class'=>'pull-left label label-' . ($porch->published ? 'success' : 'danger'), 'style'=>'margin-left:10px'],
        				    ($porch->published ? 'отображается на сайте' : 'скрыт на сайте')
        				); ?>
        				<?= \CHtml::link(
        				    '<span class="glyphicon glyphicon-home"></span>',
        				    ['/cp/crud/index', 'cid'=>'buildings_floors', 'porch'=>$porch->id],
        				    ['title'=>'Этажи', 'class'=>'btn btn-warning btn-xs pull-right', 'style'=>'margin-left:5px']
        				); ?>
                  		<?= \CHtml::link(
                  		    'Добавить этаж', 
                  		    ['/cp/crud/create', 'cid'=>'buildings_floors', 'porch'=>$porch->id], 
                  		    ['class'=>'btn btn-xs btn-primary pull-right', 'style'=>'margin-left:5px']
                  		); ?>
        				<?= \CHtml::link(
                  		    'Редактировать', 
                  		    ['/cp/crud/update', 'cid'=>'buildings_porches', 'id'=>$porch->id], 
                  		    ['class'=>'btn btn-xs btn-info pull-right']
                  		); ?>
        			</div>
            		<div id="porch__<?= $porch->id; ?>" class="panel-collapse collapse<?php if(true || !$porchIdx) echo ' in';?>">
          				<div class="panel-body">
          					<?php $this->renderPartial('extend.modules.buildings.modules.admin.views.default._floors', compact('porch')); ?>
          				</div>
	        		</div>
      			</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>