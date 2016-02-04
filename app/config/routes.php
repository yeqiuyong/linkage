<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 17/1/16
 * Time: 11:38 AM
 */


use Phalcon\Mvc\Router;

$router = new Router();

/**
 * 前台路由控制
 */
$router->setDefaultModule("frontend");

//设置默认登录
$router->add('/', array(
    'module' => 'frontend',
    'namespace'=>'Multiple\Frontend\Controllers\\',
    'controller' => 'index',
    'action' => 'index'
));


/**
 * 管理后台控制
 */
$router->add("/admin", array(
    'module' => 'backend',
    'namespace'=>'Multiple\Backend\Controllers\\',
    'controller' => 'session',
    'action' => 'index',

));

$router->add("/admin/:controller/:action/:params", array(
    'module' => 'backend',
    'namespace'=>'Multiple\Backend\Controllers\\',
    'controller' => 1,
    'action' => 2,
    'params' => 3,
));


/**
 * API
 */
$router->add('/api/:controller/:action', array(
    'module' => 'api',
    'namespace'=>'Multiple\API\Controllers\\',
    'controller' => 1,
    'action' => 2,
));

$router->add('/api/:controller/:action/:params', array(
    'module' => 'api',
    'namespace'=>'Multiple\API\Controllers\\',
    'controller' => 1,
    'action' => 2,
    'params' => 3,
));


return $router;