<?php
namespace extend\modules\booking\components\helpers;

use common\components\helpers\HArray as A;
use common\components\helpers\HDb;
use crud\models\ar\extend\modules\booking\models\Schedule;
use crud\models\ar\extend\modules\booking\models\Request;

class HBooking
{
    /**
     * Кэш для активных расписаний
     * @var Schedule[]
     */
    private static $schedules=null;
    
    
    /**
     * Получить объект кэша
     * @return \CCache
     */
    public static function cache()
    {
        if(!\Yii::app()->getComponent('booking_cache')) {
            \Yii::app()->setComponent('booking_cache', [
                'class'=>'system.caching.CFileCache',
                'keyPrefix'=>'booking',
                'cachePath'=>\Yii::getPathOfAlias('application.runtime.booking_cache')                
            ]);
        }
        
        return \Yii::app()->booking_cache;
    }
    
    /**
     * Получить активные расписания
     * @return Schedule[]
     */
    public static function getSchedules()
    {
        if(static::$schedules === null) {
            static::$schedules=A::toa(Schedule::model()->published()->findAll());
        }
        
        return static::$schedules;
    }
    
    /**
     * Получить наименования месяцев
     * @return []
     */
    public static function monthLabels()
    {
        return [
            1=>'Январь',
            2=>'Февраль',
            3=>'Март',
            4=>'Апрель',
            5=>'Май',
            6=>'Июнь',
            7=>'Июль',
            8=>'Август',
            9=>'Сентябрь',
            10=>'Октябрь',
            11=>'Ноябрь',
            12=>'Декабрь'
        ];
    }
    
    /**
     * Получить сокращенные наименования месяцев
     * @return []
     */
    public static function monthShortLabels()
    {
        return [
            1=>'Янв',
            2=>'Фев',
            3=>'Март',
            4=>'Апр',
            5=>'Май',
            6=>'Июнь',
            7=>'Июль',
            8=>'Авг',
            9=>'Сент',
            10=>'Окт',
            11=>'Нояб',
            12=>'Дек'
        ];
    }
    
    /**
     * Получить наименование месяца
     * @param int $month номер месяца, начиная с 1 (единицы).
     * @param string $defaultLabel наименование месяца по умолчанию
     * @param bool $toLower преобразовать в нижний регистр
     * @return string
     */
    public static function getMonthLabel($month, $defaultLabel='Каждый месяц', $toLower=true)
    {
        $label=A::get(static::monthLabels(), (int)$month, $defaultLabel);
        
        return $toLower ? mb_strtolower($label) : $label;
    }
    
    /**
     * Получить сокращенное наименование месяца
     * @param int $month номер месяца, начиная с 1 (единицы).
     * @param string $defaultLabel наименование месяца по умолчанию
     * @param bool $toLower преобразовать в нижний регистр
     * @return string
     */
    public static function getShortMonthLabel($month, $defaultLabel='Каждый месяц', $toLower=true)
    {
        $label=A::get(static::monthShortLabels(), (int)$month, $defaultLabel);
        
        return $toLower ? mb_strtolower($label) : $label;
    }
    
    /**
     * Получить наименования дней недели
     * @return []
     */
    public static function weekLabels()
    {
        return [
            1=>'Понедельник',
            2=>'Вторник',
            3=>'Среда',
            4=>'Четверг',
            5=>'Пятница',
            6=>'Суббота',
            7=>'Воскресенье',
        ];
    }
    
    /**
     * Отображение форматированого времени
     * @param string|int $hour час
     * @param string|int $minutes минуты
     * @return string
     */
    public static function formatTime($hour, $minutes)
    {
        return (((int)$hour < 10) ? '0' : '') . (int)$hour . ':' . (((int)$minutes < 10) ? '0' : '') . (int)$minutes;
    }
    
    /**
     * Получить текущий день недели.
     * @param \DateTimeImmutable|null $date объект даты, либо null, если
     * нужно получить день недели текущего дня.
     * @return int день недели. Возвращает 7 для воскресенья. 
     */
    public static function getWeek($date=null)
    {
        if(!$date) {
            $date=new \DateTimeImmutable();
        }
        
        return (int)$date->format('w') ?: 7;
    }
    
    /**
     * Получить день недели для первого дня.
     * @param \DateTimeImmutable|null $date объект даты, либо null, если
     * нужно получить день недели первого дня текущего месяца.
     * @return int день недели. Возвращает 7 для воскресенья. 
     */
    public static function getFirstDayWeek($date=null)
    {
        if(!$date) {
            $date=new \DateTimeImmutable();
        }
        
        $date=$date->setDate($date->format('Y'), $date->format('m'), 1);
        
        return static::getWeek($date); 
    }
    
