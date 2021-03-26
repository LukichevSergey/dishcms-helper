<?php
use common\components\helpers\HHtml;

$apartments = $floor->getRelated('apartments', true, [
    'select'=>'`t`.`id`, `t`.`floor_id`, `t`.`title`, `t`.`published`, `t`.`image`, `t`.`sold`, `t`.`price`, `t`.`sale_price`, `t`.`area`, `t`.`props`',
    'order'=>'title, id'
    //'scopes'=>['scopeSort'=>['buildings_apartments', $floor->id, false, 'apartments']]
]);

if (empty($apartments)):
?>Нет квартир<?php
else:
    ?>
    <div class="row">
    	<div class="col-md-12">
    		<div class="panel-group" id="panel__apartments-<?= $floor->id; ?>">
            	<?php foreach($apartments as $apartmentIdx=>$apartment): ?>
          			<div class="panel panel-default" style="margin-bottom:20px">
            			<div class="panel-heading" style="min-height:40px">
              				<h4 class="panel-title pull-left">
                      			<a data-toggle="collapse" ddata-parent="#panel__apartments-<?= $floor->id; ?>" href="#apartment_<?= $apartment->id; ?>"><?php
                      			echo 'Квартира: ' . $apartment->title;
                      			?></a>
                    		</h4>
                    		<?= \CHtml::tag(
            				    'span', 
            				    ['class'=>'pull-left label label-' . ($apartment->sold ? 'success' : 'default'), 'style'=>'margin-left:10px'],
            				    ($apartment->sold ? 'продана' : 'свободна')
            				); ?>
            				<?= \CHtml::tag(
            				    'span', 
            				    ['class'=>'pull-left label label-' . ($apartment->published ? 'success' : 'danger'), 'style'=>'margin-left:10px'],
            				    ($apartment->published ? 'отображается на сайте' : 'скрыта на сайте')
            				); ?>
            				<?= \CHtml::link(
                      		    'Редактировать', 
            				    ['/cp/crud/update', 'cid'=>'buildings_apartments', 'floor'=>$floor->id, 'id'=>$apartment->id], 
                      		    ['class'=>'btn btn-xs btn-info pull-right']
                      		); ?>
            			</div>
                		<div id="apartment_<?= $apartment->id; ?>" class="panel-collapse collapse">
              				<div class="panel-body">
              					<div class="row">
              						<div class="col-md-4">
              						<?php if($apartment->imageBehavior->exists()): ?>
              							<?= \CHtml::link(\CHtml::image($apartment->imageBehavior->getTmbSrc(250, 250)), $apartment->imageBehavior->getSrc(), ['data-fancybox'=>$apartment->id]); ?>
              						<?php else: ?>
              							<?= \CHtml::image($apartment->imageBehavior->getTmbSrc(250, 250)); ?>
              						<?php endif; ?>
              						</div>
              						<div class="col-md-8">
                      					<table class="table table-bordered">
                      						<tr>
                      							<td>Общая площадь</td><td><?= $apartment->area?: 0 ?> м<sup>2</sup></td>
                      						</tr>
                      						<tr>
                      							<td>Цена</td><td><?= HHtml::price($apartment->price) ?> руб.</td>
                      						</tr>
                      						<tr>
                      							<td>Цена по акции</td><td><?= HHtml::price($apartment->sale_price) ?> руб.</td>
                      						</tr>
                      						<?php 
                      						foreach($apartment->propsBehavior->get(true) as $prop):
                      						    if(empty($prop['title'])) continue;
                      						?>
                      						<tr>
                      							<td><?= $prop['title']; ?></td><td><?= $prop['value']; ?> <?= $prop['unit']; ?></td>
                      						</tr>
                      						<?php 
                      						endforeach; 
                      						?>
                      					</table>
                      				</div>
              					</div>
              				</div>
    	        		</div>
          			</div>
    			<?php endforeach; ?>
    		</div>
    	</div>
    </div>
    <?php 
endif;