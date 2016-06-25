<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 26/6/16
 * Time: 12:19 AM
 */


namespace Multiple\Models;

use Phalcon\Di;
use Phalcon\Mvc\Model;

use Multiple\Core\Constants\Services;
use Multiple\Core\Constants\ErrorCodes;
use Multiple\Core\Exception\DataBaseException;


class DriverTaskHistory extends Model
{
    public function initialize()
    {
        $this->setSource("linkage_driver_task_history");
    }

    public function add($taskId, $status, $memo, $image){
        $now = time();

        $this->task_id = $taskId;
        $this->status = $status;

        if(isset($memo)){
            $this->memo = $memo;
        }

        if(isset($image)){
            $this->image = $image;
        }

        $this->create_time = $now;
        $this->update_time = $now;

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