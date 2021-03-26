<?
/** @global CMain $APPLICATION */
use Bitrix\Main\Page\Asset;
use kontur\frontpad\FrontPad;

require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/prolog.php");

set_time_limit(0);

$APPLICATION->SetTitle('Импорт товаров и обновление цен из сервиса FrontPad');

require ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

if(isset($_POST['RUN']) && ($_POST['RUN']=='Y')) {
    if(FrontPad::i()->import($_POST['DATA'])) {
        $stats=FrontPad::i()->getImportStats();
        \CAdminMessage::ShowMessage([
            'TYPE'=>'OK', 
            'MESSAGE'=>"Процесс иморта товаров успешно завершен
                Добавлено: {$stats->added}
                Обновлено: {$stats->updated} из {$stats->total}
                Обновлено цен: {$stats->price_updated}"
        ]);
    }
}

Asset::getInstance()->addCss(FrontPad::i()->getAdminAssetsUrl() . '/css/admin.css');
Asset::getInstance()->addJs(FrontPad::i()->getAdminAssetsUrl() . '/js/admin.js');
?>
<link rel="stylesheet" type="text/css" href="<?= FrontPad::i()->getAdminAssetsUrl(); ?>/css/admin.css">
<div class="form frontpad__form">
    <form method="post">
        <input type="hidden" name="RUN" value="Y" />
        <table border="0">
            <tr>
                <td width="49%" valign="top"><? 
                    include dirname(__FILE__) . '/tabs/site.php'; 
                ?></td>
                <td width="1%" sstyle="background:#d1dadd;">
                    &nbsp;
                </td>
                <td width="49%" valign="top"><? 
                    include dirname(__FILE__) . '/tabs/frontpad.php'; 
                ?></td>
            </tr>
        </table>
        <br/>
        <input type="submit" class="adm-btn-save" value="Импортировать / Обновить" />
    </form>
</div>

<?require ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
