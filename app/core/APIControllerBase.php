<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 25/1/16
 * Time: 10:25 AM
 */

namespace Multiple\Core;

use Phalcon\Mvc\Controller;

use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Multiple\Core\Constants\Services;

class APIControllerBase extends Controller
{

    /**
     * 用户cid
     * @var string
     */
    public $cid = '';

    /**
     * 登陆用户信息
     * @var array
     */
    public $userInfo;

    /**
     * 当前请求设备id
     * @var string
     */
    public $device = '';


    /**
     * 客户端设备平台
     *
     * @var String
     */
    public $platform = '';

    /**
     * 客户端型号
     *
     * @var String
     */
    public $clientModel = '';

    /**
     * 客户端版本号
     *
     * @var int
     */
    public $clientVersion = 0;

    /**
     * 手机 SDK 版本号
     * @var String
     */
    public $clientSdkVersion = '';

    /**
     * 客户端操作系统版本号
     * @var String
     */
    public $clientReleaseVersion = '';


    /**
     * 第三方app id
     * @var string
     */
    public $appid = 114;


    /**
     * @var \League\Fractal\Manager
     */
    protected $fractal;


    protected function initialize(){
        $this->fractal = $this->di->get(Services::FRACTAL_MANAGER);

        $this->cid = $this->request->getPost('cid');
        $this->token = $this->request->getPost('token');
    }

    public function respondData($data){
        $data['result'] = 0;
        $data['reason'] = '';

        return $this->respond($data);
    }

    public function respondArray($array, $key){
        $response['result'] = 0;
        $response['reason'] = '';

        $response = [$key => $array];

        return $this->respond($response);
    }

    public function respondOK(){
        $response = ['result' => 0, 'reason' => ''];

        return $this->respond($response);
    }

    public function respondError($result, $reason){
        $response = ['result' => $result, 'reason' => $reason];

        return $this->respond($response);
    }

    public function responseItemOK($item, $callback, $resource_key){
        $response = $this->respondItem($item, $callback, $resource_key);
        $response['result'] = 0;
        $response['reason'] = '';

        return $this->respond($response);
    }

    public function respondItem($item, $callback, $resource_key, $meta = []){
        $resource = new Item($item, $callback, $resource_key);
        $data = $this->fractal->createData($resource)->toArray();
        $response = array_merge($data, $meta);

        return $this->respond($response);
    }

    public function respondCollection($collection, $callback, $resource_key, $meta = []){
        $resource = new Collection($collection, $callback, $resource_key);
        $data = $this->fractal->createData($resource)->toArray();
        $response = array_merge($data, $meta);

        return $this->respond($response);
    }

    public function respond($response){
        $json = json_encode($response);
        echo $json;
    }
}