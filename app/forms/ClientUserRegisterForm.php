<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 28/1/16
 * Time: 6:02 PM
 */

namespace Multiple\Forms;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Password;
use Phalcon\Validation\Validator\PresenceOf;

class RegisterForm extends Form
{

    public function initialize($entity = null, $options = null)
    {
        // Mobile
        $mobile = new Text('mobile');
        $mobile->setLabel('手机号码');
        $mobile->setFilters(array('striptags', 'string'));
        $mobile->addValidators(array(
            new PresenceOf(array(
                'message' => '手机号码不能为空'
            ))
        ));
        $this->add($mobile);

        // Password
        $password = new Password('password');
        $password->setLabel('密码');
        $password->addValidators(array(
            new PresenceOf(array(
                'message' => '密码不能为空'
            ))
        ));
        $this->add($password);

        // Confirm Password
        $repeatPassword = new Password('repeatPassword');
        $repeatPassword->setLabel('确认密码');
        $repeatPassword->addValidators(array(
            new PresenceOf(array(
                'message' => '请确认密码'
            ))
        ));
        $this->add($repeatPassword);

        // Verify Code
        $verifyCode = new Text('verifyCode');
        $verifyCode->setLabel('短信校验码');
        $verifyCode->addValidators(array(
            new PresenceOf(array(
                'message' => '密码不能为空'
            ))
        ));
        $this->add($verifyCode);
    }
}