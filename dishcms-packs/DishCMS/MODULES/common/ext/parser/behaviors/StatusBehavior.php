<?php
namespace common\ext\parser\behaviors;

use common\components\helpers\HArray as A;
use common\ext\parser\components\helpers\HStatus;

class StatusBehavior extends \CBehavior
{
    /**
     * Имя атрибута статуса
     * @var string
     */
    public $attribute='status';
    
    /**
     *
     * {@inheritDoc}
     * @see \CBehavior::events()
     */
    public function events()
    {
        return [
            'onBeforeSave'=>'beforeSave'
        ];
    }    
    
    /**
     * Scope: по статусу
     * @param array|integer $status статус или список статусов
     */
    public function byStatus($status, $tableAlias='t')
    {
        $criteria=new \CDbCriteria();
        
        $attribute="`{$tableAlias}`.`{$this->attribute}`";
        
        if(is_array($status)) {
            $criteria->addInCondition($attribute, $status);
        }
        else {
            $criteria->addColumnCondition([$attribute=>$status]);
        }
        
        $this->owner->getDbCriteria()->mergeWith($criteria);
        
        return $this->owner;
    }
    
    /**
     * Scope: кроме статуса (исключение)
     * @param array|integer $status статус или список статусов
     */
    public function byNotStatus($status, $tableAlias='t')
    {
        $criteria=new \CDbCriteria();
        
        $attribute="`{$tableAlias}`.`{$this->attribute}`";
        
        $criteria->addNotInCondition($attribute, A::toa($status));
        
        $this->owner->getDbCriteria()->mergeWith($criteria);
        
        return $this->owner;
    }
    
    /**
     * Event: onBeforeSave
     * @return boolean
     */
    public function beforeSave()
    {
        if($this->owner->isNewRecord) {
            $this->owner->{$this->attribute}=HStatus::NEWEST;
        }
        
        return true;
    }
    
    /**
     * Новый
     * @return boolean
     */
    public function isNew()
    {
        return !$this->owner->{$this->attribute} || ($this->owner->status == HStatus::NEWEST);
    }
    
    /**
     * Запущен
     * @return boolean
     */
    public function isRunned()
    {
        return in_array((int)$this->owner->{$this->attribute}, [
            HStatus::RUN, 
            HStatus::PROCESS_RUN_GETLINKS, 
            HStatus::PROCESS_RUN_GETCONTENTS
        ]);
    }
    
    /**
     * Завершен
     * @return boolean
     */
    public function isDone()
    {
        return ($this->owner->{$this->attribute} == HStatus::DONE);
    }
    
    /**
     * Ошибка
     * @return boolean
     */
    public function isError()
    {
        return ($this->owner->{$this->attribute} == HStatus::ERROR);
    }
    
    /**
     * Получить статус
     * @return integer 
     */
    public function getStatus()
    {
        return (int)$this->owner->{$this->attribute};
    }    
    
    /**
     * Установить статус
     * @param integer $status статус
     */
    public function setStatus($status)
    {
        $this->owner->{$this->attribute}=$status;
    }
    
    /**
     * Изменить статус с сохранением
     * @param integer $status новый статус
     */
    public function changeStatus($status)
    {
        if($this->owner->{$this->attribute} != $status) {
            $this->owner->{$this->attribute}=$status;
            $this->owner->update([$this->attribute]);
        }
    }
}