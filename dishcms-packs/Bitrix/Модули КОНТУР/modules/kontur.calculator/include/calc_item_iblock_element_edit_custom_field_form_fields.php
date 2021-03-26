<?php
/**
 * Поле настройки полей формы
 * 
 * @var int $ID идентификатор текущего элемента
 * @var [] $prop_fields массив параметров свойства настройки полей
 */
use Bitrix\Main\Loader;
use Bitrix\Main\Web\Json;
use Kontur\Calculator\Helper;

Loader::includeModule('catalog');
\CJSCore::Init('core_condtree');

$typeVariablesList=array_combine(range('A', 'Z'), range('A', 'Z'));
$typeNumVariablesList=array_combine(range(2, 100), range(2, 100));

$currentPropValues=$GLOBALS['KONTUR_CALC_FORM_FIELDS_PROP_FIELDS']??[];
if(!array_key_exists('VALUE', $currentPropValues)) { $currentPropValues=array_pop($currentPropValues); }
$currentPropValues=Helper::getFormFields($ID??null, $currentPropValues['VALUE']??null);

$currentValues=[
    'id'=>'calcFieldGroup',
    'controlId'=>'calcFieldGroup',
    'values'=>[],
    'children'=>[]
];

Helper::treeGetControlGroupIdx($currentValues, 'calcListFieldGroup');
Helper::treeGetControlGroupIdx($currentValues, 'calcUserFieldGroup');
Helper::treeGetControlGroupIdx($currentValues, 'calcSystemFieldGroup');
Helper::treeSetControlOptions($currentValues, 'calcListFieldGroup', ['showDeleteButton'=>false]);
Helper::treeSetControlOptions($currentValues, 'calcUserFieldGroup', ['showDeleteButton'=>false]);
Helper::treeSetControlOptions($currentValues, 'calcSystemFieldGroup', ['showDeleteButton'=>false]);

if(!empty($currentPropValues['LIST'])) {
    foreach($currentPropValues['LIST'] as $idx=>$val) {
        $type=$val['TYPE'] ?? Helper::PROP_FORM_FIELD_TYPE_ENUM;
        switch($type) {
            case  Helper::PROP_FORM_FIELD_TYPE_ENUM:
                Helper::treeSetControlValues($currentValues, 'calcListFieldGroup', 'calcListEnumTypeField', [
                    'var'=>$val['VAR'] ?? '',
                    'var_n'=>$val['VAR_N'] ?? '',
                    'type'=>$type,
                    'sort'=>(string)($val['SORT'] ?? 500),
                    'name'=>$val['NAME'] ?? '',
                    'values'=>implode('; ', $val['VALUES'] ?? []),
                ]);
                break;
        }
    }
}

if(!empty($currentPropValues['USER'])) {
    foreach($currentPropValues['USER'] as $idx=>$val) {
        $type=$val['TYPE'] ?? Helper::PROP_FORM_FIELD_TYPE_FLOAT;
        switch($type) {
            case  Helper::PROP_FORM_FIELD_TYPE_INT:
                Helper::treeSetControlValues($currentValues, 'calcUserFieldGroup', 'calcUserIntTypeField', [
                    'var'=>$val['VAR'] ?? '',
                    'var_n'=>$val['VAR_N'] ?? '',
                    'type'=>$type,
                    'sort'=>(string)($val['SORT'] ?? 500),
                    'name'=>$val['NAME'] ?? '',
                    'default'=>$val['DEFAULT'] ?? null,
                ]);
                break;
                
            case  Helper::PROP_FORM_FIELD_TYPE_FLOAT:
                Helper::treeSetControlValues($currentValues, 'calcUserFieldGroup', 'calcUserFloatTypeField', [
                    'var'=>$val['VAR'] ?? '',
                    'var_n'=>$val['VAR_N'] ?? '',
                    'type'=>$type,
                    'sort'=>(string)($val['SORT'] ?? 500),
                    'name'=>$val['NAME'] ?? '',
                    'default'=>$val['DEFAULT'] ?? null,
                ]);
                break;
        }
    }
}

