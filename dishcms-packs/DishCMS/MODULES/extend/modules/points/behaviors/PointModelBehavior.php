<?php
/**
 * Поведение модели "Точка продажи"
 *
 */
namespace extend\modules\points\behaviors;

use common\components\helpers\HDb;
use common\components\helpers\HTools;
use crud\models\ar\extend\points\models\Point;

class PointModelBehavior extends \CBehavior
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
     * Подписи атрибутов модели
     * @see \CActiveRecord::attributeLabels()
     * @return string[]
     */
    public function attributeLabels()
    {
        return [
            
        ];
    }
    
    /**
     * Правила валидации
     * @see \CActiveRecord::rules()
     * @return []
     */
    public function rules()
    {
        return [
            ['title, lon, lat', 'required'],
            ['sort, phone, address, contacts, worktime, parking, info', 'safe'],
            ['sort', 'numerical', 'integerOnly'=>true],
            ['title, phone, worktime, parking', 'length', 'max'=>255],
            ['lon, lat', 'numerical'],
            ['photohash', 'safe']
        ];
    }
    
    /**
     * Scope: Поиск
     * @param string $query
     * @param string $operatorPhrase оператор объединения поисковых фраз. 
     * По умолчанию "AND".  
     */
    public function search($query, $operatorPhrase='AND')
    {
        if(!empty($query) || is_numeric($query)) {
            $phrases=$this->searchGetPhrases($query);
            
            $criteria=new \CDbCriteria();
            $this->addSearchInCondition($criteria, 'title', $phrases, 'OR', $operatorPhrase);
            $this->addSearchInCondition($criteria, 'address', $phrases, 'OR', $operatorPhrase);
            
            $this->owner->getDbCriteria()->mergeWith($criteria);
        }
        
        return $this->owner;
    }
    
    /**
     * Получить поисковые фразы из строки поиска
     * @param string $query строка поиска
     * @return array
     */
    protected function searchGetPhrases($query)
    {
        $query=preg_replace('/[^a-z0-9абвгдеёжзийклмнопрстуфхцчшщъыьэюяАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ]+/i', ' ', $query);
        $query=preg_replace('/ +/', ' ', $query);
        
        return array_filter(explode(' ', $query), function($v) { return (strlen($v) > 2); });
    }
    
    /**
     * Добавить выражение поиска в критерий выборки  
     * @param &\CDbCriteria $criteria объект критерия выборки
     * @param string $attribute имя атрибута
     * @param array $phrases список поисковых фраз
     * @param string $operator внеший оператор объединения. По умолчанию "OR".
     * @param string $operatorPhrase оператор объединения фраз. По умолчанию "AND".  
     */
    protected function addSearchInCondition(&$criteria, $attribute, $phrases=[], $operator='OR', $operatorPhrase='AND') 
    {
        $c=new \CDbCriteria();
        
        if(!empty($phrases)) {
            foreach($phrases as $p) {
                $c->addSearchCondition($attribute, $p, true, $operatorPhrase);
            }
        }
        
        $criteria->mergeWith($c, $operator);
    }
    
    /**
     * Event: onBeforeValidate
     * @return boolean
     */
    public function beforeValidate()
    {
        $this->owner->phone=HTools::normalizePhone($this->owner->phone);
        
        return true;
    }
    
    /**
     * Event: onBeforeSave
     * @return boolean
     */
    public function beforeSave()
    {
        if($this->owner->isNewRecord) {
            if(!$this->owner->sort) {
                $query='SELECT MIN(`sort`) - 5 FROM ' . HDb::qt($this->owner->tableName()) . ' WHERE 1=1';
                $this->owner->sort=(int)HDb::queryScalar($query);
                if(!$this->owner->sort) {
                    $this->owner->sort=500;
                }
            }
            
            if(HTools::isDateEmpty($this->owner->create_time)) {
                $this->owner->create_time=new \CDbExpression('NOW()');
            }
        }

        $this->owner->phone=HTools::normalizePhone($this->owner->phone);
        
        return true;
    }
    
    /**
     * Получить точки на карте
     * @return array
     */
    public function getPoints()
    {
        $data=[];
        
        $model=new Point;
        if($points=$model->published()->findAll(['select'=>'lon, lat'])) {
            foreach($points as $point) {
                $data[]=[$point->lon, $point->lat];
            }
        }
        
        return $data;
    }
}
