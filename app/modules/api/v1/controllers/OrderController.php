<?php
/**
 * Created by PhpStorm.
 * User: uni
 * Date: 16/3/27
 * Time: 下午2:39
 */


namespace Multiple\API\Controllers;

use Multiple\Models\Company;
use Phalcon\Di;

use Multiple\Core\Exception\Exception;
use Multiple\Core\APIControllerBase;
use Multiple\Core\Constants\LinkageUtils;
use Multiple\Core\Constants\Services;
use Multiple\Core\Constants\ErrorCodes;
use Multiple\Core\Constants\StatusCodes;

use Multiple\Models\Notice;
use Multiple\Models\ClientUser;
use Multiple\Models\Order;
use Multiple\Models\OrderExport;
use Multiple\Models\OrderImport;
use Multiple\Models\OrderSelf;
use Multiple\Models\OrderCargo;


/**
 * @resource("User")
 */
class OrderController extends APIControllerBase
{

    private $logger;

    public function initialize()
    {
        parent::initialize();

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
            $mInfo = $user->getUserInfomation($this->cid);

            $company = new Company();
            $tCompanyInfo = $company->getCompanyInformation($tCompanyId);

            $orderId = $this->genOrderId($this->cid);

            $order = new Order();
            $order->add($orderId, StatusCodes::ORDER_PLACE, $mInfo['company_id'], $tCompanyId, $this->cid, $mInfo['realname'], $mInfo['mobile'], $takeAddress, $takeTime, $deliveryAddress, $deliveryTime, $isTransferPort, $memo);

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
        $cargoNo = $this->request->getPost('cargo_no', 'string');
        $cargoCompany = $this->request->getPost('cargo_company', 'string');
        $customBroker = $this->request->getPost('custom_broker', 'string');
        $customContact = $this->request->getPost('custom_contact', 'string');

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
            $mInfo = $user->getUserInfomation($this->cid);

            $company = new Company();
            $tCompanyInfo = $company->getCompanyInformation($tCompanyId);

            $orderId = $this->genOrderId($this->cid);

            $order = new Order();
            $order->add($orderId, StatusCodes::ORDER_PLACE, $mInfo['company_id'], $tCompanyId, $this->cid, $mInfo['realname'], $mInfo['mobile'], $takeAddress, $takeTime, $deliveryAddress, $deliveryTime, $isTransferPort, $memo);

            $orderImport = new OrderImport();
            $orderImport->add($orderId, $rentExpire, $billNo, $cargoNo, $cargoCompany, $customBroker, $customContact);

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
            $mInfo = $user->getUserInfomation($this->cid);

            $company = new Company();
            $tCompanyInfo = $company->getCompanyInformation($tCompanyId);

            $orderId = $this->genOrderId($this->cid);

            $order = new Order();
            $order->add($orderId, StatusCodes::ORDER_PLACE, $mInfo['company_id'], $tCompanyId, $this->cid, $mInfo['realname'], $mInfo['mobile'], $takeAddress, $takeTime, $deliveryAddress, $deliveryTime, $isTransferPort, $memo);

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
            $mInfo = $user->getUserInfomation($this->cid);

            $company = new Company();
            $tCompanyInfo = $company->getCompanyInformation($tCompanyId);

            $orderId = $this->genOrderId($this->cid);

            $order = new Order();
            $order->add($orderId, StatusCodes::ORDER_PLACE, $mInfo['company_id'], $tCompanyId, $this->cid, $mInfo['realname'], $mInfo['mobile'], $takeAddress, $takeTime, $deliveryAddress, $deliveryTime, $isTransferPort, $memo);

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
        $customBroker = $this->request->getPost('custom_broker', 'string');
        $customContact = $this->request->getPost('custom_contact', 'string');

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
            $mInfo = $user->getUserInfomation($this->cid);

            $company = new Company();
            $tCompanyInfo = $company->getCompanyInformation($tCompanyId);

            $orderId = $this->genOrderId($this->cid);

            $order = new Order();
            $order->add($orderId, StatusCodes::ORDER_PLACE, $mInfo['company_id'], $tCompanyId, $this->cid, $mInfo['realname'], $mInfo['mobile'], $takeAddress, $takeTime, $deliveryAddress, $deliveryTime, $isTransferPort, $memo);

            $orderImport = new OrderImport();
            $orderImport->add($orderId, $rentExpire, $billNo, $cargoNo, $cargoCompany, $customBroker, $customContact);

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
            $mInfo = $user->getUserInfomation($this->cid);

            $company = new Company();
            $tCompanyInfo = $company->getCompanyInformation($tCompanyId);

            $orderId = $this->genOrderId($this->cid);

            $order = new Order();
            $order->add($orderId, StatusCodes::ORDER_PLACE, $mInfo['company_id'], $tCompanyId, $this->cid, $mInfo['realname'], $mInfo['mobile'], $takeAddress, $takeTime, $deliveryAddress, $deliveryTime, $isTransferPort, $memo);

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

        return $this->respondOK();
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

        return $this->respondOK();

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

        return $this->respondOK();


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

            $orders = $this->getOrderList($this->cid, $isManufacture, $type, $status, $pagination, $offset, $size);

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondArray($orders);

    }

