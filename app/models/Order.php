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

}