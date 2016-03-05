<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 28/1/16
 * Time: 6:02 PM
 */

namespace Multiple\Models;

use Phalcon\Mvc\Model;

use Multiple\Core\Constants\LinkageUtils;
use Multiple\Core\Constants\StatusCodes;
use Multiple\Core\Constants\ErrorCodes;
use Multiple\Core\Exception\DataBaseException;

class Company extends Model
{
    public function initialize(){
        $this->setSource("linkage_company");

        $this->hasMany('company_id', 'Multiple\Models\ClientUser', 'company_id', array(  'alias' => 'users',
            'reusable' => true ));
    }

    public function create($name, $type){
        $now = time();

        $this->name = $name;
        $this->type = $type;
        $this->contactor = '';
        $this->address = '';
        $this->service_phone_1 = '';
        $this->status = StatusCodes::COMPANY_USER_PENDING;

        $this->create_time = $now;
        $this->update_time = $now;
        $this->version = LinkageUtils::APP_VERSION;

        if($this->save() == false){
            $message = '';
            foreach ($this->getMessages() as $msg) {
                $message .= (String)$msg;
            }
            $this->logger->debug($message);

            throw new DataBaseException();
        }
    }

    public function moddify($name){
        /** @var \User $user */
        $company = self::findFirst([
            'conditions' => 'username = :username:',
            'bind' => ['name' => $name]
        ]);


        if($company->update() == false){
            $message = '';
            foreach ($this->getMessages() as $msg) {
                $message .= (String)$msg;
            }
            $this->logger->debug($message);

            throw new DataBaseException();
        }
    }
}
