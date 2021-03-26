<?php
/**
 * Интеграция с FrontPad
 * 
 * http://frontpad.ru/help/?help=1002
 * 
 * Добавить в init.php
 * require_once dirname(__FILE__) . '/kontur/frontpad/frontpad.php';
 * \kontur\frontpad\FrontPad::init();
 * 
 * События
 * (new \Bitrix\Main\Event('main', 'OnKonturFrontPadNewOrder', [IntVal($orderId)]))->send();
 */
namespace kontur\frontpad;

use Bitrix\Main;
use Bitrix\Main\Application;
use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\SectionTable;
use Bitrix\Iblock\ElementTable;
use Bitrix\Iblock\PropertyTable;

class FrontPad
{
    /**
     * @var integer тип раздела "Текст"
     */
    const SECTION_TYPE_TEXT=1;
    
    /**
     * @var integer тип раздела "Инфоблок"
     */
    const SECTION_TYPE_IBLOCK=2;
    
    /**
     * @var integer тип раздела "Раздел инфоблока"
     */
    const SECTION_TYPE_SECTION=3;
    
    /**
     * @var string префикс для идентификаторов разделов типа "Инфоблок"
     */
    const SECTION_TYPE_IBLOCK_PREFIX='b';
    
    /**
     * @var \kontur\frontpad\FrontPad статический экзепляр класса
     */
    private static $instance=null;
    
    /**
     * @var \stdClass статистика импорта товаров из FrontPad
     * Доступные свойства
     * "total" (integer) общее кол-во товаров на сайте для обновления
     * "added" (integer) кол-во добавленных товаров
     * "updated" (integer) кол-во обновленных товаров
     * "price_updated" (integer) кол-во обновленных цен товаров
     */
    private $stats=null;
    
    /**
     * @var [] конфигурация
     */
    private $cfg=[];
    
    /**
     * @var []|null разделы сайта из конфигурации
     */
    private $sections=null;
    
    /**
     * @var []|null товары FrontPad
     */
    private $products=null;
    
    /**
     * @var []|null дополнительные свойства товара
     */
    private $properties=null;
    
    /**
     * @var []|null параметры цен
     */
    private $priceProperties=null;
    
    /**
     * Инициализация FrontPad
     * 
     */
    public static function init($configFile=null)
    {
        static::getInstance()->loadConfig();
        static::getInstance()->registerOnBuildGlobalMenu();
    }
    
    /**
     * Запрос в базу данных
     * @param string $sql запрос
     * @return \Bitrix\Main\DB\Result
     */
    public static function query($sql)
    {
        $connection=Application::getConnection();
        
        return $connection->query($sql);
    }
    
    /**
     * Получить статический экзепляр класса
     * 
     * @return \kontur\frontpad\FrontPad
     */
    public static function getInstance()
    {
        if(static::$instance === null) {
            static::$instance=new static;
        }
        
        return static::$instance;
    }
    
    /**
     * Псевдоним для \kontur\frontpad\FrontPad::getInstance()
     * 
     */
    public static function i()
    {
        return static::getInstance();
    }
    
    /**
     * Загрузка конфигурации
     * 
     */
    public function loadConfig($configFile=null)
    {
        if($configFile === null) {
            $configFile=dirname(__FILE__) . '/config/import.php';
        }
        
        $this->cfg=include($configFile);
    }
    
    /**
     * Получить значение параметра конфигурации
     * 
     * @param string $name имя параметра. Может быть передано
     * значение через "." (точку) для получения вложенного параметра.
     * @param mixed $default значение по умолчанию. По умолчанию NULL.
     * 
     * @return mixed
     */
    public function config($name, $default=null, $cfg=null)
    {
        if($cfg === null) {
            $cfg=$this->cfg;
        }
        
        if(is_array($name)) $keys=$name;
        else $keys=explode('.', $name);
        
        $name=array_shift($keys);        
        if(!empty($keys) && array_key_exists($name, $cfg) && is_array($cfg[$name])) {
            return $this->config($keys, $default, $cfg[$name]);
        }
        elseif(empty($keys) && array_key_exists($name, $cfg)) {
            return $cfg[$name];
        }
        
        return $default;
    }
    
    /**
     * Регистрация пункта меню импорта из FrontPad в разделе администрирования
     * 
     */
    public function registerOnBuildGlobalMenu()
    {
        $eventManager = Main\EventManager::getInstance();
        $eventManager->addEventHandler('main', 'OnBuildGlobalMenu', ['\kontur\frontpad\FrontPad', 'buildAdminMenu']);
        $eventManager->addEventHandler('main', 'OnKonturFrontPadNewOrder', ['\kontur\frontpad\FrontPad', 'onNewOrder']);
        
        $admin_import_file = $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/admin_frontpad_import.php';
        if(!is_file($admin_import_file)) {
            copy(dirname(__FILE__).'/admin/admin_frontpad_import.php', $admin_import_file);
        }
        
        // добавление поля FRONTPAD_ORDER_ID в таблицу заказов
        $columns=static::query('DESCRIBE `bd_order_pizza`')->fetchAll();
        if(!in_array('FRONTPAD_ORDER_ID', array_column($columns, 'Field'))) {
            static::query('ALTER TABLE `bd_order_pizza` ADD COLUMN `FRONTPAD_ORDER_ID` VARCHAR(255)')->fetch();
        }
    }
    
