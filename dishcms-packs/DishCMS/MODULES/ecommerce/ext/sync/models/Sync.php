<?php
namespace ecommerce\ext\sync\models;

use common\components\helpers\HArray as A;
use common\components\helpers\HHash;
use common\components\helpers\HDb;
use settings\components\helpers\HSettings;

class Sync extends \CFormModel
{
    /**
     *
     * @var string
     */
    const SESSION_VAR='ecommerce_ext_sync_actions_Sync';
    
    /**
     * 
     * @var array
     */
    private $importAttributes=[];
    
    /**
     *
     * @var string
     */
    private $token;
    
    /**
     *
     * @var array
     */
    private $syncErrors=[];
    
    /**
     *
     * @var array
     */
    private $warnings=[];
    
    /**
     * 
     * @return array|mixed
     */
    public function getSyncErrors()
    {
        return $this->syncErrors;
    }
    
    /**
     * 
     * @param array $attributes
     */
    public function setImportAttributes($attributes=[])
    {
        $this->importAttributes=$attributes;
    }
    
    public function execute($mode, $params=[])
    {
        $result=false;
        
        switch($mode) {
            case 'init':
                $this->initToken(false, A::get($params, 'token'));
                $data=$this->send('init');
                if($count=(int)A::rget($data, 'data.count')) {
                    $this->setState('count', $count);
                    $result=[
                        'token'=>$this->token,
                        'count'=>$count
                    ];
                }
                elseif($errors=A::get($data, 'errors')) {
                    $this->syncErrors=$errors;
                }
                break;
                
            case 'get':
                if($this->initToken(true, A::get($params, 'token'))) {
                    $count=$this->getState('count');
                    $limit=(int)$this->settings('limit') ?: 50;
                    $steps=ceil($count/$limit);
                    $step=$this->getState('step', 0);
                    if($step < $steps) {
                        if($this->import($step, $limit)) { 
                            $current=($step + 1) * $limit;
                            $current=($current > $count) ? $count : $current;
                            $result=[
                                'current'=>$current,
                                'percent'=>(($current == $count) ? 100 : ceil((100/$count)*$current)),
                            ];
                            
                            if($current == $count) {
                                $this->rebuildPrimaryKey();
                            }
                            
                            if(count($this->warnings) > 0) {
                                $result['warnings']=$this->warnings;
                            }
                        }
                    }
                }
                break;
        }
        
        return $result;
    }
    
    /**
     *
     * @param boolean $required
     * @return boolean
     */
    protected function initToken($required=false, $token=null)
    {
        if($required && !$token) {
            return false;
        }
        
        if(!$token) {
            $token=md5(HHash::random());
            $this->flushState();
        }
        
        $this->token=$token;
        
        return true;
    }
    
    protected function flushState()
    {
        \Yii::app()->user->setState(self::SESSION_VAR, []);
    }
    
    /**
     *
     * @param string $name
     * @param mixed $default
     * @return mixed|NULL
     */
    protected function getState($name=null, $default=null)
    {
        if($this->token) {
            $state=\Yii::app()->user->getState(self::SESSION_VAR);
            
            if($name === null) {
                return $state;
            }
            
            return A::rget($state, "{$this->token}.{$name}", $default);
        }
        
        return null;
    }
    
    /**
     *
     * @param string $name
     * @param mixed $value
     */
    protected function setState($name, $value)
    {
        if($this->token) {
            $state=A::toa($this->getState());
            
            $state[$this->token][$name]=$value;
            
            \Yii::app()->user->setState(self::SESSION_VAR, $state);
        }
    }
    
    /**
     *
     * @param string $name
     * @return mixed
     */
    protected function settings($name)
    {
        $settings=HSettings::getById('shop');
        
        $name='sync_' . $name;
        
        return $settings->$name;
    }
    
    /**
     *
     * @param string $mode
     * @param array $data
     * @return mixed|boolean
     */
    protected function send($mode, $data=[])
    {
        if($this->settings('url')) {
            $data['mode']=$mode;
            $data['token']=$this->settings('token');
            
            $ch=curl_init($this->settings('url'));
            
            curl_setopt_array($ch, [
                CURLOPT_POST=>true,
                CURLOPT_POSTFIELDS=>$data,
                CURLOPT_RETURNTRANSFER=>true
            ]);
            
            $result=curl_exec($ch);
            
            curl_close($ch);
            
            return json_decode($result, true);
        }
        return false;
    }
    
    /**
     * Получить удаленный файл
     * @param string $url
     * @return mixed|boolean
     */
    protected function getfile($url)
    {
        $ch=curl_init($url);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $content=curl_exec($ch);
        
        curl_close($ch);
        
        return $content;
    }
    
