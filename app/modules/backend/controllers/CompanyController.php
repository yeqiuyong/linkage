<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 21/2/16
 * Time: 2:04 PM
 */

namespace Multiple\Backend\Controllers;

use Phalcon\Paginator\Adapter\NativeArray as PaginatorArray;

use Multiple\Core\BackendControllerBase;
use Multiple\Models\ClientUser;
use Multiple\Models\Company;


class CompanyController extends BackendControllerBase
{
    public function initialize(){
        $this->tag->setTitle('Welcome');
        parent::initialize();
    }

    public function indexAction(){

    }

    public function manufacturesAction(){
        $currentPage = $this->request->getPost('pageindex', 'int'); // POST
        $pageNum = ($currentPage == null) ? 1 : $currentPage;

        $company = new Company();
        $companies = $company->getCompaniesByType(0);

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

        $company = new Company();
        $companies = $company->getCompaniesByType(1);

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


    public function informationAction(){
        $companyID = $this->request->getQuery('id', 'int'); // POST

        $company = new Company();
        $information = $company->getCompanyInformation($companyID);

        $type= $information['type'] == 0 ? "厂商" : "承运商";

        $this->view->setVars(
            array(
                'name' => $information['name'],
                'type' => $type,
                'contactor' => $information['contactor'],
                'address' => $information['address'],
                'email' => $information['email'],
                'service_phone1' => $information['service_phone_1'],
                'service_phone2' => $information['service_phone_2'],
                'service_phone3' => $information['service_phone_3'],
                'service_phone4' => $information['service_phone_4'],
                'description' => $information['description'],
                'update_time' =>date('Y-m-d',$information['update_time']),
                'status' => $information['status'],
                'level' => $information['level'],
                'credit' => $information['credit'],
            )
        );
    }

    public function changeStatusAction(){
        $companyId = $this->request->getPost('id', 'int'); // POST
        $status = $this->request->getPost('status', 'int'); // POST

        try{
            $company = new Company();
            $company->updateStatus($companyId, $status);

            $user = new ClientUser();
            $user->updateStatus($userid, $status);

            if($user->isAdmin($userid)){



            }

        }catch (Exception $e){
            return$this->responseJsonError($e->getCode(), $e->getMessage());
        }

        return $this->responseJsonOK();
    }

}