    /**
     * Добавление пунктов меню в раздел администрирования
     * 
     * @see \Bitrix\Main\Event main.OnBuildGlobalMenu     * 
     */
    public static function buildAdminMenu(&$arGlobalMenu, &$arModuleMenu)
    {
        foreach($arModuleMenu as $idxMenu=>$arMenu) { 
            if(($arMenu['parent_menu'] == 'global_menu_content') && ($arMenu['section'] == 'iblock') && ($arMenu['items_id'] == menu_iblock)) {
                foreach($arMenu['items'] as $idxItem=>$arItem) {
                    if($arItem['items_id'] == 'iblock_import') {
                        $arModuleMenu[$idxMenu]['items'][$idxItem]['items'][]=[
                            'text' => 'FrontPad',
                            'url' => 'admin_frontpad_import.php',
                            'module_id' => 'iblock',
                            'more_url' => ['admin_frontpad_import.php']
                        ];
                    }
                }
            }
        }
    }
    
    /**
     * Получить URL к ресурсам для административного раздела
     * 
     */
    public function getAdminAssetsUrl()
    {
        return '/' . trim(substr(dirname(__FILE__), strlen($_SERVER['DOCUMENT_ROOT'])), '/') . '/admin/assets';
    }
    
    /**
     * Получить конфигурация для вкладки (TAB)
     * 
     * @param string $title заголовок вкладки
     * @param string|null $div идентификатор вкладки.
     * По умолчанию (NULL) будет сгенерирован автоматически.
     */
    public function getAdminTab($title, $div=null)
    {
        static $tabIndex=1;
        
        return [
            'DIV'=>$div ?: 'tab_frontpad_' . ($tabIndex++),
            'TAB'=>$title,
            'ICON'=>'iblock',
            'TITLE'=>$title,
        ];
    }
    
    /**
     * Получить разделы сайта из конфигурации
     * 
     * @param bool $flat возвращать список без вложенной структуры CHILDS
     * 
     * @return [] разделы сайта следующей структуры:
     * [
     *  'ID'=>идентификатор раздела (для типа FrontPad::SECTION_TYPE_TEXT будет установлен в NULL),
     *  'NAME'=>наименование раздела,
     *  'TYPE'=>тип раздела (FrontPad::SECTION_TYPE_TEXT, FrontPad::SECTION_TYPE_IBLOCK, FrontPad::SECTION_TYPE_SECTION),
     *  'CHILDS'=>[
     *      аналогичная структура подразделов
     *  ]
     * ]
     */
    public function getSections($flat=false)
    {
        if($flat || ($this->sections === null)) {
            $sections=$this->setSections($this->config('site.sections', []), $flat, $flat);
            
            if($flat) {
                return $sections;
            }
        }
        
        return $this->sections;
    }
    
    /**
     * Установить разделы сайта из конфигурации.
     * 
     * @param [] $sections конфигурация разделов.
     * @param bool $return возвратить результат.
     * @param bool $flat возвращать список без вложенной структуры CHILDS
     */
    protected function setSections($sections=[], $return=false, $flat=false)
    {
        $_sections=[];
        foreach($sections as $k=>$v) {
            $section=[];
            if(is_string($k) && is_array($v)) {
                $section['ID']=null;
                $section['NAME']=$k;
                $section['TYPE']=self::SECTION_TYPE_TEXT;
                if($flat) {
                    $section['CHILDS']=[];
                    $childs=$this->setSections($v, true, true);
                    foreach($childs as $child) {
                        $_sections[]=$child;
                    }
                }
                else {
                    $section['CHILDS']=$this->setSections($v, true);
                }
            }
            elseif(is_string($k) && is_numeric($v)) {
                $section['ID']=(int)$v;
                $section['IBLOCK_ID']=$section['ID'];
                $section['NAME']=$k;
                $section['TYPE']=self::SECTION_TYPE_IBLOCK;
                $section['CHILDS']=[];
            }
            elseif(is_numeric($k) && ($v === true)) {
                $section['ID']=(int)$k;
                $section['IBLOCK_ID']=$section['ID'];
                $section['NAME']=$this->getIBlockName($section['ID']);
                $section['TYPE']=self::SECTION_TYPE_IBLOCK;
                $section['CHILDS']=[];
                $isections=$this->getIBlockSections($section['ID']);  
                foreach($isections as $isection) {
                    $isection=[
                        'ID'=>$isection['ID'],
                        'IBLOCK_ID'=>$section['ID'],
                        'NAME'=>$isection['NAME'],
                        'TYPE'=>self::SECTION_TYPE_SECTION,
                        'CHILDS'=>[]
                    ];
                    if($flat) {
                        $_sections[]=$isection;
                    }
                    else {
                        $section['CHILDS'][]=$isection;
                    }
                }
            }
            elseif(is_numeric($v)) {
                $section['ID']=(int)$v;
                $section['IBLOCK_ID']=$section['ID'];
                $section['NAME']=$this->getIBlockName($section['ID']);
                $section['TYPE']=self::SECTION_TYPE_IBLOCK;
                $section['CHILDS']=[];
            }
            
            if(!empty($section)) {
                $_sections[]=$section;
            }
        }
        
        if($return || $flat) {
            return $_sections;
        }
        
        $this->sections=$_sections;
    }
    