    /**
     * Получить день недели для последнего дня.
     * @param \DateTimeImmutable|null $date объект даты, либо null, если
     * нужно получить день недели первого дня текущего месяца.
     * @return int день недели. Возвращает 7 для воскресенья. 
     */
    public static function getLastDayWeek($date=null)
    {
        if(!$date) {
            $date=new \DateTimeImmutable();
        }
        
        $date=$date->setDate($date->format('Y'), $date->format('m'), static::getDaysCount($date));
        
        return static::getWeek($date); 
    }
    
    /**
     * Получить количество дней в месяце.
     * @param \DateTimeImmutable|null $date объект даты, либо null, если
     * нужно получить количество дней дня текущего месяца.
     * @return int
     */
    public static function getDaysCount($date=null)
    {
        if(!$date) {
            $date=new \DateTimeImmutable();
        }
        
        return (int)$date->format('t');
    }
    
    /**
     * Получить наименование дня недели
     * @param int $week номер месяца, начиная с 1 (единицы).
     * @param string $defaultLabel наименование дня недели по умолчанию
     * @param bool $toLower преобразовать в нижний регистр
     * @return string
     */
    public static function getWeekLabel($week, $defaultLabel='Каждый день', $toLower=true)
    {
        $label=A::get(static::weekLabels(), (int)$week, $defaultLabel);
        
        return $toLower ? mb_strtolower($label) : $label;
    }
    
    /**
     * Получить список годов от начала текущего года
     * @param int $limit кол-во годов. По умолчанию 10.
     * @return []
     */
    public static function getYearRange($limit=10)
    {
        $y=(int)date('Y');
        
        $range=range($y, $y + $limit);
        
        return array_combine($range, $range);
    }
    
    /**
     * Получить список дней
     * @param int $days кол-во дней. По умолчанию 31.
     * @return []
     */
    public static function getDaysRange($days=31)
    {
        $range=range(1, $days);
        
        return array_combine($range, $range);
    }
    
    /**
     * Получить список часов
     * @return []
     */
    public static function getHoursRange()
    {
        $range=range(1, 24);
        
        return array_combine($range, $range);
    }
    
    /**
     * Получить список минут
     * @param int $step шаг. По умолчанию 5.
     * @param int $start начальная минута. По умолчанию 0 (нуль).
     * @param int $max максимальное количество минут. По умолчанию 59.
     * @return array
     */
    public static function getMinutesRange($step=5, $start=0, $max=59)
    {
        $range=range($start, $max, $step);
        
        return array_combine($range, $range);
    }
    
    /**
     * Получить доступные расписания для дня.
     * @param \DateTimeImmutable $date дата, для которой получается расписание
     * @return []
     */
    public static function getDaySchedules($date)
    {
        $timestamp=$date->format('d.m.Y');
        
        $cacheId='booking_schedule_by_day_' . $timestamp;        
        if($schedule=static::cache()->get($cacheId)) {
            return $schedule;
        }
        
        $year=(int)$date->format('Y');
        $month=(int)$date->format('n');
        $day=(int)$date->format('j');
        $week=static::getWeek($date);
        
        // формируем список кандидатов для определения активных расписаний дня
        $candidates=[];
        foreach(static::getSchedules() as $schedule) {
            $candidate=(object)$schedule->getAttributes();
            $candidate->weight=0;
            
            $continue=false;
            foreach(['year', 'month', 'day', 'week'] as $attribute) {
                if($schedule->$attribute && ($schedule->$attribute != $$attribute)) {
                    $continue=true;
                    break;
                }
                elseif($schedule->$attribute && ($schedule->$attribute == $$attribute)) {
                    $candidate->weight++;
                }
            }
            
            if($continue) {
                continue;
            }
            
            $candidates[]=$candidate;
        }
        
        // в приоритете более точное расписание
        usort($candidates, function($a, $b) { return ($a->weight < $b->weight) ? 1 : -1; });
        
        static::cache()->set($cacheId, $candidates);
        
        return $candidates;
    }
    
