<?php

class ShopController extends AdminController
{
    public function actionIndex()
    {
        $categories = $this->getCategories();


       # $products = Product::model()->findAll(array('order'=>'new DESC, ordering ASC, id DESC', 'limit'=>16));
        
        

        $products   = Product::model()->findAll(array('order'=>'new DESC, ordering ASC, id DESC', 'limit'=>16));
        $orders     = Order::model()->findAll(array('order'=>'id DESC'));

        $this->render('index', compact('categories', 'products', 'orders'));
    }

    public function actionCategory($id)
    {

        $categories = $this->getCategories($id);
        $bredcrumbs = $this->getBreadcrumbs($id);

        $model = $this->loadCategory($id);
        
        if(!$model)
            throw new CHttpException(404, "Not found");


        $c = new CDbCriteria;
        $c->order = "new DESC, ordering ASC, id DESC";
        $c->condition = "category_id = :model_id";
        $c->params = array(':model_id' => $model->id);
        $products   = Product::model()->findAll($c);
        
        $this->render('category', compact('model', 'categories', 'bredcrumbs', 'id', 'products'));
    }


    /* --- Product CRUD --- */
    public function actionRemoveVideo($id = null)
    {
        if($id){
            $video = Video::model()->findByPk($id);
            unlink(Yii::getPathOfAlias('webroot').'/upload/'.$video->name);
            $video->delete();
        }
    }

    public function actionProductCreate($category_id = null)
    {
        
        $last = Product::model()->lastRecord()->find();
        $model = new Product();

        if (isset($_POST['Product'])) {
            $model->attributes = $_POST['Product'];

            if ($model->save()) {

                if (isset($_FILES['Video'])) {
                    $files = $this->reArrayFiles($_FILES['Video']);

                    $path=Yii::getPathOfAlias('webroot').'/upload/';

                    foreach ($files as $file) {
                        if($file['tmp_name'] && is_uploaded_file($file['tmp_name'])){

                            $tmp_name = $file["tmp_name"];
                            $name = $file["name"];

                            $info = new SplFileInfo($name);

                            $name = uniqid() . '.' . $info->getExtension();

                            if(move_uploaded_file($tmp_name, $path.$name)){
                                $video = new Video;
                                $video->name = $name;
                                $video->product_id = $model->id;
                                $video->save();
                            }

                        }
                    }

                }

                if(isset($_POST['EavValue'])){
                    foreach ($_POST['EavValue'] as $key => $value) {

                        $attributesProduct = new EavValue;
                        $attributesProduct->id_attrs = $key;
                        $attributesProduct->id_product = $model->id;
                        $attributesProduct->value = $value;
                        $attributesProduct->save();

                    }
                }

                $this->redirect(array('index'));
            }
        }

        if ($category_id)
            $model->category_id = $category_id;
        else{
            if(count($last)>0)
	            $model->category_id = $last->category_id;
        }

        $fixAttributes = array();

        if(Yii::app()->params['attributes']){
            $criteria = new CDbCriteria;
            $criteria->condition = "fixed = 1";

            $fixAttributes = EavAttribute::model()->findAll($criteria);
        }

        $this->render('productcreate', compact('model', 'fixAttributes'));
    }

