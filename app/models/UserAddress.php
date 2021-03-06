<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/4/16
 * Time: 4:35 PM
 */

namespace Multiple\Models;

use Phalcon\Di;
use Phalcon\Mvc\Model;

use Multiple\Core\Constants\StatusCodes;
use Multiple\Core\Constants\ErrorCodes;
use Multiple\COre\Constants\Services;
use Multiple\Core\Exception\DataBaseException;
use Multiple\Core\Exception\UserOperationException;

class UserAddress extends Model
{
    public function initialize(){
        $this->setSource("linkage_user_address");
    }

    public function add($userid, $companyId, $title, $address, $memo='')
    {
        $now = time();

        $this->user_id = $userid;
        $this->company_id = $companyId;
        $this->title = $title;
        $this->address = $address;
        $this->memo  = $memo;

        $this->create_time = $now;
        $this->update_time = $now;
        $this->status = StatusCodes::ADDRESS_ACTIVE;

        if ($this->save() == false) {
            $message = '';
            foreach ($this->getMessages() as $msg) {
                $message .= (String)$msg . ',';
            }
            $logger = Di::getDefault()->get(Services::LOGGER);
            $logger->fatal($message);

            throw new DataBaseException(ErrorCodes::DATA_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FAIL]);
        }

        return $this->address_id;
    }

    public function delAddress($userId, $addressId)
    {
        $address = self::findFirst([
            'conditions' => 'user_id = :user_id: AND address_id = :address_id: AND status = :status:',
            'bind' => [
                'user_id' => $userId,
                'address_id' => $addressId,
                'status'=> StatusCodes::ADDRESS_ACTIVE
            ]
        ]);

        if(!isset($address->address_id)){
            throw new UserOperationException(ErrorCodes::USER_ADDRESS_NOT_FOUND, ErrorCodes::$MESSAGE[ErrorCodes::USER_ADDRESS_NOT_FOUND]);
        }

        $now = time();

        $address->update_time = $now;
        $address->status = StatusCodes::ADDRESS_DELETE;

        if ($address->update() == false) {
            $message = '';
            foreach ($address->getMessages() as $msg) {
                $message .= (String)$msg . ',';
            }
            $logger = Di::getDefault()->get(Services::LOGGER);
            $logger->fatal($message);

            throw new DataBaseException(ErrorCodes::DATA_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FAIL]);
        }
    }

    public function updateAddress($userId, $addressId, $title, $modeAddress)
    {
        $address = self::findFirst([
            'conditions' => 'user_id = :user_id: AND address_id = :address_id: AND status = :status:',
            'bind' => [
                'user_id' => $userId,
                'address_id' => $addressId,
                'status'=> StatusCodes::ADDRESS_ACTIVE
            ]
        ]);

        if(!isset($address->address_id)){
            throw new UserOperationException(ErrorCodes::USER_ADDRESS_NOT_FOUND, ErrorCodes::$MESSAGE[ErrorCodes::USER_ADDRESS_NOT_FOUND]);
        }

        if(!empty($title)){
            $address->title = $title;
        }

        if(!empty($modeAddress)){
            $address->address = $modeAddress;
        }

        $now = time();

        $address->update_time = $now;

        if ($address->update() == false) {
            $message = '';
            foreach ($address->getMessages() as $msg) {
                $message .= (String)$msg . ',';
            }
            $logger = Di::getDefault()->get(Services::LOGGER);
            $logger->fatal($message);

            throw new DataBaseException(ErrorCodes::DATA_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FAIL]);
        }
    }

    public function getList($companyId, $pagination, $offset, $size)
    {
        if($pagination){
            $condition = [
                'conditions' => 'company_id = :company_id: AND status = :status:',
                'bind' => ['company_id' => $companyId,
                    'status'=> StatusCodes::ADDRESS_ACTIVE],
                'offset' => $offset,
                'limit' => $size,
            ];
        }else{
            $condition = [
                'conditions' => 'company_id = :company_id: AND status = :status:',
                'bind' => ['company_id' => $companyId,
                    'status'=> StatusCodes::ADDRESS_ACTIVE]
            ];
        }

        $addresses = self::find($condition);

        $results = [];
        foreach($addresses as $address){
            $result['address_id'] = $address->address_id;
            $result['title'] = $address->title;
            $result['address'] = $address->address;

            array_push($results, $result);
        }

        return $results;
    }

}