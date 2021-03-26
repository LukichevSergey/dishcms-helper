<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

if(!empty($GLOBALS['KONTUR_CALCULATOR_IBLOCK_TYPE_ID'])) {
    $iblockTypeId=$GLOBALS['KONTUR_CALCULATOR_IBLOCK_TYPE_ID'];
    $siteId='s1'; // !FIXME 
    $access=[
        '2' => 'D', // Все пользователи
    ];

    $fields=[
        'ACTIVE' => 'Y',
        'NAME' => 'Заявки',
        'CODE' => "{$iblockTypeId}_requests",
        'IBLOCK_TYPE_ID' => $iblockTypeId,
        'SITE_ID'=> $siteId,
        'SORT' => '100',
        'GROUP_ID' => $access, // Права доступа
        'FIELDS' => [

        ],
        'LIST_PAGE_URL' => '',
        'DETAIL_PAGE_URL' => '',
        'INDEX_ELEMENT' => 'N', // Индексировать элементы для модуля поиска
        'VERSION' => 1, // Хранение элементов в общей таблице
        'ELEMENT_NAME' => 'Заявка',
        'ELEMENTS_NAME' => 'Заявки',
        'ELEMENT_ADD' => 'Добавить заявку',
        'ELEMENT_EDIT' => 'Изменить заявку',
        'ELEMENT_DELETE' => 'Удалить заявку',
    ];

    $ib = new \CIBlock;
    $calculatorRequestIblockId = $ib->Add($fields);
    if(!$calculatorRequestIblockId) {
        echo 'Возникла ошибка при создании инфоблока для заявок из формы калькулятора<br/>';
        return;
    }

    // добавление свойств для инфоблока
    $rs = \CIBlockProperty::GetList([], ['IBLOCK_ID'=>$calculatorRequestIblockId]);
    if ($rs->SelectedRowsCount() <= 0) {
        $props=[
            [
                'NAME' => 'Телефон',
                'ACTIVE' => 'Y',
                'SORT' => '100',
                'CODE' => 'PHONE',
                'PROPERTY_TYPE' => 'S'
            ],
            [
                'NAME' => 'E-Mail',
                'ACTIVE' => 'Y',
                'SORT' => '200',
                'CODE' => 'EMAIL',
                'PROPERTY_TYPE' => 'S'
            ],
        ];
        
        $ibp = new \CIBlockProperty;
        foreach($props as $fields) {
            $fields['IBLOCK_ID'] = $calculatorRequestIblockId;
            $id = $ibp->Add($fields);
            if(!$id) {
                echo 'Возникла ошибка при создании инфоблока для заявок из формы калькулятора. 
                Не удалось создать свойство "' . $fields['NAME'] . '".<br/>';
                return;
            }
        }
    }
}