<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

class KonturSliderSlickComponent extends CBitrixComponent
{
    public function getResult()
    {
        $arResult=[];
        
        if(empty($this->arParams['FILTER_NAME'])) {
            $this->arParams['FILTER_NAME']='arrKSlickSliderFilter';
        }
        
        if(!empty($GLOBALS[$this->arParams['FILTER_NAME']])) {
            $filter=$GLOBALS[$this->arParams['FILTER_NAME']];
            $filter['IBLOCK_ID']=$this->arParams['IBLOCK_ID'];
            
            $select=['ID', 'IBLOCK_ID', 'NAME', 'PREVIEW_PICTURE', 'DETAIL_PICTURE'];
            if(!empty($this->arParams['FIELD_CODE'])) {
                foreach($this->arParams['FIELD_CODE'] as $code) {
                    $select[]=$code;
                }
            }
            if(!empty($this->arParams['PROPERTY_CODE'])) {
                foreach($this->arParams['PROPERTY_CODE'] as $code) {
                    $select[]="PROPERTY_{$code}";
                }
            }
            
            if(!empty($this->arParams['MORE_PHOTO_PROPERTY_CODE'])) {
                $select[]='PROPERTY_' . $this->arParams['MORE_PHOTO_PROPERTY_CODE'];
            }
            
            if($elm=\CIBlockElement::GetList(['ID'=>'ASC'], $filter, false, false, $select)->Fetch()) {
                $arResult=$elm;
                
                if(!empty($this->arParams['MORE_PHOTO_PROPERTY_CODE'])) {
                    $arResult['MORE_PHOTO']=$arResult["PROPERTY_{$this->arParams['MORE_PHOTO_PROPERTY_CODE']}_VALUE"];
                }
            }
        }
        
        return $arResult;
    }
}
