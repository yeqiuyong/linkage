<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 25/1/16
 * Time: 10:25 AM
 */

namespace Multiple\Core\Constants;

class ErrorCodes
{
    // General
    const GEN_SYSTEM = 9999;
    const GEN_UPLOAD_FILE_NOT_FOUND = 9998;
    const GEN_UPLOAD_FILE_SIZE_ERROR = 9997;
    const GEN_UPLOAD_FILE_TYPE_ERROR = 9996;
    const GEN_UPLOAD_UPYUN_ERROR = 9995;
    const GEN_INPUT_ERROR = 9994;

    // Data
    const DATA_DUPLICATE = 2001;
    const DATA_NOTFOUND = 2002;
    const DATA_INVALID = 2004;
    const DATA_FAIL = 2005;

    const DATA_FIND_FAIL = 2010;
    const DATA_CREATE_FAIL = 2020;
    const DATA_UPDATE_FAIL = 2030;
    const DATA_DELETE_FAIL = 2040;
    const DATA_REJECTED = 2060;
    const DATA_NOTALLOWED = 2070;

    // Authentication
    const AUTH_PASSWORD_INVALID = 3004;
    const AUTH_IDENTITY_MISS = 3005;
    const AUTH_BADTOKEN = 3006;
    const AUTH_NOUSERNAME = 3007;
    const AUTH_INVALIDTYPE = 3008;
    const AUTH_BADLOGIN = 3009;
    const AUTH_UNAUTHORIZED = 3010;
    const AUTH_FORBIDDEN = 3020;
    const AUTH_EXPIRED = 3030;

    // User management
    const USER_MOBILE_NOTFOUND = 4001;
    const USER_DUPLICATE = 4002;
    const USER_NOTACTIVE = 4003;
    const USER_NOTFOUND = 4004;
    const USER_REGISTERFAIL = 4005;
    const USER_MODFAIL = 4006;
    const USER_CREATEFAIL = 4007;
    const USER_MOBILE_NULL = 4008;
    const USER_SMS_CONTENT_NULL = 4009;
    const USER_PASSWORD_NULL = 4010;
    const USER_VERIFY_CODE_NULL = 4011;
    const USER_ROLE_NULL = 4012;
    const USER_COMPANY_NAME_NULL = 4013;
    const USER_MOBILE_DUPLICATE = 4014;
    const USER_VERIFY_CODE_EXPIRE = 4015;
    const USER_VERIFY_CODE_ERROR = 4016;
    const USER_INVITE_CODE_NULL = 4017;
    const USER_INVITE_CODE_EXPIRE = 4018;
    const USER_TYPE_ERROR = 4019;
    const USER_ID_NULL = 4020;
    const USER_COMPLAIN_CONTENT_NULL = 4021;
    const USER_CAR_ID_NULL = 4022;
    const USER_CAR_NOT_FOUND = 4023;
    const USER_FAVORITE_NOT_FOUND = 4024;
    const USER_ADDRESS_INPUT_ERROR = 4025;
    const USER_ADDRESS_NOT_FOUND = 4026;
    const USER_ADVERTISE_NOT_FOUND = 4026;
    const USER_NAME_NULL = 4027;
    const USER_FAVORITE_EXIST = 4028;
    const USER_IS_DELETE = 4029;

    //Company mangement
    const COMPANY_ID_NULL = 5001;
    const COMPANY_DEUPLICATE = 5002;
    const COMPANY_NOTFOUND = 5003;

    //Order mangement
    const ORDER_ID_NULL = 6001;
    const ORDER_NOT_FOUND = 6002;
    const ORDER_TRANSPORTER_NULL = 6003;
    const ORDER_DISPATCH_INFO_NULL = 6004;
    const ORDER_ACCEPT_ERROR = 6005;
    const ORDER_COMMENT_COMMENT_ALREADY = 6006;
    const ORDER_TASK_NOT_FOUND = 6007;
    const ORDER_IS_HANDLING = 6008;
    const ORDER_REJECT_ERROR = 6009;


