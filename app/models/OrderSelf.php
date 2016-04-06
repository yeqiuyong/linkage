<?php
/**
 * Created by PhpStorm.
 * User: uni
 * Date: 16/3/27
 * Time: 下午2:34
 */



namespace Multiple\Models;

use Phalcon\Di;
use Phalcon\Mvc\Model;

use Multiple\Core\Constants\Services;
use Multiple\Core\Constants\ErrorCodes;
use Multiple\Core\Exception\DataBaseException;

class OrderSelf extends Model
{
    public function initialize(){
        $this->setSource("linkage_order_self_cargo");

    }

    public function add($orderId, $customsIn, $cargoTakeTime, $isCustomsDeclare){
        $this->order_id = $orderId;
        $this->customs_in = $customsIn;
        $this->cargo_take_time = $cargoTakeTime;
        $this->is_customs_declare = $isCustomsDeclare;

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


    public function getDetail4Manufacture($orderId){
        $phql="select a.*, b.name as company_name, c.* from Multiple\Models\Order a join Multiple\Models\Company b join Multiple\Models\OrderSelf c where a.transporter_id = b.company_id and a.order_id = c.order_id and order_id = '".$orderId."'";
        $order = $this->modelsManager->executeQuery($phql);

        $orderDetail = [
            'company_id' => $order->transporter_id,
            'company_name' => $order->company_name,
            'take_address' => $order->take_address,
            'take_time' => $order->take_time,
            'delivery_address' => $order->delivery_address,
            'delivery_time' => $order->delivery_time,
            'is_transfer_port' => $order->is_transfer_port,
            'memo' => $order->memo,
            'is_customs_declare' => $order->is_customs_declare,
            'customs_in' => $order->customs_in,
            'cargo_take_time' => $order->cargo_take_time,

        ];

        return $orderDetail;
    }

    public function getDetail4Transporter($orderId){
        $phql="select a.*, b.name as company_name, c.* from Multiple\Models\Order a join Multiple\Models\Company b join Multiple\Models\OrderSelf c where a.manufacture_id = b.company_id and a.order_id = c.order_id and order_id = '".$orderId."'";
        $order = $this->modelsManager->executeQuery($phql);

        $orderDetail = [
            'company_id' => $order->manufacture_id,
            'company_name' => $order->company_name,
            'take_address' => $order->take_address,
            'take_time' => $order->take_time,
            'delivery_address' => $order->delivery_address,
            'delivery_time' => $order->delivery_time,
            'is_transfer_port' => $order->is_transfer_port,
            'memo' => $order->memo,
            'is_customs_declare' => $order->is_customs_declare,
            'customs_in' => $order->customs_in,
            'cargo_take_time' => $order->cargo_take_time,

        ];

        return $orderDetail;
    }

}