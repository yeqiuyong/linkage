<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/2/16
 * Time: 11:18 PM
 */

namespace Multiple\Models;

use Multiple\Core\Constants\LinkageUtils;
use Phalcon\Di;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Resultset\Simple as Resultset;

use Multiple\Core\Constants\Services;
use Multiple\Core\Constants\StatusCodes;
use Multiple\Core\Constants\ErrorCodes;
use Multiple\Core\Exception\DataBaseException;
use Multiple\Core\Exception\UserOperationException;


class Order extends Model
{
    public function initialize(){
        $this->setSource("linkage_order");

    }

    public function add($orderId, $type, $manufactureId, $transporterId, $mContactId, $mContactName, $mContactTel, $takeAddr, $takeTime, $deliveryAddr, $deliveryTime, $isTranPort, $memo){
        $now = time();

        $this->order_id = $orderId;
        $this->type = $type;
        $this->manufacture_id = $manufactureId;
        $this->transporter_id = $transporterId;
        $this->manufacture_contact_id = $mContactId;
        $this->manufacture_contact_name = $mContactName;
        $this->manufacture_contact_tel = $mContactTel;
        $this->take_address = $takeAddr;
        $this->take_time = $takeTime;
        $this->delivery_address = $deliveryAddr;
        $this->delivery_time = $deliveryTime;
        $this->is_transfer_port = $isTranPort;
        $this->memo = $memo;

        $this->create_time = $now;
        $this->update_time = $now;
        $this->status = StatusCodes::ORDER_PLACE;
        $this->is_comment = '0';
        $this->is_read = '-1';

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

    public function accept($orderId, $transporter_id, $transporter_name, $transporter_tel){
        $order = self::findFirst([
            'conditions' => 'order_id = :order_id:',
            'bind' => ['order_id' => $orderId]
        ]);

        if(!isset($order->order_id)){
            throw new UserOperationException(ErrorCodes::ORDER_NOT_FOUND, ErrorCodes::$MESSAGE[ErrorCodes::ORDER_NOT_FOUND]);
        }

        $order->transporter_contact_id = $transporter_id;
        $order->transporter_contact_name = $transporter_name;
        $order->transporter_contact_tel = $transporter_tel;

        $order->update_time = time();
        $order->status = StatusCodes::ORDER_HANDLING;

        if($order->update() == false){
            $message = '';
            foreach ($this->getMessages() as $msg) {
                $message .= (String)$msg . ",";
            }
            $logger = Di::getDefault()->get(Services::LOGGER);
            $logger->fatal($message);

            throw new DataBaseException(ErrorCodes::DATA_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FAIL]);
        }
    }

