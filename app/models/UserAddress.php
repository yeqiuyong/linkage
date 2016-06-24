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

    public function add($userid, $title, $address, $memo='')
    {
        $now = time();

        $this->user_id = $userid;
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
            'conditions' => 'user_id = :user_id: AND address_id = :address_id:',
            'bind' => [
                'user_id' => $userId,
                'address_id' => $addressId,
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

    public function updateAddress($userId, $addressId, $title, $address)
    {
        $address = self::findFirst([
            'conditions' => 'user_id = :user_id: AND address_id = :address_id:',
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
            $this->title = $title;
        }

        if(!empty($address)){
            $this->address = $address;
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

    public function getList($userId, $pagination, $offset, $size)
    {
        if($pagination){
            $condition = [
                'conditions' => 'user_id = :user_id:',
                'bind' => ['user_id' => $userId],
                'offset' => $offset,
                'limit' => $size,
            ];
        }else{
            $condition = [
                'conditions' => 'user_id = :user_id:',
                'bind' => ['user_id' => $userId]
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