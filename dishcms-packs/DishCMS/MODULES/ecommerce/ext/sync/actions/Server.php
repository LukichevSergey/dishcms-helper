<?php
/**
 * Слушатель на стороне сервера
 * Добавить действие в контроллер:
 * 'sync'=>'\ecommerce\ext\sync\actions\Server'
 */
namespace ecommerce\ext\sync\actions;

use common\components\helpers\HArray as A;
use common\components\helpers\HAjax;
use settings\components\helpers\HSettings;

class Server extends \CAction
{
    public function run()
    {
        $ajax=HAjax::start();
        
        if(empty($_POST['token'])) {
            $ajax->addError('Не передан токен синхронизации');
        }
        else {
            $settings=HSettings::getById('shop');
            if($_POST['token'] !== $settings->sync_token) {
                $ajax->addError('Не верный токен синхронизации');
            }
            else {
                switch(A::get($_POST, 'mode')) {
                    case 'init':
                        $ajax->data=$this->actionInit();
                        $ajax->success=true;
                        break;
                        
                     case 'cat':
                        $ajax->data=$this->actionCat();
                        $ajax->success=true;
                        break;
                        
                    case 'get':
                        $ajax->data=$this->actionGet(A::get($_POST, 'step'), A::get($_POST, 'limit'));
                        $ajax->success=true;
                        break;
                        
                    default:
                        $ajax->addError('Некорректный запрос');
                }
            }
        }
        
        $ajax->end();
    }
    
    protected function actionInit()
    {
        return [
            'count'=>\Product::model()->count()
        ];
    }
    
    protected function actionCat()
    {
        $data=[];
        
        if($categories=\Category::model()->findAll()) {            
            foreach($categories as $category) {
                $data[$category->id]=$category->getAttributes();
            }
        }
        
        return ['categories'=>$data];
    }
    
    protected function actionGet($step=0, $limit=0)
    {
        $data=[];
        
        $criteria=['order'=>'`t`.`id`'];
        if((int)$limit > 0) {
            $criteria['limit']=$limit;
            if((int)$step > 0) {
                $criteria['offset'] = $step * $limit;
            }
        }
        
        $eavAttrs = \EavAttribute::model()->findAll(['index'=>'id']);
        if($products=\Product::model()->with(['relatedCategories', 'productAttributes'])->findAll($criteria)) {            
            foreach($products as $product) {
                $data[$product->id]=$product->getAttributes();
                
                $data[$product->id]['mainImageSrc']='';
                if($product->mainImageBehavior->exists()) {
                    $data[$product->id]['mainImageSrc']=$product->mainImageBehavior->getSrc(true);
                }
                
                $data[$product->id]['moreImages']=[];
                if($images = $product->getMoreImages()) {
                    foreach($images as $img) {
                        $data[$product->id]['moreImages'][]=$this->controller->createAbsoluteUrl($img->url);
                    }
                }
                
                $data[$product->id]['moreFiles']=[];
                $files = \File::model()->findAll([
                    'condition' => 'model=? AND item_id=?',
                    'params' => ['product', $product->id]
                ]);
                if($files) {
                    foreach($files as $file) {
                        $data[$product->id]['moreFiles'][]=$this->controller->createAbsoluteUrl('/files/product/' . $file->filename);
                    }
                }                
                
                $data[$product->id]['relatedCategories']=[];
                if($product->relatedCategories) {
                    foreach($product->relatedCategories as $relatedCategory) {
                        $data[$product->id]['relatedCategories'][]=$relatedCategory->getAttributes();
                    }
                }
                $data[$product->id]['productAttributes']=[];
                if($product->productAttributes) {
                    foreach($product->productAttributes as $paIdx=>$productAttribute) {
                        $data[$product->id]['productAttributes'][$paIdx]=$productAttribute->getAttributes();
                        if(!empty($eavAttrs[$productAttribute->id_attrs])) {
                            $data[$product->id]['productAttributes'][$paIdx]['attr']=$eavAttrs[$productAttribute->id_attrs];
                        }
                    }
                }
                
            }
        }
        
        return ['products'=>$data];
    }
}
