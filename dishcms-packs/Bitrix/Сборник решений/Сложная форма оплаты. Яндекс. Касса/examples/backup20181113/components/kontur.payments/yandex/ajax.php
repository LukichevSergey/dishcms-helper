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
$fValidate=function() {
    $fields=[
        'promocode'=>'safe',
        'name'=>'required',
        'phone'=>'phone',
        'email'=>'email',
        'passport_number'=>'required',
        'passport_org'=>'required',
        'passport_date'=>'date',
        'passport_address'=>'required',
        'date'=>'date',
        'agree'=>'required',
        'creditors'=>'required'
    ];
    
    $result=['success'=>true];
    foreach($fields as $name=>$rule) {
        $value=Data::get($_POST, $name);
        if(is_array($value)) {
            foreach($value as $idx=>$val) {
                if(is_array($val)) {
                    foreach($val as $idx2=>$val2) {
                        $result[$name][$idx][$idx2]=call_user_func_array(['KonturPaymentsComponent', 'validate'], [$val2, $rule]);
                        $result['values'][$name][$idx][$idx2]=$val2;
                        $result['success'] = ($result['success'] && $result[$name][$idx][$idx2]);
                    }
                }
                else {
                    $result[$name][$idx]=call_user_func_array(['KonturPaymentsComponent', 'validate'], [$val, $rule]);
                    $result['values'][$name][$idx]=$val;
                    $result['success'] = ($result['success'] && $result[$name][$idx]);
                }
            }
        }
        else {
            $result['values'][$name]=trim($value);
            $result[$name]=call_user_func_array(['KonturPaymentsComponent', 'validate'], [$result['values'][$name], $rule]);
            $result['success'] = ($result['success'] && $result[$name]);
        }
    }    
    
    return $result;
};

$fCheckPromocode=function($promocode) {
    $arParams=\KonturPaymentsComponent::decrypt(Data::get($_POST, 'params'));
    $rs=\CIBlockElement::GetList(
        ['NAME'=>'ASC'], 
        ['IBLOCK_ID'=>$arParams['PROMOCODE_IBLOCK_ID'], 'ACTIVE'=>'Y', 'PROPERTY_PROMOCODE'=>$promocode], 
        false, 
        false, 
        ['ID']
    );
    return !!$rs->Fetch();
};

$result=['success'=>false];
switch(Data::get($_POST, 'mode')) {
    case 'promocode':
        $result['success']=$fCheckPromocode(trim(Data::get($_POST, 'promocode')));
        break;
        
    case 'validate':
        $result=$fValidate();
        unset($result['values']);
        break;
        
    case 'payment':
        $valid=$fValidate();
        if($valid['success']) {
            $arParams=\KonturPaymentsComponent::decrypt(Data::get($_POST, 'params'));
            
            $priceDefault=(int)Data::get($arParams, 'PRICE_DEFAULT', 0);
            $pricePromocode=(int)Data::get($arParams, 'PRICE_PROMOCODE', 0);
            $priceCreditor=(int)Data::get($arParams, 'PRICE_CREDITOR', 0);
            
            $promocode=trim(Data::get($_POST, 'promocode'));            
            $price=$fCheckPromocode($promocode) ? $pricePromocode : $priceDefault;
            
            $creditors=Data::get($_POST, 'creditors');
            if(count($creditors) > 1) {
                $price += ($priceCreditor * (count($creditors) - 1));  
            }
            $data=$valid['values'];
            $data['amount']=$price;
            
            $result['formUrl'] = \KonturPaymentsComponent::ypayment($arParams, $data);
        }
        break;
}

Request::endAjax($result);