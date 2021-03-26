<?
/**
 * Iblock Component Parameters
 *
 */
namespace Kontur\Core\Iblock\Component; 

use Bitrix\Main\Localization\Loc,
    Kontur\Core\Iblock\TypeTable,
    Kontur\Core\Iblock\IblockTable,
    Kontur\Core\Main\Tools;

Loc::loadMessages(__FILE__);
    
class Parameters
{
    public static function getTypeList($parameters=[], $empty=false, $prefixKey=false)
    {
    	if(empty($parameters)) {
    		$parameters=['select'=>['ID']];
    	}
    	
        return Tools\Data::listData(TypeTable::getAll($parameters), 'ID', 'ID', $prefixKey, $empty);
    }
    
    public static function getIblockList($parameters, $empty=false, $prefixKey=false)
    {
    	if(empty($parameters)) {
    		$parameters=['select'=>['ID', 'NAME'], 'filter'=>['=ACTIVE'=>'Y']];
    	}

        return Tools\Data::listData(IblockTable::getAll($parameters), 'ID', 'NAME', $prefixKey, $empty);
    }
    
    public static function getEventTypeList($parameters=[], $empty=false)
    {
        if($empty) $list=$empty;
        else $list=[];
        
    	$rs=\CEventType::GetList(
            Tools\Data::get($parameters, 'filter', []), 
            Tools\Data::get($parameters, 'order', [])
        );
        
        while($item=$rs->Fetch()) { 
            $list[ $item['EVENT_NAME'] ] = "[{$item['EVENT_NAME']}] {$item['NAME']}";
        }
        
        return $list;
    }

    /**    
     * @param array $parameters
     * Поле "order" должно содержать одно значение, либо не передано. 
     * напр., ['site_id'=>'asc']
     */
    public static function getEventList($parameters=[], $empty=false, $prefixKey=false)
    {        
        if($empty) $list=$empty;
        else $list=[];
        
        list($by, $order) = Tools\Data::get($parameters, 'order', ['event_name'=>'asc']);
        $rs=\CEventMessage::GetList($by, $order, Tools\Data::get($parameters, 'filter', ['ACTIVE'=>'Y']));
        
        while($item=$rs->Fetch()) {
            $list[ $item['ID'] ] = "[{$item['ID']}] {$item['EVENT_MESSAGE_TYPE_NAME']}";
        }
        
        return $list;
    }    
    
    /**
     * @param &array $arParameters массив параметров. Напр., $arComponentParameters['PARAMETERS']
     * @param array $typeParams массив конфигурации для параметра типа инфоблока.
     * Доступны: "PARAM_NAME", "PARENT", "NAME", "DEFAULT".
     * "PARAM_NAME" - имя параметра типа инфоблока (по умолчанию "IBLOCK_TYPE")
     *  
     */
    public static function addIblockParameters(
        &$arParameters, 
        $arCurrentValues,
        $typeParams=['DEFAULT'=>''], 
        $iblockIdParams=['DEFAULT'=>'={$_REQUEST["ID"]}']
    )
    {
        $typeList = self::getTypeList();
        
        $typeParamName=Tools\Data::get($typeParams, 'PARAM_NAME', 'IBLOCK_TYPE');
        self::addParameter($arParameters, $typeParamName, $typeParams, [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('IBLOCK_TYPE_NAME'),
            'TYPE' => 'LIST',
            'ADDITIONAL_VALUES' => 'Y',
            'DEFAULT' => '',
            'REFRESH' => 'Y',
            'VALUES' => $typeList
        ]);
        
        if(empty($arCurrentValues[$typeParamName])) {
        	$typeId = key($typeList);
        }
        else {
        	$typeId = $arCurrentValues[$typeParamName];
        }
        
        $iblockList = self::getIblockList([
        	'select' => ['ID', 'NAME'],
        	'filter' => ['=IBLOCK_TYPE_ID'=>$typeId, '=ACTIVE'=>'Y']
        ], false);

        self::addParameter($arParameters, 'IBLOCK_ID', $iblockIdParams, [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('IBLOCK_ID_NAME'),
            'TYPE' => 'LIST',
            'ADDITIONAL_VALUES' => 'Y',
            'DEFAULT' => '',
            'REFRESH' => 'Y',
            'VALUES' => $iblockList
        ]);
    }
    