    //Клонирование продукта
    public function actionProductClone($id){
    	$model = $this->loadProduct($id);
    	$cloned_product = new Product;
    	$cloned_product->attributes = $model->attributes;
    	$cloned_product->title = $cloned_product->title."_копия";
    	$cloned_product->alias = $cloned_product->alias."_copy";
        //Если продукт сохранен, то начинаем работу с картинками.
        //Объявляем хелпер.
        $fhelp = new CFileHelper;
        //Получаем изображения.
        $files_to_copy = glob("images/product/$model->id*"); 
        //Если продукт склонировался выполняем нужные действия
        if($cloned_product->save()){
            if(!empty($files_to_copy)) {
                foreach ($files_to_copy as $key => $file) {
                    $ext = $fhelp->getExtension($file);
                    $tmp = explode('/', $file);
                    $tmp = explode('.', $tmp[2]);
                    $tmp = explode('_', $tmp[0]);
                    if(isset($tmp[1])){
                        copy( $file, 'images/product/'.$cloned_product->id.'_'.$tmp[1].'.'.$ext); 
                    }
                    else{
                        copy( $file, 'images/product/'.$cloned_product->id.'.'.$ext);
                    }
                }
            }
            if(!empty($files_to_copy)) {
                foreach ($files_to_copy as $key => $file) {
                    $ext = $fhelp->getExtension($file);
                    copy( $file, 'images/product/'.$cloned_product->id.'.'.$ext);
                }
            } 
            //Обработка дополнительных фотографий.
            $imgages = CImage::model()->findAll(array('condition'=>"item_id = $model->id"));
            if($imgages) {
                foreach ($imgages as $key => $img) {
                    $new_image = new CImage;
                    $new_image->attributes = $img->attributes;
                    $uid = uniqid();
                    $ext = $fhelp->getExtension('/images/product/'.$img->filename);
                    $fname = $uid.'.'.$ext;
                    $new_image->filename = $fname;
                    $new_image->item_id = $cloned_product->id;
                    if(copy('images/product/'.$img->filename, 'images/product/'.$fname)){
                        $new_image->save();
                    }
                }
            }
    		$url = $this->createUrl('shop/productupdate', array('id'=>$cloned_product->id));
    		$this->redirect($url);
    	}
    }

    protected function reArrayFiles(&$file_post) {

        $file_ary = array();
        $file_count = count($file_post['name']);
        $file_keys = array_keys($file_post);

        for ($i=0; $i<$file_count; $i++) {
            foreach ($file_keys as $key) {
                $file_ary[$i][$key] = $file_post[$key][$i];
            }
        }

        return $file_ary;
    }

    public function actionProductUpdate($id, $price = null, $save = false)
    {   

        $model = $this->loadProduct((int)$id);
        if($save) {
            $model->price = $price;
            $model->save(false);
            Yii::app()->end();
        }

        if (isset($_POST['Product'])) {
            $model->attributes = $_POST['Product'];
            if ($model->save()) {

                if (isset($_FILES['Video'])) {
                    $files = $this->reArrayFiles($_FILES['Video']);

                    $path=Yii::getPathOfAlias('webroot').'/upload/';

                    foreach ($files as $file) {
                        if($file['tmp_name'] && is_uploaded_file($file['tmp_name'])){

                            $tmp_name = $file["tmp_name"];
                            $name = $file["name"];

                            $info = new SplFileInfo($name);

                            $name = uniqid() . '.' . $info->getExtension();

                            if(move_uploaded_file($tmp_name, $path.$name)){
                                $video = new Video;
                                $video->name = $name;
                                $video->product_id = $model->id;
                                $video->save();
                            }

                        }
                    }

                }

                if($_POST['EavValue']){
                    foreach ($_POST['EavValue'] as $key => $value) {

                        $criteria = new CDbCriteria;
                        $criteria->condition = "id_attrs = {$key} AND id_product = {$model->id}";

                        $attributesProduct = EavValue::model()->find($criteria);

                        if(count($attributesProduct)){

                            $attributesProduct->value = $value;
                            $attributesProduct->save();
                        }else{

                            $attributesProduct = new EavValue;
                            $attributesProduct->id_attrs = $key;
                            $attributesProduct->id_product = $model->id;
                            $attributesProduct->value = $value;
                            $attributesProduct->save();

                        }

                    }
                }

               // $this->redirect('/cp/shop/index');
            }
        }

        $this->render('productupdate', compact('model'));
    }
    public function actionThumbsUpdate($id)
    {
        $model = $this->loadProduct($id);

        if (isset($_POST['Product'])) {
            $model->attributes = $_POST['Product'];

            if ($model->save()) {
                $this->refresh();
            }
        }

        $this->render('thumbsupdate', compact('model'));
    }

    public function actionProductDelete($id)
    {
        $model = $this->loadProduct($id);
        $model->delete();

        $this->redirect(array('shop/index'));
    }


