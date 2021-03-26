<?
	$menu=$this->menu;
    foreach($menu as $i=>$item) {
    if(isset($item['url']) && is_string($item['url']) && preg_match('#^(http|https)://#', $item['url'])) {
        $menu[$i]['template']='<noindex>{menu}</noindex>';
        $menu[$i]['linkOptions']['rel']='nofollow';
    }
}
?>
<?php $this->widget('zii.widgets.CMenu', array(
    'items'=>$menu,
    'htmlOptions'=>array('class'=>'menu clearfix'),
)); ?>

<? // ------------------------------------------------------------------------ ?>

<?
// /protected/modules/menu/widgets/menu/BaseMenuWidget.php
$htmlOptions=array('title'=>$item['model']->seo_a_title);

$noindex=preg_match('#^(http|https)://#', $url);
if($noindex) $htmlOptions['rel']='nofollow';
$html .= ($noindex?'<noindex>':'')
    .\CHtml::link('<span>'.$item['model']->title.'</span>', $url, $htmlOptions);
    .($noindex?'</noindex>':'')
