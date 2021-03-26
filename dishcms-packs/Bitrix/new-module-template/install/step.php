<?
use Bitrix\Main\Localization\Loc;
 
if(!check_bitrix_sessid()) return;

Loc::loadMessages(__FILE__);

echo CAdminMessage::ShowNote(GetMessage("KONTUR_CORE_INSTALLED"));
?>
