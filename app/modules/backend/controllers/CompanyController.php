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
use Multiple\Core\Constants\LinkageUtils;
use Multiple\Core\Exception\Exception;
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

    public function detailAction(){
        $companyId = $this->request->get('id', 'int'); // POST

        $this->view->setVars(
            array(
                'company_id'   => $companyId,
            )
        );
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
        $companyID = $this->request->getPost('id', 'int'); // POST

        $company = new Company();
        $information = $company->getCompanyInformation($companyID);

        $type= $information['type'] == LinkageUtils::COMPANY_MANUFACTURE ? "厂商" : "承运商";
        $createTime = date('Y-m-d',$information['create_time']);

        unset ($information['type']);
        unset ($information['create_time']);

        $information['type'] = $type;
        $information['create_time'] = $createTime;

        return $this->response->setJsonContent($information);
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