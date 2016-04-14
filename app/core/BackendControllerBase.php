<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 28/1/16
 * Time: 6:02 PM
 */

namespace Multiple\Core;

use Phalcon\Di;

use Multiple\Core\ControllerBase;
use Multiple\Core\Constants\Services;
use Multiple\Core\Constants\ErrorCodes;
use Multiple\Core\Exception\UploadException;

class BackendControllerBase extends ControllerBase
{
    protected $userName;

    protected $userProfile;

    protected function initialize()
    {
        $this->tag->prependTitle('Linkage | ');
        $this->view->setTemplateAfter('main');
        $this->myProfile();
    }

    protected function upload2Upyun()
    {
        $upyun = Di::getDefault()->get(Services::UPYUN);

        // Check if the user has uploaded files
        if ($this->request->hasFiles()) {
            $file = $this->request->getUploadedFiles()[0];
            $fileName = $upyun->uploadImage($file);
        }else{
            throw new UploadException(ErrorCodes::GEN_UPLOAD_FILE_NOT_FOUND, ErrorCodes::$MESSAGE[ErrorCodes::GEN_UPLOAD_FILE_NOT_FOUND]);
        }

        return $fileName;
    }

    private function myProfile(){
        $auth = $this->session->get('auth');
        $this->userName = $auth['username'];
        $this->userProfile = $auth['profile_name'];

        $this->view->setVars(
            array(
                'username'   => $this->userName,
                'profilename' => $this->userProfile,
            )
        );
    }

}
