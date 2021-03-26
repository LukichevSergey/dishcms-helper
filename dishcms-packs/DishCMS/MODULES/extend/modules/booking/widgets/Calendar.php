<?php
namespace extend\modules\booking\widgets;

use extend\modules\booking\components\helpers\HBooking;

class Calendar extends \common\components\base\Widget
{
    public $view='calendar';
    
    /**
     * Получить данные для календаря
     * @param int $monthLimit максимальное количество месяцев, 
     * отображаемых для календаря
     * @return array
     */
    public function getCalendarData($monthLimit=3)
    {
        $data=[];
        
        $date=new \DateTimeImmutable();
        for($i=0; $i<$monthLimit; $i++) {
            $data[]=$this->getMonthData($date);
            
			$nextMonth=(int)$date->format('n') + 1;
            $nextYear=(int)$date->format('Y');
            if($nextMonth > 12) {
                $nextMonth=1;
                $nextYear++;
            }
            $date=new \DateTimeImmutable("{$nextYear}-{$nextMonth}-01");
        }
        
        return $data;
    }
    
    /**
     * Получить данные календаря для месяца
     * @param \DateTimeImmutable $date дата месяца
     */
    protected function getMonthData($date)
    {
        $prevMonth=(int)$date->format('n') - 1;
        if($prevMonth < 1) $prevMonth=12;
        $prev=new \DateTimeImmutable($date->format('Y') . "-{$prevMonth}-01");
        
        $nextMonth=(int)$date->format('n') + 1;
        if($nextMonth > 12) $nextMonth=1;
        $next=new \DateTimeImmutable($date->format('Y') . "-{$nextMonth}-01");
        
        $data=[
            // месяцы
            'months'=>[
                // наименование предыдущего месяца
                'prev'=>HBooking::getMonthLabel((int)$prev->format('n'), ''),
                // наименование текущего месяца
                'current'=>HBooking::getMonthLabel((int)$date->format('n'), ''),
                // наименование следующего месяца
                'next'=>HBooking::getMonthLabel((int)$next->format('n'), ''),
            ],
            
            // день недели, с которого начинается месяц
            'first_day_week'=>HBooking::getFirstDayWeek($date),
            
            // день недели, в который заканчивается месяц
            'last_day_week'=>HBooking::getLastDayWeek($date),
            
            // количество дней в месяце
            'days_count'=>HBooking::getDaysCount($date),
            
            // дни месяца
            'days'=>[]
        ];
        
        foreach(range(1, $data['days_count']) as $day) {
            $date=$date->setDate($date->format('Y'), $date->format('m'), $day);
            
            if($date->format('Y-m-d') < date('Y-m-d')) {
                $data['days'][$day]=[
                    'date'=>$date->format('d.m.Y'),
                    'ticket_count'=>0,
                    'free_ticket_count'=>0,
                    'minimal_ticket_price'=>0,
                    'times'=>[]
                ];
            }
            else {
                $times=HBooking::getScheduleData($date);
                
                $ticketCount=0;
                $freeTicketCount=0;
                $minTicketPrice=0;
                foreach($times as $time) {
                    $ticketCount+=$time['count'];
                    $freeTicketCount+=$time['free_count'];
                    if($time['price'] && (!$minTicketPrice || ($time['price'] < $minTicketPrice))) {
                        $minTicketPrice=$time['price'];
                    }
                }
                
                $data['days'][$day]=[
                    'date'=>$date->format('d.m.Y'),
                    'ticket_count'=>$ticketCount,
                    'free_ticket_count'=>$freeTicketCount,
                    'minimal_ticket_price'=>$minTicketPrice,
                    'times'=>$times
                ];
            }
        }
        
        return $data;
    }
}