    /**
     * Получить дополнительные свойства товара
     * 
     * @param bool $reload перезагрузить свойства
     * @return []
     */
    public function getProperties($reload=false)
    {
        if($this->properties === null) {
            $this->properties=[];    
                    
            $props=$this->config('site.properties.frontpad', []);
            if(!empty($props)) {
                foreach($props as $iblockId=>$props) {
                    $_props=[];
                    foreach($props as $propCode=>$frontPadPropCode) {
                        if(is_numeric($propCode)) {
                            $_props[$frontPadPropCode]=true;
                        }
                        else {
                            $_props[$propCode]=$frontPadPropCode;
                        }
                        
                    }
                    $rs=PropertyTable::getList([
                        'filter'=>['IBLOCK_ID'=>$iblockId, 'CODE'=>array_keys($_props)],
                        'order'=>['SORT'=>'ASC']
                    ]);
                    while($property=$rs->fetch()) {
                        if($_props[$property['CODE']] === true) {
                            $property['FRONTPAD_PROPERTY_CODE_IS_MAIN']=true;
                            $property['FRONTPAD_PROPERTY_CODE']=$property['CODE'];
                        }
                        else {
                            $property['FRONTPAD_PROPERTY_CODE_IS_MAIN']=false;
                            $property['FRONTPAD_PROPERTY_CODE']=$_props[$property['CODE']];
                        }
                        $property['SELECT_VALUE']='PROPERTY_' . $property['CODE'];
                        if(!empty($property['USER_TYPE'])) {
                            $property['USER_TYPE_DATA']=\CIBlockProperty::GetUserType($property['USER_TYPE']);
                        }
                        else {
                            $property['USER_TYPE_DATA']=null;
                        }
                        $this->properties[(int)$iblockId][$property['CODE']]=$property;
                    }
                }
            }
        }
        
        return $this->properties;
    }
    
    /**
     * Получить товары сайта
     * 
     * @param [] $filter фильтр выборки
     * @param [] $select дополнительные поля для выборки
     */
    public function getBxProducts($filter=[], $select=[])
    {
        $products=[];
        
        $select=array_merge($select, $this->config('site.products.select', ['ID', 'IBLOCK_ID', 'SECTION_ID', 'NAME']));
        if(!empty($filter['IBLOCK_ID'])) {
            $properties=$this->getProperties();
            if(!empty($properties[$filter['IBLOCK_ID']])) {
                foreach($properties[$filter['IBLOCK_ID']] as $propCode=>$prop) {
                     $select[]=$prop['SELECT_VALUE'];
                }
            }
            
            $priceProperties=$this->getPriceProperties();
            if(!empty($priceProperties[(int)$filter['IBLOCK_ID']]['PRICE'])) {
                $select[]='PROPERTY_' . $priceProperties[(int)$filter['IBLOCK_ID']]['PRICE'];
            }
        }
        $rs = \CIBlockElement::GetList(['NAME'=>'ASC'], $filter, false, false, $select);
        while ($product = $rs->Fetch()) {
            $products[$product['ID']] = $product;
        }
        
        return $products;
    }
    
    /**
     * Получить все артикулы FrontPad выгруженных товаров сайта
     * 
     * @return []
     */
    public function getBxProductFrontPadCodes()
    {
        $frontPadCodes=[];
        
        $iblocks=[];
        $sections=$this->getSections(true);
        foreach($sections as $section) {
            if(!in_array($section['IBLOCK_ID'], $iblocks)) {
                $iblocks[]=$section['IBLOCK_ID'];
                if($frontPadPropertyCode=$this->getFrontPadPropertyCode($section['IBLOCK_ID'])) {
                    $products=$this->getBxProducts(['IBLOCK_ID'=>$section['IBLOCK_ID']]);
                    foreach($products as $product) {
                        if(!empty($product["PROPERTY_{$frontPadPropertyCode}_VALUE"])) {
                            $frontPadCode=$product["PROPERTY_{$frontPadPropertyCode}_VALUE"];
                            if(!in_array($frontPadCode, $frontPadCodes)) {
                                $frontPadCodes[]=$frontPadCode;
                            }
                        }
                    }
                }
            }
        }
        
        return $frontPadCodes;        
    }
    
    /**
     * Получить наименование инфоблока
     * 
     * @param int $iblockId идентификатор инфоблока
     * 
     * @return string|null
     */
    protected function getIBlockName($iblockId)
    {
        $iblock=IblockTable::getById($iblockId)->fetch();
        
        if(!empty($iblock)) {
            return $iblock['NAME'];
        }
        
        return null;
    }
    
    /**
     * Получить разделы первого уровня инфоблока
     * 
     * @param int $iblockId идентификатор инфоблока
     * 
     * @return []
     */
    protected function getIBlockSections($iblockId)
    {
        return SectionTable::getList([
            'filter'=>['IBLOCK_ID'=>$iblockId],
            'order'=>['SORT', 'ID']
        ])->fetchAll();
    }
  
    /**
     * Получить код свойства артикула FrontPad
     * 
     * @param integer $iblockId идентификатор инфоблока
     * 
     * @return string|null
     */
    public function getFrontPadPropertyCode($iblockId)
    {
        $iblockId=(int)$iblockId;
        $properties=$this->getProperties();
        if(!empty($properties[$iblockId])) {
            foreach($properties[$iblockId] as $code=>$prop) {
                if(!empty($prop['FRONTPAD_PROPERTY_CODE_IS_MAIN'])) {
                    return $code;
                }
            }
        }
        
        return null;
    }
    
