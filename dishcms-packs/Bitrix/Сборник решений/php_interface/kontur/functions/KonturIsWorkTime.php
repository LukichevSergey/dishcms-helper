<?
if(!function_exists("KonturIsWorkTime"))
{
    function KonturIsWorkTime($workAt, $workTo, $serverNTime=0)
    {
        function GetUTime($sTime) {
            $times=explode(':', $sTime);
            if(count($times) == 2) {
                if($date=date("U", mktime((int)$times[0], (int)$times[1], 0))) {
                    return $date;
                }
            }
            return 0;
        }
        $iCurrentTime=time() + ($serverNTime*3600);
        $iWorktimeAt=(int)GetUTime($workAt);
        $iWorktimeTo=(int)GetUTime($workTo);
        return ($iWorktimeAt <= $iCurrentTime) && ($iCurrentTime <= $iWorktimeTo);
    }
}

