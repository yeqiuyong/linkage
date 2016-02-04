<?php
namespace Multiple\Plugins;

use Phalcon\Acl;
use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Mvc\Dispatcher;

/**
 * SecurityPlugin
 *
 * This is the security plugin which controls that users only have access to the modules they're assigned to
 */
class BackendSecurityPlugin extends Plugin
{
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
		//$action = $dispatcher->getActionName();

		if($controller == 'session' || $controller == 'errors'){
			return true;
		}else {
			$auth = $this->session->get('auth');
			if (!$auth) {
				$dispatcher->forward(array(
					'controller' => 'errors',
					'action' => 'show401'
				));

				return false;
			}
		}

	}
}
