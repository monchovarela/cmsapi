<?php

define('ROOT', rtrim(dirname(__FILE__), '\\/'));

define('CORE', ROOT.'/core');
define('EXTENSIONS', CORE.'/extensions');
define('VENDOR', CORE.'/vendor');
define('CONTROLLERS', CORE.'/controllers');
define('ROUTES', CORE.'/routes');
define('PUBLICFOLDER', ROOT.'/public');

define('ACCESS', true);
define('DEV_MODE', false);

if (DEV_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
}

// library
require(VENDOR."/autoload.php");
require(CONTROLLERS."/snippets/Snippets.php");
require(CONTROLLERS."/app/App.php");
require(ROUTES."/index.php");

