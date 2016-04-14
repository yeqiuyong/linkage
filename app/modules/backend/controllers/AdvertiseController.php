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

use Multiple\Core\BackendControllerBase;
use Multiple\Models\AdminUser;
use Multiple\Models\Notice;


class AdvertiseController extends BackendControllerBase
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
        $advertise = new Notice();
        $results = $advertise->getAdv4Admin();

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

        $advertise = new Notice();
        $adv = $advertise->getAdvById($id);

        return $this->response->setJsonContent($adv);

    }

    public function addAction(){
        $title = $this->request->getPost('title', 'string');
        $link = $this->request->getPost('link', 'string');
        $description = $this->request->getPost('description', 'string');
        $memo = $this->request->getPost('memo', 'string');

        $advertise = new Notice();
        $admin = new AdminUser();

        $image = $this->upload2Upyun();
        $adminInfo = $admin->getUserByName('admin');

        $advertise->addAdv($title, $link, $description, $memo, $image, $adminInfo->admin_id);

        return $this->forward('advertise/index');

    }

    public function changeStatusAction(){
        $advId = $this->request->getPost('id', 'int'); // POST
        $status = $this->request->getPost('status', 'int'); // POST

        try{
            $advertise = new Notice();
            $advertise->updateStatus($advId, $status);



        }catch (Exception $e){
            return$this->responseJsonError($e->getCode(), $e->getMessage());
        }

        return $this->responseJsonOK();
    }

}