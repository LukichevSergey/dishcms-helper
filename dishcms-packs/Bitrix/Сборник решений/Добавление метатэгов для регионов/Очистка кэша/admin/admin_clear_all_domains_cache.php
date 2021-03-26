<? 
/** 
 * /bitrix/admin/admin_clear_all_domains_cache.php
 * require($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/kontur/admin_clear_all_domains_cache.php");
 */
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/prolog.php");
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");

$sites = [
'new_diabazit'=>'Основной сайт diabazit.ru',
'nn.diabazit.ru'=>'Нижний Новгород',
'perm.diabazit.ru'=>'Пермь',
'ufa.diabazit.ru'=>'Уфа',
'tula.diabazit.ru'=>'Тула',
];


if(isset($_REQUEST['RunClearAllCache']) && ($_REQUEST['RunClearAllCache']=='Y')) {
	if(isset($_REQUEST['ClearAllCacheSite'])) {
		if(isset($sites[$_REQUEST['ClearAllCacheSite']])) {
			$clearCacheDir=[$_REQUEST['ClearAllCacheSite']];
		}
	}
	else {
		$clearCacheDir=array_keys($sites);
	}
	if(!empty($clearCacheDir)) {
		$okMessage='';
		foreach($clearCacheDir as $dir) {
			$path='/home/d/diabazitru/'.$dir.'/public_html/bitrix/cache';
			exec('find '.$path.' -type f -exec rm {} \;');
			exec('find '.$path.' -type d -maxdepth 1 -regex "'.$path.'/.+" -exec rm -r {} \;');
			$okMessage.='Очистка кэша завершена' . ($dir ? " (" . $sites[$dir] . ")" : '') . '.'."\n";
		}
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
