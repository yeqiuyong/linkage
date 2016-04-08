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

    const COMPANY_ACTIVE = 0;
    const COMPANY_INACTIVE = 1;
    const COMPANY_PENDING = 2;
    const COMPANY_BANNED = 3;
    const COMPANY_DELETED = 4;

    const ORDER_PLACE = 0;
    const ORDER_HANDLING = 1;
    const ORDER_REJECT = 2;
    const ORDER_HANDLED = 3;
    const ORDER_CANCEL = 4;
    const ORDER_DELETED = 5;

    const TASK_RECEIPT = 0;
    const TASK_PRINT_ORDER = 1;
    const TASK_TAKE_CARGO = 2;
    const TASK_SEND_CARGO = 3;
    const TASK_SET_GOODS = 4;
    const TASK_RETURN_CARGO = 5;
    const TASK_ACCESS_PORT = 6;
    const TASK_OFF_CARGO = 7;
    const TASK_COMPLETE = 8;

    const CAR_ACTIVE = 0;
    const CAR_INACTIVE = 1;
    const CAR_DELETED = 2;

    const FAVORITE_ACTIVE = 0;
    const FAVORITE_INACTIVE = 1;
    const FAVORITE_DELETE = 2;

    const COMPLAIN_HANDLING = 0;
    const COMPLAIN_HANDLED= 1;
    const COMPLAIN_DELETED = 2;


}