<?php
/** @var \buildings\controllers\DefaultController $this */
use common\components\helpers\HYii as Y;
use extend\modules\buildings\models\Floor;
use extend\modules\buildings\components\helpers\HBuildings;

$t=Y::ct('\extend\modules\buildings\BuildingsModule.common', 'extend.buildings');

$floors = Floor::model()->published()->findAll(['select'=>'id, map_hash']);
if($floors) {
    $floorsHashes = [];
    foreach($floors as $floor) {
        if($floor->map_hash) $floorsHashes[$floor->map_hash] = $this->createUrl('/buildings/floor', ['id'=>$floor->id]);
    }
    Y::module('common')->publishJs(['js/php/utf8_encode.js', 'js/php/md5.js']);
    Y::js(false,
    ';(function(){
        var floors = '.json_encode($floorsHashes).';
        var pathAll = ".buildings__facade-svg svg g";
        var path = ".buildings__facade-svg svg g[data-available=1]";
        var attr = "id";
        var tag = "g";        
        $(pathAll).each(function(){if(!$(this).find(tag).length) $(this).animate({opacity: 0},0);});
        for(var hash in floors) {
            $(pathAll).each(function(){
                if((hash == md5($(this).attr(attr)))) {
                    $(this).css("cursor", "pointer");
                    $(this).attr("onclick", "window.location.href=\'"+floors[hash]+"\'");
                    $(this).attr("data-available", "1");
                }
            });
        }
        $(pathAll).each(function(){if(!$(this).find(tag).length && $(this).is("[data-available=1]")){$(this).animate({opacity: 0.4},0);}});        
        $(document).on("mouseover", path, function(e){$(e.target).closest(tag).animate({opacity: 0.6},50);});
        $(document).on("mouseout", path, function(e){$(e.target).closest(tag).animate({opacity: 0.4},50);});        
    })();',
    \CClientScript::POS_READY
    );
}
?>
<h1><?= $this->getHomeTitle(); ?></h1>

<div class="buildings__text"><?= HBuildings::settings()->text; ?></div>

<?php if(HBuildings::settings()->imageBehavior->exists() && HBuildings::settings()->svgBehavior->exists()): ?>
<div class="buildings__facade" style="position:relative;width:970px;min-height:300px;margin:20px 0;">
	<?= HBuildings::settings()->imageBehavior->img(970, 300, false, ['style'=>'position:absolute;left:0;top:0;']); ?>
	<?php if($floors): ?>
	<div class="buildings__facade-svg" style="position:absolute;top:0;left:0"><?php 
	   echo file_get_contents(HBuildings::settings()->svgBehavior->getFilename(true));
	?></div>
	<?php endif; ?>
</div>
<?php endif; ?>

<div class="buildings__text_bottom"><?= HBuildings::settings()->text_bottom; ?></div>