if(!empty($currentPropValues['SYSTEM'])) {
    usort($currentPropValues['SYSTEM'], function($a, $b) {
        if($a['VAR'] == $b['VAR']) { return ($a['VAR_N'] === $b['VAR_N']) ? 0 : ($a['VAR_N'] < $b['VAR_N'] ? -1 : 1); }
        return $a['VAR'] < $b['VAR'] ? -1 : 1;
    });
    foreach($currentPropValues['SYSTEM'] as $idx=>$val) {
        $type=$val['TYPE'] ?? Helper::PROP_FORM_FIELD_TYPE_FLOAT;
        switch($type) {
            case  Helper::PROP_FORM_FIELD_TYPE_FLOAT:
                Helper::treeSetControlValues($currentValues, 'calcSystemFieldGroup', 'calcSystemNumberTypeField', [
                    'var'=>$val['VAR'] ?? '',
                    'var_n'=>$val['VAR_N'] ?? '',
                    'type'=>$type,
                    'name'=>$val['NAME'] ?? '',
                    'default'=>$val['DEFAULT'] ?? null,
                ]);
                break;            
        }
    }
}

$conditionRules=[];

$conditionRules[]=[
    'controlId'=>'calcFieldGroup',
    'group'=>true,
    'label'=>'Поля калькулятора',
    'showIn'=>[],
    'containsOneAction'=>true,
    'mess'=>[
        'ADD_CONTROL'=>'Добавить тип поля',
        'SELECT_CONTROL'=>'Выбeрите тип поля...',
    ],
    'control'=>[
        [
            'id'=>'All',
            'type'=>'prefix',
            'text'=>'Поля для формы калькулятора и расчета',
        ]
    ],
];

$conditionRules[]=[
    'controlId'=>'calcListFieldGroup',
    'group'=>true,
    'label'=>'Поля выбора из списка значений',
    'showIn'=>['calcFieldGroup'],
    'mess'=>[
        'ADD_CONTROL'=>'Добавить поле',
        'SELECT_CONTROL'=>'Выбeрите тип поля...',
        'DELETE_CONTROL'=>'Удалить все поля',
    ],
    'control'=>[
        [
            'id'=>'title',
            'type'=>'prefix',
            'text'=>'Поля выбора из списка значений',
        ]
    ]
];

$conditionRules[]=[
    'controlgroup'=>true,
    'group'=>false,
    'label'=>'Доступные типы полей',
    'showIn'=>['calcListFieldGroup'],
    'children'=>[        
        [
            'controlId'=>'calcListEnumTypeField',
            'description'=>'Поле выбора из списка значений',
            'group'=>false,
            'label'=>'Список значений',
            'showIn'=>['calcListFieldGroup'],
            'mess'=>[
                'DELETE_CONTROL'=>'Удалить поле',
            ],
            'control'=>[
                [
                    'id'=>'var',
                    'name'=>'var',
                    'type'=>'select',
                    'defaultText'=>'...',
                    'first_option'=>'Выберите имя переменной для формулы...',
                    'values'=>$typeVariablesList,
                ],
                [
                    'id'=>'var_n',
                    'name'=>'var_n',
                    'type'=>'select',
                    'defaultText'=>'...',
                    'first_option'=>'Выберите дополнительный номер переменной для формулы...',
                    'values'=>$typeNumVariablesList,
                ],
                [
                    'id'=>'sort',
                    'name'=>'sort',
                    'type'=>'input',
                    'defaultText'=>'порядок',

                ],
                [
                    'id'=>'type',
                    'name'=>'type',
                    'type'=>'prefix',
                    'text'=>'Список',
                ],
                [
                    'id'=>'name',
                    'name'=>'name',
                    'type'=>'input',
                    'defaultText'=>'наименование поля'
                ],
                [
                    'id'=>'values',
                    'name'=>'values',
                    'type'=>'input',
                    'defaultText'=>'список значений',
                    'show_value'=>'Y'
                ],
            ]
        ],        
    ]
];

