<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/3/16
 * Time: 1:06 PM
 */


namespace Multiple\API\Controllers;

use Multiple\Core\Constants\StatusCodes;
use Multiple\Core\Constants\LinkageUtils;
use Multiple\Models\Company;
use Phalcon\Di;

use Multiple\Core\Constants\ErrorCodes;
use Multiple\Core\Exception\Exception;
use Multiple\Core\APIControllerBase;
use Multiple\Core\Constants\Services;
use Multiple\Models\ClientUser;

class CompanyController extends APIControllerBase
{
    public function initialize(){
        parent::initialize();

        $this->redis = Di::getDefault()->get(Services::REDIS);
        $this->logger = Di::getDefault()->get(Services::LOGGER);
    }

    public function informationAction(){
        $companyId = $this->request->getPost('company_id', 'string');

        try{
            $company = new Company();
            $information = $company->getCompanyInformation($companyId);

            $result = [
                'logo' => $information['logo'] ,
                'company_id' => $companyId,
                'company_name' => $information['name'],
                'contact_name' => $information['contactor'],
                'contact_address' => $information['address'],
                'contact_phone' => $information['service_phone_1'],
                'contact_description' => $information['description'],
                'service_phone2' => $information['service_phone_2'],
                'service_phone3' => $information['service_phone_3'],
                'service_phone4' => $information['service_phone_4'],
                'email' => $information['email'],
                'home_page' => $information['home_page'],
                'fax' => $information['fax'],
                'images' => $information['images'],
                'order_num' => 0,
            ];

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondArray($result);
    }


    public function modCompany4RecheckAction(){
        $name = $this->request->getPost('name', 'string');
        $contact_name = $this->request->getPost('contact_name', 'string');
        $address = $this->request->getPost('address', 'string');
        $email = $this->request->getPost('email', 'string');
        $home_page = $this->request->getPost('home_page', 'string');
        $description = $this->request->getPost('description', 'string');
        $phone_1 = $this->request->getPost('phone_1', 'string');
        $phone_2 = $this->request->getPost('phone_2', 'string');
        $phone_3 = $this->request->getPost('phone_3', 'string');
        $phone_4 = $this->request->getPost('phone_4', 'string');
        $fax = $this->request->getPost('fax', 'string');
        $logo = $this->request->getPost('logo', 'string');
        $images = $this->request->getPost('images', 'string');

        $info = [
            'name' => $name,
            'contact_name' => $contact_name,
            'address' => $address,
            'email' => $email,
            'home_page' => $home_page,
            'description' => $description,
            'phone_1' => $phone_1,
            'phone_2' => $phone_2,
            'phone_3' => $phone_3,
            'phone_4' => $phone_4,
            'fax' => $fax,
            'logo' => $logo,
            'images' => $images,
        ];

        if(!isset($name)){
            return $this->respondError(ErrorCodes::USER_COMPANY_NAME_NULL, ErrorCodes::$MESSAGE[ErrorCodes::USER_COMPANY_NAME_NULL]);
        }

        try{
            $user = new ClientUser();
            if(!$user->isAdmin($this->cid) || $user->getStatus($this->cid) != StatusCodes::CLIENT_USER_INACTIVE){
                return $this->respondError(ErrorCodes::AUTH_UNAUTHORIZED, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_UNAUTHORIZED]);
            }

            $companyId = $user->getCompanyidByUserid($this->cid);

            $company = new Company();
            $company->modifyNameById($companyId, $name, $info);

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondOK();
    }

    public function modCompanyAction(){
        $contact_name = $this->request->getPost('contact_name', 'string');
        $address = $this->request->getPost('address', 'string');
        $email = $this->request->getPost('email', 'string');
        $home_page = $this->request->getPost('home_page', 'string');
        $description = $this->request->getPost('description', 'string');
        $phone_1 = $this->request->getPost('phone_1', 'string');
        $phone_2 = $this->request->getPost('phone_2', 'string');
        $phone_3 = $this->request->getPost('phone_3', 'string');
        $phone_4 = $this->request->getPost('phone_4', 'string');
        $fax = $this->request->getPost('fax', 'string');
        $logo = $this->request->getPost('logo', 'string');
        $images = $this->request->getPost('images', 'string');

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
            'fax' => $fax,
            'logo' => $logo,
            'images' => $images,
        ];

        try{
            $user = new ClientUser();
            if(!$user->isAdmin($this->cid)){
                return $this->respondError(ErrorCodes::AUTH_UNAUTHORIZED, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_UNAUTHORIZED]);
            }

            $companyId = $user->getCompanyidByUserid($this->cid);

            $company = new Company();
            $company->modifyById($companyId, $info);

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondOK();
    }

    public function searchAction(){
        $companyname = $this->request->getPost('name', 'string');
        $pagination = $this->request->getPost('pagination', 'int')?$this->request->getPost('pagination', 'int'):0;
        $offset = $this->request->getPost('offset', 'int')?$this->request->getPost('offset', 'int'):0;
        $size = $this->request->getPost('size', 'int')?$this->request->getPost('size', 'int'):1000;

        if(!isset($this->cid)){
            return $this->respondError(ErrorCodes::AUTH_IDENTITY_MISS, ErrorCodes::$MESSAGE[ErrorCodes::AUTH_IDENTITY_MISS]);
        }

        try {
            $user = new  ClientUser();
            $role = $user->getRoleId($this->cid);
            $isManufacture = ($role == LinkageUtils::USER_ADMIN_MANUFACTURE || $role == LinkageUtils::USER_MANUFACTURE) ? true : false;

            $company = new Company();
            if($isManufacture){
                $companies = $company->getSearch4Manufacture($companyname, $pagination, $offset, $size);
            }else{

                $companies = $company->getSearch4Transporter($companyname, $pagination, $offset, $size);
            }

        }catch (Exception $e){
            return $this->respondError($e->getCode(), $e->getMessage());
        }

        return $this->respondArray(['companies' => $companies]);
    }

}