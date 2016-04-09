<?php
/**
 * Created by PhpStorm.
 * User: uni
 * Date: 16/3/18
 * Time: 下午8:39
 */


namespace Multiple\API\Controllers;

use Multiple\Models\ClientUser;
use Phalcon\Di;

use Multiple\Core\Exception\Exception;
use Multiple\Core\APIControllerBase;
use Multiple\Core\Constants\LinkageUtils;
use Multiple\Core\Constants\Services;
use Multiple\Core\Constants\ErrorCodes;

use Multiple\Models\Notice;


/**
 * @resource("User")
 */
class MessageController extends APIControllerBase
{

    private $logger;

    public function initialize()
    {
        parent::initialize();

        $this->logger = Di::getDefault()->get(Services::LOGGER);

    }

    /**
     * @title("list")
     * @description("User message list")
     * @requestExample("POST /message/list")
     * @response("Data object or Error object")
     */
    public function listAction(){
        $pagination = $this->request->getPost('pagination');
        $offset = $this->request->getPost('offset');
        $size = $this->request->getPost('size');

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        try {
            $user = new ClientUser();
            $roleId = $user->getRoleId($this->cid);

            $notice = new Notice();
            $messages = $notice->getMsg($roleId, $pagination, $offset, $size);

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondArray($messages);

    }

    /**
     * @title("detail")
     * @description("User message detail")
     * @requestExample("POST /message/detail")
     * @response("Data object or Error object")
     */
    public function detailAction(){
        $messageId = $this->request->getPost('mid');

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        try {
            $notice = new Notice();
            $message = $notice->getMsgDetail($messageId);

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondArray($message);

    }

}