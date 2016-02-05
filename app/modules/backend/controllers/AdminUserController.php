<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 4/2/16
 * Time: 4:25 PM
 */


namespace Multiple\Backend\Controllers;

use Multiple\Core\BackendControllerBase;
use Multiple\Models\AdminUser;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;

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
        $users = AdminUser::find();
        echo $users[0]->profile->profile_name;


        // Create a Model paginator, show 10 rows by page starting from $currentPage
        $paginator = new PaginatorModel(
            array(
                "data"  => $users,
                "limit" => 1,
                "page"  => $page_num
            )
        );

        // Get the paginated results
        $page = $paginator->getPaginate();

        return $this->response->setJsonContent($page);

    }
}
