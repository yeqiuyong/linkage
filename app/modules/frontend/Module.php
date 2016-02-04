<?php
namespace Multiple\Frontend;

use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Events\Manager as EventsManager;

use Multiple\Core\Constants\Services;
use Multiple\Plugins\FrontendSecurityPlugin;
use Multiple\Plugins\NotFoundPlugin;

class Module
{

	public function registerAutoloaders(){
		$loader = new \Phalcon\Loader();

		$loader->registerNamespaces(array(
			'Multiple\Frontend\Controllers' => APP_PATH.'app/modules/frontend/controllers/',
		));

		$loader->register();
	}

	/**
	 * Register the services here to make them module-specific
	 */
	public function registerServices($di){

		//Registering a dispatcher
		$di->set(Services::DISPATCHER, function () {
			$eventsManager = new EventsManager;

			/**
			 * Check if the user is allowed to access certain action using the SecurityPlugin
			 */
			$eventsManager->attach('dispatch:beforeDispatch', new FrontendSecurityPlugin);

			/**
			 * Handle exceptions and not-found exceptions using NotFoundPlugin
			 */
			$eventsManager->attach('dispatch:beforeException', new NotFoundPlugin);


			$dispatcher = new \Phalcon\Mvc\Dispatcher();
			$dispatcher->setDefaultNamespace("Multiple\\Frontend\\Controllers");
			$dispatcher->setEventsManager($eventsManager);

			return $dispatcher;
		});

        /**
         * Setting up the view component
         */
        //Registering a shared view component
        $di->set(Services::VIEW, function() {
            $view = new \Phalcon\Mvc\View();
            $view->setViewsDir(APP_PATH.'app/modules/frontend/views/');
            $view->registerEngines(array(".volt" => 'volt'));
            return $view;
        });

        /**
         * Setting up volt
         */
        $di->set(Services::VOLT, function($view, $di) {
            $volt = new VoltEngine($view, $di);

	        $volt->setOptions(array(
				"compiledPath" => APP_PATH . "cache/frontend/"
			));

			$compiler = $volt->getCompiler();
			$compiler->addFunction('is_a', 'is_a');

			return $volt;
        }, true);

	}

}