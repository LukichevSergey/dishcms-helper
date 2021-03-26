<?
use Bitrix\Main\Localization\Loc;

if(!check_bitrix_sessid()) return;

Loc::loadMessages(__FILE__);

if($errorException = $APPLICATION->GetException()) {
    echo CAdminMessage::ShowMessage($errorException->GetString());
} else {
    echo CAdminMessage::ShowNote(Loc::getMessage('KONTUR_CHECK_PRICE_UNSTEP_UNINSTALLED'));
}
?>
<form action="<?= $APPLICATION->GetCurPage(); ?>">
  <input type="hidden" name="lang" value="<?= LANG; ?>" />
  <input type="submit" value="<?= Loc::getMessage('KONTUR_CHECK_PRICE_UNSTEP_SUBMIT_BACK'); ?>">
</form>