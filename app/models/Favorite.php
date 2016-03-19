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

use Multiple\Core\Constants\StatusCodes;
use Multiple\Core\Constants\ErrorCodes;
use Multiple\COre\Constants\Services;
use Multiple\Core\Exception\DataBaseException;

class Favorite extends Model
{
    public function initialize(){
        $this->setSource("linkage_user_favorite");
    }

    public function addFavorite($userid, $companyId)
    {
        $now = time();

        $favorite = new Favorite();
        $favorite->user_id = $userid;
        $favorite->company_id = $companyId;
        $favorite->create_time = $now;
        $favorite->update_time = $now;
        $favorite->status = StatusCodes::FAVORITE_ACTIVE;

        if ($favorite->save() == false) {
            $message = '';
            foreach ($favorite->getMessages() as $msg) {
                $message .= (String)$msg . ',';
            }
            $logger = Di::getDefault()->get(Services::LOGGER);
            $logger->fatal($message);

            throw new DataBaseException(ErrorCodes::DATA_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FAIL]);
        }
    }

    public function delFavorite($userid, $companyId)
    {
        $now = time();

        $favorite = self::findFirst([
            'conditions' => 'user_id = :user_id: AND company_id = :company_id:',
            'bind' => [
                'user_id' => $userid,
                'company_id' => $companyId,
            ]
        ]);

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

        $phql="select a.company_id, b.name, b.contactor, b.service_phone_1 from Favorite a join Company b where a.company_id = b.company_id and user_id = $userid " . $condition;
        $favorites = $this->modelsManager->executeQuery($phql);

        $companies = [];
        foreach($favorites as $favorite){
            $company['company_id'] = $favorite->company_id;
            $company['company_name'] = $favorite->name;
            $company['contact_name'] = $favorite->contactor;
            $company['service_phone'] = $favorite->service_phone_1;

            array_push($companies, $company);
        }
        return $companies;
    }

}