<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Kontur\Calculator\Helper;

if(!empty($GLOBALS['KONTUR_CALCULATOR_IBLOCK_TYPE_ID'])) {
    $iblockTypeId=$GLOBALS['KONTUR_CALCULATOR_IBLOCK_TYPE_ID'];
    $siteId='s1'; // !FIXME 
    $access=[
        '2' => 'R', // Все пользователи
    ];

    $fields=[
        'ACTIVE' => 'Y',
        'NAME' => 'Настройки',
        'CODE' => "{$iblockTypeId}_items",
        'IBLOCK_TYPE_ID' => $iblockTypeId,
        'SITE_ID'=> $siteId,
        'SORT' => '700',
        'GROUP_ID' => $access, // Права доступа
        'FIELDS' => [

        ],
        'LIST_PAGE_URL' => '',
        'DETAIL_PAGE_URL' => '',
        'INDEX_ELEMENT' => 'Y', // Индексировать элементы для модуля поиска
        'VERSION' => 1, // Хранение элементов в общей таблице
        'ELEMENT_NAME' => 'Тип товара',
        'ELEMENTS_NAME' => 'Типы товаров',
        'ELEMENT_ADD' => 'Добавить тип товара',
        'ELEMENT_EDIT' => 'Изменить тип товара',
        'ELEMENT_DELETE' => 'Удалить тип товара',
        // Файл для редактирования элемента, позволяющий модифицировать поля перед сохранением
        'EDIT_FILE_BEFORE' => '/local/modules/kontur.calculator/include/calc_item_iblock_element_edit_before_save.php',
        // Файл с формой редактирования элемента
        'EDIT_FILE_AFTER' => '/local/modules/kontur.calculator/include/calc_item_iblock_element_edit.php',
    ];

    $ib = new \CIBlock;
    $calculatorItemsIblockId = $ib->Add($fields);
    if(!$calculatorItemsIblockId) {
        echo 'Возникла ошибка при создании инфоблока типов товара для калькулятора<br/>';
        return;
    }
    else {
        \COption::SetOptionString('kontur.calculator', Helper::SETTINGS_IBLOCK_ID_OPTION_NAME, $calculatorItemsIblockId);
    }

    // добавление свойств для инфоблока
    $rs = \CIBlockProperty::GetList([], ['IBLOCK_ID'=>$calculatorItemsIblockId]);
    if ($rs->SelectedRowsCount() <= 0) {
        $props=[
            Helper::PROP_FORM_FIELDS_OPTION_NAME=>[
                'NAME' => '[системное] Настройки полей формы',
                'ACTIVE' => 'Y',
                'SORT' => '100',
                'CODE' => 'FORM_FIELDS',
                'PROPERTY_TYPE' => 'S'
            ],
            Helper::PROP_CALC_FORMULAS_OPTION_NAME=>[
                'NAME' => '[системное] Настройки формул расчета',
                'ACTIVE' => 'Y',
                'SORT' => '200',
                'CODE' => 'CALC_FORMULAS',
                'PROPERTY_TYPE' => 'S'
            ],
        ];
        
        $ibp = new \CIBlockProperty;
        foreach($props as $optionKey=>$fields) {
            $fields['IBLOCK_ID'] = $calculatorItemsIblockId;
            $id = $ibp->Add($fields);
            if(!$id) {
                echo 'Возникла ошибка при создании инфоблока типов товара для калькулятора. 
                Не удалось создать свойство "' . $fields['NAME'] . '".<br/>';
                return;
            }
            elseif(!is_numeric($optionKey)) {
                \COption::SetOptionString('kontur.calculator', $optionKey, $id);
            }
        }
    }
}