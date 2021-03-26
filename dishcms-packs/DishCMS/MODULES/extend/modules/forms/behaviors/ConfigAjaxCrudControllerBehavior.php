<?php
namespace extend\modules\forms\behaviors;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use common\components\helpers\HFile;
use common\components\helpers\HAjax;
use crud\components\helpers\HCrud;
use extend\modules\forms\components\helpers\HForm;
use crud\models\ar\extend\modules\forms\models\Config;

class ConfigAjaxCrudControllerBehavior extends \CBehavior
{
    /**
     * Шаблон типа по умолчанию
     * @var string
     */
    public $typeFieldDefault='default';
    
    /**
     * Базовый путь к шаблонам типов
     * @var string
     */
    public $typeFieldBasePath='extend.modules.forms.views.crud.forms.types';
    
    /**
     * Ипортирование конфигурации формы
     * @param string $cid идентификатор конфигурации CRUD форм 
     */
    public function actionImport($cid)
    {
        $ajax=HAjax::start();
        
        $file=\CUploadedFile::getInstanceByName('file');
        if($file instanceof \CUploadedFile) {
            if($model=$this->import($cid, $file->getTempName())) {
                $ajax->success=(bool)$model->id;
                
                if(!$ajax->success) {
                    $ajax->addErrors($model->getError());
                }
            }
        }
        
        $ajax->end();
    }
    
    /**
     * Импортирование предустановленной конфигурации формы
     * @param string $cid идентификатор конфигурации CRUD форм 
     */
    public function actionImportPreset($cid)
    {
        $ajax=HAjax::start();
        
        if($presets=R::post('presets')) {
            foreach($presets as $preset) {
                $filename=HFile::path([\Yii::getPathOfAlias(Config::PRESET_DIR), $preset.'.json']);
                if($model=$this->import($cid, $filename)) {
                    $ajax->success=(bool)$model->id;
                    
                    if(!$ajax->success) {
                        $ajax->addErrors($model->getError());
                    }
                }
            }
        }
        
        $ajax->end();
    }
    
    /**
     * Получить форму типа поля
     * @param string $cid идентификатор конфигурации CRUD форм
     */
    public function actionGetTypeField($cid)
    {
        $params=R::post('params', []);
        $item=A::get($params, 'item', []);
        
        if($typeId=A::rget($item, 'type.id', Config::DEFAULT_FIELD_TYPE_ID)) {
            if($type=HForm::type($typeId)) {
                $viewFile=HFile::path([\Yii::getPathOfAlias($this->typeFieldBasePath), $typeId . '.php']);
                $view=is_file($viewFile) ? $typeId : $this->typeFieldDefault;
            }
            
            $this->owner->renderPartial(
                $this->typeFieldBasePath . '.' . $view, 
                ['type'=>$type, 'params'=>A::toa(R::post('params', []))]
            );
        }
        
        Y::end();
    }
    
    /**
     * Импортирование формы из файла
     * @param string $cid идентификатор CRUD настройки
     * @param string $filename полное имя файла
     * @return \CActiveRecord|null
     */
    protected function import($cid, $filename)
    {
        $model=null;
        
        if($className=HCrud::param($cid, 'class')) {
            if(is_file($filename)) {
                if($attributes=@json_decode(file_get_contents($filename), true)) {
                    $model=new $className;
                    foreach($attributes as $attribute=>$value) {
                        if($jvalue=@json_decode($value, true)) {
                            $model->$attribute=$jvalue;
                        }
                        else {
                            $model->$attribute=$value;
                        }
                    }
                    $model->id=null;
                    $model->create_time=new \CDbExpression('NOW()');
                    $model->save();
                }
            }
        }
        
        return $model;
    }
}