    private function _saveCategories(array $categories, $parentModel=null)
    {
        foreach ($categories as $code => $data) {
            $model = Category::model()->findByAttributes(array('code'=>$code));
            
            if(!$model) {
                $model = new Category();
                $model->title = $data['category']['title'];
                $model->code = $data['category']['code'];
                
                if($parentModel!=null)
                    $model->appendTo($parentModel);

                $model->saveNode();
            }

            if (isset($data['subcategory'])) 
                $this->_saveCategories($data['subcategory'], $model);
        }
    }

    public function actionCategoryCreate($parent_id = null)
    {
        $model = new Category();

        if (isset($_POST['Category'])) {
            $model->attributes = $_POST['Category'];

            if ($parent_id) {
                $parent = Category::model()->findByPk($parent_id);
                $model->appendTo($parent);
                $this->redirect(array('shop/category', 'id'=>$parent_id));
            } else {
                $model->saveNode();
                $this->redirect(array('index'));
            }
        }

        $this->render('categorycreate', compact('model'));
    }




    public function actionCategoryUpdate($id)
    {
        $model = $this->loadCategory($id);

        if (isset($_POST['Category'])) {
            $model->attributes = $_POST['Category'];
            
            if ($model->saveNode()) {
                $this->refresh();
            }
        }

        $this->render('categoryupdate', compact('model'));
    }
    public function actionCategoryDelete($id)
    {
        $model = $this->loadCategory($id);
        $model->deleteNode();

        $this->redirect(array('shop/index'));
    }


    private function loadProduct($id)
    {
        $model = Product::model()->findByPk((int) $id);
        if ($model === null)
            throw new CHttpException(404, 'Продукт не найден');
        return $model;
    }
    private function loadCategory($id)
    {
        $model = Category::model()->findByPk((int) $id);
        if ($model === null)
            throw new CHttpException(404, 'Категория не найдена');
        return $model;
    }


    /** ---  */
    public function actionRemoveMainImg()
    {
        $status = 0;

        if (isset($_POST['product_id'])) {
            Product::model()->removeMainImage($_POST['product_id']);
            $status = 1;
        }
        echo $status;
        Yii::app()->end();
    }

    public function actionClearImageCache()
    {
        Product::model()->clearImageCache();

        if (Yii::app()->request->isAjaxRequest) {
            echo 'ok';
            Yii::app()->end();
        }
        $this->redirect(array('shop/index'));
    }

    public function actionCategoryOrder()
    {
        $orders = Yii::app()->request->getParam('shop-category');

        $categories = Category::model()->findAllByPk($orders);

        foreach($categories as $c) {
            $c->ordering = array_search($c->id, $orders) + 1;
            $c->saveNode();
        }

        echo 'ok';
        Yii::app()->end();
    }

    /**
     * @param null $parent
     * @return mixed
     */
    private function getCategories($parent = null)
    {
        if ($parent) {
            $category = Category::model()->findByPk($parent);
            return $category->children()->findAll();
        }

        return Category::model()->roots()->findAll(array('order'=>'ordering'));
    }

    private function getBreadcrumbs($id)
    {
        $category = Category::model()->findByPk($id);
        $parents = $category->ancestors()->findAll();

        $result = array();
        foreach($parents as $p) {
            $result[] = CHtml::link($p->title, array('shop/category', 'id'=>$p->id));
        }
        return $result;
    }

    public function actionResize() {
        Yii::import('ext.EJCropper');
        $jcropper = new EJCropper();
        $jcropper->thumbPath = Yii::getPathOfAlias('webroot.images.product');
         
        $jcropper->jpeg_quality = 95;
        $jcropper->png_compression = 8;
         
        // get the image cropping coordinates (or implement your own method)
        $coords = $jcropper->getCoordsFromPost();
         
        // returns the path of the cropped image, source must be an absolute path.
        $src = mb_strpos($_POST['src'], '?') ? Yii::getPathOfAlias('webroot').mb_strcut($_POST['src'], 0, mb_strpos($_POST['src'], '?')) : Yii::getPathOfAlias('webroot').$_POST['src'];
        $dst = mb_strpos($_POST['dst'], '?') ? Yii::getPathOfAlias('webroot').mb_strcut($_POST['dst'], 0, mb_strpos($_POST['dst'], '?')) : Yii::getPathOfAlias('webroot').$_POST['dst'];
        $thumbnail = $jcropper->crop($src, $dst, $coords);
    }

}
