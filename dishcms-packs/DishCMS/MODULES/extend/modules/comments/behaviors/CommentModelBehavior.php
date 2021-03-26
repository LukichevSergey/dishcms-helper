<?php
/**
 * Поведение модели "Комментарий"
 * 
 */
namespace extend\modules\comments\behaviors;

use common\components\helpers\HArray as A;
use common\components\helpers\HDb;
use extend\modules\comments\components\helpers\HComment;

class CommentModelBehavior extends \CBehavior
{
    /**
     * {@inheritDoc}
     * @see \CBehavior::events()
     */
    public function events()
    {
        return [
            'onBeforeValidate'=>'beforeValidate',
            'onBeforeSave'=>'beforeSave'
        ];
    }
    
    /**
     * Правила валидации модели
     * @return array
     */
    public function rules()
    {
        return [
            ['name, comment, model, model_id, rating', 'required'],
            ['rating', 'numerical', 'integerOnly'=>true, 'min'=>0, 'max'=>5],
            ['model_id', 'numerical', 'integerOnly'=>true],
            ['model, name, comment, sort', 'safe'],
            ['model_hash', 'safe']
        ];
    }
    
    public function byModel($modelClass=null)
    {
        if($modelClass === null) {
            $modelClass=$this->owner->model;
        }
        
        $criteria=HDb::criteria();
        
        $modelClass=HComment::normalizeClass($modelClass);
        $criteria->addColumnCondition(['model'=>$modelClass]);
        
        $this->owner->getDbCriteria()->mergeWith($criteria);
        
        return $this->owner;
    }
    
    public function beforeValidate()
    {
        if($this->owner->model_hash) {
            if($config=HComment::getConfigByHash($this->owner->model_hash)) {
                $this->owner->model=$config['class'];
            }
        }
        return true;
    }
    
    public function beforeSave()
    {
        if(!$this->owner->sort && ($this->owner->getScenario() == 'insert')) {
            $query='SELECT MAX(`sort`) + 10 FROM ' . HDb::qt($this->owner->tableName()) . ' WHERE 1=1';
            if($this->owner->model) {
                $query.=' AND (`model`=' . HDb::qv(HComment::normalizeClass($this->owner->model)) . ')';
            }
            if($this->owner->model_id) {
                $query.=' AND (`model_id`=' . (int)$this->owner->model_id . ')';
            }
            
            $this->owner->sort=(int)HDb::queryScalar($query);
        }
        
        return true;
    }
    
    public function getModel($criteria=null)
    {
        if($this->owner->model_id) {
            if($cfg=HComment::getConfigModel($this->owner->model)) {
                $class=$cfg['class'];
                $select="{$cfg['attributeId']},{$cfg['attributeTitle']}";
                if($attributeParentId=A::get($this->getParentConfig(), 'attributeParentId')) {
                    $select.=",{$attributeParentId}";
                }
                
                return $class::model()->select($select)->findByPk($this->owner->model_id, $criteria);
            }   
        }
        
        return null;
    }
    
    public function getModelLabel()
    {
        return A::get(HComment::getConfigModel($this->owner->model), 'label');
    }
    
    public function getModelItemLabel()
    {
        return A::get(HComment::getConfigModel($this->owner->model), 'itemLabel');
    }
    
    public function getModelTitle($criteria=null)
    {
        if($model=$this->getModel($criteria)) {
            $attributeTitle=A::get(HComment::getConfigModel($this->owner->model), 'attributeTitle');
            
            return $model->$attributeTitle;
        }
        
        return null;
    }
    
    public function getParentHash()
    {
        return HComment::getParentHashByModel($this->owner->model);
    }
    
    public function getParentId()
    {
        if($cfg=$this->getParentConfig()) {
            if($parent=$this->getParent()) {
                return $parent->{$cfg['attributeId']};
            }
            return null;
        }
        
        return false;
    }
    
    public function getParent($parentCriteria=null, $modelCriteria=null)
    {
        if($cfg=$this->getParentConfig()) {
            if($model=$this->getModel($modelCriteria)) {
                $class=$cfg['class'];                    
                return $class::model()
                    ->select("{$cfg['attributeId']},{$cfg['attributeTitle']}")
                    ->findByPk($model->{$cfg['attributeParentId']}, $parentCriteria);
            }
            return null;
        }
        
        return false;
    }
    
    public function getParentLabel()
    {
        return A::get($this->getParentConfig(), 'label');
    }
    
    public function getParentItemLabel()
    {
        return A::get($this->getParentConfig(), 'itemLabel');
    }
    
    
    public function getParentTitle()
    {
        if($parent=$this->getParent()) {
            $attributeTitle=A::get($this->getParentConfig(), 'attributeTitle');
            return $parent->$attributeTitle;
        }
        
        return null;
    }
    
    public function getParentsListData($empty=null)
    {
        $data=[];
        
        $parents=HComment::getParents();        
        foreach($parents as $modelClass=>$cfg) {
            $data[HComment::getParentHashByModel($modelClass)]=$cfg['label'];
        }
        
        return $data;
    }
    
    public function getParentListData($criteria=null, $empty=null)
    {
        if($cfg=$this->getParentConfig()) {
            $class=$cfg['class'];
            return $class::model()->listData($cfg['attributeTitle'], $criteria, $empty, $cfg['attributeId']);
        }
        
        return [];
    }
    
    public function getModelListData($criteria=null, $empty=null)
    {
        
        if($cfg=HComment::getConfigModel($this->owner->model)) {
            $parentId=$this->getParentId();
            if($parentId !== false) {
                $criteria=HDb::criteria($criteria);
                if($parentId) {
                    $criteria->scopes=A::toa($criteria->scopes);
                    $criteria->scopes[]=['wcolumns'=>[[$cfg['parent']['attributeParentId']=>$parentId]]];
                }
                else {
                    $criteria->addCondition('1=1');
                }
            }
            
            $class=$cfg['class'];
            return $class::model()->listData($cfg['attributeTitle'], $criteria, $empty, $cfg['attributeId']);
        }
        
        return [];
    }
    
    protected function getParentConfig()
    {
        return A::get(HComment::getConfigModel($this->owner->model), 'parent');
    }
}