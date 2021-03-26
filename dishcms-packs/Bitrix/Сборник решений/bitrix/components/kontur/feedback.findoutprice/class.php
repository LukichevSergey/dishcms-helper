<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!\CModule::IncludeModule("iblock"))
{
	ShowMessage(GetMessage("IBLOCK_ERROR"));
    return false;
}

class KonturFeedbackComponent extends CBitrixComponent
{
	public function GetItem($ID)
	{
	    $rs=\CIBlockElement::GetByID($ID);
    	return $rs->GetNext();
	}

	public function Validate($ID, $arData, $returnResult=false)
	{
		global $APPLICATION;
		
		$arErrors=array();
		$bProductFounded=false;
		if(!empty($arData)) {
			foreach($arData as $arField) {
				$value=$arField['value'];
				switch($arField['name']) {
					case 'id':
						$rs=\CIBlockElement::GetList(array("SORT"=>"ASC"), array("IBLOCK_ID"=>1, "ID"=>$value), false, false, array('ID'));
						$bProductFounded=(bool)$rs->Fetch();
						break;
					case 'name':
						if(!trim($value)) {
							$arErrors['name']='Обязательное поле не заполнено';
						}
						break;
					case 'email':
						if(!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
							$arErrors['email']='Неверно задан адрес электронной почты';
						}
						break;
					case 'phone':
						if(!trim($value)) {
                            $arErrors['phone']='Обязательное поле не заполнено';
                        }
                        elseif(!preg_match('/^[0-9 \-()+,]+$/', $value)) {
                        	$arErrors['phone']='Неверно задан номер телефона';
                        }
						break;
				}
			}
		}

		if($returnResult) {
			return ($bProductFounded && empty($arErrors));
		}

		$APPLICATION->RestartBuffer();
		echo json_encode(array('success'=>($bProductFounded && empty($arErrors)), 'errors'=>$arErrors));
		die;
	}

	public function Send($ID, $arData)
	{
		$sended=false;

		$rs=\CIBlockElement::GetList(array("SORT"=>"ASC"), array("IBLOCK_ID"=>1, "ID"=>$ID), false, false, array('ID', 'NAME', 'DETAIL_PAGE_URL'));
		if($arElement=$rs->GetNext()) {
			$arFields=array();
			foreach($arData as $arField) {
				switch($arField['name']) {
					case 'name': $arFields['NAME']=$arField['value']; break;
					case 'email': $arFields['EMAIL']=$arField['value']; break;
					case 'phone': $arFields['PHONE']=$arField['value']; break;
					case 'comment': $arFields['COMMENT']=$arField['value']; break;
				}
			}
			$arFields['PRODUCT_LINK']='http://' . $_SERVER['SERVER_NAME'] . $arElement['DETAIL_PAGE_URL'];
			
			$arIFields=array(
				'IBLOCK_ID'=>11,
				'IBLOCK_SECTION_ID'=>false,
				'NAME'=>$arFields['NAME'],
				'ACTIVE'=>'Y',
				'PROPERTY_VALUES'=>$arFields
			); 
			
			$el=new CIBlockElement;
			if($fbID=$el->Add($arIFields, false, false, false)) {
				$arFields['EMAIL_TO']=\COption::GetOptionString("main", "email_from");
				if($eventID=\CEvent::Send('FEEDBACK_FIND_OUT_PRICE', SITE_ID, $arFields)) {
					$sended=true;
				}
			}
		}
		return $sended;
	}
	
	public function CheckFields($arProperty, $value)
	{
		return array();
	}
}
