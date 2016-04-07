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
        $phql="select a.transporter_id, a.take_address, a.take_time, a.delivery_address, a.delivery_time, a.is_transfer_port, a.memo, b.name as company_name, c.cargos_rent_expire, c.bill_no, c.cargo_no, c.cargo_company, c.customs_broker, c.customshouse_contact from Multiple\Models\Order a join Multiple\Models\Company b join Multiple\Models\OrderImport c where a.transporter_id = b.company_id and a.order_id = c.order_id and a.order_id = '$orderId'";
        $order = $this->modelsManager->executeQuery($phql);

        if(sizeof($order) == 0){
            throw new UserOperationException(ErrorCodes::ORDER_NOT_FOUND, ErrorCodes::$MESSAGE[ErrorCodes::ORDER_NOT_FOUND]);
        }

        $orderDetail = [
            'company_id' => $order[0]->transporter_id,
            'company_name' => $order[0]->company_name,
            'take_address' => $order[0]->take_address,
            'take_time' => $order[0]->take_time,
            'delivery_address' => $order[0]->delivery_address,
            'delivery_time' => $order[0]->delivery_time,
            'is_transfer_port' => $order[0]->is_transfer_port,
            'memo' => $order[0]->memo,
            'cargo_rent_expire' => $order[0]->cargos_rent_expire,
            'bill_no' => $order[0]->bill_no,
            'cargo_no' => $order[0]->cargo_no,
            'cargo_company' => $order[0]->cargo_company,
            'customs_broker' => $order[0]->customs_broker,
            'customs_contact' => $order[0]->customshouse_contact,
        ];

        return $orderDetail;
    }

    public function getDetail4Transporter($orderId){
        $phql="select a.manufacture_id, a.take_address, a.take_time, a.delivery_address, a.delivery_time, a.is_transfer_port, a.memo, b.name as company_name, c.cargos_rent_expire, c.bill_no, c.cargo_no, c.cargo_company, c.customs_broker, c.customshouse_contact from Multiple\Models\Order a join Multiple\Models\Company b join Multiple\Models\OrderImport c where a.manufacture_id = b.company_id and a.order_id = c.order_id and a.order_id = '$orderId'";
        $order = $this->modelsManager->executeQuery($phql);

        if(sizeof($order) == 0){
            throw new UserOperationException(ErrorCodes::ORDER_NOT_FOUND, ErrorCodes::$MESSAGE[ErrorCodes::ORDER_NOT_FOUND]);
        }

        $orderDetail = [
            'company_id' => $order[0]->manufacture_id,
            'company_name' => $order[0]->company_name,
            'take_address' => $order[0]->take_address,
            'take_time' => $order[0]->take_time,
            'delivery_address' => $order[0]->delivery_address,
            'delivery_time' => $order[0]->delivery_time,
            'is_transfer_port' => $order[0]->is_transfer_port,
            'memo' => $order[0]->memo,
            'cargo_rent_expire' => $order[0]->cargos_rent_expire,
            'bill_no' => $order[0]->bill_no,
            'cargo_no' => $order[0]->cargo_no,
            'cargo_company' => $order[0]->cargo_company,
            'customs_broker' => $order[0]->customs_broker,
            'customs_contact' => $order[0]->customshouse_contact,
        ];

        return $orderDetail;
    }


}