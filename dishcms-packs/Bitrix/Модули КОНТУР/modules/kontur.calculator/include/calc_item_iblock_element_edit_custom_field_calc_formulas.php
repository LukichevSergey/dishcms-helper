<?php
/**
 * Поле настройки формул калькулятора
 * 
 * @var int $ID идентификатор текущего элемента
 * @var [] $prop_fields массив параметров свойства настройки формул калькулятора
 */
use Bitrix\Main\Loader;
use Bitrix\Main\Web\Json;
use Kontur\Calculator\Helper;

Loader::includeModule('catalog');
\CJSCore::Init('core_condtree');

$currentFormFields=$GLOBALS['KONTUR_CALC_FORM_FIELDS_PROP_FIELDS']??[];
if(!array_key_exists('VALUE', $currentFormFields)) { $currentFormFields=array_pop($currentFormFields); }
$currentFormFields=Helper::getFormFields($ID??null, $currentFormFields['VALUE']??null);

$currentPropValues=$GLOBALS['KONTUR_CALC_FORMULAS_PROP_FIELDS']??[];
if(!array_key_exists('VALUE', $currentPropValues)) { $currentPropValues=array_pop($currentPropValues); }
$currentPropValues=Helper::getCalcFormulas($ID??null, $currentPropValues['VALUE']??null);

$currentValues=[
    'id'=>'calcFormulasGroup',
    'controlId'=>'calcFormulasGroup',
    'values'=>[],
    'children'=>[]
];

Helper::treeSetControlValues($currentValues, 'calcBaseFormulasGroup', 'calcBaseFormulasTotalMass', [
    'label'=>'Общая масса',
    'expression'=>$currentPropValues['BASE']['MASS']['EXPRESSION']??''
]);
Helper::treeSetControlValues($currentValues, 'calcBaseFormulasGroup', 'calcBaseFormulasTotalPrice', [
    'label'=>'Итоговая стоимость',
    'expression'=>$currentPropValues['BASE']['PRICE']['EXPRESSION']??''
]);
Helper::treeSetControlOptions($currentValues, 'calcBaseFormulasGroup', ['showDeleteButton'=>false]);
Helper::treeSetControlOptions($currentValues, 'calcBaseFormulasTotalMass', ['showDeleteButton'=>false]);
Helper::treeSetControlOptions($currentValues, 'calcBaseFormulasTotalPrice', ['showDeleteButton'=>false]);

if(!empty($currentFormFields['USER'])) {
    foreach($currentFormFields['USER'] as $field) {
        $varname=$field['VAR'] . $field['VAR_N'];
        Helper::treeSetControlValues($currentValues, 'calcUserAutoFormulasGroup', 'calcUserAutoFormulasItem', [
            'var'=>$varname,
            'label'=>$field['NAME'],
            'expression'=>$currentPropValues['AUTO'][$varname]['EXPRESSION']??''
        ], $varname);
    }
    Helper::treeSetControlOptions($currentValues, 'calcUserAutoFormulasGroup', ['showDeleteButton'=>false]);
    Helper::treeSetControlOptions($currentValues, 'calcUserAutoFormulasItem', ['showDeleteButton'=>false]);
}

Helper::treeGetControlGroupIdx($currentValues, 'calcMatrixFormulasGroup');
Helper::treeSetControlOptions($currentValues, 'calcMatrixFormulasGroup', ['showDeleteButton'=>false]);

$conditionRules=[];

$conditionRules[]=[
    'controlId'=>'calcFormulasGroup',
    'group'=>true,
    'label'=>'Формулы расчета',
    'showIn'=>[],
    'containsOneAction'=>true,
    'control'=>[
        [
            'id'=>'title',
            'type'=>'prefix',
            'text'=>'Формулы расчета',
        ]
    ] 
];

$conditionRules[]=[
    'controlId'=>'calcBaseFormulasGroup',
    'group'=>true,
    'label'=>'Основные формулы расчета',
    'showIn'=>['calcFormulasGroup'],
    'containsOneAction'=>true,
    'mess'=>[
        'ADD_CONTROL'=>'Добавить формулу',
        'SELECT_CONTROL'=>'Выбeрите поле...',
        'DELETE_CONTROL'=>'Удалить все формулы',
    ],
    'control'=>[
        [
            'id'=>'title',
            'type'=>'prefix',
            'text'=>'Основные формулы расчета',
        ]
    ] 
];

