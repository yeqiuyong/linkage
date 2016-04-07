<?php
/**
 * Created by PhpStorm.
 * User: uni
 * Date: 16/4/5
 * Time: 下午11:01
 */


namespace Multiple\Models;

use Phalcon\Di;
use Phalcon\Mvc\Model;

use Multiple\Core\Constants\Services;
use Multiple\Core\Constants\StatusCodes;
use Multiple\Core\Constants\ErrorCodes;
use Multiple\Core\Exception\DataBaseException;
use Multiple\Core\Exception\UserOperationException;


class Driver extends Model
{
    public function initialize(){
        $this->setSource("linkage_driver");

        $this->hasOne('driver_id', 'Multiple\Models\ClientUser', 'user_id', array(  'alias' => 'detail',
            'reusable' => true ));
    }

    public function add($driver_id, $license){
        $this->driver_id = $driver_id;
        $this->license = $license;

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

    public function getDriversByCompanyId($companyId){
        $phql="select a.user_id, a.name, a.mobile, a.icon, b.license from Multiple\Models\ClientUser a join Multiple\Models\Driver b where a.user_id = b.driver_id and a.company_id = $companyId and a.status =".StatusCodes::CLIENT_USER_ACTIVE;
        $drivers = $this->modelsManager->executeQuery($phql);

        $results = [];
        foreach ($drivers as $driver) {
            $result['driver_id'] = $driver->user_id;
            $result['driver_name'] = $driver->name;
            $result['driver_mobile'] = $driver->mobile;
            $result['driver_icon'] = $driver->icon;
            $result['license'] = $driver->license;

            array_push($results, $result);
        }

        return $results;
    }

    public function getDriverDetail($driverId){
        $phql="select a.user_id, a.name, a.mobile, a.gender, a.icon, b.license from Multiple\Models\ClientUser a join Multiple\Models\Driver b where a.user_id = b.driver_id and b.driver_id = $driverId and a.status =".StatusCodes::CLIENT_USER_ACTIVE;
        $driver = $this->modelsManager->executeQuery($phql);

        if(sizeof($driver) == 0){
            throw new UserOperationException(ErrorCodes::USER_NOTFOUND, ErrorCodes::$MESSAGE[ErrorCodes::USER_NOTFOUND]);
        }

        $result['driver_id'] = $driver[0]->user_id;
        $result['driver_name'] = $driver[0]->name;
        $result['driver_mobile'] = $driver[0]->mobile;
        $result['gender'] = $driver[0]->gender;
        $result['driver_icon'] = $driver[0]->icon;
        $result['license'] = $driver[0]->license;

        return $result;
    }

}