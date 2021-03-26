<? 
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if(!Loader::includeModule("iblock")) {
    return;
}

$arServices = [
    'iblock' => [
        'NAME' => Loc::getMessage('wizard.services.iblock'),
        'STAGES' => [
            'calculator.php',
            'calculator_items.php',
            'calculator_requests.php'
        ]
    ],
];

