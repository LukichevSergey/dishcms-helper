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
 
    return $ibp->Add( $arFields );
}

$cityPrefixes=array(
//    'MAIN_'=>'Новосибирск',
    'MSK_'=>'Москва',
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
    'UFA_'=>'Уфа',
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
    'YKTS_'=>'Якутск',
	'PERM_'=>'Пермь'
);

$SORT = 7000;
foreach($cityPrefixes as $prefix=>$city) {
    $enCity=$city ? trim($city, '_') : 'NSK';
    
    KonturAddStringProperty($CATALOG_IBLOCK_ID, $prefix.'META_TITLE', "Заголовок окна браузера ({$city})", false, $SORT);
    KonturAddStringProperty($CATALOG_IBLOCK_ID, $prefix.'META_KEY', "Ключевые слова ({$city})", false, $SORT + 2);
    KonturAddStringProperty($CATALOG_IBLOCK_ID, $prefix.'META_DESC', "Мета-описание ({$city})", false, $SORT + 4);
    KonturAddStringProperty($CATALOG_IBLOCK_ID, $prefix.'DETAIL_TEXT', "Детальное описание ({$city})", true, $SORT + 6);
    
    $SORT += 10;
}

echo 'done!';

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
