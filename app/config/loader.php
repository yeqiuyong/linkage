<?php

$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerDirs(
	array(
		APP_PATH . $config->application->pluginsDir,
		APP_PATH . $config->application->modelsDir,
		APP_PATH . $config->application->formsDir,
		APP_PATH . $config->application->coreDir,
		APP_PATH . $config->application->constantsDir,
		APP_PATH . $config->application->librariesDir,
		APP_PATH . $config->application->servicesDir,
		APP_PATH . $config->application->exceptionDir,
		APP_PATH . $config->application->authDir,
		APP_PATH . $config->application->coreAPIDir,

		APP_PATH . $config->application->vendorFractalDir,
		APP_PATH . $config->application->vendorJWTDir,
	)
)->register();

$loader->registerNamespaces(array(
	'Multiple\Models' => APP_PATH . $config->application->modelsDir,
	'Multiple\Forms' => APP_PATH . $config->application->formsDir,
	'Multiple\Plugins' => APP_PATH . $config->application->pluginsDir,
	'Multiple\Core' => APP_PATH . $config->application->coreDir,
	'Multiple\Core\Constants' => APP_PATH . $config->application->constantsDir,
	'Multiple\Core\Libraries' => APP_PATH . $config->application->librariesDir,
	'Multiple\Core\Services' => APP_PATH . $config->application->servicesDir,
	'Multiple\Core\Exception' => APP_PATH . $config->application->exceptionDir,
	'Multiple\Core\Auth' => APP_PATH . $config->application->authDir,
	'Multiple\Core\API' => APP_PATH . $config->application->coreAPIDir,

	'League\Fractal' => APP_PATH . $config->application->vendorFractalDir,
	'Firebase\JWT' => APP_PATH . $config->application->vendorJWTDir,

))->register();
