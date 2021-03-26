<?php 
/**
 * Подключение и запуск в php_interface/init.php
 * require_once dirname(__FILE__) . '/kontur/CatalogDynPropertiesParser.php';
 * \kontur\CatalogDynPropertiesParser::register();
 */
namespace kontur;

use Bitrix\Main\Loader;

class CatalogDynPropertiesParser
{
    const PROP_PREFIX='DYN_';
    
    private $config=null;
    private $props=null;
    private $sort=1500;

	public static function c($str)
    {
        return iconv('UTF-8', \SITE_CHARSET, $str);
    }
    
    public static function register()
    {
        $eventManager=\Bitrix\Main\EventManager::getInstance();
        $eventManager->addEventHandler('main', 'OnBuildGlobalMenu', ['\kontur\CatalogDynPropertiesParser', 'registerMenu']);
        
        $admin_import_file = $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/admin_iblock_properties_parser.php';
        if(!is_file($admin_import_file)) {
            copy(dirname(__FILE__).'/admin/admin_iblock_properties_parser.php', $admin_import_file);
        }        
    }
    
    /**
     * Добавление пунктов меню в раздел администрирования
     * @see \Bitrix\Main\Event main.OnBuildGlobalMenu
     */    
    public static function registerMenu(&$arGlobalMenu, &$arModuleMenu)
    {
        foreach($arModuleMenu as $idxMenu=>$arMenu) {
            if(($arMenu['parent_menu'] == 'global_menu_content') && ($arMenu['section'] == 'iblock') && ($arMenu['items_id'] == 'menu_iblock')) {
                foreach($arMenu['items'] as $idxItem=>$arItem) { 
                    if($arItem['items_id'] == 'iblock_redirect') {
                        $arModuleMenu[$idxMenu]['items'][$idxItem]['items'][] = [
                            'text' => static::c("Загрузить свойства товаров"),
                            'url' => 'admin_iblock_properties_parser.php?lang=ru',
                            'module_id' => 'iblock',
                            'more_url' => ['admin_iblock_properties_parser.php']
                        ];
                        break;
                    }
                }
            }
        }
    }
    
    public static function run()
    {
        Loader::includeModule("iblock");
        
        $parser=new static;
        
        $parser->execute();
        
        return true;
    }
    
    public function execute()
    {
        set_time_limit(0);
        
        $elements=\Bitrix\Iblock\ElementTable::getList([
            'select'=>['ID', 'NAME'],
            'filter'=>['IBLOCK_ID'=>$this->getIBlockId()],
            'limit'=>9999999
        ]);
        
        $i=0;
        foreach($elements as $element) {
            $properties=$this->getDynProperties($element);
            if(!empty($properties)) {
                $this->setDynProperties($properties);
            }
        }
    }
    
    protected function getConfig()
    {
        if($this->config === null) {
            $configFile=dirname(__FILE__) . '/config/catalog_dyn_properties.php';
            if(is_file($configFile)) {
                $this->config=include($configFile);
            }
        }
        return $this->config;
    }
    
    protected function getConfigParam($name, $default=null)
    {
        $config=$this->getConfig();
        
        if(array_key_exists($name, $config)) {
            return $config[$name];
        }
        
        return $default;
    }
    
    protected function convert($str)
    {
        if(\SITE_CHARSET != $this->getEncoding()) {
            return iconv($this->getEncoding(), \SITE_CHARSET, $str);
        }
        return $str;
    }
    
    protected function getIBlockId()
    {
        return $this->getConfigParam('IBLOCK_ID');
    }
    
    protected function getEncoding()
    {
        return $this->getConfigParam('ENCODING', 'UTF-8');
    }
    
