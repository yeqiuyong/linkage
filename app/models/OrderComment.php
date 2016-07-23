<?php
/**
 * Created by PhpStorm.
 * User: uni
 * Date: 16/4/5
 * Time: 下午4:29
 */


namespace Multiple\Models;

use Phalcon\Di;
use Phalcon\Mvc\Model;

use Multiple\Core\Constants\Services;
use Multiple\Core\Constants\ErrorCodes;
use Multiple\Core\Exception\DataBaseException;


class OrderComment extends Model
{
    public function initialize(){
        $this->setSource("linkage_order_comment");
    }

    public function add($orderId, $score, $comment = ''){
        $now = time();

        $this->order_id = $orderId;
        $this->score = $score;
        $this->comment = $comment;

        $this->create_time = $now;
        $this->update_time = $now;

        if($this->save() == false){
            $message = '';
            foreach ($this->getMessages() as $msg) {
                $message .= (String)$msg . ",";
            }
            $logger = Di::getDefault()->get(Services::LOGGER);
            $logger->fatal($message);

            throw new DataBaseException(ErrorCodes::DATA_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FAIL]);
        }
    }

    public function isCommentExist($orderId){
        $orders = self::find([
            'conditions' => 'order_id = :order_id:',
            'bind' => ['order_id' => $orderId]
        ]);

        return (sizeof($orders) == 0) ? false : true;
    }

    public function getCommentInfo($orderId){
        $comments = self::findFirst([
            'conditions' => 'order_id = :order_id:',
            'bind' => ['order_id' => $orderId]
        ]);

        return $comments;
    }

}