<?
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

if($this->pickup && ($this->value == 'pickup')) {
    echo $this->pickupLabel;
}
elseif($this->cdek && ($this->value == 'cdek')) {
    echo $this->cdekLabel . '&nbsp;(' . \CHtml::ajaxLink('подробнее', '/ecommerce/cdek/admin/default/orderView/id/'.$this->orderId, [
        'success'=>'js:function(r){var $i=$("#cdek_order_info-'.$this->orderId.'");if($i.is(":visible"))$i.hide();else{$i.html($.parseHTML(r));$i.show();}}'
    ], ['class'=>'']) . ')';
    ?><div style="display:none" id="cdek_order_info-<?=$this->orderId?>"></div><?
}
elseif($this->rpochta && ($this->value == 'rpochta')) {
    echo $this->rpochtaLabel. '&nbsp;(' . \CHtml::ajaxLink('подробнее', '/ecommerce/rpochta/admin/default/orderView/id/'.$this->orderId, [
        'success'=>'js:function(r){var $i=$("#rpochta_order_info-'.$this->orderId.'");if($i.is(":visible"))$i.hide();else{$i.html($.parseHTML(r));$i.show();}}'
    ], ['class'=>'']) . ')';
    ?><div style="display:none" id="rpochta_order_info-<?=$this->orderId?>"></div><?
}
else {
    echo $this->emptyValue;
}
?>
