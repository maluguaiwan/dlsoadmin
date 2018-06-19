<?php
namespace common\utils;

class Calendar
{

    private static $now_time = 0;

    public static function getNowTime()
    {
        if (self::$now_time == 0) {
            self::$now_time = time();
        }
        return self::$now_time;
    }

    /**
     * 今天
     */
    public static function getTodayStartAndEnd()
    {
        $start_time = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $end_time = strtotime('+1 day', $start_time) - 1;
        return array(
            $start_time,
            $end_time
        );
    }
    /**
     * 今天
     */
    public static function getToday(){
    	$start_time = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
    	return $start_time;
    }
	/**
	 * 昨天
	 */
    public static function getYestoday(){
    	$start_time = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
    	return $start_time-3600*24;
    }
    /**
     * 昨天的开始和结束
     */
    public static function getYestodayStartAndEnd(){
    	$start_time = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
    	$end_time-=1;
    	$start_time-=3600*24;
    	return [$start_time,$end_time];
    }
    /**
     * 本周
     */
    public static function getWeekStartAndEnd()
    {
        $week = date("w");
        $year = date('Y');
        $month = date('m');
        if ($week == 0) {
            $week = 7;
        }
        // 本周开始（星期一）
        $start_time = mktime(0, 0, 0, $month, date("d") - $week + 1, $year);
        // 本周开始（星期天）
        $end_time = $start_time + 7 * 24 * 3600 - 1;
        return array(
            $start_time,
            $end_time,
            date('Y',$start_time),
            date('W',$start_time)
        );
    }

    /**
     * 上周
     */
    public static function getLastWeekStartAndEnd()
    {
        list ($start_time, $end_time) = self::getWeekStartAndEnd();
        $start_time -= 7 * 24 * 3600;
        $end_time -= 7 * 24 * 3600;
        return array(
            $start_time,
            $end_time,
            date('Y',$start_time),
            date('W',$start_time)
        );
    }

    /**
     * 本月
     */
    public static function getMonthStartAndEnd()
    {
        $year = date('Y');
        $month = date('m');
        $start_time = mktime(0, 0, 0, $month, 1, $year);
        $end_time = strtotime('+1 month', $start_time) - 1;
        return array(
            $start_time,
            $end_time,
            date('Y',$start_time),
            $month
        );
    }

    /**
     * 上月
     */
    public static function getLastMonthStartAndEnd()
    {
        $year = date('Y');
        $month = date('m');
        $start_time = mktime(0, 0, 0, $month - 1, 1, $year);
        $end_time = strtotime('+1 month', $start_time) - 1;
        return array(
            $start_time,
            $end_time,
            date('Y',$start_time),
            date('m',$start_time),
        );
    }

//     /**
//      * 本年
//      */
//     public static function getYearStartAndEnd()
//     {
//         $year = date('Y');
//         $start_time = mktime(0, 0, 0, 1, 1, $year);
//         $end_time = strtotime('+1 year', $start_time) - 1;
//         return array(
//             $start_time,
//             $end_time
//         );
//     }

//     /**
//      * 3个月 自然月
//      */
//     public static function getThreeMonthStartAndEnd()
//     {
//         $year = date('Y');
//         $month = date('m');
//         $month = floor(($month - 1) / 3) * 3 + 1;
//         $start_time = mktime(0, 0, 0, $month, 1, $year);
//         $end_time = strtotime('+3 month', $start_time) - 1;
//         return array(
//             $start_time,
//             $end_time
//         );
//     }

//     /**
//      * 6个月
//      */
//     public static function getSixMonthStartAndEnd()
//     {
//         $year = date('Y');
//         $month = date('m');
//         $month = floor(($month - 1) / 6) * 6 + 1;
//         $start_time = mktime(0, 0, 0, $month, 1, $year);
//         $end_time = strtotime('+6 month', $start_time) - 1;
//         return array(
//             $start_time,
//             $end_time
//         );
//     }
}

?>