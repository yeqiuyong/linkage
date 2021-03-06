<?php

use Phalcon\Mvc\View;
use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Url as UrlProvider;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Flash\Session as FlashSession;
use Phalcon\Mvc\Model\Metadata\Memory as MetaData;
use Phalcon\Db\Adapter\Pdo\Mysql as DBAdapter;
use Phalcon\Mvc\Model\Manager;

use Multiple\Core\Constants\Services as AppServices;
use Multiple\Core\Libraries\Elements;
use Multiple\Core\Libraries\Logger;
use Multiple\Core\Libraries\SMS;
use Multiple\Core\Libraries\UPyun;
use Multiple\Core\Libraries\Alidayu;


/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->set(AppServices::URL, function () use ($config) {
	$url = new UrlProvider();
	$url->setBaseUri($config->application->baseUri);
	return $url;
});

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->set(AppServices::DB, function () use ($config) {
	$connection = new DBAdapter(array(
		"host" => $config->database->host,
		"port" => 33062,
                "username" => $config->database->username,
		"password" => $config->database->password,
		"dbname" => $config->database->dbname,
		//"prefix" => $config->database->prefix,
		"options" => array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING ,PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8')
	));

	return $connection;
});

/**
 * Redis connection is created based in the parameters defined in the configuration file
 */
$di->set(AppServices::REDIS, function() use ($config) {
	$redisConnect = new Redis();
	$redisConnect->connect($config->redis->host, $config->redis->port);
	//$redisConnect->select($config->redis->database);
        $redisConnect->auth('Qiushui456@Redis');
	return $redisConnect;
});

/**
 * Register a user component
 */
$di->set(AppServices::LOGGER, function() use ($config){
	return new Logger( APP_PATH.$config->logger->path, $config->logger->level);
});

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->set(AppServices::METADATA, function () {
	return new MetaData();
});

/**
 * Start the session the first time some component request the session service
 */
$di->set(AppServices::SESSION, function () {
	$session = new SessionAdapter();
	$session->start();
	return $session;
});

/**
 * Register the flash service with custom CSS classes
 */
$di->set(AppServices::FLASH, function () {
	return new FlashSession(array(
		'error'   => 'alert alert-danger',
		'success' => 'alert alert-success',
		'notice'  => 'alert alert-info',
		'warning' => 'alert alert-warning'
	));
});

/**
 * Register a user component
 */
$di->set(AppServices::ELEMENTS, function () {
	return new Elements();
});


/**
 * Register a Router
 */
$di->set(
	AppServices::ROUTER, function () {
		require __DIR__.'/routes.php';

		return $router;
	}
);

/**
 * Register a Model manager service
 */
$di->set(AppServices::MODELS_MANAGER, function(){
		return new Manager();
});

/**
 * Register a SMS Service
 */
$di->setShared(AppServices::SMS, function() {
	return new SMS();
});

/**
 * Register a Upyun Service
 */
$di->setShared(AppServices::UPYUN, function() {
	return new UPyun();
});

