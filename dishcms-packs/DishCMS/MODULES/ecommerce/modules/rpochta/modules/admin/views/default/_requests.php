<?php
use common\components\helpers\HArray as A;
use rpochta\models\Order;

$rpochtaOrderDataProvider=\rpochta\models\Order::model()->getDataProvider(['order'=>'create_time DESC']);
?>
<h1>Список накладных</h1>
<?php $this->widget('zii.widgets.grid.CGridView', [
    'id'=>'cdek-orders-grid',
    'itemsCssClass'=>'table table-striped table-bordered table-hover',
    'pagerCssClass'=>'pagination',
    'dataProvider'=>$rpochtaOrderDataProvider,
    'enableSorting'=>false,
    'columns'=>[
        [
            'name'=>'result_ids',
            'header'=>'Номер заказа в сервисе Почта.России',
            'type'=>'raw',
            'value'=>'implode(",", $data->getResultIds())',
            'headerHtmlOptions'=>['style'=>'font-size:13px;width:120px'],
            'htmlOptions'=>['style'=>'text-align:center'],
        ],
        [
            'name'=>'order_id',
            'headerHtmlOptions'=>['style'=>'font-size:13px;width:20px'],
            'htmlOptions'=>['style'=>'text-align:center'],
        ],
        [
            'name'=>'order_number',
            'headerHtmlOptions'=>['style'=>'width:100px'],
        ],
        [
            'header'=>'Получатель',
            'type'=>'raw',
            'value'=>'"ФИО: " . $data->getFullName() . "<br/>Город: {$data->city_name_to}<br/>Телефон: {$data->given_phone}"'
             . '. "<br/>(" . \CHtml::ajaxLink(
                    "подробнее", 
                    "/ecommerce/rpochta/admin/default/orderView/id/{$data->order_id}", [
                        "success"=>\'js:function(r){var $i=$("#rpochta_order_info-\'.$data->order_id.\'");if($i.is(":visible"))$i.hide();else{$i.html($.parseHTML(r));$i.show();}}\'
                ], ["class"=>""]) . ")<div style=\"display:none\" id=\"rpochta_order_info-{$data->order_id}\"></div>"',
            'htmlOptions'=>['style'=>'font-size:13px'],
        ],
        [
            'header'=>'Статус',
            'name'=>'status',
            'type'=>'raw',
            'headerHtmlOptions'=>['style'=>'text-align:center;width:100px'],
            'htmlOptions'=>['style'=>'text-align:center;font-size:13px;vertical-align: middle;'],
            'value'=>'\CHtml::tag("div", ["style"=>"border:1px solid #ccc;","id"=>"rpochta-status-".$data->id,"class"=>\rpochta\components\helpers\HRPochta::getStatusCssClass($data->status)], $data->statusLabels($data->status))'
                . ' . (in_array($data->status, [\rpochta\models\Order::STATUS_WAIT, \rpochta\models\Order::STATUS_RPOCHTA_ERROR])'
                . ' ? \CHtml::ajaxButton("отправить", "/ecommerce/rpochta/admin/default/newOrder/id/{$data->order_id}",'
                . ' ["beforeSend"=>\'js:function(){if(confirm("Подтвердите принудительную отправку заказа в сервис Почта.России")){$("#rpochta-status-btn-\'.$data->id.\'").button("loading");return true;}else{return false;};}\','
                . '"dataType"=>"json",'
                . '"success"=>\'js:function(r){if(r.success){
                $("#rpochta-status-btn-\'.$data->id.\'").button("reset");
                var $s=$("#rpochta-status-\'.$data->id.\'");$s.attr("title", "");
                if(r.data.order_status=='.Order::STATUS_RPOCHTA.'){$s.siblings(".btn").remove();$s.parents("tr:first").find("td:first").html(r.data["result-ids"].join(","));}
                else if(typeof(r.data.errors)!="undefined") {$s.attr("title", (function(){var t="";r.data.errors[0]["error-codes"].forEach(
                function(error){t+=error.description+"\n";}
                );return t;})());} else { $s.attr("title", r.data.error); }
                $s.removeClass();$s.addClass(r.data.html_status_css_class);
                $s.text(r.data.html_status_label);}else{alert("Произошла серверная ошибка.");}}\''
                . '],'
                . ' ["class"=>"btn btn-xs btn-info", "id"=>"rpochta-status-btn-".$data->id, "style"=>"margin-top:5px", "data-loading-text"=>"отправляется..."])'
                . ' : "")',
        ],
        [
            'header'=>'Создано',
            'name'=>'create_time',
            'type'=>'raw',
            'headerHtmlOptions'=>['style'=>'text-align:center;width:100px'],
            'htmlOptions'=>['style'=>'text-align:center;font-size:13px'],
            'value'=>'$data->create_time'
                . ' . \CHtml::ajaxButton("удалить", "/ecommerce/rpochta/admin/default/deleteOrder/id/{$data->id}",'
                . ' ["beforeSend"=>\'js:function(){return confirm("Подтвердите удаление заказа\nВНИМАНИЕ! Заказ не удалится из сервиса Почта.России");}\','
                . '"dataType"=>"json",'
                . '"success"=>\'js:function(r){if(r.success){$("#rpochta-rm-btn-\'.$data->id.\'").parents("tr:first").remove();}
                    else{alert("Произошла серверная ошибка.");}}\''
                . '],'
                . ' ["class"=>"btn btn-xs btn-danger","id"=>"rpochta-rm-btn-".$data->id, "style"=>"margin-top:5px"])'
        ],
    ]
]); 
?>
