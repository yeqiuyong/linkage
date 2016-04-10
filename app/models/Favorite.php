<?php
/**
 * Created by PhpStorm.
 * User: uni
 * Date: 16/3/18
 * Time: 下午4:04
 */


namespace Multiple\Models;

use Phalcon\Di;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Resultset\Simple as Resultset;

use Multiple\Core\Constants\StatusCodes;
use Multiple\Core\Constants\ErrorCodes;
use Multiple\COre\Constants\Services;
use Multiple\Core\Exception\DataBaseException;

class Favorite extends Model
{
    public function initialize(){
        $this->setSource("linkage_user_favorite");
    }

    public function add($userid, $companyId)
    {
        $now = time();

        $this->user_id = $userid;
        $this->company_id = $companyId;
        $this->create_time = $now;
        $this->update_time = $now;
        $this->status = StatusCodes::FAVORITE_ACTIVE;

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

    public function delFavorite($userid, $companyId)
    {
        $favorite = self::findFirst([
            'conditions' => 'user_id = :user_id: AND company_id = :company_id:',
            'bind' => [
                'user_id' => $userid,
                'company_id' => $companyId,
            ]
        ]);

        if(!isset($favorite->favorite_id)){
            throw new UserOperationException(ErrorCodes::USER_FAVORITE_NOT_FOUND, ErrorCodes::$MESSAGE[ErrorCodes::USER_FAVORITE_NOT_FOUND]);
        }

        $now = time();
        $favorite->update_time = $now;
        $favorite->status = StatusCodes::FAVORITE_DELETE;

        if ($favorite->update() == false) {
            $message = '';
            foreach ($favorite->getMessages() as $msg) {
                $message .= (String)$msg . ',';
            }
            $logger = Di::getDefault()->get(Services::LOGGER);
            $logger->fatal($message);

            throw new DataBaseException(ErrorCodes::DATA_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FAIL]);
        }
    }

    public function getList($userid, $pagination, $offset, $size){
        $condition = "";
        if($pagination != 0){
            $condition = "limit ".$offset.",".$size;
        }

//        $phql="select a.company_id, b.name, b.contactor, b.service_phone_1, b.logo, c.order_num from Multiple\Models\Favorite a join Multiple\Models\Company b (select count(1) as order_num, transporter_id as company_id from linkage_order t group by t.transporter_id) c where a.company_id = b.company_id and a.company_id = c.and user_id = $userid " . $condition;
//        $favorites = $this->modelsManager->executeQuery($phql);

        $sql = "select a.company_id, b.name, b.contactor, b.service_phone_1, c.order_num from linkage_user_favorite a left join linkage_company b on a.company_id = b.company_id left join (select count(1) as order_num, t.transporter_id as company_id from linkage_order t group by t.transporter_id) c  on a.company_id = c.company_id where a.status = 0 and b.status=0 ".$condition;
        $favorites = new Resultset(null, $this, $this->getReadConnection()->query($sql));

        $companies = [];
        foreach($favorites as $favorite){
            $company['company_id'] = $favorite->company_id;
            $company['company_name'] = $favorite->name;
            $company['contact_name'] = $favorite->contactor;
            $company['service_phone'] = $favorite->service_phone_1;
            $company['logo'] = isset($favorite->logo) ? $favorite->logo : 0;
            $company['score'] = 5;
            $company['order_num'] = isset($favorite->order_num) ? $favorite->order_num : 0;

            array_push($companies, $company);
        }
        return $companies;
    }

}