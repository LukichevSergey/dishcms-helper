<?php
/** @var \ext\uploader\widgets\UploadFile $this */
$assetUrl=\CHtml::asset(\Yii::getPathOfAlias('ext.uploader.widgets.assets.vendors'));
if(!$this->hash) {
	$this->hash=$this->generateHash();
}
?>
<? if($this->tag) echo \CHtml::openTag($this->tag, $this->tagOptions); ?>
<? 
if($this->attribute && ($this->form instanceof \CActiveForm) && ($this->model instanceof \CModel)) {
	if(!$this->model->{$this->attribute}) $this->model->{$this->attribute}=$this->hash;
    else $this->hash=$this->model->{$this->attribute};
	$this->model->{$this->attribute}=$this->hash;
	echo $this->form->hiddenField($this->model, $this->attribute); 
} ?>
<div class="form-upload form-upload_<?=$this->hash?>">
    <div class="file-upload">
        <label>
            <input type="file" name="files[]" id="file-upload_<?=$this->hash?>" style="display:none" />
            <span class="fileinput-button"><?=$this->label?></span>
        </label>
    </div>
    <div id="file_error_<?=$this->hash?>" style="display:none"></div>
    <div id="progress_<?=$this->hash?>" class="progress" style="display:none">
      <div class="progress-bar progress-bar-striped"></div>
    </div>
    <div class="image_place"></div>
</div>
<? if($this->tag) echo \CHtml::closeTag($this->tag); ?>

<script type="text/javascript">
$(document).ready(function(){
    'use strict';
    $('#file-upload_<?=$this->hash?>').fileupload({
        url: "<?= $this->uploadUrl; ?>",
        singleFileUploads: false,
        limitMultiFileUploads: 10,
        dataType: 'json',
        formData: {hash: "<?=$this->hash?>"},
        done: function (e, data) {
            if(data.result.error==1){
                $('#file_error_<?=$this->hash?>').show();
                $('#file_error_<?=$this->hash?>').html(data.result.errors[0])
            }
            else {
            	$('#file_error_<?=$this->hash?>').hide();
                if("jpg,jpeg,gif,png".indexOf(data.result.ext) > -1) {
                    $('.form-upload_<?=$this->hash?> .image_place').append('<a href="javascript;:" data-href="'+data.result.img+'" data-fancybox="1" rel="form-<?= $this->id; ?>"><i data-src="'+data.result.img+'" class="js-file-delete fas fa-times-circle" title="Удалить">X</i><img src="'+data.result.img+'"></a>');
                }
                else {
                    $('.form-upload_<?=$this->hash?> .image_place').append('<a href="javascript:;"><i data-src="'+data.result.img+'" class="js-file-delete fas fa-times-circle" title="Удалить">X</i><i class="filename fas fa-file-alt" title="'+data.result.origin+'">'+data.result.origin+'</i></a>');
                }
            }

            $('#progress_<?=$this->hash?> .progress-bar').removeClass('active');
			$('#progress_<?=$this->hash?> .progress-bar').addClass('progress-bar-success');
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
			$('#progress_<?=$this->hash?> .progress-bar').removeClass('progress-bar-success');
			$('#progress_<?=$this->hash?>').show();
            $('#progress_<?=$this->hash?> .progress-bar').addClass('active').css(
                'width',
                progress + '%'
            );
        }
    });
    $(document).on("click", ".form-upload_<?=$this->hash?> .js-file-delete", function(e) {
        e.stopImmediatePropagation();
        $.post("<?= $this->deleteUrl; ?>", {filename: $(e.target).data("src")}, function(response){
            if(response.success) {
                $(e.target).parent().remove();
                $('#file_error_<?=$this->hash?>').hide();
            }
            else {
                $('#file_error_<?=$this->hash?>').show();
                $('#file_error_<?=$this->hash?>').html("Повторите попытку удаления файла позже");
            }
        }, "json");
        return false;
    });
});
</script>
<script src="<?=$assetUrl?>/jfileupload/js/vendor/jquery.ui.widget.js"></script>
<script src="<?=$assetUrl?>/jfileupload/js/jquery.fileupload.js"></script>
<script src="<?=$assetUrl?>/jfileupload/js/jquery.fileupload-process.js"></script>
<script src="<?=$assetUrl?>/jfileupload/js/jquery.fileupload-audio.js"></script>
<script src="<?=$assetUrl?>/jfileupload/js/jquery.fileupload-video.js"></script>
<script src="<?=$assetUrl?>/jfileupload/js/jquery.fileupload-validate.js"></script>
<link rel="stylesheet" href="<?=$assetUrl?>/jfileupload/css/jquery.fileupload.css">
<style>
.form-upload #file_error_<?=$this->hash?>{color:#f00;font-size:0.9em;}
.form-upload .image_place a{display:inline-block;margin:2px;vertical-align:top;position:relative;}
.form-upload .image_place a img{max-width:100px;max-height:100px;}
.form-upload .image_place a i.js-file-delete{cursor:pointer;position:absolute;color:#000;font-size:10px;font-style:normal;border:1px solid #000;border-radius:50%;padding:2px 5px;background:rgba(0, 0, 0, 0.2);right:0;top:-10px;}
.form-upload .image_place a i.js-file-delete:hover{cursor:pointer;color: #fff;background: #000;}	
.form-upload .image_place a i.filename{border:1px solid #ccc;padding:15px 5px 2px;}
.fileinput-button{padding:10px;border:1px dashed #ccc;cursor:pointer;}
.fileinput-button:hover{border:1px dashed #999;opacity:0.7;}
.progress-striped .progress-bar,.progress-striped .progress-bar-success{background-image:-webkit-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent)}@-webkit-keyframes progress-bar-stripes{from{background-position:40px 0}to{background-position:0 0}}@keyframes progress-bar-stripes{from{background-position:40px 0}to{background-position:0 0}}.progress{overflow:hidden;height:20px;margin-bottom:20px;background-color:#f5f5f5;border-radius:4px;-webkit-box-shadow:inset 0 1px 2px rgba(0,0,0,.1);box-shadow:inset 0 1px 2px rgba(0,0,0,.1)}.progress-bar{float:left;width:0;height:100%;font-size:12px;line-height:20px;color:#fff;text-align:center;background-color:#428bca;-webkit-box-shadow:inset 0 -1px 0 rgba(0,0,0,.15);box-shadow:inset 0 -1px 0 rgba(0,0,0,.15);-webkit-transition:width .6s ease;transition:width .6s ease}.progress-striped .progress-bar{background-image:linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);background-size:40px 40px}.progress.active .progress-bar{-webkit-animation:progress-bar-stripes 2s linear infinite;animation:progress-bar-stripes 2s linear infinite}.progress-bar-success{background-color:#5cb85c}.progress-striped .progress-bar-success{background-image:linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent)}.progress-striped .progress-bar-info,.progress-striped .progress-bar-warning{background-image:-webkit-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent)}.progress-bar-info{background-color:#5bc0de}.progress-striped .progress-bar-info{background-image:linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent)}.progress-bar-warning{background-color:#f0ad4e}.progress-striped .progress-bar-warning{background-image:linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent)}.progress-bar-danger{background-color:#d9534f}.progress-striped .progress-bar-danger{background-image:-webkit-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);background-image:linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent)}
.progress-bar-success{background-color:#5cb85c;}
.progress{height:3px};
</style>
