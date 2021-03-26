<h1><?=$page->getMetaH1()?></h1>

<?=$page->text?>

<?if($page->blog && $page->blog->id) {
	$url=$this->createUrl('site/blog', array('id'=>$page->blog->id));
	echo HtmlHelper::linkBack('Назад', $url, $url);
}?>
