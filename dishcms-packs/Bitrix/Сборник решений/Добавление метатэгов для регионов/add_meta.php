<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
/**
 * Добавление пользовательского свойства
 */
error_reporting(E_ALL);
$CATALOG_IBLOCK_ID=13;

function KonturAddUserTypeEntitySectionString($IBLOCK_ID, $FIELD_NAME, $SIZE='20', $ROWS='1', 
    $EDIT_FORM_LABEL, $HELP_MESSAGE=null, $ERROR_MESSAGE=null)
{
    if(empty($HELP_MESSAGE)) $HELP_MESSAGE=$EDIT_FORM_LABEL;
    if(empty($ERROR_MESSAGE)) $ERROR_MESSAGE=array('ru'=>'', 'en'=>'');
    
    $oUserTypeEntity = new CUserTypeEntity();
    $aUserFields = array(
        /*
         *  Идентификатор сущности, к которой будет привязано свойство.
         * Для секция формат следующий - IBLOCK_{IBLOCK_ID}_SECTION
         */
        'ENTITY_ID' => 'IBLOCK_' . $IBLOCK_ID . '_SECTION',
        /* Код поля. Всегда должно начинаться с UF_ */
        'FIELD_NAME' => 'UF_' . $FIELD_NAME,
        /* Указываем, что тип нового пользовательского свойства строка */
        'USER_TYPE_ID' => 'string',
        /*
         * XML_ID пользовательского свойства.
         * Используется при выгрузке в качестве названия поля
         */
        'XML_ID' => 'XML_ID_' . $FIELD_NAME,
        /* Сортировка */
        'SORT' => 500,
        /* Является поле множественным или нет */
        'MULTIPLE' => 'N',
        /* Обязательное или нет свойство */
        'MANDATORY' => 'N',
        /*
         * Показывать в фильтре списка. Возможные значения:
         * не показывать = N, точное совпадение = I,
         * поиск по маске = E, поиск по подстроке = S
         */
        'SHOW_FILTER' => 'N',
        /*
         * Не показывать в списке. Если передать какое-либо значение,
         * то будет считаться, что флаг выставлен.
         */
        'SHOW_IN_LIST' => '',
        /*
         * Не разрешать редактирование пользователем.
         * Если передать какое-либо значение, то будет считаться,
         * что флаг выставлен.
         */
        'EDIT_IN_LIST' => '',
        /* Значения поля участвуют в поиске */
        'IS_SEARCHABLE' => 'N',
        /*
         * Дополнительные настройки поля (зависят от типа).
         * В нашем случае для типа string
         */
        'SETTINGS' => array(
            /* Значение по умолчанию */
            'DEFAULT_VALUE' => '',
            /* Размер поля ввода для отображения */
            'SIZE' => $SIZE,
            /* Количество строчек поля ввода */
            'ROWS' => $ROWS,
            /* Минимальная длина строки (0 - не проверять) */
            'MIN_LENGTH'    => '0',
            /* Максимальная длина строки (0 - не проверять) */
            'MAX_LENGTH'    => '0',
            /* Регулярное выражение для проверки */
            'REGEXP'        => '',
        ),
        /* Подпись в форме редактирования */
        'EDIT_FORM_LABEL'   => $EDIT_FORM_LABEL,
        /* Заголовок в списке */
        'LIST_COLUMN_LABEL' => $EDIT_FORM_LABEL,
        /* Подпись фильтра в списке */
        'LIST_FILTER_LABEL' => $EDIT_FORM_LABEL,
        /* Сообщение об ошибке (не обязательное) */
        'ERROR_MESSAGE'     => $ERROR_MESSAGE,
        /* Помощь */
        'HELP_MESSAGE'      => $HELP_MESSAGE,
    );
 
    return $oUserTypeEntity->Add( $aUserFields ); // int
}

$cityPrefixes=array(
    'MAIN_'=>'Новосибирск',
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
    
    'BRN_'=>'Барнаул',
    'CHT_'=>'Чита',
    'BRK_'=>'Братск',
    'IRK_'=>'Иркутск',    
    'KEM_'=>'Кемерово',
    'NKZ_'=>'Новокузнецк',    
    'KRSK_'=>'Красноярск',    
    'UU_'=>'Улан-Удэ',
    'TUVA_'=>'Тува',        
    'ABN_'=>'Абакан',        
    'TMSK_'=>'Томск',        
    'PKM_'=>'Петропавловск-Камчатский',        
    'MGN_'=>'Магадан',        
    'VLD_'=>'Владивосток',        
    'YKTS_'=>'Якутск',        
    'USH_'=>'Южно-Сахалинск',        
    'HBK_'=>'Хабаровск',
    
    'BUR_'=>'Бурятия',
    'AMUR_'=>'Амур',
	'PERM_'=>'Пермь'
);

foreach($cityPrefixes as $prefix=>$city) {
    $enCity=$city ? trim($city, '_') : 'NSK';
    
    KonturAddUserTypeEntitySectionString($CATALOG_IBLOCK_ID, $prefix.'META_TITLE', '60', '1', 
        array('ru'=>"Заголовок окна браузера ({$city})", 'en'=>"META Title ({$enCity})")
    );
    KonturAddUserTypeEntitySectionString($CATALOG_IBLOCK_ID, $prefix.'KEYWORDS', '60', '1', 
        array('ru'=>"Ключевые слова ({$city})", 'en'=>"META Keywords ({$enCity})")
    );
    KonturAddUserTypeEntitySectionString($CATALOG_IBLOCK_ID, $prefix.'META_DESC', '60', '5', 
        array('ru'=>"Мета-описание ({$city})", 'en'=>"META Description ({$enCity})")
    );    
    KonturAddUserTypeEntitySectionString($CATALOG_IBLOCK_ID, $prefix.'DETAIL_TEXT', '60', '10', 
        array('ru'=>"Описание раздела ({$city})", 'en'=>"Detail Text ({$enCity})")
    );
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
