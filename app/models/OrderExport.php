<?php
/**
 * Created by PhpStorm.
 * User: uni
 * Date: 16/3/27
 * Time: 下午2:33
 */


namespace Multiple\Models;

use Multiple\Core\Exception\Exception;
use Multiple\Core\Exception\UserOperationException;
use Phalcon\Di;
use Phalcon\Mvc\Model;

use Multiple\Core\Constants\Services;
use Multiple\Core\Constants\ErrorCodes;
use Multiple\Core\Exception\DataBaseException;


class OrderExport extends Model
{
    public function initialize(){
        $this->setSource("linkage_order_export");
    }

    public function add($orderId, $so, $soImages, $customsIn, $port, $shipCompany, $shipName, $shipSchedule, $isBookCargo){
        $this->order_id = $orderId;
        $this->so = $so;
        $this->so_images = $soImages;
        $this->customs_in = $customsIn;
        $this->port = $port;
        $this->ship_company = $shipCompany;
        $this->ship_name = $shipName;
        $this->ship_schedule_no = $shipSchedule;
        $this->is_book_cargo = $isBookCargo;

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
        $phql="select a.transporter_id, a.take_address, a.take_time, a.delivery_address, a.delivery_time, a.is_transfer_port, a.memo, b.name as company_name, c.so, c.so_images, c.ship_company, c.ship_name, c.ship_schedule_no, c.is_book_cargo from Multiple\Models\Order a join Multiple\Models\Company b join Multiple\Models\OrderExport c where a.transporter_id = b.company_id and a.order_id = c.order_id and a.order_id = '$orderId'";
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
            'so' => $order[0]->so,
            'so_images' => $order[0]->so_images,
            'ship_company' => $order[0]->ship_company,
            'ship_name' => $order[0]->ship_name,
            'ship_schedule_no' => $order[0]->ship_schedule_no,
            'is_book_cargo' => $order[0]->is_book_cargo,
        ];

        return $orderDetail;
    }

    public function getDetail4Transporter($orderId){
        $phql="select a.manufacture_id, a.take_address, a.take_time, a.delivery_address, a.delivery_time, a.is_transfer_port, a.memo, b.name as company_name, c.so, c.so_images, c.ship_company, c.ship_name, c.ship_schedule_no, c.is_book_cargo from Multiple\Models\Order a join Multiple\Models\Company b join Multiple\Models\OrderExport c where a.manufacture_id = b.company_id and a.order_id = c.order_id and a.order_id = '$orderId'";
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
            'so' => $order[0]->so,
            'so_images' => $order[0]->so_images,
            'ship_company' => $order[0]->ship_company,
            'ship_name' => $order[0]->ship_name,
            'ship_schedule_no' => $order[0]->ship_schedule_no,
            'is_book_cargo' => $order[0]->is_book_cargo,
        ];

        return $orderDetail;
    }

}