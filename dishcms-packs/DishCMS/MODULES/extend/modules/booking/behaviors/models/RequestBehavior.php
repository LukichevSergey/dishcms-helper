<?php
namespace extend\modules\booking\behaviors\models;

use extend\modules\booking\components\helpers\HBooking;

class RequestBehavior extends \CBehavior
{
    /**
     * 
     * @see \CActiveRecord::rules()
     */
    public function rules()
    {
        return [
            ['name, phone, count, price, date', 'required'],
            ['count', 'numerical', 'integerOnly'=>true],
            ['price', 'numerical'],
            ['name, phone, comment', 'length', 'max'=>255],
            ['date', 'safe'],
            ['reject', 'boolean']
        ];
    }
    
    /**
     * Получить форматированную дату бронирования
     * @return string
     */
    public function getFormattedDate()
    {
        if(preg_match('/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/', $this->owner->date, $m)) {
            return $m[3] . ' ' . HBooking::getShortMonthLabel($m[2]) . ", {$m[1]} в {$m[4]}:{$m[5]}";
        }
        else {
            return $this->owner->date;
        }
    }
}