$conditionRules[]=[
    'controlgroup'=>true,
    'group'=>false,
    'label'=>'Основные формулы расчета',
    'showIn'=>['calcBaseFormulasGroup'],
    'mess'=>[
        'ADD_CONTROL'=>'Добавить формулу',
        'SELECT_CONTROL'=>'Выбeрите тип формулы...',
        'DELETE_CONTROL'=>'Удалить все формулы',
    ],
    'children'=>[
        [
            'controlId'=>'calcBaseFormulasTotalMass',
            'group'=>false,
            'label'=>'Общая масса',
            'showIn'=>['calcBaseFormulasGroup'],
            'mess'=>[
                'DELETE_CONTROL'=>'Удалить формулу',
            ],
            'control'=>[ 
                [
                    'id'=>'label',
                    'name'=>'label',
                    'type'=>'prefix',
                    'text'=>'Общая масса (в тоннах)',
                ],
                [
                    'id'=>'expression',
                    'name'=>'expression',
                    'type'=>'input',
                    'defaultText'=>'укажите формулу расчета...'
                ],                
            ]
        ],
        [
            'controlId'=>'calcBaseFormulasTotalPrice',
            'group'=>false,
            'label'=>'Итоговая стоимость',
            'showIn'=>['calcBaseFormulasGroup'],
            'mess'=>[
                'DELETE_CONTROL'=>'Удалить формулу',
            ],
            'control'=>[ 
                [
                    'id'=>'label',
                    'name'=>'label',
                    'type'=>'prefix',
                    'text'=>'Итоговая стоимость (руб)',
                ],
                [
                    'id'=>'expression',
                    'name'=>'expression',
                    'type'=>'input',
                    'defaultText'=>'укажите формулу расчета...'
                ],                
            ]
        ],
    ],
];

$conditionRules[]=[
    'controlId'=>'calcUserAutoFormulasGroup',
    'group'=>true,
    'label'=>'Формулы авторасчета для полей ввода пользователем',
    'showIn'=>['calcFormulasGroup'],
    'containsOneAction'=>true,
    'mess'=>[
        'ADD_CONTROL'=>'Добавить формулу',
        'SELECT_CONTROL'=>'Выбeрите поле...',
        'DELETE_CONTROL'=>'Удалить все формулы',
    ],
    'control'=>[
        [
            'id'=>'title',
            'type'=>'prefix',
            'text'=>'Формулы авторасчета для полей ввода пользователем',
        ]
    ] 
];

$conditionRules[]=[
    'controlgroup'=>true,
    'group'=>false,
    'label'=>'Формулы авторасчета для полей ввода пользователем',
    'showIn'=>['calcUserAutoFormulasGroup'],
    'mess'=>[
        'ADD_CONTROL'=>'Добавить формулу',
        'SELECT_CONTROL'=>'Выбeрите поле...',
        'DELETE_CONTROL'=>'Удалить все формулы',
    ],
    'children'=>[
        [
            'controlId'=>'calcUserAutoFormulasItem',
            'group'=>false,
            'label'=>'Пользовательское поле',
            'showIn'=>['calcUserAutoFormulasGroup'],
            'mess'=>[
                'DELETE_CONTROL'=>'Удалить формулу',
            ],
            'control'=>[ 
                [
                    'id'=>'var',
                    'name'=>'var',
                    'type'=>'label',
                    'class'=>'control-string',
                    'defaultText'=>'Переменная',
                    'defaultValue'=>'Переменная',
                ],
                [
                    'id'=>'label',
                    'name'=>'label',
                    'type'=>'label',
                    'text'=>'Наименование',
                    'defaultText'=>'Наименование',
                    'defaultValue'=>'Наименование',
                ],
                [
                    'id'=>'expression',
                    'name'=>'expression',
                    'type'=>'input',
                    'defaultText'=>'укажите формулу расчета...'
                ],                
            ]
        ],
    ],
];

