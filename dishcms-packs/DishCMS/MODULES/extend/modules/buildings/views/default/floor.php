<?php
/** @var \extend\modules\buildings\models\Floor $model */
use common\components\helpers\HYii as Y;
use extend\modules\buildings\models\Floor;
use extend\modules\buildings\components\helpers\HBuildings;

$apartments = $model->getRelated('apartments', true, ['select'=>'id, map_hash, sold', 'scopes'=>'published']);
if($apartments) {
    $apartmentsHashes = [];
    foreach($apartments as $apartment) {
        if($apartment->map_hash) {
            if($apartment->sold) {
                $apartmentsHashes[$apartment->map_hash] = '';
            }
            else {
                $apartmentsHashes[$apartment->map_hash] = $this->createUrl('/buildings/apartment', ['id'=>$apartment->id]);
            }
        }
    }
    Y::module('common')->publishJs(['js/php/utf8_encode.js', 'js/php/md5.js']);
    Y::js(false,
        ';(function(){
        var apartments = '.json_encode($apartmentsHashes).';
        var pathAll = ".buildings__floor-svg svg path";
        var path = ".buildings__floor-svg svg path[data-available=1]";
        var attr = "d";
        var tag = "path";
        $(pathAll).each(function(){if(!$(this).find(tag).length) $(this).animate({opacity: 0},0);});
        for(var hash in apartments) {
            $(pathAll).each(function(){
                if((hash == md5($(this).attr(attr)))) {
                    if(!apartments[hash].length) {
                        $(this).css("fill", "#2b2b2b");
                        $(this).animate({opacity: 0.5},0);
                    }
                    else {
                        $(this).css("cursor", "pointer");
                        $(this).attr("onclick", "window.location.href=\'"+apartments[hash]+"\'");
                        $(this).attr("data-available", "1");
                    }   
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
<h1><?= $this->pageTitle; ?></h1>

<?php if($model->imageBehavior->exists() && $model->svgBehavior->exists()): ?>
<div class="buildings__floor-note">
	<div><span style="background-color:#2b2b2b;opacity:0.5;"></span><i>- продано</i></div>
	<div><span style="background-color:#56c529;opacity:0.4;"></span><i>- свободно</i></div>
</div>
<div class="buildings__floor" style="position:relative;width:750px;min-height:520px;margin:20px 0;">
	<?= $model->imageBehavior->img(750, 500, false, ['style'=>'position:absolute;left:0;top:0;']); ?>
	<?php if($apartments): ?>
	<div class="buildings__floor-svg" style="position:absolute;top:0;left:0"><?php 
	   echo file_get_contents($model->svgBehavior->getFilename(true));
	?></div>
	<?php endif; ?>
</div>
<?php endif; ?>

<div class="floor__description"><?php
    echo $model->text;
?></div>