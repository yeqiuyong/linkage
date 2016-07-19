<?php
/**
 * Created by PhpStorm.
 * User: whoami
 * Date: 16-7-4
 * Time: 下午2:42
 */

namespace Multiple\Backend\Controllers;

use Phalcon\Paginator\Adapter\NativeArray as PaginatorArray;

use Multiple\Core\BackendControllerBase;
use Multiple\Core\Constants\LinkageUtils;
use Multiple\Core\Exception\Exception;
use Multiple\Models\ClientUser;
use Multiple\Models\Company;
use Multiple\Models\Order;

require APP_PATH . 'app/core/libraries/PHPExcel/Classes/PHPExcel.php';

class ExportController extends BackendControllerBase
{
    public function initialize(){
        $this->tag->setTitle('Welcome');
        parent::initialize();
    }

    public function indexAction(){

    }

    public function manuorderAction(){
        $id = $this->request->get('id', 'int')?$this->request->get('id', 'int'):0;
        $company = new Company();
        $companyInfo = $company->getCompanyByid($id);
        $this->view->setVar("id", $id);
        $this->view->setVar("company_name", $companyInfo['company_name']);
    }

    public function transorderAction(){
        $id = $this->request->get('id', 'int')?$this->request->get('id', 'int'):0;
        $company = new Company();
        $companyInfo = $company->getCompanyByid($id);
        $this->view->setVar("id", $id);
        $this->view->setVar("company_name", $companyInfo['company_name']);
    }

    public function manufacturesAction(){
        $currentPage = $this->request->getPost('pageindex', 'int'); // POST
        $pageNum = ($currentPage == null) ? 1 : $currentPage;
        $start_time = $this->request->getPost('start_time', 'string')?$this->request->getPost('start_time', 'string'):'';
        $end_time = $this->request->getPost('end_time', 'string')?$this->request->getPost('end_time', 'string'):'';

        $company = new Company();
        if($start_time != '' && $end_time != ''){
            $start_time = $this->getTime($start_time);
            $end_time = $this->getTime($end_time);
            $companies = $company->getCompaniesByType(LinkageUtils::COMPANY_MANUFACTURE,$start_time,$end_time);
        }else{
            $companies = $company->getCompaniesByType(LinkageUtils::COMPANY_MANUFACTURE);
        }



        // Create a Model paginator, show 10 rows by page starting from $currentPage
        $paginator = new PaginatorArray(
            array(
                "data"  => $companies,
                "limit" => 10,
                "page"  => $pageNum
            )
        );

        // Get the paginated results
        $page = $paginator->getPaginate();

        return $this->response->setJsonContent($page);
    }

    public function transportersAction(){
        $currentPage = $this->request->getPost('pageindex', 'int'); // POST
        $pageNum = ($currentPage == null) ? 1 : $currentPage;
        $start_time = $this->request->getPost('start_time', 'string')?$this->request->getPost('start_time', 'string'):'';
        $end_time = $this->request->getPost('end_time', 'string')?$this->request->getPost('end_time', 'string'):'';

        $company = new Company();
        if($start_time != '' && $end_time != ''){
            $start_time = $this->getTime($start_time);
            $end_time = $this->getTime($end_time);
            $companies = $company->getCompaniesByType(LinkageUtils::COMPANY_TRANSPORTER,$start_time,$end_time);
        }else{
            $companies = $company->getCompaniesByType(LinkageUtils::COMPANY_TRANSPORTER);
        }

        // Create a Model paginator, show 10 rows by page starting from $currentPage
        $paginator = new PaginatorArray(
            array(
                "data"  => $companies,
                "limit" => 10,
                "page"  => $pageNum
            )
        );

        // Get the paginated results
        $page = $paginator->getPaginate();

        return $this->response->setJsonContent($page);
    }
    //获取指定厂商订单
    public function manuOrderListAction(){
        $currentPage = $this->request->getPost('pageindex', 'int'); // POST
        $pageNum = ($currentPage == null) ? 1 : $currentPage;
        $id = $this->request->getPost('id', 'int')?$this->request->getPost('id', 'int'):0;
        $start_time = $this->request->getPost('start_time', 'string')?$this->request->getPost('start_time', 'string'):'';
        $end_time = $this->request->getPost('end_time', 'string')?$this->request->getPost('end_time', 'string'):'';

        $order = new Order();
        if($start_time != '' && $end_time != ''){
            $start_time = $this->getTime($start_time);
            $end_time = $this->getTime($end_time);
            $orders = $order->getManureOrder4admin($id, $start_time,$end_time);
        }else{
            $orders = $order->getManureOrder4admin($id);
        }
        // Create a Model paginator, show 10 rows by page starting from $currentPage
        $paginator = new PaginatorArray(
            array(
                "data"  => $orders,
                "limit" => 10,
                "page"  => $pageNum
            )
        );

        // Get the paginated results
        $page = $paginator->getPaginate();

        return $this->response->setJsonContent($page);
    }

