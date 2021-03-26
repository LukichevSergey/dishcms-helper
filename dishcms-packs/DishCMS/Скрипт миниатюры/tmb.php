<?
$file=$_SERVER['DOCUMENT_ROOT'] . $slide->src;
if(is_file($file)) {
    $dirname=dirname($file); 
    $tmb='/890x385_'.basename($file);
    if(!is_file($dirname.$tmb)) {
        Yii::app()->ih->load($file)->adaptiveThumb(890, 385)->save($dirname.$tmb);
    }
    $tmb='/images/carousel'.$tmb;
}
?>
