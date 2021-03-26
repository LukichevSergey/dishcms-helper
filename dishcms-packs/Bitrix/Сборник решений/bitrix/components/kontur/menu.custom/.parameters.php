<? 
/**
 * @link https://dev.1c-bitrix.ru/learning/course/?COURSE_ID=43&LESSON_ID=2132
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

require(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'class.php');

use Kontur\Core\Iblock\Component\Parameters;

$arComponentParameters = array(
    "GROUPS" => array(
        "MENU_ITEMS" => array(
            "NAME" => "Пункты меню",
            "SORT" => 700
        )
    )
);

$arComponentParameters['PARAMETERS']['MENU_ITEMS_COUNT']=array(
    'PARENT'=>'MENU_ITEMS',
    'NAME'=>'Количество пунктов меню',
    'TYPE'=>'STRING',
    'DEFAULT'=>0,
    'REFRESH'=>'Y'
);

if(!empty($arCurrentValues['MENU_ITEMS_COUNT']) && is_numeric($arCurrentValues['MENU_ITEMS_COUNT']))
{
    foreach(range(1,$arCurrentValues['MENU_ITEMS_COUNT']) as $i) {
        $arComponentParameters['GROUPS']['MENU_ITEMS_'.$i]=array(
            'NAME'=>'Пункт меню #'.$i
        );
        $arComponentParameters['PARAMETERS']['MENU_ITEMS_NAME_'.$i]=array(
            'PARENT'=>'MENU_ITEMS_'.$i,
            'NAME'=>'Наименование',
            'TYPE'=>'STRING'
        );
        
        $arComponentParameters['PARAMETERS']['MENU_ITEMS_LINK_'.$i]=array(
            'PARENT'=>'MENU_ITEMS_'.$i,
            'NAME'=>'Ссылка',
            'TYPE'=>'STRING'
        );
        
        $arComponentParameters['PARAMETERS']['MENU_ITEMS_SORT_'.$i]=array(
            'PARENT'=>'MENU_ITEMS_'.$i,
            'NAME'=>'Сортировка',
            'TYPE'=>'STRING',
            'DEFAULT'=>'500'
        );        
        
        Parameters::addParameter($arComponentParameters['PARAMETERS'], 'MENU_ITEMS_TYPE_'.$i, [
            'PARENT' => 'MENU_ITEMS_'.$i,
            'NAME' => 'Тип меню',
            'TYPE' => 'LIST',
            'DEFAULT' => KonturMenuCustomComponent::TYPE_LINK,
            'VALUES' => [
                KonturMenuCustomComponent::TYPE_LINK => 'Ссылка',
                KonturMenuCustomComponent::TYPE_MENU => 'Меню сайта',
                KonturMenuCustomComponent::TYPE_SECTIONS => 'Разделы инфоблока'
            ],
            'REFRESH'=>'Y'
        ]);        
        
        $type=empty($arCurrentValues['MENU_ITEMS_TYPE_'.$i]) ? KonturMenuCustomComponent::TYPE_LINK : $arCurrentValues['MENU_ITEMS_TYPE_'.$i];
        
        if(($type == KonturMenuCustomComponent::TYPE_MENU) || ($type == KonturMenuCustomComponent::TYPE_SECTIONS)) {
            $arComponentParameters['PARAMETERS']['MENU_ITEMS_DEPTH_'.$i]=array(
                'PARENT'=>'MENU_ITEMS_'.$i,
                'NAME'=>'Уровень вложенности меню',
                'TYPE'=>'STRING'
            );
        }

        if($type == KonturMenuCustomComponent::TYPE_MENU) {
            Parameters::addMenuTypesParameter($arComponentParameters['PARAMETERS'], 'MENU_ITEMS_MENU_TYPE_'.$i, [
                'PARENT'=>'MENU_ITEMS_'.$i,
                'NAME'=>'Тип меню'
            ]);
        }

        if($type == KonturMenuCustomComponent::TYPE_SECTIONS) {
            Parameters::addIblockParameters(
                $arComponentParameters['PARAMETERS'], 
                $arCurrentValues, 
                ['PARAM_NAME'=>'MENU_ITEMS_IBLOCK_TYPE_'.$i, 'PARENT'=>'MENU_ITEMS_'.$i],
                ['PARAM_NAME'=>'MENU_ITEMS_IBLOCK_ID_'.$i, 'PARENT'=>'MENU_ITEMS_'.$i]
            );
        }
    }
}
?>
