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
use Phalcon\Mvc\Model\Resultset\Simple as Resultset;

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
        $this->status = StatusCodes::COMPANY_PENDING;

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

        if(!empty($info['fax'])){
            $company->fax = $info['fax'];
        }

        if(!empty($info['logo'])){
            $company->logo = $info['logo'];
        }

        if(!empty($info['images'])){
            $company->images = $info['images'];
        }

        if($company->update() == false){
            $message = '';
            foreach ($company->getMessages() as $msg) {
                $message .= (String)$msg . ',';
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

        if(!empty($info['fax'])){
            $company->fax = $info['fax'];
        }

        if(!empty($info['logo'])){
            $company->logo = $info['logo'];
        }

        if(!empty($info['images'])){
            $company->images = $info['images'];
        }

        if($company->update() == false){
            $message = '';
            foreach ($company->getMessages() as $msg) {
                $message .= (String)$msg . ",";
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
                $message .= (String)$msg . ',';
            }
            $logger = Di::getDefault()->get(Services::LOGGER);
            $logger->fatal($message);

            throw new DataBaseException(ErrorCodes::DATA_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FAIL]);
        }
    }

    public function updateStatus($companyId, $status){
        $company = self::findFirst([
            'conditions' => 'company_id = :companyId:',
            'bind' => ['companyId' => $companyId]
        ]);

        if(!isset($company->company_id)){
            throw new UserOperationException(ErrorCodes::COMPANY_NOTFOUND, ErrorCodes::$MESSAGE[ErrorCodes::COMPANY_NOTFOUND]);
        }

        $company->status = $status;
        $company->update_time = time();

        if($company->update() == false){
            $message = '';
            foreach ($company->getMessages() as $msg) {
                $message .= (String)$msg . ',';
            }
            $logger = Di::getDefault()->get(Services::LOGGER);
            $logger->fatal($message);

            throw new DataBaseException(ErrorCodes::DATA_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FAIL]);
        }
    }

    public function updateStaffStatus($companyId, $status){
        $phql="update status from Multiple\Models\ClientUser set status = ".$status." where company_id = ".$companyId;
        $this->modelsManager->executeQuery($phql);
    }

    public function getCompaniesByType($type,$start_time='',$end_time=''){
        if($start_time == '' || $end_time==''){
            $companies = self::find([
                'conditions' => "type = :type: AND status != :status:",
                'bind' => [  "type" => $type,
                    "status" => StatusCodes::COMPANY_DELETED,]
            ]);
        }else{
            $companies = self::find([
                'conditions' => "type = :type: AND status != :status: AND create_time >= :start_time: AND create_time <= :end_time:",
                'bind' => [  "type" => $type,
                    "status" => StatusCodes::COMPANY_DELETED,
                    "start_time" => $start_time,
                    "end_time" => $end_time,
                ]
            ]);
        }

        $results = [];
        foreach ($companies as $company) {
            $result = [];
            $result['id'] = $company->company_id;
            $result['name'] = $company->name;
            $result['contact'] = $company->contactor;
            $result['phone'] = $company->service_phone_1;
            $result['create_time'] = $company->create_time;
            $result['status'] = $company->status;

            array_push($results,$result);
        }

        return $results;
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
            'level' => isset($company->level) ? $company->level : 0,
            'credit' => isset($company->credit) ? $company->credit : 0,
            'type' => $company->type,
            'contactor' => isset($company->contactor) ? $company->contactor : '',
            'address' => isset($company->address) ? $company->address : '',
            'province' => isset($company->province) ? $company->province : '',
            'city' => isset($company->city) ? $company->city : '',
            'email' => isset($company->email) ? $company->email : '',
            'home_page' => isset($company->home_page) ? $company->home_page : '',
            'service_phone_1' => isset($company->service_phone_1) ? $company->service_phone_1 : '',
            'service_phone_2' => isset($company->service_phone_2) ? $company->service_phone_2 : '',
            'service_phone_3' => isset($company->service_phone_3) ? $company->service_phone_3 : '',
            'service_phone_4' => isset($company->service_phone_4) ? $company->service_phone_4 : '',
            'fax' => isset($company->fax) ? $company->fax : '',
            'description' => isset($company->description) ? $company->description : '',
            'remark' => isset($company->remark) ? $company->remark : '',
            'logo' => isset($company->logo) ? $company->logo : '',
            'images' => isset($company->images) ? $company->images : '',
        ];

    }

    public function getManufactures($pagination, $offset = 0, $size = 10){
        if($pagination){
            $condition = [
                'conditions' => 'type = :type: and status = :status:',
                'bind' => ['type' => LinkageUtils::COMPANY_MANUFACTURE,
                    'status' => StatusCodes::COMPANY_ACTIVE,
                ],
            ];
        }else{
            $condition = [
                'conditions' => 'type = :type: and status = :status:',
                'bind' => ['type' => LinkageUtils::COMPANY_MANUFACTURE,
                    'status' => StatusCodes::COMPANY_ACTIVE,
                ],
                'limit' => $size,
                'offset' => $offset,
            ];
        }

        $results = [];
        $manufactures = self::find($condition);
        foreach ($manufactures as $manufacture) {
            $result = [];
            $result['company_id'] = $manufacture->company_id;
            $result['company_name'] = $manufacture->name;
            $result['contact_name'] = isset($manufacture->contactor) ? $manufacture->contactor : '';
            $result['contact_address'] = isset($manufacture->address) ? $manufacture->address : '';
            $result['contact_phone'] = isset($manufacture->service_phone_1) ? $manufacture->service_phone_1 : '';
            $result['description'] = isset($manufacture->description) ? $manufacture->description : '';
            $result['logo'] = isset($manufacture->logo) ? $manufacture->logo : '';

            array_push($results,$result);
        }

        return $results;

    }

    public function getTransporters($pagination, $offset = 0, $size = 10){
        $condition = ' and a.type=1 ';
        if($pagination){
            $condition = "limit $offset, $size";
        }

        $sql = "select a.company_id, a.name, a.contactor, a.address, a.service_phone_1, a.description, a.status, a.logo, a.level, b.order_num from linkage_company a left join (select count(1) as order_num, transporter_id as company_id from linkage_order b where b.status in(1,3) group by b.transporter_id) b on a.company_id = b.company_id where a.status=0 ".$condition;
        $transporters = new Resultset(null, $this, $this->getReadConnection()->query($sql));

        $results = [];;
        foreach ($transporters as $transporter) {
            $result = [];
            $result['company_id'] = $transporter->company_id;
            $result['company_name'] = $transporter->name;
            $result['contact_name'] = isset($transporter->contactor) ? $transporter->contactor : '';
            $result['contact_address'] = isset($transporter->address) ? $transporter->address : '';
            $result['contact_phone'] = isset($transporter->service_phone_1) ? $transporter->service_phone_1 : '';
            $result['description'] = isset($transporter->description) ? $transporter->description : '';
            $result['logo'] = isset($transporter->logo) ? $transporter->logo : '';
            $result['score'] = isset($transporter->level) ? $transporter->level : '0';
            $result['order_num'] = isset($transporter->order_num) ? (int)$transporter->order_num : 0;

            array_push($results,$result);
        }

        return $results;
    }

    public function getCompanyByid($id){
        $condition = [
            'conditions' => 'company_id = :company_id:',
            'bind' => ['company_id' => $id]
        ];

        $companies = self::find($condition);
        foreach ($companies as $company) {
            $result = [];
            $result['company_id'] = $company->company_id;
            $result['company_name'] = $company->name? $company->name : '';
            $result['contact_name'] = isset($company->contactor) ? $company->contactor : '';
            $result['contact_address'] = isset($company->address) ? $company->address : '';
            $result['contact_phone'] = isset($company->service_phone_1) ? $company->service_phone_1 : '';
            $result['description'] = isset($company->description) ? $company->description : '';
            $result['logo'] = isset($company->logo) ? $company->logo : '';
        }

        return $result;

    }

    public function isCompanyExist($companyID){
        $companies = self::find([
            'conditions' => 'company_id = :companyID:',
            'bind' => ['companyID' => $companyID]
        ]);

        return sizeof($companies) > 0 ? true : false;
    }

    public function isCompanyRegistered($companyName){
        $companies = self::find([
            'conditions' => 'name = :name:',
            'bind' => ['name' => $companyName]
        ]);

        return sizeof($companies) > 0 ? true : false;
    }

    public function getSearch4Manufacture($companyname='', $pagination = 0,  $offset = 0, $size = 10){
        if(!$pagination){
            $limit = " limit $offset, $size";
        }else{
            $limit = "";
        }

        if($companyname == ''){
            $condition = " and a.status=0 ";
        }else{
            $condition = " and a.name like '%".$companyname."%' and a.status=0 ";
        }
        $sql = "select a.company_id, a.name, a.contactor, a.address, a.email, a.logo, a.create_time, a.update_time, a.level, b.order_num from linkage_company a left join (select count(1) as order_num, transporter_id as company_id from linkage_order b where b.status in(1,3) group by b.transporter_id) b on a.company_id = b.company_id where a.type=1 ".$condition." order by a.create_time desc ".$limit;
        $lists = new Resultset(null, $this, $this->getReadConnection()->query($sql));

        $companies = [];
        foreach($lists as $list){
            $company = [
                'company_id' => $list->company_id,
                'company_name' => $list->name,
                'contact_name' => $list->contactor,
                'company_address' => $list->address,
                'email' => $list->email,
                'logo' => $list->logo?$list->logo:'',
                'create_time' => $list->create_time,
                'update_time' => $list->update_time,
                'score' => isset($list->level)?$list->level:'0',
                'order_num' => isset($list->order_num)?$list->order_num:'0'

            ];

            array_push($companies, $company);
        }

        return $companies;

    }

    public function getSearch4Transporter($companyname='', $pagination = 0,  $offset = 0, $size = 10){
        if(!$pagination){
            $limit = " limit $offset, $size";
        }else{
            $limit = "";
        }

        if($companyname == ''){
            $condition = " and c.status=0 ";
        }else{
            $condition = " and c.name like '%".$companyname."%' and c.status=0 ";
        }

        $phql="select c.company_id, c.name, c.contactor, c.address, c.email, c.logo, c.create_time, c.update_time, c.level from Multiple\Models\Company c where type=0 ".$condition." order by c.create_time desc ".$limit;
        $lists = $this->modelsManager->executeQuery($phql);

        $companies = [];
        foreach($lists as $list){
            $company = [
                'company_id' => $list->company_id,
                'company_name' => $list->name,
                'contact_name' => $list->contactor,
                'company_address' => $list->address,
                'email' => $list->email,
                'logo' => $list->logo?$list->logo:'',
                'create_time' => $list->create_time,
                'update_time' => $list->update_time,
                'score' => isset($list->level)?$list->level:'0',

            ];

            array_push($companies, $company);
        }

        return $companies;

    }

    public function updateCompanyLevel($companyId,$score=0){
        if($score != 0){
            $company = self::findFirst([
                'conditions' => 'company_id = :company_id:',
                'bind' => ['company_id' => $companyId]
            ]);

            if(!isset($company->company_id)){
                throw new UserOperationException(ErrorCodes::COMPANY_NOTFOUND, ErrorCodes::$MESSAGE[ErrorCodes::COMPANY_NOTFOUND]);
            }

            $company->level = $score;//每5分加一级
            $company->update_time = time();

            if($company->update() == false){
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

}
