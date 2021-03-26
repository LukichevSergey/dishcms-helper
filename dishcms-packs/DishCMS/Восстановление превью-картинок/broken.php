/protected/views/site/broken.php

<meta charset="utf8" />
<? if($mode == 'regenerate'): ?>
<h1>Перегенерация превью-изображений</h1>

<p>Перегенерировано изображений: <b><?=$count?></b> из <b><?=$total?></b></p>

<?if($count < $total):?>
<script>window.location.href=window.location.origin + window.location.pathname + "?h=<?=$_GET['h']?>&m=regenerate&step=<?=$step+1?>";</script>
<?else:?>
<br/><b>Перегенрация завершена!</b>
<?endif?>
<? else: ?>
<h1>Основные картинки товаров (не найденные файлы)</h1>
<table>
    <tr>
        <th>Имя файла</th>
        <th>Наименование товара</th>
        <th>&nbsp;</th>
    </tr>
<? foreach($products as $model): ?>
    <tr>
        <td><?=$model->getFullImg(false, true)?></td>
        <td><?=$product->title?></td>
        <td><?=CHtml::link('перейти', array('/shop/product', 'id'=>$product->id), array('target'=>'_blank'))?></td>
    </tr>
<? endforeach; ?>
</table>



<h1>Дополнительные картинки страниц и товаров (не найденные файлы)</h1>
<table>
    <tr>
        <th>Имя файла</th>
        <th>Наименование товара</th>
        <th>&nbsp;</th>
    </tr>
<? foreach($data as $model): ?>
    <?if($model->model == 'product'):?>
        <?if($product=Product::model()->findByPk($model->item_id,array('select'=>'id,title'))):?>
            <tr>
                <td>/images/product/<?=$model->filename?></td>
                <td><?=$product->title?></td>
                <td><?=CHtml::link('перейти', array('/shop/product', 'id'=>$product->id), array('target'=>'_blank'))?></td>
            </tr>
        <?else:?>
            <tr style="background-color:#F44336">
                <td>/images/product/<?=$model->filename?></td>
                <td colspan="2">товар не найден</td>
            </tr>
        <?endif?>
    <?else:?>
     <tr style="background-color:#9E9E9E">
           <td>/images/<b><?=$model->model?></b>/<?=$model->filename?></td>
           <td colspan="2">не является товаром</td>
     </tr>
    <?endif?>
<? endforeach; ?>
</table>
<? endif; ?>

