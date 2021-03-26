<?
/** @var \Kontur\Calculator\Component\Calculator $component */
if(!defined('B_PROLOG_INCLUDED')||B_PROLOG_INCLUDED!==true)exit();

use \Bitrix\Main;

defined('VUEJS_DEBUG') or define('VUEJS_DEBUG', true);

\CJSCore::Init(['currency']); 
Main\UI\Extension::load('ui.vue');
Main\Page\Asset::getInstance()->addJs('https://cdn.jsdelivr.net/npm/lodash@4.17.20/lodash.min.js');
Main\Page\Asset::getInstance()->addJs($this->getFolder() . '/vendor/js/utf8_encode.js');
Main\Page\Asset::getInstance()->addJs($this->getFolder() . '/vendor/js/utf8_decode.js');
Main\Page\Asset::getInstance()->addJs($this->getFolder() . '/vendor/js/md5.js');

$applicationId=uniqid('calculator-');
$currencyFormat = CCurrencyLang::GetFormatDescription('RUB');
?>
<div id="<?=$applicationId?>"></div>
<script>
BX.Currency.setCurrencyFormat('RUB', <? echo CUtil::PhpToJSObject($currencyFormat, false, true); ?>);
KonturCalculatorComponent.create(<?=\CUtil::PhpToJSObject([
    'application_id'=>$applicationId,
    'settings_items'=>$arResult['ITEMS'],
    'webform_id'=>8,
    'webform_order_field_name'=>'form_textarea_32',
    'consent_url'=>'/company/consent/'
]); ?>);</script>