Product.php
    private function createMainImages()
    {
        $path     = $this->path;
        $ext      = strtolower($this->mainImg->extensionName);
        $name     = $this->id. '.' .$ext;

        $this->mainImg->saveAs($path .DS. $name);

---->
       	$uploaded = new stdClass();
       	$uploaded->type  = $this->mainImg->type;
       	$uploaded->src   = $path. DS .$name;
       	$uploaded->fname = $name;
       	$uploaded->path  = $path;
        $uploaded->tmb_width  = $item->tmb_width;
        $uploaded->tmb_height  = $item->tmb_height;
        UploadHelper::getInstance()->createWatermark($uploaded);

/public_html/protected/extensions/imagemodifier/upload.php
fix error
[\.-\w]+ заменить на [\.\-\w]+


/public_html/protected/extensions/helpers/UploadHelper.php

    private function resize($onlyThumbnail = false)
            if (true) {//$watermark && isset(Yii::app()->params['watermark'])) {
                $this->createWatermark($item);

если нужно установить позицию				
    public function createWatermark($item)
	
--->    $image->image_watermark_position='BR'; // B-bottom,R-right,L-left,T-top. RL - центрирование
        $image->jpeg_quality = 100;
        $image->file_new_name_body = $image->file_src_name_body;
		
		документация https://github.com/verot/class.upload.php/blob/master/README.md