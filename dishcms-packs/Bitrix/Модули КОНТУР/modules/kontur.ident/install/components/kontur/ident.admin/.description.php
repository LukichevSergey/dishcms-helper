<?php if(!defined('B_PROLOG_INCLUDED')||B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = [
    'NAME' => Loc::getMessage('KONTUR_IDENT_COMPONENT_NAME'),
    'DESCRIPTION' => Loc::getMessage('KONTUR_IDENT_COMPONENT_DESCRIPTION'),
];
