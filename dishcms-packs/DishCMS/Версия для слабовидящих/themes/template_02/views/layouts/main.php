<?use common\components\helpers\HYii as Y;?>
<!DOCTYPE html>
<html>
<head>
	<?php
	CmsHtml::head();
	CmsHtml::js('/js/main.js');
	CmsHtml::js($this->template . '/js/scripts.js');
	CmsHtml::js($this->template . '/js/magnific-popup.js');
	Yii::app()->clientScript->registerCoreScript('cookie');
	CmsHtml::js($this->template.'/js/menu/script.js');
	Yii::app()->clientScript->registerScriptFile('/js/jquery.mmenu.all.js');
?>

<!--[if IE]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

</head>

<body class="<?=D::c($this->isIndex(), 'index_page')?>">
<div class="page_wrap">
	<div>
		<section class="top-panel">
			<div class="font-size">
				<span>Размер шрифта:</span>
				<a href="javascript:;" class="size normal-size active" data-size="16">a</a>
				<a href="javascript:;" class="size middle-size" data-size="18">a</a>
				<a href="javascript:;" class="size big-size" data-size="20">a</a>
			</div>
			<div class="site-color">
				<span>Цвет сайта:</span>
				<a href="javascript:;" class="color white-color active" data-color="white">ц</a>
				<a href="javascript:;" class="color black-color" data-color="black">ц</a>
				<a href="javascript:;" class="color blue-color" data-color="blue">ц</a>
			</div>
			<div class="real-version">
				<a href="<?= Yii::app()->createUrl('site/change'); ?>">Обычная версия сайта</a>
			</div>
		</section>
	</div>
	<div class="menu_block">
		<section>
			<nav>
				<?php
                    $this->widget('\menu\widgets\menu\MenuWidget', array(
                        'rootLimit' => D::cms('menu_limit'),
                        'cssClass' => 'menu'
                    ));
                ?>
			</nav>
		</section>
	</div>
	<div class="menu_block-sub">
		<section>
			<nav>
				<?php
                    $this->widget('\menu\widgets\menu\MenuWidget', array(
                        'rootId' => 8,
                        'cssClass' => 'menu'
                    ));
                ?>
			</nav>
		</section>
	</div>
	<header>
		<section>
			<div class="header">
				<div class="logo left">
					<a href="/">
						<?=D::cms('slogan')?>
					</a>
				</div>
				<div class="phone_top right">
					<p><?=D::cms('phone')?></p>
					<p><?=D::cms('phone2')?></p>
				</div>
				<div class="address_top right">
					<?=D::cms('address'); ?>
				</div>
			</div>
		</section>
	</header>
	<div class="info_block">
		<section>
			<div class="sliderBlock">
				<?//$this->widget('widget.SiteSlider.SiteSlider', array('type'=>Slide::TYPE_SLIDER))?>
			</div>
		</section>
	</div>
	<div class="content_block">
		<section>
			<div class="main main_pad clearfix">
				<?=$content?>
			</div>
		</section>
	</div>
</div>
<div class="footer_block">
	<section>
		<footer>
			<div class="name left">
				<?ModuleHelper::Copyright()?>
				<div class="ya_m"><?=D::cms('counter')?></div>
			</div>
			<div class="address left">
				<?=D::cms('address'); ?>
				<p>тел.: <?=D::cms('phone'); ?></p>
				<p>e-mail: <a href="mailto:<?=D::cms('emailPublic'); ?>"><?=D::cms('emailPublic'); ?></a></p>
				<!-- <p><?=D::cms('phone2'); ?></p> -->
			</div>
			<div class="made right">
				<div class="creation">
					<a href="http://kontur-lite.ru" title="Создание сайтов в Новосибирске" target="_blank">
					Создание сайтов —
					<?include(Yii::app()->theme->getBasePath().'/images/svg/made.svg')?>
						<?/*<img src="<?php echo $this->template; ?>/images/made.png" alt="Kontur-lite.ru" />*/?>
					</a>
				</div>
				<div class="promotion">
					<a href="http://kontur-promo.ru" title="Продвижение сайтов в Новосибирске" target="_blank">
						Продвижение сайтов —
						<?include(Yii::app()->theme->getBasePath().'/images/svg/promotion.svg')?>
						<?/*<img src="<?php echo $this->template; ?>/images/promotion.png" alt="Kontur-promo.ru" />*/?>
					</a>
				</div>
			</div>
		</footer>
	</section>
</div>
	<div id="totop" ><p>&#xe845;</p> ^ Наверх </div>
</body>
</html>
