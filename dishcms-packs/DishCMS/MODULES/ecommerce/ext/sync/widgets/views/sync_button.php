<?php
/** @var \ecommerce\ext\sync\widgets\Sync $this */

?>
<div class="sync__wrapper js-sync-catalog pull-right" style="margin-left:5px;position:relative;">
    <?= CHtml::link(
        'Синхронизовать с основным сайтом <i class="glyphicon glyphicon-transfer"></i>', 
        'javascript:;', 
        ['class'=>'btn btn-info', 'data-loading-text'=>'Идет синхронизация...', 'style'=>'width:280px']
    );
    ?>
    <div class="progress progress-striped active" style="display:none;position:absolute;width:100%;height:10px;z-index:99;margin-top:2px">
    	<div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">
      	</div>
    </div>
</div>