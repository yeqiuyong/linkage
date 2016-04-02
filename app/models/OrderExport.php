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
        $this->soImages = $soImages;
        $this->customsIn = $customsIn;
        $this->port = $port;
        $this->shipCompany = $shipCompany;
        $this->shipName = $shipName;
        $this->shipSchedule = $shipSchedule;
        $this->isBookCargo = $isBookCargo;

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