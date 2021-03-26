<?php

/**
 * This is the model class for table "product".
 *
 * The followings are the available columns in table 'product':
 * @property integer $id
 * @property integer $category_id
 * @property string $code
 * @property string $title
 * @property string $description
 * @property integer $price
 * @property boolean $notexist
 * @property boolean $new
 * @property integer $ordering
 * @property CUploadedFile $mainImg
 * @property CUploadedFile $moreImg
 * @property string $path Get path for images directory
 *
 * @property string|boolean ext
 */
class Product extends CActiveRecord
{
    public $property;


    public $meta_title;
    public $meta_key;
    public $meta_desc;

    protected $mainImg;
    protected $moreImg;

    protected $sizes = array(
        'full'=>array(
            'suffix'=>'',
            'size'=>900,
            'masterSize'=>4
        ),
        'big'=>array(
            'suffix'=>'_b',
            'size'=>320,
            'masterSize'=>4
        ),
        'small'=>array(
            'suffix'=>'_s',
            'size'=>140,
            'crop'=>1
        ),
        'tmb'=>array(
            'suffix'=>'_tmb',
            'size'=>45,
            'crop'=>1
        )
    );

    protected $exts = array('jpg', 'png', 'gif');

    /**
     * Returns the static model of the specified AR class.
     * @return Product the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'product';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            
            array('alias', 'urlPath'),
            array('alias', 'unique', 'caseSensitive'=>false),
            array('alias', 'unique', 'caseSensitive'=>false, 'className'=>'Page', 'attributeName'=>'alias'),
            array('alias', 'unique', 'caseSensitive'=>false, 'className'=>'Category', 'attributeName'=>'alias'),
            array('alias', 'unique', 'caseSensitive'=>false, 'className'=>'Product', 'attributeName'=>'alias'),
            array('category_id, title, price', 'required'),
            array('category_id, ordering', 'numerical', 'integerOnly'=>true),
            array('title, alias ,link_title', 'length', 'max'=>255),
            array('alt_title', 'length', 'max'=>500),
            array('mainImg', 'file', 'allowEmpty'=>true, 'types'=>'jpg, gif, png'),
            array('notexist, sale, new', 'boolean'),
            array('description, moreImg, price, code', 'safe'),
            array('meta_title, meta_key, meta_desc', 'safe'),

        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'category'=>array(self::BELONGS_TO, 'Category', 'category_id'),
            'productAttributes'=>array(self::HAS_MANY, 'EavValue', 'id_product'),
            'video'=>array(self::HAS_MANY, 'Video', 'product_id'),
            'reviews'=>array(self::HAS_MANY, 'ProductReview', 'product_id'),
            'meta'=>array(self::BELONGS_TO, 'Metadata', array('id'=>'owner_id'),
                'together'=>true,
                'condition'=>'owner_name = :owner_name',
                'params'=>array(':owner_name'=>strtolower(get_class($this)))
            )
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'category_id' => 'Категория',
            'title' => 'Название',
            'code'=>'Артикул',
            'description' => 'Описание',
            'price' => 'Цена',
            'ordering'=> 'Порядок',
            'property'=>'Свойство',
            'mainImg' => 'Главное фото',
            'moreImg' => 'Дополнительные фото',
            'notexist'=>'Нет в наличии',
            'sale'=>'Спецпредложение',
            'new'=>'Новинка',
            'alt_title'=>'Альт. фото',
            'link_title'=>'Альт. название',
            'alias'=>'url',
            'meta_title'=>'Заголовок',
            'meta_key'=>'Ключевые слова',
            'meta_desc'=>'Описание',
        );
    }

    public function urlPath($attribute, $params = null) {
        $pattern = '/^[-\w\d]+$/ui';
        if(!preg_match($pattern, $this->$attribute) && !empty($this->$attribute)) {
            $this->addError($attribute, $this->getAttributeLabel($attribute).' может содержать только буквы, цифры и символы "-"');
        }
    }

    public function getCategories()
    {
        $cats_list = Category::model()->findAll(array('order'=>'root, lft'));;
        if (isset(Yii::app()->params['subcategories'])) {
            $cats_list = CmsCore::prepareTreeSelect($cats_list);
        }
        $categories = CHtml::listData($cats_list, 'id', 'title');
        return $categories;
    }

    public function search()
    {
        $criteria=new CDbCriteria;
        $criteria->with=array('productAttributes');
        $price_from = Yii::app()->getRequest()->getQuery('price_from');
        $price_to = Yii::app()->getRequest()->getQuery('price_to');
        $ftitle = Yii::app()->getRequest()->getQuery('f_title');
        $cat_id = Yii::app()->getRequest()->getQuery('id');
        $data_json = Yii::app()->getRequest()->getQuery('data');
        if(isset($data_json)){
            $attr_filter = json_decode($data_json);
            if(count($attr_filter)>0){
                $counter = 0;
                foreach ($attr_filter as $key => $attr) {
                    if($attr->value=="none") continue;
                    $counter++;
                    $criteria->addCondition('productAttributes.value = "'.$attr->value.'" and productAttributes.id_attrs = "'.$attr->name.'"', 'OR');
                }
                if($counter!=0){
                    $criteria->group = 'id_product';
                    $criteria->having = 'count(id_product)='.$counter;  
                }

            }
        }
        //Фильтрация цены
        if(isset($price_from) && isset($price_to)){
            #$criteria->addCondition('price >= '.$price_from.' AND price <= '.$price_to, 'AND');
            $criteria->addBetweenCondition('price', $price_from, $price_to );
        }
        elseif(isset($price_from)){
            $criteria->addCondition('price >= '.$price_to, 'AND');
           # $criteria->params = array('price_from'=>$price_from); 
        }
        elseif(isset($price_from)){
            $criteria->addCondition('price >= '.$price_to, 'AND');
        }
        $criteria->addSearchCondition('title', $ftitle, true, 'AND');

        if(isset($ftitle)){
            $criteria->addSearchCondition('title', $ftitle, true, 'AND');  
        }
        $criteria->compare('category_id',$cat_id);
        $criteria->compare('id',$this->id);
        $criteria->together = true;

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize' => 15,
                'pageVar'=>'p'
            ),
            'sort'=>array(
                'sortVar'=>'s', 
                'descTag'=>'d',
                'defaultOrder'=>'ordering ASC',
            ),
        ));
    }

    protected function beforeValidate()
    {
        $this->mainImg = CUploadedFile::getInstance($this, 'mainImg');
        $this->moreImg = CUploadedFile::getInstances($this, 'moreImg');

        return true;
    }

    protected function afterSave()
    {
        if ($this->mainImg instanceof CUploadedFile) {
            $this->createMainImages();
        }

        if (count($this->moreImg)) {
            $this->createMoreImages();
        }
        
        if (!$this->meta) {
        	$this->meta = new Metadata();
        	$this->meta->owner_name = get_class($this);
        	$this->meta->owner_id   = $this->id;
        }
        
        $this->meta->attributes = $this->getAttributes(array('meta_title', 'meta_key', 'meta_desc'));
        $this->meta->save();
        
        return true;
    }

    protected function afterDelete()
    {
        if(Yii::app()->params['attributes']){
            foreach($this->productAttributes as $model)
                $model->delete();
        }
        
        
        $this->removeMainImage();
        return true;
    }

    protected function afterFind()
    {
        $image = $this->id .'_s.' .$this->ext;

        

        if ($this->meta)
            $this->attributes = $this->meta->attributes;

        if (is_file(Yii::getPathOfAlias('webroot.images.product') .DS. $image)) {
            $this->mainImg = '/images/product/'. $image;
        } else {
            $this->mainImg = '/images/shop/product_no_image.png';
        }

        return true;
    }

    public function removeMainImage($id = null)
    {
        $id       = $id ? $id : $this->id;
        if (!$this->id) {
            $this->id = $id;
        }

        $path     = $this->path;
        $suffixes = array('', '_b', '_s', '_tmb');
        $ext = $this->ext;

        foreach($suffixes as $s) {
            $file = $path. DS . $id . $s .'.'. $ext;

            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    public function clearImageCache()
    {
        $suffixes = array('_b', '_s', '_tmb');
        $path     = $this->path;
        $files    = scandir($path);

        foreach($files as $file) {
            foreach($suffixes as $s) {
                if (strpos($file, $s) !== false)
                    unlink($path .DS. $file);
            }
        }
    }

    public function getMoreImages()
    {
        if ($this->moreImg == null) {
            $this->moreImg = CImage::model()->findAll('model=? AND item_id=?', array(
                strtolower(get_class($this)),
                $this->id
            ));
        }

        return $this->moreImg;
    }

    public function getMainImg($admin = false)
    {
        return $this->checkSize('small', $admin);
    }

    public function getBigMainImg($admin = false)
    {
        return $this->checkSize('big', $admin);
    }

    public function getTmbImg($admin = false)
    {
        return $this->checkSize('tmb', $admin);
    }

    public function getFullImg($bool = false, $withTime = true)
    {
        $image = $this->id .'.' .$this->ext;
        if (!$withTime){
            if (is_file($this->path .DS. $image)){
            return $this->id .'.' .$this->ext;
            }
            else{
                return false;
            }
        }
        if (is_file($this->path .DS. $image))
            return $bool ? true : '/images/product/' .$image .'?'.filemtime($this->path .DS. $image);
        else
            return $bool ? false : '/images/shop/product_no_image_b.png';
    }

    private function createMainImages()
    {
        $path     = $this->path;
        $ext      = strtolower($this->mainImg->extensionName);
        $name     = $this->id. '.' .$ext;

        $this->mainImg->saveAs($path .DS. $name);

        $this->checkSize('full', true, true, true);
        $this->checkSize('big', true, true);
        $this->checkSize('small', true, true);
        $this->checkSize('tmb', true, true);
    }

    private function createMoreImages()
    {
        $params = array('max'=>100, 'master_side'=>4);

        if ($cropTop = Yii::app()->settings->get('shop_settings', 'cropTop')) {
            $params['crop'] = true;
            $params['cropt_top'] = $cropTop;
        }

        $upload = new UploadHelper;
        $upload->add($this->moreImg, $this);
        $upload->runUpload($params);
    }

    protected function getPath()
    {
        return Yii::getPathOfAlias('webroot.images.product');
    }

    protected function getExt($name = null)
    {
        if (!$name) {
            $name = $this->id;
        }

        foreach($this->exts as $ext) {
            if (is_file($this->path .DS. $name .'.'. $ext)) {
                return $ext;
            }
        }

        return false;
    }

    /**
     * Return images link
     * @param string $sizeName Full name of size type
     * @param bool $admin
     * @param bool $createOnly
     * @param bool $force
     * @return string|bool
     * @throws CException
     */

