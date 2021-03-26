<?php
/** @var  XlsImportForm $model */
?>
<div class="panel panel-info js-import-panel">
	<div class="panel-heading">Идет процесс импорта товаров...</div>
	<div class="panel-body" style="padding-top:35px">
    	<div class="progress progress-striped active js-import-progress-bar">
          <div class="progress-bar progress-bar-warning"  role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;color:#000">
            <span style="position:absolute;padding-left:10px">Выполнено: <span class="js-import-progress-bar-percent">0%</span>. 
            Выгружено товаров: <span class="js-import-progress-bar-imported">0</span> из <?= $model->getTotalRows(); ?></span> 
          </div>
        </div>
    </div>
</div>
<script>;$(document).ready(function(){
	function sleep(ms){ms+=new Date().getTime();while(new Date() < ms){}} 
	var $panel=$(".js-import-panel"), 
	$bar=$panel.find(".js-import-progress-bar"),
	$progress=$bar.find(".progress-bar"),
	$percent=$progress.find(".js-import-progress-bar-percent"),
	$imported=$progress.find(".js-import-progress-bar-imported");
	function next(response) {
		$.post("/cp/xlsimport/next", {iteration: response.data.iteration + 1, hash: response.data.hash}, function(response) {
			$progress.attr("aria-valuenow", response.data.percent);
			$progress.css("width", response.data.percent + "%");
			$percent.text(response.data.percent + "%");
			$imported.text(response.data.imported_count);
			if(response.data.percent >= 100) {
				$panel.removeClass("panel-info").addClass("panel-success");
				$panel.find(".panel-heading").text("Импорт товаров успешно завершен");
				$bar.removeClass("active progress-striped")
				$progress.removeClass("progress-bar-warning").addClass("progress-bar-success");
				$panel.after('<br/><a href="/cp/shop" class="btn btn-primary">Перейти в каталог</a>');
			}
			else {
				if(+response.data.imported_count > 0) {
					sleep(<?= $model->getDelay() * 1000; ?>);
					next(response);
				}
			}
		}, "json").fail(function(jqXHR, textStatus, errorThrown){
			$panel.removeClass("panel-info").addClass("panel-danger");
			$panel.find(".panel-heading").text("Произошла ошибка: " + textStatus);
			$bar.removeClass("active progress-striped")
			$progress.removeClass("progress-bar-warning").addClass("progress-bar-danger");
		});
	}
	next({data: <?= json_encode($model->getAjaxData()); ?>});
});</script>
