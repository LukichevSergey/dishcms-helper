<?php
$floors = $porch->getRelated('floors', true, [
    'select'=>'`t`.`id`, `t`.`porch_id`, `t`.`number`, `t`.`title`, `t`.`published`',
    'order'=>'number, title'
    //'scopes'=>['scopeSort'=>['buildings_floors', $porch->id, false, 'floors']]
]);

if (empty($floors)):
    ?>Нет этажей<?php 
else:
    ?>
    <div class="row">
    	<div class="col-md-12">
    		<div class="panel-group" id="panel__floors-<?= $porch->id; ?>">
            	<?php foreach($floors as $floorIdx=>$floor): ?>
          			<div class="panel panel-info" style="margin-bottom:20px">
            			<div class="panel-heading" style="min-height:40px">
              				<h4 class="panel-title pull-left">
                      			<a data-toggle="collapse" ddata-parent="#panel__floors-<?= $porch->id; ?>" href="#floor_<?= $floor->id; ?>"><?php
                      			echo $floor->getNumberTitle();
                      			if($floor->title) {
                      			    echo " ({$floor->title})";
                      			}
                      			?></a>
                    		</h4>
            				<?= \CHtml::tag(
            				    'span', 
            				    ['class'=>'pull-left label label-' . ($floor->published ? 'success' : 'danger'), 'style'=>'margin-left:10px'],
            				    ($floor->published ? 'отображается на сайте' : 'скрыт на сайте')
            				); ?>
            				<?= \CHtml::link(
            				    '<span class="glyphicon glyphicon-home"></span>',
            				    ['/cp/crud/index', 'cid'=>'buildings_apartments', 'floor'=>$floor->id],
            				    ['title'=>'Квартиры', 'class'=>'btn btn-warning btn-xs pull-right', 'style'=>'margin-left:5px']
            				); ?>
                      		<?= \CHtml::link(
                      		    'Добавить квартиру', 
                      		    ['/cp/crud/create', 'cid'=>'buildings_apartments', 'floor'=>$floor->id], 
                      		    ['class'=>'btn btn-xs btn-primary pull-right', 'style'=>'margin-left:5px']
                      		); ?>
            				<?= \CHtml::link(
                      		    'Редактировать', 
            				    ['/cp/crud/update', 'cid'=>'buildings_floors', 'porch'=>$porch->id, 'id'=>$floor->id], 
                      		    ['class'=>'btn btn-xs btn-info pull-right']
                      		); ?>
            			</div>
                		<div id="floor_<?= $floor->id; ?>" class="panel-collapse collapse<?php if(true || !$floorIdx) echo ' in';?>">
              				<div class="panel-body">
              					<?php $this->renderPartial('extend.modules.buildings.modules.admin.views.default._apartments', compact('floor')); ?>
              				</div>
    	        		</div>
          			</div>
    			<?php endforeach; ?>
    		</div>
    	</div>
    </div>
    <?php 
endif;