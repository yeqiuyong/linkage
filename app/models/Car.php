<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/2/16
 * Time: 11:12 PM
 */

namespace Multiple\Models;

use Multiple\Core\Constants\StatusCodes;
use Phalcon\Di;
use Phalcon\Mvc\Model;

use Multiple\Core\Constants\Services;
use Multiple\Core\Constants\ErrorCodes;
use Multiple\Core\Exception\DataBaseException;
use Multiple\Core\Exception\UserOperationException;

class Car extends Model
{
    public function initialize(){
        $this->setSource("linkage_car");
    }

    public function add($companyId, $license, $engineNo, $frameNo, $applyDate, $examineDate, $maintainDate, $trafficInsureDate, $businessInsureDate, $insureCompany, $memo){
        $now = time();

        $this->company_id = $companyId;
        $this->license = $license;
        $this->engine_no = $engineNo;
        $this->frame_no = $frameNo;
        $this->apply_date = $applyDate;
        $this->examine_date = $examineDate;
        $this->maintain_date = $maintainDate;
        $this->traffic_insure_date = $trafficInsureDate;
        $this->business_insure_date = $businessInsureDate;
        $this->insure_company = $insureCompany;
        $this->memo = $memo;

        $this->create_time = $now;
        $this->update_time = $now;
        $this->status = StatusCodes::CAR_ACTIVE;

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

    public function modify($carId,  $applyDate, $examineDate, $maintainDate, $trafficInsureDate, $businessInsureDate, $insureCompany, $memo){
        $car = self::findFirst([
            'conditions' => 'car_id = :car_id: and status = :status:',
            'bind' => ['car_id' => $carId,
                'status' => StatusCodes::CAR_ACTIVE]
        ]);

        if(!isset($car->car_id)){
            throw new UserOperationException(ErrorCodes::USER_CAR_NOT_FOUND, ErrorCodes::$MESSAGE[ErrorCodes::USER_CAR_NOT_FOUND]);
        }

        if(!empty($applyDate)){
            $car->apply_date = $applyDate;
        }

        if(!empty($examineDate)){
            $car->examine_date = $examineDate;
        }

        if(!empty($maintainDate)){
            $car->maintain_date = $maintainDate;
        }

        if(!empty($trafficInsureDate)){
            $car->traffic_insure_date = $trafficInsureDate;
        }

        if(!empty($businessInsureDate)){
            $car->business_insure_date = $businessInsureDate;
        }

        if(!empty($insureCompany)){
            $car->insure_company = $insureCompany;
        }

        if(!empty($memo)){
            $car->memo = $memo;
        }

        $car->update_time = time();

        if($car->save() == false){
            $message = '';
            foreach ($this->getMessages() as $msg) {
                $message .= (String)$msg . ",";
            }
            $logger = Di::getDefault()->get(Services::LOGGER);
            $logger->fatal($message);

            throw new DataBaseException(ErrorCodes::DATA_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FAIL]);
        }
    }

    public function getCarsByCompanyId($companyId){
        $cars = self::find([
            'conditions' => 'company_id = :company_id: and status = :status:',
            'bind' => ['company_id' => $companyId,
                'status' => StatusCodes::CAR_ACTIVE
            ]
        ]);

        $results = [];
        foreach ($cars as $car) {
            $result['car_id'] = $car->car_id;
            $result['license'] = $car->license;

            array_push($results, $result);
        }

        return $results;

    }

    public function getCarDetail($carId){
        $car = self::findFirst([
            'conditions' => 'car_id = :car_id:',
            'bind' => ['car_id' => $carId]
        ]);

        if(!isset($car->car_id)){
            throw new UserOperationException(ErrorCodes::USER_CAR_NOT_FOUND, ErrorCodes::$MESSAGE[ErrorCodes::USER_CAR_NOT_FOUND]);
        }

        return [
            'license' => $car->license,
            'engine_no' => $car->engine_no,
            'frame_no' => $car->frame_no,
            'apply_date' => $car->apply_date,
            'examine_date' => $car->examine_date,
            'maintain_date' => $car->maintain_date,
            'traffic_insure_date' => $car->traffic_insure_date,
            'business_insure_date' => $car->business_insure_date,
            'insure_company' => $car->insure_company,
            'memo' => $car->memo,

        ];
    }

    public function updateStatus($carId, $status){
        $car = self::findFirst([
            'conditions' => 'car_id = :car_id:',
            'bind' => ['car_id' => $carId]
        ]);

        if(!isset($car->car_id)){
            throw new UserOperationException(ErrorCodes::USER_CAR_NOT_FOUND, ErrorCodes::$MESSAGE[ErrorCodes::USER_CAR_NOT_FOUND]);
        }

        $car->status = $status;
        $car->update_time = time();

        if($car->update() == false){
            $message = '';
            foreach ($car->getMessages() as $msg) {
                $message .= (String)$msg. ',';
            }
            $logger = Di::getDefault()->get(Services::LOGGER);
            $logger->fatal($message);

            throw new DataBaseException(ErrorCodes::DATA_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FAIL]);
        }
    }

}