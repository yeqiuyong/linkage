<?php
/**
 * Created by PhpStorm.
 * User: uni
 * Date: 16/3/5
 * Time: 下午5:35
 */

namespace Multiple\Core\Constants;

class LinkageUtils
{
    const APP_VERSION = 0;

    const LINKAGE_SERVER = 'http://120.25.82.122:8000';
    const APP_DOWNLOAD_URL = 'http://120.25.82.122:8000';

    const VERIFY_PREFIX = 'reg_verfiy_';
    const INVITE_PREFIX = 'reg_invite_';

    const INVITE_SECRET = 137453;

    const COMPANY_MANUFACTURE = 0;
    const COMPANY_TRANSPORTER = 1;

    const USER_ADMIN_MANUFACTURE = 1;
    const USER_MANUFACTURE = 2;
    const USER_ADMIN_TRANSPORTER = 3;
    const USER_TRANSPORTER = 4;
    const USER_DRIVER = 5;

    const MESSAGE_TYPE_ADV = 0;
    const MESSAGE_TYPE_EMPLOYMENT = 1;
    const MESSAGE_TYPE_NOTICE = 2;

    const ORDER_TYPE_EXPORT = 0;
    const ORDER_TYPE_IMPORT = 1;
    const ORDER_TYPE_INLAND = 2;
    const ORDER_TYPE_SELF = 3;

    const CARGO_TYPE_GP_20 = 0;
    const CARGO_TYPE_GP_40 = 1;
    const CARGO_TYPE_HQ_40 = 2;
    const CARGO_TYPE_HQ_45 = 3;
    const CARGO_TYPE_OT_20 = 4;
    const CARGO_TYPE_OT_40 = 5;
    const CARGO_TYPE_FR_20 = 6;
    const CARGO_TYPE_FR_40 = 7;
    const CARGO_TYPE_FR_45 = 8;
    const CARGO_TYPE_R_20 = 9;
    const CARGO_TYPE_R_40 = 10;

}