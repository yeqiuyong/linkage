<?php
/**
 * Created by PhpStorm.
 * User: uni
 * Date: 16/3/27
 * Time: 下午2:33
 */


namespace Multiple\Models;

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
        $phql="select c.* from Multiple\Models\Order a left join Multiple\Models\OrderExport c where a.order_id = c.order_id and a.order_id = $orderId";
        $order = $this->modelsManager->executeQuery($phql);

        $orderDetail = [
            'company_id' => $order[0]->transporter_id,
            'company_name' => $order->company_name,
            'take_address' => $order->take_address,
            'take_time' => $order->take_time,
            'delivery_address' => $order->delivery_address,
            'delivery_time' => $order->delivery_time,
            'is_transfer_port' => $order->is_transfer_port,
            'memo' => $order->memo,
            'so' => $order[0]->so,
            'so_images' => $order->so_images,
            'ship_company' => $order->ship_company,
            'ship_name' => $order->ship_name,
            'ship_schedule_no' => $order->ship_schedule_no,
            'is_book_cargo' => $order->is_book_cargo,
        ];

        return $orderDetail;
    }

    public function getDetail4Transporter($orderId){
        $phql="select a.*, b.name as company_name, c.* from Multiple\Models\Order a join Multiple\Models\Company b join Multiple\Models\OrderExport c where a.manufacture_id = b.company_id and a.order_id = c.order_id and a.order_id = '".$orderId."'";
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
            'so' => $order->so,
            'so_images' => $order->so_images,
            'ship_company' => $order->ship_company,
            'ship_name' => $order->ship_name,
            'ship_schedule_no' => $order->ship_schedule_no,
            'is_book_cargo' => $order->is_book_cargo,
        ];

        return $orderDetail;
    }

}