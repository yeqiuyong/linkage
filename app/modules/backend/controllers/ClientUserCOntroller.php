<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 14/2/16
 * Time: 3:32 PM
 */


namespace Multiple\Backend\Controllers;

use Multiple\Core\BackendControllerBase;
use Multiple\Core\Exception\DataBaseException;
use Multiple\Core\Exception\UserOperationException;
use Multiple\Models\AdminUser;

use Multiple\Models\ClientUser;
use Phalcon\Paginator\Adapter\NativeArray as PaginatorArray;

class ClientUserController extends BackendControllerBase
{
    public function initialize(){
        $this->tag->setTitle('Welcome');
        parent::initialize();
    }

    public function indexAction(){

    }

    public function manufacturesAction(){
        $currentPage = $this->request->getPost('pageindex', 'int'); // POST
        $page_num = ($currentPage == null) ? 1 : $currentPage;

        // The data set to paginate
        $results = [];
        $users = ClientUser::find();
        foreach ($users as $user) {
            $result = [];
            $result['username'] = $user->username;
            $result['mobile'] = $user->mobile;
            $result['create_time'] = $user->create_time;
            $result['active'] = $user->active;

            array_push($results,$result);
        }

        // Create a Model paginator, show 10 rows by page starting from $currentPage
        $paginator = new PaginatorArray(
            array(
                "data"  => $results,
                "limit" => 10,
                "page"  => $page_num
            )
        );

        // Get the paginated results
        $page = $paginator->getPaginate();

        return $this->response->setJsonContent($page);

    }

    public function transportersAction(){

    }

    public function driversAction(){

    }
}