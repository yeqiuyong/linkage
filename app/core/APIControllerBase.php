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
     * 登陆用户信息
     * @var array
     */
    public $userinfo;

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
    public $client_model = '';

    /**
     * 客户端版本号
     *
     * @var int
     */
    public $client_ver = 0;

    /**
     * 手机 SDK 版本号
     * @var String
     */
    public $client_sdkver = '';

    /**
     * 客户端操作系统版本号
     * @var String
     */
    public $client_releasever = '';


    /**
     * 卡卡兔cid
     * @var string
     */
    public $cid = '';


    /**
     * 第三方app id
     * @var string
     */
    public $appid = 114;


    /**
     * User Model
     * @var muser
     */
    public $muser = null;



    /**
     * @var \League\Fractal\Manager
     */
    protected $fractal;



    public function onConstruct()
    {
        $this->fractal = $this->di->get(Services::FRACTAL_MANAGER);
    }

    public function respondArray($array, $key)
    {
        $response = [$key => $array];

        return $this->respond($response);
    }

    public function respondOK()
    {
        $response = ['result' => 'OK'];

        return $this->respond($response);
    }

    public function responseItemOK($item, $callback, $resource_key)
    {
        $response = $this->respondItem($item, $callback, $resource_key);
        $response['result'] = 'OK';

        return $this->respond($response);
    }

    public function respondItem($item, $callback, $resource_key, $meta = [])
    {
        $resource = new Item($item, $callback, $resource_key);
        $data = $this->fractal->createData($resource)->toArray();
        $response = array_merge($data, $meta);

        return $this->respond($response);
    }

    public function respondCollection($collection, $callback, $resource_key, $meta = [])
    {
        $resource = new Collection($collection, $callback, $resource_key);
        $data = $this->fractal->createData($resource)->toArray();
        $response = array_merge($data, $meta);

        return $this->respond($response);
    }

    public function respond($response)
    {
        $json = json_encode($response);
        echo $json;
    }
}