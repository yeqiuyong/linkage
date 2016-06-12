<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 22/2/16
 * Time: 10:36 PM
 */


use Phalcon\Config\Adapter\Ini as ConfigIni;
use Phalcon\Di\FactoryDefault\Cli as CliDI;
use Phalcon\Cli\Console as ConsoleApp;
use Phalcon\Db\Adapter\Pdo\Mysql as DBAdapter;

use Multiple\Core\Constants\Services as AppServices;

define('VERSION', '1.0.0');

/**
 *  Using the CLI factory default services container
 */
$di = new CliDI();

/**
 *  Define path to application directory
 */
defined('APPLICATION_PATH')
|| define('APPLICATION_PATH', realpath(dirname(__FILE__)));


/**
 *  Load the configuration file (if any)
 */
if (is_readable(APPLICATION_PATH . '/config/config.ini')) {
    $config = new ConfigIni(APPLICATION_PATH . '/config/config.ini');
}

/**
 * Register the autoloader and tell it to register the tasks directory
 */
$loader = new \Phalcon\Loader();

$loader->registerDirs(
    array(
        APPLICATION_PATH . '/tasks',
        APPLICATION_PATH . '/models'
    )
);

$loader->registerNamespaces(array(
    'Multiple\Models' => APPLICATION_PATH . '/models',
    'Multiple\Core\Constants' => APPLICATION_PATH . '/core/constants',
))->register();

$loader->register();


/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->setShared(AppServices::DB, function () use ($config) {
    $connection = new DBAdapter(array(
        "host" => $config->database->host,
        "username" => $config->database->username,
        "password" => $config->database->password,
        "dbname" => $config->database->dbname,
        "options" => array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING ,PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8')
    ));

    return $connection;
});

/**
 * Redis connection is created based in the parameters defined in the configuration file
 */
$di->setShared(AppServices::REDIS, function() use ($config) {
    $redisConnect = new Redis();
    $redisConnect->connect($config->redis->host, $config->redis->port);
    $redisConnect->auth('Qiushui456@Redis');
    return $redisConnect;
});

/**
 *  Create a console application
 */
$console = new ConsoleApp();
$console->setDI($di);

/**
 * Process the console arguments
 */
$arguments = array();
foreach ($argv as $k => $arg) {
    if ($k == 1) {
        $arguments['task'] = $arg;
    } elseif ($k == 2) {
        $arguments['action'] = $arg;
    } elseif ($k >= 3) {
        $arguments['params'][] = $arg;
    }
}

/**
 *  Define global constants for the current task and action
 */
define('CURRENT_TASK',   (isset($argv[1]) ? $argv[1] : null));
define('CURRENT_ACTION', (isset($argv[2]) ? $argv[2] : null));

try {
    $console->handle($arguments);
} catch (\Phalcon\Exception $e) {
    echo $e->getMessage();
    exit(255);
}
