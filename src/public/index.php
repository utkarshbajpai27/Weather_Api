<?php

//---------------------------------------USING NAMESPACES------------------------------------
use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Url;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Config\ConfigFactory;
use Phalcon\Session\Manager;
use Phalcon\Session\Adapter\Stream;

//--------------------------------------SOME ABSOULUTE PATHS---------------------------------

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('URLROOT', 'http/localhost:8080/');

//--------------------------------------SET LOADER OBJECT------------------------------------
$loader = new Loader();

//---------------------------------------CUSTOM LIBRARIES------------------------------------

require_once BASE_PATH.'/vendor/autoload.php';

//-------------------------------------------------------------------------------------------

//----------------------------------REGISTER DIRECTORIES START-------------------------------

$loader->registerDirs(
    [
        APP_PATH . "/controllers/",
        APP_PATH . "/models/",
    ]
);

//-----------------------------------REGISTER DIRECTORIES END--------------------------------

//----------------------------------RESGIETER NAMESPACES START-------------------------------

$loader->registerNamespaces(
    [
        "App\Component" => APP_PATH . '/component'
    ]
);

//-------------------------------------REGISTER NAMESPACE END--------------------------------

$loader->register();
$container = new FactoryDefault();

$container->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);

//---------------------------------------URL CONTAINER START--------------------------------- 

$container->set(
    'url',
    function () {
        $url = new Url();
        $url->setBaseUri('/');
        return $url;
    }
);

//-------------------------------------URL CONTAINER STOP------------------------------------


//-----------------------------------CONFIG CONTAINER START----------------------------------

$container->set(
    'config',
    function () {
        $filename = '../app/etc/config.php';
        $factory = new ConfigFactory();
        return $factory->newInstance("php", $filename);
    },
    true
);

//-------------------------------------CONFIG CONTAINER END----------------------------------

//--------------------------------------DB CONNECTION START----------------------------------

$container->set(
    'db',
    function () {
        $dbInfo = $this->get('config');
        return new Mysql(
            [
                'host' => $dbInfo['db']['host'],
                'username' => $dbInfo['db']['username'],
                'password' => $dbInfo['db']['password'],
                'dbname'   => $dbInfo['db']['dbname'],
            ]
        );
    }
);

//--------------------------------------DB CONNECTION END------------------------------------


//-----------------------------------SESSION CONTAINER START---------------------------------

$container->setShared(
    'session',
    function () {
        $session = new Manager();
        $files = new Stream(
            [
                'savePath' => '/tmp',
            ]
        );

        $session
            ->setAdapter($files)
            ->start();

        return $session;
    }
);

//------------------------------------SESSION CONTAINER END----------------------------------

//------------------------------------TRANSLATOR CONTAINER START------------------------------

$container->set('locale', (new \App\Component\MyHelper())->getTranslator());

//-------------------------------------TRANSLATOR CONTAINER END--------------------------------




//-------------------------------------TIME CONTAINER START----------------------------------

$container->setShared(
    'timezone',
    function () {
        date_default_timezone_set("Asia/Kolkata");
        return date("d-m-Y H:i:s");
    }
);

//--------------------------------------TIME CONTAINER END-----------------------------------

$application = new Application($container);


//------------------------------------RESPONSE AND EXCEPTION START---------------------------

try {
    // Handle the request
    $response = $application->handle(
        $_SERVER["REQUEST_URI"]
    );

    $response->send();
} catch (\Exception $e) {
    echo 'Exception: ', $e->getMessage();
}

//-----------------------------------RESPONSE AND EXCEPTION END--------------------------------