<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Loader, 
    Bitrix\Main\Localization\Loc,
    Kontur\Core\Main\Data\Cache,
    Kontur\Core\Iblock\Component\Parameters,
    Kontur\Core\Main\Tools\Data;
   
Loc::loadMessages(__FILE__);
Loader::includeModule("iblock");

class KonturFeedbackFormComponent extends \CBitrixComponent
{
    const FIELD=1;
    const PROPERTY=2;
    
    const FIELD_PREFIX='KFF_FIELD_';
    const PROPERTY_PREFIX='KFF_PROPERTY_';

    /**
     * @var array массив полей формы вида:
     * array (
     * 		<CODE> => array(
     * 			PROPERTY_TYPE => 
     * 			PROPERTY_CODE => 
     * 			LABEL => 
     * 			TYPE => 
     * 			REQUIRE => 
     * 			REQUIRE_ERROR => 
     * 			SORT => 
     * 		)
     * )
     */
    protected $fields=[];
    
    /**
     * @param string $prefix префикс параметров. По умолчанию \KonturFeedbackFormComponent::FIELD_PREFIX
     */
    public static function addFieldParameters(&$arComponentParameters, $data, $prefix=null, $idxField=0)
    {
        if(!$prefix) {
            $prefix=self::FIELD_PREFIX;
        }
        
        return Parameters::addRange(
            $arComponentParameters, 
            $data,
            [
                'GROUP_NAME'=>$prefix,
                'NAME'=>'Поле формы'
            ],
            [
                $prefix . 'CODE_' =>[
                    'NAME'  => 'Код',
                    'TYPE'  => 'STRING',
                ],
                $prefix . 'LABEL_' => [
                    'NAME'  => 'Заголовок',
                    'TYPE'  => 'STRING',
                ],
                $prefix . 'TYPE_' => [
                    'NAME'  => 'Тип',
                    'TYPE'  => 'LIST',
                    'VALUES'  => self::getFieldTypes(),
                    'DEFAULT'  => 'S',
                ],
                $prefix . 'REQUIRE_' => [
                    'NAME'  => 'Обязательное',
                    'TYPE'  => 'CHECKBOX',
                ],
                $prefix . 'REQUIRE_ERROR_' => [
                    'NAME'  => 'Текст ошибки обязательного поля',
                    'TYPE'  => 'STRING',
                    'DEFAULT'  => '',
                ],
                $prefix . 'SORT_' => [
                    'NAME'  => 'Сортировка',
                    'TYPE'  => 'STRING',
                    'DEFAULT' => 500
                ]
            ],
            $idxField
        );
    }
    
    public static function getFieldTypes()
    {
        return [
            'S' => 'Строка',
            'N' => 'Число',
            'L' => 'Выпадающий список',
            'F' => 'Файл',
            'T' => 'Текст',
            'C' => 'Флажок (checkbox)',
            'LC' => 'Список (checkbox)',
            'LR' => 'Список (radio)',
            'E' => 'E-Mail',
            'PH' => 'Телефон',
            'PHM' => 'Телефон c маской +7 (xxx) xxx-xx-xx'
        ];
    }
    
    public function getParameterPrefixes()
    {
        return [
            self::FIELD => self::FIELD_PREFIX,
            self::PROPERTY => self::PROPERTY_PREFIX
        ];
    }
    
    public function getFields($arParams)
    {
    	if(empty($this->fields)) {
    		$this->fields=[];
    		foreach($arParams as $name=>$value) {
    			foreach($this->getParameterPrefixes() as $type=>$prefix) {
    				if(strpos($name, $prefix.'CODE_') === 0) {
    					$propertyCode=substr($name, strlen($prefix.'CODE_'));
    					if($code=trim(Data::get($arParams, $prefix.'CODE_'.$propertyCode, ''))) {
	    					$this->fields[$code]=[    							
	    						'PROPERTY_TYPE' => $type,
	    						'PROPERTY_CODE' => $propertyCode,
	    						'CODE' => $code,
	    						'TYPE' => Data::get($arParams, $prefix.'TYPE_'.$propertyCode, false),
	    						'LABEL' => trim(Data::get($arParams, $prefix.'LABEL_'.$propertyCode, '')),
	    						'REQUIRE' => (Data::get($arParams, $prefix.'REQUIRE_'.$propertyCode) == 'Y'),
	    						'REQUIRE_ERROR' => trim(Data::get($arParams, $prefix.'REQUIRE_ERROR_'.$propertyCode, '')),
	    						'SORT' => Data::get($arParams, $prefix.'SORT_'.$propertyCode, 500)
	    					];
    					}
    				}
    			}
    		}
    	}
    	
   		return $this->fields;
    }
    
    public function getFormId($arParams)
    {
        return Data::get($arParams, 'KFF_FORM_ID', false);
    }
    
    public function getCacheId($arParams, $key=null)
    {
        return $this->getFormId($arParams).'___'.count($arParams).($key ? ('___'.$key) : '');
    }
    
    public function getCacheDir($arParams)
    {
        return '/KonturFeedbackFormComponent/' . $this->getFormId($arParams);
    }
    
    /**
     * В $arParams может быть дополнительно передано два параметра:
     * 'KFF_IBLOCK_TYPE_PARAM_NAME' - имя параметра типа информационного блока.
     * 'KFF_IBLOCK_ID_PARAM_NAME' - имя параметра идентификатора инфоблока.
     * 'KFF_SECTION_ID_PARAM_NAME' - имя параметра идентификатора раздела.
     * 'KFF_ACTIVE_DEFAULT_PARAM_NAME' - имя параметра при добавлении элемента 
     * устанавливать статус активный или неактивый.
     */
    public function send($data, $arParams)
    {
        global $APPLICATION;
        
        $APPLICATION->RestartBuffer();
        
        $result=['success'=>false, 'errors'=>[]];
        
        $data=$this->normalizeData($data, $arParams);
        if($this->validate($result['errors'], $data, $arParams)) {
            $result['success']=$this->eventSend($data, $arParams);                
        }
        
        echo \Bitrix\Main\Web\Json::encode($result);
        die;
    }
    
