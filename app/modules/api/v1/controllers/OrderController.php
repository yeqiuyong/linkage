<?php
/**
 * Created by PhpStorm.
 * User: uni
 * Date: 16/3/27
 * Time: 下午2:39
 */


namespace Multiple\API\Controllers;

use Phalcon\Di;

use Multiple\Core\Exception\Exception;
use Multiple\Core\APIControllerBase;
use Multiple\Core\Constants\LinkageUtils;
use Multiple\Core\Constants\Services;
use Multiple\Core\Constants\ErrorCodes;
use Multiple\Core\Constants\StatusCodes;

use Multiple\Models\ClientUser;
use Multiple\Models\Order;
use Multiple\Models\OrderExport;
use Multiple\Models\OrderImport;
use Multiple\Models\OrderSelf;
use Multiple\Models\OrderCargo;
use Multiple\Models\Company;
use Multiple\Models\DriverTask;
use Multiple\Models\OrderComment;


/**
 * @resource("User")
 */
class OrderController extends APIControllerBase
{

    private $logger;

    public function initialize()
    {
        parent::initialize();

        $this->redis = Di::getDefault()->get(Services::REDIS);
        $this->logger = Di::getDefault()->get(Services::LOGGER);

    }

    /**
     * @title("place4export")
     * @description("Place export order")
     * @requestExample("POST /order/place4export")
     * @response("Data object or Error object")
     */
    public function place4exportAction(){
        $tCompanyId = $this->request->getPost('company_id', 'int');
        $cargoStr = $this->request->getPost('cargo', 'string');
        $takeAddress = $this->request->getPost('take_address', 'string');
        $takeTime = $this->request->getPost('take_time', 'int');
        $deliveryAddress = $this->request->getPost('delivery_address', 'string');
        $deliveryTime = $this->request->getPost('delivery_time', 'int');
        $isTransferPort = $this->request->getPost('is_transfer_port', 'int');
        $memo = $this->request->getPost('memo', 'string');
        $port = $this->request->getPost('port', 'string');
        $customsIn = $this->request->getPost('customs_in', 'int');
        $so = $this->request->getPost('so', 'string');
        $soImages = $this->request->getPost('so_images', 'string');
        $shipCompany = $this->request->getPost('ship_company', 'string');
        $shipName = $this->request->getPost('ship_name', 'string');
        $shipSchedule = $this->request->getPost('ship_schedule_no', 'string');
        $isBookCargo = $this->request->getPost('is_book_cargo', 'int');

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        if(!isset($tCompanyId)){
            return $this->respondError(ErrorCodes::ORDER_TRANSPORTER_NULL, ErrorCodes::$MESSAGE[ErrorCodes::ORDER_TRANSPORTER_NULL]);
        }

        try{
            // Start a transaction
            $this->db->begin();

            $user = new ClientUser();
            $mUserInfo = $user->getUserInfomation($this->cid);

            $company = new Company();
            $tCompanyInfo = $company->getCompanyInformation($tCompanyId);

            $orderId = $this->genOrderId($this->cid);

            $order = new Order();
            $order->add($orderId, LinkageUtils::ORDER_TYPE_EXPORT, $mUserInfo['company_id'], $tCompanyId, $this->cid, $mUserInfo['realname'], $mUserInfo['mobile'], $takeAddress, $takeTime, $deliveryAddress, $deliveryTime, $isTransferPort, $memo);

            $orderExport = new OrderExport();
            $orderExport->add($orderId, $so, $soImages, $customsIn, $port, $shipCompany, $shipName, $shipSchedule, $isBookCargo);

            $cargoObjs = $this->genCargosObj($cargoStr);
            foreach($cargoObjs as $cargoObj){
                for($i = 0; $i < $cargoObj['num']; $i++){
                    $orderCargo = new OrderCargo();
                    $orderCargo->add($orderId, $cargoObj['type']);
                }
            }

            // Commit the transaction
            $this->db->commit();

            $result = ['order_id' => $orderId,
                'transporter_id' => $tCompanyId,
                'transporter_name' => $tCompanyInfo['name'],
                'order_status' => StatusCodes::ORDER_PLACE,
                'create_time' => time(),
                'process' => 0
            ];

        }catch (Exception $e){
            $this->db->rollback();

            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondArray($result);

    }

    /**
     * @title("place4import")
     * @description("Place import order")
     * @requestExample("POST /order/place4import")
     * @response("Data object or Error object")
     */
    public function place4importAction(){
        $tCompanyId = $this->request->getPost('company_id', 'int');
        $cargoStr = $this->request->getPost('cargo', 'string');
        $takeAddress = $this->request->getPost('take_address', 'string');
        $takeTime = $this->request->getPost('take_time', 'int');
        $deliveryAddress = $this->request->getPost('delivery_address', 'string');
        $deliveryTime = $this->request->getPost('delivery_time', 'int');
        $isTransferPort = $this->request->getPost('is_transfer_port', 'int');
        $memo = $this->request->getPost('memo', 'string');
        $rentExpire = $this->request->getPost('cargos_rent_expire', 'int');
        $billNo = $this->request->getPost('bill_no', 'string');
        //$cargoNo = $this->request->getPost('cargo_no', 'string');
        $cargoCompany = $this->request->getPost('cargo_company', 'string');
        $customBroker = $this->request->getPost('customs_broker', 'string');
        $customContact = $this->request->getPost('customs_contact', 'string');

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        if(!isset($tCompanyId)){
            return $this->respondError(ErrorCodes::ORDER_TRANSPORTER_NULL, ErrorCodes::$MESSAGE[ErrorCodes::ORDER_TRANSPORTER_NULL]);
        }

        try{
            // Start a transaction
            $this->db->begin();

            $user = new ClientUser();
            $mUserInfo = $user->getUserInfomation($this->cid);

            $company = new Company();
            $tCompanyInfo = $company->getCompanyInformation($tCompanyId);

            $orderId = $this->genOrderId($this->cid);

            $order = new Order();
            $order->add($orderId, LinkageUtils::ORDER_TYPE_IMPORT, $mUserInfo['company_id'], $tCompanyId, $this->cid, $mUserInfo['realname'], $mUserInfo['mobile'], $takeAddress, $takeTime, $deliveryAddress, $deliveryTime, $isTransferPort, $memo);

            $orderImport = new OrderImport();
            $orderImport->add($orderId, $rentExpire, $billNo, '', $cargoCompany, $customBroker, $customContact);

            $cargoObjs = $this->genCargosObj2($cargoStr);
            foreach($cargoObjs as $cargoObj){
                $orderCargo = new OrderCargo();
                $orderCargo->addWithNo($orderId, $cargoObj['cargono'], $cargoObj['type']);
            }

            // Commit the transaction
            $this->db->commit();

            $result = ['order_id' => $orderId,
                'transporter_id' => $tCompanyId,
                'transporter_name' => $tCompanyInfo['name'],
                'order_status' => StatusCodes::ORDER_PLACE,
                'create_time' => time(),
                'process' => 0
            ];

        }catch (Exception $e){
            $this->db->rollback();

            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondArray($result);

    }

    /**
     * @title("place4self")
     * @description("Place self order")
     * @requestExample("POST /order/place4self")
     * @response("Data object or Error object")
     */
    public function place4selfAction(){
        $tCompanyId = $this->request->getPost('company_id', 'int');
        $cargoStr = $this->request->getPost('cargo', 'string');
        $takeAddress = $this->request->getPost('take_address', 'string');
        $takeTime = $this->request->getPost('take_time', 'int');
        $deliveryAddress = $this->request->getPost('delivery_address', 'string');
        $deliveryTime = $this->request->getPost('delivery_time', 'int');
        $isTransferPort = $this->request->getPost('is_transfer_port', 'int');
        $memo = $this->request->getPost('memo', 'string');
        $customsIn = $this->request->getPost('customs_in', 'int');
        $cargoTakeTime = $this->request->getPost('cargo_take_time', 'int');
        $isCustomsDeclare = $this->request->getPost('is_customs_declare', 'int');

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        if(!isset($tCompanyId)){
            return $this->respondError(ErrorCodes::ORDER_TRANSPORTER_NULL, ErrorCodes::$MESSAGE[ErrorCodes::ORDER_TRANSPORTER_NULL]);
        }

        try{
            // Start a transaction
            $this->db->begin();

            $user = new ClientUser();
            $mUserInfo = $user->getUserInfomation($this->cid);

            $company = new Company();
            $tCompanyInfo = $company->getCompanyInformation($tCompanyId);

            $orderId = $this->genOrderId($this->cid);

            $order = new Order();
            $order->add($orderId, LinkageUtils::ORDER_TYPE_SELF, $mUserInfo['company_id'], $tCompanyId, $this->cid, $mUserInfo['realname'], $mUserInfo['mobile'], $takeAddress, $takeTime, $deliveryAddress, $deliveryTime, $isTransferPort, $memo);

            $orderSelf = new OrderSelf();
            $orderSelf->add($orderId, $customsIn, $cargoTakeTime, $isCustomsDeclare);

            $cargoObjs = $this->genCargosObj($cargoStr);
            foreach($cargoObjs as $cargoObj){
                for($i = 0; $i < $cargoObj['num']; $i++){
                    $orderCargo = new OrderCargo();
                    $orderCargo->add($orderId, $cargoObj['type']);
                }
            }

            // Commit the transaction
            $this->db->commit();

            $result = ['order_id' => $orderId,
                'transporter_id' => $tCompanyId,
                'transporter_name' => $tCompanyInfo['name'],
                'order_status' => StatusCodes::ORDER_PLACE,
                'create_time' => time(),
                'process' => 0
            ];

        }catch (Exception $e){
            $this->db->rollback();

            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondArray($result);

    }

    /**
     * @title("mod4export")
     * @description("modify export order")
     * @requestExample("POST /order/mod4export")
     * @response("Data object or Error object")
     */
    public function mod4exportAction(){
        $rejectOrderId = $this->request->getPost('reject_order_id', 'string');
        $rejectOrderStatus = $this->request->getPost('reject_order_status', 'int');
        $tCompanyId = $this->request->getPost('company_id', 'int');
        $cargoStr = $this->request->getPost('cargo', 'string');
        $takeAddress = $this->request->getPost('take_address', 'string');
        $takeTime = $this->request->getPost('take_time', 'int');
        $deliveryAddress = $this->request->getPost('delivery_address', 'string');
        $deliveryTime = $this->request->getPost('delivery_time', 'int');
        $isTransferPort = $this->request->getPost('is_transfer_port', 'int');
        $memo = $this->request->getPost('memo', 'string');
        $port = $this->request->getPost('port', 'string');
        $customsIn = $this->request->getPost('customs_in', 'int');
        $so = $this->request->getPost('so', 'string');
        $soImages = $this->request->getPost('so_images', 'string');
        $shipCompany = $this->request->getPost('ship_company', 'string');
        $shipName = $this->request->getPost('ship_name', 'string');
        $shipSchedule = $this->request->getPost('ship_schedule_no', 'string');
        $isBookCargo = $this->request->getPost('is_book_cargo', 'int');

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        if(!isset($tCompanyId)){
            return $this->respondError(ErrorCodes::ORDER_TRANSPORTER_NULL, ErrorCodes::$MESSAGE[ErrorCodes::ORDER_TRANSPORTER_NULL]);
        }

        if(!isset($rejectOrderId)){
            return $this->respondError(ErrorCodes::ORDER_ID_NULL, ErrorCodes::$MESSAGE[ErrorCodes::ORDER_ID_NULL]);
        }

        if(!isset($rejectOrderStatus) || $rejectOrderStatus != StatusCodes::ORDER_REJECT){
            return $this->respondError(ErrorCodes::AUTH_UNAUTHORIZED, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_UNAUTHORIZED]);
        }

        try{
            // Start a transaction
            $this->db->begin();

            $order = new Order();
            $order->updateStatus($rejectOrderId, StatusCodes::ORDER_DELETED);

            $user = new ClientUser();
            $mUserInfo = $user->getUserInfomation($this->cid);

            $company = new Company();
            $tCompanyInfo = $company->getCompanyInformation($tCompanyId);

            $orderId = $this->genOrderId($this->cid);

            $order = new Order();
            $order->add($orderId, LinkageUtils::ORDER_TYPE_EXPORT, $mUserInfo['company_id'], $tCompanyId, $this->cid, $mUserInfo['realname'], $mUserInfo['mobile'], $takeAddress, $takeTime, $deliveryAddress, $deliveryTime, $isTransferPort, $memo);

            $orderExport = new OrderExport();
            $orderExport->add($orderId, $so, $soImages, $customsIn, $port, $shipCompany, $shipName, $shipSchedule, $isBookCargo);

            $cargoObjs = $this->genCargosObj($cargoStr);
            foreach($cargoObjs as $cargoObj){
                for($i = 0; $i < $cargoObj['num']; $i++){
                    $orderCargo = new OrderCargo();
                    $orderCargo->add($orderId, $cargoObj['type']);
                }
            }

            // Commit the transaction
            $this->db->commit();

            $result = ['order_id' => $orderId,
                'transporter_id' => $tCompanyId,
                'transporter_name' => $tCompanyInfo['name'],
                'order_status' => StatusCodes::ORDER_PLACE,
                'create_time' => time(),
                'process' => 0
            ];

        }catch (Exception $e){
            $this->db->rollback();

            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondArray($result);

    }

    /**
     * @title("place4import")
     * @description("Place import order")
     * @requestExample("POST /order/place4import")
     * @response("Data object or Error object")
     */
    public function mod4importAction(){
        $rejectOrderId = $this->request->getPost('reject_order_id', 'string');
        $rejectOrderStatus = $this->request->getPost('reject_order_status', 'int');
        $tCompanyId = $this->request->getPost('company_id', 'int');
        $cargoStr = $this->request->getPost('cargo', 'string');
        $takeAddress = $this->request->getPost('take_address', 'string');
        $takeTime = $this->request->getPost('take_time', 'int');
        $deliveryAddress = $this->request->getPost('delivery_address', 'string');
        $deliveryTime = $this->request->getPost('delivery_time', 'int');
        $isTransferPort = $this->request->getPost('is_transfer_port', 'int');
        $memo = $this->request->getPost('memo', 'string');
        $rentExpire = $this->request->getPost('cargos_rent_expire', 'int');
        $billNo = $this->request->getPost('bill_no', 'string');
        $cargoNo = $this->request->getPost('cargo_no', 'string');
        $cargoCompany = $this->request->getPost('cargo_company', 'string');
        $customBroker = $this->request->getPost('customs_broker', 'string');
        $customContact = $this->request->getPost('customs_contact', 'string');

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        if(!isset($tCompanyId)){
            return $this->respondError(ErrorCodes::ORDER_TRANSPORTER_NULL, ErrorCodes::$MESSAGE[ErrorCodes::ORDER_TRANSPORTER_NULL]);
        }

        if(!isset($rejectOrderId)){
            return $this->respondError(ErrorCodes::ORDER_ID_NULL, ErrorCodes::$MESSAGE[ErrorCodes::ORDER_ID_NULL]);
        }

        if(!isset($rejectOrderStatus) || $rejectOrderStatus != StatusCodes::ORDER_REJECT){
            return $this->respondError(ErrorCodes::AUTH_UNAUTHORIZED, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_UNAUTHORIZED]);
        }

        try{
            // Start a transaction
            $this->db->begin();

            $order = new Order();
            $order->updateStatus($rejectOrderId, StatusCodes::ORDER_DELETED);

            $user = new ClientUser();
            $mUserInfo = $user->getUserInfomation($this->cid);

            $company = new Company();
            $tCompanyInfo = $company->getCompanyInformation($tCompanyId);

            $orderId = $this->genOrderId($this->cid);

            $order = new Order();
            $order->add($orderId, LinkageUtils::ORDER_TYPE_IMPORT, $mUserInfo['company_id'], $tCompanyId, $this->cid, $mUserInfo['realname'], $mUserInfo['mobile'], $takeAddress, $takeTime, $deliveryAddress, $deliveryTime, $isTransferPort, $memo);

            $orderImport = new OrderImport();
            $orderImport->add($orderId, $rentExpire, $billNo, $cargoNo, $cargoCompany, $customBroker, $customContact);

            $cargoObjs = $this->genCargosObj2($cargoStr);
            foreach($cargoObjs as $cargoObj){
                $orderCargo = new OrderCargo();
                $orderCargo->addWithNo($orderId, $cargoObj['cargono'], $cargoObj['type']);
            }

            // Commit the transaction
            $this->db->commit();

            $result = ['order_id' => $orderId,
                'transporter_id' => $tCompanyId,
                'transporter_name' => $tCompanyInfo['name'],
                'order_status' => StatusCodes::ORDER_PLACE,
                'create_time' => time(),
                'process' => 0
            ];

        }catch (Exception $e){
            $this->db->rollback();

            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondArray($result);

    }

    /**
     * @title("place4self")
     * @description("Place self order")
     * @requestExample("POST /order/place4self")
     * @response("Data object or Error object")
     */
    public function mod4selfAction(){
        $rejectOrderId = $this->request->getPost('reject_order_id', 'string');
        $rejectOrderStatus = $this->request->getPost('reject_order_status', 'int');
        $tCompanyId = $this->request->getPost('company_id', 'int');
        $cargoStr = $this->request->getPost('cargo', 'string');
        $takeAddress = $this->request->getPost('take_address', 'string');
        $takeTime = $this->request->getPost('take_time', 'int');
        $deliveryAddress = $this->request->getPost('delivery_address', 'string');
        $deliveryTime = $this->request->getPost('delivery_time', 'int');
        $isTransferPort = $this->request->getPost('is_transfer_port', 'int');
        $memo = $this->request->getPost('memo', 'string');
        $customsIn = $this->request->getPost('customs_in', 'int');
        $cargoTakeTime = $this->request->getPost('cargo_take_time', 'int');
        $isCustomsDeclare = $this->request->getPost('is_customs_declare', 'int');

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        if(!isset($tCompanyId)){
            return $this->respondError(ErrorCodes::ORDER_TRANSPORTER_NULL, ErrorCodes::$MESSAGE[ErrorCodes::ORDER_TRANSPORTER_NULL]);
        }

        if(!isset($rejectOrderId)){
            return $this->respondError(ErrorCodes::ORDER_ID_NULL, ErrorCodes::$MESSAGE[ErrorCodes::ORDER_ID_NULL]);
        }

        if(!isset($rejectOrderStatus) || $rejectOrderStatus != StatusCodes::ORDER_REJECT){
            return $this->respondError(ErrorCodes::AUTH_UNAUTHORIZED, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_UNAUTHORIZED]);
        }

        try{
            // Start a transaction
            $this->db->begin();

            $order = new Order();
            $order->updateStatus($rejectOrderId, StatusCodes::ORDER_DELETED);

            $user = new ClientUser();
            $mUserInfo = $user->getUserInfomation($this->cid);

            $company = new Company();
            $tCompanyInfo = $company->getCompanyInformation($tCompanyId);

            $orderId = $this->genOrderId($this->cid);

            $order = new Order();
            $order->add($orderId, LinkageUtils::ORDER_TYPE_SELF, $mUserInfo['company_id'], $tCompanyId, $this->cid, $mUserInfo['realname'], $mUserInfo['mobile'], $takeAddress, $takeTime, $deliveryAddress, $deliveryTime, $isTransferPort, $memo);

            $orderSelf = new OrderSelf();
            $orderSelf->add($orderId, $customsIn, $cargoTakeTime, $isCustomsDeclare);

            $cargoObjs = $this->genCargosObj($cargoStr);
            foreach($cargoObjs as $cargoObj){
                for($i = 0; $i < $cargoObj['num']; $i++){
                    $orderCargo = new OrderCargo();
                    $orderCargo->add($orderId, $cargoObj['type']);
                }
            }

            // Commit the transaction
            $this->db->commit();

            $result = ['order_id' => $orderId,
                'transporter_id' => $tCompanyId,
                'transporter_name' => $tCompanyInfo['name'],
                'order_status' => StatusCodes::ORDER_PLACE,
                'create_time' => time(),
                'process' => 0
            ];

        }catch (Exception $e){
            $this->db->rollback();

            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondArray($result);

    }

    /**
     * @title("accept")
     * @description("Accept order")
     * @requestExample("POST /order/accept")
     * @response("Data object or Error object")
     */
    public function acceptAction(){
        $orderId = $this->request->getPost('order_id', 'string');

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        if(empty($orderId)){
            return $this->respondError(ErrorCodes::ORDER_ID_NULL, ErrorCodes::$MESSAGE[ErrorCodes::ORDER_ID_NULL]);
        }

        try {
            $user = new ClientUser();
            $userInfo = $user->getUserInfomation($this->cid);

            if($userInfo['role'] != LinkageUtils::ROLE_ADMIN_TRANSPORTER && $userInfo['role'] != LinkageUtils::ROLE_TRANSPORTER){
                return $this->respondError(ErrorCodes::AUTH_UNAUTHORIZED, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_UNAUTHORIZED]);
            }

            $order = new Order();
            if($order->getStatus($orderId) != StatusCodes::ORDER_PLACE){
                return $this->respondError(ErrorCodes::ORDER_ACCEPT_ERROR, ErrorCodes::$MESSAGE[ErrorCodes::ORDER_ACCEPT_ERROR]);
            }

            $mutex = "LINKAGE_ORDER_APT_".$orderId;
            if($this->redis->setnx($mutex, 'processing')) {
                if($order->getStatus($orderId) != StatusCodes::ORDER_PLACE){
                    $this->redis->delete($mutex);
                    return $this->respondError(ErrorCodes::ORDER_ACCEPT_ERROR, ErrorCodes::$MESSAGE[ErrorCodes::ORDER_ACCEPT_ERROR]);
                }

                $this->redis->setTimeout($mutex, 30);

                $order->accept($orderId, $this->cid, $userInfo['name'], $userInfo['mobile']);
                $this->redis->delete($mutex);
            }else{
                return $this->respondError(ErrorCodes::ORDER_ACCEPT_ERROR, ErrorCodes::$MESSAGE[ErrorCodes::ORDER_ACCEPT_ERROR]);
            }

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondArray(['order_status' => StatusCodes::ORDER_HANDLING]);
    }

    /**
     * @title("confirm")
     * @description("Confirm order")
     * @requestExample("POST /order/confirm")
     * @response("Data object or Error object")
     */
    public function confirmAction(){
        $orderId = $this->request->getPost('order_id', 'string');

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        if(empty($orderId)){
            return $this->respondError(ErrorCodes::ORDER_ID_NULL, ErrorCodes::$MESSAGE[ErrorCodes::ORDER_ID_NULL]);
        }

        try {
            $order = new Order();
            $order->updateStatus($orderId, StatusCodes::ORDER_HANDLED);

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondArray(['order_status' => StatusCodes::ORDER_HANDLED]);
    }

    /**
     * @title("cancel")
     * @description("Cancel order")
     * @requestExample("POST /order/cancel")
     * @response("Data object or Error object")
     */
    public function cancelAction(){
        $orderId = $this->request->getPost('order_id', 'string');

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        if(empty($orderId)){
            return $this->respondError(ErrorCodes::ORDER_ID_NULL, ErrorCodes::$MESSAGE[ErrorCodes::ORDER_ID_NULL]);
        }

        try {
            $order = new Order();
            $order->updateStatus($orderId, StatusCodes::ORDER_CANCEL);

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondArray(['order_status' => StatusCodes::ORDER_CANCEL]);

    }

    /**
     * @title("reject")
     * @description("Reject order")
     * @requestExample("POST /order/reject")
     * @response("Data object or Error object")
     */
    public function rejectAction(){
        $orderId = $this->request->getPost('order_id', 'string');

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        if(empty($orderId)){
            return $this->respondError(ErrorCodes::ORDER_ID_NULL, ErrorCodes::$MESSAGE[ErrorCodes::ORDER_ID_NULL]);
        }

        try {
            $order = new Order();
            $order->updateStatus($orderId, StatusCodes::ORDER_REJECT);

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondArray(['order_status' => StatusCodes::ORDER_REJECT]);

    }

    /**
     * @title("listbystatus")
     * @description("List orders by status")
     * @requestExample("POST /order/listbystatus")
     * @response("Data object or Error object")
     */
    public function listByStatusAction(){
        $type = $this->request->getPost('type', 'int');
        $status = $this->request->getPost('status', 'int');
        $pagination = $this->request->getPost('pagination', 'int');
        $offset = $this->request->getPost('offset', 'int');
        $size = $this->request->getPost('size', 'int');

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        try {
            $user = new  ClientUser();
            $role = $user->getRoleId($this->cid);
            $isManufacture = ($role == LinkageUtils::USER_ADMIN_MANUFACTURE || $role == LinkageUtils::USER_MANUFACTURE) ? true : false;

            $order = new Order();
            if($isManufacture){
                $orders = $order->getOrders4Manufacture($this->cid, $type, $status, $pagination, $offset, $size);
            }else{
                $info = $user->getUserInfomation($this->cid);
                $orders = $order->getOrders4Transporter($this->cid, $info['company_id'], $type, $status, $pagination, $offset, $size);
            }

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondArray(['orders' => $orders]);

    }

    /**
     * @title("detail4export")
     * @description("Export order detail")
     * @requestExample("POST /order/detail4export")
     * @response("Data object or Error object")
     */
    public function detail4exportAction(){
        $orderId = $this->request->getPost('order_id', 'string');

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        if(empty($orderId)){
            return $this->respondError(ErrorCodes::ORDER_ID_NULL, ErrorCodes::$MESSAGE[ErrorCodes::ORDER_ID_NULL]);
        }

        try {
            $user = new  ClientUser();
            $role = $user->getRoleId($this->cid);
            $isManufacture = ($role == LinkageUtils::USER_ADMIN_MANUFACTURE || $role == LinkageUtils::USER_MANUFACTURE) ? true : false;

            //Get Order Detail
            $orderExport = new OrderExport();
            if($isManufacture){
                $orderDetail = $orderExport->getDetail4Manufacture($orderId);
            }else{
                $orderDetail = $orderExport->getDetail4Transporter($orderId);
            }

            //Get Cargos Information
            $orderCargo = new OrderCargo();
            $cargos = $orderCargo->getCargosByOrderId($orderId);
            $orderDetail['cargos'] = $cargos;

            //Get Driver Tasks
            $driverTask = new DriverTask();
            $tasks = $driverTask->getTaskByOrderId($orderId);
            $orderDetail['tasks'] = $tasks;

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondArray($orderDetail);

    }

    /**
     * @title("detail4import")
     * @description("Import order detail")
     * @requestExample("POST /order/detail4import")
     * @response("Data object or Error object")
     */
    public function detail4importAction(){
        $orderId = $this->request->getPost('order_id', 'string');

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        if(empty($orderId)){
            return $this->respondError(ErrorCodes::ORDER_ID_NULL, ErrorCodes::$MESSAGE[ErrorCodes::ORDER_ID_NULL]);
        }

        try {
            $user = new  ClientUser();
            $role = $user->getRoleId($this->cid);
            $isManufacture = ($role == LinkageUtils::USER_ADMIN_MANUFACTURE || $role == LinkageUtils::USER_MANUFACTURE) ? true : false;

            //Get Order Detail
            $orderImport = new OrderImport();
            if($isManufacture){
                $orderDetail = $orderImport->getDetail4Manufacture($orderId);
            }else{
                $orderDetail = $orderImport->getDetail4Transporter($orderId);
            }

            //Get Cargos Information
            $orderCargo = new OrderCargo();
            $cargos = $orderCargo->getCargosByOrderIdWithNo($orderId);
            $orderDetail['cargos'] = $cargos;

            //Get Driver Tasks
            $driverTask = new DriverTask();
            $tasks = $driverTask->getTaskByOrderId($orderId);
            $orderDetail['tasks'] = $tasks;

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondArray($orderDetail);

    }

    /**
     * @title("detail4self")
     * @description("Self order detail")
     * @requestExample("POST /order/detail4self")
     * @response("Data object or Error object")
     */
    public function detail4selfAction(){
        $orderId = $this->request->getPost('order_id', 'string');

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        if(empty($orderId)){
            return $this->respondError(ErrorCodes::ORDER_ID_NULL, ErrorCodes::$MESSAGE[ErrorCodes::ORDER_ID_NULL]);
        }

        try {
            $user = new  ClientUser();
            $role = $user->getRoleId($this->cid);
            $isManufacture = ($role == LinkageUtils::USER_ADMIN_MANUFACTURE || $role == LinkageUtils::USER_MANUFACTURE) ? true : false;

            //Get Order Detail
            $orderSelf = new OrderSelf();
            if($isManufacture){
                $orderDetail = $orderSelf->getDetail4Manufacture($orderId);
            }else{
                $orderDetail = $orderSelf->getDetail4Transporter($orderId);
            }

            //Get Cargos Information
            $orderCargo = new OrderCargo();
            $cargos = $orderCargo->getCargosByOrderId($orderId);
            $orderDetail['cargos'] = $cargos;

            //Get Driver Tasks
            $driverTask = new DriverTask();
            $tasks = $driverTask->getTaskByOrderId($orderId);
            $orderDetail['tasks'] = $tasks;

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondArray($orderDetail);

    }

    /**
     * @title("comment")
     * @description("Order comment")
     * @requestExample("POST /order/comment")
     * @response("Data object or Error object")
     */
    public function commentAction(){
        $orderId = $this->request->getPost('order_id', 'string');
        $score = $this->request->getPost('score', 'int');
        $comment = $this->request->getPost('comment', 'string');

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        if(empty($orderId)){
            return $this->respondError(ErrorCodes::ORDER_ID_NULL, ErrorCodes::$MESSAGE[ErrorCodes::ORDER_ID_NULL]);
        }

        try {
            $order = new Order();
            $isOrderExist = $order->isOrderExist($orderId);
            if(!$isOrderExist){
                return $this->respondError(ErrorCodes::ORDER_NOT_FOUND, ErrorCodes::$MESSAGE[ErrorCodes::ORDER_NOT_FOUND]);
            }

            $orderComment = new OrderComment();
            if($orderComment->isCommentExist($orderId)){
                $this->respondError(ErrorCodes::ORDER_COMMENT_DUPLICATED, ErrorCodes::$MESSAGE[ErrorCodes::ORDER_COMMENT_DUPLICATED]);
            }

            $orderComment->add($orderId, $score, $comment);

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondOK();

    }

    private function genOrderId($userid){
        list($tmp1, $tmp2) = explode(' ', microtime());

        $msec =  (String)((int)sprintf('%.0f', (floatval($tmp1) + floatval($tmp2)) * 10000));

        $date = date("Ymd");
        $prefix = "37";
        return $prefix.$userid.$date.substr($msec, 4, 10);
    }

    private function genCargosObj($cargoStr){
        $cargoObjs = [];
        $cargoArrs = explode(';', $cargoStr);

        foreach($cargoArrs as $cargoAttr){
            $cargo = explode(':', $cargoAttr);

            $cargoObj['type'] = $cargo[0];
            $cargoObj['num'] = (int)$cargo[1];

            array_push($cargoObjs, $cargoObj);
        }

        return $cargoObjs;

    }

    private function genCargosObj2($cargoStr){
        $cargoObjs = [];
        $cargoArrs = explode(';', $cargoStr);

        foreach($cargoArrs as $cargoAttr){
            $cargo = explode(':', $cargoAttr);

            $cargoObj['cargono'] = $cargo[0];
            $cargoObj['type'] = $cargo[1];

            array_push($cargoObjs, $cargoObj);
        }

        return $cargoObjs;
    }

}