    //获取指定承运商订单
    public function transOrderListAction(){
        $currentPage = $this->request->getPost('pageindex', 'int'); // POST
        $pageNum = ($currentPage == null) ? 1 : $currentPage;
        $id = $this->request->getPost('id', 'int')?$this->request->getPost('id', 'int'):0;
        $start_time = $this->request->getPost('start_time', 'string')?$this->request->getPost('start_time', 'string'):'';
        $end_time = $this->request->getPost('end_time', 'string')?$this->request->getPost('end_time', 'string'):'';

        $order = new Order();
        if($start_time != '' && $end_time != ''){
            $start_time = $this->getTime($start_time);
            $end_time = $this->getTime($end_time);
            $orders = $order->getTransporterOrder4admin($id, $start_time,$end_time);
        }else{
            $orders = $order->getTransporterOrder4admin($id);
        }
        // Create a Model paginator, show 10 rows by page starting from $currentPage
        $paginator = new PaginatorArray(
            array(
                "data"  => $orders,
                "limit" => 10,
                "page"  => $pageNum
            )
        );

        // Get the paginated results
        $page = $paginator->getPaginate();

        return $this->response->setJsonContent($page);
    }

    public function getTime($time){
        $time_arr = explode(" ",$time);
        switch($time_arr[1]){
            case 'January': $time = $time_arr[2].'-01-'.$time_arr[0];break;
            case 'February': $time = $time_arr[2].'-02-'.$time_arr[0];break;
            case 'March': $time = $time_arr[2].'-03-'.$time_arr[0];break;
            case 'April': $time = $time_arr[2].'-04-'.$time_arr[0];break;
            case 'May': $time = $time_arr[2].'-05-'.$time_arr[0];break;
            case 'June': $time = $time_arr[2].'-06-'.$time_arr[0];break;
            case 'July': $time = $time_arr[2].'-07-'.$time_arr[0];break;
            case 'August': $time = $time_arr[2].'-08-'.$time_arr[0];break;
            case 'September': $time = $time_arr[2].'-09-'.$time_arr[0];break;
            case 'October': $time = $time_arr[2].'-10-'.$time_arr[0];break;
            case 'November': $time = $time_arr[2].'-11-'.$time_arr[0];break;
            default: $time = $time_arr[2].'-12-'.$time_arr[0];
        }
        $time = strtotime($time);
        return $time;
    }

    //导出厂商列表
    public function createManufacturesAction(){
        $start_time = $this->request->get('start_time', 'string')?$this->request->get('start_time', 'string'):'';
        $end_time = $this->request->get('end_time', 'string')?$this->request->get('end_time', 'string'):'';
        //获取数据
        $company = new Company();
        if($start_time != '' && $end_time != ''){
            $start_time = $this->getTime($start_time);
            $end_time = $this->getTime($end_time);
            $companies = $company->getCompaniesByType(LinkageUtils::COMPANY_MANUFACTURE,$start_time,$end_time);
        }else{
            $companies = $company->getCompaniesByType(LinkageUtils::COMPANY_MANUFACTURE);
        }

        $pexcel = new \PHPExcel();

        $pexcel->getProperties()->setCreator("WY")
            ->setLastModifiedBy("WY")
            ->setTitle("厂商管理列表")
            ->setSubject("厂商列表")
            ->setDescription("manufactures list")
            ->setKeywords("厂商")
            ->setCategory("list");

        $pexcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '序号')
            ->setCellValue('B1', '公司名称')
            ->setCellValue('C1', '公司联系人')
            ->setCellValue('D1', '手机')
            ->setCellValue('E1', '创建时间')
            ->setCellValue('F1', '状态')
        ;

        $pexcel->getActiveSheet()->setTitle('厂商列表');

