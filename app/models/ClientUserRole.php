<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/2/16
 * Time: 3:41 PM
 */

namespace Multiple\Models;

use Phalcon\Di;
use Phalcon\Mvc\Model;

use Multiple\Core\Constants\Services;
use Multiple\Core\Constants\ErrorCodes;
use Multiple\Core\Exception\DataBaseException;

class ClientUserRole extends Model
{
    private $logger;

    public function initialize(){
        $this->setSource("linkage_user_role");

        $this->hasOne('role_id', 'Multiple\Models\Role', 'role_id', array(  'alias' => 'role',
            'reusable' => true ));

        $this->logger = Di::getDefault()->get(Services::LOGGER);
    }

    public function add($userid, $roleid){
        $this->user_id = $userid;
        $this->role_id = $roleid;

        if($this->save() == false){
            $message = '';
            foreach ($this->getMessages() as $msg) {
                $message .= (String)$msg;
            }
            $this->logger->fatal($message);

            throw new DataBaseException(ErrorCodes::DATA_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FAIL]);
        }
    }

    public function getUserCount(){
        $userCounts = self::count([
            'column' => 'user_id',
            'group' => 'role_id',
            'order' => 'role_id'
        ]);

        $results = [];
        foreach ($userCounts as $userCount) {
            $result['role_id'] = $userCount->role_id;
            $result['count'] = $userCount->rowcount;

            array_push($results, $result);
        }

        return $userCounts;
    }

}
