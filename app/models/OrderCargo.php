<?php
/**
 * Created by PhpStorm.
 * User: uni
 * Date: 16/3/28
 * Time: 下午6:26
 */

namespace Multiple\Models;

use Phalcon\Di;
use Phalcon\Mvc\Model;

use Multiple\Core\Constants\Services;
use Multiple\Core\Constants\ErrorCodes;
use Multiple\Core\Exception\DataBaseException;


class OrderCargo extends Model
{
    public function initialize(){
        $this->setSource("linkage_order_2_cargo");
    }

    public function add($orderId, $cargoType){
        $this->order_id = $orderId;
        $this->cargo_type = $cargoType;

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

    public function addWithNo($orderId, $cargoNo, $cargoType){
        $this->order_id = $orderId;
        $this->cargo_no = $cargoNo;
        $this->cargo_type = $cargoType;

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

    public function getCargosByOrderId($orderId){
        $phql = "select a.cargo_type, COUNT(1) as number FROM Multiple\Models\OrderCargo a where a.order_id='$orderId' GROUP BY a.cargo_type";
        $cargos = $this->modelsManager->executeQuery($phql);

        $results = [];
        foreach ($cargos as $cargo) {
            $result['type'] = $cargo->cargo_type;
            $result['number'] = $cargo->number;

            array_push($results, $result);
        }

        return $results;
    }

    public function getCargosByOrderIdWithNo($orderId){
        $phql = "select a.cargo_type, a.cargo_no FROM Multiple\Models\OrderCargo a where a.order_id='$orderId'";
        $cargos = $this->modelsManager->executeQuery($phql);

        $results = [];
        foreach ($cargos as $cargo) {
            $result['type'] = $cargo->cargo_type;
            $result['cargo_no'] = $cargo->cargo_no;

            array_push($results, $result);
        }

        return $results;
    }

}