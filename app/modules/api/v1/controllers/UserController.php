<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 25/1/16
 * Time: 10:25 AM
 */

namespace Multiple\API\Controllers;

use Phalcon\Di;

use Multiple\Core\Exception\Exception;
use Multiple\Core\APIControllerBase;
use Multiple\Core\Constants\Services;
use Multiple\Core\Constants\ErrorCodes;

use Multiple\Models\ClientUser;


/**
 * @resource("User")
 */
class UserController extends APIControllerBase
{

    private $logger;

    public function initialize(){
        parent::initialize();

        $this->logger = Di::getDefault()->get(Services::LOGGER);

    }

    public function staffAction(){
        $pagination = $this->request->getPost('pagination', 'int');
        $offset = $this->request->getPost('offset', 'int');
        $size = $this->request->getPost('size', 'int');

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        try {
            $user = new ClientUser();

            if(!$user->isAdmin($this->cid)){
                return $this->respondError(ErrorCodes::AUTH_UNAUTHORIZED, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_UNAUTHORIZED]);
            }

            $staffs = $user->getStaffs($this->cid, $pagination, $offset, $size);

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondArray($staffs);
    }

    public function delStaffAction(){
        $staffId = $this->request->getPost('staff_cid');

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        try {
            $user = new ClientUser();

            if(!$user->isAdmin($this->cid)){
                return $this->respondError(ErrorCodes::AUTH_UNAUTHORIZED, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_UNAUTHORIZED]);
            }

            $user->delStaff($this->cid, $staffId);

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this-$this->respondOK();
    }

}
