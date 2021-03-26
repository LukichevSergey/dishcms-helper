<?
use Bitrix\Main\Loader;
use Bitrix\Main\Web\Json;
use Kontur\Calculator\Helper;

// $error = new _CIBlockError(2, 'ERROR_CODE', 'message');

if(Loader::includeModule('kontur.calculator')) {
    if($REQUEST_METHOD=="POST" && strlen($Update)>0 && $view!="Y" && (!$error) && empty($dontsave)) {
        // сохранение настроек полей формы
        $calcPropFormFieldsId=Helper::getFormFieldPropertyId();
        if($calcPropFormFieldsId) {
            $calcPropFormFieldsValues=['LIST'=>[], 'USER'=>[], 'SYSTEM'=>[]];
            if(!empty($_POST['KONTUR_CALC_FORM_FIELDS'])) {
                foreach($_POST['KONTUR_CALC_FORM_FIELDS'] as $key=>$val) {
                    if(isset($val['controlId'])) {
                        switch($val['controlId']) {
                            case 'calcSystemNumberTypeField':
                                $calcPropFormFieldsValues['SYSTEM'][]=[
                                    'VAR'=>Helper::normalizeString($val['var']??''),
                                    'VAR_N'=>Helper::normalizeString($val['var_n']??''),
                                    'TYPE'=>Helper::PROP_FORM_FIELD_TYPE_FLOAT,
                                    'NAME'=>Helper::normalizeString($val['name']??''),
                                    'DEFAULT'=>Helper::normalizeString($val['default']??'')
                                ];
                                break;

                            case 'calcListEnumTypeField':
                                $calcPropFormFieldsValues['LIST'][]=[
                                    'VAR'=>Helper::normalizeString($val['var']??''),
                                    'VAR_N'=>Helper::normalizeString($val['var_n']??''),
                                    'TYPE'=>Helper::PROP_FORM_FIELD_TYPE_ENUM,
                                    'SORT'=>(int)($val['sort']??500),
                                    'NAME'=>Helper::normalizeString($val['name'] ?? ''), 
                                    'VALUES'=>Helper::parseEnumValues($val['values']??'')
                                ];
                            break;

                            case 'calcUserIntTypeField':
                                $calcPropFormFieldsValues['USER'][]=[
                                    'VAR'=>Helper::normalizeString($val['var']??''),
                                    'VAR_N'=>Helper::normalizeString($val['var_n']??''),
                                    'TYPE'=>Helper::PROP_FORM_FIELD_TYPE_INT,
                                    'SORT'=>(int)($val['sort']??500),
                                    'NAME'=>Helper::normalizeString($val['name']??''),                                     
                                    'DEFAULT'=>Helper::normalizeString($val['default']??'')
                                ];
                            break;

                            case 'calcUserFloatTypeField':
                                $calcPropFormFieldsValues['USER'][]=[
                                    'VAR'=>Helper::normalizeString($val['var']??''),
                                    'VAR_N'=>Helper::normalizeString($val['var_n']??''),
                                    'TYPE'=>Helper::PROP_FORM_FIELD_TYPE_FLOAT,
                                    'SORT'=>(int)($val['sort']??500),
                                    'NAME'=>Helper::normalizeString($val['name'] ?? ''), 
                                    'DEFAULT'=>Helper::normalizeString($val['default']??'')
                                ];
                            break;
                        }
                    }
                }

                if(!empty($calcPropFormFieldsValues['USER'])) {
                    usort($calcPropFormFieldsValues['USER'], function($a, $b) {
                        return ($a['SORT'] === $b['SORT']) ? 0 : ($a['SORT'] < $b['SORT'] ? -1 : 1);
                    });
                }

                if(!empty($calcPropFormFieldsValues['LIST'])) {
                    usort($calcPropFormFieldsValues['LIST'], function($a, $b) {
                        return ($a['SORT'] === $b['SORT']) ? 0 : ($a['SORT'] < $b['SORT'] ? -1 : 1);
                    });
                }

                $calcPropFormFieldsValuesJson=Json::encode($calcPropFormFieldsValues, JSON_UNESCAPED_UNICODE);
                $PROP[$calcPropFormFieldsId]=$calcPropFormFieldsValuesJson;
                $_POST['PROP'][$calcPropFormFieldsId]=$calcPropFormFieldsValuesJson;
                $_REQUEST['PROP'][$calcPropFormFieldsId]=$calcPropFormFieldsValuesJson;
            }
        }

        // сохранение формул для калькулятора
        $calcPropCalcFormulasId=Helper::getCalcFormulasPropertyId();
        if($calcPropCalcFormulasId) {
            $calcPropCalcFormulasValues=['BASE'=>[], 'AUTO'=>[], 'MATRIX'=>[]];
            if(!empty($_POST['KONTUR_CALC_FORMULAS'])) {
                foreach($_POST['KONTUR_CALC_FORMULAS'] as $key=>$val) {
                    if(isset($val['controlId'])) {
                        $controlId=$val['controlId'];
                        switch($controlId) {
                            // формула расчета общей массы
                            case 'calcBaseFormulasTotalMass':
                                $calcPropCalcFormulasValues['BASE']['MASS']=[
                                    'EXPRESSION'=>Helper::normalizeExpression($val['expression']??''),
                                ];
                                break;

                            // формула расчета итоговой стоимости
                            case 'calcBaseFormulasTotalPrice':
                                $calcPropCalcFormulasValues['BASE']['PRICE']=[
                                    'EXPRESSION'=>Helper::normalizeExpression($val['expression']??''),
                                ];
                                break;

                            // формулы авторасчета
                            case 'calcUserAutoFormulasItem':
                                if(preg_match('/_calcUserAutoFormulasItem_([A-Z0-9]+)$/', $key, $m)) {
                                    $calcPropCalcFormulasValues['AUTO'][$m[1]]=[
                                        'EXPRESSION'=>Helper::normalizeExpression($val['expression']??''),
                                    ];
                                }
                                break;

                            default:
                                // формулы матрицы
                                if(preg_match('/^calcMatrixFormulasItemValue_([A-Z0-9]+)_([a-z0-9]+)_([A-Z0-9]+)$/', $controlId, $m)) {
                                    $fieldVarName=$m[1];
                                    $fieldValueHash=$m[2];
                                    $systemVarName=$m[3];
                                    $calcPropCalcFormulasValues['MATRIX'][$fieldVarName][$fieldValueHash][$systemVarName]=[
                                        'EXPRESSION'=>Helper::normalizeExpression($val['expression']??''),
                                    ];
                                }
                                break;
                        }
                    }
                }

                $calcPropCalcFormulasValuesJson=Json::encode($calcPropCalcFormulasValues, JSON_UNESCAPED_UNICODE);
                $PROP[$calcPropCalcFormulasId]=$calcPropCalcFormulasValuesJson;
                $_POST['PROP'][$calcPropCalcFormulasId]=$calcPropCalcFormulasValuesJson;
                $_REQUEST['PROP'][$calcPropCalcFormulasId]=$calcPropCalcFormulasValuesJson;
            }
        }
    }

    /**
     * Обработчик, котоырй будет вызвана после успешного сохранения элемента
     *
     * @param [] $arFields
     * @return void
     */
    function BXIBlockAfterSave($arFields) {
    }
}
?>