    public static $MESSAGE = [
        // General
        self::GEN_SYSTEM => "系统通用错误",
        self::GEN_UPLOAD_FILE_NOT_FOUND => "上存文件不存在",
        self::GEN_UPLOAD_FILE_SIZE_ERROR => "上传文件大小错误",
        self::GEN_UPLOAD_FILE_TYPE_ERROR => "上传文件类型错误",
        self::GEN_INPUT_ERROR => "输入错误",

        // Data
        self:: DATA_DUPLICATE => "数据重复",
        self:: DATA_NOTFOUND => "数据查找错误",
        self:: DATA_INVALID => "数据无效",
        self:: DATA_FAIL => "数据通用错误",

        // Data Base
        self:: DATA_FIND_FAIL => "数据库查找错误",
        self:: DATA_CREATE_FAIL => "数据库新增错误",
        self:: DATA_UPDATE_FAIL => "数据库更新错误",
        self:: DATA_DELETE_FAIL => "数据库删除错误",
        self:: DATA_REJECTED => "数据库拒绝错误错误",
        self:: DATA_NOTALLOWED => "数据库授权错误",

         // Authentication
        self:: AUTH_PASSWORD_INVALID => "密码不正确",
        self:: AUTH_IDENTITY_MISS => "缺少用户ID",
        self:: AUTH_BADTOKEN => "无效签名",
        self:: AUTH_NOUSERNAME => "缺少用户名",
        self:: AUTH_INVALIDTYPE => "认证类型错误",
        self:: AUTH_BADLOGIN => "认证登陆错误",
        self:: AUTH_UNAUTHORIZED => "授权错误",
        self:: AUTH_FORBIDDEN => "授权禁止",
        self:: AUTH_EXPIRED => "授权超时",

         // User management
        self:: USER_DUPLICATE => "注册用户已存在",
        self:: USER_NOTACTIVE => "用户被禁止",
        self:: USER_NOTFOUND => "用户查找失败",
        self:: USER_REGISTERFAIL => "用户注册失败",
        self:: USER_MODFAIL => "用户信息修改失败",
        self:: USER_CREATEFAIL => "添加用户失败",
        self:: USER_MOBILE_NULL => "电话号码为空",
        self:: USER_SMS_CONTENT_NULL => "信息内容为空",
        self:: USER_PASSWORD_NULL =>  "用户密码为空",
        self:: USER_VERIFY_CODE_NULL => "校验码为空",
        self:: USER_ROLE_NULL => "公司名为空",
        self:: USER_COMPANY_NAME_NULL => "公司名为空",
        self:: USER_MOBILE_DUPLICATE => "手机被注册",
        self:: USER_VERIFY_CODE_EXPIRE => "校验码过期",
        self:: USER_VERIFY_CODE_ERROR => "校验码错误",
        self:: USER_INVITE_CODE_NULL => "邀请码为空",
        self:: USER_INVITE_CODE_EXPIRE => "邀请码过期",
        self:: USER_TYPE_ERROR => "用户类型不合法",
        self:: USER_ID_NULL => "用户ID为空",
        self:: USER_MOBILE_NOTFOUND => "用户电话不存在",
        self:: USER_COMPLAIN_CONTENT_NULL => "投诉内容为空",
        self:: USER_CAR_ID_NULL => "汽车编码为空",
        self:: USER_CAR_NOT_FOUND => "汽车不存在",
        self:: USER_FAVORITE_NOT_FOUND => "收藏公司不存在",
        self:: USER_ADDRESS_INPUT_ERROR => "用户地址簿输入错误",
        self:: USER_ADDRESS_NOT_FOUND => "用户收藏地址不存在",
        self:: USER_ADVERTISE_NOT_FOUND => "广告编码不存在",
        self:: USER_NAME_NULL => "用户姓名为空",
        self:: USER_FAVORITE_EXIST => "公司不能重复收藏",
        self:: USER_IS_DELETE => "用户已经删除",

        // Company management
        self:: COMPANY_ID_NULL => "公司ID为空",
        self:: COMPANY_DEUPLICATE => "注册公司已存在",
        self:: COMPANY_NOTFOUND => "公司查找失败",

        // Order management
        self:: ORDER_ID_NULL => "订单ID为空",
        self:: ORDER_NOT_FOUND => "订单不存在",
        self:: ORDER_TRANSPORTER_NULL => "订单运营商为空",
        self:: ORDER_DISPATCH_INFO_NULL => "订单分配信息为空",
        self:: ORDER_ACCEPT_ERROR => "接单失败",
        self:: ORDER_COMMENT_COMMENT_ALREADY => " 订单已经被评论",
        self:: ORDER_TASK_NOT_FOUND => "任务不存在",
        self:: ORDER_IS_HANDLING => "订单已被接受",
        self:: ORDER_REJECT_ERROR => "拒绝订单失败",

    ];

}