<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

global $DB;

$iblockTypeId = \CIBlockType::GetByID('calculator')->Fetch() ?  uniqid('calculator_') : 'calculator';
$fields=[
    'ID'=>$iblockTypeId,
    'SECTIONS'=>'N',
    'IN_RSS'=>'N',
    'SORT'=>900,
    'LANG'=>[
        'ru'=>[
            'NAME'=>'Калькулятор',
            'ELEMENT_NAME'=>'Элементы'
        ],
        'en'=>[
            'NAME'=>'Calculator',
            'ELEMENT_NAME'=>'Elements'
        ]
    ]
];
        
$DB->StartTransaction();
$iblockType = new \CIBlockType;
if(!$iblockType->Add($fields)) {
    $DB->Rollback();
    echo 'Error: '.$iblockType->LAST_ERROR.'<br>';
}
else {
    $DB->Commit();
    $GLOBALS['KONTUR_CALCULATOR_IBLOCK_TYPE_ID'] = $iblockTypeId;
}
