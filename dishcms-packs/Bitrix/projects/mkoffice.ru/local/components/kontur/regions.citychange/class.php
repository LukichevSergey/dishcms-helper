<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Kontur\Core\Main\Tools\Data;
use Bitrix\Main\Page\Asset;

class KonturRegionsCityChangeComponent extends CBitrixComponent
{
    private $helper=null;
    
    public function getHelper()
    {
        if($this->helper === null) {
            $this->helper=new \KRCCHelper;
            $this->helper->init([
                'IBLOCK_ID'=>Data::get($this->arParams, 'IBLOCK_ID'), 
                'COOKIE_KEY'=>Data::get($this->arParams, 'COOKIE_KEY'),
                'PROPERTY_IS_DEFAULT_CODE'=>Data::get($this->arParams, 'IS_DEFAULT_PROPERTY_CODE'),
                'DISABLE_GEOIP'=>Data::get($this->arParams, 'DISABLE_BX_GEOIP', 'N')
            ]);
        }
        return $this->helper;
    }
    
    public function registerYMap()
    {
        $url='https://api-maps.yandex.ru/2.1/?lang=ru_RU';
        if($apikey=Data::get($this->arParams, 'YMAP_API_KEY')) {
            $url.='&apikey='.$apikey;
        }
        Asset::getInstance()->addJs($url);
    }
    
    public function getIsDefaultPropertyCode($prefix='', $postfix='')
    {
        $code=Data::get($this->arParams, 'IS_DEFAULT_PROPERTY_CODE');
        return empty($code) ? null : $prefix.$code.$postfix;
    }
    
    public function getLocationPropertyCode($prefix='', $postfix='')
    {
        $code=Data::get($this->arParams, 'LOCATION_PROPERTY_CODE');
        return empty($code) ? null : $prefix.$code.$postfix;
    }
    
    public function getAddressPropertyCode($prefix='', $postfix='')
    {
        $code=Data::get($this->arParams, 'ADDRESS_PROPERTY_CODE');
        return empty($code) ? null : $prefix.$code.$postfix;
    }
    
    public function getMapPropertyCode($prefix='', $postfix='')
    {
        $code=Data::get($this->arParams, 'MAP_PROPERTY_CODE');
        return empty($code) ? null : $prefix.$code.$postfix;
    }
    
    public function getResult()
    {
        $arResult=[
            'ACTIVE_ITEM'=>null,
            'ITEMS'=>[],
            'REGIONS'=>[]
        ];
        
        $iblockType=Data::get($this->arParams, 'IBLOCK_TYPE');
        $iblockId=Data::get($this->arParams, 'IBLOCK_ID');
        
        if(!empty($iblockType) && !empty($iblockId)) {
            $select=['ID', 'IBLOCK_ID', 'NAME', 'CODE'];
            if($this->getIsDefaultPropertyCode()) $select[]=$this->getIsDefaultPropertyCode('PROPERTY_');
            if($this->getLocationPropertyCode()) $select[]=$this->getLocationPropertyCode('PROPERTY_');
            if($this->getAddressPropertyCode()) $select[]=$this->getAddressPropertyCode('PROPERTY_');
            if($this->getMapPropertyCode()) $select[]=$this->getMapPropertyCode('PROPERTY_');
            
            $rs=\CIblockElement::GetList(['NAME'=>'ASC'], [
                'IBLOCK_TYPE'=>$iblockType,
                'IBLOCK_ID'=>$iblockId
            ], false, false, $select);
            while($elm=$rs->Fetch()) {
                $item=[
                    'ID'=>$elm['ID'],
                    'IBLOCK_ID'=>$elm['IBLOCK_ID'],
                    'NAME'=>$elm['NAME'],
                    'CODE'=>$elm['CODE'],
                    'LOCATION'=>null,
                    'ADDRESS'=>null,
                    'MAP'=>null,
                    'IS_DEFAULT'=>'N'
                ];
                
                if($isDefaultProp=$this->getIsDefaultPropertyCode('PROPERTY_', '_VALUE')) {
                    $item['IS_DEFAULT']=($elm[$isDefaultProp] == 'Y') ? 'Y' : 'N';
                }
                
                if($addressProp=$this->getAddressPropertyCode('PROPERTY_', '_VALUE')) {
                    $item['ADDRESS']=$elm[$addressProp];
                }
                
                if($mapProp=$this->getMapPropertyCode('PROPERTY_', '_VALUE')) {
                    $item['MAP']=$elm[$mapProp];
                }
                
                if($locationProp=$this->getLocationPropertyCode('PROPERTY_', '_VALUE')) {
                    if(!empty($elm[$locationProp])) {
                        $item['LOCATION']=$this->getHelper()->getLocation($elm[$locationProp]);
                        
                        if(count($item['LOCATION']) > 0) {
                            $regionName='Остальные';
                            foreach($item['LOCATION'] as $loc) {
                                if(!empty($loc['I_TYPE_CODE']) && ($loc['I_TYPE_CODE'] == 'REGION')) {
                                    $regionName=$loc['I_NAME_RU'];
                                    break;
                                }
                            }
                            
                            if(empty($arResult['REGIONS'][$regionName])) {
                                $arResult['REGIONS'][$regionName]=[];
                            }
                            $arResult['REGIONS'][$regionName][]=[
                                'NAME'=>end($item['LOCATION'])['I_NAME_RU'],
                                'ITEM'=>$item
                            ];
                        }
                    }                
                }
                
                $arResult['ITEMS'][]=$item;
            }
        }
        
        if($activeCity=$this->getHelper()->getActiveCity()) {
            foreach($arResult['ITEMS'] as $item) {
                if($item['CODE'] == $activeCity['CODE']) {
                    $arResult['ACTIVE_ITEM']=$item;
                    break;
                }
            }
        }
                
        if(empty($arResult['ACTIVE_ITEM'])) {
            $arResult['ACTIVE_ITEM']=reset($arResult['ITEMS']);
        }
        
        return $arResult;
    }
}