$conditionRules[]=[
    'controlId'=>'calcUserFieldGroup',
    'group'=>true,
    'label'=>'Поля ввода для пользователя',
    'showIn'=>['calcFieldGroup'],
    'mess'=>[
        'ADD_CONTROL'=>'Добавить поле',
        'SELECT_CONTROL'=>'Выбeрите тип поля...',
        'DELETE_CONTROL'=>'Удалить все поля',
    ],
    'control'=>[
        [
            'id'=>'title',
            'type'=>'prefix',
            'text'=>'Поля ввода для пользователя',
        ]
    ]
];

$conditionRules[]=[
    'controlgroup'=>true,
    'group'=>false,
    'label'=>'Доступные типы полей',
    'showIn'=>['calcUserFieldGroup'],
    'children'=>[        
        [
            'controlId'=>'calcUserIntTypeField',
            'description'=>'Поле ввода числового значения',
            'group'=>false,
            'label'=>'Целое число',
            'showIn'=>['calcUserFieldGroup'],
            'mess'=>[
                'DELETE_CONTROL'=>'Удалить поле',
            ],
            'control'=>[
                [
                    'id'=>'var',
                    'name'=>'var',
                    'type'=>'select',
                    'defaultText'=>'...',
                    'first_option'=>'Выберите имя переменной для формулы...',
                    'values'=>$typeVariablesList,
                ],
                [
                    'id'=>'var_n',
                    'name'=>'var_n',
                    'type'=>'select',
                    'defaultText'=>'...',
                    'first_option'=>'Выберите дополнительный номер переменной для формулы...',
                    'values'=>$typeNumVariablesList,
                ],
                [
                    'id'=>'sort',
                    'name'=>'sort',
                    'type'=>'input',
                    'defaultText'=>'порядок',
                ],
                [
                    'id'=>'type',
                    'name'=>'type',
                    'type'=>'prefix',
                    'text'=>'Целое',
                ],
                [
                    'id'=>'name',
                    'name'=>'name',
                    'type'=>'input',
                    'defaultText'=>'наименование поля'
                ],
                [
                    'id'=>'default',
                    'name'=>'default',
                    'type'=>'input',
                    'defaultText'=>'значение по умолчанию',
                    'show_value'=>'Y'
                ],
            ]
        ],
        [
            'controlId'=>'calcUserFloatTypeField',
            'description'=>'Поле ввода вещественного значения',
            'group'=>false,
            'label'=>'Вещественное число',
            'showIn'=>['calcUserFieldGroup'],
            'mess'=>[
                'DELETE_CONTROL'=>'Удалить поле',
            ],
            'control'=>[
                [
                    'id'=>'var',
                    'name'=>'var',
                    'type'=>'select',
                    'defaultText'=>'...',
                    'first_option'=>'Выберите имя переменной для формулы...',
                    'values'=>$typeVariablesList,
                ],
                [
                    'id'=>'var_n',
                    'name'=>'var_n',
                    'type'=>'select',
                    'defaultText'=>'...',
                    'first_option'=>'Выберите дополнительный номер переменной для формулы...',
                    'values'=>$typeNumVariablesList,
                ],
                [
                    'id'=>'sort',
                    'name'=>'sort',
                    'type'=>'input',
                    'defaultText'=>'порядок',

                ],
                [
                    'id'=>'type',
                    'name'=>'type',
                    'type'=>'prefix',
                    'text'=>'Вещественное',
                ],
                [
                    'id'=>'name',
                    'name'=>'name',
                    'type'=>'input',
                    'defaultText'=>'наименование поля'
                ],
                [
                    'id'=>'default',
                    'name'=>'default',
                    'type'=>'input',
                    'defaultText'=>'значение по умолчанию',
                    'show_value'=>'Y'
                ],
            ]
        ],      
    ]
];

