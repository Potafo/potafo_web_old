<?php
//Common functions


use Helpers\DataSource;
use Helpers\commonsource;


//Check Decimal

//      function timeadd($time)
//    {
//        $datetimeadd = $time->format('h:i A',strtotime($time . "+45 minutes"));
//        $datetimeadd = date('h:i A', strtotime($time . "+45 minutes"));
//        return $datetimeadd;
//    }

    function timediff($time)
    {
        $timezone = 'ASIA/KOLKATA';
        $totime = new DateTime('now', new DateTimeZone($timezone));
        $to_time = $totime->format('Y-m-d H:i:s');
        $fromtime = new DateTime(date('Y-m-d H:i:s' ,strtotime($time)), new DateTimeZone($timezone));
        $from_time = $fromtime->format('Y-m-d H:i:s');
        $t =  $fromtime->diff($totime);
        $hr = ($t->format('%a')*1440)+($t->format('%h')*60) +($t->format('%i'));
         if($hr >= 40)
         {
                return 'Y';
         }
        else
        {
                return 'N';
        }
    }
    function time_difference($time1,$time2)
    {
        $from_time = strtotime($time1);
         $to_time = strtotime($time2);
        
        //echo round(abs($to_time - $from_time) / 60,2). " minute";
        $time_diff=round(abs($to_time - $from_time) / 60,2);
         return "00:".$time_diff.":00";
    }
    
    function confirmationdiff($time)
    {
        $rest_conf = Commonsource::restconfirmationalert();
        $timezone = 'ASIA/KOLKATA';
        $totime = new DateTime('now', new DateTimeZone($timezone));
        $to_time = $totime->format('Y-m-d H:i:s');
        $fromtime = new DateTime(date('Y-m-d H:i:s' ,strtotime($time)), new DateTimeZone($timezone));
        $from_time = $fromtime->format('Y-m-d H:i:s');
        $t =  $fromtime->diff($totime);
        $sec = ((($t->format('%a')*24)+$t->format('%H'))*60 +$t->format('%i'))*60+($t->format('%s'));
         if($sec > $rest_conf)
         {
                return 'Y';
         }
        else
        {
                return 'N';
        }
    }
	
	function validate_mobile($mobile)
	{
		return preg_match('/^[0-9]{10}+$/', $mobile);
	}


?>