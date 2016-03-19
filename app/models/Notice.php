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

use Multiple\Core\Constants\ErrorCodes;
use Multiple\Core\Exception\DataBaseException;

class Notice extends Model
{
    public function initialize(){
        $this->setSource("linkage_notice");
    }

    public function getList($type, $roleId, $pagination, $offset, $size){
        if($pagination){
            $notices = self::find([
                'conditions' => 'client_type = :client_type: AND type = :type: AND status = 0',
                'bind' => ['client_type' => $roleId, 'type' => $type],
                'order' => 'create_time DESC',
                'offset' => $offset,
                'limit' => $size,

            ]);
        }else{
            $notices = self::find([
                'conditions' => 'client_type = :client_type: AND type = :type: AND status = 0',
                'bind' => ['client_type' => $roleId, 'type' => $type],
                'order' => 'create_time DESC',
            ]);
        }

        $results = [];
        foreach($notices as $notice){
            $result['type'] = $notice->type;
            $result['icon'] = $notice->link;
            $result['title'] = $notice->title;
            $result['description'] = $notice->description;
            $result['creation_time'] = $notice->creation_time;

            array_push($results, $result);
        }

        return $results;
    }

    public function getDetail($noticeId){
        $notice = self::findFirst([
            'conditions' => 'id = :notice_id:',
            'bind' => ['notice_id' => $noticeId]
        ]);

        if(isset($notice->id)){
            return [
                'type' => $notice->type,
                'icon' => $notice->link,
                'title' => $notice->title,
                'description' => $notice->description,
                'creation_time' => $notice->creation_time,

            ];
        }else{
            throw new DataBaseException(ErrorCodes::DATA_FIND_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FIND_FAIL]);
        }
    }

}