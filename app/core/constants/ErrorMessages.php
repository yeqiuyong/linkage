<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 29/1/16
 * Time: 11:11 PM
 */

namespace Multiple\Core\Constants;

class ErrorMessages
{
    // General
    const GEN_SYSTEM = "系统通用错误";

    // Data
    const DATA_DUPLICATE = "数据重复";
    const DATA_NOTFOUND = "数据查找错误";
    const DATA_INVALID = "数据无效";
    const DATA_FAIL = "数据通用错误";

    // Data Base
    const DATA_FIND_FAIL = "数据库查找错误";
    const DATA_CREATE_FAIL = "数据库新增错误";
    const DATA_UPDATE_FAIL = "数据库更新错误";
    const DATA_DELETE_FAIL = "数据库删除错误";
    const DATA_REJECTED = "数据库拒绝错误错误";
    const DATA_NOTALLOWED = "数据库授权错误";

    // Authentication
    const AUTH_BADTOKEN = "无效签名";
    const AUTH_NOUSERNAME = "缺少用户名";
    const AUTH_INVALIDTYPE = "认证类型错误";
    const AUTH_BADLOGIN = "认证登陆错误";
    const AUTH_UNAUTHORIZED = "授权错误";
    const AUTH_FORBIDDEN = "授权禁止";
    const AUTH_EXPIRED = "授权超时";

    // User management
    const USER_NOTACTIVE = "用户被禁止";
    const USER_NOTFOUND = "用户查找失败";
    const USER_REGISTERFAIL = "用户注册失败";
    const USER_MODFAIL = "用户信息修改失败";
    const USER_CREATEFAIL = "添加用户失败";



}