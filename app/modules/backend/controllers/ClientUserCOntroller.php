<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 14/2/16
 * Time: 3:32 PM
 */


namespace Multiple\Backend\Controllers;

use Phalcon\Paginator\Adapter\Model as PaginatorModel;

use Multiple\Core\BackendControllerBase;
use Multiple\Models\ClientUser;

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
        $pageNum = ($currentPage == null) ? 1 : $currentPage;

        // The data set to paginate
        $phql="select a.user_id, a.username, a.mobile, a.create_time, a.status from Multiple\Models\ClientUser a join Multiple\Models\ClientUserRole b where a.user_id = b.user_id and b.role_id = 1";
        $users=$this->modelsManager->executeQuery($phql);

        // Create a Model paginator, show 10 rows by page starting from $currentPage
        $paginator = new PaginatorModel(
            array(
                "data"  => $users,
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

        // The data set to paginate
        $phql="select a.user_id, a.username, a.mobile, a.create_time, a.status from Multiple\Models\ClientUser a join Multiple\Models\ClientUserRole b where a.user_id = b.user_id and b.role_id in (2,3)";
        $users=$this->modelsManager->executeQuery($phql);

        // Create a Model paginator, show 10 rows by page starting from $currentPage
        $paginator = new PaginatorModel(
            array(
                "data"  => $users,
                "limit" => 10,
                "page"  => $pageNum
            )
        );

        // Get the paginated results
        $page = $paginator->getPaginate();

        return $this->response->setJsonContent($page);
    }

    public function driversAction(){
        $currentPage = $this->request->getPost('pageindex', 'int'); // POST
        $pageNum = ($currentPage == null) ? 1 : $currentPage;

        // The data set to paginate
        $phql="select a.user_id, a.username, a.mobile, a.create_time, a.status from Multiple\Models\ClientUser a join Multiple\Models\ClientUserRole b where a.user_id = b.user_id and b.role_id = 4";
        $users=$this->modelsManager->executeQuery($phql);

        // Create a Model paginator, show 10 rows by page starting from $currentPage
        $paginator = new PaginatorModel(
            array(
                "data"  => $users,
                "limit" => 10,
                "page"  => $pageNum
            )
        );

        // Get the paginated results
        $page = $paginator->getPaginate();

        return $this->response->setJsonContent($page);
    }

    public function informationAction(){
        $userid = $this->request->getQuery('id', 'int'); // POST

        $user = new ClientUser();
        $information = $user->getUserInfomation($userid);

        $this->view->setVars(
            array(
                'username' => $information['username'],
                'realname' => $information['realname'],
                'mobile' => $information['mobile'],
                'email' => $information['email'],
                'profile_name' => $information['role'],
                'update_time' =>date('Y-m-d', $information['update_time']),
            )
        );
    }

    public function editorAction(){

    }

    public function changeStatusAction(){
        $userid = $this->request->getPost('id', 'int'); // POST
        $status = $this->request->getPost('status', 'int'); // POST

        $user = new ClientUser();
        $user->updateStatus($userid, $status);

        return $this->response->setJsonContent('ok');
    }

}