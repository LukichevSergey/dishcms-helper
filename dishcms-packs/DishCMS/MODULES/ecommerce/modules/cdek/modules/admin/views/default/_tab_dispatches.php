<?php
use common\components\helpers\HArray as A;
use cdek\models\Order;

//$this->owner->widget('\cdek\modules\admin\widgets\DispatchesList');
$cdekOrderDataProvider=\cdek\models\Order::model()->getDataProvider(['order'=>'create_time DESC']);
?>
<h1>Список накладных</h1>
<?php $this->widget('zii.widgets.grid.CGridView', [
    'id'=>'cdek-orders-grid',
    'itemsCssClass'=>'table table-striped table-bordered table-hover',
    'pagerCssClass'=>'pagination',
    'dataProvider'=>$cdekOrderDataProvider,
    'enableSorting'=>false,
    //'filter'=>$cdekOrderDataProvider->model,
    'columns'=>[
        [
            'name'=>'dispatch_number',
            'headerHtmlOptions'=>['style'=>'font-size:13px;width:100px'],
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
            'value'=>'"ФИО: {$data->rec_name}<br/>Город: {$data->rec_city_name}<br/>E-Mail: {$data->rec_email}<br/>Телефон: {$data->rec_phone}"'
                 . '. "<br/>(" . \CHtml::ajaxLink(
                    "подробнее", 
                    "/ecommerce/cdek/admin/default/orderView/id/{$data->order_id}", [
                        "success"=>\'js:function(r){var $i=$("#cdek_order_info-\'.$data->order_id.\'");if($i.is(":visible"))$i.hide();else{$i.html($.parseHTML(r));$i.show();}}\'
                ], ["class"=>""]) . ")<div style=\"display:none\" id=\"cdek_order_info-{$data->order_id}\"></div>"',
            'htmlOptions'=>['style'=>'font-size:13px'],
        ],
        [
            'header'=>'Статус',
            'name'=>'status',
            'type'=>'raw',
            'headerHtmlOptions'=>['style'=>'text-align:center;width:100px'],
            'htmlOptions'=>['style'=>'text-align:center;font-size:13px;vertical-align: middle;'],
            'value'=>'\CHtml::tag("div", ["style"=>"border:1px solid #ccc;","id"=>"cdek-status-".$data->id,"class"=>\cdek\components\helpers\HCdek::getStatusCssClass($data->status)], $data->statusLabels($data->status))'
                . ' . (in_array($data->status, [\cdek\models\Order::STATUS_WAIT, \cdek\models\Order::STATUS_CDEK_ERROR])'
                . ' ? \CHtml::ajaxButton("отправить", "/ecommerce/cdek/admin/default/newOrder/id/{$data->order_id}",'
                . ' ["beforeSend"=>\'js:function(){if(confirm("Подтвердите принудительную отправку заказа в сервис СДЭК")){$("#cdek-status-btn-\'.$data->id.\'").button("loading");return true;}else{return false;};}\','
                . '"dataType"=>"json",'
                . '"success"=>\'js:function(r){if(r.success){
                $("#cdek-status-btn-\'.$data->id.\'").button("reset");
                var $s=$("#cdek-status-\'.$data->id.\'");
                if(r.data.order_status=='.Order::STATUS_CDEK.'){$s.siblings(".btn").remove();$s.siblings(".btn").remove();$s.parents("tr:first").find("td:first").html(r.data["dispatch_number"]);}
                $s.removeClass();$s.addClass(r.data.html_status_css_class);
                $s.text(r.data.html_status_label);}else{alert("Произошла серверная ошибка.");}}\''
                . '],'
                . ' ["class"=>"btn btn-xs btn-info", "id"=>"cdek-status-btn-".$data->id, "style"=>"margin-top:5px", "data-loading-text"=>"отправляется..."])'
                . ' : "")',
        ],        
        [
            'header'=>'Создано',
            'name'=>'create_time',
            'type'=>'raw',
            'headerHtmlOptions'=>['style'=>'text-align:center;width:100px'],
            'htmlOptions'=>['style'=>'text-align:center;font-size:13px'],
            'value'=>'$data->create_time'
                . ' . \CHtml::ajaxButton("удалить", "/ecommerce/cdek/admin/default/deleteOrder/id/{$data->id}",'
                . ' ["beforeSend"=>\'js:function(){return confirm("Подтвердите удаление заказа\nВНИМАНИЕ! Заказ не удалится из сервиса СДЭК");}\','
                . '"dataType"=>"json",'
                . '"success"=>\'js:function(r){if(r.success){$("#cdek-rm-btn-\'.$data->id.\'").parents("tr:first").remove();}
                    else{alert("Произошла серверная ошибка.");}}\''
                . '],'
                . ' ["class"=>"btn btn-xs btn-danger","id"=>"cdek-rm-btn-".$data->id, "style"=>"margin-top:5px"])'
        ],
    ]
]); 
?>