    /**
     * Добавить параметры выбора шаблона почтового уведомления.
     *
     * @param &array $arParameters массив параметров. Напр., $arComponentParameters['PARAMETERS']
     *
     * @param array $typeParams массив конфигурации для параметра типа почтового сообщения.
     * Доступны: "PARAM_NAME", "PARENT", "NAME", "DEFAULT".
     * "PARAM_NAME" - имя параметра типа инфоблока (по умолчанию "IBLOCK_TYPE")
     *  
     * @param array $eventParams массив конфигурации для параметра выбора шаблона почтового сообщения.
     * Доступны: "PARAM_NAME", "PARENT", "NAME", "DEFAULT".
     * "PARAM_NAME" - имя параметра типа инфоблока (по умолчанию "IBLOCK_TYPE")
     *  
     */
    public static function addEventParameters(&$arParameters, $arCurrentValues, $typeParams=[], $eventParams=[])
    {
        $typeList = self::getEventTypeList(['filter'=>['LID'=>\LANGUAGE_ID]], false, 'ID');
        
        $typeParamName=Tools\Data::get($typeParams, 'PARAM_NAME', 'EVENT_TYPE');
        self::addParameter($arParameters, $typeParamName, $typeParams, [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('EVENT_TYPE_NAME'),
            'TYPE' => 'LIST',
            'ADDITIONAL_VALUES' => 'Y',
            'DEFAULT' => '',
            'REFRESH' => 'Y',
            'VALUES' => $typeList
        ]);
        
        if(empty($arCurrentValues[$typeParamName])) {
        	$typeId = key($typeList);
        }
        else {
        	$typeId = $arCurrentValues[$typeParamName];
        }
        
        $eventList = self::getEventList(['filter' => [
                'TYPE_ID'=>$typeId, 
                'ACTIVE'=>'Y', 
                'SITE_ID'=>\Bitrix\Main\Context::getCurrent()->getSite()
            ]], 
            false, 
            'TYPE_ID'
        );

        self::addParameter($arParameters, 'EVENT_ID', $eventParams, [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('EVENT_ID_NAME'),
            'TYPE' => 'LIST',
            'ADDITIONAL_VALUES' => 'Y',
            'DEFAULT' => '',
            'VALUES' => $eventList
        ]);
    }
    
    public static function addMenuTypesParameter(&$arParameters, $name='MENU_TYPE', $params=[])
    {
        self::addParameter($arParameters, $name, $params, [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('MENU_TYPE_NAME'),
            'TYPE' => 'LIST',
            'ADDITIONAL_VALUES' => 'Y',
            'DEFAULT' => '',
            'VALUES' => GetMenuTypes(),
            'COLS' => 45
        ]);
    }
    
    public static function addMedialibParameter(&$arParameters, $name='MLIBCOLLECTION_ID', $params=[])
    {
        \Bitrix\Main\Loader::includeModule('fileman');
        \CMedialib::Init();
        
        $collections=\CMedialibCollection::GetList(array('arFilter' => array('ACTIVE' => 'Y')));
        $rootsList=[];
        $childList=[];
        foreach($collections as $collection) {
            if(empty($collection['PARENT_ID'])) {
                $rootsList[ $collection['ID'] ] = $collection['NAME'];
            }
            else {
                $childList[ $collection['PARENT_ID'] ][ $collection['ID'] ] = $collection['NAME'];
            }
        }
        
        $collectionList=[];
        if(!empty($rootsList)) {
            $fGetList=function($data, $path='') use (&$collectionList, &$fGetList, $childList) {
                foreach($data as $id=>$title) {
                    $collectionList[ $id ] = $path . $title;
                    if(!empty($childList[ $id ])) {
                        $fGetList($childList[ $id ], $path.$title.' / ');
                    }
                }
            };
            $fGetList($rootsList);
        }
        
        self::addParameter($arParameters, $name, $params, [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('MLIBCOLLECTION_ID_NAME'),
            'TYPE' => 'LIST',
            'DEFAULT' => '',
            'VALUES' => $collectionList,
            'REFRESH' => 'Y'
        ]);
    }
    
    /**
     * @param array $params доступны "PARAM_NAME", "PARENT", "NAME", "REFRESH".
     */
    public static function addFieldCode(&$arParameters, $params=[])
    { 
        $name=Tools\Data::get($params, 'PARAM_NAME', 'FIELD_CODE');
        $arParameters[$name] = \CIBlockParameters::GetFieldCode(
            Tools\Data::get($params, 'NAME', Loc::getMessage('FIELD_CODE_NAME')), 
            Tools\Data::get($params, 'PARENT', 'BASE')
        );
        
        $arParameters[$name]['REFRESH'] = Tools\Data::get($params, 'REFRESH', 'N');
    }
    
