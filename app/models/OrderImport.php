<?php
/**
 * Created by PhpStorm.
 * User: uni
 * Date: 16/3/27
 * Time: 下午2:32
 */


namespace Multiple\Models;

use Phalcon\Di;
use Phalcon\Mvc\Model;

use Multiple\Core\Constants\Services;
use Multiple\Core\Constants\ErrorCodes;
use Multiple\Core\Exception\DataBaseException;

class OrderImport extends Model
{
    public function initialize(){
        $this->setSource("linkage_order_import");

    }

    public function add($orderId, $rentExpire, $billNo, $cargoNo, $cargoCompany, $customBroker, $customContact){
        $this->order_id = $orderId;
        $this->cargos_rent_expire = $rentExpire;
        $this->bill_no = $billNo;
        $this->cargo_no = $cargoNo;
        $this->cargo_company = $cargoCompany;
        $this->customs_broker = $customBroker;
        $this->customshouse_contact = $customContact;

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
        $phql="select a.*, b.name as company_name, c.* from Multiple\Models\Order a join Multiple\Models\Company b join Multiple\Models\OrderImport c where a.transporter_id = b.company_id and a.order_id = c.order_id and a.order_id = '".$orderId."'";
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
            'cargo_rent_expire' => $order->cargo_rent_expire,
            'bill_no' => $order->bill_no,
            'cargo_no' => $order->cargo_no,
            'cargo_company' => $order->cargo_company,
            'customs_broker' => $order->customs_broker,
            'customs_contact' => $order->customs_contact,
        ];

        return $orderDetail;
    }

    public function getDetail4Transporter($orderId){
        $phql="select a.*, b.name as company_name, c.* from Multiple\Models\Order a join Multiple\Models\Company b join Multiple\Models\OrderImport c where a.manufacture_id = b.company_id and a.order_id = c.order_id and a.order_id = '".$orderId."'";
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
            'cargo_rent_expire' => $order->cargo_rent_expire,
            'bill_no' => $order->bill_no,
            'cargo_no' => $order->cargo_no,
            'cargo_company' => $order->cargo_company,
            'customs_broker' => $order->customs_broker,
            'customs_contact' => $order->customs_contact,
        ];

        return $orderDetail;
    }


}