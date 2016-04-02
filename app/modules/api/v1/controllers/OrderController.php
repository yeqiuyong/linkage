<?php
/**
 * Created by PhpStorm.
 * User: uni
 * Date: 16/3/27
 * Time: 下午2:39
 */


namespace Multiple\API\Controllers;

use Phalcon\Di;

use Multiple\Core\Exception\Exception;
use Multiple\Core\APIControllerBase;
use Multiple\Core\Constants\LinkageUtils;
use Multiple\Core\Constants\Services;
use Multiple\Core\Constants\ErrorCodes;
use Multiple\Core\Constants\StatusCodes;

use Multiple\Models\Notice;
use Multiple\Models\ClientUser;
use Multiple\Models\Order;
use Multiple\Models\OrderExport;
use Multiple\Models\OrderImport;
use Multiple\Models\OrderSelf;
use Multiple\Models\OrderCargo;


/**
 * @resource("User")
 */
class OrderController extends APIControllerBase
{

    private $logger;

    public function initialize()
    {
        parent::initialize();

        $this->logger = Di::getDefault()->get(Services::LOGGER);

    }

    /**
     * @title("place4export")
     * @description("Place export order")
     * @requestExample("POST /order/place4export")
     * @response("Data object or Error object")
     */
    public function place4exportAction(){
        $tCompanyId = $this->request->getPost('company_id', 'int');
        $cargoStr = $this->request->getPost('cargo', 'string');
        $takeAddress = $this->request->getPost('take_address', 'string');
        $takeTime = $this->request->getPost('take_time', 'int');
        $deliveryAddress = $this->request->getPost('delivery_address', 'string');
        $deliveryTime = $this->request->getPost('delivery_time', 'int');
        $port = $this->request->getPost('port', 'string');
        $customsIn = $this->request->getPost('customs_in', 'int');
        $so = $this->request->getPost('so', 'string');
        $soImages = $this->request->getPost('so_images', 'string');
        $shipCompany = $this->request->getPost('ship_company', 'string');
        $shipName = $this->request->getPost('ship_name', 'string');
        $shipSchedule = $this->request->getPost('ship_schedule_no', 'string');
        $isBookCargo = $this->request->getPost('is_book_cargo', 'int');
        $isTransferPort = $this->request->getPost('is_transfer_port', 'int');
        $memo = $this->request->getPost('memo', 'string');

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        if(!isset($tCompanyId)){
            return $this->respondError(ErrorCodes::ORDER_TRANSPORTER_NULL, ErrorCodes::$MESSAGE[ErrorCodes::ORDER_TRANSPORTER_NULL]);
        }

        try{
            // Start a transaction
            $this->db->begin();

            $user = new ClientUser();
            $mCompanyId = $user->getCompanyidByUserid($this->cid);
            $mInfo = $user->getUserInfomation($this->cid);

            $orderId = $this->genOrderId($this->cid);

            $order = new Order();
            $order->add($orderId, StatusCodes::ORDER_PLACE, $mCompanyId, $tCompanyId, $mInfo['realname'], $mInfo['mobile'], $takeAddress, $takeTime, $deliveryAddress, $deliveryTime, $isTransferPort, $memo);

            $orderExport = new OrderExport();
            $orderExport->add($orderId, $so, $soImages, $customsIn, $port, $shipCompany, $shipName, $shipSchedule, $isBookCargo);

            $cargoObjs = $this->genCargosObj($cargoStr);
            foreach($cargoObjs as $cargoObj){
                for($i = 0; $i < $cargoObj['num']; $i++){
                    $orderCargo = new OrderCargo();
                    $orderCargo->add($orderId, $cargoObj['type'], $i);
                }
            }

            // Commit the transaction
            $this->db->commit();

        }catch (Exception $e){
            $this->db->rollback();

            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondOK();

    }

    /**
     * @title("place4import")
     * @description("Place import order")
     * @requestExample("POST /order/place4import")
     * @response("Data object or Error object")
     */
    public function place4importAction(){


    }

    /**
     * @title("place4self")
     * @description("Place self order")
     * @requestExample("POST /order/place4self")
     * @response("Data object or Error object")
     */
    public function place4selfAction(){


    }

    /**
     * @title("cancel")
     * @description("Cancel order")
     * @requestExample("POST /order/cancel")
     * @response("Data object or Error object")
     */
    public function cancelAction(){


    }

    /**
     * @title("list")
     * @description("List orders")
     * @requestExample("POST /order/list")
     * @response("Data object or Error object")
     */
    public function listAction(){
        $pagination = $this->request->getPost('pagination');
        $offset = $this->request->getPost('offset');
        $size = $this->request->getPost('size');

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        try {
            $user = new ClientUser();
            $roleId = $user->getRoleId($this->cid);

            $notice = new Notice();
            $messages = $notice->getList(LinkageUtils::MESSAGE_TYPE_NOTICE, $roleId, $pagination, $offset, $size);

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondArray($messages);

    }

    /**
     * @title("detail4export")
     * @description("Export order detail")
     * @requestExample("POST /order/detail4export")
     * @response("Data object or Error object")
     */
    public function detail4exportAction(){


    }

    /**
     * @title("detail4import")
     * @description("Import order detail")
     * @requestExample("POST /order/detail4import")
     * @response("Data object or Error object")
     */
    public function detail4importAction(){


    }

    /**
     * @title("detail4self")
     * @description("Self order detail")
     * @requestExample("POST /order/detail4self")
     * @response("Data object or Error object")
     */
    public function detail4selfAction(){


    }

    /**
     * @title("process")
     * @description("Order process")
     * @requestExample("POST /order/process")
     * @response("Data object or Error object")
     */
    public function processAction(){


    }

    /**
     * @title("comment")
     * @description("Order comment")
     * @requestExample("POST /order/comment")
     * @response("Data object or Error object")
     */
    public function commentAction(){


    }

    private function genOrderId($userid){
        list($tmp1, $tmp2) = explode(' ', microtime());

        $msec =  (String)((int)sprintf('%.0f', (floatval($tmp1) + floatval($tmp2)) * 10000));

        $date = date("Ymd");
        $prefix = "37";
        return $prefix.$userid.$date.substr($msec, 4, 10);
    }

    private function genCargosObj($cargoStr){
        $cargoObjs = [];
        $cargoArrs = explode(';', $cargoStr);

        foreach($cargoArrs as $cargoAttr){
            $cargo = explode(';', $cargoAttr);

            $cargoObj['type'] = $cargo[0];
            $cargoObj['num'] = (int)$cargo[1];

            array_push($cargoObjs, $cargoObj);
        }

        return $cargoObjs;

    }

}