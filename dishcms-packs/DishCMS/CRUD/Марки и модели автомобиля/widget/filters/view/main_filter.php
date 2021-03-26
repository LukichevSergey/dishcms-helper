<?php
use common\components\helpers\HYii as Y;

$categoryUrl=null;
if($this->owner->category_id) {
    $categoryUrl=$this->owner->createUrl('/shop/category', ['id'=>$this->owner->category_id]);
}

$categories=Category::model()->roots()->findAll(['select'=>'id, title']);

$exists=[];
if($products=\Product::model()->findAll(['select'=>'`t`.`category_id`, `t`.`car_brand_id`, `t`.`car_model_id`'])) {
    foreach($products as $product) {
        if($product->car_brand_id && $product->car_model_id) {
            $exists[$product->category_id][$product->car_brand_id][$product->car_model_id]=1;
        }
    }
}

$carBrands=\crud\models\ar\CarBrand::model()->published()->findAll(['order'=>'title ASC']);
$carModels=\crud\models\ar\CarModel::model()->published()->findAll(['order'=>'title ASC']);
?>
<div  class="select-wrap">
    <div class="select container">
        <h2>Купить для вашего автомобиля</h2>
        <form action="#" class="form">
            <div class="select-item">
            	<select id="mainfilter_cid">
            	<option value disabled selected>Выбрать вид товара</option>
            	<?php 
            	foreach($categories as $category) {
            	    $url=$this->owner->createUrl('/shop/category', ['id'=>$category->id]);
            	    $selected=($url == $categoryUrl) ? ' selected="selected"' : '';
            	    ?><option data-url="<?=$url?>"<?= $selected; ?> value="<?=$category->id?>"><?= $category->title; ?></option><?php 
            	}
            	?></select>                
            </div>
            <div class="select-item">
            	<select id="mainfilter_bid">
            	<option value disabled selected>Выбрать марку автомобиля</option>
            	<option value=""></option><?php 
            	foreach($carBrands as $carBrand) {
            	    $selected=($carBrand->id == $this->owner->carbrand) ? ' selected="selected"' : '';
            	    ?><option style="display:none" <?= $selected; ?> value="<?=$carBrand->id?>"><?= $carBrand->title; ?></option><?php 
            	}
            	?></select>
            </div>
            <div class="select-item">
                <select id="mainfilter_mid">
                <option value disabled selected>Выбрать модель автомобиля</option><?php
                foreach($carModels as $carModel) {
                    $selected=($carModel->id == $this->owner->carmodel) ? ' selected="selected"' : '';
                    ?><option style="display:none" <?= $selected; ?> data-brand="<?=$carModel->brand_id?>" value="<?=$carModel->id?>"><?= $carModel->title; ?></option><?php 
            	}
            	?></select>
            </div>
        </form>
        <a href="jsvascript:;" class="select-btn js-main-filter-submit"><?= (Y::isAction($this->owner, 'shop') ? 'ПОКАЗАТЬ' : 'В МAГАЗИН'); ?></a>
    </div>
</div>
<script>$(document).ready(function(){
	let ex=<?=json_encode($exists)?>;
	function n(name){return '#mainfilter_'+name;}
	function j(name){return $(n(name));}
	function v(name){return j(name).val();}
	function onchange(e,params){
		function r(){return (typeof params == 'undefined') || (typeof params.reset == 'undefined') || params.reset;}
		switch(e.data.own) {
			case 'cid': j('bid').find('option').hide();j('bid').find('option').attr('disabled', 'disabled');
			case 'bid': j('mid').find('option').hide();j('mid').find('option').attr('disabled', 'disabled');
		}
		for(let cid in ex) {
			if(j('cid').val() == cid) {
    			for(let bid in ex[cid]) {
    				j('bid').find('option').each(function(){
        				if($(this).attr('value') == bid) {$(this).show();$(this).removeAttr('disabled');}
        			});
        			if(j('bid').val() == bid) {
        				for(let mid in ex[cid][bid]) {
            				j('mid').find('option').each(function(){
                				if($(this).attr('value') == mid) {$(this).show();$(this).removeAttr('disabled');}
                			});
            			}
        			}
    			}
			}
		}
		let first=false;
		switch(e.data.own) {
			case 'cid': 
			    j('bid').find('option').each(function(){if(!first && ($(this).css('display')!='none')){first=$(this).attr('value');if(r()){j('bid').val(first);}};});
			    if(r() && !v('bid') && first){j('bid').val(first);j('bid').trigger('change');}
			    else if(r() && !first){j('bid').val('');}
			    j('bid').select2();
			    j('bid').trigger('change');
			    break;
			case 'bid': 
			    j('mid').find('option').each(function(){if(!first && !$(this).attr('disabled')){first=$(this).attr('value');if(!r()){j('mid').val(first);}};});
			    if(r() && !v('mid') && first){j('mid').val(first);}
			    else if(r() && !first){j('mid').val('');}
			    j('mid').select2();
		}
	}
    $(document).on('click', '.js-main-filter-submit', function(e) {
        let op=j('cid').find('option[value='+v('cid')+']');
        if(op && op.data('url')) {
            let url=op.data('url');
            let params='';
            if(v('bid')) params+='?b=' + v('bid');
            if(v('mid')) params+=(params.length?'&':'?') + 'm=' + v('mid');
    		window.location.href=op.data('url') + params;
        }
    	e.preventDefault();return false;
    });
    $(document).on('change',n('cid'),{own:'cid'},onchange);
    $(document).on('change',n('bid'),{own:'bid'},onchange);
    $(document).on('change',n('mid'),{own:'mid'},onchange);
    j('cid').trigger('change',{reset:false});
});
</script>