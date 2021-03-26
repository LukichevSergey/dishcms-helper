/protected/models/CImage.php
...
заменить/дополнить метод при необходимости

	public function getTmbUrl($forcy=false)
    {
        $path = YiiBase::getPathOfAlias('webroot') .DS. 'images' .DS. $this->model;
        if (is_file($path .DS. $this->filename) && ($forcy || !is_file($path .DS. 'tmb_' . $this->filename))) {
            $type=exif_imagetype($path .DS. $this->filename);
            if(in_array($type,array(IMAGETYPE_PNG, IMAGETYPE_JPEG))) {
            $upload = new UploadHelper;
            $upload->createThumbnails(array(
                (object) array('path'=>$path, 'filename'=>$this->filename)
            ));
            }
        }
        if(is_file($path .DS. 'tmb_' . $this->filename)) {
            return '/images/'.$this->model.'/tmb_'.$this->filename;
        }
        else {
            return '/images/'.$this->model.'/'.$this->filename;
        }
    }

