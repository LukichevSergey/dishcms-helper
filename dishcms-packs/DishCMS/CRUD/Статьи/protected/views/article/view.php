<?
/** @var SaleController $this */
/** @var Sale $model */ 
?>
<h1><?=$model->getMetaH1()?></h1>
<?=$model->text?>
<?=HtmlHelper::linkBack('Назад', '/stati', '/stati')?>
