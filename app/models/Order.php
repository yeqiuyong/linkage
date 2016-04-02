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


class Order extends Model
{
    public function initialize(){
        $this->setSource("linkage_order");

    }

    public function add($orderId, $type, $manufactureId, $transporterId, $tContactName, $tContactTel, $takeAddr, $takeTime, $deliveryAddr, $deliveryTime, $isTranPort, $memo){
        $now = time();

        $this->order_id = $orderId;
        $this->type = $type;
        $this->manufacture_id = $manufactureId;
        $this->transporter_id = $transporterId;
        $this->transporter_contact_name = $tContactName;
        $this->transporter_contact_tel = $tContactTel;
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

}