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

use Multiple\Core\Constants\StatusCodes;
use Multiple\Core\Constants\ErrorCodes;
use Multiple\COre\Constants\Services;
use Multiple\Core\Exception\DataBaseException;

class Notice extends Model
{
    public function initialize(){
        $this->setSource("linkage_notice");
    }

    public function getList($userid, $pagination, $offset, $size){
        $users = self::find([
            'conditions' => 'username = :username:',
            'bind' => ['username' => $username]
        ]);

        return sizeof($users) > 0 ? true : false;
    }

    public function getDetail($noticeId){
        $notice = self::findFirst([
            'conditions' => 'id = :notice_id:',
            'bind' => ['notice_id' => $noticeId]
        ]);


        return [
            'type' => ,

        ];

    }

}