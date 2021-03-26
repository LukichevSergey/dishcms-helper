<?php
if( isset($_SERVER['HTTP_X_REQUESTED_WITH']) ) {
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
    require \Bitrix\Main\Application::getDocumentRoot() . getLocalPath('components/kontur.payments/class.php');
}
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Loader,
Bitrix\Main\Localization\Loc,
Kontur\Core\Main\Tools\Data,
Kontur\Core\Main\Tools\Request;

Loc::loadMessages(__FILE__);
Loader::includeModule("iblock");

/**
 * Validate fields
 * @var callable $validate
 */
$validate=function() {
    $fields=[
        'name'=>'required',
        'amount'=>'amount',
        'phone'=>'phone',
        'email'=>'email',
    ];
    
    $result=['success'=>true];
    foreach($fields as $name=>$rule) {
        $result['values'][$name]=trim(Data::get($_POST, $name));
        $result[$name]=call_user_func_array(['KonturPaymentsComponent', 'validate'], [$result['values'][$name], $rule]);
        $result['success'] = ($result['success'] && $result[$name]);
    }    
    
    return $result;
};

$result=['success'=>false];
switch(Data::get($_POST, 'mode')) {
    case 'validate':
        $result=$validate();
        unset($result['values']);
        break;
        
    case 'payment':
        $valid=$validate();
        if($valid['success']) {
            $arParams=\KonturPaymentsComponent::decrypt(Data::get($_POST, 'params'));
            $params=[
                'IS_TEST_MODE'=>(Data::get($arParams, 'PAYMENT_TEST_MODE', 'N') == 'Y'),
                'IBLOCK_ID'=>Data::get($arParams, 'IBLOCK_ID'),
                'EVENT_TYPE_WAIT'=>Data::get($arParams, 'EVENT_TYPE_WAIT'),
                'EVENT_ID_WAIT'=>Data::get($arParams, 'EVENT_ID_WAIT'),
                'PAYMENT_STATUS_WAIT_ID'=>Data::get($arParams, 'PAYMENT_STATUS_WAIT_ID'),
                'URL'=>Data::get($_POST, 'url'),
                'AMOUNT'=>(float)Data::get($valid['values'], 'amount', 0),
                'NAME'=>Data::get($valid['values'], 'name'),
                'PHONE'=>Data::get($valid['values'], 'phone'),
                'EMAIL'=>Data::get($valid['values'], 'email')
            ];
            
            $result['formUrl'] = \KonturPaymentsComponent::payment($params);
        }
        break;
}

Request::endAjax($result);