    /**
     * Получить активное расписание 
     * @param \DateTimeImmutable|null $date дата, для которой получается расписание.
     * По умолчанию текущая дата.
     * @return []
     */
    public static function getActiveSchedules($date)
    {
        if(!$date) {
            $date=new \DateTimeImmutable();
        }
        
        // @todo если дата меньше текущей, то все расписания считаются неактивными
        if(false) {
            return [];
        }
        
        $year=(int)$date->format('Y');
        $month=(int)$date->format('n');
        $day=(int)$date->format('j');
        $week=static::getWeek($date);
        
        $daySchedules=static::getDaySchedules($date);
        
        // @var [] $activeDateSchedules активные расписания с приоритетом по соотвествию параметров даты
        $activeDateSchedules=[];
        foreach($daySchedules as $sch) {
            // Приоритет расписаний по дате (от наиболее высокого):
            // - с заданным годом, месяцем, числом и днем недели
            if($sch->year && $sch->month && $sch->day && $sch->week) {
                if(((int)$sch->year == $year) && ((int)$sch->month == $month) && ((int)$sch->day == $day) && ((int)$sch->week == $week)) {
                    $sch->weight=100;
                    $activeDateSchedules[]=$sch;
                    continue;
                }
            }
            
            // - с заданным годом, месяцем, числом
            if($sch->year && $sch->month && $sch->day) {
                if(((int)$sch->year == $year) && ((int)$sch->month == $month) && ((int)$sch->day == $day)) {
                    $sch->weight=80;
                    $activeDateSchedules[]=$sch;
                    continue;
                }
            }
            
            // - с заданным годом, месяцем, днем недели
            if($sch->year && $sch->month && $sch->week) {
                if(((int)$sch->year == $year) && ((int)$sch->month == $month) && ((int)$sch->week == $week)) {
                    $sch->weight=60;
                    $activeDateSchedules[]=$sch;
                    continue;
                }
            }
            
            // - с заданным годом, и числом и днем недели
            if($sch->year && $sch->day && $sch->week) {
                if(((int)$sch->year == $year) && ((int)$sch->day == $day) && ((int)$sch->week == $week)) {
                    $sch->weight=50;
                    $activeDateSchedules[]=$sch;
                    continue;
                }
            }
            
            // - с заданным месяцем, и числом и днем недели
            if($sch->month && $sch->day && $sch->week) {
                if(((int)$sch->month == $month) && ((int)$sch->day == $day) && ((int)$sch->week == $week)) {
                    $sch->weight=40;
                    $activeDateSchedules[]=$sch;
                    continue;
                }
            }
            
            // - с заданным годом и числом
            if($sch->year && $sch->day) {
                if(((int)$sch->year == $year) && ((int)$sch->day == $day)) {
                    $sch->weight=30;
                    $activeDateSchedules[]=$sch;
                    continue;
                }
            }
            
            // - с заданным месяцем и числом
            if($sch->month && $sch->day) {
                if(((int)$sch->month == $month) && ((int)$sch->day == $day)) {
                    $sch->weight=25;
                    $activeDateSchedules[]=$sch;
                    continue;
                }
            }
            
            // - с заданным годом и днем недели
            if($sch->year && $sch->week) {
                if(((int)$sch->year == $year) && ((int)$sch->week == $week)) {
                    $sch->weight=20;
                    $activeDateSchedules[]=$sch;
                    continue;
                }
            }
            
            // - с заданным месяцем и днем недели
            if($sch->month && $sch->week) {
                if(((int)$sch->month == $month) && ((int)$sch->week == $week)) {
                    $sch->weight=15;
                    $activeSchedules[]=$sch;
                    continue;
                }
            }
            
            // - с заданным числом и днем недели
            if($sch->day && $sch->week) {
                if(((int)$sch->day == $day) && ((int)$sch->week == $week)) {
                    $sch->weight=10;
                    $activeDateSchedules[]=$sch;
                    continue;
                }
            }
            
            // - с заданным числом
            if($sch->day) {
                if((int)$sch->day == $day) {
                    $sch->weight=5;
                    $activeDateSchedules[]=$sch;
                    continue;
                }
            }
            
            // - с заданным днем недели
            if($sch->week) {
                if((int)$sch->week == $week) {
                    $sch->weight=1;
                    $activeDateSchedules[]=$sch;
                    continue;
                }
            }
            
            // - для любой даты
            if(!$sch->year && !$sch->month && !$sch->day && !$sch->week) {
                $sch->weight=0;
                $activeDateSchedules[]=$sch;
                continue;
            }
        }
        
        // @todo если дата больше текущей, то все расписания для даты считаются 
        // предварительно активными, 
        if(true) {
            $activeTimeSchedules=$activeDateSchedules;
        }
        else {
            // иначе ищем активные расписания
            if(!empty($activeDateSchedules)) {
                $hour=(int)$date->format('G') ?: 24;
                $minute=(int)$date->format('i');
                
                // @var [] $activeTimeSchedules активные расписания с приоритетом по соотвествию параметров времени
                $activeTimeSchedules=[];
                foreach($activeDateSchedules as $sch) {
                    
                    // Приоритет расписаний по времени (от наиболее высокого):
                    // - заданным часом начала сеанса и не заданными часом окончания сеанса (фиксированный час)
                    if($sch->hour && !$sch->hour_to) {
                        if($sch->hour == $hour) {
                            if((int)$sch->minute === $minute) {
                                $sch->weight=100;
                                $activeTimeSchedules[]=$sch;
                            }
                        }
                        continue;
                    }
                    
                    // если задан час окончания сеанса, но не задан час начала сеанса
                    // устанавливаем значение часа, как 24
                    if(!$sch->hour && $sch->hour_to) {
                        $sch->hour=24;
                    }
                    
                    // - заданным часом начала сеанса, заданным часом окончания сеанса,
                    // с заданной продолжительностью сеанса и заданной продолжительностью
                    // перерыва между сеансами
                    if($sch->hour && $sch->hour_to && $sch->session_duration && $sch->break_duration) {
                    }        
                    
                    // - заданным часом начала сеанса, заданным часом окончания сеанса
                    // заданной продолжительностью перерыва между сеансами
                    if($sch->hour && $sch->hour_to && $sch->break_duration) {
                    }
                    
                    // - заданным часом начала сеанса, заданным часом окончания сеанса
                    // заданной продолжительностью сеанса
                    if($sch->hour && $sch->hour_to && $sch->break_duration) {
                    }           
                    
                    
                    // - расписания с заданной продолжительностью сеанса
                    // и заданной продолжительностью перерыва между сеансами
                    if($sch->session_duration && $sch->break_duration) {
                    } 
                    
                    // - расписания с заданной продолжительностью сеанса
                    if($sch->session_duration) {
                    }
                    
                    // - расписания с заданной продолжительностью перерыва между сеансами
                    if($sch->break_duration) {
                    }
                }
                
            }
        }
        
        if(!empty($activeTimeSchedules)) {
            // в приоритете более точное расписание
            usort($activeTimeSchedules, function($a, $b) { return ($a->weight < $b->weight) ? 1 : -1; });
        }
        
        return $activeTimeSchedules;
    }
    
