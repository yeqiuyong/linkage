<?php
/**
 * Created by PhpStorm.
 * User: uni
 * Date: 16/4/5
 * Time: 下午10:30
 */


namespace Multiple\API\Controllers;

use Phalcon\Di;

use Multiple\Core\APIControllerBase;
use Multiple\Core\Constants\ErrorCodes;
use Multiple\Core\Constants\Services;
use Multiple\Core\Constants\StatusCodes;
use Multiple\Core\Constants\LinkageUtils;
use Multiple\Core\Exception\Exception;

use Multiple\Models\ClientUser;
use Multiple\Models\ClientUserRole;
use Multiple\Models\Driver;
use Multiple\Models\DriverTask;

class TransporterController extends APIControllerBase
{
    private $logger;

    public function initialize(){
        parent::initialize();

        $this->logger = Di::getDefault()->get(Services::LOGGER);

    }

    /**
     * @title("dispatch")
     * @description("Dispatch task for drivers")
     * @requestExample("POST /transporter/dispatch")
     * @response("Data object or Error object")
     */
    public function dispatchAction(){

    }

    /**
     * @title("drivers")
     * @description("Get Drivers")
     * @requestExample("POST /transporter/drivers")
     * @response("Data object or Error object")
     */
    public function driversAction(){
        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        try {
            $user = new ClientUser();
            $userInfo = $user->getUserInfomation($this->cid);

            if($userInfo['role'] != ''){

            }

            $driver = new Driver();
            $drivers = $driver->getDriversByCompanyId($userInfo['company_id']);

            $driverTask = new DriverTask();
            $driverOrderCnts = $driverTask->getOrderNumber($userInfo['company_id']);

            foreach($drivers as $myDriver){
                foreach($driverOrderCnts as $driverOrderCnt){
                    if($myDriver['driver_id'] == $driverOrderCnt['driver_id']){
                        $myDriver['order_num'] = $driverOrderCnt['order_num'];
                    }
                }
            }

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondArray(['drivers' => $drivers]);

    }

    /**
     * @title("adddriver")
     * @description("Add driver")
     * @requestExample("POST /transporter/adddriver")
     * @response("Data object or Error object")
     */
    public function addDriverAction(){
        $name = $this->request->getPost('name', 'string');
        $mobile = $this->request->getPost('mobile', 'string');
        $gender = $this->request->getPost('gender', 'string');
        $license = $this->request->getPost('license', 'string');
        $icon = $this->request->getPost('icon', 'string');

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        if(empty($mobile)){
            return $this->respondError(ErrorCodes::USER_MOBILE_NULL, ErrorCodes::$MESSAGE[ErrorCodes::USER_MOBILE_NULL]);
        }

        try{
            // Start a transaction
            $this->db->begin();

            $transporterAdmin = new ClientUser();
            $companyID = $transporterAdmin->getCompanyidByUserid($this->cid);

            $driver = new ClientUser();
            $driver->registerDriver($mobile, $mobile, StatusCodes::CLIENT_USER_ACTIVE, $companyID, $name, $gender, $icon);
            $driverId = $driver->user_id;

            $userRole = new ClientUserRole();
            $userRole->add($driverId, LinkageUtils::USER_DRIVER);

            $mDriver = new Driver();
            $mDriver->add($driverId, $license);

            // Commit the transaction
            $this->db->commit();

        }catch (Exception $e){
            $this->db->rollback();

            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondOK();
    }

    /**
     * @title("deldriver")
     * @description("Delete driver")
     * @requestExample("POST /transporter/deldriver")
     * @response("Data object or Error object")
     */
    public function delDriverAction(){
        $driver_id = $this->request->getPost('driver_id', 'int');

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        if(!isset($driver_id)){
            return $this->respondError(ErrorCodes::USER_ID_NULL, ErrorCodes::$MESSAGE[ErrorCodes::USER_ID_NULL]);
        }

        try{
            $driver = new ClientUser();
            $driver->updateStatus($driver_id, StatusCodes::CLIENT_USER_DELETED);

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondOK();
    }

    /**
     * @title("driverdetail")
     * @description("Driver detail")
     * @requestExample("POST /transporter/driverdetail")
     * @response("Data object or Error object")
     */
    public function driverDetailAction(){

        $driver_id = $this->request->getPost('driver_id', 'int');

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        if(!isset($driver_id)){
            return $this->respondError(ErrorCodes::USER_ID_NULL, ErrorCodes::$MESSAGE[ErrorCodes::USER_ID_NULL]);
        }

        try{
            $driver = new ClientUser();
            $driver->updateStatus($driver_id, StatusCodes::CLIENT_USER_DELETED);

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondOK();
    }

    /**
     * @title("cars")
     * @description("Get Cars")
     * @requestExample("POST /transporter/cars")
     * @response("Data object or Error object")
     */
    public function carsAction(){

    }

    /**
     * @title("addcar")
     * @description("Add car")
     * @requestExample("POST /transporter/addcar")
     * @response("Data object or Error object")
     */
    public function addCarAction(){

    }

    /**
     * @title("delcar")
     * @description("Delete car")
     * @requestExample("POST /transporter/delcar")
     * @response("Data object or Error object")
     */
    public function delCarAction(){

    }

    /**
     * @title("cardetail")
     * @description("Car detail")
     * @requestExample("POST /transporter/cardetail")
     * @response("Data object or Error object")
     */
    public function carDetailAction(){

    }

}