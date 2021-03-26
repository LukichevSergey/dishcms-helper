<?php
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/prolog.php");

use \kontur\CatalogDynPropertiesParser as Parser;

$APPLICATION->SetTitle(Parser::c('Загрузка свойств товаров'));

require ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

if(isset($_POST['RUN']) && ($_POST['RUN'] === 'Y')) {
    Parser::run();
    \CAdminMessage::ShowMessage([
        'TYPE'=>'OK',
        'MESSAGE'=>Parser::c('Свойства товаров успешно обновлены')
    ]);
    ?><? /* ?>
	<form action="/bitrix/admin/iblock_reindex_admin.php?lang=ru">    
    	<input type="submit" class="adm-btn-save" value="<?= Parser::c('Перейти к пересозданию фасетного индекса'); ?>">
    </form><? /**/ ?>
    <br/>    
    <a href="<?= $_SERVER['REQUEST_URI']?>"><?= Parser::c('Вернуться и повторить выгрузку')?></a>
    <?php 
}
else {
?>
<div class="form">
    <form method="post">
        <input type="hidden" name="RUN" value="Y" />        
		<input type="submit" class="adm-btn-save" value="<?= Parser::c('Загрузить свойства товаров'); ?>" onclick="BX.style(this,'display','none');BX.style(BX('jsparsernote'),'display','block');" />
		<p id="jsparsernote" style="display:none"><?= Parser::c('Идет выгрузка свойств товара. Пожалуйста, дождитесь завершения процесса...'); ?></p>
	</form>
</div>
<?php 
} 
?>
<?require ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
