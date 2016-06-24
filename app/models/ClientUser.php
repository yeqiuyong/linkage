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
use Phalcon\Mvc\Model\Validator\Email as EmailValidator;
use Phalcon\Mvc\Model\Validator\Uniqueness as UniquenessValidator;

use Multiple\Core\Constants\Services;
use Multiple\Core\Constants\ErrorCodes;
use Multiple\Core\Constants\StatusCodes;
use Multiple\Core\Constants\LinkageUtils;
use Multiple\Core\Exception\DataBaseException;
use Multiple\Core\Exception\UserOperationException;

class ClientUser extends Model
{
    public $email;

    public function initialize(){
        $this->setSource("linkage_clientuser");

        $this->hasOne('user_id', 'Multiple\Models\ClientUserRole', 'user_id', array(  'alias' => 'user_role',
            'reusable' => true ));

        $this->BelongsTo('company_id', 'Multiple\Models\Company', 'company_id', array(  'alias' => 'company',
            'reusable' => true ));

    }

    public function registerByName($username, $mobile, $password, $status, $companyID){
        $security = Di::getDefault()->get(Services::SECURITY);

        if($this->isUserNameRegistered($username)){
            throw new UserOperationException(ErrorCodes::COMPANY_DEUPLICATE, ErrorCodes::$MESSAGE[ErrorCodes::COMPANY_DEUPLICATE]);
        }

        $now = time();

        $this->username = $username;
        $this->mobile = $mobile;
        $this->password = $security->hash($password);
        $this->company_id = $companyID;
        $this->create_time = $now;
        $this->update_time = $now;
        $this->status = $status;

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

    public function registerByMobile($mobile, $password, $status, $companyID){
        $security = Di::getDefault()->get(Services::SECURITY);

        if($this->isMobileRegistered($mobile)){
            throw new UserOperationException(ErrorCodes::USER_MOBILE_DUPLICATE, ErrorCodes::$MESSAGE[ErrorCodes::USER_MOBILE_DUPLICATE]);
        }

        $now = time();

        $this->username = $mobile;
        $this->mobile = $mobile;
        $this->password = $security->hash($password);
        $this->company_id = $companyID;

        $this->create_time = $now;
        $this->update_time = $now;
        $this->status = $status;

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

    public function registerDriver($mobile, $password, $status, $companyID, $name, $gender, $icon){
        $security = Di::getDefault()->get(Services::SECURITY);

        if($this->isMobileRegistered($mobile)){
            throw new UserOperationException(ErrorCodes::USER_MOBILE_DUPLICATE, ErrorCodes::$MESSAGE[ErrorCodes::USER_MOBILE_DUPLICATE]);
        }

        $now = time();

        $this->username = $mobile;
        $this->mobile = $mobile;
        $this->name = $name;
        $this->password = $security->hash($password);
        $this->company_id = $companyID;
        $this->gender = $gender;
        $this->icon = $icon;

        $this->create_time = $now;
        $this->update_time = $now;
        $this->status = $status;

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

    public function updatePasswordByMobile($mobile, $password){
        $security = Di::getDefault()->get(Services::SECURITY);

        if(!$this->isMobileRegistered($mobile)){
            throw new UserOperationException(ErrorCodes::USER_MOBILE_NOTFOUND, ErrorCodes::$MESSAGE[ErrorCodes::USER_MOBILE_NOTFOUND]);
        }

        $user = self::findFirst([
            'conditions' => 'mobile = :mobile:',
            'bind' => ['mobile' => $mobile]
        ]);

        $user->password = $security->hash($password);
        $user->update_time = time();

        if($user->update() == false){
            $message = '';
            foreach ($user->getMessages() as $msg) {
                $message .= (String)$msg . ",";
            }
            $logger = Di::getDefault()->get(Services::LOGGER);
            $logger->fatal($message);

            throw new DataBaseException(ErrorCodes::DATA_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FAIL]);
        }
    }

    public function updatePasswordByID($userid, $password){
        $security = Di::getDefault()->get(Services::SECURITY);

        $user = self::findFirst([
            'conditions' => 'user_id = :user_id:',
            'bind' => ['user_id' => $userid]
        ]);

        if(!isset($user->user_id)){
            throw new UserOperationException(ErrorCodes::USER_NOTFOUND, ErrorCodes::$MESSAGE[ErrorCodes::USER_NOTFOUND]);
        }

        $user->password = $security->hash($password);
        $user->update_time = time();

        if($user->update() == false){
            $message = '';
            foreach ($user->getMessages() as $msg) {
                $message .= (String)$msg . ",";
            }
            $logger = Di::getDefault()->get(Services::LOGGER);
            $logger->fatal($message);

            throw new DataBaseException(ErrorCodes::DATA_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FAIL]);
        }
    }

    public function updateMobileByID($userid, $mobile){
        $user = self::findFirst([
            'conditions' => 'user_id = :user_id:',
            'bind' => ['user_id' => $userid]
        ]);

        if(!isset($user->user_id)){
            throw new UserOperationException(ErrorCodes::USER_NOTFOUND, ErrorCodes::$MESSAGE[ErrorCodes::USER_NOTFOUND]);
        }

        $user->mobile = $mobile;
        $user->update_time = time();

        if($user->update() == false){
            $message = '';
            foreach ($user->getMessages() as $msg) {
                $message .= (String)$msg . ",";
            }
            $logger = Di::getDefault()->get(Services::LOGGER);
            $logger->fatal($message);

            throw new DataBaseException(ErrorCodes::DATA_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FAIL]);
        }
    }

