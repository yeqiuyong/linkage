<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/3/16
 * Time: 9:24 PM
 */

namespace Multiple\API\Controllers;

use Phalcon\Di;

use Multiple\Core\APIControllerBase;
use Multiple\Core\Constants\Services;
use Multiple\Core\Exception\UploadException;
use Multiple\Models\Company;
use Multiple\Models\ClientUser;

class UploadController extends APIControllerBase
{
    private $upyun;

    public function uploadFilesAction()
    {
        $this->upyun = Di::getDefault()->get(Services::UPYUN);

        $FileNames = '';
        // Check if the user has uploaded files
        if ($this->request->hasFiles()) {
            try{
                foreach ($this->request->getUploadedFiles() as $file) {
                    $FileNames .= $this->upyun->uploadImage($file).';';
                }
            }catch (UploadException $e){
                $this->respondError($e->getCode(), $e->getMessage());
            }

        }

        return $this->respondArray(['file' => $FileNames]);
    }

    public function userIconAction()
    {
        $this->upyun = Di::getDefault()->get(Services::UPYUN);

        // Check if the user has uploaded files
        if ($this->request->hasFiles()) {
            try{
                $file = $this->request->getUploadedFiles()[0];
                $fileName = $this->upyun->uploadImage($file);

                $user = new ClientUser();
                $user->updateIconById($this->cid, $fileName);

            }catch (UploadException $e){
                $this->respondError($e->getCode(), $e->getMessage());
            }

        }

        return $this->respondArray(['icon' => $fileName]);
    }

    public function companyIconAction()
    {
        $this->upyun = Di::getDefault()->get(Services::UPYUN);

        // Check if the user has uploaded files
        if ($this->request->hasFiles()) {
            try{
                $file = $this->request->getUploadedFiles()[0];
                $fileName = $this->upyun->uploadImage($file);

                $user = new ClientUser();
                $companyId = $user->getCompanyidByUserid($this->cid);

                $company = new Company();
                $company->updateIconById($companyId, $fileName);


            }catch (UploadException $e){
                $this->respondError($e->getCode(), $e->getMessage());
            }

        }

        return $this->respondArray(['icon' => $fileName]);
    }

}