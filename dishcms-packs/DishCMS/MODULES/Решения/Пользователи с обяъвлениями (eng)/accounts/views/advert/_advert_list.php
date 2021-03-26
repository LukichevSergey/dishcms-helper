<?php
use common\components\helpers\HYii as Y;
use crud\models\ar\accounts\models\Advert;

Y::js('advertlist_download_doc_file', '$(document).on("click",".js-advert-doc-file",function(e){
let id=$(e.target).closest("a").data("item");if(id&&id!="undefined"){$.post("/accounts/advert/checkDownload/"+id,function(r){
if(r.success){window.location.href="/download-doc/"+id;}else{$.fancybox.open(\'<div class="account__alert">You do not have permission to download this file.</div>\');}
},"json");}e.preventDefault();return false;
});', \CClientScript::POS_READY);

Y::js('advertlist_respond_btn-respond', '$(document).on("click",".js-advert-respond-btn-respond",function(e){
$.fancybox.close();let btn=$(e.target).closest("a"),id=btn.data("item"),msg="";if(id&&id!="undefined"){$.post("/accounts/advert/respond/"+id,function(r){
if(r.success){btn=$(".js-advert-respond-btn[data-item="+id+"]");btn.addClass("btn-advert-respond-sended");btn.attr("href","javascript:;");btn.text("Sended");}
$.fancybox.open(\'<div class="account__respond-alert">\'+r.data.msg+\'</div>\');
},"json");}e.preventDefault();return false;
});', \CClientScript::POS_READY);

Y::js('advertlist_respond_btn', '$(document).on("click",".js-advert-respond-btn",function(e){
let btn=$(e.target).closest("a"),id=btn.data("item"),msg="";if(id&&id!="undefined"){$(".js-advert-respond-btn-respond").attr("data-item", id);
$.fancybox.open($(".js-advert-confirm-response").html());
}e.preventDefault();return false;
});$(document).on("click",".js-advert-respond-btn-cancel",function(e){$.fancybox.close();});', \CClientScript::POS_READY);

?>
<div class="advert__fancybox-confirm-response js-advert-confirm-response" style="display:none">
	<div class="account__respond-alert">
		Send response to an ad?
		<br/><br/>
		<div style="width:100%;display:flex;justify-content:center">
			<a href="javascript:;" data-item="" class="btn js-advert-respond-btn-respond" style="width:47%;margin-right:10px;height:36px;">Respond</a>
			<a href="javascript:;" class="btn btn-cancel js-advert-respond-btn-cancel" style="width:47%;height:36px;">Cancel</a>
		</div>
	</div>
</div>
<div class="container">
	<div class="marketplace">
		<div class="marketplace-head">
			<div class="marketplace-head-item<?php if($type == Advert::TYPE_AIRLINE_PARTS_WANTED) echo ' active'; ?>">
				<a href="/airline-wanted" class="marketplace-head-item-inner">
					<div class="marketplace-head-item-title">Airline/MRO</div>
					<div class="marketplace-head-item-subtitle">Parts Wanted</div>
				</a>
			</div>

			<div class="marketplace-head-item<?php if($type == Advert::TYPE_AIRLINE_FOR_SALE) echo ' active'; ?>">
				<a href="/airline-sale" class="marketplace-head-item-inner">
					<div class="marketplace-head-item-title">Airline/MRO</div>
					<div class="marketplace-head-item-subtitle">For Sale</div>
				</a>
			</div>

			<div class="marketplace-head-item<?php if($type == Advert::TYPE_AIRPORT_EQUIPMENT_WANTED) echo ' active'; ?>">
				<a href="/airport-wanted" class="marketplace-head-item-inner">
					<div class="marketplace-head-item-title">Airports</div>
					<div class="marketplace-head-item-subtitle">Equipment Wanted</div>
				</a>
			</div>

			<div class="marketplace-head-item<?php if($type == Advert::TYPE_AIRPORT_FOR_SALE) echo ' active'; ?>">
				<a href="/airport-sale" class="marketplace-head-item-inner">
					<div class="marketplace-head-item-title">Airports</div>
					<div class="marketplace-head-item-subtitle">For Sale</div>
				</a>
			</div>
		</div>

		<div class="marketplace-table">
			<div class="marketplace-table-head">
				<div class="marketplace-table-row">
					<div class="marketplace-num">№</div>
					<div class="marketplace-adverts">Adverts</div>
					<div class="marketplace-aircraft"><?php 
					if(in_array($type, [Advert::TYPE_AIRPORT_FOR_SALE, Advert::TYPE_AIRPORT_EQUIPMENT_WANTED])) {
					    echo 'Equipment';
					}
					else {
					    echo 'Airctraft';
					}
					?>
					</div>
					<div class="marketplace-category">Category</div>
					<div class="marketplace-posted">Posted on</div>
					<div class="marketplace-list">Part list</div>
					<div class="marketplace-respond"></div>
				</div>
			</div>

            <?php 
        		$this->widget('zii.widgets.CListView', [
                    'dataProvider'=>$dataProvider,
                    'itemView'=>'_advert_item',   
        		    'itemsCssClass'=>'marketplace-table-body',
        		    'template'=>'{items}<div class="marketplace-pager">{pager}</div>',
        		    'pagerCssClass'=>'pagination',
        		    'pager'=>[
        		        'lastPageLabel'=>false,
        		        'firstPageLabel'=>false,
        		        'prevPageLabel'=>false,
        		        'nextPageLabel'=>'next',
        		        'header'=>''
        		    ],
        		    'emptyText'=>'No ads found'
                ]);
    		?>
		</div>
	</div>
</div>