<?php
namespace extend\modules\booking\behaviors\models;

use common\components\helpers\HYii as Y;
use common\components\helpers\HTools;
use extend\modules\booking\components\helpers\HBooking;

class ScheduleBehavior extends \CBehavior
{
    /**
     * 
     * {@inheritDoc}
     * @see \CBehavior::events()
     */
    public function events()
    {
        return [
            'onBeforeSave'=>'beforeSave',
            'onAfterSave'=>'afterSave',
        ];
    }
    
    /**
     * 
     * @see \CActiveRecord::rules()
     */
    public function rules()
    {
        return [
            ['year, month, day, week, hour, hour_to, minute', 'numerical', 'integerOnly'=>true],
            ['session_duration, session_ticket_count, break_duration', 'numerical', 'integerOnly'=>true],
            ['session_ticket_price', 'numerical'],
        ];
    }
    
    /**
     * Scope: по значению хэша расписания
     * @param string $hash хэш расписания
     */
    public function byHash($hash)
    {
        $this->owner->getDbCriteria()->mergeWith(['condition'=>'hash=:hash', 'params'=>['hash'=>$hash]]);
        
        return $this->owner;
    }
    
    /**
     * Генерировать хэш расписания
     * @return string
     */
    public function generateHash()
    {
        return md5(
            $this->owner->year
            . '@'
            .  $this->owner->month
            . '@'
            .  $this->owner->week
            . '@'
            .  $this->owner->day
            . '@'
            .  $this->owner->hour
            . '@'
            .  $this->owner->hour_to
            . '@'
            .  $this->owner->minute
        );
    }
    
    /**
     * Получить наименование месяца
     * @param int $month номер месяца, начиная с 1 (единицы). 
     * @param string $defaultLabel наименование месяца по умолчанию
     * @param bool $toLower преобразовать в нижний регистр
     * @return string
     */
    public function getMonthLabel($month=null, $defaultLabel='Каждый месяц', $toLower=true)
    {
        if($month===null) {
            $month=$this->owner->month;
        }
        
        return HBooking::getMonthLabel($month, $defaultLabel, $toLower);
    }
    
    /**
     * Получить наименование дня недели
     * @param int $week номер дня недели, начиная с 1 (единицы).
     * @param string $defaultLabel наименование дня недели по умолчанию
     * @param bool $toLower преобразовать в нижний регистр
     * @return string
     */
    public function getWeekLabel($week=null, $defaultLabel='Любой', $toLower=true)
    {
        if($week===null) {
            $week=$this->owner->week;
        }
        
        return HBooking::getWeekLabel($week, $defaultLabel, $toLower);
    }
    
    /**
     * Event: onBeforeSave
     */
    public function beforeSave()
    {
        $this->owner->hash=$this->generateHash();
    }
    
    /**
     * Event: onAfterSave
     */
    public function afterSave()
    {
        HBooking::cache()->flush();
    }
    
    /**
     * Получить текстовое время начала
     * @return [] возвращается массив вида array(0=>время начала, 1=>время окончания)
     */
    public function getStartTime()
    {
        $labels=[];
        
        $m=(int)$this->owner->minute;
        if($m < 10) {
            $m="0{$m}";
        }
        
        if($this->owner->hour) {
            $time=HBooking::formatTime($this->owner->hour, $m);
        }
        else {
            $time="каждый час в {$m} мин";
        }
        $labels[0]=$time;
        
        $labels[1]=null;
        if((int)$this->owner->hour_to) {
            $labels[1]=HBooking::formatTime($this->owner->hour_to, $m);;
        }
        
        return $labels;
    }
    
    /**
     * Получить время последнего обновления расписания
     * @return string
     */
    public function getLastUpdateTime()
    {
        $updateTime='';
        
        if(!HTools::isDateEmpty($this->owner->update_time)) {
            $updateTime=$this->owner->update_time;
        }
        elseif(!HTools::isDateEmpty($this->owner->create_time)) {
            $updateTime=$this->owner->create_time;
        }
        
        return $updateTime;
    }
}