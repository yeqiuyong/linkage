<?php
/**
 * Created by PhpStorm.
 * User: uni
 * Date: 16/3/18
 * Time: 下午8:37
 */


namespace Multiple\Models;

use Phalcon\Di;
use Phalcon\Mvc\Model;

use Multiple\Core\Constants\LinkageUtils;
use Multiple\Core\Constants\ErrorCodes;
use Multiple\Core\Constants\Services;
use Multiple\Core\Constants\StatusCodes;
use Multiple\Core\Exception\DataBaseException;
use Multiple\Core\Exception\UserOperationException;

class Notice extends Model
{
    public function initialize(){
        $this->setSource("linkage_notice");
    }

    public function getAdv($pagination = 0, $offset = 0, $size = 10){
        if($pagination){
            $notices = self::find([
                'conditions' => 'type = :type: AND status = :status:',
                'bind' => ['type' => LinkageUtils::MESSAGE_TYPE_ADV, 'status' => StatusCodes::NOTICE_ACTIVE],
                'order' => 'create_time DESC',
                'offset' => $offset,
                'limit' => $size,

            ]);
        }else{
            $notices = self::find([
                'conditions' => 'type = :type: AND status = :status:',
                'bind' => ['type' => LinkageUtils::MESSAGE_TYPE_ADV, 'status' => StatusCodes::NOTICE_ACTIVE],
                'order' => 'create_time DESC',
            ]);
        }

        $results = [];
        foreach($notices as $notice){
            $result['title'] = $notice->title;
            $result['link'] = $notice->link;
            $result['icon'] = $notice->image;

            array_push($results, $result);
        }

        return $results;
    }

    public function getAdv4Admin(){
        $advs = self::find([
            'conditions' => 'type = :type: AND status != :status:',
            'bind' => ['type' => LinkageUtils::MESSAGE_TYPE_ADV, 'status' => StatusCodes::NOTICE_DELETE],
        ]);

        $results = [];
        foreach ($advs as $adv) {
            $result = [];
            $result['id'] = $adv->id;
            $result['description'] = $adv->description;
            $result['link'] = $adv->link;
            $result['title'] = $adv->title;
            $result['status'] = $adv->status;

            array_push($results,$result);
        }

        return $results;
    }

    public function getAdvById($id){
        $adv = self::findFirst([
            'conditions' => 'type = :type: AND id = :id: AND status != :status:',
            'bind' => ['type' => LinkageUtils::MESSAGE_TYPE_ADV,
                'status' => StatusCodes::NOTICE_DELETE,
                'id' => $id,
            ]
        ]);

        if(isset($adv->id)){
            $result['id'] = $adv->id;
            $result['description'] = $adv->description;
            $result['link'] = $adv->link;
            $result['title'] = $adv->title;
            $result['memo'] = $adv->memo;

        }else{
            throw new DataBaseException(ErrorCodes::DATA_FIND_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FIND_FAIL]);
        }

        return $result;

    }

    public function addAdv($title, $link, $description, $memo, $image, $creator){
        $now = time();

        $this->title = $title;
        $this->link = $link;
        $this->description = $description;
        $this->memo = $memo;
        $this->image = $image;
        $this->type = LinkageUtils::MESSAGE_TYPE_ADV;
        $this->status = StatusCodes::NOTICE_ACTIVE;
        $this->client_type = 0;
        $this->create_by = $creator;

        $this->create_time = $now;
        $this->update_time = $now;

        if ($this->save() == false){
            $message = '';
            foreach ($this->getMessages() as $msg) {
                $message .= (String)$msg. ',';
            }
            $logger = Di::getDefault()->get(Services::LOGGER);
            $logger->fatal($message);

            throw new DataBaseException(ErrorCodes::DATA_CREATE_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_CREATE_FAIL]);
        }

    }

    public function getMsg($roleId, $pagination, $offset, $size){
        if($pagination){
            $notices = self::find([
                'conditions' => 'client_type = :client_type: AND type = :type: AND status = 0',
                'bind' => ['client_type' => $roleId, 'type' => LinkageUtils::MESSAGE_TYPE_NOTICE],
                'order' => 'create_time DESC',
                'offset' => $offset,
                'limit' => $size,

            ]);
        }else{
            $notices = self::find([
                'conditions' => 'client_type = :client_type: AND type = :type: AND status = 0',
                'bind' => ['client_type' => $roleId, 'type' => LinkageUtils::MESSAGE_TYPE_NOTICE],
                'order' => 'create_time DESC',
            ]);
        }

        $results = [];
        foreach($notices as $notice){
            $result['type'] = $notice->type;
            $result['icon'] = $notice->image;
            $result['title'] = $notice->title;
            $result['description'] = $notice->description;
            $result['creation_time'] = $notice->create_time;

            array_push($results, $result);
        }

        return $results;
    }

    public function getMsgDetail($noticeId){
        $notice = self::findFirst([
            'conditions' => 'id = :notice_id:',
            'bind' => ['notice_id' => $noticeId]
        ]);

        if(isset($notice->id)){
            return [
                'type' => $notice->type,
                'icon' => $notice->image,
                'title' => $notice->title,
                'description' => $notice->description,
                'creation_time' => $notice->create_time,

            ];
        }else{
            throw new DataBaseException(ErrorCodes::DATA_FIND_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FIND_FAIL]);
        }
    }

    public function updateStatus($advId, $status){
        $advertise = self::findFirst([
            'conditions' => 'id = :id:',
            'bind' => ['id' => $advId]
        ]);

        if(!isset($advertise->id)){
            throw new UserOperationException(ErrorCodes::USER_ADVERTISE_NOT_FOUND, ErrorCodes::$MESSAGE[ErrorCodes::USER_ADVERTISE_NOT_FOUND]);
        }

        $advertise->status = $status;
        $advertise->update_time = time();

        if($advertise->update() == false){
            $message = '';
            foreach ($advertise->getMessages() as $msg) {
                $message .= (String)$msg. ',';
            }
            $logger = Di::getDefault()->get(Services::LOGGER);
            $logger->fatal($message);

            throw new DataBaseException(ErrorCodes::DATA_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FAIL]);
        }
    }

}