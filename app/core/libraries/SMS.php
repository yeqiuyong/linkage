<?php
/**
 * Created by PhpStorm.
 * User: uni
 * Date: 16/2/26
 * Time: 下午10:32
 */

namespace Multiple\Core\Libraries;


class SMS{


    /**
     * Send SMS
     * @param $mobile
     * @param $msg
     * @throws Exception
     */
    public function sendSms($mobile,$msg){

        if(!$mobile){

            throw new Exception('手机号码不能为空',1001);
        }

        if(!$msg){

            throw new Exception('信息不能为空',1002);
        }


        if(!$this->redis){
            log_message('error','no redis.............');
        }
        else{
            //通过往redis里面插入数据，短信后台程序循环读取数据发送
            $this->redis->lPush('sms', json_encode(array('phone' => $mobile, 'msg' => $msg)));
        }

    }

}