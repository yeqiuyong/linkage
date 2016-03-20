<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 28/1/16
 * Time: 6:02 PM
 */

namespace Multiple\Models;

use Phalcon\Di;
use Phalcon\Mvc\Model;

use Multiple\Core\Constants\Services;
use Multiple\Core\Constants\LinkageUtils;
use Multiple\Core\Constants\StatusCodes;
use Multiple\Core\Constants\ErrorCodes;
use Multiple\Core\Exception\DataBaseException;
use Multiple\Core\Exception\UserOperationException;

class Company extends Model
{

    public function initialize(){
        $this->setSource("linkage_company");

        $this->hasMany('company_id', 'Multiple\Models\ClientUser', 'company_id', array(  'alias' => 'users',
            'reusable' => true ));

    }

    public function add($name, $type){
        if($this->isCompanyRegistered($name)){
            throw new UserOperationException(ErrorCodes::COMPANY_DEUPLICATE, ErrorCodes::$MESSAGE[ErrorCodes::COMPANY_DEUPLICATE]);
        }

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
            $logger = Di::getDefault()->get(Services::LOGGER);
            $logger->fatal($message);

            throw new DataBaseException(ErrorCodes::DATA_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FAIL]);
        }
    }

    public function modifyNameById($companyId, $name, $info = array() ){
        $company = self::findFirst([
            'conditions' => 'company_id = :companyId:',
            'bind' => ['companyId' => $companyId]
        ]);

        if(!isset($company->company_id)){
            throw new UserOperationException(ErrorCodes::COMPANY_NOTFOUND, ErrorCodes::$MESSAGE[ErrorCodes::COMPANY_NOTFOUND]);
        }

        if(!empty($info['name'])){
            $company->name = $name;
        }

        if(!empty($info['contact_name'])){
            $company->contactor = $info['contact_name'];
        }

        if(!empty($info['address'])){
            $company->address = $info['address'];
        }

        if(!empty($info['email'])){
            $company->email = $info['email'];
        }

        if(!empty($info['home_page'])){
            $company->home_page = $info['home_page'];
        }

        if(!empty($info['description'])){
            $company->description = $info['description'];
        }

        if(!empty($info['phone_1'])){
            $company->service_phone_1 = $info['phone_1'];
        }

        if(!empty($info['phone_2'])){
            $company->service_phone_2 = $info['phone_2'];
        }

        if(!empty($info['phone_3'])){
            $company->service_phone_3 = $info['phone_3'];
        }

        if(!empty($info['phone_4'])){
            $company->service_phone_4 = $info['phone_4'];
        }

        if($company->update() == false){
            $message = '';
            foreach ($company->getMessages() as $msg) {
                $message .= (String)$msg;
            }
            $logger = Di::getDefault()->get(Services::LOGGER);
            $logger->fatal($message);

            throw new DataBaseException(ErrorCodes::DATA_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FAIL]);
        }
    }

    public function modifyById($companyId, $info = array() ){
        $company = self::findFirst([
            'conditions' => 'company_id = :companyId:',
            'bind' => ['companyId' => $companyId]
        ]);

        if(!isset($company->company_id)){
            throw new UserOperationException(ErrorCodes::COMPANY_NOTFOUND, ErrorCodes::$MESSAGE[ErrorCodes::COMPANY_NOTFOUND]);
        }

        if(!empty($info['contact_name'])){
            $company->contactor = $info['contact_name'];
        }

        if(!empty($info['address'])){
            $company->address = $info['address'];
        }

        if(!empty($info['email'])){
            $company->email = $info['email'];
        }

        if(!empty($info['home_page'])){
            $company->home_page = $info['home_page'];
        }

        if(!empty($info['description'])){
            $company->description = $info['description'];
        }

        if(!empty($info['phone_1'])){
            $company->service_phone_1 = $info['phone_1'];
        }

        if(!empty($info['phone_2'])){
            $company->service_phone_2 = $info['phone_2'];
        }

        if(!empty($info['phone_3'])){
            $company->service_phone_3 = $info['phone_3'];
        }

        if(!empty($info['phone_4'])){
            $company->service_phone_4 = $info['phone_4'];
        }

        if($company->update() == false){
            $message = '';
            foreach ($company->getMessages() as $msg) {
                $message .= (String)$msg;
            }
            $logger = Di::getDefault()->get(Services::LOGGER);
            $logger->fatal($message);

            throw new DataBaseException(ErrorCodes::DATA_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FAIL]);
        }
    }

    public function updateIconById($companyId, $logo){
        $company = self::findFirst([
            'conditions' => 'company_id = :companyID:',
            'bind' => ['companyID' => $companyId]
        ]);

        if(!isset($company->company_id)){
            throw new UserOperationException(ErrorCodes::COMPANY_NOTFOUND, ErrorCodes::$MESSAGE[ErrorCodes::COMPANY_NOTFOUND]);
        }

        $company->logo = $logo;
        $company->update_time = time();

        if($company->update() == false){
            $message = '';
            foreach ($company->getMessages() as $msg) {
                $message .= (String)$msg;
            }
            $logger = Di::getDefault()->get(Services::LOGGER);
            $logger->fatal($message);

            throw new DataBaseException(ErrorCodes::DATA_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FAIL]);
        }
    }

    public function getCompanyInformation($companyId){
        $company = self::findFirst([
            'conditions' => 'company_id = :companyID:',
            'bind' => ['companyID' => $companyId]
        ]);

        if(!isset($company->company_id)){
            throw new UserOperationException(ErrorCodes::COMPANY_NOTFOUND, ErrorCodes::$MESSAGE[ErrorCodes::COMPANY_NOTFOUND]);
        }

        return [
            'name' => $company->name,
            'level' => $company->level,
            'credit' => $company->credit,
            'type' => $company->type,
            'contactor' => $company->contactor,
            'address' => $company->address,
            'province' => $company->province,
            'city' => $company->city,
            'email' => $company->email,
            'home_page' => $company->home_page,
            'service_phone_1' => $company->service_phone_1,
            'service_phone_2' => $company->service_phone_2,
            'service_phone_3' => $company->service_phone_3,
            'service_phone_4' => $company->service_phone_4,
            'description' => $company->description,
            'remark' => $company->remark,
            'logo' => $company->logo,
        ];

    }

    public function isCompanyExist($companyID){
        $companies = self::find([
            'conditions' => 'company_id = :companyID:',
            'bind' => ['companyID' => $companyID]
        ]);

        return sizeof($companies) > 0 ? true : false;
    }

    private function isCompanyRegistered($companyName){
        $companies = self::find([
            'conditions' => 'name = :name:',
            'bind' => ['name' => $companyName]
        ]);

        return sizeof($companies) > 0 ? true : false;
    }
}
