<?php
define('APP_DIR', 'app');
define('VENDOR_DIR', 'Slim');
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__FILE__));

define('APP_PATH', ROOT . DS . APP_DIR . DS);
define('CONFIG_PATH', APP_PATH . 'Config' . DS);
define('CONTROLLER_PATH', APP_PATH . 'Controller' . DS);
define('VIEW_PATH', APP_PATH . 'View' . DS);

// Load Slim
require ROOT . DS . VENDOR_DIR . DS . 'Slim.php';
\Slim\Slim::registerAutoloader();

require APP_PATH . 'index.php';