$conditionRules[]=[
    'controlId'=>'calcMatrixFormulasGroup',
    'group'=>true,
    'label'=>'Дополнительная матрица значений',
    'showIn'=>['calcFormulasGroup'],
    'mess'=>[
        'ADD_CONTROL'=>'Добавить поле',
        'SELECT_CONTROL'=>'Выбeрите поле...',
        'DELETE_CONTROL'=>'Удалить все поля',
    ],
    'control'=>[
        [
            'id'=>'title',
            'type'=>'prefix',
            'text'=>'Дополнительная матрица значений',
        ]
    ] 
];

if(!empty($currentFormFields['SYSTEM'])) {
    $calcMatrixFormulasItems=[
        'controlgroup'=>true,
        'group'=>false,
        'label'=>'Доступные поля',
        'showIn'=>['calcMatrixFormulasGroup'],
        'mess'=>[
            'ADD_CONTROL'=>'Добавить значение',
            'SELECT_CONTROL'=>'Выбeрите значение...',
            'DELETE_CONTROL'=>'Удалить все значения',
        ],
        'children'=>[     
        ]
    ];

    foreach($currentFormFields['LIST']??[] as $item) {
        if(!empty($item['VAR']) && !empty($item['VALUES'])) {
            $varname=$item['VAR'] . $item['VAR_N'];
            $name=$item['NAME']??"Поле {$varname}";
            $groupItemControlId="calcMatrixFormulasItemGroup_{$varname}";
            $calcMatrixFormulasItemGroup=[
                'controlId'=>$groupItemControlId,
                'group'=>true,
                'label'=>"{$name} ({$varname})",
                'showIn'=>['calcMatrixFormulasGroup'],
                'mess'=>[
                    'ADD_CONTROL'=>'Добавить значение',
                    'SELECT_CONTROL'=>'Выбeрите значение...',
                    'DELETE_CONTROL'=>'Удалить поле',
                ],
                'control'=>[
                    [
                        'id'=>'title',
                        'type'=>'prefix',
                        'text'=>"{$name} ({$varname})"
                    ]
                ],
            ];

            if(!empty($currentPropValues['MATRIX'][$varname])) {
                $matrixGroupIdx=Helper::treeGetControlGroupIdx($currentValues, 'calcMatrixFormulasGroup');
                $groupItemControlIdx=Helper::treeGetControlGroupIdx($currentValues['children'][$matrixGroupIdx], $groupItemControlId);
            }

            $conditionRules[]=$calcMatrixFormulasItemGroup;
            foreach($item['VALUES'] as $val) {
                $valueHash=Helper::getValueHash($val);
                $groupItemValuesControlId="calcMatrixFormulasItemValueGroup_{$varname}_{$valueHash}";
                $calcMatrixFormulasItemValuesGroup=[
                    'controlId'=>$groupItemValuesControlId,
                    'group'=>true,
                    'label'=>"Значение: {$val}",
                    'showIn'=>[$groupItemControlId],
                    'mess'=>[
                        'ADD_CONTROL'=>'Добавить поле',
                        'SELECT_CONTROL'=>'Выбeрите поле...',
                        'DELETE_CONTROL'=>'Удалить значение',
                    ],
                    'control'=>[
                        [
                            'id'=>'title',
                            'type'=>'prefix',
                            'text'=>"Значение: {$val}"
                        ]
                    ],
                ];
                $conditionRules[]=$calcMatrixFormulasItemValuesGroup;

                if(!empty($currentPropValues['MATRIX'][$varname][$valueHash])) {
                    Helper::treeGetControlGroupIdx(
                        $currentValues['children'][$matrixGroupIdx]['children'][$groupItemControlIdx], 
                        $groupItemValuesControlId
                    );
                }

                foreach($currentFormFields['SYSTEM'] as $systemField) {
                    if(!empty($systemField['VAR']) && !empty($systemField['NAME'])) {
                        $systemVarname=$systemField['VAR'] . $systemField['VAR_N'];
                        $systemName=$systemField['NAME'];
                        $itemValueControlId="calcMatrixFormulasItemValue_{$varname}_{$valueHash}_{$systemVarname}";
                        $calcMatrixFormulasItemValueGroup=[
                            'controlId'=>$itemValueControlId,
                            'group'=>false,
                            'label'=>$systemName,
                            'showIn'=>[$groupItemValuesControlId],
                            'containsOneAction'=>true,                    
                            'mess'=>[
                                'ADD_CONTROL'=>'Добавить поле',
                                'SELECT_CONTROL'=>'Выбeрите поле...',
                                'DELETE_CONTROL'=>'Удалить поле',
                            ],
                            'control'=>[
                                [
                                    'id'=>'var',
                                    'name'=>'var',
                                    'type'=>'label',
                                    'class'=>'control-string',
                                    'defaultText'=>$systemVarname,
                                    'defaultValue'=>$systemVarname,
                                ],
                                [
                                    'id'=>'label',
                                    'name'=>'label',
                                    'type'=>'label',
                                    'defaultText'=>$systemName,
                                    'defaultValue'=>$systemName,
                                ],
                                [
                                    'id'=>'expression',
                                    'name'=>'expression',
                                    'type'=>'input',
                                    'defaultText'=>'укажите значение...',
                                    'defaultValue'=>$systemField['DEFAULT']?:null
                                ],
                            ],
                        ];
                        
                        $conditionRules[]=$calcMatrixFormulasItemValueGroup;

                        if(!empty($currentPropValues['MATRIX'][$varname][$valueHash][$systemVarname]['EXPRESSION'])) {
                            Helper::treeSetControlValues(
                                $currentValues['children'][$matrixGroupIdx]['children'][$groupItemControlIdx], 
                                $groupItemValuesControlId, 
                                $itemValueControlId, 
                                [
                                    'expression'=>$currentPropValues['MATRIX'][$varname][$valueHash][$systemVarname]['EXPRESSION'],
                                ]
                            );
                        }
                    }
                }

            }
        }
    }

    $conditionRules[]=$calcMatrixFormulasItems;


    $calcMatrixFormulasItems=[
        'controlgroup'=>true,
        'group'=>false,
        'label'=>'Дополнительная матрица значений',
        'showIn'=>['calcMatrixFormulasGroup'],
        'mess'=>[
            'ADD_CONTROL'=>'Добавить значение',
            'SELECT_CONTROL'=>'Выбeрите значение...',
            'DELETE_CONTROL'=>'Удалить все значения',
        ],
        'children'=>[
            [
                'controlId'=>'calcUserAutoFormulasItem',
                'group'=>false,
                'label'=>'Поле',
                'showIn'=>['calcMatrixFormulasGroup'],
                'mess'=>[
                    'DELETE_CONTROL'=>'Удалить значение',
                ],
                'control'=>[ 
                    [
                        'id'=>'var',
                        'name'=>'var',
                        'type'=>'label',
                        'class'=>'control-string',
                        'defaultText'=>'Переменная',
                        'defaultValue'=>'Переменная',
                    ],
                    [
                        'id'=>'label',
                        'name'=>'label',
                        'type'=>'label',
                        'text'=>'Наименование',
                        'defaultText'=>'Наименование',
                        'defaultValue'=>'Наименование',
                    ],
                    [
                        'id'=>'expression',
                        'name'=>'expression',
                        'type'=>'input',
                        'defaultText'=>'укажите формулу расчета...'
                    ],                
                ]
            ],
        ],
    ];

    // $conditionRules[]=$calcMatrixFormulasItems;
}

