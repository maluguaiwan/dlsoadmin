<?php
namespace common\utils;

class FormTools
{
    /**
     * 向$_POST $_GET $_REQUEST 中插入默认数据 
     * @param string $k
     * @param string $v
     */
    public static function setInputDefVal($k, $v)
    {
        $_POST[$k] = $_GET[$k] = $_REQUEST[$k] = $v;
    }

    /**
     *
     * @param string $start_date            
     * @param string $end_date            
     * @param string $end_date_time            
     * @author lpdx111
     *         开发日期 2014-12-1 上午10:15:16
     *        
     */
    public static function stringDateToInt($start_date = 'start_date', $end_date = 'end_date', $end_date_time = '23:59:59')
    {
        $date = $_POST[$start_date];
        if (is_string($date)) {
            $_POST[$start_date] = $_REQUEST[$start_date] = $_GET[$start_date] = strtotime($date);
        }
        
        $date = $_POST[$end_date];
        if (is_string($date)) {
            $_POST[$end_date] = $_REQUEST[$end_date] = $_GET[$end_date] = strtotime($date . ' ' . $end_date_time);
        }
    }

    public static function checkBoxToString($name)
    {
        if (is_string($name)) {
            $value = $_REQUEST[$name];
            if (is_array($value)) {
                $_POST[$name] = $_REQUEST[$name] = $_GET[$name] = implode(',', $value);
            }
        } else 
            if (is_array($name)) {
                foreach ($name as $n) {
                    $value = $_REQUEST[$n];
                    if (is_array($value)) {
                        $_POST[$n] = $_REQUEST[$n] = $_GET[$n] = implode(',', $value);
                    }
                }
            }
    }
}

?>