<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 24/4/16
 * Time: 9:18 PM
 */


namespace Multiple\Models;

use Phalcon\Di;
use Phalcon\Mvc\Model;

use Multiple\Core\Constants\StatusCodes;
use Multiple\Core\Constants\ErrorCodes;
use Multiple\COre\Constants\Services;
use Multiple\Core\Exception\DataBaseException;

class SystemSet extends Model
{
    public function initialize()
    {
        $this->setSource("linkage_user_sys_set");
    }

    public function init($userid)
    {
        $now = time();

        $this->user_id = $userid;
        $this->receive_sms = StatusCodes::RECEIVE_SMS;
        $this->receive_email = StatusCodes::RECEIVE_EMAIL;

        $this->create_time = $now;
        $this->update_time = $now;

        if ($this->save() == false) {
            $message = '';
            foreach ($this->getMessages() as $msg) {
                $message .= (String)$msg . ',';
            }
            $logger = Di::getDefault()->get(Services::LOGGER);
            $logger->fatal($message);

            throw new DataBaseException(ErrorCodes::DATA_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FAIL]);
        }
    }

    public function set($userid, $isReceiveSms, $isReceiveEmail)
    {
        $now = time();

        $this->user_id = $userid;
        $this->receive_sms = $isReceiveSms;
        $this->receive_email = $isReceiveEmail;

        //$this->create_time = $now;
        $this->update_time = $now;

        if ($this->save() == false) {
            $message = '';
            foreach ($this->getMessages() as $msg) {
                $message .= (String)$msg . ',';
            }
            $logger = Di::getDefault()->get(Services::LOGGER);
            $logger->fatal($message);

            throw new DataBaseException(ErrorCodes::DATA_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FAIL]);
        }
    }

    public function getSettingById($userid){
        $setting = self::findFirst([
            'conditions' => 'user_id = :userid:',
            'bind' => ['userid' => $userid]
        ]);

        if(!isset($setting->user_id)){
            throw new DataBaseException(ErrorCodes::DATA_FIND_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FIND_FAIL]);
        }

        $result['receive_sms'] = $setting->receive_sms;
        $result['receive_email'] = $setting->receive_email;

        return $result;

    }
}