    /**
     * @param array $params доступны 
     *  "PARAM_NAME" - по умолчанию PROPERTY_CODE; 
     *  "PARENT" - по умолчанию BASE;
     *  "NAME" - по умолчанию Loc::getMessage('PROPERTY_CODE_NAME');
     *  "MULTIPLE" - по умолчанию "Y";
     *  "SIZE" - по умолчанию 8;
     *  "ADDITIONAL_VALUES" - по умолчанию "Y";
     *  "DEFAULT"  - по умолчанию "" (пустая строка);
     * REFRESH
     */
    public static function addPropertyCode(&$arParameters, $iblockId, $params=[])
    {
        if(!empty($iblockId)) {
            $data = \Kontur\Core\Iblock\PropertyTable::getAll(['filter'=>['IBLOCK_ID'=>$iblockId]]);
            $propertyList = Tools\Data::listData($data, 'CODE', 'NAME', 'CODE');
            
            self::addParameter($arParameters, 'PROPERTY_CODE', $params, [
                'PARENT' => 'BASE',
                'NAME' => Loc::getMessage('PROPERTY_CODE_NAME'),
                'TYPE' => 'LIST',
                'ADDITIONAL_VALUES' => 'Y',
                'SIZE' => 8,
                'MULTIPLE' => 'Y',
                'DEFAULT' => '',
                'VALUES' => $propertyList
            ]);
        }
    }
    
    /**
     * @param array $name имя параметра. Если передан $params['PARAM_NAME'] 
     * или $default['PARAM_NAME'], то имя будет заменено переданным значением. 
     * Если имя параметра нигде не передано, параметр добавлен не будет. 
     *
     * @param array $params массив параметров.
     * @param array $default массив значений параметров по умолчанию.
     */
    public static function addParameter(&$arParameters, $name, $params=[], $default=[])
    {
        $name=Tools\Data::get($params, 'PARAM_NAME', Tools\Data::get($default, 'PARAM_NAME', $name));
        
        if(!empty($name)) {
            $config=[];
            foreach(['PARENT', 'NAME', 'TYPE', 'VALUES', 'DEFAULT', 'ADDITIONAL_VALUES', 'MULTIPLE', 'SIZE', 'COLS', 'REFRESH'] as $paramName) {
                self::addConfigParam($config, $paramName, $params, $default);
            }
            $arParameters[$name] = $config;
        }
    }
    
    public static function addConfigParam(&$config, $name, $params, $default=[])
    {
        $value = Tools\Data::get($params, $name, Tools\Data::get($default, $name, null));
        
        if($value !== null) {
            $config[$name] = $value;
        }
    }
    
    /**
     * Добавление перечисляемых параметров.
     *
     * @param array $data массив кодов параметров.
     * @param array $groupConfig конфигурация группы вида:
     *  array(
     *      'GROUP_NAME' => Префикс для имени группы. К префиксу будет добавлен код параметра.
     *      'NAME' => Заголовок группы. К имени будет добавлен номер группы и код параметра.
     *  )
     * @param array $fieldConfig массив конфигурации параметров группы вида:
     *  array(
     *      '<PREFIX_FIELD_CODE>' => array( 
     *           'NAME'  => имя,
     *           'TYPE'  => тип,
     *              ... стандартная конфигурация параметра.
     *      )
     *  )
     *  "<PREFIX_FIELD_CODE>" - префикс поля. К префиксу будет добавлен код параметра.
     * @param integer $idxField стартовый номер параметра (нумерация с нуля). По умолчанию 0.
     * @return integer номер ($idxField) последнего добавленного параметра.
     */
    public static function addRange(&$arComponentParameters, $data, $groupConfig, $fieldConfig, $idxField=0)
    {
        if(!empty($data)) {
            $groupKey=Tools\Data::get($groupConfig, 'GROUP_NAME');
            $groupName=Tools\Data::get($groupConfig, 'NAME');
            if(!empty($groupKey) && !empty($groupName)) {
                foreach($data as $code) {
                    if(!empty($code)) {
                        ++$idxField;
                        $arComponentParameters['GROUPS'][$groupKey . $code] = [
                            'NAME' => $groupName . " #{$idxField} ({$code})"
                        ];
                
                        foreach($fieldConfig as $fieldKey=>$params) {
                            Parameters::addParameter(
                                $arComponentParameters['PARAMETERS'], 
                                $fieldKey.$code, 
                                $params,
                                ['PARENT' => $groupKey.$code]
                            );
                        }
                    }
                }
            }
        }
        
        return $idxField;
    }
}