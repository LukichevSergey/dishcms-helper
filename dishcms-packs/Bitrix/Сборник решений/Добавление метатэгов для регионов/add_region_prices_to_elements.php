<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
/**
 * Добавление пользовательского свойства
 */
error_reporting(E_ALL);
$CATALOG_IBLOCK_ID=2;

function KonturAddStringProperty($IBLOCK_ID, $CODE, $NAME, $isHTML=false, $SORT=700)
{
    $arFields = [
        'IBLOCK_ID'=>$IBLOCK_ID,
        'CODE'=>$CODE,
        'NAME'=>$NAME,
        'ACTIVE'=>'Y',
        'SORT'=>$SORT,
        'PROPERTY_TYPE'=>'S',
        'MULTIPLE'=>'N',
        'ROW_COUNT'=>1,
        'COL_COUNT'=>80
    ];
    
    if($isHTML) {
        $arFields['USER_TYPE'] = 'HTML';
        $arFields['USER_TYPE_SETTINGS'] = ['height'=>200];
    }
    
    $ibp = new CIBlockProperty;
 
    $ibp->Add( $arFields );
    
    echo 'Error: '.$ibp->LAST_ERROR . '<br/>';
}

function KonturAddDateProperty($IBLOCK_ID, $CODE, $NAME, $SORT=700)
{
    $arFields = [
        'IBLOCK_ID'=>$IBLOCK_ID,
        'CODE'=>$CODE,
        'NAME'=>$NAME,
        'ACTIVE'=>'Y',
        'SORT'=>$SORT,
        'PROPERTY_TYPE'=>'S',
        'MULTIPLE'=>'N',
        'ROW_COUNT'=>1,
        'COL_COUNT'=>30,
        'MULTIPLE_CNT'=>2,
        'USER_TYPE'=>'DateTime'
    ];
    
    $ibp = new CIBlockProperty;
 
    $ibp->Add( $arFields );
    echo 'Error: '.$ibp->LAST_ERROR . '<br/>';
}

function KonturAddNumberProperty($IBLOCK_ID, $CODE, $NAME, $SORT=600)
{
    $arFields = [
        'IBLOCK_ID'=>$IBLOCK_ID,
        'CODE'=>$CODE,
        'NAME'=>$NAME,
        'ACTIVE'=>'Y',
        'SORT'=>$SORT,
        'PROPERTY_TYPE'=>'N',
        'MULTIPLE'=>'N',
        'ROW_COUNT'=>1,
        'COL_COUNT'=>30
    ];
    
    $ibp = new CIBlockProperty;
 
    return var_dump($ibp->Add( $arFields ));
}

function KonturAddListProperty($IBLOCK_ID, $CODE, $NAME, $SORT=600, $VALUES=[])
{
    $arFields = [
        'IBLOCK_ID'=>$IBLOCK_ID,
        'CODE'=>$CODE,
        'NAME'=>$NAME,
        'ACTIVE'=>'Y',
        'SORT'=>$SORT,
        'PROPERTY_TYPE'=>'L',
        'MULTIPLE'=>'N',
        'ROW_COUNT'=>1,
        'COL_COUNT'=>30
    ];
    
    $VALUE_SORT=100;
    $arFields["VALUES"] = [];
    foreach($VALUES as $XML_ID=>$VALUE) {
        $arFields["VALUES"][] = [
            "XML_ID"=>$XML_ID,
            "VALUE"=>$VALUE,
            "DEF"=>"N",
            "SORT"=>$VALUE_SORT
        ];
        $VALUE_SORT+=100;
    }
    $ibp = new CIBlockProperty;
 
    $ibp->Add( $arFields );
    
    echo 'Error: '.$ibp->LAST_ERROR . '<br/>';

}

$cityPrefixes=array(
//    'MAIN_'=>'Новосибирск',
/*    'MSK_'=>'Москва',
    'BGD_'=>'Белгород',
    'KZN_'=>'Казань',
    'EKB_'=>'Екатеринбург',
    'NN_'=>'Нижний Новгород',
    'SMR_'=>'Самара',
    'TMN_'=>'Тюмень',
    'PSKOV_'=>'Псков',
    'TVER_'=>'Тверь',
    'SMK_'=>'Смоленск',
    'BRNSK_'=>'Брянск',
    'OREL_'=>'Орел',
    'LPSK_'=>'Липецк',
    'TULA_'=>'Тула',
    'RZN_'=>'Рязань',
    'TMBV_'=>'Тамбов',
    'VGD_'=>'Волгоград',
    'PNZ_'=>'Пенза',
    'KRV_'=>'Киров',
    'YRSL_'=>'Ярославль',
    'ULNK_'=>'Ульяновск',
    'VRZ_'=>'Воронеж',
    'KOS_'=>'Кострома',
    'IVN_'=>'Иваново',
    'TLT_'=>'Тольятти',
    'KRSD_'=>'Краснодар',
    'KLND_'=>'Калининград',
    'RND_'=>'Ростов-на-Дону',
    'SPB_'=>'Санкт-Петербург',
    'UFA_'=>'Уфа', */
    'ABN_'=>'Абакан',        
    'BRN_'=>'Барнаул',
    'BRK_'=>'Братск',
    'CHT_'=>'Чита',
    'HBK_'=>'Хабаровск',
    'IRK_'=>'Иркутск',    
    'KEM_'=>'Кемерово',
    'KRSK_'=>'Красноярск',    
    'MGN_'=>'Магадан',        
    'NKZ_'=>'Новокузнецк',    
    'PKM_'=>'Петропавловск-Камчатский',        
    'TMSK_'=>'Томск',        
    'TUVA_'=>'Тува',        
    'USH_'=>'Южно-Сахалинск',        
    'UU_'=>'Улан-Удэ',
    'VLD_'=>'Владивосток',        
    'YKTS_'=>'Якутск'    
);

$SORT = 6000;
foreach($cityPrefixes as $prefix=>$city) {
    $enCity=$city ? trim($city, '_') : 'NSK';

    KonturAddNumberProperty($CATALOG_IBLOCK_ID, $prefix.'BXR_PRICE', "Цена ({$city})", $SORT);
	KonturAddNumberProperty($CATALOG_IBLOCK_ID, $prefix.'BXR_DISCOUNT_PRICE', "Цена со скидкой ({$city})", $SORT + 5);
	KonturAddListProperty($CATALOG_IBLOCK_ID, $prefix.'BXR_INSTOCK', "Наличие ({$city})", $SORT + 6, [
		'Y'=>'В наличии',
		'custom'=>'Под заказ',
		'N'=>'Нет в наличии'
	]);

    KonturAddStringProperty($CATALOG_IBLOCK_ID, $prefix.'BXR_UNIT_PRICE', "Единица измерения ({$city})", false, $SORT + 7);
	KonturAddDateProperty($CATALOG_IBLOCK_ID, $prefix.'BXR_DISCOUNT_PERIOD_FROM', "Начало действия скидки ({$city})", $SORT + 8);
	KonturAddDateProperty($CATALOG_IBLOCK_ID, $prefix.'BXR_DISCOUNT_PERIOD_TO', "Окончание действия скидки ({$city})", $SORT + 9);

    $SORT += 10;
}

echo 'done!';

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
