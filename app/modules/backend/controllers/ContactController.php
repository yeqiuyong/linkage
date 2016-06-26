<?php
/**
 * Created by PhpStorm.
 * User: whoami
 * Date: 16-6-25
 * Time: 下午10:19
 */
namespace Multiple\Backend\Controllers;

use Multiple\Models\Contact;
use Phalcon\Di;
use Phalcon\Paginator\Adapter\NativeArray as PaginatorArray;

use Multiple\Core\Constants\ErrorCodes;
use Multiple\Core\Exception\UserOperationException;
use Multiple\Core\BackendControllerBase;
use Multiple\Models\AdminUser;

class ContactController extends BackendControllerBase
{
    public function initialize()
    {
        $this->tag->setTitle('Welcome');
        parent::initialize();
    }

    public function indexAction()
    {

    }

    public function listAction(){
        // Current page to show
        // In a controller this can be:
        // $this->request->getQuery('page', 'int'); // GET
        $currentPage = $this->request->getPost('pageindex', 'int'); // POST
        $pageNum = ($currentPage == null) ? 1 : $currentPage;

        // The data set to paginate
        $contact = new Contact();
        $results = $contact->getContact4Admin();

        // Create a Model paginator, show 10 rows by page starting from $currentPage
        $paginator = new PaginatorArray(
            array(
                "data"  => $results,
                "limit" => 10,
                "page"  => $pageNum
            )
        );

        // Get the paginated results
        $page = $paginator->getPaginate();

        return $this->response->setJsonContent($page);

    }

    public function detailAction(){
        $id = $this->request->get('id', 'int'); // POST

        $contact = new Contact();
        $con = $contact->getContactDetail4Admin($id);

        return $this->response->setJsonContent($con);

    }

    public function changeStatusAction(){
        $conId = $this->request->getPost('id', 'int'); // POST
        $status = $this->request->getPost('status', 'int'); // POST

        $contact = new Contact();
        $contact->updateStatus($conId, $status);

        return $this->responseJsonOK();
    }

}