<?php
namespace Multiple\API;

use Multiple\Core\Auth\TokenParser;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Events\Manager as EventsManager;

use Multiple\Plugins\APISecurityPlugin;
use Multiple\Core\Libraries\AuthenticateManager;
use Multiple\Core\Auth\UsernameAdaptor;
use Multiple\Core\Libraries\CustomSerializer;
use Multiple\Core\Constants\Services;

class Module
{

	public function registerAutoloaders()
	{

		$loader = new \Phalcon\Loader();

		$loader->registerNamespaces(array(
			'Multiple\API\Controllers' => APP_PATH.'app/modules/api/v1/controllers/',
			'Multiple\API\Auth' => APP_PATH.'app/modules/api/v1/auth/',
			'Multiple\API\services' => APP_PATH.'app/modules/api/v1/services/',
		));

		$loader->register();
	}

	/**
	 * Register the services here to make them module-specific
	 */
	public function registerServices($di)
	{

		//Registering a dispatcher
		$di->set(Services::DISPATCHER, function () {
			$eventsManager = new EventsManager;

			/**
			 * Check if the user is allowed to access certain action using the SecurityPlugin
			 */
			$eventsManager->attach('dispatch:beforeDispatch', new APISecurityPlugin);

			$dispatcher = new \Phalcon\Mvc\Dispatcher();
			$dispatcher->setDefaultNamespace("Multiple\\API\\Controllers");
			$dispatcher->setEventsManager($eventsManager);

			return $dispatcher;
		});

		/**
		 * Setting up the view component
		 */
		//Registering a shared view component
		$di->set(Services::VIEW, function() {
			$view = new \Phalcon\Mvc\View();
			$view->setViewsDir(APP_PATH.'app/modules/api/views/');
			$view->registerEngines(array(".volt" => 'volt'));
			return $view;
		});

		/**
		 * Setting up volt
		 */
		$di->set(Services::VOLT, function($view, $di) {
			$volt = new VoltEngine($view, $di);

			$volt->setOptions(array(
				"compiledPath" => APP_PATH . "cache/api/"
			));

			$compiler = $volt->getCompiler();
			$compiler->addFunction('is_a', 'is_a');

			return $volt;
		}, true);


		$di->setShared(Services::AUTH_MANAGER, function () use ($di) {

			$authManager = new AuthenticateManager(60000);
			$authManager->registerAdaptor(UsernameAdaptor::NAME, new UsernameAdaptor());
			$authManager->registerTokenParser(new TokenParser("mysecret"));

			return $authManager;
		});

		/**
		 * @description PhalconRest - \League\Fractal\Manager
		 */
		$di->setShared(Services::FRACTAL_MANAGER, function () {

			$fractal = new \League\Fractal\Manager;
			$fractal->setSerializer(new CustomSerializer());

			return $fractal;
		});

		/** @var \Phalcon\Http\ResponseInterface $response */
		$response = $di->get('response');
		$response->setHeader('Access-Control-Allow-Origin', '*')
			->setContentType('application/json', 'utf-8');

	}

}