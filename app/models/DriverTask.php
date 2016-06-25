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
use Multiple\Core\Exception\UserOperationException;


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
        $phql="select a.cargo_no, a.cargo_type, a.driver_id, a.status, b.name, c.license from Multiple\Models\DriverTask a join Multiple\Models\ClientUser b join Multiple\Models\Driver c on a.driver_id = b.user_id and a.driver_id = c.driver_id where a.order_id = '".$orderId."'";
        $tasks = $this->modelsManager->executeQuery($phql);

        $results = [];
        foreach ($tasks as $task) {
            $result['cargo_no'] = $task->cargo_no;
            $result['cargo_type'] = $task->cargo_type;
            $result['driver_id'] = $task->driver_id;
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

    public function updateTaskStatus($taskId, $status){
        $task = self::findFirst([
            'conditions' => 'task_id = :task_id:',
            'bind' => ['task_id' => $taskId]
        ]);

        if(!isset($task->task_id)){
            throw new UserOperationException(ErrorCodes::ORDER_TASK_NOT_FOUND, ErrorCodes::$MESSAGE[ErrorCodes::ORDER_TASK_NOT_FOUND]);
        }

        $task->status = $status;
        $task->update_time = time();

        if($task->update() == false){
            $message = '';
            foreach ($task->getMessages() as $msg) {
                $message .= (String)$msg . ',';
            }
            $logger = Di::getDefault()->get(Services::LOGGER);
            $logger->fatal($message);

            throw new DataBaseException(ErrorCodes::DATA_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FAIL]);
        }
    }

}