    public function updateIconById($userid, $icon){
        $user = self::findFirst([
            'conditions' => 'user_id = :user_id:',
            'bind' => ['user_id' => $userid]
        ]);

        if(!isset($user->user_id)){
            throw new UserOperationException(ErrorCodes::USER_NOTFOUND, ErrorCodes::$MESSAGE[ErrorCodes::USER_NOTFOUND]);
        }

        $user->icon = $icon;
        $user->update_time = time();

        if($user->update() == false){
            $message = '';
            foreach ($user->getMessages() as $msg) {
                $message .= (String)$msg;
            }
            $logger = Di::getDefault()->get(Services::LOGGER);
            $logger->fatal($message);

            throw new DataBaseException(ErrorCodes::DATA_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FAIL]);
        }
    }

    public function updateProfile($userid, $info = array()){
        $user = self::findFirst([
            'conditions' => 'user_id = :user_id:',
            'bind' => ['user_id' => $userid]
        ]);

        if(!isset($user->user_id)){
            throw new UserOperationException(ErrorCodes::USER_NOTFOUND, ErrorCodes::$MESSAGE[ErrorCodes::USER_NOTFOUND]);
        }

        //$user->mobile = empty($info['mobile']) ? $user->mobile : $info['mobile'];

        if(!empty($info['username'])){
            $user->username = $info['username'];
        }

        if(!empty($info['name'])){
            $user->name = $info['name'];
        }

        if(!empty($info['email'])){
            $user->email = $info['email'];
        }

        if(!empty($info['gender'])){
            $user->gender = $info['gender'];
        }

        if(!empty($info['birthday'])){
            $user->birthday = $info['birthday'];
        }

        if(!empty($info['identity'])){
            $user->identity_id = $info['identity'];
        }

        if(!empty($info['icon'])){
            $user->icon = $info['icon'];
        }

        if($user->update() == false){
            $message = '';
            foreach ($user->getMessages() as $msg) {
                $message .= (String)$msg. ',';
            }
            $logger = Di::getDefault()->get(Services::LOGGER);
            $logger->fatal($message);

            throw new DataBaseException(ErrorCodes::DATA_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FAIL]);
        }

    }

    public function updateStatus($userid, $status){
        $user = self::findFirst([
            'conditions' => 'user_id = :user_id:',
            'bind' => ['user_id' => $userid]
        ]);

        if(!isset($user->user_id)){
            throw new UserOperationException(ErrorCodes::USER_NOTFOUND, ErrorCodes::$MESSAGE[ErrorCodes::USER_NOTFOUND]);
        }

        $user->status = $status;
        $user->update_time = time();

        if($user->update() == false){
            $message = '';
            foreach ($user->getMessages() as $msg) {
                $message .= (String)$msg. ',';
            }
            $logger = Di::getDefault()->get(Services::LOGGER);
            $logger->fatal($message);

            throw new DataBaseException(ErrorCodes::DATA_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FAIL]);
        }
    }

    public function getSecretByName($username){
        $user = self::findFirst([
            'conditions' => 'username = :username:',
            'bind' => ['username' => $username]
        ]);

        if(!isset($user->user_id)){
            throw new UserOperationException(ErrorCodes::USER_NOTFOUND, ErrorCodes::$MESSAGE[ErrorCodes::USER_NOTFOUND]);
        }

        $password = $user == null ? '' : $user->password;
        $id = $user == null ? '' : $user->user_id;

        return ['password' => $password, 'id' => $id];
    }

    public function getSecretByMobile($mobile){
        $user = self::findFirst([
            'conditions' => 'mobile = :mobile:',
            'bind' => ['mobile' => $mobile]
        ]);

        if(!isset($user->user_id)){
            throw new UserOperationException(ErrorCodes::USER_NOTFOUND, ErrorCodes::$MESSAGE[ErrorCodes::USER_NOTFOUND]);
        }

        $password = $user == null ? '' : $user->password;
        $id = $user == null ? '' : $user->user_id;

        return ['password' => $password, 'id' => $id];
    }

    public function getUserByToken($identity){
        $user = self::findFirst([
            'conditions' => 'user_id = :identity:',
            'bind' => ['identity' => $identity]
        ]);

        if(!isset($user->user_id)){
            throw new UserOperationException(ErrorCodes::USER_NOTFOUND, ErrorCodes::$MESSAGE[ErrorCodes::USER_NOTFOUND]);
        }

        return $user;
    }

    public function getUserInfomation($userid){
        $user = self::findFirst([
            'conditions' => 'user_id = :user_id:',
            'bind' => ['user_id' => $userid]
        ]);

        if(!isset($user->user_id)){
            throw new UserOperationException(ErrorCodes::USER_NOTFOUND, ErrorCodes::$MESSAGE[ErrorCodes::USER_NOTFOUND]);
        }

        return [
            'username' => $user->username,
            'realname' => isset($user->name) ? $user->name : '',
            'mobile' => $user->mobile,
            'email' => isset($user->email) ? $user->email : '',
            'gender' => isset($user->gender) ? $user->gender : '',
            'birthday' => isset($user->birthday) ? $user->birthday : '',
            'identity' => isset($user->identity_id) ? $user->identity_id : '',
            'icon' => isset($user->icon) ? $user->icon : '',
            'company_id' => $user->company_id,
            'role' => $user->user_role->role->rolename,
            'role_id' => $user->user_role->role_id,
            'status' => $user->status,
            'update_time' => $user->update_time,
        ];
    }

    public function getCompanyidByUserid($userid){
        $user = self::findFirst([
            'conditions' => 'user_id = :user_id:',
            'bind' => ['user_id' => $userid]
        ]);

        if(!isset($user->user_id)){
            throw new UserOperationException(ErrorCodes::USER_NOTFOUND, ErrorCodes::$MESSAGE[ErrorCodes::USER_NOTFOUND]);
        }

        return $user->company_id;
    }

    public function getUserNameByUserid($userid){
        $user = self::findFirst([
            'conditions' => 'user_id = :user_id:',
            'bind' => ['user_id' => $userid]
        ]);

        if(!isset($user->username)){
            throw new DataBaseException(ErrorCodes::USER_NOTFOUND, ErrorCodes::$MESSAGE[ErrorCodes::USER_NOTFOUND]);
        }

        return $user->username;
    }

    public function getStaffs($userid, $pagination=0, $offset=0, $size=10){
        $condition = " and status = ". StatusCodes::CLIENT_USER_ACTIVE;
        if($pagination != 0){
            $condition = " limit ".$offset.",".$size;
        }

        $sql="select user_id, username, name, mobile, icon from linkage_clientuser where company_id in (select company_id from linkage_clientuser where user_id = $userid)" . $condition;
        // Execute the query
        $staffs = new Resultset(null, $this, $this->getReadConnection()->query($sql));

        $results = [];
        foreach($staffs as $staff){
            $result['staff_id'] = $staff->user_id;
            $result['username'] = $staff->username;
            $result['name'] = isset($staff->name) ? $staff->name : '';
            $result['mobile'] = $staff->mobile;
            $result['staff_icon'] = isset($staff->icon) ? $staff->icon : '';

            array_push($results, $result);
        }

        return $results;

    }

    public function getUsersByCompanyId($companyId){
        $users = self::find([
            'conditions' => 'company_id = :company_id:',
            'bind' => ['company_id' => $companyId]
        ]);

        $results = [];
        foreach ($users as $user) {
            $result['user_id'] = $user->user_id;
            $result['user'] = $user->username;
            $result['realname'] = $user->name;
            $result['mobile'] = $user->mobile;
            $result['status'] = $user->status;

            array_push($results,$result);
        }

        return $results;

    }

    public function getRoleId($userid){
        $user = self::findFirst([
            'conditions' => 'user_id = :user_id:',
            'bind' => ['user_id' => $userid]
        ]);

        if(!isset($user->user_id)){
            throw new UserOperationException(ErrorCodes::USER_NOTFOUND, ErrorCodes::$MESSAGE[ErrorCodes::USER_NOTFOUND]);
        }

        return $user->user_role->role_id;
    }

    public function getStatus($userid){
        $user = self::findFirst([
            'conditions' => 'user_id = :user_id:',
            'bind' => ['user_id' => $userid]
        ]);

        if(!isset($user->user_id)){
            throw new UserOperationException(ErrorCodes::USER_NOTFOUND, ErrorCodes::$MESSAGE[ErrorCodes::USER_NOTFOUND]);
        }

        return $user->status;
    }

    public function getNewUserCount(){
        $y = date("Y");
        $m = date("m");
        $d = date("d");
        $todayTime = mktime(0,0,0,$m,$d,$y);

        $phql="select count(1) as count, b.role_id from Multiple\Models\ClientUser a join Multiple\Models\ClientUserRole b where a.user_id = b.user_id and a.create_time > $todayTime group by b.role_id";
        $newUsers = $this->modelsManager->executeQuery($phql);

        return $newUsers;
    }

    public function getUserCountPerWeek($time){
        $time = (int)$time;

        $sql = "select t.type, t.role_id, sum(t.num ) as count from" . "(select a.user_id, b.role_id, 1 as num,"
            . " case" . " when(create_time > ". ($time - 7*86400) ." and create_time <= ". ($time - 6*86400) .") then '1'"
            . " when(create_time > ". ($time - 6*86400) ." and create_time <= ". ($time - 5*86400) .") then '2'"
            . " when(create_time > ". ($time - 5*86400) ." and create_time <= ". ($time - 4*86400) .") then '3'"
            . " when(create_time > ". ($time - 4*86400) ." and create_time <= ". ($time - 3*86400) .") then '4'"
            . " when(create_time > ". ($time - 3*86400) ." and create_time <= ". ($time - 2*86400) .") then '5'"
            . " when(create_time > ". ($time - 2*86400) ." and create_time <= ". ($time - 1*86400) .") then '6'"
            . " when(create_time > ". ($time - 1*86400) ." and create_time <= ". ($time) .") then '7'"
            . " else '8'" . " end as type from linkage_clientuser a "
            . " join linkage_user_role b on a.user_id = b.user_id"
            . " where create_time > ". ($time - 6*86400) ." and create_time <= ". ($time + 1*86400)
            . ") t group by t.type, t.role_id";

        $results = new Resultset(null, $this, $this->getReadConnection()->query($sql));

        return $results;
    }

    public function getUserCountPerMon($time){
        $qryDate = date('Y-m-d',$time);
        $BeginDate = date('Y-m-01', strtotime($qryDate));

        $yearTime = [
            strtotime("$BeginDate -0 month"),
            strtotime("$BeginDate -1 month"),
            strtotime("$BeginDate -2 month"),
            strtotime("$BeginDate -3 month"),
            strtotime("$BeginDate -4 month"),
            strtotime("$BeginDate -5 month"),
            strtotime("$BeginDate -6 month"),
            strtotime("$BeginDate -7 month"),
            strtotime("$BeginDate -8 month"),
            strtotime("$BeginDate -9 month"),
            strtotime("$BeginDate -10 month"),
            strtotime("$BeginDate -11 month"),
        ];

        $sql = "select t.type, t.role_id, sum(t.num ) as count from" . "(select a.user_id, b.role_id, 1 as num,"
            . " case" . " when(create_time >= ". ($yearTime[11]) ." and create_time < ". ($yearTime[10]) .") then '1'"
            . " when(create_time >= ". ($yearTime[10]) ." and create_time < ". ($yearTime[9]) .") then '2'"
            . " when(create_time >= ". ($yearTime[9]) ." and create_time < ". ($yearTime[8]) .") then '3'"
            . " when(create_time >= ". ($yearTime[8]) ." and create_time < ". ($yearTime[7]) .") then '4'"
            . " when(create_time >= ". ($yearTime[7]) ." and create_time < ". ($yearTime[6]) .") then '5'"
            . " when(create_time >= ". ($yearTime[6]) ." and create_time < ". ($yearTime[5]) .") then '6'"
            . " when(create_time >= ". ($yearTime[5]) ." and create_time < ". ($yearTime[4]) .") then '7'"
            . " when(create_time >= ". ($yearTime[4]) ." and create_time < ". ($yearTime[3]) .") then '8'"
            . " when(create_time >= ". ($yearTime[3]) ." and create_time < ". ($yearTime[2]) .") then '9'"
            . " when(create_time >= ". ($yearTime[2]) ." and create_time < ". ($yearTime[1]) .") then '10'"
            . " when(create_time >= ". ($yearTime[1]) ." and create_time < ". ($yearTime[0]) .") then '11'"
            . " when(create_time >= ". ($yearTime[0]) ." and create_time < ". $time .") then '12'"
            . " else '13'" . " end as type from linkage_clientuser a"
            . " join linkage_user_role b on a.user_id = b.user_id"
            . " where create_time > ". ($yearTime[11]) ." and create_time <= ". $time
            . ") t group by t.type, t.role_id";

        $results = new Resultset(null, $this, $this->getReadConnection()->query($sql));

        return $results;
    }


    public function delStaff($staffId){
        $staff = self::findFirst([
            'conditions' => 'user_id = :user_id:',
            'bind' => ['user_id' => $staffId]
        ]);

        if(!isset($staff->user_id)){
            throw new UserOperationException(ErrorCodes::USER_NOTFOUND, ErrorCodes::$MESSAGE[ErrorCodes::USER_NOTFOUND]);
        }

        $staff->status = StatusCodes::CLIENT_USER_INACTIVE;
        $staff->update_time = time();

        if($staff->update() == false){
            $message = '';
            foreach ($staff->getMessages() as $msg) {
                $message .= (String)$msg . ",";
            }
            $logger = Di::getDefault()->get(Services::LOGGER);
            $logger->fatal($message);

            throw new DataBaseException(ErrorCodes::DATA_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FAIL]);
        }
    }

    public function isPasswordValidate($userid, $password){
        $security = Di::getDefault()->get(Services::SECURITY);

        $user = self::findFirst([
            'conditions' => 'user_id = :user_id:',
            'bind' => ['user_id' => $userid]
        ]);

        if(!isset($user->user_id)){
            throw new UserOperationException(ErrorCodes::USER_NOTFOUND, ErrorCodes::$MESSAGE[ErrorCodes::USER_NOTFOUND]);
        }

        return $security->checkHash($password, $user->password);
    }

    public function isAdmin($userid){
        $user = self::findFirst([
            'conditions' => 'user_id = :user_id:',
            'bind' => ['user_id' => $userid]
        ]);

        if(!isset($user->user_id)){
            throw new UserOperationException(ErrorCodes::USER_NOTFOUND, ErrorCodes::$MESSAGE[ErrorCodes::USER_NOTFOUND]);
        }

        $roleId = $user->user_role->role_id;

        return ($roleId == LinkageUtils::USER_ADMIN_MANUFACTURE || $roleId == LinkageUtils::USER_ADMIN_TRANSPORTER) ? true : false;

    }

    public function isMobileRegistered($mobile){
        $users = self::find([
            'conditions' => 'mobile = :mobile:',
            'bind' => ['mobile' => $mobile]
        ]);

        return sizeof($users) > 0 ? true : false;
    }

    public function isUserNameRegistered($username){
        $users = self::find([
            'conditions' => 'username = :username:',
            'bind' => ['username' => $username]
        ]);

        return sizeof($users) > 0 ? true : false;
    }

    public function isUserRegistered($userid){
        $users = self::find([
            'conditions' => 'user_id = :user_id:',
            'bind' => ['user_id' => $userid]
        ]);

        return sizeof($users) > 0 ? true : false;
    }

    public function validation(){
        if($this->email){
            $this->validate(new EmailValidator(array(
                'field' => 'email'
            )));
        }
        $this->validate(new UniquenessValidator(array(
            'field' => 'username',
            'message' => 'Sorry, That username is already taken'
        )));
        if ($this->validationHasFailed() == true) {
            return false;
        }
    }
}
