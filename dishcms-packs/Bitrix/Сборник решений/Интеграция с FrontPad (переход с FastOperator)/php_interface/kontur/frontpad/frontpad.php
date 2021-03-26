<?php
/**
 * Интеграция FrontPad и Fast Operator
 * 
 * http://frontpad.ru/help/?help=1002
 * 
 * Добавить в init.php
 * require_once dirname(__FILE__) . '/kontur/frontpad/frontpad.php';
 * \kontur\frontpad\FrontPad::init();
 */
namespace kontur\frontpad;

use Bitrix\Main;
use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;

class FrontPad
{
	const SECTION_PIZZA_ID = 92;
    const PRODUCTS_IBLOCK_ID = 5;
    const PROPERTY_FO_PRICE_ID = 6;
    const PROPERTY_FRONTPAD_CODE_ID = 34;
    const PROPERTY_FRONTPAD_CODE_CODE = 'FRONTPAD_CODE';
    const SESSION_IMPORT_HASH_VAR = 'FRONTPAD_IMPORT_HASH';
    
    const INGRIDIENTY_IBLOCK_ID = 4;
    const INGRIDIENTY_PROPERTY_FO_PRICE_ID = 2;
    const INGRIDIENTY_PROPERTY_FRONTPAD_CODE_ID = 35;
    const INGRIDIENTY_PROPERTY_FRONTPAD_CODE_CODE = 'FRONTPAD_CODE';
    const INGRIDIENTY_SECTION_KEY = 'ingridienty';
    
    const INGRIDIENTY_PROPERTY_IS_BATTER_ID = 36;
    const INGRIDIENTY_PROPERTY_IS_BATTER_CODE = 'IS_BATTER';
    const INGRIDIENTY_SECTION_IS_THIN_BATTER = 'IS_THIN_BATTER';
    const INGRIDIENTY_SECTION_IS_FAT_BATTER = 'IS_FAT_BATTER';
    const INGRIDIENTY_PROPERTY_IS_BATTER_THIN_VALUE_ID = 251; // 1040 (dev), 251 (local)
    const INGRIDIENTY_PROPERTY_IS_BATTER_FAT_VALUE_ID = 252; // 1041 (dev), 252 (local)
    
    /**
     * @var string секрет
     */
    private static $secret='bZA2nRrAYGQeS5D49dKe8FhTKEBYe9ehZF7ahiA6Tb9B6Ds5G49Fzfk7NQRTNZ4As2AD92yT4yhYZBQtsN2QY9KyQrbnb6QtSaYS3eARBGBk8bGnRt5AtDY7SZiYR2bz32dQ5Dtt9TG7ZbiBzb3RFHNktZ79K8zAYBbQ5z84dFZ3G7r69dHENiR78K7GHs7tT97Ky6DAAkn7hF4fHBNiKreG4bdAy9Sb9QQtGdT7s85BDTHafhiTBsnrtt';
    
    /**
     * Инициализация FrontPad
     */
    public static function init()
    {
        $eventManager = Main\EventManager::getInstance();
        $eventManager->addEventHandler('resta.aventa', 'OnAfterSetOrder', ['\kontur\frontpad\FrontPad', 'newOrder']);
        $eventManager->addEventHandler('main', 'OnBuildGlobalMenu', ['\kontur\frontpad\FrontPad', 'buildAdminMenu']);
        
        $admin_import_file = $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/admin_frontpad_import.php';
        if(!is_file($admin_import_file)) {
            copy(dirname(__FILE__).'/admin/admin_frontpad_import.php', $admin_import_file);
        }
    }
    
    /**
     * Способы оплаты 
     * @return array массив способов оплаты вида 
     * array(bitrixPaySystemId=>frontPadPaySystemId)
     */
    public static function getPaySystems()
    {
        return [
            '100000001' => 1, // Наличные
            '100000002' => 2, // Картой курьеру
            '100000003' => 627 // On-line оплата
        ];
    }
    
    public static function getPaySystemIdByPayMethodId($payMethodId)
    {
        $paySystems = static::getPaySystems();
        if(!empty($paySystems[$payMethodId])) {
            return $paySystems[$payMethodId];
        }
        return null;
    }
    
