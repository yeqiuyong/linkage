<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 14/4/16
 * Time: 4:19 PM
 */


namespace Multiple\Backend\Controllers;

use Multiple\Core\Constants\LinkageUtils;
use Multiple\Models\Order;
use Phalcon\Di;
use Phalcon\Paginator\Adapter\NativeArray as PaginatorArray;

use Multiple\Core\BackendControllerBase;
use Multiple\Core\Constants\ErrorCodes;
use Multiple\Core\Exception\UserOperationException;
use Multiple\Models\AdminUser;
use Multiple\Models\Notice;


class OrderController extends BackendControllerBase
{
    public function initialize()
    {
        $this->tag->setTitle('Welcome');
        parent::initialize();
    }

    public function indexAction()
    {
        $order = new Order();
        $countsGroupByType = $order->getCountsGroupByType();
        $placeOrderCounts = $order->getPlaceOrderCounts();
        $acceptOrderCounts = $order->getAcceptOrderCounts();

        $exportCnt = 0;
        $importCnt = 0;
        $selfCnt = 0;
        foreach($countsGroupByType as $countGroupByType){
            switch($countGroupByType['order_type']){
                case LinkageUtils::ORDER_TYPE_EXPORT : $exportCnt = $countGroupByType['order_num'];break;
                case LinkageUtils::ORDER_TYPE_IMPORT : $importCnt = $countGroupByType['order_num'];break;
                case LinkageUtils::ORDER_TYPE_SELF : $selfCnt = $countGroupByType['order_num'];break;
                default: $exportCnt = $countGroupByType['order_num'];break;
            }
        }
        $totalCnt = $exportCnt + $importCnt + $selfCnt;

        $this->view->setVars(
            array(
                'totalCnt' => $totalCnt,
                'exportCnt' => $exportCnt,
                'importCnt' => $importCnt,
                'selfCnt' => $selfCnt,
                'placeOrderCounts' => $placeOrderCounts,
                'acceptOrderCounts' => $acceptOrderCounts,
            )
        );


    }

    public function listAction(){
        // Current page to show
        // In a controller this can be:
        // $this->request->getQuery('page', 'int'); // GET
        $currentPage = $this->request->getPost('pageindex', 'int'); // POST
        $pageNum = ($currentPage == null) ? 1 : $currentPage;

        // The data set to paginate
        $advertise = new Notice();
        $results = $advertise->getAdv4Admin();

        // Create a Model paginator, show 10 rows by page starting from $currentPage
        $paginator = new PaginatorArray(
            array(
                "data"  => $results,
                "limit" => 10,
                "page"  => $pageNum
            )
        );

        // Get the paginated results
        $page = $paginator->getPaginate();

        return $this->response->setJsonContent($page);

    }

    public function detailAction(){
        $id = $this->request->get('id', 'int'); // POST

        $advertise = new Notice();
        $adv = $advertise->getAdvById($id);

        return $this->response->setJsonContent($adv);

    }

    public function addAction(){
        $title = $this->request->getPost('title', 'string');
        $link = $this->request->getPost('link', 'string');
        $description = $this->request->getPost('description', 'string');
        $memo = $this->request->getPost('memo', 'string');

        $advertise = new Notice();
        $admin = new AdminUser();

        $image = $this->upload2Upyun();

        if(empty($image) || empty($title) || empty($link)){
            throw new UserOperationException(ErrorCodes::GEN_INPUT_ERROR, ErrorCodes::$MESSAGE[ErrorCodes::GEN_INPUT_ERROR]);
        }


        $adminInfo = $admin->getUserByName('admin');

        $advertise->addAdv($title, $link, $description, $memo, $image, $adminInfo->admin_id);

        $url = $this->url->get('admin/advertise/index');
        return $this->response->redirect($url);

    }

    public function updateAction(){
        $id =  $this->request->getPost('id-editor-modal', 'int');
        $title = $this->request->getPost('title-editor-modal', 'string');
        $link = $this->request->getPost('link-editor-modal', 'string');
        $description = $this->request->getPost('description-editor-modal', 'string');
        $memo = $this->request->getPost('memo-editor-modal', 'string');

        $advertise = new Notice();
        $admin = new AdminUser();

        $image = $this->upload2Upyun();
        $adminInfo = $admin->getUserByName('admin');

        $advertise->updateNotice($id, $title, $link, $description, $memo, $image, $adminInfo->admin_id);

        $url = $this->url->get('admin/advertise/index');
        return $this->response->redirect($url);

    }

    public function changeStatusAction(){
        $advId = $this->request->getPost('id', 'int'); // POST
        $status = $this->request->getPost('status', 'int'); // POST

        $advertise = new Notice();
        $advertise->updateStatus($advId, $status);

        return $this->responseJsonOK();
    }

}