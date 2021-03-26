<? 
use common\components\helpers\HHtml;
if($dataProvider->totalItemCount): 
	?><div class="site-bar-list row"><?
		foreach($dataProvider->getData() as $data): 
			?><div class="site-bar-item col-sm-3">
				<a href="<?=$this->owner->createUrl('shop/filter', ['filter'=>['rangeof'=>$data->sef]])?>">				
					<div class="sv-images"><? 
					if($data->previewImageBehavior->isEnabled()):
						echo $data->previewImageBehavior->img(260, 115, true, ['class'=>'img-responsive']);
					else:
						echo HHtml::phImg(['w'=>260,'h'=>115,'t'=>'','bg'=>'22b3e0']); 
					endif; ?></div>
					<span class="sb-text-block">
						<span class="sb-text-row">
							<p><?= $data->title; ?></p>
						</span>
					</span>
				</a>
			</div><? 
		endforeach; 
	?></div><? 
endif; 
?>