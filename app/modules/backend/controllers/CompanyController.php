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
use Multiple\Models\Company;

class CompanyController extends BackendControllerBase
{
    public function initialize()
    {
        $this->tag->setTitle('Welcome');
        parent::initialize();
    }

    public function indexAction()
    {

    }

    public function manufacturesAction(){
        $currentPage = $this->request->getPost('pageindex', 'int'); // POST
        $pageNum = ($currentPage == null) ? 1 : $currentPage;

        // The data set to paginate
        $companies = $this->getCompanies(0);

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

        $companies = $this->getCompanies(1);

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

    private function getCompanies($type){
        // The data set to paginate
        $results = [];

        $conditions = "type = :type:";
        $parameters = array(
            "type" => $type
        );
        $companies = Company::find(
            array(
                $conditions,
                "bind" => $parameters
            )
        );

        foreach ($companies as $company) {
            $result = [];
            $result['id'] = $company->company_id;
            $result['name'] = $company->name;
            $result['contact'] = $company->contactor;
            $result['phone'] = $company->service_phone_1;
            $result['create_time'] = $company->create_time;
            $result['status'] = $company->status;

            array_push($results,$result);
        }

        return $results;
    }
}