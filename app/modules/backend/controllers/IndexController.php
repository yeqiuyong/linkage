<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 4/2/16
 * Time: 4:25 PM
 */

namespace Multiple\Backend\Controllers;

use Multiple\Core\BackendControllerBase;
use Multiple\Core\Constants\LinkageUtils;
use Multiple\Models\ClientUser;
use Multiple\Models\ClientUserRole;

class IndexController extends BackendControllerBase
{
    public function initialize(){
        $this->tag->setTitle('Welcome');
        parent::initialize();
    }

    public function indexAction(){
        $userRole = new ClientUserRole();
        $userCounts = $userRole->getUserCount();

        $manufactureCnt = 0;
        $transporterCnt = 0;
        $driverCnt = 0;
        $totalCnt = 0;
        foreach ($userCounts as $userCount) {
            $roleId = $userCount->role_id;
            $count = $userCount->rowcount;

            switch($roleId){
                case LinkageUtils::USER_ADMIN_MANUFACTURE : $manufactureCnt += $count;break;
                case LinkageUtils::USER_MANUFACTURE : $manufactureCnt += $count;break;
                case LinkageUtils::USER_ADMIN_TRANSPORTER : $transporterCnt += $count;break;
                case LinkageUtils::USER_TRANSPORTER : $transporterCnt += $count;break;
                case LinkageUtils::USER_DRIVER : $driverCnt += $count;break;
                default: $driverCnt += $count;break;
            }

            $totalCnt += $count;
        }

        $user = new ClientUser();
        $newUserCounts = $user->getNewUserCount();

        $newManufactureCnt = 0;
        $newTransporterCnt = 0;
        $newDriverCnt = 0;
        $newTotalCnt = 0;
        foreach ($newUserCounts as $newUserCount) {
            $roleId = $newUserCount->role_id;
            $count = $newUserCount->count;

            switch($roleId){
                case LinkageUtils::USER_ADMIN_MANUFACTURE : $newManufactureCnt += $count;break;
                case LinkageUtils::USER_MANUFACTURE : $newManufactureCnt += $count;break;
                case LinkageUtils::USER_ADMIN_TRANSPORTER : $newTransporterCnt += $count;break;
                case LinkageUtils::USER_TRANSPORTER : $newTransporterCnt += $count;break;
                case LinkageUtils::USER_DRIVER : $newDriverCnt += $count;break;
                default: $driverCnt += $count;break;
            }

            $newTotalCnt += $count;
        }

        $this->view->setVars(
            array(
                'totalCnt' => $totalCnt,
                'manufactureCnt' => $manufactureCnt,
                'transporterCnt' => $transporterCnt,
                'driverCnt' => $driverCnt,
                'newTotalCnt' => $newTotalCnt,
                'newManufactureCnt' => $newManufactureCnt,
                'newTransporterCnt' => $newTransporterCnt,
                'newDriverCnt' => $newDriverCnt,
            )
        );
    }


}