    public function updateStatus($orderId, $status){
        $order = self::findFirst([
            'conditions' => 'order_id = :order_id:',
            'bind' => ['order_id' => $orderId]
        ]);

        if(!isset($order->order_id)){
            throw new UserOperationException(ErrorCodes::ORDER_NOT_FOUND, ErrorCodes::$MESSAGE[ErrorCodes::ORDER_NOT_FOUND]);
        }

        $order->update_time = time();
        $order->status = $status;
        $order->is_read = '-3';

        if($order->update() == false){
            $message = '';
            foreach ($this->getMessages() as $msg) {
                $message .= (String)$msg . ",";
            }
            $logger = Di::getDefault()->get(Services::LOGGER);
            $logger->fatal($message);

            throw new DataBaseException(ErrorCodes::DATA_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FAIL]);
        }
    }

    public function getStatus($orderId){
        $order = self::findFirst([
            'conditions' => 'order_id = :order_id:',
            'bind' => ['order_id' => $orderId]
        ]);

        if(!isset($order->order_id)){
            throw new UserOperationException(ErrorCodes::ORDER_NOT_FOUND, ErrorCodes::$MESSAGE[ErrorCodes::ORDER_NOT_FOUND]);
        }

        return $order->status;
    }

    public function getOrderInfo($orderId){
        $order = self::findFirst([
            'conditions' => 'order_id = :order_id:',
            'bind' => ['order_id' => $orderId]
        ]);

        if(!isset($order->order_id)){
            throw new UserOperationException(ErrorCodes::ORDER_NOT_FOUND, ErrorCodes::$MESSAGE[ErrorCodes::ORDER_NOT_FOUND]);
        }

        return ['type' => $order->type,
            'manufacture_id' => $order->manufacture_id,
            'transporter_id' => $order->transporter_id,
            'manufacture_contact_id' => $order->manufacture_contact_id,
            'transporter_contact_id' => $order->transporter_contact_id,
        ];
    }

    public function getOrders4Manufacture($companyId, $type = -1, $status, $pagination = 0,  $offset = 0, $size = 10){
        if($type == -1){
            $condition = " and a.manufacture_id = $companyId ";
        }else{
            $condition = " and a.manufacture_id = $companyId and type = $type";
        }

        if($status == 1){
            $condition .= " and a.status in (0, 1)";
        }else if($status == 2){
            $condition .= " and a.status in (2, 3,4)";
        }else{
            $condition .= " and a.status not in (5)";
        }

        if(!$pagination){
            $limit = " limit $offset, $size";
        }else{
            $limit = "";
        }

        $phql="select a.order_id, a.type, a.transporter_id, a.create_time, a.update_time, a.status, b.name as company_name from Multiple\Models\Order a join Multiple\Models\Company b where a.transporter_id = b.company_id ".$condition." order by a.create_time desc ".$limit;
        $lists = $this->modelsManager->executeQuery($phql);

        $orders = [];
        foreach($lists as $list){
            $order = [
                'order_id' => $list->order_id,
                'type' =>  $list->type,
                'status' => $list->status,
                'company_id' => $list->transporter_id,
                'company_name' => $list->company_name,
                'create_time' => $list->create_time,
                'update_time' => $list->update_time,
                'comments' =>''

            ];

            array_push($orders, $order);
        }

        return $orders;
    }

    public function getOrders4Transporter($companyId, $type = -1, $status, $pagination = 0,  $offset = 0, $size = 10){
        if($type == -1){
            $condition = " and a.transporter_id = $companyId ";
        }else{
            $condition = " and a.transporter_id = $companyId and type = $type";
        }

        if($status == 1){
            $condition .= " and (a.status in (1) or a.status in (0))";
        }else if($status == 2){
            $condition .= " and a.status in (2, 3, 4)";
        }else{
            $condition .= " and a.status not in (5)";
        }

        if(!$pagination){
            $limit = " limit $offset, $size";
        }else{
            $limit = "";
        }

        $phql="select a.order_id, a.type, a.manufacture_id, a.create_time, a.update_time, a.status, b.name as company_name from Multiple\Models\Order a join Multiple\Models\Company b where a.manufacture_id = b.company_id ".$condition." order by a.create_time desc ".$limit;
        $lists = $this->modelsManager->executeQuery($phql);

        $orders = [];
        foreach($lists as $list){
            $order = [
                'order_id' => $list->order_id,
                'type' => $list->type,
                'status' => $list->status,
                'company_id' => $list->manufacture_id,
                'company_name' => $list->company_name,
                'create_time' => $list->create_time,
                'update_time' => $list->update_time,
                'comments' => ''

            ];

            array_push($orders, $order);
        }

        return $orders;
    }

    public function getSearchOrders4Manufacture($companyId,$searchType, $value, $pagination = 0,  $offset = 0, $size = 10){

        if(!$pagination){
            $limit = " limit $offset, $size";
        }else{
            $limit = "";
        }

        if($searchType == 2){
            $condition = " license like '%".$value."%'";
            $sql="select driver_id,license from Multiple\Models\Driver where ".$condition;
            $plist = $this->modelsManager->executeQuery($sql);
            $driver_id = '';
            foreach($plist as $value){
                $driver_id .= $value->driver_id.',';
            }
            $driver_id = substr($driver_id,0,-1);
            if($driver_id){
                $sqlcondition = " a.manufacture_id = $companyId and c.driver_id in(".$driver_id.") and a.status not in(5) ";
            }else{
                $sqlcondition = " 1!=1 ";
            }
            //$driver_id=$plist[0]->driver_id;

            $phql="select DISTINCT a.order_id, a.type, a.create_time, a.update_time, a.status, a.transporter_id as company_id, b.name as company_name from Multiple\Models\Order a inner join Multiple\Models\Company b on a.transporter_id = b.company_id inner join Multiple\Models\DriverTask c on a.order_id = c.order_id where ". $sqlcondition." order by a.create_time desc ".$limit;


        }else{
            $condition = "a.manufacture_id = $companyId and a.status not in(5) ";

            if($searchType == 0){
                $condition .= " and a.order_id like '%".$value."%'";
                $phql="select a.order_id, a.type, a.create_time, a.update_time, a.status, a.transporter_id as company_id, b.name as company_name from Multiple\Models\Order a join Multiple\Models\Company b where a.transporter_id = b.company_id and ".$condition." order by a.create_time desc ".$limit;
            }elseif($searchType == 1){
                $condition .= " and c.cargo_no like '%".$value."%'";
                $phql="select DISTINCT a.order_id, a.type, a.create_time, a.update_time, a.status, a.transporter_id as company_id, b.name as company_name from Multiple\Models\Order a inner join Multiple\Models\Company b on a.transporter_id = b.company_id inner join Multiple\Models\OrderCargo c on a.order_id = c.order_id where ". $condition." order by a.create_time desc ".$limit;
            }elseif($searchType == 3){
                $timeArrs = explode(';', $value);
                $condition .= " and a.create_time >= $timeArrs[0] and a.create_time <= $timeArrs[1]";
                $phql="select a.order_id, a.type, a.create_time, a.update_time, a.status, a.transporter_id as company_id, b.name as company_name from Multiple\Models\Order a join Multiple\Models\Company b where a.transporter_id = b.company_id and ".$condition." order by a.create_time desc ".$limit;
            }elseif($searchType == 4){
                $phql="select a.order_id, a.type, a.create_time, a.update_time, a.status, a.transporter_id as company_id, b.name as company_name from Multiple\Models\Order a join Multiple\Models\Company b where a.transporter_id = b.company_id and ".$condition." order by a.create_time desc ".$limit;
            }else{
                $condition .= " and b.name like '%".$value."%'";
                $phql="select a.order_id, a.type, a.create_time, a.update_time, a.status, a.transporter_id as company_id, b.name as company_name from Multiple\Models\Order a join Multiple\Models\Company b where a.transporter_id = b.company_id and ".$condition." order by a.create_time desc ".$limit;
            }

        }
        $lists = $this->modelsManager->executeQuery($phql);

        $orders = [];
        foreach($lists as $list){
            $order = [
                'order_id' => $list->order_id,
                'type' => $list->type,
                'status' => $list->status,
                'company_id' => $list->company_id,
                'company_name' => $list->company_name,
                'create_time' => $list->create_time,
                'update_time' => $list->update_time,

            ];

            array_push($orders, $order);
        }
        return $orders;
    }

    public function getSearchOrders4Transporter($companyId,$searchType, $value, $pagination = 0,  $offset = 0, $size = 10){

        if(!$pagination){
            $limit = " limit $offset, $size";
        }else{
            $limit = "";
        }

        if($searchType == 2){
            $condition = " license like '%".$value."%'";
            $sql="select driver_id,license from Multiple\Models\Driver where ".$condition;
            $plist = $this->modelsManager->executeQuery($sql);
            $driver_id = '';
            foreach($plist as $value){
                $driver_id .= $value->driver_id.',';
            }
            $driver_id = substr($driver_id,0,-1);
            //$driver_id=$plist[0]->driver_id;
            if($driver_id){
                $sqlcondition = " a.transporter_id = $companyId and c.driver_id in(".$driver_id.") and a.status not in(5) ";
            }else{
                $sqlcondition = " 1!=1 ";

            }

            $phql="select DISTINCT a.order_id, a.type, a.create_time, a.update_time, a.status, a.manufacture_id as company_id, b.name as company_name from Multiple\Models\Order a inner join Multiple\Models\Company b on a.manufacture_id = b.company_id inner join Multiple\Models\DriverTask c on a.order_id = c.order_id where ". $sqlcondition." order by a.create_time desc ".$limit;


        }else{
            $condition = "a.transporter_id = $companyId and a.status not in(5) ";

            if($searchType == 0){
                $condition .= " and a.order_id like '%".$value."%'";
                $phql="select a.order_id, a.type, a.create_time, a.update_time, a.status, a.manufacture_id as company_id, b.name as company_name from Multiple\Models\Order a join Multiple\Models\Company b where a.manufacture_id = b.company_id and ".$condition." order by a.create_time desc ".$limit;
            }elseif($searchType == 1){
                $condition .= " and c.cargo_no like '%".$value."%'";
                $phql="select DISTINCT a.order_id, a.type, a.create_time, a.update_time, a.status, a.manufacture_id as company_id, b.name as company_name from Multiple\Models\Order a inner join Multiple\Models\Company b on a.manufacture_id = b.company_id inner join Multiple\Models\OrderCargo c on a.order_id = c.order_id where ". $condition." order by a.create_time desc ".$limit;
            }elseif($searchType == 3){
                $timeArrs = explode(';', $value);
                $condition .= " and a.create_time >= $timeArrs[0] and a.create_time <= $timeArrs[1]";
                $phql="select a.order_id, a.type, a.create_time, a.update_time, a.status, a.manufacture_id as company_id, b.name as company_name from Multiple\Models\Order a join Multiple\Models\Company b where a.manufacture_id = b.company_id and ".$condition." order by a.create_time desc ".$limit;
            }elseif($searchType == 4){
                $condition .= " and b.name like '%".$value."%' ";
                $phql="select a.order_id, a.type, a.create_time, a.update_time, a.status, a.manufacture_id as company_id, b.name as company_name from Multiple\Models\Order a join Multiple\Models\Company b where a.manufacture_id = b.company_id and ".$condition." order by a.create_time desc ".$limit;
            }else{
                $phql="select a.order_id, a.type, a.create_time, a.update_time, a.status, a.manufacture_id as company_id, b.name as company_name from Multiple\Models\Order a join Multiple\Models\Company b where a.manufacture_id = b.company_id and ".$condition." order by a.create_time desc ".$limit;
            }

        }
        $lists = $this->modelsManager->executeQuery($phql);

        $orders = [];
        foreach($lists as $list){
            $order = [
                'order_id' => $list->order_id,
                'type' => $list->type,
                'status' => $list->status,
                'company_id' => $list->company_id,
                'company_name' => $list->company_name,
                'create_time' => $list->create_time,
                'update_time' => $list->update_time,

            ];

            array_push($orders, $order);
        }
        return $orders;
    }

    public function getCountsGroupByType(){
        $ordersCounts = self::count([
            'column' => 'order_id',
            'group' => 'type',
            'conditions' => 'status != :status:',
            'bind' => ['status' => StatusCodes::ORDER_DELETED]
        ]);

        $results = [];
        foreach ($ordersCounts as $ordersCount) {
            $result['order_type'] = $ordersCount->type;
            $result['order_num'] = $ordersCount->rowcount;

            array_push($results, $result);
        }

        return $results;
    }

    public function getCountsGroupByType4Company($companyId, $companyType){
        if(LinkageUtils::COMPANY_MANUFACTURE == $companyType){
            $ordersCounts = self::count([
                'column' => 'order_id',
                'group' => 'type',
                'conditions' => 'status != :status: AND manufacture_id = :company_id:',
                'bind' => ['status' => StatusCodes::ORDER_DELETED,
                    'company_id' => $companyId
                ]
            ]);
        }else{
            $ordersCounts = self::count([
                'column' => 'order_id',
                'group' => 'type',
                'conditions' => 'status != :status: AND transporter_id = :company_id:',
                'bind' => ['status' => StatusCodes::ORDER_DELETED,
                    'company_id' => $companyId
                ]
            ]);
        }

        return $ordersCounts;
    }

    public function getPlaceOrderCounts(){
        $condition = " where a.status not in (5) group by b.name order by order_cnt desc";
        $phql="select count(a.order_id) as order_cnt, b.name as company_name from Multiple\Models\Order a join Multiple\Models\Company b on a.manufacture_id = b.company_id ".$condition;
        $orderCounts = $this->modelsManager->executeQuery($phql);

        $orders = [];
        $cnt = 0;
        $otherCompanyCnt = 0;
        foreach($orderCounts as $orderCount){
            $order = [
                'company_name' => $orderCount->company_name,
                'order_num' => $orderCount->order_cnt,
            ];

            if(++$cnt < 6){
                array_push($orders, $order);
            }else{
                $otherCompanyCnt += $orderCount->order_cnt;
            }

        }

        if($cnt >= 6){
            $order = [
                'company_name' => '其他',
                'order_num' => $otherCompanyCnt,
            ];

            array_push($orders, $order);
        }

        return $orders;
    }

    public function getAcceptOrderCounts(){
        $condition = " where a.status not in (5) group by b.name order by order_cnt desc";
        $phql="select count(a.order_id) as order_cnt, b.name as company_name from Multiple\Models\Order a join Multiple\Models\Company b on a.transporter_id = b.company_id ".$condition;
        $orderCounts = $this->modelsManager->executeQuery($phql);

        $orders = [];
        $cnt = 0;
        $otherCompanyCnt = 0;
        foreach($orderCounts as $orderCount){
            $order = [
                'company_name' => $orderCount->company_name,
                'order_num' => $orderCount->order_cnt,
            ];

            if(++$cnt < 6){
                array_push($orders, $order);
            }else{
                $otherCompanyCnt += $orderCount->order_cnt;
            }

        }

        if($cnt >= 6){
            $order = [
                'company_name' => '其他',
                'order_num' => $otherCompanyCnt,
            ];

            array_push($orders, $order);
        }

        return $orders;
    }

    public function getOrderCountPerMon($time){
        $qryDate = date('Y-m-d',$time);
        $beginDate = date('Y-m-01', strtotime($qryDate));

        $yearTime = [
            strtotime("$beginDate -0 month"),
            strtotime("$beginDate -1 month"),
            strtotime("$beginDate -2 month"),
            strtotime("$beginDate -3 month"),
            strtotime("$beginDate -4 month"),
            strtotime("$beginDate -5 month"),
            strtotime("$beginDate -6 month"),
            strtotime("$beginDate -7 month"),
            strtotime("$beginDate -8 month"),
            strtotime("$beginDate -9 month"),
            strtotime("$beginDate -10 month"),
            strtotime("$beginDate -11 month"),
        ];

        $sql = "select t.type, t.order_date, sum(t.num ) as count from" . "(select a.type, 1 as num,"
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
            . " when(create_time >= ". ($yearTime[0]) ." and create_time <= ". $time .") then '12'"
            . " else '13'" . " end as order_date from linkage_order a"
            . " where status != ".StatusCodes::ORDER_DELETED." and create_time > ". ($yearTime[11]) ." and create_time <= ". $time
            . ") t group by t.type, t.order_date";

        $results = new Resultset(null, $this, $this->getReadConnection()->query($sql));

        return $results;
    }

    public function getOrderCountPerMon4Company($companyId, $companyType){
        $now = time();
        $qryDate = date('Y-m-d', $now);
        $beginDate = date('Y-m-01', strtotime($qryDate));

        $yearTime = [
            strtotime("$beginDate -0 month"),
            strtotime("$beginDate -1 month"),
            strtotime("$beginDate -2 month"),
            strtotime("$beginDate -3 month"),
            strtotime("$beginDate -4 month"),
            strtotime("$beginDate -5 month"),
            strtotime("$beginDate -6 month"),
            strtotime("$beginDate -7 month"),
            strtotime("$beginDate -8 month"),
            strtotime("$beginDate -9 month"),
            strtotime("$beginDate -10 month"),
            strtotime("$beginDate -11 month"),
        ];

        if(LinkageUtils::COMPANY_MANUFACTURE == $companyType){
            $condition = " and manufacture_id = " . $companyId;
        }else{
            $condition = " and transporter_id = " . $companyId;
        }


        $sql = "select t.order_date, sum(t.num ) as count from" . "(select 1 as num,"
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
            . " when(create_time >= ". ($yearTime[0]) ." and create_time <= ". $now .") then '12'"
            . " else '13'" . " end as order_date from linkage_order a"
            . " where status != ".StatusCodes::ORDER_DELETED." and create_time > ". ($yearTime[11]) ." and create_time <= ". $now
            . $condition
            . ") t group by t.order_date";

        $results = new Resultset(null, $this, $this->getReadConnection()->query($sql));

        return $results;
    }

    public function getPlaceOrderCountsByType($type){
        $condition = " where a.status not in (5) group by b.name order by order_cnt desc";
        $phql="select a.manufacture_id, count(a.order_id) as order_cnt, b.name as company_name, b.create_time from Multiple\Models\Order a join Multiple\Models\Company b on a.manufacture_id = b.company_id ".$condition;
        $orderCounts = $this->modelsManager->executeQuery($phql);

        $condition = " where a.status not in (5) and a.type=$type group by a.manufacture_id order by order_cnt desc";
        $phql="select a.manufacture_id, count(a.order_id) as order_cnt from Multiple\Models\Order a".$condition;
        $orderCounts4Type = $this->modelsManager->executeQuery($phql);

        $results = [];
        foreach($orderCounts as $orderCount){
            foreach($orderCounts4Type as $orderCount4Type){
                if($orderCount['manufacture_id'] == $orderCount4Type['manufacture_id']){
                    $result = [
                        'company_name' => $orderCount->company_name,
                        'order_num' => $orderCount->order_cnt,
                        'create_time' => $orderCount->create_time,
                        'sub_order_num' => $orderCount4Type->order_cnt,
                    ];

                    array_push($results, $result);
                    break;
                }
            }
        }

        return $results;
    }

    public function getAcceptOrderCountsByType($type){
        $condition = " where a.status not in (5) group by b.name order by order_cnt desc";
        $phql="select a.transporter_id, count(a.order_id) as order_cnt, b.name as company_name, b.create_time from Multiple\Models\Order a join Multiple\Models\Company b on a.transporter_id = b.company_id ".$condition;
        $orderCounts = $this->modelsManager->executeQuery($phql);

        $condition = " where a.status not in (5) and a.type = $type group by a.manufacture_id order by order_cnt desc";
        $phql="select a.transporter_id, count(a.order_id) as order_cnt from Multiple\Models\Order a".$condition;
        $orderCounts4Type = $this->modelsManager->executeQuery($phql);

        $results = [];
        foreach($orderCounts as $orderCount){
            foreach($orderCounts4Type as $orderCount4Type){
                if($orderCount['transporter_id'] == $orderCount4Type['transporter_id']){
                    $result = [
                        'company_name' => $orderCount->company_name,
                        'order_num' => $orderCount->order_cnt,
                        'create_time' => $orderCount->create_time,
                        'sub_order_num' => $orderCount4Type->order_cnt,
                    ];

                    array_push($results, $result);
                    break;
                }
            }
        }

        return $results;
    }

    public function isOrderExist($orderId){
        $orders = self::find([
            'conditions' => 'order_id = :order_id:',
            'bind' => ['order_id' => $orderId]
        ]);

        return (sizeof($orders) == 0) ? false : true;
    }

    public function updateComment($orderId){
        $order = self::findFirst([
            'conditions' => 'order_id = :order_id:',
            'bind' => ['order_id' => $orderId]
        ]);

        if(!isset($order->order_id)){
            throw new UserOperationException(ErrorCodes::ORDER_NOT_FOUND, ErrorCodes::$MESSAGE[ErrorCodes::ORDER_NOT_FOUND]);
        }

        $order->update_time = time();
        $order->is_comment = 1;

        if($order->update() == false){
            $message = '';
            foreach ($this->getMessages() as $msg) {
                $message .= (String)$msg . ",";
            }
            $logger = Di::getDefault()->get(Services::LOGGER);
            $logger->fatal($message);

            throw new DataBaseException(ErrorCodes::DATA_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FAIL]);
        }
    }

    public function getManureOrder4admin($companyId,$start_time='',$end_time=''){
        $condition = " and manufacture_id='".$companyId."' ";
        if($start_time != '' || $end_time != ''){
            $condition .= " and a.create_time>=$start_time and a.create_time<=$end_time ";
        }
        $phql="select a.order_id, a.type, a.create_time, a.status, b.name as company_name from Multiple\Models\Order a join Multiple\Models\Company b where a.transporter_id = b.company_id ".$condition." order by a.create_time desc ";
        $orders = $this->modelsManager->executeQuery($phql);

        $results = [];
        foreach ($orders as $order) {
            switch($order->type){
                case '1': $type = '进口';break;
                case '2': $type = '内陆柜';break;
                case '3': $type = '自备柜';break;
                default:$type = '出口';

            }
            switch($order->status){
                case '1': $status = '处理中';break;
                case '2': $status = '被拒绝';break;
                case '3': $status = '已完成';break;
                case '4': $status = '被取消';break;
                default:$status = '未处理';

            }
            $result = [];
            $result['order_id'] = $order->order_id;
            $result['type'] = $type;
            $result['company_name'] = $order->company_name;
            $result['status'] = $status;
            $result['create_time'] = $order->create_time;

            array_push($results,$result);
        }

        return $results;
    }

    public function getTransporterOrder4admin($companyId,$start_time='',$end_time=''){
        $condition = "and transporter_id='".$companyId."' ";
        if($start_time != '' || $end_time != ''){
            $condition .= " and a.create_time>=$start_time and a.create_time<=$end_time ";
        }
        $phql="select a.order_id, a.type, a.create_time, a.status, b.name as company_name from Multiple\Models\Order a join Multiple\Models\Company b where a.manufacture_id = b.company_id ".$condition."order by a.create_time desc ";
        $orders = $this->modelsManager->executeQuery($phql);

        $results = [];
        foreach ($orders as $order) {
            switch($order->type){
                case '1': $type = '进口';break;
                case '2': $type = '内陆柜';break;
                case '3': $type = '自备柜';break;
                default:$type = '出口';

            }
            switch($order->status){
                case '1': $status = '处理中';break;
                case '2': $status = '被拒绝';break;
                case '3': $status = '已完成';break;
                case '4': $status = '被取消';break;
                default:$status = '未处理';

            }
            $result = [];
            $result['order_id'] = $order->order_id;
            $result['type'] = $type;
            $result['company_name'] = $order->company_name;
            $result['status'] = $status;
            $result['create_time'] = $order->create_time;

            array_push($results,$result);
        }

        return $results;
    }

    public function getRead4Manufacture($companyId =''){
        $orders = self::find([
            'conditions' => 'manufacture_id = :company_id: AND is_read=-3',
            'bind' => ['company_id' => $companyId]
        ]);

        return (sizeof($orders) == 0) ? false : true;
    }

    public function getRead4Transporter($companyId =''){
        $orders = self::find([
            'conditions' => 'transporter_id = :company_id: AND is_read=-1',
            'bind' => ['company_id' => $companyId]
        ]);

        return (sizeof($orders) == 0) ? false : true;
    }

    public function updateReadStatus($orderId,$read){
        $order = self::findFirst([
            'conditions' => 'order_id = :order_id:',
            'bind' => ['order_id' => $orderId]
        ]);

        if(!isset($order->order_id)){
            throw new UserOperationException(ErrorCodes::ORDER_NOT_FOUND, ErrorCodes::$MESSAGE[ErrorCodes::ORDER_NOT_FOUND]);
        }

        $order->update_time = time();
        $order->is_read = $read;

        if($order->update() == false){
            $message = '';
            foreach ($this->getMessages() as $msg) {
                $message .= (String)$msg . ",";
            }
            $logger = Di::getDefault()->get(Services::LOGGER);
            $logger->fatal($message);

            throw new DataBaseException(ErrorCodes::DATA_FAIL, ErrorCodes::$MESSAGE[ErrorCodes::DATA_FAIL]);
        }
    }
    //查找承运商的总订单数量
    public function getOrderScore4manu($companyId){
        $phql="select count(1)as total, sum(b.score) as allscore from Multiple\Models\Order a join Multiple\Models\OrderComment b where a.order_id = b.order_id and a.is_comment='1' and a.transporter_id='".$companyId."'";
        $orders = $this->modelsManager->executeQuery($phql);
        foreach ($orders as $order) {

            $result[] = $order->allscore;
            $result[] = $order->total;

        }
        return $result;

    }
    //查找厂商的总订单数量
    public function getOrderScore4tran($companyId){
        $phql="select count(1)as total, sum(b.score) as allscore from Multiple\Models\Order a join Multiple\Models\OrderComment b where a.order_id = b.order_id and a.is_comment='1' and a.manufacture_id='".$companyId."'";
        $orders = $this->modelsManager->executeQuery($phql);
        foreach ($orders as $order) {

            $result[] = $order->allscore;
            $result[] = $order->total;

        }
        return $result;

    }

}