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

    const USER_ADMIN_MANUFACTURE = 0;
    const USER_MANUFACTURE = 1;
    const USER_ADMIN_TRANSPORTER = 2;
    const USER_TRANSPORTER = 3;
    const USER_DRIVER = 4;

    const MESSAGE_TYPE_ADV = 0;
    const MESSAGE_TYPE_EMPLOYMENT = 1;
    const MESSAGE_TYPE_NOTICE = 2;

}