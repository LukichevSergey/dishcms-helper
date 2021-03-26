/protected/controllers/SiteController.php
....
    public function actionExportBrokenImages()
    {
        if(!empty($_GET['h']) && ($_GET['h'] == '<<СГЕНЕРИРОВАТЬ УНИКАЛЬНЫЙ ХЭШ>>')) {

        set_time_limit(1800);

        $mode=empty($_GET['m']) ? 'broken' : $_GET['m'];

        $this->layout=false;

        $data=array();
        $products=array();
        $count=0;
        $total=0;
        $step=0;

        if($mode == 'regenerate') {
            $total=\CImage::model()->count();
            $limit=empty($_GET['limit']) ? 20 : (int)$_GET['limit'];
            $step=empty($_GET['step']) ? 0 : (int)$_GET['step'];
            $offset=$step * $limit;
            $count=$offset;
            $models=\CImage::model()->findAll(array('limit'=>$limit, 'offset'=>$offset));
             foreach($models as $model) {
                $model->getTmbUrl();
             }
        }
        else {
        $models=\CImage::model()->findAll();
        foreach($models as $model) {
            $path=\Yii::getPathOfAlias('webroot.images.'.$model->model);
            if(!is_file($path.'/'.$model->filename)) {
                $data[]=$model;
            }
        }

        $pmodels=Product::model()->findAll(array('select'=>'id,title'));
        foreach($pmodels as $pmodel) {
            if(!$pmodel->getFullImg(true)) {
                $products[]=$pmodel;
            }
        }
        }

        $this->render('broken', compact('data', 'products', 'mode', 'count', 'total', 'step'));

        }
        else {
            throw new \CHttpException(404);
        }
    }
