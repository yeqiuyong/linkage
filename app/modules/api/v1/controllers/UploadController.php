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

    public function uploadFileAction()
    {
        $this->upyun = Di::getDefault()->get(Services::UPYUN);

        // Check if the user has uploaded files
        if ($this->request->hasFiles()) {
            try{
                $file = $this->request->getUploadedFiles();
                $fileName = $this->upyun->uploadImage($file);

            }catch (UploadException $e){
                $this->respondError($e->getCode(), $e->getMessage());
            }

        }

        return $this->respondArray(['file' => $fileName]);
    }

}