    /**
     * Номализация входных данных.
     * @todo нормализация идет из данных, переданных в формате jQuery.serializeArray()
     * @return array массив полей вида:
     *  array(
     *      <self::FIELD | self::PROPERTY> => array (
     *          <PROPERTY_CODE> => array(
     *              'PROPERTY_TYPE' => self::FIELD|self::PROPERTY, 
     *              'PROPERTY_CODE' => property code, 
     *              'CODE' => field code,
     *              'TYPE' => field type,
     *              'LABEL' => field label,
     *              'REQUERE' => true|false
     *  )))
     */
    public function normalizeData($data, $arParams)
    {
        $result=[];
        
        if(!empty($data)) {
            $formId=$this->getFormId($arParams);
            
            $values=[];
			foreach($data as $field) {
				$code=$this->getFieldCode($formId, $field['name']);
				$values[ $code ] = [
					'FORM_FIELD_NAME' => $field['name'],
					'VALUE' => $field['value']
				];
			}
			
            foreach($this->getFields($arParams) as $code=>$params) {
            	if(!array_key_exists($code, $values)) {
            		$values[$code]=[
            			'FORM_FIELD_NAME' => "{$formId}[{$code}]",
            			'VALUE' => ''            			
            		];
            	}
            	$result[ $params['PROPERTY_TYPE'] ][ $params['PROPERTY_CODE'] ] = array_merge($params, $values[$code]);
            }
        }
        
        return $result;
    }
    
    public function getFieldCode($formId, $fieldName)
    {
        return trim(str_replace($formId.'[', '', $fieldName), ']');
    }
    
	public function validate(&$errors, $normalizedData, $arParams)
	{
        $errors=[];
        
		if(empty($normalizedData)) {
            return false;
        }
        else {
            foreach($normalizedData as $type=>$parameters) {
                foreach($parameters as $code=>$params) {
                    if(!trim($params['VALUE'])) {
                    	if($params['REQUIRE']) {
                    		$error=!empty($params['REQUIRE_ERROR']) ? $params['REQUIRE_ERROR'] : ('Поле "'.$params['LABEL'].'" обязательно для заполнения');
	                        $errors[ $params['FORM_FIELD_NAME'] ] = $error;
    	                }
    	                continue;
                    }
                    switch($params['TYPE']) {
                        case 'E': 
                            if(filter_var($params['VALUE'], FILTER_VALIDATE_EMAIL) === false) {
                                $errors[ $params['FORM_FIELD_NAME'] ] = 'Поле "'.$params['LABEL'].'" не является E-Mail адресом.';
                                continue;
                            }
                            break;
                        case 'PH':
                        case 'PHM':
                            if(!preg_match('/^[0-9+\-(),;\s]+$/', $params['VALUE'])) {
                                $errors[ $params['FORM_FIELD_NAME'] ] = 'Поле "'.$params['LABEL'].'" не является корректным номером телефона.';
                                continue;
                            }
                            break;
                    }
                }
            }
        }
        
        return empty($errors);        
	}

	public function eventSend($normalizedData, $arParams)
	{
        $fields=[];
        $properties=[];
        $eventFields=[];        
        foreach($normalizedData as $type=>$parameters) {
            foreach($parameters as $code=>$params) {
                $eventFields[ $params['CODE'] ] = $params['VALUE'];
                if($type == self::FIELD) {
                    $fields[ $code ] = $params['VALUE'];
                }
                elseif($type == self::PROPERTY) {
                    $properties[ $code ] = $params['VALUE'];
                }
            }
        }        
        
        // $iblockTypeParamName=Data::get($arParams, 'KFF_IBLOCK_TYPE_PARAM_NAME', 'IBLOCK_TYPE');
        $iblockIdParamName=Data::get($arParams, 'KFF_IBLOCK_ID_PARAM_NAME', 'IBLOCK_ID');
        $sectionIdParamName=Data::get($arParams, 'KFF_SECTION_ID_PARAM_NAME', 'SECTION_ID');
        $activeDefaultParamName=Data::get($arParams, 'KFF_ACTIVE_DEFAULT_PARAM_NAME', 'ACTIVE_DEFAULT');
        
        if($iblockId=Data::get($arParams, $iblockIdParamName)) {
            $el=new \CIBlockElement;
            
            $elementFields=[
                'IBLOCK_ID' => $iblockId,
                'IBLOCK_SECTION_ID' => Data::get($arParams, $sectionIdParamName, false),
                'ACTIVE' => ((Data::get($arParams, $activeDefaultParamName, 'Y') == 'Y') ? 'Y' : 'N') 
            ];
            $elementFields=array_merge($elementFields, $fields);
            if(!empty($properties)) {
                $elementFields['PROPERTY_VALUES']=$properties;
            }
            
            if($el->Add($elementFields, false, true, true)) {
                $eventType=Data::get($arParams, 'EVENT_TYPE');
                if($eventType) {
                    $eventFields['EMAIL_TO']=\COption::GetOptionString("main", "email_from");
                    return \CEvent::Send(
                        $arParams['EVENT_TYPE'], 
                        \SITE_ID, 
                        $eventFields, 
                        'Y', 
                        Data::get($arParams, 'EVENT_ID', '')
                    );
                }
				else {
					return true;
				}
            }            
        }
        
        return false;
	}	
}