        foreach ($companies as $key => $company) {
            switch($company['status']){
                case 0: $status = 'active';break;
                case 1: $status = 'inactive';break;
                case 2: $status = 'pending';break;
                default:$status = 'banned';

            }
            $key = $key+2;
            $pexcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$key, $key-1)
                ->setCellValue('B'.$key, $company['name'])
                ->setCellValue('C'.$key, $company['contact'])
                ->setCellValue('D'.$key, $company['phone'])
                ->setCellValue('E'.$key, date('Y-m-d', $company['create_time']))
                ->setCellValue('F'.$key, $status);

        }

        $pexcel->setActiveSheetIndex(0);

        $pexcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(18);
        $pexcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(15);
        $pexcel->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
        $pexcel->getActiveSheet()->getColumnDimension('B')->setWidth('35');
        $pexcel->getActiveSheet()->getColumnDimension('E')->setWidth('20');
        $pexcel->getActiveSheet()->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $filename = '厂商列表_'.date('Y-m-d', time());

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');

        $objWriter = \PHPExcel_IOFactory::createWriter($pexcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    //导出承运商列表
    public function createTransportersAction(){
        $start_time = $this->request->get('start_time', 'string')?$this->request->get('start_time', 'string'):'';
        $end_time = $this->request->get('end_time', 'string')?$this->request->get('end_time', 'string'):'';
        //获取数据
        $company = new Company();
        if($start_time != '' && $end_time != ''){
            $start_time = $this->getTime($start_time);
            $end_time = $this->getTime($end_time);
            $companies = $company->getCompaniesByType(LinkageUtils::COMPANY_TRANSPORTER,$start_time,$end_time);
        }else{
            $companies = $company->getCompaniesByType(LinkageUtils::COMPANY_TRANSPORTER);
        }

        $pexcel = new \PHPExcel();

        $pexcel->getProperties()->setCreator("WY")
            ->setLastModifiedBy("WY")
            ->setTitle("承运商管理列表")
            ->setSubject("承运商列表")
            ->setDescription("transporters list")
            ->setKeywords("承运商")
            ->setCategory("list");

        $pexcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '序号')
            ->setCellValue('B1', '公司名称')
            ->setCellValue('C1', '公司联系人')
            ->setCellValue('D1', '手机')
            ->setCellValue('E1', '创建时间')
            ->setCellValue('F1', '状态')
        ;

        $pexcel->getActiveSheet()->setTitle('承运商列表');

        foreach ($companies as $key => $company) {
            switch($company['status']){
                case 0: $status = 'active';break;
                case 1: $status = 'inactive';break;
                case 2: $status = 'pending';break;
                default:$status = 'banned';

            }
            $key = $key+2;
            $pexcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$key, $key-1)
                ->setCellValue('B'.$key, $company['name'])
                ->setCellValue('C'.$key, $company['contact'])
                ->setCellValue('D'.$key, $company['phone'])
                ->setCellValue('E'.$key, date('Y-m-d', $company['create_time']))
                ->setCellValue('F'.$key, $status);

        }

        $pexcel->setActiveSheetIndex(0);

        $pexcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(18);
        $pexcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(15);
        $pexcel->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
        $pexcel->getActiveSheet()->getColumnDimension('B')->setWidth('35');
        $pexcel->getActiveSheet()->getColumnDimension('E')->setWidth('20');
        $pexcel->getActiveSheet()->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $filename = '承运商列表_'.date('Y-m-d', time());

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');

        $objWriter = \PHPExcel_IOFactory::createWriter($pexcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    //导出指定厂商列表
    public function createManuOrderAction(){
        $start_time = $this->request->get('start_time', 'string')?$this->request->get('start_time', 'string'):'';
        $end_time = $this->request->get('end_time', 'string')?$this->request->get('end_time', 'string'):'';
        $id = $this->request->get('id', 'int')?$this->request->get('id', 'int'):0;
        if(!empty($id) && $id != 0){
            $company = new Company();
            $companyInfo = $company->getCompanyByid($id);
        }
        //获取数据
        $order = new Order();
        if($start_time != '' && $end_time != ''){
            $start_time = $this->getTime($start_time);
            $end_time = $this->getTime($end_time);
            $orders = $order->getManureOrder4admin($id, $start_time,$end_time);
        }else{
            $orders = $order->getManureOrder4admin($id);
        }

        $pexcel = new \PHPExcel();

        $pexcel->getProperties()->setCreator("WY")
            ->setLastModifiedBy("WY")
            ->setTitle($companyInfo['company_name'])
            ->setSubject($companyInfo['company_name'])
            ->setDescription("Order list")
            ->setKeywords("Order")
            ->setCategory("list");

        $pexcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '序号')
            ->setCellValue('B1', '订单编号')
            ->setCellValue('C1', '订单类型')
            ->setCellValue('D1', '下单公司')
            ->setCellValue('E1', '下单时间')
            ->setCellValue('F1', '接单公司')
            ->setCellValue('G1', '订单状态')
        ;

        $pexcel->getActiveSheet()->setTitle($companyInfo['company_name']);

        foreach ($orders as $key => $order) {
            $key = $key+2;
            $pexcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$key, $key-1)
                ->setCellValue('B'.$key, $order['order_id'])
                ->setCellValue('C'.$key, $order['type'])
                ->setCellValue('D'.$key, $companyInfo['company_name'])
                ->setCellValue('E'.$key, date('Y-m-d H:i:s', $order['create_time']))
                ->setCellValue('F'.$key, $order['company_name'])
                ->setCellValue('G'.$key, $order['status']);
            $pexcel->getActiveSheet()->setCellValueExplicit('B'.$key,$order['order_id'],\PHPExcel_Cell_DataType::TYPE_STRING);

        }

        $pexcel->setActiveSheetIndex(0);

        $pexcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(18);
        $pexcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(15);
        $pexcel->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
        $pexcel->getActiveSheet()->getColumnDimension('B')->setWidth('35');
        $pexcel->getActiveSheet()->getColumnDimension('E')->setWidth('30');
        $pexcel->getActiveSheet()->getColumnDimension('F')->setWidth('35');
        $pexcel->getActiveSheet()->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
       //pexcel->getActiveSheet()->setCellValueExplicit('B',PHPExcel_Cell_DataType::TYPE_STRING);

        $filename = $companyInfo['company_name'].'_'.date('Y-m-d', time());

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');

        $objWriter = \PHPExcel_IOFactory::createWriter($pexcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    //导出指定承运商订单列表
    public function createTransOrderAction(){
        $start_time = $this->request->get('start_time', 'string')?$this->request->get('start_time', 'string'):'';
        $end_time = $this->request->get('end_time', 'string')?$this->request->get('end_time', 'string'):'';
        $id = $this->request->get('id', 'int')?$this->request->get('id', 'int'):0;
        if(!empty($id) && $id != 0){
            $company = new Company();
            $companyInfo = $company->getCompanyByid($id);
        }
        //获取数据
        $order = new Order();
        if($start_time != '' && $end_time != ''){
            $start_time = $this->getTime($start_time);
            $end_time = $this->getTime($end_time);
            $orders = $order->getTransporterOrder4admin($id, $start_time,$end_time);
        }else{
            $orders = $order->getTransporterOrder4admin($id);
        }

        $pexcel = new \PHPExcel();

        $pexcel->getProperties()->setCreator("WY")
            ->setLastModifiedBy("WY")
            ->setTitle($companyInfo['company_name'])
            ->setSubject($companyInfo['company_name'])
            ->setDescription("Order list")
            ->setKeywords("Order")
            ->setCategory("list");

        $pexcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '序号')
            ->setCellValue('B1', '订单编号')
            ->setCellValue('C1', '订单类型')
            ->setCellValue('D1', '下单公司')
            ->setCellValue('E1', '下单时间')
            ->setCellValue('F1', '接单公司')
            ->setCellValue('G1', '订单状态')
        ;

        $pexcel->getActiveSheet()->setTitle($companyInfo['company_name']);

        foreach ($orders as $key => $order) {
            $key = $key+2;
            $pexcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$key, $key-1)
                ->setCellValue('B'.$key, $order['order_id'])
                ->setCellValue('C'.$key, $order['type'])
                ->setCellValue('D'.$key, $order['company_name'])
                ->setCellValue('E'.$key, date('Y-m-d H:i:s', $order['create_time']))
                ->setCellValue('F'.$key, $companyInfo['company_name'])
                ->setCellValue('G'.$key, $order['status']);
            $pexcel->getActiveSheet()->setCellValueExplicit('B'.$key,$order['order_id'],\PHPExcel_Cell_DataType::TYPE_STRING);

        }

        $pexcel->setActiveSheetIndex(0);

        $pexcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(18);
        $pexcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(15);
        $pexcel->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
        $pexcel->getActiveSheet()->getColumnDimension('B')->setWidth('35');
        $pexcel->getActiveSheet()->getColumnDimension('E')->setWidth('30');
        $pexcel->getActiveSheet()->getColumnDimension('F')->setWidth('35');
        $pexcel->getActiveSheet()->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //pexcel->getActiveSheet()->setCellValueExplicit('B',PHPExcel_Cell_DataType::TYPE_STRING);

        $filename = $companyInfo['company_name'].'_'.date('Y-m-d', time());

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');

        $objWriter = \PHPExcel_IOFactory::createWriter($pexcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

}