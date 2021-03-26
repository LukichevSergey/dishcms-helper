<? 
/** 
 * /bitrix/admin/admin_clear_all_domains_cache.php
 * require($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/kontur/admin_clear_all_domains_cache.php");
 */
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/prolog.php");
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");

$sites = [
'httpdocs'=>'Основной сайт www.enetra.ru',
'abakan.enetra.ru'=>'Абакан',
'barnaul.enetra.ru'=>'Барнаул',
'bratsk.enetra.ru'=>'Братск',
'vladivostok.enetra.ru'=>'Владивосток',
'irkutsk.enetra.ru'=>'Иркутск',
'kemerovo.enetra.ru'=>'Кемерово',
'krasnoyarsk.enetra.ru'=>'Красноярск',
'magadan.enetra.ru'=>'Магадан',
'novokuznetsk.enetra.ru'=>'Новокузнецк',
'pkamch.enetra.ru'=>'Петропавловск-Камчатский',
'tomsk.enetra.ru'=>'Томск',
'tuva.enetra.ru'=>'Тува',
'ulan-ude.enetra.ru'=>'Улан-Удэ',
'chita.enetra.ru'=>'Чита',
'habarovsk.enetra.ru'=>'Хабаровск',
'u-sahalinsk.enetra.ru'=>'Южно-Сахалинск',
'yakutsk.enetra.ru'=>'Якутск'
];


if(isset($_REQUEST['RunClearAllCache']) && ($_REQUEST['RunClearAllCache']=='Y')) {
	if(isset($_REQUEST['ClearAllCacheSite'])) {
		if(isset($sites[$_REQUEST['ClearAllCacheSite']])) {
			$clearCacheDir=$_REQUEST['ClearAllCacheSite'];
		}
	}
	else {
		$clearCacheDir='*';
	}
	if(!empty($clearCacheDir)) {
		exec('find /var/www/vhosts/enetra.ru/'.$clearCacheDir.'/bitrix/cache -type f -exec rm {} \;');
		$okMessage='Очистка кэша завершена' . ($clearCacheDir ? " (" . $sites[$clearCacheDir] . ")" : '') . '.';
	}
}
?>
<h2>Очистка кэша все доменов/регионов, включая основной</h2>
<?if(!empty($okMessage)){?>
<?= CAdminMessage::ShowNote($okMessage);?>
<?}?>
<form>
	<input type="hidden" name="RunClearAllCache" value="Y" />
	<input type="submit" name="save" value="Очистить кэш всех доменов" title="Запустить процесс очистки кэша для всех доменов" class="adm-btn-save" />
</form>
<br/>
<br/>
Или выберите нужны сайт:
<form>
	<input type="hidden" name="RunClearAllCache" value="Y" />
	<select name="ClearAllCacheSite"><?foreach($sites as $site=>$title){?>
		<option value="<?=$site?>"<? if(!empty($_REQUEST['ClearAllCacheSite']) && ($_REQUEST['ClearAllCacheSite']==$site)) echo ' selected="selected"';?>><?=$title;?></option>
	<?}?></select>
	<input type="submit" name="save" value="Очистить кэш" title="Запустить процесс очистки кэша для всех доменов" class="adm-btn-save" />
</form>

<?
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/bitrix/modules/main/include/epilog_admin.php");
