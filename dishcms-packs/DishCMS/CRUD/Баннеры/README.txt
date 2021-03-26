------------------------------------------------
Подключить конфигурацию и добавить пункт меню
------------------------------------------------
/protected/config/crud.php
'banners'=>'application.config.crud.banners',

/protected/modules/admin/config/menu.php
use crud\components\helpers\HCrud;

HCrud::getMenuItems(Y::controller(), 'banners', 'crud/index', true)

------------------------------------------------
Отображение списка баннеров
------------------------------------------------

<?
if($banners=Banner::model()->activly()->scopeSort('banners')->findAll()):
	?><div class="banners__block"><?
	foreach($banners as $banner):
		if($banner->imageBehavior->exists()):
		?><div class="banner__item" data-id="<?=$banner->id?>">
			<? if($banner->link): ?><a href="<?=$banner->link?>"><? endif; ?>
				<?= $banner->imageBehavior->img(240, false, true, ['title'=>$banner->title, 'alt'=>$banner->title]); ?>
			<? if($banner->link): ?></a><? endif; ?>
		</div><?
		endif;
	endforeach;
	?></div><?
endif;
?>
