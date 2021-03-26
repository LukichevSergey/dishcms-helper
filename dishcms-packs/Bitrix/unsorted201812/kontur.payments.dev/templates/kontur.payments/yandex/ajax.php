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
	if(empty($promocode)) return false;
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

$fDaDataSend=function($url, $data, $httpRequestType='POST') {
    $options=[
        CURLOPT_RETURNTRANSFER=>true,
        CURLOPT_CUSTOMREQUEST=>$httpRequestType,
        CURLOPT_HTTPHEADER=>[
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Token bb82b6ec5c2a38a963a6b3de533ecc74efb45267'
        ],
    ];
        
    if($httpRequestType == 'POST') {
	    $options[CURLOPT_POSTFIELDS]=json_encode($data);
    }
    else {
		$url .= ((strpos($url, '?') !== false) ? '&' : '?') . http_build_query($data);
	}

    $ch=curl_init($url);
    curl_setopt_array($ch, $options);
    $result=curl_exec($ch);
    curl_close($ch);
    
    return @json_decode($result, true);
};

$fDaDadaGetAddresses=function($address) use ($fDaDataSend) {
	$result=[];
	$daDadaResult=$fDaDataSend('https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address', ['query'=>$address, 'count'=>10]);
	if(!empty($daDadaResult['suggestions'])) {
		foreach($daDadaResult['suggestions'] as $suggestion) {
			if(!empty($suggestion['value'])) {
				$result[]=$suggestion['value'];
			}
		}
	}
	return $result;
};

$fDaDadaGetCompanyAddresses=function($query) use ($fDaDataSend) {
	$result=[];
	$daDadaResult=$fDaDataSend('https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/party', ['query'=>$query, 'count'=>20]);
	if(!empty($daDadaResult['suggestions'])) {
		foreach($daDadaResult['suggestions'] as $suggestion) {
			if(!empty($suggestion['data']['address']['value']) && !empty($suggestion['data']['name']['short_with_opf'])) {
				$result[]=[
					'name'=>$suggestion['data']['name']['short_with_opf'],
					'address'=>$suggestion['data']['address']['value'],
					// 'data'=>$suggestion
				];
			}
		}
		usort($result, function($a, $b) {
			return strcasecmp($a['address'], $b['address']);
		});
	}
	return $result;
};

$result=['success'=>false];
switch(Data::get($_POST, 'mode')) {
	case 'address':
		$result['data']=$fDaDadaGetAddresses(Data::get($_POST, 'address'));
		$result['success']=true;
		break;
		
	case 'company':
		$result['data']=$fDaDadaGetCompanyAddresses(Data::get($_POST, 'query'));
		$result['success']=true;
		break;
		
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
            $price=($promocode && $fCheckPromocode($promocode)) ? $pricePromocode : $priceDefault;
            
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
