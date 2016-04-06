<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/2/16
 * Time: 11:18 PM
 */

namespace Multiple\Models;

use Phalcon\Di;
use Phalcon\Mvc\Model;

use Multiple\Core\Constants\Services;
use Multiple\Core\Constants\StatusCodes;
use Multiple\Core\Constants\ErrorCodes;
use Multiple\Core\Exception\DataBaseException;
use Multiple\Core\Exception\UserOperationException;


class Order extends Model
{
    public function initialize(){
        $this->setSource("linkage_order");

    }

    public function add($orderId, $type, $manufactureId, $transporterId, $mContactId, $mContactName, $mContactTel, $takeAddr, $takeTime, $deliveryAddr, $deliveryTime, $isTranPort, $memo){
        $now = time();

        $this->order_id = $orderId;
        $this->type = $type;
        $this->manufacture_id = $manufactureId;
        $this->transporter_id = $transporterId;
        $this->manufacture_contact_id = $mContactId;
        $this->manufacture_contact_name = $mContactName;
        $this->manufacture_contact_tel = $mContactTel;
        $this->take_address = $takeAddr;
        $this->take_time = $takeTime;
        $this->delivery_address = $deliveryAddr;
        $this->delivery_time = $deliveryTime;
        $this->is_transfer_port = $isTranPort;
        $this->memo = $memo;

        $this->create_time = $now;
        $this->update_time = $now;
        $this->status = StatusCodes::ORDER_PLACE;

        if($this->save() == false){
            $message = '';
            foreach ($this->getMessages() as $msg) {
                $message .= (String)$msg . ",";
            }
            $logger = Di::getDefault()->get(Services::LOGGER);
            $logger->fatal($message);

            throw new DataBaseException(ErrorCodes::DATA_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FAIL]);
        }
    }

    public function updateStatus($orderId, $status){
        $order = self::findFirst([
            'conditions' => 'order_id = :order_id:',
            'bind' => ['order_id' => $orderId]
        ]);

        if(!isset($order->order_id)){
            throw new UserOperationException(ErrorCodes::ORDER_NOT_FOUND, ErrorCodes::$MESSAGE[ErrorCodes::ORDER_NOT_FOUND]);
        }

        $this->update_time = time();
        $this->status = $status;

        if($this->update() == false){
            $message = '';
            foreach ($this->getMessages() as $msg) {
                $message .= (String)$msg . ",";
            }
            $logger = Di::getDefault()->get(Services::LOGGER);
            $logger->fatal($message);

            throw new DataBaseException(ErrorCodes::DATA_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FAIL]);
        }
    }

    public function getOrders4Manufacture($userid, $type = -1, $status, $pagination = 0,  $offset = 0, $size = 10){
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

        $phql="select a.order_id, a.manufacture_id, a.create_time, a.update_time, a.status, b.name as company_name from Multiple\Models\Order a join Multiple\Models\Company b where a.manufacture_id = b.company_id ".$condition.$limit;
        $lists = $this->modelsManager->executeQuery($phql);

        $orders = [];
        foreach($lists as $list){
            $order = [
                'order_id' => $list->order_id,
                'type' => 0,
                'status' => $list->status,
                'company_id' => $list->manufacture_id,
                'company_name' => $list->company_name,
                'create_time' => $list->create_time,
                'update_time' => $list->update_time,

            ];

            array_push($orders, $order);
        }

        return $orders;
    }

    public function getOrders4Transporter($userid, $type = -1, $status, $pagination = 0,  $offset = 0, $size = 10){
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

        $phql="select a.order_id, a.transporter_id, a.create_time, a.update_time, a.status, b.name as company_name from Multiple\Models\Order a join Multiple\Models\Company b where a.transporter_id = b.company_id ".$condition.$limit;
        $lists = $this->modelsManager->executeQuery($phql);

        $orders = [];
        foreach($lists as $list){
            $order = [
                'order_id' => $list->order_id,
                'type' => 0,
                'status' => $list->status,
                'company_id' => $list->transporter_id,
                'company_name' => $list->company_name,
                'create_time' => $list->create_time,
                'update_time' => $list->update_time,

            ];

            array_push($orders, $order);
        }

        return $orders;
    }

}