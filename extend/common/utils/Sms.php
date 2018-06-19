<?php
namespace common\utils;

class Sms
{

    const REGISTER_SN_CODE = 'register_sn_code';

    static function getErr($code)
    {
        $err[1000] = '手机号不合法';
        $err[1001] = '未开启短信设置';
        $err[1002] = '余额不足';
        $err[1003] = '未配置模板消息';
        $err[112300] = '接收短信的手机号码为空';
        $err[112301] = '短信正文为空';
        $err[112302] = '群发短信已暂停';
        $err[112303] = '应用未开通短信功能';
        $err[112304] = '短信内容的编码转换有误';
        $err[112305] = '应用未上线，短信接收号码外呼受限';
        $err[112306] = '接收模板短信的手机号码为空';
        $err[112307] = '模板短信模板ID为空';
        $err[112308] = '模板短信模板data参数为空';
        $err[112309] = '模板短信模板data参数为空';
        $err[112310] = '应用未上线，模板短信接收号码外呼受限';
        $err[112311] = '短信模板不存在';
        return isset($err[$code]) ? $err[$code] : '未知';
    }

    static function isMobile($to)
    {
        return true;
    }
}
?>