?>
<tr><td>
    <script>(function(){
        let conditionRules=<?=Json::encode($conditionRules, JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS | JSON_OBJECT_AS_ARRAY)?>;
        let currentValues=<?=Json::encode($currentValues, JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS | JSON_OBJECT_AS_ARRAY)?>;
        function konturCalculatorFormulasInit() {
            konturCalculatorFormulasInit.TreeCondCtrlLabel=function(parentContainer, state, arParams) {
	            if (BX.TreeCondCtrlInput.superclass.constructor.apply(this, arguments)) { this.Init(); }
	            return this.boolResult;
            };
            BX.extend(konturCalculatorFormulasInit.TreeCondCtrlLabel, BX.TreeCondCtrlAtom);
            konturCalculatorFormulasInit.TreeCondCtrlLabel.prototype.Init=function() {
                if (this.boolResult) {
                    this.parentContainer = BX(this.parentContainer);
                    if (!!this.parentContainer)	{
                        if (this.type === 'label') {
                            this.parentContainer.appendChild(BX.create('SPAN', {
                                props: { className: !!this.arStartParams.class ? this.arStartParams.class : 'control-prefix' },
                                html: BX.util.htmlspecialchars((this.IsValue() ? this.valuesContainer[this.id] : ''))
                            }));
                        }
                        else { this.CreateLink(); }
                    }
                    else { this.boolResult = false; }
                }
                return this.boolResult;
            };
            konturCalculatorFormulasInit.tree=new BX.TreeConditions({
                'parentContainer': 'kontur_calculator_calc_formulas_tree',
                'form': 'form_element_35_form',
                'sepID': '__',
                'prefix': 'KONTUR_CALC_FORMULAS',
                'atomtypes': {'label': konturCalculatorFormulasInit.TreeCondCtrlLabel}
            }, currentValues, conditionRules);
            BX.findChildren(
                BX.findParent(BX('kontur_calculator_calc_formulas_tree'), {className:'adm-detail-content'}), 
                {class:'adm-detail-title'}
            )[0].innerHTML='Настройка формул расчета';
        }
        document.addEventListener('DOMContentLoaded', konturCalculatorFormulasInit);
    })();
    </script>
    <? $vars=[];
    foreach($currentFormFields as $section=>$fields) {
        foreach($fields as $field) {
            $vars[$field['VAR'].$field['VAR_N']]=$field['NAME'];
        }
    }
    ksort($vars);
    ?>
    <div class="adm-info-message-wrap">
        <div class="adm-info-message" style="padding-left:10px;width:75%;">
            <span class="adm-info-message-title">Доступные переменные для формул</span>
            <? if(empty($vars)) { ?>
                <br/>
                Для расчета формул необходимо задать переменные в разделе "Поля формы".
            <? } else { ?>
            <table class="calc__formulas_table-vars">
                <tbody>
                    <? foreach($vars as $varname=>$name) { ?>
                    <tr>
                        <td><?= $varname; ?></td>
                        <td><?= $name; ?></td>
                    </tr>
                    <? } ?>
                </tbody>
            </table>
            <? } ?> 
        </div>
    </div>
    <div id="kontur_calculator_calc_formulas_tree"></div>
    <div class="adm-info-message-wrap">
        <div class="adm-info-message" style="padding-left:10px">
        <span class="adm-info-message-title">Инструкция по управлению формулами расчета</span>
        <br/>
        1) Привязка полей для формул происходит по имени переменной.
        <br/>
        2) Формулы для авторасчета не должны содержать переменной поля для которого указывается формула
        <br/>
        3) Если для значения в разделе <span class="adm-info-message-title">"Дополнительная матрица значений"</span> добавить
        несколько одинаковых полей, то будет использовано последнее доступное значение.
        <br/>       
        </div>
    </div>
    <style>
    .calc__formulas_table-vars {
        width: 100%;
    }
    .calc__formulas_table-vars tbody {
        display: flex;
        margin-top: 10px;
        justify-content: start;
	    flex-wrap: wrap;
    }
    .calc__formulas_table-vars tr {
        border: 1px solid #cabc90;
        background: #f5f9f9;
        margin-bottom: 5px;
	    margin-left: 5px;
    }
    .calc__formulas_table-vars td {
        padding: 3px 10px;
    }
    .calc__formulas_table-vars tr td:first-child {
        font-weight: bold;
        font-family: monospace;
        border-right: 1px solid #cabc90;
        color: #000;
    }
    .calc__formulas_table-vars tr td:last-child {
        font-weight: normal;
        font-family: Arial;
        font-size: 12px;
    }
    </style>
</td></tr>