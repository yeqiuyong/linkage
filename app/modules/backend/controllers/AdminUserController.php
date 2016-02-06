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

class AdminUserController extends BackendControllerBase
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
        $page_num = ($currentPage == null) ? 1 : $currentPage;

        // The data set to paginate
        $results = [];
        $users = AdminUser::find();
        foreach ($users as $user) {
            $result = [];
            $result['username'] = $user->username;
            $result['create_time'] = $user->create_time;
            $result['active'] = $user->active;
            $result['profile_name'] = $user->profile->profile_name;

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

    public function addAction(){
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $realname = $this->request->getPost('realname');
        $mobile = $this->request->getPost('mobile');
        $email = $this->request->getPost('email');

        $adminUser = new AdminUser();
        try{
            $adminUser->add($username, $password, $realname, $mobile , $email);
            return $this->forward('adminuser/index');
        }catch (UserOperationException $e){
            echo "用户名已经被注册";
        }catch (DataBaseException $e){
            echo "数据库错误";
        }


    }
}
