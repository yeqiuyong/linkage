<?php
namespace Multiple\Plugins;

use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Acl;
use Phalcon\Acl\Role;
use Phalcon\Acl\Resource;
use Phalcon\Acl\Adapter\Memory as AclList;

use Multiple\Core\Constants\ErrorCodes;
use Multiple\Core\Exception\AuthenticateException;
use Multiple\Core\Constants\Services;


/**
 * SecurityPlugin
 *
 * This is the security plugin which controls that users only have access to the modules they're assigned to
 */
class APISecurityPlugin extends Plugin
{

    /**
     * Returns an existing or new access control list
     *
     * @returns AclList
     */
    public function getAcl()
    {
        //if (!isset($this->persistent->acl)) {

            $acl = new AclList();
            $acl->setDefaultAction(Acl::DENY);

            //Register roles
            $role = new Role('Guests');
            $acl->addRole($role);

            //Public area resources
            $publicResources = array(
                'user'  => array('register', 'registerbyname','registerbymobile', 'session', 'test'),
                'errors' => array('show'),

            );
            foreach ($publicResources as $resource => $actions) {
                $acl->addResource(new Resource($resource), $actions);
            }


            //Grant access to private area to role Users
            foreach ($publicResources as $resource => $actions) {
                foreach ($actions as $action){
                    $acl->allow('Guests', $resource, $action);
                }
            }

            //The acl is stored in session, APC would be useful here too
            $this->persistent->acl = $acl;
     //   }

        return $this->persistent->acl;
    }

    /**
     * This action is executed before execute any action in the application
     *
     * @param Event $event
     * @param Dispatcher $dispatcher
     * @return bool
     */
    public function beforeDispatch(Event $event, Dispatcher $dispatcher)
    {
        $controller = $dispatcher->getControllerName();
        $action = $dispatcher->getActionName();

        $acl = $this->getAcl();

        $allowed = $acl->isAllowed('Guests', $controller, $action);
        if($allowed){
            return true;
        }

        try{
            $token = $this->request->getPost('token');
            $authManager = $this->di->get(Services::AUTH_MANAGER);
            $isAuthenticated = $authManager->authenticateToken($token);

            if(!$isAuthenticated){
                throw new AuthenticateException(ErrorCodes::AUTH_UNAUTHORIZED,ErrorCodes::$MESSAGE[ErrorCodes::AUTH_UNAUTHORIZED]);
            }
        }catch (AuthenticateException $e){

            $dispatcher->forward(array(
                'controller' => 'errors',
                'action'     => 'show',
                "params" => array("code" => $e->getCode(), "message" => $e->getMessage()),
            ));

            return false;
        }
    }
}
