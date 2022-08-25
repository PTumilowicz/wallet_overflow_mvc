<?php

//Autoloader
require '../vendor/autoload.php';

error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

session_start();

$router = new Core\Router();

$router->add('api/limit/{category:[\wżźćńółęąśŻŹĆĄŚĘŁÓŃ ]+}', ['controller' => 'Expense', 'action' => 'limit']);
$router->add('api/limitSum/{category:[\wżźćńółęąśŻŹĆĄŚĘŁÓŃ ]+}/{date:[\d-]+}', ['controller' => 'Expense', 'action' => 'expenseMonthlySum']);

$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('login', ['controller' => 'Login', 'action' => 'new']);
$router->add('logout', ['controller' => 'Login', 'action' => 'destroy']);
$router->add('password/reset/{token:[\da-f]+}', ['controller' => 'password', 'action' => 'reset']);
$router->add('signup/activate/{token:[\da-f]+}', ['controller' => 'Signup', 'action' => 'activate']);
$router->add('{controller}/{action}');

$url = $_SERVER['QUERY_STRING'];

$router->dispatch($url);