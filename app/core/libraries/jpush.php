<?php
/**
 * Created by PhpStorm.
 * User: whoami
 * Date: 16-8-30
 * Time: 下午7:49
 */
require_once APP_PATH . 'vendor/jpush/jpush/src/JPush/Client.php';
require_once APP_PATH . 'vendor/jpush/jpush/src/JPush/PushPayload.php';
require_once APP_PATH . 'vendor/jpush/jpush/src/JPush/Http.php';
require_once APP_PATH . 'vendor/jpush/jpush/src/JPush/ReportPayload.php';
require_once APP_PATH . 'vendor/jpush/jpush/src/JPush/Exceptions/JPushException.php';
require_once APP_PATH . 'vendor/jpush/jpush/src/JPush/Exceptions/APIConnectionException.php';
require_once APP_PATH . 'vendor/jpush/jpush/src/JPush/Exceptions/APIRequestException.php';

class jpush
{


    private $_masterSecret;

    private $_appkeys;

    public function __construct(){
        $this->_masterSecret = 'ab6203c0c8263729040aade7';
        $this->_appkeys = '886e799b9063abb80fb6a4db';
    }

    /**
     * 模拟post进行url请求
     * @param string $url
     * @param string $param
     */
    function request_post($url = '', $param = '',$header='') {
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
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);

        return $data;
    }

    function pushsend($receiver='0',$alert = '消息提示',$type=2) {

        $client = new \JPush\Client($this->_appkeys,$this->_masterSecret);
        $push = $client->push();
        $platform = array('ios', 'android');
        $alias = array($receiver);
        $ios_notification = array(
            'sound' => '',
            'badge' => '+1',
            'content-available' => true,
            'extras'=> array(
                'id'=> $receiver,
                'title'=>'Linkage 通知',
                'content'=> $alert,
                'create_time'=>time(),
                'type' => $type
            )
        );
        $android_notification = array(
            'title' => 'Linkage 通知',
            'build_id' => 2,
            'extras'=> array(
                'id'=> $receiver,
                'title'=>'Linkage 通知',
                'content'=> $alert,
                'create_time'=>time(),
                'type' => $type
            ),
        );
        /*
         *自定义消息
         * /
        $content = 'Hello World';

        $message = array(
            'content_type'=>'text',
            'title'=>'1122',
            'extras'=> array(
                'id'=> $receiver,
                'title'=>'Linkage 通知',
                'content'=> $alert,
                'create_time'=>time(),
                'type' => $type
            )
        );
        */

        $options = array(
            'apns_production' => true
        );

        $response = $push->setPlatform($platform)
            ->addAlias($alias)
            ->iosNotification($alert, $ios_notification)
            ->androidNotification($alert, $android_notification)
            //->message($content, $message)自定义消息
            ->options($options)
        ->send();
        return $response;

    }

}