<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 14/4/16
 * Time: 4:19 PM
 */


namespace Multiple\Backend\Controllers;

use Multiple\Models\Company;
use Phalcon\Di;
use Phalcon\Paginator\Adapter\NativeArray as PaginatorArray;

use Multiple\Core\BackendControllerBase;
use Multiple\Core\Constants\LinkageUtils;
use Multiple\Models\Order;


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

    public function exportAction(){

    }

    public function importAction(){

    }

    public function selfAction(){

    }

    public function orderCountPerMonAction(){
        $date = $this->request->getPost('date_offset', 'int'); // POST

        $order = new Order();
        $orderCountsPerMon = $order->getOrderCountPerMon($date);

        $exportCountsPerMon = [];
        $importCountsPerMon = [];
        $selfCountsPerMon = [];
        foreach ($orderCountsPerMon as $orderCount) {
            $result['order_date'] = $orderCount->order_date;
            $result['order_num'] = $orderCount->count;

            switch($orderCount->type){
                case LinkageUtils::ORDER_TYPE_EXPORT : array_push($exportCountsPerMon, $result);break;
                case LinkageUtils::ORDER_TYPE_IMPORT : array_push($importCountsPerMon, $result);break;
                case LinkageUtils::ORDER_TYPE_SELF : array_push($selfCountsPerMon, $result);break;
                default: array_push($exportCountsPerMon, $result);break;
            }
        }

        $countArray = [
            "offset" => $date,
            'export' => $exportCountsPerMon,
            'import' => $importCountsPerMon,
            'self' => $selfCountsPerMon,
        ];

        return $this->response->setJsonContent($countArray);
    }

    public function getManufactureOrderListAction(){
        // Current page to show
        // In a controller this can be:
        // $this->request->getQuery('page', 'int'); // GET
        $currentPage = $this->request->getPost('pageindex', 'int'); // POST
        $orderType = $this->request->getPost('order_type', 'int'); // POST
        $pageNum = ($currentPage == null) ? 1 : $currentPage;

        // The data set to paginate
        $order = new Order();
        $result = $order->getPlaceOrderCountsByType($orderType);

        // Create a Model paginator, show 10 rows by page starting from $currentPage
        $paginator = new PaginatorArray(
            array(
                "data"  => $result,
                "limit" => 10,
                "page"  => $pageNum
            )
        );

        // Get the paginated results
        $page = $paginator->getPaginate();

        return $this->response->setJsonContent($page);
    }

    public function getTransporterOrderListAction(){
        // Current page to show
        // In a controller this can be:
        // $this->request->getQuery('page', 'int'); // GET
        $currentPage = $this->request->getPost('pageindex', 'int'); // POST
        $orderType = $this->request->getPost('order_type', 'int'); // POST
        $pageNum = ($currentPage == null) ? 1 : $currentPage;

        // The data set to paginate
        $order = new Order();
        $result = $order->getAcceptOrderCountsByType($orderType);

        // Create a Model paginator, show 10 rows by page starting from $currentPage
        $paginator = new PaginatorArray(
            array(
                "data"  => $result,
                "limit" => 10,
                "page"  => $pageNum
            )
        );

        // Get the paginated results
        $page = $paginator->getPaginate();

        return $this->response->setJsonContent($page);
    }

    public function orderCnt4CompanyByTypeAction(){
        $companyId = $this->request->getPost('company_id', 'int'); // POST

        $company = new Company();
        $order = new Order();

        $companyInfo = $company->getCompanyInformation($companyId);
        $countsGroupByType = $order->getCountsGroupByType4Company($companyId, $companyInfo['type']);

        $exportCnt = 0;
        $importCnt = 0;
        $selfCnt = 0;
        foreach ($countsGroupByType as $orderCount) {
            $result['order_date'] = $orderCount->order_date;
            $result['order_num'] = $orderCount->count;

            switch($orderCount->type){
                case LinkageUtils::ORDER_TYPE_EXPORT : $exportCnt = $orderCount->rowcount;break;
                case LinkageUtils::ORDER_TYPE_IMPORT : $importCnt = $orderCount->rowcount;break;
                case LinkageUtils::ORDER_TYPE_SELF : $selfCnt = $orderCount->rowcount;break;
                default: $exportCnt = $orderCount->rowcount;break;
            }
        }

        $result = [
            'exportCnt' => $exportCnt,
            'importCnt' => $importCnt,
            'selfCnt' => $selfCnt,
        ];

        return $this->response->setJsonContent($result);
    }

    public function orderCnt4CompanyByMonAction(){
        $companyId = $this->request->getPost('company_id', 'int'); // POST

        $company = new Company();
        $order = new Order();

        $companyInfo = $company->getCompanyInformation($companyId);
        $countsGroupByMon = $order->getOrderCountPerMon4Company($companyId, $companyInfo['type']);

        $results = [];
        foreach ($countsGroupByMon as $orderCount) {
            $result['order_date'] = $orderCount->order_date;
            $result['order_num'] = $orderCount->count;

            array_push($results, $result);
        }

        $countArray = [
            "offset" => time(),
            'count_group' => $results,

        ];

        return $this->response->setJsonContent($countArray);

    }
}