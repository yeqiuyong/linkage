<?php
/**
 * Created by PhpStorm.
 * User: uni
 * Date: 16/3/5
 * Time: 下午4:58
 */


namespace Multiple\Core\Constants;

class StatusCodes
{
    const CLIENT_USER_ACTIVE = 0;
    const CLIENT_USER_INACTIVE = 1;
    const CLIENT_USER_PENDING = 2;
    const CLIENT_USER_BANNED = 3;
    const CLIENT_USER_DELETED = 4;

    const COMPANY_USER_ACTIVE = 0;
    const COMPANY_USER_INACTIVE = 1;
    const COMPANY_USER_PENDING = 2;
    const COMPANY_USER_BANNED = 3;
    const COMPANY_USER_DELETED = 4;

    const FAVORITE_ACTIVE = 0;
    const FAVORITE_INACTIVE = 1;
    const FAVORITE_DELETE = 2;

    const COMPLAIN_HANDLING = 0;
    const COMPLAIN_HANDLED= 1;
    const COMPLAIN_DELETED = 2;

}