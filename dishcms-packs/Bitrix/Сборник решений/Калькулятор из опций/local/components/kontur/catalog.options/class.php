<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

\Bitrix\Main\Loader::includeModule("iblock");

class KonturCatalogOptionsComponent extends CBitrixComponent
{
    const PROP_OS='OPTIONS_SECTIONS';
    const PROP_OE='OPTIONS_ELEMENTS';
    const PROP_PRICE='FILTER_PRICE';
    const OPT_PROP_PRICE='PRICE';
    const UF_MULTIPLE='UF_MULTIPLE';
    
    private $element=null;
    
    public function getElement()
    {
        if($this->element === null) 
        {
            $parent=$this->GetParent();
            $id=$parent->arResult['ID'];
            
            $result=array($id=>array());
            \CIBlockElement::GetPropertyValuesArray(
                $result,
                $parent->arResult['IBLOCK_ID'],
                array('ID'=>$id),
                array('CODE'=>array(self::PROP_OS, self::PROP_OE, self::PROP_PRICE)),
                array('USE_PROPERTY_ID'=>'N', 'PROPERTY_FIELDS'=>array('VALUE'))
            );
            
            if(empty($result[$id])) {
                throw new \Exception('Option element not found');            
            }
            
            $this->element=array('ID'=>$id, 'IBLOCK_ID'=>$parent->arResult['IBLOCK_ID'], 'NAME'=>$parent->arResult['NAME']);
            $this->element[self::PROP_OS]=array_map('intval', $result[$id][self::PROP_OS]['VALUE']);
            $this->element[self::PROP_OE]=array_map('intval', $result[$id][self::PROP_OE]['VALUE']);
            $this->element[self::PROP_PRICE]=(int)$result[$id][self::PROP_PRICE]['VALUE'];
        }
        
        return $this->element;
    }
    