    protected function uploadImage($url, $filename)
    {
        if($data=$this->getfile($url)) {
            $tmpname=pathinfo($filename, PATHINFO_DIRNAME) . '/' . pathinfo($filename, PATHINFO_FILENAME);
            file_put_contents($tmpname, $data);
            if(is_file($tmpname)) {
                $filename=null;
                switch (exif_imagetype($tmpname)) {
                    case IMAGETYPE_GIF:
                        $filename=$tmpname . '.gif';
                        break;
                        
                    case IMAGETYPE_JPEG:
                        $filename=$tmpname . '.jpg';
                        break;
                        
                    case IMAGETYPE_PNG:
                        $filename=$tmpname . '.png';
                        break;
                }
                
                if($filename) {
                    rename($tmpname, $filename);
                    return $filename;
                }
                else {
                    unlink($tmpname);
                }
            }
        }
        return null;
    }
    
    protected function uploadFile($url, $filename)
    {
        if($data=$this->getfile($url)) {
            file_put_contents($filename, $data);
            return $filename;
        }
        return null;
    }
    
    /**
     *
     * @param integer $step
     * @param integer $limit
     * @return boolean
     */
    protected function import($step, $limit)
    {
        $data=$this->send('get', compact('limit', 'step'));
        
        if(A::get($data, 'success') === true) {
            if($categories=$this->initCategories()) {
                $eavAttrs=\EavAttribute::model()->findAll(['index'=>'name']);
                $eavAttrKeys=[];
                $products=A::rget($data, 'data.products', []);
                foreach($products as $product) {
                    if($code=A::get($product, 'code')) {
                        $isNewRecord=false;
                        $models=\Product::model()->findAllByAttributes(['code'=>$product['code']]);
                        if(empty($models)) {
                            $model=new \Product;
                            $model->code=$code;
                            $model->category_id=A::get($categories, A::get($product, 'category_id'));
                            if(!$model->category_id) {
                                $this->warnings[]="Не найдена категория для товара с артикулом \"{$code}\"";
                                continue;
                            }
                            $isNewRecord=true;
                        }
                        elseif(count($models) > 1) {
                            $this->warnings[]='Товар с артикулом "'. $code .'" не является уникальным (всего: ' . count($models) . ', #ID: ' . A::get($product, 'id') . ')';
                            continue;
                        }
                        else {
                            $model=array_shift($models);
                        }
                        
                        // основные атрибуты
                        foreach($this->importAttributes as $attribute) {
                            $model->$attribute=A::get($product, $attribute, $model->$attribute);
                        }
                        
                        
                        if(!$model->save()) {
                            $this->warnings[]="Товар с артикулом \"{$code}\" не удалось обновить. Причина:\n" . json_encode(model->getErrors(), JSON_UNESCAPED_UNICODE);
                            continue;
                        }
                        
                        // основная картинка
                        if($isNewRecord || $this->settings('reload_files')) {
                            $model->main_image='';
                            $model->main_image_alt=A::get($product, 'main_image_alt', '');
                            $model->main_image_enable=A::get($product, 'main_image_enable', 1);
                            if($src=A::get($product, 'mainImageSrc')) {
                                if($filename=$this->uploadImage($src, $model->mainImageBehavior->getBasename(true))) {
                                    $model->main_image=basename($filename);
                                }
                            }
                        }
                        
                        // дополнительные картинки
                        if(!empty($product['moreImages']) && ($isNewRecord || $this->settings('reload_files'))) {
                            if(!$isNewRecord) {
                                \CImage::model()->deleteAll(['condition'=>'model=? AND item_id=?', 'params'=>['product', $model->id]]);
                            }
                            
                            $values=[];
                            $path=\Yii::getPathOfAlias('webroot.images.product') . '/';
                            foreach($product['moreImages'] as $src) {
                                if($filename=$this->uploadImage($src, $path . HHash::get(null,8))) {
                                    $values[]="('product', {$model->id}, '" . basename($filename) . "')";
                                }
                            }
                            if(!empty($values)) {
                                $query='INSERT INTO `image` (`model`, `item_id`, `filename`) VALUES ' . implode(',', $values);
                                HDb::execute($query);
                            }
                        }
                        
                        // дополнительные файлы
                        if(!empty($product['moreFiles']) && ($isNewRecord || $this->settings('reload_files'))) {
                            if(!$isNewRecord) {
                                \File::model()->deleteAll(['condition'=>'model=? AND item_id=?', 'params'=>['product', $model->id]]);
                            }
                            
                            $values=[];
                            $basepath=\Yii::getPathOfAlias('webroot.files.product') . '/';
                            foreach($product['moreFiles'] as $url) {
                                $path=$basepath . preg_replace('#^(.*)/([^/]+?)$#', '\2', $url);
                                if($filename=$this->uploadFile($url, $path)) {
                                    $values[]="('product', {$model->id}, '" . basename($filename) . "')";
                                }
                            }
                            if(!empty($values)) {
                                $query='INSERT INTO `file` (`model`, `item_id`, `filename`) VALUES ' . implode(',', $values);
                                HDb::execute($query);
                            }
                        }
                        
                        // eav атрибуты
                        if(!empty($product['productAttributes'])) {
                            $newEavValues=[];
                            $productAttributes=$model->getRelated('productAttributes');
                            foreach($product['productAttributes'] as $eavValues) {
                                if($eavAttrId=(int)A::get($eavValues, 'id_attrs')) {
                                    if(empty($eavAttrKeys[$eavAttrId])) {
                                        foreach($eavAttrs as $eavAttr) {
                                            if($eavAttr->name == $eavValues['attr']['name']) {
                                                $eavAttrKeys[$eavAttrId]=$eavAttr->id;
                                                break;
                                            }
                                        }
                                    }
                                    
                                    if(empty($eavAttrKeys[$eavAttrId])) {
                                        $eavAttrModel=new \EavAttribute();
                                        $eavAttrModel->filter=$eavValues['attr']['filter'];
                                        $eavAttrModel->fixed=$eavValues['attr']['fixed'];
                                        $eavAttrModel->name=$eavValues['attr']['name'];
                                        $eavAttrModel->type=$eavValues['attr']['type'];
                                        if($eavAttrModel->save()) {
                                            $eavAttrKeys[$eavAttrId]=$eavAttrModel->id;
                                            $eavAttrs[]=$eavAttrModel;
                                        }
                                        else {
                                            $this->warnings[]="Для товара с артикулом \"{$code}\" не удалось добавить свойство \"" 
                                                . $eavValues['attr']['name'] . "\". Причина:\n" . json_encode($model->getErrors(), JSON_UNESCAPED_UNICODE);
                                            
                                            continue;
                                        }
                                    }
                                    
                                    if(!empty($eavAttrKeys[$eavAttrId])) {
                                        $newEavValues[]="({$eavAttrKeys[$eavAttrId]}, {$model->id}, '" . \CHtml::encode($eavValues['value']) . "')";
                                    }
                                }
                            }
                            
                            if(!empty($newEavValues)) {
                                $query='DELETE FROM `eav_value` WHERE (`id_product`=:id)';
                                HDb::execute($query, ['id'=>$model->id]);
                                
                                $query='INSERT INTO `eav_value` (`id_attrs`, `id_product`, `value`) VALUES ' . implode(',', $newEavValues);
                                HDb::execute($query);
                            }
                        }
                        
                        // дополнительные категории
                        if(!empty($product['relatedCategories'])) {
                            $query='DELETE FROM `related_category` WHERE (`product_id`=:id)';
                            HDb::execute($query, ['id'=>$model->id]);
                            
                            $values=[];
                            foreach($product['relatedCategories'] as $relatedCategory) {
                                if($categoryId=A::get($categories, A::get($relatedCategory, 'category_id'))) {
                                    $values[]="({$categoryId}, {$model->id})";
                                }
                            }
                            
                            if(!empty($values)) {
                                $query='INSERT INTO `related_category` (`category_id`, `product_id`) VALUES ' . implode(',', $values);
                                HDb::execute($query);
                            }
                        }
                        
                        
                        if(!$model->save()) {
                            $this->warnings[]="Для товара с артикулом \"{$code}\" удалось обновить только основные данные. Причина:\n" . json_encode($model->getErrors(), JSON_UNESCAPED_UNICODE);
                        }
                    }
                }
                
                $this->setState('step', $step + 1);
                
                return true;
            }
        }
        return false;
    }
    
    protected function initCategories()
    {
        $categories=$this->getState('categories');
        
        if(empty($categories)) {
            $categories=[];
            
            $data=$this->send('cat');
            if(A::get($data, 'success') !== true) {
                return false;
            }
            
            $items=A::rget($data, 'data.categories', []);
            $cats=\Category::model()->findAll(['select'=>'id, title']);
            foreach($items as $category) {
                $id=A::get($category, 'id');
                $title=A::get($category, 'title');
                if($id && $title) {
                    foreach($cats as $cat) {
                        if($cat->title == $title) {
                            $categories[$id]=$cat->id;
                        }
                    }
                }
            }
            
            $this->setState('categories', $categories);
        }
        
        return $categories;
    }
    
    protected function rebuildPrimaryKey()
    {
        foreach(['image', 'file', 'related_category', 'eav_value'] as $tableName) {
            $query='SET @n:=0;UPDATE `'.$tableName.'` SET `id` = @n := @n + 1;ALTER TABLE `'.$tableName.'` AUTO_INCREMENT = 1;';
            HDb::execute($query);
        }
    }
}
