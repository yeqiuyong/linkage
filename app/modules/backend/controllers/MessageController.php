<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 14/4/16
 * Time: 4:19 PM
 */


namespace Multiple\Backend\Controllers;

use Phalcon\Di;
use Phalcon\Paginator\Adapter\NativeArray as PaginatorArray;

use Multiple\Core\Constants\ErrorCodes;
use Multiple\Core\Exception\UserOperationException;
use Multiple\Core\BackendControllerBase;
use Multiple\Models\AdminUser;
use Multiple\Models\Notice;


class MessageController extends BackendControllerBase
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
        $message = new Notice();
        $results = $message->getMsg4Admin();

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

        $message = new Notice();
        $msg = $message->getMsgDetail4Admin($id);

        return $this->response->setJsonContent($msg);

    }

    public function addAction(){
        $type = $this->request->getPost('msg_type', 'int');
        $client_type = $this->request->getPost('client_type', 'int');
        $title = $this->request->getPost('title', 'string');
        $link = $this->request->getPost('link', 'string');
        $description = $this->request->getPost('description', 'string');
        $memo = $this->request->getPost('memo', 'string');

        if(empty($type) || empty($title) || empty($description)){
            throw new UserOperationException(ErrorCodes::GEN_INPUT_ERROR, ErrorCodes::$MESSAGE[ErrorCodes::GEN_INPUT_ERROR]);
        }

        $message = new Notice();
        $admin = new AdminUser();

        $image = $this->upload2Upyun();
        $adminInfo = $admin->getUserByName('admin');

        $message->addMsg($type, $client_type, $title, $link, $description, $memo, $image, $adminInfo->admin_id);

        $url = $this->url->get('admin/message/index');
        return $this->response->redirect($url);

    }

    public function updateAction(){
        $id =  $this->request->getPost('id-editor-modal', 'int');
        $title = $this->request->getPost('title-editor-modal', 'string');
        $link = $this->request->getPost('link-editor-modal', 'string');
        $description = $this->request->getPost('description-editor-modal', 'string');
        $memo = $this->request->getPost('memo-editor-modal', 'string');

        $message = new Notice();
        $admin = new AdminUser();

        $image = $this->upload2Upyun();
        $adminInfo = $admin->getUserByName('admin');

        $message->updateNotice($id, $title, $link, $description, $memo, $image, $adminInfo->admin_id);

        $url = $this->url->get('admin/message/index');
        return $this->response->redirect($url);

    }

    public function changeStatusAction(){
        $msgId = $this->request->getPost('id', 'int'); // POST
        $status = $this->request->getPost('status', 'int'); // POST

        $message = new Notice();
        $message->updateStatus($msgId, $status);

        return $this->responseJsonOK();
    }

}