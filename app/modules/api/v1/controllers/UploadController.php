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
    const PROTOCOL = "http://";
    private $upyun;

    /**
     * @title("uploadfiles")
     * @description("Upload multiple files")
     * @requestExample("POST /upload/upload")
     * @response("Data object or Error object")
     */
    public function uploadFilesAction()
    {
        $this->upyun = Di::getDefault()->get(Services::UPYUN);

        $FileNames = '';
        // Check if the user has uploaded files
        if ($this->request->hasFiles()) {
            try{
                foreach ($this->request->getUploadedFiles() as $file) {
                    $FileNames .= self::PROTOCOL . $this->upyun->uploadImage($file).';';
                }
            }catch (UploadException $e){
                $this->respondError($e->getCode(), $e->getMessage());
            }

        }

        return $this->respondArray(['file' => $FileNames]);
    }

    /**
     * @title("usericon")
     * @description("Upload User icon")
     * @requestExample("POST /upload/usericon")
     * @response("Data object or Error object")
     */
    public function userIconAction()
    {
        $this->upyun = Di::getDefault()->get(Services::UPYUN);

        // Check if the user has uploaded files
        if ($this->request->hasFiles()) {
            try{
                $file = $this->request->getUploadedFiles()[0];
                $fileName = self::PROTOCOL . $this->upyun->uploadImage($file);

                $user = new ClientUser();
                $user->updateIconById($this->cid, $fileName);

            }catch (UploadException $e){
                $this->respondError($e->getCode(), $e->getMessage());
            }

        }

        return $this->respondArray(['icon' => $fileName]);
    }

    /**
     * @title("register4invitecode")
     * @description("Upload company logo")
     * @requestExample("POST /upload/companylogo")
     * @response("Data object or Error object")
     */
    public function companyLogoAction()
    {
        $this->upyun = Di::getDefault()->get(Services::UPYUN);

        // Check if the user has uploaded files
        if ($this->request->hasFiles()) {
            try{
                $file = $this->request->getUploadedFiles()[0];
                $fileName = self::PROTOCOL . $this->upyun->uploadImage($file);

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

    /**
     * @title("uploadfiles")
     * @description("Upload multiple files")
     * @requestExample("POST /upload/upload")
     * @response("Data object or Error object")
     */
    public function companyImagesAction()
    {
        $this->upyun = Di::getDefault()->get(Services::UPYUN);

        $FileNames = '';
        // Check if the user has uploaded files
        if ($this->request->hasFiles()) {
            try{
                foreach ($this->request->getUploadedFiles() as $file) {
                    $FileNames .=  self::PROTOCOL . $this->upyun->uploadImage($file).';';
                }

                $FileNames = substr($FileNames, 0, strlen($FileNames) - 1);

            }catch (UploadException $e){
                $this->respondError($e->getCode(), $e->getMessage());
            }

        }

        return $this->respondArray(['company_images' => $FileNames]);
    }

    /**
     * @title("uploadfiles")
     * @description("Upload multiple files")
     * @requestExample("POST /upload/upload")
     * @response("Data object or Error object")
     */
    public function soAction()
    {
        $this->upyun = Di::getDefault()->get(Services::UPYUN);

        $FileNames = '';
        // Check if the user has uploaded files
        if ($this->request->hasFiles()) {
            try{
                foreach ($this->request->getUploadedFiles() as $file) {
                    $FileNames .=  self::PROTOCOL . $this->upyun->uploadImage($file).';';
                }

                $FileNames = substr($FileNames, 0, strlen($FileNames) - 1);

            }catch (UploadException $e){
                $this->respondError($e->getCode(), $e->getMessage());
            }

        }

        return $this->respondArray(['so' => $FileNames]);
    }
}