    /**
     * @title("detail4export")
     * @description("Export order detail")
     * @requestExample("POST /order/detail4export")
     * @response("Data object or Error object")
     */
    public function detail4exportAction(){


    }

    /**
     * @title("detail4import")
     * @description("Import order detail")
     * @requestExample("POST /order/detail4import")
     * @response("Data object or Error object")
     */
    public function detail4importAction(){


    }

    /**
     * @title("detail4self")
     * @description("Self order detail")
     * @requestExample("POST /order/detail4self")
     * @response("Data object or Error object")
     */
    public function detail4selfAction(){


    }

    /**
     * @title("process")
     * @description("Order process")
     * @requestExample("POST /order/process")
     * @response("Data object or Error object")
     */
    public function processAction(){


    }

    /**
     * @title("comment")
     * @description("Order comment")
     * @requestExample("POST /order/comment")
     * @response("Data object or Error object")
     */
    public function commentAction(){


    }

    private function getOrderList($userid, $isManufacture, $type = -1, $status, $pagination = 0,  $offset = 0, $size = 10){
        if($type == -1){
            $condition = " and a.manufacture_contact_id = $userid ";
        }else{
            $condition = " and a.manufacture_contact_id = $userid and type = $type";
        }

        if($status == 1){
            $condition .= " and status in (0, 1, 2)";
        }else if($status == 2){
            $condition .= " and status in (3, 4)";
        }else{
            $condition .= " and status not in (5)";
        }

        if(!$pagination){
            $limit = " limit $offset, $size";
        }else{
            $limit = "";
        }

        if($isManufacture){
            $phql="select a.order_id, a.manufacture_id as company_id, a.create_time, a.update_time, a.status, b.name as company_name from Multiple\Models\Order a join Multiple\Models\Company b where a.manufacture_id = b.company_id ".$condition.$limit;
        }else{
            $phql="select a.order_id, a.transporter_id as company_id, a.create_time, a.update_time, a.status, b.name as company_name from Multiple\Models\Order a join Multiple\Models\Company b where a.transporter_id = b.company_id ".$condition.$limit;

        }
        $lists = $this->modelsManager->executeQuery($phql);

        $orders = [];
        foreach($lists as $list){
            $order = [
                'order_id' => $list->order_id,
                'type' => 0,
                'status' => $list->status,
                'company_id' => $list->company_id,
                'company_name' => $list->company_name,
                'create_time' => $list->create_time,
                'update_time' => $list->update_time,

            ];

            array_push($orders, $order);
        }

        return $orders;
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

}