<?php
/**
 * Created by PhpStorm.
 * User: whoami
 * Date: 16-8-30
 * Time: 下午7:49
 */

class jpush
{


    private $_masterSecret;

    private $_appkeys;

    public function __construct(){
        $this->_masterSecret = '886e799b9063abb80fb6a4db';
        $this->_appkeys = 'ab6203c0c8263729040aade7';
    }

    public function request_post($url = '', $param = '') {
        if (empty($url) || empty($param)) {
            return false;
        }

        $postUrl = $url;
        $curlPost = $param;
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);

        return $data;
    }

    public function send($sendno = 0,$receiver_type = 1, $receiver_value = '', $msg_type = 1, $msg_content = '', $platform = 'android,ios') {
        $url = 'http://api.jpush.cn:8800/sendmsg/v2/sendmsg';
        $param = '';
        $param .= '&sendno='.$sendno;
        $appkeys = $this->_appkeys;
        $param .= '&app_key='.$appkeys;
        $param .= '&receiver_type='.$receiver_type;
        $param .= '&receiver_value='.$receiver_value;
        $masterSecret = $this->_masterSecret;
        $verification_code = md5($sendno.$receiver_type.$receiver_value.$masterSecret);
        $param .= '&verification_code='.$verification_code;
        $param .= '&msg_type='.$msg_type;
        $param .= '&msg_content='.$msg_content;
        $param .= '&platform='.$platform;
        $res = $this->request_post($url, $param);
        if ($res === false) {
            return false;
        }
        $res_arr = json_decode($res, true);
        return $res_arr;
    }


}