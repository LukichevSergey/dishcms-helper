<?
/**
 * Скрипт восстановления заказа из письма
 */
$s='Пони, 4 расцветки, 20см.
Артикул: 2285 
Цена: 130.00	руб. 
Количество: 4	шт. 
Итого: (130 руб) x 4 шт = 520 руб 
Мишки кучерявые мал. с бантиком в ассортименте
Артикул: 2254 
Цена: 170.00	руб. 
Количество: 2	шт. 
Итого: (170 руб) x 2 шт = 340 руб 
Хаски, 20см.
Артикул: 3916 
Цена: 170.00	руб. 
Количество: 2	шт. 
Итого: (170 руб) x 2 шт = 340 руб';

$r=[];
$h=uniqid('h');
preg_match_all('/^(.*)$/m', $s, $m);
$i=0;
foreach($m[0] as $line) {
    if(preg_match('/^Итого:/', $line)) {
        $h=uniqid('h');
        $i=0;
        $r[$h]=[
            'id'=>['label'=>'Идентификатор', 'value'=>-1],
            'model'=>['label'=>'Модель', 'value'=>'Product'],
        ];
        continue;
    }
    
    switch($i++) {
        case 0: $r[$h]['title']=['label'=>'Заголовок', 'value'=>trim($line)]; break;
        default: 
            if(preg_match('/Артикул/', $line)) {
                $r[$h]['code']=['label'=>'Артикул', 'value'=>trim(preg_replace('/Артикул:(.*)/', '$1', trim($line)))];
            }
            elseif(preg_match('/Цена/', $line)) {
                $r[$h]['price']=['label'=>'Цена', 'value'=>trim(preg_replace('/Цена:(.*)руб./', '$1', trim($line)))]; 
            }
            elseif(preg_match('/Количество/', $line)) {
                $r[$h]['count']=['label'=>'Количество', 'value'=>trim(preg_replace('/Количество:(.*)шт./', '$1', trim($line)))]; 
            }
    }
}
foreach($r as $h=>$_r) if(count($_r) < 3) unset($r[$h]);
//print_r($r);
print_r(serialize($r));