    /**
     * Получить параметры обновления цен
     * 
     * @return []
     */
    public function getPriceProperties()
    {
        if($this->priceProperties === null) {
            $this->priceProperties=$this->config('site.prices', []);
            foreach($this->priceProperties as $iblockId=>$pricePropertyCode) {
                if(is_string($pricePropertyCode)) {
                    $this->priceProperties[$iblockId]=['PRICE'=>$pricePropertyCode];
                }
            }
        }
        
        return $this->priceProperties;
    }
    
    /**
     * Получить код свойства основной цены товара
     * 
     * @param integer $iblockId идентификатор инфоблока
     * @param string $name имя параметра. По умолчанию "PRICE".
     * 
     * @return string|null
     */
    public function getProductPriceProperty($iblockId, $name='PRICE')
    {
        $iblockId=(int)$iblockId;
        $properties=$this->getPriceProperties();
        if(!empty($properties[$iblockId][$name])) {
            return $properties[$iblockId][$name];
        }
        
        return null;
    }
    
    /**
     * Сбросить статистику импорта
     * 
     */
    public function resetImportStats()
    {
        $this->stats=new \stdClass;
            
        $this->stats->total=0;
        $this->stats->added=0;
        $this->stats->updated=0;
        $this->stats->price_updated=0;
    }
    
    /**
     * Получить объект статистики
     * 
     * @return \stdClass
     */
    public function getImportStats()
    {
        return $this->stats;
    }
    
