<?php
use common\components\helpers\HArray as A;
use extend\modules\forms\components\helpers\HForm;
use common\components\helpers\HHash;

$jsId=HHash::ujs();
echo \CHtml::dropDownList(A::get($params, 'name', 'type')."[id]", $type->getId(), HForm::types(), [
    'id'=>$jsId,
    'class'=>'form-control w100', 
    'style'=>'height:16px;min-height:25px;font-size:12px;padding:4px;'
]);

if($typeParams=$type->getParams()) {
    foreach($typeParams as $name=>$label) {
        $id=HHash::u('fieldtype__' . $name);
        
        $fieldLabel=$label;
        $fieldDefault=null;
        $fieldType='string';
        $fieldTypeData=[];
        $fieldTypeOptions=[];
        
        if(is_array($label)) {
            $fieldDefault=A::get($label, 'default');
            $fieldLabel=A::get($label, 'label', $name);
            $fieldType=A::get($label, 'type', 'string');
            $fieldTypeData=A::get($label, 'data');
            $fieldTypeOptions=A::get($label, 'htmlOptions', []);
        }
        
        $fieldName=A::get($params, 'name', 'type') . "[{$type->getId()}][params][{$name}]";
        $fieldValue=A::rget($params, "item.type.{$type->getId()}.params.{$name}", $fieldDefault);
        
        echo \CHtml::label($fieldLabel, $id);
        
        switch($fieldType) {
            case 'checkbox':
                echo \CHtml::checkBox($fieldName, (bool)$fieldValue, A::m($fieldTypeOptions, ['id'=>$id]));
                break;
                
            case 'number':
                echo \CHtml::numberField($fieldName, $fieldValue, A::m($fieldTypeOptions, [
                    'id'=>$id,
                    'class'=>'form-control w100',
                    'style'=>'height:16px;min-height:25px;font-size:12px;padding:4px;'
                ]));
                break;
                
            case 'list':
                echo \CHtml::dropDownList($fieldName, $fieldValue, $fieldTypeData, A::m($fieldTypeOptions, [
                    'id'=>$id,
                    'class'=>'form-control w100',
                    'style'=>'height:16px;min-height:25px;font-size:12px;padding:4px;'
                ]));
                break;
                
            case 'text':
                echo \CHtml::textArea($fieldName, $fieldValue, A::m($fieldTypeOptions, [
                    'id'=>$id,
                    'class'=>'form-control w100',
                    'style'=>'min-height:48px;font-size:12px;padding:4px;'
                ]));
                break;
                
            case 'string':
            default:
                echo \CHtml::textField($fieldName, $fieldValue, A::m($fieldTypeOptions, [
                    'id'=>$id,
                    'class'=>'form-control w100',
                    'style'=>'height:16px;min-height:25px;font-size:12px;padding:4px;'
                ]));
                break;
        }
    }
}
?><script>$("#<?=$jsId?>").on("change",function(e){
	let $t=$(e.target),$p=$t.parents("[data-ajax-tpl-url]:first"),params=eval($p.data("params"));
	if((typeof params.item == 'undefined') || Array.isArray(params.item)) params.item={}; 
	if(typeof params.item.type == 'undefined') params.item.type={}; 
	params.item.type.id=$t.val();
	$.post($p.data("ajax-tpl-url"),{params:params},function(html){$p.html(html);});
});</script>