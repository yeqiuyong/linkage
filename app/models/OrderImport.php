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

}