    /**
     * Импорт товаров из FrontPad
     *  
     */
    public function import($data)
    {
        $this->resetImportStats();
        
        $properties=$this->getProperties();
        $frontPadProducts=$this->getProducts();
        $products=[];        
        
        // Обновление товаров
        if(!empty($data['PRODUCTS'])) {
            foreach($data['PRODUCTS'] as $productId=>$product) {
                if(empty($product['IBLOCK_ID'])) {
                    continue;
                }
                
                $iblockId=$product['IBLOCK_ID'];
                
                if(!array_key_exists($iblockId, $products)) {
                    $products[$iblockId]=$this->getBxProducts(['IBLOCK_ID'=>$iblockId]);
                    $this->stats->total+=count($products[$iblockId]);
                }
                
                if(!empty($product['PROPERTIES']) && !empty($properties[$iblockId])) {
                    $frontPadPropertyCode=$this->getFrontPadPropertyCode($iblockId);
                    $priceProperties=$this->getProductPriceProperty($iblockId, 'PROPERTIES');
                    $props=[];
                    
                    foreach($product['PROPERTIES'] as $propertyCode=>$propertyValue) {
                        // обновление цен
                        if(($propertyCode == $frontPadPropertyCode) && !empty($propertyValue)) {
                            // основная цена
                            if($pricePropertyCode=$this->getProductPriceProperty($iblockId)) {
                                $price='';
                                if(!empty($frontPadProducts[$propertyValue])) {
                                    $price=$frontPadProducts[$propertyValue]['PRICE'];
                                }
                                
                                $lastPrice='';
                                if(!empty($products[$iblockId][$productId]["PROPERTY_{$pricePropertyCode}_VALUE"])) {
                                    $lastPrice=$products[$iblockId][$productId]["PROPERTY_{$pricePropertyCode}_VALUE"];
                                }
                                
                                if($lastPrice !== $price) {
                                    $props[$pricePropertyCode]=['VALUE'=>$price];
                                    
                                    $this->stats->price_updated++;
                                }                                
                            }                            
                        }
                            
                        // обновление цен у сложных свойств товара
                        if($priceProperties && is_array($propertyValue)) {
                            foreach($priceProperties as $productPropertyCode=>$pricePropertyCode) {
                                if($productPropertyCode == $propertyCode) {
                                    if(!empty($properties[$iblockId][$productPropertyCode]['FRONTPAD_PROPERTY_CODE'])) {
                                        $frontPadPropertyCode=$properties[$iblockId][$productPropertyCode]['FRONTPAD_PROPERTY_CODE'];
                                        foreach($propertyValue as $idx=>$propertyValueItem) {
                                            if(!empty($propertyValueItem[$frontPadPropertyCode]) && !empty($frontPadProducts[$propertyValueItem[$frontPadPropertyCode]])) {
                                                $lastPrice=$propertyValue[$idx][$pricePropertyCode];
                                                $propertyValue[$idx][$pricePropertyCode]=$frontPadProducts[$propertyValueItem[$frontPadPropertyCode]]['PRICE'];
                                                if($lastPrice !== $propertyValue[$idx][$pricePropertyCode]) {
                                                    $this->stats->price_updated++;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        
                        // обновление свойств
                        $lastValue=null;
                        if(!empty($products[$iblockId][$productId]["PROPERTY_{$propertyCode}_VALUE"])) {
                            $lastValue=$products[$iblockId][$productId]["PROPERTY_{$propertyCode}_VALUE"];
                        }
                        if(empty($lastValue) && empty($propertyValue)) {
                            continue;
                        }
                        if(!empty($propertyValue) && (crc32(serialize($lastValue)) === crc32(serialize($propertyValue)))) {
                            continue;
                        }
                        $props[$propertyCode]=['VALUE'=>$propertyValue];
                    }
                    
                    if(count($props) > 0) {
                        \CIBlockElement::SetPropertyValuesEx($productId, $iblockId, $props);
                        
                        \Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex($iblockId, $productId);
                        
                        $this->stats->updated++;
                    }
                }
            }
        }
        
        // Добавление новых товаров из сервиса FrontPad
        if(!empty($data['NEW_PRODUCT']['PRODUCTS'])) {
            $defaultSection=$this->detectSectionId($data['NEW_PRODUCT']['SECTION_DEFAULT']);
            
            $newProducts=[];
            foreach($data['NEW_PRODUCT']['PRODUCTS'] as $frontPadCode=>$product) {
                if(!empty($product['ADD']) && ($product['ADD'] == $frontPadCode)) {
                    $section=$this->detectSectionId($product['SECTION']) ?: $defaultSection;
                    if(!$section) {
                        continue;
                    }
                    $newProducts[$frontPadCode]=$section;
                }
            }
            
            if(!empty($newProducts)) {
                $sections=$this->getSections(true);
                foreach($newProducts as $frontPadCode=>$section) {
                    if(!empty($frontPadProducts[$frontPadCode])) {
                        $fields=[
                            'NAME'=>$frontPadProducts[$frontPadCode]['NAME'],
                            'ACTIVE'=>'Y',
                        ];
                        
                        $fields['CODE']=\CUtil::translit($fields['NAME'], 'ru');
                        
                        if($section['TYPE'] == self::SECTION_TYPE_IBLOCK) {
                            $fields['IBLOCK_ID']=$section['ID'];
                            $fields['IBLOCK_SECTION_ID']=false;
                        }
                        elseif($section['TYPE'] == self::SECTION_TYPE_SECTION) {
                            foreach($sections as $_section) {
                                if(($section['ID'] == $_section['ID']) && ($_section['TYPE'] == self::SECTION_TYPE_SECTION)) {
                                    $fields['IBLOCK_ID']=$_section['IBLOCK_ID'];
                                    $fields['IBLOCK_SECTION_ID']=$section['ID'];
                                    break;
                                }
                            }
                            
                            if(empty($fields['IBLOCK_ID'])) {
                                continue;
                            }
                        }
                        else {
                            continue;
                        }
                        
                        if($frontPadPropertyCode=$this->getFrontPadPropertyCode($fields['IBLOCK_ID'])) {
                            $exists=$this->getBxProducts(['IBLOCK_ID'=>$fields['IBLOCK_ID'], "PROPERTY_$frontPadPropertyCode"=>$frontPadCode]);
                            if(empty($exists)) {
                                $fields['PROPERTY_VALUES'][$frontPadPropertyCode]['VALUE']=$frontPadCode;
                                
                                if($pricePropertyCode=$this->getProductPriceProperty($fields['IBLOCK_ID'])) {
                                    $fields['PROPERTY_VALUES'][$pricePropertyCode]['VALUE']=$frontPadProducts[$frontPadCode]['PRICE'];
                                }                        
                                
                                $el=new \CIBlockElement;
                                if($elementId=$el->Add($fields)) {
                                    $this->stats->added++;
                                }
                                else {
                                    $fields['CODE']=\CUtil::translit($fields['NAME'], 'ru') . uniqid('-');
                                    if($elementId=$el->Add($fields)) {
                                        $this->stats->added++;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        
        return true;
    }
    
    /**
     * Определить тип идентификатора раздела
     * @param string $sectionId
     * @return []|false массив вида [
     *  "ID" идентификатор раздела или инфоблока
     *  "TYPE" тип идентификатора раздела (раздел или инфоблок).
     * ]
     * Если тип не определен будет возвращено false.
     */
    protected function detectSectionId($sectionId)
    {
        if(is_numeric($sectionId)) {
            return [
                'ID'=>(int)$sectionId,
                'TYPE'=>self::SECTION_TYPE_SECTION
            ];
        }
        elseif(strpos($sectionId, self::SECTION_TYPE_IBLOCK_PREFIX) === 0) {
            return [
                'ID'=>substr($sectionId, strlen(self::SECTION_TYPE_IBLOCK_PREFIX)),
                'TYPE'=>self::SECTION_TYPE_IBLOCK
            ];
        }
            
        return false;
    }
    
    /**
     * Получение списка товаров
     * 
     * @param string $sort поле сортировки товаров. 
     * Доступные значения сортировки: 
     *  "NAME" - по наименованию товаров,
     *  "FRONTPAD_CODE" - по артикулу FrontPad, 
     *  NULL - без сортировки
     * @param bool $reload загрузить заного товары из FrontPad
     * 
     * @return []
     */
    public function getProducts($sort='NAME', $reload=false)
    {
        if($reload || ($this->products === null)) {
            $products = [];
            
            $response = $this->send('get_products');
            if(!empty($response['result']) && ($response['result'] == 'success')) {
                $filter=$this->config('frontpad.filter');
                foreach($response['product_id'] as $idx=>$id) {
                    if(!$filter || $filter((int)$id)) {
                        $products[$id] = [
                            'FRONTPAD_CODE' => $id,
                            'NAME' => $response['name'][$idx],
                            'PRICE' => $response['price'][$idx]
                        ];
                    }
                }
            }
            
            if(in_array($sort, ['NAME', 'FRONTPAD_CODE'])) {
                uasort($products, function($a, $b) use ($sort) {
                    return strcmp($a[$sort], $b[$sort]);
                });
            }
            
            $this->products=$products;
        }
        
        return $this->products;
    }
    
    /**
     * Нормализация номера телефона
     * @param string $phone номер телефона
     * @return string
     */
    public function normalizePhone($phone)
    {
        $phone=preg_replace('/[^0-9]+/', '', $phone);
        
        if(!empty($phone)) {
            if($phone[0] !== '8') $phone='+'.$phone;
        }
        
        return $phone;
    }
    
    /**
     * Получить информацию о клиенте
     * @param string $phone номер телефона
     * 
     * @return array|mixed
     */
    public function getClient($phone)
    {
        $phone=$this->normalizePhone($phone);
        
        if(!empty($phone)) {
            $data=['client_phone'=>$phone];
            
            return $this->send('get_client', $data);
        }
        
        return [];
    }
    
    /**
     * Получить кол-во доступных баллов клиента
     * @param string $phone номер телефона
     * @return integer|false
     */
    public function getClientScore($phone)
    {
        $client=$this->getClient($phone);
        
        if(!isset($client['score'])) {
            return false; 
        }
        
        return $client['score'];
    }
    
    /**
     * Получить номер карты клиента
     * @param string $phone номер телефона
     * @return integer|false
     */
    public function getClientCard($phone)
    {
        $client=$this->getClient($phone);
        
        if(!isset($client['card'])) {
            return false;
        }
        
        return $client['card'];
    }
    
    /**
     * Проверка сертификата
     * @param string $certificate номер сертификата
     * @param string|false $returnValue возвращать значение сертификата. 
     * Нужно передавать имя параметра, напр. "sale" для процентной скидки. 
     */
    public function getCertificate($certificate, $returnValue=false)
    { 
        $result=$this->send('get_certificate', compact('certificate'));

        if($returnValue) {
            if(!empty($result['result']) && ($result['result'] == 'success') && !empty($result[$returnValue])) {
                return $result[$returnValue];
            }
            
            return false;
        }
        
        return $result;
    }
    
    /**
     * Отправка запроса в FrontPad
     * @param string $method метод
     * @param array $data данные
     * @return array
     */
    public function send($method, $data=[])
    {
        if(is_array($data)) {
            $data['secret']=$this->config('frontpad.apikey');
        }
        else {
            $data.='&secret=' . $this->config('frontpad.apikey');
        }
        
        $ch=curl_init('https://app.frontpad.ru/api/index.php?' . $method);
        curl_setopt_array($ch, [
            CURLOPT_FAILONERROR=>1,
            CURLOPT_RETURNTRANSFER=>1,
            CURLOPT_SSL_VERIFYPEER=>false,
            CURLOPT_SSL_VERIFYHOST=>false,
            CURLOPT_TIMEOUT=>30,
            CURLOPT_POST=>1,
            CURLOPT_POSTFIELDS=>$data
        ]);
        
        $result=curl_exec($ch);
        /*
        print_r($data);
        print_r($result);
        print_r(curl_error($ch));
        print_r(curl_getinfo($ch));
        /**/        
        curl_close($ch);
        
        return json_decode($result, true);
    }
    
    /**
     * Отправка заказ в FrontPad
     * 
     * @param \Bitrix\Main\Event $event 
     */
    public static function onNewOrder(\Bitrix\Main\Event $event)
    {
        $parameters = $event->getParameters();
        $orderId = array_shift($parameters);
        if(!empty($orderId)) {
            $order=\Bd\Deliverypizza\Entity\OrderTable::getList([
                'filter'=>['=ID'=>$orderId]
            ])->fetch();
            
            if($order) {
                $data = [
                    'name'=>$order['USER_NAME'],
                    'phone'=>static::i()->normalizePhone($order['USER_PHONE'])
                ];
                
                if($point=static::i()->config('frontpad.point')) {
                    $data['point']=$point;
                }                
                
                if((int)$order['DELIVERY_TIME_TYPE'] === 1) {
                    if(preg_match('/^\d{2}:\d{2}$/', $order['DELIVERY_TIME']) && !empty($order['DELIVERY_DATE'])) {
                        $year=(int)date('Y');
                        $month=(int)date('n');
                        $currentDay=(int)date('j');
                        $day=(int)$order['DELIVERY_DATE'];
                        if($day < $currentDay) {
                            $month++;
                            if($month > 12) {
                                $month=1;
                                $year++;
                            }
                        }
                        
                        if($month < 10) {
                            $month="0{$month}";
                        }
                        if($day < 10) {
                            $day="0{$day}";
                        }
                        
                        // указывается в формате ГГГГ-ММ-ДД ЧЧ:ММ:СС 
                        // Максимальный период предзаказа - 30 дней от текущей даты;
                        $data['datetime']="{$year}-{$month}-{$day} {$order['DELIVERY_TIME']}:00";
                    }
                }
                
                if($user=\Bd\Deliverypizza\Entity\UserTable::getList(['filter'=>['=USER_ID'=>$order['USER_ID']]])->fetch()) {
                    $data['mail']=$user['EMAIL'];
                }
                
                if($clientCard=static::i()->getClientCard($data['phone'])) {
                    $data['card']=$clientCard;
                }
                
                if(!empty($order['DELIVERY_PICKUP_ID'])) {
                    $delivery=ElementTable::getList([
                        'filter'=>['IBLOCK_ID'=>static::i()->config('bdpizza.pickup'), '=ID'=>$order['DELIVERY_PICKUP_ID']]
                    ])->fetch();
                    
                    $data['street']='Самовывоз' . (empty($delivery['NAME']) ? '' : ", {$delivery['NAME']}");
                        
                    if(\COption::GetOptionString('bd.deliverypizza','BD_CF_BASKET_PICKUP_DISCOUNT_ENABLED', '', SITE_ID) == 'Y') {
                        if($percent=(int)\COption::GetOptionInt('bd.deliverypizza', 'BD_CF_BASKET_PICKUP_DISCOUNT_VALUE', '', SITE_ID)) {
                            $data['sale']=(int)$percent;
                        }
                    }
                }
                else {
                    $data['street']=$order['STREET'];
                    $data['home']=$order['HOUSE'];
                    $data['et']=$order['FLOOR'];
                    $data['apart']=$order['APARTMENT'];
                }
                
                if(!empty($order['PROMO_CODE'])) {
                    if($discount=(int)static::i()->getCertificate($order['PROMO_CODE'], 'sale')) {
                        $sale=empty($data['sale']) ? 0 : $data['sale'];
                        $data['sale']+=$discount;
                    }
                }
                
                $data['descr'] = TruncateText($order['COMMENT'], 100);
                
                if(!empty($order['PAYMENT_TYPE'])) {
                    $payment=static::i()->config('frontpad.payment');
                    if($rsPay=\CIBlockElement::GetList(
                        ['NAME'=>'ASC'], 
                        ['IBLOCK_ID'=>$payment['iblock'], '=ID'=>$order['PAYMENT_TYPE']],
                        false,
                        false,
                        ['IBLOCK_ID', 'ID', "PROPERTY_{$payment['property']}"]
                    )) 
                    {
                        $pay=$rsPay->Fetch();
                        if(!empty($pay["PROPERTY_{$payment['property']}_VALUE"])) {
                            $data['pay']=(int)$pay["PROPERTY_{$payment['property']}_VALUE"];
                        }
                    }
                }                
                
                $basket=new \Bd\Deliverypizza\Models\Basket();
                $basketContent=$basket->getBasketContent(unserialize($order['BASKET_CONTENT']));
                
                $bxProducts=static::i()->getBxProducts(['ID'=>array_column($basketContent['products'], 'ID')], ['ID', 'PROPERTY_FRONTPAD_CODE']);
                if(!empty($bxProducts) && !empty($basketContent['products'])) {
                    $hasError=false;
                    
                    $bxProductFrontPadCodes=array_column($bxProducts, 'PROPERTY_FRONTPAD_CODE_VALUE', 'ID');
                    
                    $dataProducts=[];
                    foreach($basketContent['products'] as $product) {
                        if(!isset($bxProductFrontPadCodes[$product['ID']])) {
                            $hasError=true;
                            break;
                        }
                        $dataProduct=[
                            'FRONTPAD'=>$bxProductFrontPadCodes[$product['ID']],
                            'AMOUNT'=>$product['AMOUNT'],
                            'PRICE'=>$product['PRICE']
                        ];
                        
                        if(!empty($product['OPTIONS'])) {
                            $optionsHasError=false;
                            $dataProduct['MODIFICATORS']=[];
                            foreach($product['OPTIONS'] as $optionIdx=>$optionVariantIdx) {
                                if(is_numeric($optionVariantIdx)) {
                                    $optionNativeIdx=$optionIdx - 1;
                                    $optionVariantIdx=(int)$optionVariantIdx;
                                    if(empty($product['OPTIONS_NATIVE'][$optionNativeIdx][$optionVariantIdx]['FRONTPAD'])) {
                                        $optionsHasError=true;
                                        break;
                                    }
                                    
                                    $dataProduct['MODIFICATORS'][]=[
                                        'FRONTPAD'=>$product['OPTIONS_NATIVE'][$optionNativeIdx][$optionVariantIdx]['FRONTPAD'],
                                        'AMOUNT'=>1,
                                        'PRICE'=>(float)$product['OPTIONS_NATIVE'][$optionNativeIdx][$optionVariantIdx]['PRICE']
                                    ];
                                }                                
                            }
                            if($optionsHasError) {
                                $hasError=true;
                                break;
                            }                            
                        }
                        
                        $dataProducts[]=$dataProduct;
                    }
                        
                    if(!$hasError && !empty($dataProducts)) {
                        $data=http_build_query($data);
                        
                        $key=0;
                        foreach($dataProducts as $product) {
                            $data .= "&product[{$key}]=".$product['FRONTPAD'];
                            $data .= "&product_kol[{$key}]=".$product['AMOUNT'];
                            $data .= "&product_price[{$key}]=".$product['PRICE'];
                            if(!empty($product['MODIFICATORS'])) {
                                $parentKey=$key;
                                foreach($product['MODIFICATORS'] as $mod) {
                                    $key++;
                                    $data .= "&product[{$key}]=".$mod['FRONTPAD'];
                                    $data .= "&product_kol[{$key}]=".$mod['AMOUNT'];
                                    $data .= "&product_price[{$key}]=".$mod['PRICE'];
                                    $data .= "&product_mod[{$key}]=".$parentKey;
                                }
                            }
                            $key++;
                        }
                         
                        /**
                          Параметры запроса:
                          secret - секрет;
                          product – массив артикулов товаров;
                          product_kol – массив количества товаров;
                          product_mod – массив модификаторов товаров, где значение элемента массива является ключом родителя (товара в который добавлен модификатор);
                          product_price – массив цен товаров (установка цены при заказе через API возможна только для товаров с включенной опцией "Изменение цены при создании заказа";
                          score – баллы для оплаты заказа;
                          sale – скидка, положительное, целое число от 1 до 100;
                          sale_amount - скидка суммой, назначить к заказу можно один тип скидки - процентную или суммой;
                          card – карта клиента, положительное, целое число до 16 знаков;
                          street – улица, длина до 50 знаков;
                          home – дом, длина до 50 знаков;
                          pod – подъезд, длина до 2 знаков;
                          et – этаж, длина до 2 знаков;
                          apart – квартира, длина до 50 знаков;
                          phone – телефон, длина до 50 знаков;
                          mail – адрес электронной почты, длина до 50 знаков, доступно только с активной опцией автоматического сохранения клиентов;
                          descr – примечание, длина до 100 знаков;
                          name – имя клиента, длина до 50 знаков;
                          pay – отметка оплаты заказа, значение можно посмотреть в справочнике “Варианты оплаты”;
                          certificate – номер сертификата;
                          person – количество персон, длина 2 знака. Обратите внимание, привязка "автосписания" к количеству персон, переданному через api, не осуществляется;
                          channel – канал продаж, значение можно посмотреть в справочнике программы;
                          datetime – время “предзаказа”, указывается в формате ГГГГ-ММ-ДД ЧЧ:ММ:СС,
                          например 2016-08-15 15:30:00. Максимальный период предзаказа - 30 дней от текущей даты;
                          affiliate – филиал, значение можно посмотреть в справочнике программы;
                          point – точка продаж, значение можно посмотреть в справочнике программы.
                        */
                        $result=static::i()->send('new_order', $data);
                        if(!empty($result['result']) && ($result['result'] == 'success') && !empty($result['order_id'])) {
                            static::query('UPDATE `bd_order_pizza` SET `FRONTPAD_ORDER_ID`=' . $result['order_id'] . ' WHERE `ID`=' . $orderId);
                            return true;
                        }
                        return false;
                    }
                }                
            }            
        }
        
        return false;
    }
    
    public function getOrderStatus($orderId)
    {
        $result=static::i()->send('get_status', ['order_id'=>$orderId]);
        
        if(!empty($result['result']) && ($result['result'] == 'success')) {
            return $result['status'];
        }
        
        return null;
    }
    
    /**
     * Обновить статусы заказов
     * 
     * @param [] &$orders заказы
     */
    public function updateOrderStatuses(&$orders)
    {
        $orderIds=array_column($orders, 'ID');
        if(!empty($orderIds)) {
            $orderStatuses=$this->config('frontpad.order_statuses', []);
            $orderDoneStatuses=$this->config('frontpad.order_done_statuses', []);
            
            $_orders=static::query('SELECT `ID`, `STATUS`, `FRONTPAD_ORDER_ID` FROM `bd_order_pizza` WHERE `ID` IN ('.implode(',', $orderIds).')')->fetchAll();
            foreach($_orders as $_order) {
                if(!empty($_order['FRONTPAD_ORDER_ID']) && !in_array((int)$_order['STATUS'], $orderDoneStatuses)) {
                    if($status=$this->getOrderStatus($_order['FRONTPAD_ORDER_ID'])) {
                        $_statusIds=[];
                        foreach($orderStatuses as $statusId=>$frontPadStatus) {
                            if($status == $frontPadStatus) {
                                $_statusIds[]=$statusId;
                            }
                        }
                        if(!in_array((int)$_order['STATUS'], $_statusIds)) {
                            $bxStatus=(int)reset($_statusIds);
                            static::query('UPDATE `bd_order_pizza` SET `STATUS`='.$bxStatus.' WHERE `ID`='.$_order['ID']);
                            foreach($orders as $orderIdx=>$order) {
                                if($order['ID'] == $_order['ID']) {
                                    $orders[$orderIdx]['STATUS']=$bxStatus;
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    
    public function getWorkTimeFrom()
    {
        $h=$this->getWorkTimeFromHour();
        if($h < 10) $h="0{$h}";
        $m=$this->getWorkTimeFromMinute();
        if($m < 10) $m="0{$m}";
        return "{$h}:{$m}";
    }
    
    public function getWorkTimeFromHour()
    {
        $worktime=$this->config('frontpad.worktime.from');
        return $worktime[0];
    }
    
    public function getWorkTimeFromMinute()
    {
        $worktime=$this->config('frontpad.worktime.from');
        return $worktime[1];
    }
    
    public function getWorkTimeTo()
    {
        $h=$this->getWorkTimeToHour();
        if($h < 10) $h="0{$h}";
        $m=$this->getWorkTimeToMinute();
        if($m < 10) $m="0{$m}";
        return "{$h}:{$m}";
    }
    
    public function getWorkTimeToHour()
    {
        $worktime=$this->config('frontpad.worktime.to');
        return $worktime[0];
    }
    
    public function getWorkTimeToMinute()
    {
        $worktime=$this->config('frontpad.worktime.to');
        return $worktime[1];
    }
    
    public function isCashClose()
    {
        $hour=(int)date('G') + $this->config('frontpad.worktime.timezone');
        $min=(int)date('i');
        if($hour >= $this->getWorkTimeFromHour()) {
            if(($hour === $this->getWorkTimeFromHour()) && ($min < $this->getWorkTimeFromMinute())) {
                return true;
            }
            elseif(($hour === $this->getWorkTimeToHour()) && ($min < $this->getWorkTimeToMinute())) {
                return false;
            }
            elseif($hour < $this->getWorkTimeToHour()) {
                return false;
            }
        }
        return true;        
    }
}