    public function scopes()
    {
        return array(
            'lastRecord'=>array(
                'order'=>'id DESC',
                'limit'=>1,
            ),
        );
    }

    private function checkSize($sizeName, $admin, $createOnly = false, $force = false)
    {
        if (!isset($this->sizes[$sizeName])) {
            throw new CException('Size type not found');
        }

        $path    = $this->path;
        $params  = $this->sizes[$sizeName];
        $ext     = $this->ext;

        $fullImg = $this->id .'.'. $ext;
        $image   = $this->id . $params['suffix'] .'.'. $ext;

        if (!is_file($path .DS. $image) && is_file($path .DS. $fullImg) || $force) {
            $img = Yii::app()->image->load($path .DS. $fullImg);

            if (isset($params['masterSize'])) {
                $masterSize = $params['masterSize'];
            } else {
                $masterSize = $img->width > $img->height ? Image::HEIGHT : Image::WIDTH;
            }

            if ($img->width > $params['size']) {
                $img->resize($params['size'], $params['size'], $masterSize);

                $cropTop = Yii::app()->settings->get('shop_settings', 'cropTop');

                if (isset($params['crop']) && $cropTop) {
                    $img->crop($params['size'], $params['size'], $cropTop);
                }
            }

            $img->save($path .DS. $image);
        }

        if ($createOnly)
            return;

        if (is_file($path .DS. $image)) {
            return '/images/product/'. $image . '?'. filemtime($path .DS. $image);
        }

        return $admin ? false : '/images/shop/product_no_image'. $params['suffix'] .'.png';
    }

    private function urlToPath($url) {
        $path = $_SERVER['DOCUMENT_ROOT'].mb_strcut($url, 0, mb_strpos($url, '?'));
        if(file_exists($path))
            return $path;
        else
            return false;
    }

    public function getWidth($image) {
        if(is_file($this->urlToPath($image))) {
            $imageObject = Yii::app()->image->load($this->urlToPath($image));
            return $imageObject->width;
        }
    }

    public function getHeight($image) {
        if(is_file($this->urlToPath($image))) {
            $imageObject = Yii::app()->image->load($this->urlToPath($image));
            return $imageObject->height;
        }
    }

    public function createUrl()
    {
        Yii::import('ext.Transliteration.Transliteration');

        $url = strtolower(Transliteration::text($this->title));
        $url = preg_replace(array('/[^a-z0-9_-]+/ui', '/-{2,}/ui'), '-', $url);
        $url = preg_replace(array('/^[^\w\d]+/ui', '/[^\w\d]+$/'), '', $url);

        return trim($url);
    }

}
