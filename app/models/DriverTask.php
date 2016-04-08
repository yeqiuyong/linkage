<?php
/**
 * Created by PhpStorm.
 * User: uni
 * Date: 16/3/28
 * Time: 下午6:24
 */


namespace Multiple\Models;

use Phalcon\Di;
use Phalcon\Mvc\Model;

use Multiple\Core\Constants\Services;
use Multiple\Core\Constants\StatusCodes;
use Multiple\Core\Constants\ErrorCodes;
use Multiple\Core\Exception\DataBaseException;


class DriverTask extends Model
{
    public function initialize(){
        $this->setSource("linkage_driver_task");

    }

    public function add($orderId, $orderType, $companyId, $driverId, $carId, $cargoNo, $cargoType){
        $now = time();

        $this->order_id = $orderId;
        $this->order_type = $orderType;
        $this->company_id = $companyId;
        $this->driver_id = $driverId;
        $this->car_id = $carId;
        $this->cargo_no = $cargoNo;
        $this->cargo_type = $cargoType;

        $this->create_time = $now;
        $this->update_time = $now;
        $this->status = StatusCodes::TASK_RECEIPT;

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

    public function getTaskByOrderId($orderId){
        $phql="select a.*, b.* from Multiple\Models\DriverTask a left join Multiple\Models\ClientUser b where a.driver_id = b.user_id and a.order_id = '".$orderId."'";
        $tasks = $this->modelsManager->executeQuery($phql);

        $results = [];
        foreach ($tasks as $task) {
            $result['cargo_no'] = $task->cargo_no;
            $result['driver_name'] = $task->name;
            $result['license'] = $task->license;
            $result['status'] = $task->status;

            array_push($results, $result);
        }

        return $results;
    }

    public function getOrderNumber($companyId){
        $ordersCounts = self::count([
            'column' => 'task_id',
            'group' => 'driver_id',
            'conditions' => 'company_id = :company_id:',
            'bind' => ['company_id' => $companyId]
        ]);

        $results = [];
        foreach ($ordersCounts as $ordersCount) {
            $result['driver_id'] = $ordersCount->driver_id;
            $result['order_num'] = $ordersCount->rowcount;

            array_push($results, $result);
        }

        return $results;
    }

}