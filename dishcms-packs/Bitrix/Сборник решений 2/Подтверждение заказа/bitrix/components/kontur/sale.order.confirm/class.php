<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Sale\Location;
use Bitrix\Main\Loader;
use Bitrix\Sale\DiscountCouponsManager;

if (!Loader::includeModule("sale"))
{
	ShowError(GetMessage("SOA_MODULE_NOT_INSTALL"));
	return;
}

$bUseCatalog = Loader::includeModule("catalog");

class KonturSaleOrderConfirmComponent extends CBitrixComponent
{
	public function GetResult($arParams)
	{
		$arResult=array();

		if(!empty($_POST)) {
			$arResult['ORDER']=array();
			foreach($_POST as $key=>$value) {
				if($key == 'PERSON_TYPE') {
					$this->SetValue($arResult['ORDER'], $key, function($ID) { return CSalePersonType::GetByID($ID); });
				}
				elseif($key == 'DELIVERY_ID') {
					$this->SetValue($arResult['ORDER'], array($key,'DELIVERY'), function($ID) { return CSaleDelivery::GetByID($ID); });
				}
				elseif($key == 'PAY_SYSTEM_ID') {
					$this->SetValue($arResult['ORDER'], array($key,'PAY_SYSTEM'), function($ID) { return CSalePaySystem::GetByID($ID); });
				}
				elseif($key == 'BUYER_STORE') {
					$this->SetValue($arResult['ORDER'], 'BUYER_STORE', function($ID) {
						$rs=CCatalogStore::GetList(array(),array('ID'=>$ID));
						return $rs->GetNext();
					});
				}
				elseif(preg_match('/^ORDER_PROP_(\d+)$/', $key, $m)) {
					$arResult['ORDER']['PROPERTIES'][$m[1]]=CSaleOrderProps::GetByID($m[1]);
					$arResult['ORDER']['PROPERTIES'][$m[1]]['VALUE']=$value;
				}
				elseif(in_array($key, array('ORDER_DESCRIPTION'))) {
					$this->SetValue($arResult['ORDER'], 'ORDER_DESCRIPTION');
				}
			}
		}

		return $arResult;
	}

	protected function GetValue($name, $default=null)
	{
		return array_key_exists($name, $_POST) ? $_POST[$name] : $default;
	}

	protected function SetValue(&$arResult, $name, $callable=null, $params=array())
	{
		if(is_array($name)) {
			$key=$name[0];
			$name=$name[1];
		}
		else {
			$key=$name;
		}

		$value=$this->GetValue($key);
		if($value !== null) {
			if(is_callable($callable)) {
				array_unshift($params, $value);
				$arResult[$name]=call_user_func_array($callable, $params);
			}
			else {
				$arResult[$name]=$value;
			}
			return true;
		}
		$arResult[$name]=null;
		return false;
	}
}