    public function getGroups()
    {
        if(!$this->arParams['IBLOCK_ID']) {
            throw new \Exception('Options iblock not found');       
        }
        
        $el=$this->getElement();
        
        if(!array_key_exists('GROUPS', $el)) {
            $el['GROUPS']=array();
            
            $sectionIDs=array();
            $hasOE=(count($el[self::PROP_OE]) > 0);
            $hasOS=(count($el[self::PROP_OS]) > 0);
            
            if($hasOE) {
                $rs=\CIBlockElement::GetList(
                    array('SORT'=>'ASC'), 
                    array('IBLOCK_ID'=>$this->arParams['IBLOCK_ID'], 'ID'=>$el[self::PROP_OE], 'ACTIVE'=>'Y'), 
                    false, 
                    false, 
                    array('ID', 'IBLOCK_ID', 'IBLOCK_SECTION_ID')
                );                
                while($e=$rs->Fetch()) {
                    $sectionIDs[(int)$e['IBLOCK_SECTION_ID']]=$e['IBLOCK_SECTION_ID'];
                }
            }
            
            if($hasOS) {
                $sectionIDs=array_merge($sectionIDs, $el[self::PROP_OS]);
            }
            
            if(!empty($sectionIDs)) {
                $rs=\CIBlockSection::GetList(
                    array('SORT'=>'ASC'), 
                    array('IBLOCK_ID'=>$this->arParams['IBLOCK_ID'], 'ID'=>$sectionIDs, 'ACTIVE'=>'Y', 'GLOBAL_ACTIVE'=>'Y'),
                    false,
                    array('ID', 'NAME', 'SORT', 'IBLOCK_ID', 'IBLOCK_SECTION_ID', self::UF_MULTIPLE)
                );
                
                $parentSectionIDs=array();
                while($section=$rs->Fetch()) {
                    if(!empty($section['IBLOCK_SECTION_ID'])) {
                        $parentSectionIDs[]=(int)$section['IBLOCK_SECTION_ID'];
                        $el['GROUPS'][(int)$section['IBLOCK_SECTION_ID']]['SUBGROUPS'][(int)$section['ID']]=$section;
                    }
                    else {
                        $el['GROUPS'][(int)$section['ID']]=$section;
                    }
                }
                
                if(!empty($parentSectionIDs)) {
                    $rs=\CIBlockSection::GetList(
                        array('SORT'=>'ASC'),
                        array('IBLOCK_ID'=>$this->arParams['IBLOCK_ID'], 'ID'=>$parentSectionIDs, 'ACTIVE'=>'Y', 'GLOBAL_ACTIVE'=>'Y'),
                        false,
                        array('ID', 'NAME', 'SORT', 'IBLOCK_ID', 'IBLOCK_SECTION_ID', self::UF_MULTIPLE)
                    );
                    while($section=$rs->Fetch()) {
                        if(!empty($section['IBLOCK_SECTION_ID'])) {
                            if(isset($el['GROUPS'][(int)$section['ID']])) {
                                unset($el['GROUPS'][(int)$section['ID']]);
                            }
                        }
                        else {
                            $el['GROUPS'][(int)$section['ID']]=array_merge($el['GROUPS'][(int)$section['ID']], $section);
                        }
                    }
                }
                
                $rootSectionIDs=array_keys($el['GROUPS']);
                if(!empty($rootSectionIDs)) {
                    $rs=\CIBlockSection::GetList(
                        array('SORT'=>'ASC'),
                        array('IBLOCK_ID'=>$this->arParams['IBLOCK_ID'], 'SECTION_ID'=>$rootSectionIDs, 'ACTIVE'=>'Y', 'GLOBAL_ACTIVE'=>'Y'),
                        false,
                        array('ID', 'NAME', 'SORT', 'IBLOCK_ID', 'IBLOCK_SECTION_ID', self::UF_MULTIPLE)
                    );
                    while($section=$rs->Fetch()) {
                        $el['GROUPS'][(int)$section['IBLOCK_SECTION_ID']]['SUBGROUPS'][(int)$section['ID']]=$section;
                    }
                }
            }
            
            uasort($el['GROUPS'], [$this, 'sort']);            
            foreach($el['GROUPS'] as $id=>$data) {
                if(!empty($data['SUBGROUPS'])) {
                    uasort($el['GROUPS'][$id]['SUBGROUPS'], [$this, 'sort']);
                    foreach($data['SUBGROUPS'] as $subgroupId=>$subgroup) {
                        $el['GROUPS'][$id]['SUBGROUPS'][$subgroupId]['ELEMENTS']=array();
                    }
                }
                $el['GROUPS'][$id]['ELEMENTS']=array();
            }
            
            // get group elements
            $arElementsFilter=array(
                'IBLOCK_ID'=>$this->arParams['IBLOCK_ID'],
                'ACTIVE'=>'Y'
            );
            if($hasOE && $hasOS) {
                $arElementsFilter[]=array(
                    'LOGIC'=>'OR',
                    array('ID'=>$el[self::PROP_OE]),
                    array('IBLOCK_SECTION_ID'=>$el[self::PROP_OS])
                );
            }
            elseif($hasOE) {
                $arElementsFilter['ID']=$el[self::PROP_OE];
            }
            elseif($hasOS) {
                $arElementsFilter['IBLOCK_SECTION_ID']=$el[self::PROP_OS];
            }
            else {
                $arElementsFilter=false;
            }
            
            if(!empty($arElementsFilter)) {
                $subgroupElements=array();
                $rs=\CIBlockElement::GetList(array('SORT'=>'ASC'), $arElementsFilter);
                while($e=$rs->GetNextElement()) {
                    $element=$e->GetFields();
                    $element['PROPERTIES']=$e->GetProperties();
                    $element['PROPERTIES'][self::OPT_PROP_PRICE]['VALUE']=(int)preg_replace('/[^0-9]+/', '', $element['PROPERTIES'][self::OPT_PROP_PRICE]['VALUE']);
                    $sectionId=(int)$element['IBLOCK_SECTION_ID'];
                    if(!empty($el['GROUPS'][$sectionId])) {
                        $el['GROUPS'][$sectionId]['ELEMENTS'][(int)$element['ID']]=$element;
                    }
                    else {
                        $subgroupElements[$sectionId][(int)$element['ID']]=$element;
                    }
                }
                
                if(!empty($subgroupElements)) {
                    foreach($el['GROUPS'] as $id=>$data) {
                        if(!empty($data['SUBGROUPS'])) {
                            foreach($data['SUBGROUPS'] as $subgroupId=>$subgroup) {
                                if(!empty($subgroupElements[$subgroupId])) {
                                    $el['GROUPS'][$id]['SUBGROUPS'][$subgroupId]['ELEMENTS']=$subgroupElements[$subgroupId];
                                }
                            }
                        }
                    }
                }
            }
            
            $this->element['GROUPS']=$el['GROUPS'];
        }
        
        return $el['GROUPS'];
    }
    
    protected function sort($a, $b)
    {
        if((int)$a['SORT'] < (int)$b['SORT']) {
            return -1;
        }
        elseif((int)$a['SORT'] > (int)$b['SORT']) {
            return 1;
        }
        return 0;
    }
}