    /**
     * Добавление пунктов меню в раздел администрирования
     * @see \Bitrix\Main\Event main.OnBuildGlobalMenu
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
            //print_r($arMenu);
        }
    }
    
    public static function import()
    {
        if(!empty($_POST['RUN']) && ($_POST['RUN']=='Y')) { 
            if(empty($_SESSION[self::SESSION_IMPORT_HASH_VAR]) || ($_SESSION[self::SESSION_IMPORT_HASH_VAR] != md5(serialize($_POST)))) {
                $_SESSION[self::SESSION_IMPORT_HASH_VAR] = md5(serialize($_POST));
                return static::updateProducts(self::PRODUCTS_IBLOCK_ID, $_POST['FRONTPAD_IMPORT_DATA'], $_POST['FRONTPAD_IMPORT_DATA_INGRIDIENTY']);
            }
            else {
                LocalRedirect($_SERVER['REQUEST_URI']);
            }
        }
        else {
            $_SESSION[self::SESSION_IMPORT_HASH_VAR]=null;
        }
    }
    
    /**
     * Новый заказ
     * @param \Bitrix\Main\Event $event объект события resta.aventa::OnAfterSetOrder
     */
    public static function newOrder(\Bitrix\Main\Event $event)
    {
        $parameters = $event->getParameters();
        $orderId = array_shift($parameters);
        if(!empty($orderId)) {
            \Bitrix\Main\Loader::includeModule("iblock");
            
            $globalDiscount=intval(\Bitrix\Main\Config\Option::get('resta.aventa','global_discount_'.SITE_ID,''));
            $rs=\Resta\Aventa\OrderTable::getById($orderId);
            $arOrder=$rs->fetch();
            
            if(!$arOrder) {
                return false;
            }
            
            $frontPadCodes = []; 
            $rs = \CIBlockProperty::GetPropertyEnum(self::PROPERTY_FRONTPAD_CODE_ID, array("SORT"=>"ASC"), array('IBLOCK_ID'=>self::PRODUCTS_IBLOCK_ID));
            while($frontPadCode = $rs->Fetch()) {
                $frontPadCodes[$frontPadCode['XML_ID']] = $frontPadCode['VALUE'];
            }
            
            $ingridientyFrontPadCodes = [];
            $rs = \CIBlockProperty::GetPropertyEnum(self::INGRIDIENTY_PROPERTY_FRONTPAD_CODE_ID, array("SORT"=>"ASC"), array('IBLOCK_ID'=>self::INGRIDIENTY_IBLOCK_ID));
            while($ingridientyFrontPadCode = $rs->Fetch()) {
                $ingridientyFrontPadCodes[$ingridientyFrontPadCode['XML_ID']] = $ingridientyFrontPadCode['VALUE'];
            }
            
            $sid=$arOrder['SITE_ID'];
            $foPropertyCode=\Bitrix\Main\Config\Option::get('resta.aventa','iblock_property_code_'.$sid,0);
            
            $arProducts = [];
            
            $rs = \Resta\Aventa\Basket::getList(array('filter'=>array('ORDER_ID'=>$arOrder['ID'])));
            $arBasket=array();
            $arIDs=array();
            $arIngrIDs=array();
            $arRemIngrIDs=array();
            while($arItems=$rs->fetch()){
                $basketId = $arItems['ID'];                
                $productId = intval($arItems['PRODUCT_ID']);
                $foCode = $arItems['PROPS']['FO'];
                $discount=floatval($arItems['DISCOUNT']);
                
                if(empty($frontPadCodes[$foCode])) { 
                    return false; 
                }
                
                $arProducts[$basketId]=array(
                    'ID'			=> $arItems['ID'],
                    'QUANTITY'		=> $arItems['QUANTITY'],
                    'NAME'			=> $arItems['NAME'],
                    'PRICE'			=> $arItems['PRICE'],
                    'PRODUCT_ID'	=> $arItems['PRODUCT_ID'],
                    'FO_CODE'		=> $arItems['PROPS']['FO'],
                    'PRICE_TYPE'	=> $arItems['PROPS']['PRICE_TYPE'],
                    'PARTS'			=> $arItems['PROPS']['PARTS'],
                    'REMARK'		=> $arItems['PROPS']['REMARK'],
                    'ACTION_NAME'	=> $arItems['PROPS']['ACTION_NAME'],
                    'ACTION'		=> $arItems['PROPS']['ACTION'],
                    'OLD_PRICE'		=> $arItems['PROPS']['OLD_PRICE'],
                    'CLEAR_PRICE'	=> $arItems['PROPS']['CLEAR_PRICE'],
                    'ADD_INGR'		=> array(),
                    'REM_INGR'		=> array(),
                    'DISCOUNT'		=> $discount,
                    'FRONTPAD_CODE' => $frontPadCodes[$foCode]
                );
                
                $arIDs[]=$arItems['PRODUCT_ID'];
                
                foreach($arItems['PROPS']['ADD_INGR'] as $adIngr){
                    if($adIngr['Q']>0){
                        $arIDs[]=$adIngr['ID'];
                        $arIngrIDs[$basketId][$adIngr['ID']]=$adIngr['ID'];
                        $arProducts[$basketId]['ADD_INGR'][$adIngr['ID']]=$adIngr['Q'];
                    }
                }
                foreach($arItems['PROPS']['REMOVE_INGR'] as $rmIngr){
                    $arProducts[$basketId]['REM_INGR'][]=$rmIngr;
                    $arIDs[]=$rmIngr;
                    $arRemIngrIDs[$basketId][$rmIngr]=$rmIngr;
                }
            }
            
            if(empty($arIDs)) {
                return false;
            }
            
            $rs=\CIBlockElement::GetList(array(),array('ID'=>$arIDs));
            $arEl=array();
            while($elS=$rs->GetNextElement()){
                $el=$elS->GetFields();
                $el['PROPS']=$elS->GetProperties();
                $foValues = $el['PROPS'][$foPropertyCode]['VALUE'];
                if(empty($foValues) || !is_array($foValues)) {
                    return false;
                }
                foreach($foValues as $foCode=>$foValue) {
                    $product=null;
                    foreach($arProducts as $basketId=>$arProduct) {
                        if($arProduct['FO_CODE'] === $foCode) {
                            $product=[$basketId, $arProduct];
                            break;
                        }
                    }
                    if(!empty($product)) {
                        $arProducts[$product[0]]['PRICE']=$foValue['PRICE'];
                    }
                    else {
                        foreach($arIngrIDs as $parentBasketId=>$parentIngrs) {
                            foreach($parentIngrs as $parentIngrId) {
                                if($el['ID'] == $parentIngrId) {
                                    $arProducts[$parentBasketId]['ADD_INGR'][$parentIngrId] = [
                                        'ID'=>$el['ID'],
                                        'NAME'=>$el['NAME'],
                                        'FO_CODE'=> $foValue['CODE'],
                                        'PRICE'=>$foValue['PRICE'],
                                        'QUANTITY'=>$arProducts[$parentBasketId]['ADD_INGR'][$parentIngrId]?:1
                                    ];
                                }
                            }
                        }
                        foreach($arRemIngrIDs as $parentBasketId=>$parentRemIngrs) {
                            foreach($parentRemIngrs as $parentRemIngrId) {
                                if($el['ID'] == $parentRemIngrId) {
                                    $arProducts[$parentBasketId]['REM_INGR'][$parentRemIngrId] = [
                                        'ID'=>$el['ID'],
                                        'NAME'=>$el['NAME'],
                                        'FO_CODE'=> $foValue['CODE'],
                                        'PRICE'=>$foValue['PRICE'],
                                        'QUANTITY'=>1
                                    ];
                                }
                            }
                        }
                    }
                }                
            }
            
            $rs=\Resta\Aventa\PaySystem::getList();
            $paySystem=false;
            while($ps=$rs->fetch()){
                if($ps['ID']==$arOrder['PAY_SYSTEM_ID']) $paySystem=$ps;
            }
            //$persentD=round(100*$arOrder['DISCOUNT']/($arOrder['PRICE']+$arOrder['DISCOUNT']),2);
            $arProps=unserialize($arOrder['PROPS']);
            $arOrder['USER']=($arOrder['USER']>0?$arOrder['USER']:'');
            if(intval($arOrder['USER'])==$arOrder['USER']){
                $rs_= \Bitrix\Main\UserTable::getById($arOrder['USER']);
                $arUser=$rs_->fetch();
                $arOrder['USER']=$arUser['LOGIN'];
            }
            $matPhone=\Resta\Aventa\Tools::getClearPhone($arOrder['PHONE'],true);
            $dSamov=\Bitrix\Main\Config\Option::get("resta.aventa","action_samovivoz_".$sid);
            $arDep=\Resta\Aventa\Department::getLineArray($sid);
                        
            $arProps['dType']=$arProps['dType']==$dSamov?2:1;
            
            $isValid = true;
            $products = [];
            foreach($arProducts as $basketId=>$el){
                $products[$basketId]['ATR']=array(
                    'Name'=>$el['NAME'],
                    'Code'=>$el['FO_CODE'], 
                    'Price'=>$el['CLEAR_PRICE']?$el['CLEAR_PRICE']:$el['PRICE'], 
                    'Qty'=>$el['QUANTITY'],
                    'FrontPadCode'=>$el['FRONTPAD_CODE']
                );
                
                if(!empty($el['ADD_INGR'])) {
                    $products[$basketId]['MODIFICATORS']=[];
                    foreach($el['ADD_INGR'] as $ingrID=>$arIngr) {
                        $products[$basketId]['MODIFICATORS'][] = [
                            'Name'=>$arIngr['NAME'],
                            'Code'=>$arIngr['FO_CODE'],
                            'Price'=>$arIngr['PRICE'],
                            'Qty'=>$arIngr['QUANTITY'],
                            'FrontPadCode'=>$ingridientyFrontPadCodes[$arIngr['FO_CODE']]
                        ];
                    }
                }
            }
            
            $brand=intval(\Bitrix\Main\Config\Option::get('resta.aventa','brand_'.$sid));
            $arRes=array('Order'=>array(
                'ATR'	=> array(
                    'PayMethod'=>$paySystem['FO_CODE'], 
                    'OrderId'=>$arOrder['ID'], 
                    'Type'=>$arProps['dType'], 
                    'Brand'=>$brand, 
                    'DeliveryPrice'=>$arOrder['DELIVERY'],
                    'DiscountAmount'=>($arProps['coupon_discount_type']=='SUM'?$arProps['coupon_discount']:0), 
                    'RemarkMoney'=>floatval($arProps['oddMoney']), 
                    'QtyPerson'=>intval($arProps['countPersons']), 
                    'Department'=>$arProps['dDepartment'], 
                    'TimePlan'=> $arProps['devTime'], 
                    'Remark'=>$arProps['remark'],
                    'Score'=>$arProps['Score'],
                    'Certificate'=>$arProps['Certificate'],
                ),
                'VALUE'	=> array(
                    'Customer'	=> array('ATR'=>array('Login'=>$arOrder['USER'],'Name'=>$arProps['userName'],'DiscountCard'=>$arProps['DiscountCard'])),
                    'Phone'		=> array('ATR'=>array('Code'=>$matPhone[0], 'Number'=>$matPhone[1], 'Remark'=>'')),
                    //'Remark'	=> array('VALUE'=>($arProps['remark'])),
                    'Address'	=> array('ATR'=>array(
                        'CityName'		=> $arProps['CityName']?$arProps['CityName']:\Bitrix\Main\Config\Option::get('resta.aventa','city_def_'.$sid),
                        'StationName'	=> $arProps['StationName'],
                        'StreetName'	=> $arProps['StreetName'],
                        'House'			=> $arProps['House'],
                        'Corpus'		=> $arProps['Corpus'],
                        'Building'		=> $arProps['Building'],
                        'Floor'			=> intval($arProps['Floor']),
                        'Flat'			=> $arProps['Flat'],
                        'Porch'			=> $arProps['Porch'],
                        'DoorCode'		=> $arProps['DoorCode'],
                        'RoomType'		=> $arProps['RoomType'],
                    ),
                        'VALUE'=>array('Remark'=>array('VALUE'=>$arProps['Remark']))
                    ),
                    'Products'	=> array('VALUE'=>$products),
                ),
            ));
        }
        
        if(!$isValid) {
            return false;
        }
        
        $data = [];
        
        $data['name'] = $arRes['Order']['VALUE']['Customer']['ATR']['Name'];
        
        $arAddress = $arRes['Order']['VALUE']['Address']['ATR'];
        $data['street'] = $arAddress['StreetName'];
        $data['home'] = $arAddress['House'];
        $data['pod'] = $arAddress['Porch'];
        $data['et'] = $arAddress['Floor'];
        $data['apart'] = $arAddress['Flat'];
        
        $arPhone = $arRes['Order']['VALUE']['Phone']['ATR'];
        $data['phone'] = '7' . $arPhone['Code'] . $arPhone['Number'];
        $data['mail'] = $arUser['EMAIL'];
        
        $data['descr'] = TruncateText($arRes['Order']['ATR']['Remark'], 100);
        
        $data['pay'] = static::getPaySystemIdByPayMethodId($arRes['Order']['ATR']['PayMethod']);
        
        if(!empty($arRes['Order']['ATR']['Score'])) {
            $data['score'] = $arRes['Order']['ATR']['Score'];
        }
        
        if(!empty($arRes['Order']['ATR']['Certificate'])) {
            /* 
            $certificate = static::getCertificate($arRes['Order']['ATR']['Certificate']);
            if($certificate['result'] !== 'error') {
                if(!empty($certificate['product_id'])) {
                    foreach($arRes['Order']['VALUE']['Products']['VALUE'] as $product) {
                        if($product['ATR']['FrontPadCode'] == $certificate['product_id']) {
                            $data['certificate'] = $arRes['Order']['ATR']['Certificate'];
                        }
                    }
                }
                else {
                    $data['certificate'] = $arRes['Order']['ATR']['Certificate'];
                }
            }
            */
            $data['certificate'] = $arRes['Order']['ATR']['Certificate'];
        } 
        
        $data = http_build_query($data);
        $key=0;
        foreach($arRes['Order']['VALUE']['Products']['VALUE'] as $product) {
            $data .= "&product[{$key}]=".$product['ATR']['FrontPadCode'];
            $data .= "&product_kol[{$key}]=".$product['ATR']['Qty'];
            $data .= "&product_price[{$key}]=".$product['ATR']['Price'];
            if(!empty($product['MODIFICATORS'])) {
                $parentKey=$key;
                foreach($product['MODIFICATORS'] as $mod) {
                    if(empty($mod['FrontPadCode'])) {
                        return false;
                    }
                    $key++;
                    $data .= "&product[{$key}]=".$mod['FrontPadCode'];
                    $data .= "&product_kol[{$key}]=".$mod['Qty'];
                    $data .= "&product_price[{$key}]=".$mod['Price'];
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
        return static::send('new_order', $data);
    }
    
    /**
     * Добавление/обновление товаров из сервиса FrontPad
     * @param integer $iblockId идентификатор инфоблока товаров
     */
    public static function updateProducts($iblockId, $updateData=[], $updateDataIngridienty=[])
    {
        $countUpdate = 0;
        $countPriceUpdate = 0;
        $countNew = 0;
        $ingridientyCountUpdate = 0;
        $ingridientyCountPriceUpdate = 0;
        
        $frontPadCodeEnumValuesUpdate = [];
        
        $frontPadProducts = static::getProducts();
        
        if(!empty($updateData['FRONTPAD_CODE'])) {
            $siteProducts = static::getSiteProducts($iblockId);
            foreach($updateData['FRONTPAD_CODE'] as $productId=>$productData) {
                $propertyPriceValues = [self::PROPERTY_FO_PRICE_ID=>['VALUE'=>[]]];
                foreach($productData as $priceId=>$prices) {
                    foreach($prices as $priceCode=>$frontPadCode) {
                        if(!empty($frontPadCode)) {
                            $frontPadCodeEnumValuesUpdate[] = [
                                'PROPERTY_ID'=>self::PROPERTY_FRONTPAD_CODE_ID, 
                                'XML_ID'=>$priceCode,
                                'VALUE'=>$frontPadCode
                            ];
                            
                            if(!empty($siteProducts[$productId]['PROPERTY_FO_PRICE_VALUE'])) {
                                if(empty($propertyPriceValues[self::PROPERTY_FO_PRICE_ID]['VALUE'])) {
                                    $propertyPriceValues[self::PROPERTY_FO_PRICE_ID]['VALUE'] = $siteProducts[$productId]['PROPERTY_FO_PRICE_VALUE'];
                                }
                                $propertyPriceValues[self::PROPERTY_FO_PRICE_ID]['VALUE'][$priceCode]['PRICE'] = $frontPadProducts[$frontPadCode]['PRICE'];
                                
                                $combo=[];
                                if(!empty($updateData['IS_BATTER_THIN'][$productId][$priceId][$priceCode])) {
                                    $combo[]=$updateData['IS_BATTER_THIN'][$productId][$priceId][$priceCode];
                                }
                                if(!empty($updateData['IS_BATTER_FAT'][$productId][$priceId][$priceCode])) {
                                    $combo[]=$updateData['IS_BATTER_FAT'][$productId][$priceId][$priceCode];
                                }
                                
                                if(!empty($combo)) {
                                    $propertyPriceValues[self::PROPERTY_FO_PRICE_ID]['VALUE'][$priceCode]['PARTS']=serialize(['Combo'=>$combo]);
                                }
                                else {
                                    $propertyPriceValues[self::PROPERTY_FO_PRICE_ID]['VALUE'][$priceCode]['PARTS']='';
                                }
                                
                                if($siteProducts[$productId]['PROPERTY_FO_PRICE_VALUE'][$priceCode]['PRICE'] != $frontPadProducts[$frontPadCode]['PRICE']) {
                                    $countPriceUpdate++;
                                }
                            }
                            
                            $countUpdate++;
                        }
                    }
                }
                if(!empty($propertyPriceValues[self::PROPERTY_FO_PRICE_ID]['VALUE'])) {
                    \CIBlockElement::SetPropertyValuesEx($productId, $iblockId, $propertyPriceValues);
                }
            }
        }
        
        if(!empty($updateData['NEW_PRODUCT'])) {
            $el = new \CIBlockElement;
            foreach($updateData['NEW_PRODUCT'] as $frontPadCode) {
                if(!empty($frontPadProducts[$frontPadCode])) {
                    $priceCode = "100000{$frontPadCode}";
                    $iblockSectionId=false;
                    if(!empty($updateData['NEW_PRODUCT_SECTION_DEFAULT'])) {
                        $iblockSectionId=$updateData['NEW_PRODUCT_SECTION_DEFAULT'];
                    }
                    if(!empty($updateData['NEW_PRODUCT_SECTION'][$frontPadCode])) {
                        $iblockSectionId=$updateData['NEW_PRODUCT_SECTION'][$frontPadCode];
                    }
                    
                    if(!empty($iblockSectionId) && !in_array($iblockSectionId, [self::INGRIDIENTY_SECTION_KEY, self::INGRIDIENTY_SECTION_IS_THIN_BATTER, self::INGRIDIENTY_SECTION_IS_FAT_BATTER])) {
                        $arFields = [
                            'IBLOCK_ID'=>$iblockId,
                            'IBLOCK_SECTION_ID'=>$iblockSectionId,
                            'NAME'=>$frontPadProducts[$frontPadCode]['NAME'],
                            'ACTIVE'=>'Y',
                            'PROPERTY_VALUES'=>[
                                'FO_PRICE'=>[
                                    'VALUE'=>[['CODE'=>$priceCode, 'PRICE'=>$frontPadProducts[$frontPadCode]['PRICE']]]
                                ]
                            ]
                        ];
                        
                        if($elementId = $el->Add($arFields)) {
                            $frontPadCodeEnumValuesUpdate[] = [
                                'PROPERTY_ID'=>self::PROPERTY_FRONTPAD_CODE_ID,
                                'XML_ID'=>$priceCode,
                                'VALUE'=>$frontPadCode
                            ];
                            $countNew++;
                        }
                    }
                }
            }
        }
        
        $CIBlockProp = new \CIBlockProperty;
        $CIBlockProp->UpdateEnum(self::PROPERTY_FRONTPAD_CODE_ID, $frontPadCodeEnumValuesUpdate);
        
        // Ингридиенты/Начинки
        $frontPadCodeEnumValuesUpdate = [];
        if(!empty($updateDataIngridienty['FRONTPAD_CODE'])) {
            $siteProducts = static::getSiteProducts(self::INGRIDIENTY_IBLOCK_ID);
            foreach($updateDataIngridienty['FRONTPAD_CODE'] as $productId=>$productData) {
                $propertyPriceValues = [self::INGRIDIENTY_PROPERTY_FO_PRICE_ID=>['VALUE'=>[]]];
                foreach($productData as $priceId=>$prices) {
                    foreach($prices as $priceCode=>$frontPadCode) {
                        if(!empty($frontPadCode)) {
                            $frontPadCodeEnumValuesUpdate[] = [
                                'PROPERTY_ID'=>self::INGRIDIENTY_PROPERTY_FRONTPAD_CODE_ID,
                                'XML_ID'=>$priceCode,
                                'VALUE'=>$frontPadCode
                            ];
                            
                            if(!empty($siteProducts[$productId]['PROPERTY_FO_PRICE_VALUE'])) {
                                if($siteProducts[$productId]['PROPERTY_FO_PRICE_VALUE'][$priceCode]['PRICE'] != $frontPadProducts[$frontPadCode]['PRICE']) {
                                    if(empty($propertyPriceValues[self::INGRIDIENTY_PROPERTY_FO_PRICE_ID]['VALUE'])) {
                                        $propertyPriceValues[self::INGRIDIENTY_PROPERTY_FO_PRICE_ID]['VALUE'] = $siteProducts[$productId]['PROPERTY_FO_PRICE_VALUE'];
                                    }
                                    $propertyPriceValues[self::INGRIDIENTY_PROPERTY_FO_PRICE_ID]['VALUE'][$priceCode]['PRICE'] = $frontPadProducts[$frontPadCode]['PRICE'];
                                    $ingridientyCountPriceUpdate++;
                                }
                            }
                            
                            $ingridientyCountUpdate++;
                        }
                    }
                }
                if(!empty($propertyPriceValues[self::INGRIDIENTY_PROPERTY_FO_PRICE_ID]['VALUE'])) {
                    \CIBlockElement::SetPropertyValuesEx($productId, self::INGRIDIENTY_IBLOCK_ID, $propertyPriceValues);
                }
            }
        }
        
        if(!empty($updateData['NEW_PRODUCT'])) {
            $el = new \CIBlockElement;
            foreach($updateData['NEW_PRODUCT'] as $frontPadCode) {
                if(!empty($frontPadProducts[$frontPadCode])) {
                    $priceCode = "100000{$frontPadCode}";
                    $iblockSectionId=false;
                    if(!empty($updateData['NEW_PRODUCT_SECTION_DEFAULT'])) {
                        $iblockSectionId=$updateData['NEW_PRODUCT_SECTION_DEFAULT'];
                    }
                    if(!empty($updateData['NEW_PRODUCT_SECTION'][$frontPadCode])) {
                        $iblockSectionId=$updateData['NEW_PRODUCT_SECTION'][$frontPadCode];
                    }
                    
                    if(!empty($iblockSectionId)) {
                        $isBatterPropertyValueId=null;
                        if($iblockSectionId == self::INGRIDIENTY_SECTION_IS_THIN_BATTER) {
                            $isBatterPropertyValueId=self::INGRIDIENTY_PROPERTY_IS_BATTER_THIN_VALUE_ID;
                            $iblockSectionId=self::INGRIDIENTY_SECTION_KEY;
                        }
                        elseif($iblockSectionId == self::INGRIDIENTY_SECTION_IS_FAT_BATTER) {
                            $isBatterPropertyValueId=self::INGRIDIENTY_PROPERTY_IS_BATTER_FAT_VALUE_ID;
                            $iblockSectionId=self::INGRIDIENTY_SECTION_KEY;
                        }
                        
                        if($iblockSectionId == self::INGRIDIENTY_SECTION_KEY) {
                            $propertyValues=[
                                'FO_PRICE'=>[
                                    'VALUE'=>[['CODE'=>$priceCode, 'PRICE'=>$frontPadProducts[$frontPadCode]['PRICE']]]
                                ]
                            ];
                            if(!empty($isBatterPropertyValueId)) {
                                $propertyValues[self::INGRIDIENTY_PROPERTY_IS_BATTER_CODE]=$isBatterPropertyValueId;
                            }
                            $arFields = [
                                'IBLOCK_ID'=>self::INGRIDIENTY_IBLOCK_ID,
                                'IBLOCK_SECTION_ID'=>'',
                                'NAME'=>$frontPadProducts[$frontPadCode]['NAME'],
                                'ACTIVE'=>'Y',
                                'PROPERTY_VALUES'=>$propertyValues
                            ];
                            
                            if($elementId = $el->Add($arFields)) {
                                $frontPadCodeEnumValuesUpdate[] = [
                                    'PROPERTY_ID'=>self::INGRIDIENTY_PROPERTY_FRONTPAD_CODE_ID,
                                    'XML_ID'=>$priceCode,
                                    'VALUE'=>$frontPadCode
                                ];
                                $countNew++;
                            }
                        }
                    }
                }
            }
        }
        
        $CIBlockProp = new \CIBlockProperty;
        $CIBlockProp->UpdateEnum(self::INGRIDIENTY_PROPERTY_FRONTPAD_CODE_ID, $frontPadCodeEnumValuesUpdate);
        
        return [$countNew, $countUpdate, $countPriceUpdate, $ingridientyCountUpdate, $ingridientyCountPriceUpdate];
    }
    
    public static function getBatterPriceCode($batterValueId)
    {
        $rs = \CIBlockElement::GetList(
            ['NAME'=>'ASC'], 
            ['IBLOCK_ID'=>self::INGRIDIENTY_IBLOCK_ID, 'PROPERTY_IS_BATTER'=>$batterValueId], 
            false, 
            false, 
            ['ID', 'NAME', 'PROPERTY_FRONTPAD_CODE', 'PROPERTY_FO_PRICE', 'PROPERTY_IS_BATTER']
        );
        if($product=$rs->Fetch()) {
            $price=reset($product['PROPERTY_FO_PRICE_VALUE']);
            return $price['CODE'];
        }
        
        return null;
    }
    
    public static function getFrontPadPropertyEnumValues($iblockId, $propertyId=null)
    {
        $values = [];
        
        if(empty($propertyId)) {
            $propertyId=self::PROPERTY_FRONTPAD_CODE_ID;
        }
        
        $rs = \CIBlockPropertyEnum::GetList(["SORT"=>"ASC", "VALUE"=>"ASC"], ['IBLOCK_ID'=>$iblockId, 'PROPERTY_ID'=>$propertyId]);
        while($value = $rs->Fetch()) {
            $values[$value['XML_ID']] = $value;
        }
        
        return $values;
    }
    
    public static function getSiteProducts($iblockId, $filter=[])
    {
        $products = [];
        
        $rs = \CIBlockElement::GetList(['NAME'=>'ASC'], ['IBLOCK_ID'=>$iblockId] + $filter, false, false, ['ID', 'NAME', 'PROPERTY_FRONTPAD_CODE', 'PROPERTY_FO_PRICE', 'PROPERTY_IS_BATTER']);
        while ($product = $rs->Fetch()) {
            $products[$product['ID']] = $product;
        }
        
        return $products;
    }
    
    public static function getSiteProductsBySections($iblockId, $filter=[])
    {
        $products = [];
        $sections = [];
        
        $rs = \CIBlockElement::GetList(['NAME'=>'ASC'], ['IBLOCK_ID'=>$iblockId] + $filter, false, false, ['ID', 'NAME', 'IBLOCK_SECTION_ID', 'PROPERTY_FRONTPAD_CODE', 'PROPERTY_FO_PRICE', 'PROPERTY_IS_BATTER']);
        while ($product = $rs->Fetch()) {
            $sections[$product['IBLOCK_SECTION_ID']] = $product['IBLOCK_SECTION_ID'];
            $products[$product['IBLOCK_SECTION_ID']][$product['ID']] = $product;
        }
        $rs = \CIBlockSection::GetList(['SORT'=>'ASC'], ['IBLOCK_ID'=>$iblockId, 'ID'=>$sections] + $filter, false, ['ID', 'NAME']);
        $sections = [];
        while ($section = $rs->Fetch()) {
            $sections[$section['ID']] = $section;
        }
        
        return [$sections, $products];
    }
    
    public static function getSiteSections($iblockId)
    {
        $sections = [];
        
        $rs = \CIBlockSection::GetList(['SORT'=>'ASC'], ['IBLOCK_ID'=>$iblockId], false, ['ID', 'NAME']);
        while ($section = $rs->Fetch()) {
            $sections[$section['ID']] = $section['NAME'];
        }
        
        return $sections;
    }
    
    /**
     * Получение списка товаров
     */
    public static function getProducts()
    {
        $products = [];
        
        $response = static::send('get_products');
        if(!empty($response['result']) && ($response['result'] == 'success')) {
            foreach($response['product_id'] as $idx=>$id) {
                $products[$id] = [
                    'FRONTPAD_CODE' => $id,
                    'NAME' => $response['name'][$idx],
                    'PRICE' => $response['price'][$idx]
                ];
            }
        }
        
        uasort($products, function($a, $b) {
            return strcmp($a['NAME'], $b['NAME']);
        });
        
        return $products;
    }
    
    /**
     * Получить информацию о клиенте
     * @param string $phone номер телефона
     * @return array|mixed
     */
    public static function getClient($phone)
    {
        $phone = preg_replace('/[^0-9]+/', '', $phone);
        
        $data=['client_phone'=>$phone];
        
        return static::send('get_client', $data);
    }
    
    /**
     * Получить кол-во доступных баллов клиента
     * @param string $phone номер телефона
     * @return integer|false
     */
    public static function getClientScore($phone)
    {
        $client = static::getClient($phone);
        
        if(!isset($client['score'])) {
            return false; 
        }
        
        return $client['score'];
    }
    
    /**
     * Проверка сертификата
     * @param string $certificate номер сертификата
     */
    public static function getCertificate($certificate)
    {
        return static::send('get_certificate', compact('certificate'));
    }
    
    /**
     * Отправка запроса в FrontPad
     * @param string $method метод
     * @param array $data данные
     * @return array
     */
    protected static function send($method, $data=[])
    {
        if(is_array($data)) {
            $data['secret'] = self::$secret;
        }
        else {
            $data .= '&secret=' . self::$secret;
        }
        
        $ch=curl_init('https://app.frontpad.ru/api/index.php?' . $method);
        curl_setopt_array($ch, [
            CURLOPT_FAILONERROR => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $data
        ]);
        
        $result = curl_exec($ch);
        //$error = curl_error($ch);
        //$info = curl_getinfo($ch);
        /*
        print_r($data);
        print_r($result);
        print_r(curl_error($ch));
        print_r(curl_getinfo($ch));
        /**/        
        curl_close($ch);
        
        return json_decode($result, true);
    }

}
