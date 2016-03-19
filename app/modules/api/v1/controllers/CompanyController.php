<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/3/16
 * Time: 1:06 PM
 */


namespace Multiple\API\Controllers;

use Multiple\Models\Company;
use Phalcon\Di;

use Multiple\Core\Constants\ErrorCodes;
use Multiple\Core\Exception\Exception;
use Multiple\Core\APIControllerBase;
use Multiple\Core\Auth\UsernameAdaptor;
use Multiple\Core\Constants\Services;
use Multiple\Models\ClientUser;

class CompanyController extends APIControllerBase
{
    public function initialize(){
        parent::initialize();

        $this->redis = Di::getDefault()->get(Services::REDIS);
        $this->logger = Di::getDefault()->get(Services::LOGGER);
    }

    public function modCompany4Recheck(){
        $name = $this->request->getPost('name');
        $contact_name = $this->request->getPost('contact_name');
        $address = $this->request->getPost('address');
        $email = $this->request->getPost('email');
        $home_page = $this->request->getPost('home_page');
        $description = $this->request->getPost('description');
        $phone_1 = $this->request->getPost('phone_1');
        $phone_2 = $this->request->getPost('phone_2');
        $phone_3 = $this->request->getPost('phone_3');
        $phone_4 = $this->request->getPost('phone_4');

        $info = [
        'contact_name' => $contact_name,
        'address' => $address,
        'email' => $email,
            'home_page' => $home_page,
        'description' => $description,
        'phone_1' => $phone_1,
        'phone_2' => $phone_2,
        'phone_3' => $phone_3,
        'phone_4' => $phone_4,
        ];

        if(isset($name)){
            return $this->respondError(ErrorCodes::USER_COMPANY_NAME_NULL, ErrorCodes::$MESSAGE[ErrorCodes::USER_COMPANY_NAME_NULL]);
        }

        try{
            $user = new ClientUser();
            if(!$user->isAdmin($this->cid)){
                return $this->respondError(ErrorCodes::USER_NOT_ADMIN, ErrorCodes::$MESSAGE[ErrorCodes::USER_NOT_ADMIN]);
            }

            $companyId = $user->getCompanyidByUserid($this->cid);

            $company = new Company();
            $company->modifyNameById($companyId, $name, $info);

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondOK();
    }

    public function modCompany(){
        $contact_name = $this->request->getPost('contact_name');
        $address = $this->request->getPost('address');
        $email = $this->request->getPost('email');
        $home_page = $this->request->getPost('home_page');
        $description = $this->request->getPost('description');
        $phone_1 = $this->request->getPost('phone_1');
        $phone_2 = $this->request->getPost('phone_2');
        $phone_3 = $this->request->getPost('phone_3');
        $phone_4 = $this->request->getPost('phone_4');

        $info = [
            'contact_name' => $contact_name,
            'address' =>$address,
            'email' => $email,
            'home_page' => $home_page,
            'description' => $description,
            'phone_1' => $phone_1,
            'phone_2' => $phone_2,
            'phone_3' => $phone_3,
            'phone_4' => $phone_4,
        ];

        try{
            $user = new ClientUser();
            if(!$user->isAdmin($this->cid)){
                return $this->respondError(ErrorCodes::USER_NOT_ADMIN, ErrorCodes::$MESSAGE[ErrorCodes::USER_NOT_ADMIN]);
            }

            $companyId = $user->getCompanyidByUserid($this->cid);

            $company = new Company();
            $company->modifyById($companyId, $info);

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondOK();
    }

}