$conditionRules[]=[
    'controlId'=>'calcSystemFieldGroup',
    'group'=>true,
    'label'=>'Системные поля (только для формул расчета)',
    'showIn'=>['calcFieldGroup'],
    'mess'=>[
        'ADD_CONTROL'=>'Добавить поле',
        'SELECT_CONTROL'=>'Выбeрите тип поля...',
        'DELETE_CONTROL'=>'Удалить все поля',
    ],
    'control'=>[
        [
            'id'=>'title',
            'type'=>'prefix',
            'text'=>'Системные поля (только для формул расчета)',
        ]
    ]
];

$conditionRules[]=[
    'controlgroup'=>true,
    'group'=>false,
    'label'=>'Доступные типы полей',
    'showIn'=>['calcSystemFieldGroup'],
    'children'=>[
        [
            'controlId'=>'calcSystemNumberTypeField',
            'group'=>false,
            'label'=>'Число',
            'showIn'=>['calcSystemFieldGroup'],
            'mess'=>[
                'DELETE_CONTROL'=>'Удалить поле',
            ],
            'control'=>[ 
                [
                    'id'=>'var',
                    'name'=>'var',
                    'type'=>'select',
                    'defaultText'=>'...',
                    'first_option'=>'Выберите имя переменной для формулы...',
                    'values'=>$typeVariablesList,
                ],
                [
                    'id'=>'var_n',
                    'name'=>'var_n',
                    'type'=>'select',
                    'defaultText'=>'...',
                    'first_option'=>'Выберите дополнительный номер переменной для формулы...',
                    'values'=>$typeNumVariablesList,
                ],               
                [
                    'id'=>'type',
                    'name'=>'type',
                    'type'=>'prefix',
                    'text'=>'Число',
                ],
                [
                    'id'=>'name',
                    'name'=>'name',                    
                    'type'=>'input',
                    'defaultText'=>'наименование поля'
                ],                
                [
                    'id'=>'default',
                    'name'=>'default',                    
                    'type'=>'input',
                    'defaultText'=>'значение по умолчанию'
                ],                
            ]
        ],
    ]
];

?>
<tr><td>
    <script>(function() {
        let conditionRules=<?=Json::encode($conditionRules, JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS | JSON_OBJECT_AS_ARRAY)?>;
        let currentValues=<?=Json::encode($currentValues, JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS | JSON_OBJECT_AS_ARRAY)?>;
        function konturCalculatorFormFieldInit() {
            konturCalculatorFormFieldInit.tree=new BX.TreeConditions({
                'parentContainer': 'kontur_calculator_form_fields_tree',
                'form': 'form_element_35_form',
                'sepID': '__',
                'prefix': 'KONTUR_CALC_FORM_FIELDS'
            }, currentValues, conditionRules);
            BX.findChildren(
                BX.findParent(BX('kontur_calculator_form_fields_tree'), {className:'adm-detail-content'}), 
                {class:'adm-detail-title'}
            )[0].innerHTML='Настройка полей для формы калькулятора и расчета';
        }
        document.addEventListener('DOMContentLoaded', konturCalculatorFormFieldInit);
    })();
    </script>
    <div id="kontur_calculator_form_fields_tree"></div>
    <div class="adm-info-message-wrap">
        <div class="adm-info-message" style="padding-left:10px">
        1) Значения для типа поля <span class="adm-info-message-title">"список значений"</span> нужно указывать 
        через точку с запятой (<span class="adm-info-message-title">;</span>) или можно через пробел, если значения являются числами.
        <br/>
        2) Новые поля станут доступны во вкладке <span class="adm-info-message-title">"Формулы расчета"</span> только после сохранения формы.
        <br/>
        3) Во вкладке <span class="adm-info-message-title">"Формулы расчета"</span> будут доступны только поля у которых указано имя переменной.
        </div>
    </div>
</td></tr>