    protected function getDynProperties($element)
    {
        $config=$this->getConfig();
        
        $properties=[];
        foreach($config as $param=>$cfg) {
            if(strpos($param, self::PROP_PREFIX) === 0) {
                if(!empty($cfg['NAME'])) {
                    $name=$cfg['NAME'];
                    unset($cfg['NAME']);
                    
                    $found=false;
                    foreach($cfg as $title=>$pattern) {
                        if(preg_match('/(' . $this->convert($pattern) . ')/i', $element['NAME'])) {
                            $properties[$param]=[
                                'ID'=>$element['ID'],
                                'NAME'=>$this->convert($name),
                                'VALUE'=>$this->convert($title)
                            ];
                            $found=true;
                            break;
                        }
                    }
                    if($found) continue;
                }
            }
        }
        
        return $properties;
    }
    
    protected function setDynProperties($properties)
    {
        if($this->props === null) {
            $codes=array_filter(array_keys($this->getConfig()), function($code){ 
                return (strpos($code, self::PROP_PREFIX) === 0); 
            });
            
            $this->props=[];
            $rs=\CIBlockProperty::GetList([], [
                'IBLOCK_ID'=>$this->getIBlockId(), 
                'CODE'=>self::PROP_PREFIX . '%'
            ]);
            while($prop=$rs->Fetch()) {
                $this->props[$prop['CODE']]=$prop;
                $this->props[$prop['CODE']]['VALUES']=$this->getPropertyEnumValues($prop['ID']);
                $this->props[$prop['CODE']]['~VALUES']=[];
                foreach($this->props[$prop['CODE']]['VALUES'] as $valueID=>$value) {
                    $this->props[$prop['CODE']]['~VALUES'][$valueID]=$value['VALUE'];
                }
                
                if((int)$prop['SORT'] > $this->sort) {
                    $this->sort=(int)$prop['SORT'];
                }
            }
        }
                
        foreach($properties as $code=>$data) {
            if(!empty($data['ID'])) {
                if(!isset($this->props[$code])) {
                    $propID=$this->addProperty($code, $data['NAME']);                    
                    if(!$propID) continue;
                }
                
                if(!in_array($data['VALUE'], $this->props[$code]['~VALUES'])) {
                    $valueID=$this->addPropertyEnum($code, $data['VALUE']);
                }
                else {
                    foreach($this->props[$code]['~VALUES'] as $valueID=>$value) {
                        if($value == $data['VALUE']) {
                            break;
                        }
                    }                    
                }
                
                if($valueID) {
                    \CIBlockElement::SetPropertyValues($data['ID'], $this->getIBlockId(), $valueID, $code);
                }
            }            
        }
    }
    
    protected function addProperty($code, $name)
    {
        $ibp=new \CIBlockProperty;
        
        $propID=$ibp->Add([
            'IBLOCK_ID'=>$this->getIblockId(),
            'CODE'=>$code,
            'NAME'=>$name,
            'PROPERTY_TYPE'=>'L',
            'ACTIVE'=>'Y',
            'SORT'=>((int)$this->sort + 100)
        ]);
        
        if($propID) {
            global $DB;
            $DB->Query("
                UPDATE b_iblock_section_property
                SET SMART_FILTER='Y'
                WHERE SECTION_ID = 0
                AND PROPERTY_ID = {$propID}
                AND IBLOCK_ID = " . $this->getIblockId()
            );
            
            $rs=\CIBlockProperty::GetByID($propID);
            $this->props[$code]=$rs->Fetch();
            $this->props[$code]['VALUES']=[];
            $this->props[$code]['~VALUES']=[];
            
            $this->sort += 100;
        }
        
        return $propID;
    }
    
    protected function getPropertyEnumValues($propID)
    {
        $values=[];
        
        $rs=\CIBlockProperty::GetPropertyEnum($propID);
        while($value=$rs->Fetch()) {
            $values[$value['ID']]=$value;
        }
        
        return $values;
    }
    
    protected function addPropertyEnum($code, $value)
    {
        $ibpenum = new \CIBlockPropertyEnum;
        $valueID=$ibpenum->Add([
            'PROPERTY_ID'=>$this->props[$code]['ID'],
            'XML_ID'=>md5($value),
            'VALUE'=>$value,
            'SORT'=>((count($this->props[$code]['~VALUES']) * 100) + 100)
        ]);
        
        if($valueID) {
            $this->props[$code]['~VALUES'][$valueID]=$value;
        }
    }
}
