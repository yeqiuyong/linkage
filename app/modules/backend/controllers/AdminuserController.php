<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 4/2/16
 * Time: 4:25 PM
 */

namespace Multiple\Backend\Controllers;

use Multiple\Core\BackendControllerBase;
use Multiple\Core\Exception\DataBaseException;
use Multiple\Core\Exception\UserOperationException;
use Multiple\Models\AdminUser;

use Phalcon\Paginator\Adapter\NativeArray as PaginatorArray;

class AdminuserController extends BackendControllerBase
{
    public function initialize()
    {
        $this->tag->setTitle('Welcome');
        parent::initialize();
    }

    public function indexAction(){

    }

    public function listAction(){
        // Current page to show
        // In a controller this can be:
        // $this->request->getQuery('page', 'int'); // GET
        $currentPage = $this->request->getPost('pageindex', 'int'); // POST
        $pageNum = ($currentPage == null) ? 1 : $currentPage;

        // The data set to paginate
        $adminUser = new AdminUser();
        $admins = $adminUser->getAdmins();

        // Create a Model paginator, show 10 rows by page starting from $currentPage
        $paginator = new PaginatorArray(
            array(
                "data"  => $admins,
                "limit" => 10,
                "page"  => $pageNum
            )
        );

        // Get the paginated results
        $page = $paginator->getPaginate();

        return $this->response->setJsonContent($page);

    }

    public function addAction(){
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $realname = $this->request->getPost('realname');
        $mobile = $this->request->getPost('mobile');
        $email = $this->request->getPost('email');

        $adminUser = new AdminUser();
        $adminUser->add($username, $password, $realname, $mobile , $email);

        return $this->forward('adminuser/index');

    }

    public function detailAction(){
        $id = $this->request->get('id', 'int'); // POST

        $adminUser = new AdminUser();
        $admin = $adminUser->getUserById($id);

        return $this->response->setJsonContent($admin);

    }

    public function changeStatusAction(){
        $adminId = $this->request->getPost('id', 'int'); // POST
        $status = $this->request->getPost('status', 'int'); // POST

        $adminUser = new AdminUser();
        $adminUser->updateStatus($adminId, $status);

        return $this->responseJsonOK();
    }
}