    /**
     * Получить активное расписание
     * @param \DateTimeImmutable|null $date дата, для которой получается расписание.
     * По умолчанию текущая дата.
     * @return []
     */
    public static function getActiveSchedule($date)
    {
        $activeTimeSchedules=static::getActiveSchedules($date);
        
        if(!empty($activeTimeSchedules)) {
            // активное расписание это первое подходящее с самым высоким приоритетом
            return array_shift($activeTimeSchedules);
        }
        
        return null;
    }
    
    /**
     * Получить расписание сеансов для даты
     * @param \DateTimeImmutable|null $date дата, для которой получается расписание сеансов.
     * По умолчанию текущая дата.
     * @return [][] временные интервалы вида [
     *  'from'=>['h'=>час начала (начиная с 0), 'i'=>'минуты начала'], 
     *  'to'=>['h'=>час окончания (начиная с 0), 'i'=>'минуты окончания'],
     *  'count'=>количество билетов на сеанс
     *  'free_count'=>количество свободных билетов на сеанс
     *  'price'=>стоимость одного билета на сеанс
     * ]
     */
    public static function getScheduleData($date=null)
    {
        $times=[];
        
        if(!$date) {
            $date=new \DateTimeImmutable();
        }
        
        $cacheId='booking_schedule_data_' . $date->format('d.m.Y');
        if($times=static::cache()->get($cacheId)) {
            foreach($times as $hash=>$time) {
                $times[$hash]['free_count']=static::getFreeTicketCount(
                    $date->setTime($time['from']['h'], $time['from']['i']),
                    $time['count']
                );
            }
        }
        else {
            if($activeSchedules=static::getActiveSchedules($date)) {
                foreach($activeSchedules as $activeSchedule) {
                    $startMinuteAt=(int)$activeSchedule->minute;
                    $breakDuration=(int)$activeSchedule->break_duration;
                    $sessionDuration=(int)(int)$activeSchedule->session_duration;
                    if(!$sessionDuration) {
                        $sessionDuration=60 - $breakDuration;
                    }
                    $sessionFullDuration=$sessionDuration + $breakDuration;
                    
                    $hourAtIsEmpty=!(int)$activeSchedule->hour;
                    $hourToIsEmpty=!(int)$activeSchedule->hour_to;
                    $hourAt=(int)$activeSchedule->hour ?: 24;
                    $hourTo=(int)$activeSchedule->hour_to ?: 24;
                    $minuteAt=(int)$activeSchedule->minute;
                    
                    if($hourTo < $hourAt) {
                        // обратный интервал времени действия расписания
                        $sessionHours=(24 - $hourTo) + $hourAt;
                        
                        $date=$date->setTime($hourTo, $minuteAt, 0);
                    }
                    elseif($hourAtIsEmpty && ($hourTo == $hourAt) && ($hourTo == 24)) {
                        // круглосуточно
                        $sessionHours=24;
                        
                        $date=$date->setTime(0, $minuteAt, 0);
                    }
                    elseif(!$hourAtIsEmpty && $hourToIsEmpty && $hourTo) {
                        // фиксированный час
                        $sessionHours=1; 
                        
                        $date=$date->setTime($hourTo, $minuteAt, 0);
                    }
                    else {
                        // прямой интервал времени действия расписания
                        $sessionHours=$hourTo - $hourAt;
                        
                        $date=$date->setTime($hourAt, $minuteAt, 0);
                    }
                    
                    // @var int количество сеансов в течении дня
                    $sessionCount=floor(($sessionHours * 60) / ($sessionDuration + $breakDuration));
                    for($i=0; $i<$sessionCount; $i++) {
                        $hourFrom=(int)$date->format('G');
                        $minuteFrom=(int)$date->format('i');
                        
                        $freeTicketCount=static::getBookingTicketCount($date, $activeSchedule->session_ticket_count);                        
                        
                        $date=$date->modify("+{$sessionFullDuration} minute");
                        
                        $hourTo=(int)$date->format('G');
                        $minuteTo=(int)$date->format('i');
                        
                        $timeBusy=false;
                        foreach($times as $hash=>$time) {
                            if(($hourFrom >= $time['from']['h']) && ($hourFrom < $time['to']['h'])) {
                                if(($minuteFrom >= $time['from']['i']) || !(($hourTo == $time['from']['h']) && ($minuteTo < $time['from']['i']))) {
                                    $timeBusy=true;
                                    break;
                                }
                            }
                        }
                        
                        if(!$timeBusy) {
                            $hash="{$hourFrom}:{$minuteFrom}@{$hourTo}:{$minuteTo}";
                            if(empty($times[$hash])) {
                                $times[$hash]=[
                                    'schedule_hash'=>$activeSchedule->hash,
                                    'from'=>['h'=>$hourFrom, 'i'=>$minuteFrom],
                                    'to'=>['h'=>$hourTo, 'i'=>$minuteTo],
                                    'price'=>(int)$activeSchedule->session_ticket_price,
                                    'count'=>(int)$activeSchedule->session_ticket_count,
                                    'free_count'=>$freeTicketCount
                                ];
                            }
                        }
                    }
                }
                
                uasort($times, function($a, $b) {
                    if($a['from']['h'] < $b['from']['h']) return -1;
					if(($a['from']['h'] == $b['from']['h']) && ($a['from']['i'] < $b['from']['i'])) return -1;
                    return 1;
                });
            }
        }
        
        static::cache()->set($cacheId, $times);
        
        return $times;
    }
    
    /**
     * Получить количество свободных билетов
     * @param \DateTimeImmutable $date дата и время бронирования
     * @param int $totalCount общее количество билетов 
     */
    public static function getFreeTicketCount($date, $totalCount=0)
    {
        $bookingCount=static::getBookingTicketCount($date);
        
        $freeCount=(int)$totalCount - $bookingCount;
        
        if($freeCount < 0) {
            $freeCount=0;
        }
        
        return $freeCount;
    }
    
    /**
     * Получить количество забронированных билетов на дату и время
     * @param \DateTimeImmutable $date дата и время бронирования
     * @return int количество забронированных билетов
     */
    public static function getBookingTicketCount($date)
    {
        return (int)HDb::queryScalar(
            'SELECT SUM(`count`) FROM ' . HDb::qt(Request::model()->tableName()) . ' WHERE `date`=:date AND `reject`!=1',
            ['date'=>$date->format('y-m-d H:i:s')]
        );
    }
}
