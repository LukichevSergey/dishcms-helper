<?php
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HFile;

$imagesPath=Yii::getPathOfAlias($this->imagesAlias);
?>
<div class="row">
        <span class="btn btn-success fileinput-button">
          <i class="glyphicon glyphicon-plus"></i>
          <span>Загрузить фото...</span>

          <!-- The file input field used as target for the file upload widget -->
          <input id="fileupload" type="file" name="files[]" multiple>
        </span>
        <div id="file_error"></div>
        <div id="progress" class="progress">
          <div class="progress-bar progress-bar-striped"></div>
        </div>
        <div class="image_place"></div>
</div>

<script type="text/javascript">
$(document).ready(function(){
    'use strict';
    var url = '/cp/xlsimport/upload';
    $('#fileupload').fileupload({
        url: url,
//        limitMultiFileUploads: 100000,
        dataType: 'json',
        done: function (e, data) {
            if(data.result.error==1){
                $('#file_error').html(data.result.errors[0])
            }
            else{
				$(".image_place").addClass("loaded");
                $('.image_place').append('<div class="item"><a href="/'+data.result.img+'" target="_blank"><div class="img"><img src="/'+data.result.img+'"></div></a><p>'+data.result.filename+'</p></div>');
            }
            $('#progress .progress-bar').removeClass('active');
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .progress-bar').addClass('active').css(
                'width',
                progress + '%'
            );
        }
    });
});
</script>

<script src="/js/jfileupload/js/vendor/jquery.ui.widget.js"></script>
<script src="/js/jfileupload/js/jquery.fileupload.js"></script>
<script src="/js/jfileupload/js/jquery.fileupload-process.js"></script>
<script src="/js/jfileupload/js/jquery.fileupload-audio.js"></script>
<script src="/js/jfileupload/js/jquery.fileupload-video.js"></script>
<script src="/js/jfileupload/js/jquery.fileupload-validate.js"></script>

<link rel="stylesheet" href="/js/jfileupload/css/jquery.fileupload.css">

<style type="text/css">

.image_place img {
    width: 130px;
}

.fileinput-button {
    margin-bottom: 10px;
}
.image_place {
    margin-bottom: 20px;
}
.image_place.loaded:before {
    content: '\041D \043E \0432 \044B \0435  \0444 \043E \0442 \043E \0433 \0440 \0430 \0444 \0438 \0438 :';
    display: block;
    font-weight: bold;
    text-transform: uppercase;
    font-size: 1.5em;
}
.image_place img {
    display: inline-block;
    margin: 2px;
}
.image_place .item {
    width: 20%;
    height: 250px;
    border: 1px solid #ccc;
    padding: 5px;
    margin: 5px;
    display: inline-block;
}
.image_place .item .img {
    height: 190px;
    border: 1px solid #dfdfdf;
    text-align: center;
}
.image_place .item .img {
    max-height: 180px;
    max-width: 100%;
}

.image_place .item p {
    text-align: center;
    text-transform: uppercase;
    font-size: 0.8em;
    height: 20px;
    display: block;
}
</style>
