<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (empty($arResult)) return;?>

<?
$prevDepthLevel=1;
$i=0;
?>
<? foreach($arResult as $arItem): ?>
    <?if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1) continue;?>
    <?
    if($arItem["DEPTH_LEVEL"] > $prevDepthLevel) echo '<ul>';
    elseif($arItem["DEPTH_LEVEL"] < $prevDepthLevel) echo '</ul></li>';
    elseif($i) echo '</li>';
    $i++;
    ?>
    <?if($arItem["SELECTED"]){?>
        <li><a href="<?=$arItem["LINK"]?>" class="active"><?=$arItem["TEXT"]?></a>
    <?}else{?>
        <li><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
    <?}?>
    <? $prevDepthLevel=$arItem["DEPTH_LEVEL"]; ?>
<? endforeach ?>
<? if($prevDepthLevel > 1) echo str_repeat('</ul>', $prevDepthLevel-1); ?>
</li>
