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
use Multiple\Models\Order;
use Multiple\Models\Car;
use Multiple\Models\SystemSet;

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
        $orderId = $this->request->getPost('order_id', 'string');
        $dispatchJson = $this->request->getPost('dispatch_info');

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        if(empty($orderId)){
            return $this->respondError(ErrorCodes::ORDER_ID_NULL, ErrorCodes::$MESSAGE[ErrorCodes::ORDER_ID_NULL]);
        }

        if(empty($dispatchJson)){
            return $this->respondError(ErrorCodes::ORDER_DISPATCH_INFO_NULL, ErrorCodes::$MESSAGE[ErrorCodes::ORDER_DISPATCH_INFO_NULL]);
        }


        $dispatchInfo = json_decode($dispatchJson, true);

        try {
            $order = new Order();
            $orderInfo = $order->getOrderInfo($orderId);

            $cargos = $dispatchInfo['cargos'];
            if(sizeof($cargos) == 0){
                return $this->respondError(ErrorCodes::ORDER_DISPATCH_INFO_NULL, ErrorCodes::$MESSAGE[ErrorCodes::ORDER_DISPATCH_INFO_NULL]);
            }

            $this->db->begin();

            foreach($cargos as $cargo){
                $driverTask = new DriverTask();
                $driverTask->add($orderId,
                    $orderInfo['type'],
                    $orderInfo['transporter_id'],
                    $cargo['driver_id'],
                    $cargo['car_id'],
                    $cargo['cargo_no'],
                    $cargo['cargo_type']
                );
            }

            // Commit the transaction
            $this->db->commit();

        }catch (Exception $e){
            $this->db->rollback();
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondOK();
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

            if($userInfo['role'] != LinkageUtils::ROLE_ADMIN_TRANSPORTER){
                return $this->respondError(ErrorCodes::AUTH_UNAUTHORIZED, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_UNAUTHORIZED]);
            }

            $driver = new Driver();
            $drivers = $driver->getDriversByCompanyId($userInfo['company_id']);

            $driverTask = new DriverTask();
            $driverOrderCnts = $driverTask->getOrderNumber($userInfo['company_id']);

            $result = [];
            foreach($drivers as $myDriver){
                $myDriver['order_num'] = 0;
                foreach($driverOrderCnts as $driverOrderCnt){
                    if($myDriver['driver_id'] == $driverOrderCnt['driver_id']){
                        $myDriver['order_num'] = $driverOrderCnt['order_num'];
                        break;
                    }
                }

                array_push($result, $myDriver);
            }

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondArray(['drivers' => $result]);

    }

    /**
     * @title("adddriver")
     * @description("Add driver")
     * @requestExample("POST /transporter/adddriver")
     * @response("Data object or Error object")
     */
    public function addDriverAction(){
        $name = $this->request->getPost('driver_name', 'string');
        $mobile = $this->request->getPost('driver_mobile', 'string');
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
            $transporterInfo = $transporterAdmin->getUserInfomation($this->cid);

            if($transporterInfo['role'] != LinkageUtils::ROLE_ADMIN_TRANSPORTER){
                return $this->respondError(ErrorCodes::AUTH_UNAUTHORIZED, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_UNAUTHORIZED]);
            }

            $driver = new ClientUser();
            $driver->registerDriver($mobile, $mobile, StatusCodes::CLIENT_USER_ACTIVE, $transporterInfo['company_id'], $name, $gender, $icon);
            $driverId = $driver->user_id;

            $userRole = new ClientUserRole();
            $userRole->add($driverId, LinkageUtils::USER_DRIVER);

            $mDriver = new Driver();
            $mDriver->add($driverId, $license);

            $systemSet = new SystemSet();
            $systemSet->init($driverId);

            // Commit the transaction
            $this->db->commit();

        }catch (Exception $e){
            $this->db->rollback();

            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondArray(['driver_id' => $driverId]);
    }

    /**
     * @title("deldriver")
     * @description("Delete driver")
     * @requestExample("POST /transporter/deldriver")
     * @response("Data object or Error object")
     */
    public function delDriverAction(){
        $driverId = $this->request->getPost('driver_id', 'int');

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        if(!isset($driverId)){
            return $this->respondError(ErrorCodes::USER_ID_NULL, ErrorCodes::$MESSAGE[ErrorCodes::USER_ID_NULL]);
        }

        try{
            $transporterAdmin = new ClientUser();
            $transporterInfo = $transporterAdmin->getUserInfomation($this->cid);

            if($transporterInfo['role'] != LinkageUtils::ROLE_ADMIN_TRANSPORTER){
                return $this->respondError(ErrorCodes::AUTH_UNAUTHORIZED, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_UNAUTHORIZED]);
            }

            $driver = new ClientUser();
            $driver->updateStatus($driverId, StatusCodes::CLIENT_USER_DELETED);

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
        $driverId = $this->request->getPost('driver_id', 'int');

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        if(!isset($driverId)){
            return $this->respondError(ErrorCodes::USER_ID_NULL, ErrorCodes::$MESSAGE[ErrorCodes::USER_ID_NULL]);
        }

        try{
            $transporterAdmin = new ClientUser();
            $transporterInfo = $transporterAdmin->getUserInfomation($this->cid);

            if($transporterInfo['role'] != LinkageUtils::ROLE_ADMIN_TRANSPORTER){
                return $this->respondError(ErrorCodes::AUTH_UNAUTHORIZED, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_UNAUTHORIZED]);
            }

            $driver = new Driver();
            $driverDetail = $driver->getDriverDetail($driverId);

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondArray($driverDetail);
    }

    /**
     * @title("moddriver")
     * @description("Add driver")
     * @requestExample("POST /transporter/adddriver")
     * @response("Data object or Error object")
     */
    public function modDriverAction(){
//        $mobile = $this->request->getPost('driver_mobile', 'string');
        $driverId = $this->request->getPost('driver_id', 'int');
        $name = $this->request->getPost('driver_name', 'string');
        $gender = $this->request->getPost('gender', 'string');
        $license = $this->request->getPost('license', 'string');
        $icon = $this->request->getPost('icon', 'string');

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        try{
            // Start a transaction
            $this->db->begin();

            $transporterAdmin = new ClientUser();
            $transporterInfo = $transporterAdmin->getUserInfomation($this->cid);

            if($transporterInfo['role'] != LinkageUtils::ROLE_ADMIN_TRANSPORTER){
                return $this->respondError(ErrorCodes::AUTH_UNAUTHORIZED, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_UNAUTHORIZED]);
            }

            $user = new ClientUser();
            $user->updateProfile($driverId, ['name'=>$name, 'gender'=> $gender, 'icon'=>$icon ]);

            $driver = new Driver();
            $driver->modify($driverId, $license);

            // Commit the transaction
            $this->db->commit();

        }catch (Exception $e){
            $this->db->rollback();

            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondArray(['driver_id' => $driverId]);
    }

    /**
     * @title("cars")
     * @description("Get Cars")
     * @requestExample("POST /transporter/cars")
     * @response("Data object or Error object")
     */
    public function carsAction(){
        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        try {
            $user = new ClientUser();
            $userInfo = $user->getUserInfomation($this->cid);

            if($userInfo['role'] != LinkageUtils::ROLE_ADMIN_TRANSPORTER){
                return $this->respondError(ErrorCodes::AUTH_UNAUTHORIZED, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_UNAUTHORIZED]);
            }

            $car = new Car();
            $cars = $car->getCarsByCompanyId($userInfo['company_id']);

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondArray(['cars' => $cars]);
    }

    /**
     * @title("addcar")
     * @description("Add car")
     * @requestExample("POST /transporter/addcar")
     * @response("Data object or Error object")
     */
    public function addCarAction(){
        $license = $this->request->getPost('license', 'string');
        $engineNo = $this->request->getPost('engine_no', 'string');
        $frameNo = $this->request->getPost('frame_no', 'string');
        $applyDate = $this->request->getPost('apply_date', 'int');
        $examineDate = $this->request->getPost('examine_date', 'int');
        $maintainDate = $this->request->getPost('maintain_date', 'int');
        $trafficInsureDate = $this->request->getPost('traffic_insure_date', 'int');
        $businessInsureDate = $this->request->getPost('business_insure_date', 'int');
        $insureCompany = $this->request->getPost('insure_company', 'string');
        $memo = $this->request->getPost('memo', 'string');

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        try {
            $user = new ClientUser();
            $userInfo = $user->getUserInfomation($this->cid);

            if($userInfo['role'] != LinkageUtils::ROLE_ADMIN_TRANSPORTER){
                return $this->respondError(ErrorCodes::AUTH_UNAUTHORIZED, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_UNAUTHORIZED]);
            }

            $car = new Car();
            $car->add($userInfo['company_id'], $license, $engineNo, $frameNo, $applyDate, $examineDate, $maintainDate, $trafficInsureDate, $businessInsureDate, $insureCompany, $memo);

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondOK();
    }

    /**
     * @title("delcar")
     * @description("Delete car")
     * @requestExample("POST /transporter/delcar")
     * @response("Data object or Error object")
     */
    public function delCarAction(){
        $carId = $this->request->getPost('car_id', 'int');

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        if(!isset($carId)){
            return $this->respondError(ErrorCodes::USER_CAR_ID_NULL, ErrorCodes::$MESSAGE[ErrorCodes::USER_CAR_ID_NULL]);
        }

        try{
            $transporterAdmin = new ClientUser();
            $transporterInfo = $transporterAdmin->getUserInfomation($this->cid);

            if($transporterInfo['role'] != LinkageUtils::ROLE_ADMIN_TRANSPORTER){
                return $this->respondError(ErrorCodes::AUTH_UNAUTHORIZED, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_UNAUTHORIZED]);
            }

            $car = new Car();
            $car->updateStatus($carId, StatusCodes::CAR_DELETED);

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondOK();
    }

    /**
     * @title("cardetail")
     * @description("Car detail")
     * @requestExample("POST /transporter/cardetail")
     * @response("Data object or Error object")
     */
    public function carDetailAction(){
        $carId = $this->request->getPost('car_id', 'int');

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        if(!isset($carId)){
            return $this->respondError(ErrorCodes::USER_CAR_ID_NULL, ErrorCodes::$MESSAGE[ErrorCodes::USER_CAR_ID_NULL]);
        }

        try{
            $transporterAdmin = new ClientUser();
            $transporterInfo = $transporterAdmin->getUserInfomation($this->cid);

            if($transporterInfo['role'] != LinkageUtils::ROLE_ADMIN_TRANSPORTER){
                return $this->respondError(ErrorCodes::AUTH_UNAUTHORIZED, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_UNAUTHORIZED]);
            }

            $car = new Car();
            $carDetail = $car->getCarDetail($carId);

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondArray($carDetail);
    }

    /**
     * @title("modcar")
     * @description("Add car")
     * @requestExample("POST /transporter/addcar")
     * @response("Data object or Error object")
     */
    public function modCarAction(){
        $carId = $this->request->getPost('car_id', 'int');
        $applyDate = $this->request->getPost('apply_date', 'int');
        $examineDate = $this->request->getPost('examine_date', 'int');
        $maintainDate = $this->request->getPost('maintain_date', 'int');
        $trafficInsureDate = $this->request->getPost('traffic_insure_date', 'int');
        $businessInsureDate = $this->request->getPost('business_insure_date', 'int');
        $insureCompany = $this->request->getPost('insure_company', 'string');
        $memo = $this->request->getPost('memo', 'string');

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        if(!isset($carId)){
            return $this->respondError(ErrorCodes::USER_CAR_ID_NULL, ErrorCodes::$MESSAGE[ErrorCodes::USER_CAR_ID_NULL]);
        }

        try {
            $car = new Car();
            $car->modify($carId, $applyDate, $examineDate, $maintainDate, $trafficInsureDate, $businessInsureDate, $insureCompany, $memo);

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondOK();
    }

}